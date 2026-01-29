<?php
/**
 * API Endpoint - Get Books with Infinite Scroll
 * Returns JSON data for books from database + Google Books API
 */

header('Content-Type: application/json');
require_once '../includes/db.php';

// Get parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(40, max(1, intval($_GET['limit']))) : 40;
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$offset = ($page - 1) * $limit;

// ============================================
// FETCH FROM DATABASE
// ============================================
$books = [];

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

    $db_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($db_books as $book) {
        $books[] = [
            'id' => (int) $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'image' => $book['cover_image'],
            'rating' => (float) $book['rating_score'],
            'source' => 'database'
        ];
    }

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}

// ============================================
// FILL WITH GOOGLE BOOKS API IF NEEDED
// ============================================
$items_needed = $limit - count($books);

if ($items_needed > 0 && $page <= 3) { // Only fill first 3 pages
    $subjects = ['History', 'Fiction', 'Science', 'Philosophy', 'Psychology', 'Literature', 'Technology', 'Art'];
    $random_subject = $subjects[array_rand($subjects)];

    $api_url = "https://www.googleapis.com/books/v1/volumes?q=subject:$random_subject&orderBy=relevance&maxResults=$items_needed&langRestrict=tr";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);

        if (isset($data['items']) && !empty($data['items'])) {
            foreach ($data['items'] as $api_item) {
                $volumeInfo = $api_item['volumeInfo'] ?? [];

                $books[] = [
                    'id' => 0,
                    'title' => $volumeInfo['title'] ?? 'Untitled',
                    'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
                    'image' => str_replace('http://', 'https://', $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop'),
                    'rating' => rand(35, 50) / 10,
                    'source' => 'api'
                ];

                if (count($books) >= $limit)
                    break;
            }
        }
    }
}

// Return JSON response
echo json_encode([
    'success' => true,
    'page' => $page,
    'count' => count($books),
    'books' => $books
]);
