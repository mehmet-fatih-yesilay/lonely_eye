<?php
/**
 * Get Books API - Hybrid Infinite Scroll Engine
 * Combines local database + Google Books API
 * COMPLETELY REWRITTEN for proper functionality
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

// ============================================
// STEP 1: GET PARAMETERS
// ============================================
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(10, (int) $_GET['limit'])) : 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'all'; // 'tr', 'en', 'all'

$offset = ($page - 1) * $limit;
$books = [];

// ============================================
// STEP 2: SEARCH LOCAL DATABASE FIRST
// ============================================
try {
    $sql = "SELECT id, google_id, title, author, cover_image, rating_score, language,
            (SELECT COUNT(*) FROM reviews WHERE item_id = items.id) as review_count
            FROM items WHERE 1=1";

    $params = [];

    // Search filter
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Language filter
    if ($lang === 'tr') {
        $sql .= " AND (language = 'tr' OR language IS NULL)";
    } elseif ($lang === 'en') {
        $sql .= " AND language = 'en'";
    }

    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $db_books = $stmt->fetchAll();

    // Format database books
    foreach ($db_books as $book) {
        $books[] = [
            'id' => $book['id'],
            'google_id' => $book['google_id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'image' => $book['cover_image'],
            'rating' => (float) $book['rating_score'],
            'review_count' => (int) $book['review_count'],
            'source' => 'database'
        ];
    }

} catch (PDOException $e) {
    error_log("Database Error in get_books.php: " . $e->getMessage());
}

// ============================================
// STEP 3: FILL REMAINING WITH GOOGLE BOOKS API
// ============================================
$items_needed = $limit - count($books);

if ($items_needed > 0) {
    // Build Google Books API query
    $query = '';

    if (!empty($search)) {
        // User is searching - use their search term
        $query = urlencode($search);
    } else {
        // No search - use general terms for variety
        $subjects = ['fiction', 'history', 'science', 'philosophy', 'psychology', 'literature', 'art', 'technology', 'biography', 'poetry'];
        $query = 'subject:' . $subjects[array_rand($subjects)];
    }

    // Build API URL
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=" . $query;
    $api_url .= "&orderBy=relevance";
    $api_url .= "&maxResults=" . min(40, $items_needed);

    // CRITICAL: Proper pagination for infinite scroll
    $google_start_index = $offset;
    $api_url .= "&startIndex=" . $google_start_index;

    // Language restriction
    if ($lang === 'tr') {
        $api_url .= "&langRestrict=tr";
    } elseif ($lang === 'en') {
        $api_url .= "&langRestrict=en";
    }

    // Fetch from Google Books API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'LonelyEye/1.0');

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);

        if (isset($data['items']) && !empty($data['items'])) {
            foreach ($data['items'] as $api_item) {
                $volumeInfo = $api_item['volumeInfo'] ?? [];
                $google_id = $api_item['id'] ?? null;

                if (!$google_id)
                    continue;

                // Extract data
                $title = $volumeInfo['title'] ?? 'Untitled';
                $authors = isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown';
                $thumbnail = $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop';
                $thumbnail = str_replace('http://', 'https://', $thumbnail);
                $description = $volumeInfo['description'] ?? '';
                $language = $volumeInfo['language'] ?? 'en';

                // Check if this Google book already exists in our database
                $db_rating = 0;
                $db_review_count = 0;
                $db_id = null;

                try {
                    $stmt = $pdo->prepare("SELECT id, rating_score, (SELECT COUNT(*) FROM reviews WHERE item_id = items.id) as review_count FROM items WHERE google_id = ?");
                    $stmt->execute([$google_id]);
                    $existing = $stmt->fetch();

                    if ($existing) {
                        $db_id = $existing['id'];
                        $db_rating = (float) $existing['rating_score'];
                        $db_review_count = (int) $existing['review_count'];
                    }
                } catch (PDOException $e) {
                    error_log("Error checking Google book: " . $e->getMessage());
                }

                // Format as book object
                $books[] = [
                    'id' => $db_id ?? 0, // 0 means not in database yet
                    'google_id' => $google_id,
                    'title' => $title,
                    'author' => $authors,
                    'image' => $thumbnail,
                    'description' => $description,
                    'language' => $language,
                    'rating' => $db_rating > 0 ? $db_rating : (rand(35, 50) / 10),
                    'review_count' => $db_review_count,
                    'source' => 'google'
                ];

                if (count($books) >= $limit) {
                    break;
                }
            }
        }
    }
}

// ============================================
// STEP 4: RETURN RESPONSE
// ============================================
echo json_encode([
    'success' => true,
    'books' => $books,
    'page' => $page,
    'limit' => $limit,
    'count' => count($books),
    'has_more' => count($books) >= $limit,
    'debug' => [
        'search' => $search,
        'lang' => $lang,
        'offset' => $offset,
        'db_count' => count(array_filter($books, function ($b) {
            return $b['source'] === 'database'; })),
        'google_count' => count(array_filter($books, function ($b) {
            return $b['source'] === 'google'; }))
    ]
]);
?>