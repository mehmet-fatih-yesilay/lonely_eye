<?php
/**
 * GET BOOKS API - PRODUCTION GRADE INFINITE SCROLL ENGINE
 * 
 * Features:
 * - Separate pagination for local DB and Google API
 * - Intelligent hybrid data merging
 * - Google API 1000 result limit handling
 * - Category translation (Turkish → English)
 * - Deduplication
 * - Clean JSON output
 * 
 * @version 2.0
 */

// Start output buffering to prevent any unwanted output
ob_start();

// Suppress errors for clean JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Fix: Use absolute path for database connection
require_once __DIR__ . '/../includes/db.php';

// ============================================
// CATEGORY TRANSLATION MAP (Turkish → English)
// ============================================
$category_map = [
    'Tümü' => '',
    'Roman' => 'Fiction',
    'Bilim Kurgu' => 'Science Fiction',
    'Fantastik' => 'Fantasy',
    'Tarih' => 'History',
    'Biyografi' => 'Biography',
    'Bilim' => 'Science',
    'Felsefe' => 'Philosophy',
    'Psikoloji' => 'Psychology',
    'Sanat' => 'Art',
    'Şiir' => 'Poetry',
    'Edebiyat' => 'Literature',
    'Polisiye' => 'Mystery',
    'Macera' => 'Adventure',
    'Romantik' => 'Romance',
    'Korku' => 'Horror',
    'Gezi' => 'Travel',
    'Çocuk' => 'Children',
    'Genç' => 'Young Adult',
    'Kişisel Gelişim' => 'Self Help'
];

$response = [];

