<?php
/**
 * Get Books API - Infinite Scroll Engine
 * Hybrid: Database + Google Books API
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

// Get parameters
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(10, (int) $_GET['limit'])) : 20;
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$lang = isset($_GET['lang']) ? trim($_GET['lang']) : ''; // 'tr', 'en', or empty for all

$offset = ($page - 1) * $limit;
$books = [];

// ============================================
// STEP 1: FETCH FROM DATABASE
// ============================================
try {
    if (!empty($category)) {
        // Filter by category
        $stmt = $pdo->prepare("
            SELECT i.id, i.title, i.author, i.cover_image, i.rating_score 
            FROM items i 
            LEFT JOIN genres g ON i.genre_id = g.id 
            WHERE g.name = ?
            ORDER BY i.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$category, $limit, $offset]);
    } else {
        // All books
        $stmt = $pdo->prepare("
            SELECT id, title, author, cover_image, rating_score 
            FROM items 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
    }

    $db_books = $stmt->fetchAll();

    foreach ($db_books as $book) {
        $books[] = [
            'id' => $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'image' => $book['cover_image'],
            'rating' => (float) $book['rating_score'],
            'source' => 'database'
        ];
    }

} catch (PDOException $e) {
    error_log("Database Error in get_books.php: " . $e->getMessage());
}

// ============================================
// STEP 2: FILL WITH GOOGLE BOOKS API IF NEEDED
// ============================================
$items_needed = $limit - count($books);

if ($items_needed > 0) {
    // Build Google Books API query
    $query_parts = [];

    if (!empty($category)) {
        $query_parts[] = "subject:" . urlencode($category);
    } else {
        // Random subjects for variety
        $subjects = ['fiction', 'history', 'science', 'philosophy', 'psychology', 'literature', 'art', 'technology'];
        $random_subject = $subjects[array_rand($subjects)];
        $query_parts[] = "subject:" . $random_subject;
    }

    $query = implode('+', $query_parts);
    if (empty($query)) {
        $query = 'books'; // Fallback
    }

    // Build API URL
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=" . $query;
    $api_url .= "&orderBy=relevance";
    $api_url .= "&maxResults=" . min(40, $items_needed);
    $api_url .= "&startIndex=" . $offset; // CRITICAL: Prevents same books

    // Add language restriction if specified
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

                // Extract data
                $title = $volumeInfo['title'] ?? 'Untitled';
                $authors = isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown';
                $thumbnail = $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop';
                $thumbnail = str_replace('http://', 'https://', $thumbnail);

                // Generate random rating
                $rating = rand(35, 50) / 10;

                $books[] = [
                    'id' => 0, // API books have no database ID
                    'title' => $title,
                    'author' => $authors,
                    'image' => $thumbnail,
                    'rating' => $rating,
                    'source' => 'api',
                    'google_id' => $api_item['id'] ?? null
                ];

                if (count($books) >= $limit) {
                    break;
                }
            }
        }
    }
}

// ============================================
// RESPONSE
// ============================================
echo json_encode([
    'success' => true,
    'books' => $books,
    'page' => $page,
    'limit' => $limit,
    'count' => count($books),
    'has_more' => count($books) >= $limit
]);
?>