<?php
/**
 * GET BOOKS API - DATABASE FIRST WITH INFINITE SCROLL
 * 
 * Features:
 * - Database as PRIMARY data source (10,000+ real books)
 * - True infinite scrolling from database
 * - Category filtering
 * - Search functionality
 * - Clean JSON output
 * 
 * @version 4.0 - Database First
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

$response = [];

try {
    // Parse request parameters
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, (int) $_GET['limit'])) : 42;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    // Calculate offset
    $offset = ($page - 1) * $limit;

    // Build SQL query
    $sql = "SELECT i.id, i.title, i.author, i.cover_image as image, 
                   i.rating_score as rating, i.description,
                   i.publication_year as publishedDate, i.page_count as pageCount,
                   g.name as categories
            FROM items i
            LEFT JOIN genres g ON i.genre_id = g.id
            WHERE i.type = 'book'";

    $params = [];

    // Add search filter
    if ($search) {
        $sql .= " AND (i.title LIKE ? OR i.author LIKE ? OR i.description LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Add category filter
    if ($category && $category !== 'Tümü') {
        $sql .= " AND g.name = ?";
        $params[] = $category;
    }

    // Add ordering and pagination
    $sql .= " ORDER BY RAND() LIMIT ? OFFSET ?"; // RANDOM order for mixed categories
    $params[] = $limit;
    $params[] = $offset;

    // Execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format response
    foreach ($books as &$book) {
        $book['id'] = (int) $book['id'];
        $book['rating'] = (float) $book['rating'];
        $book['pageCount'] = (int) $book['pageCount'];
        $book['source'] = 'database';

        // Ensure image URL is set
        if (empty($book['image'])) {
            $book['image'] = 'https://via.placeholder.com/128x192.png?text=No+Cover';
        }
    }

    $response = $books;

} catch (Exception $e) {
    error_log("Error in get_books.php: " . $e->getMessage());
    $response = [];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>