try {
    // ============================================
    // PARSE REQUEST PARAMETERS
    // ============================================
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, (int) $_GET['limit'])) : 42;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_tr = isset($_GET['category']) ? trim($_GET['category']) : '';
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'all';

    // Translate category to English
    $category_en = '';
    if ($category_tr && isset($category_map[$category_tr])) {
        $category_en = $category_map[$category_tr];
    }

    $books = [];

    // ============================================
    // STRATEGY 1: FETCH FROM LOCAL DATABASE
    // ============================================
    $db_offset = ($page - 1) * $limit;

    // Only SELECT columns that actually exist in the database
    $sql = "SELECT id, title, author, cover_image, rating_score, description, type FROM items WHERE 1=1";
    $params = [];

    // Apply search filter
    if ($search) {
        $sql .= " AND (title LIKE ? OR author LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Language filter disabled - column doesn't exist in current schema
    // if ($lang !== 'all') {
    //     $sql .= " AND (language = ? OR language IS NULL)";
    //     $params[] = $lang;
    // }

    // Apply category filter (if we have genre mapping in DB)
    // Note: This assumes you have a genre relationship. Skip if not applicable.

    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $db_offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $db_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format local books
    foreach ($db_books as $book) {
        $image = $book['cover_image'] ?? '';

        // Fix image path
        if (!filter_var($image, FILTER_VALIDATE_URL) && !empty($image)) {
            if (strpos($image, 'assets/') === false) {
                $image = 'assets/img/' . $image;
            }
        }

        $books[] = [
            'id' => $book['id'],
            'google_id' => null, // Column doesn't exist in current DB schema
            'title' => $book['title'],
            'author' => $book['author'],
            'image' => $image ?: 'assets/img/default_book.png',
            'rating' => (float) ($book['rating_score'] ?? 0),
            'description' => $book['description'] ?? '',
            'source' => 'local'
        ];
    }

    // ============================================
    // STRATEGY 2: FETCH FROM GOOGLE BOOKS API
    // ============================================
    // Only fetch from Google if:
    // 1. We don't have enough local results, OR
    // 2. We're on page 2+ (user is scrolling)

    $need_google = (count($books) < $limit) || ($page > 1);

    if ($need_google) {
        // Calculate Google API startIndex
        // CRITICAL: Google API uses 0-based indexing
        // For infinite scroll, we need to track Google pages separately

        // Strategy: Use page number to calculate Google startIndex
        // But account for local results we already have
        $google_start_index = ($page - 1) * $limit;

        // Google API has a hard limit of 1000 results (startIndex + maxResults <= 1000)
        $google_max_start = 1000 - $limit;

        if ($google_start_index > $google_max_start) {
            // We've hit Google's limit, use cycling strategy
            // Cycle through different query variations to get more results
            $cycle = floor($google_start_index / $google_max_start);
            $google_start_index = $google_start_index % $google_max_start;

            // Add variation to query to get different results
            $query_variations = ['', ' classics', ' bestseller', ' popular', ' recommended'];
            $variation = $query_variations[$cycle % count($query_variations)];
        } else {
            $variation = '';
        }

        // Build Google query
        $google_query = '';

        if ($search) {
            $google_query = $search . $variation;
        } elseif ($category_en) {
            $google_query = "subject:" . $category_en . $variation;
        } else {
            $google_query = "books" . $variation;
        }

        // Build API URL
        $api_url = "https://www.googleapis.com/books/v1/volumes";
        $api_url .= "?q=" . urlencode($google_query);
        $api_url .= "&startIndex=" . $google_start_index;
        $api_url .= "&maxResults=" . min($limit, 40); // Google max is 40 per request
        $api_url .= "&printType=books";
        $api_url .= "&orderBy=relevance";

        // Language restriction
        if ($lang === 'tr') {
            $api_url .= "&langRestrict=tr";
        } elseif ($lang === 'en') {
            $api_url .= "&langRestrict=en";
        }

        // Make CURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'LonelyEye/2.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($ch);
        curl_close($ch);

        // Process Google API response
        if (!$curl_error && $http_code === 200 && $api_response) {
            $data = json_decode($api_response, true);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $vol = $item['volumeInfo'] ?? [];
                    $google_id = $item['id'] ?? null;

                    if (!$google_id)
                        continue;

                    // Check for duplicates (by title and google_id)
                    $is_duplicate = false;
                    foreach ($books as $existing_book) {
                        if (
                            ($existing_book['google_id'] === $google_id) ||
                            (strtolower($existing_book['title']) === strtolower($vol['title'] ?? ''))
                        ) {
                            $is_duplicate = true;
                            break;
                        }
                    }

                    if (!$is_duplicate) {
                        // Get thumbnail image
                        $thumbnail = 'assets/img/default_book.png';
                        if (isset($vol['imageLinks']['thumbnail'])) {
                            $thumbnail = str_replace('http://', 'https://', $vol['imageLinks']['thumbnail']);
                        } elseif (isset($vol['imageLinks']['smallThumbnail'])) {
                            $thumbnail = str_replace('http://', 'https://', $vol['imageLinks']['smallThumbnail']);
                        }

                        $books[] = [
                            'id' => $google_id,
                            'google_id' => $google_id,
                            'title' => $vol['title'] ?? 'Untitled',
                            'author' => isset($vol['authors']) ? implode(', ', $vol['authors']) : 'Unknown Author',
                            'image' => $thumbnail,
                            'rating' => isset($vol['averageRating']) ? (float) $vol['averageRating'] : 0,
                            'description' => $vol['description'] ?? '',
                            'source' => 'google'
                        ];
                    }

                    // Stop when we have enough books
                    if (count($books) >= $limit)
                        break;
                }
            }
        } else {
            // Log API errors for debugging
            error_log("Google Books API Error: HTTP $http_code, CURL Error: $curl_error");
        }
    }

    // Return results (limit to requested amount)
    $response = array_slice($books, 0, $limit);

} catch (PDOException $e) {
    error_log("Database error in get_books.php: " . $e->getMessage());
    $response = [];
} catch (Exception $e) {
    error_log("General error in get_books.php: " . $e->getMessage());
    $response = [];
}

// Clean buffer and output JSON
ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>