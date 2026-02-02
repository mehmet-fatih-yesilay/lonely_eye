<?php
/**
 * Save Book from Google API to Database
 * Converts external API books into local database entries
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$google_id = $data['google_id'] ?? '';
$title = $data['title'] ?? '';
$author = $data['author'] ?? '';
$description = $data['description'] ?? '';
$cover_image = $data['cover_image'] ?? '';
$language = $data['language'] ?? 'en';
$publication_year = $data['publication_year'] ?? null;
$page_count = $data['page_count'] ?? null;

if (empty($google_id) || empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz kitap verisi']);
    exit;
}

try {
    // Check if book already exists
    $stmt = $pdo->prepare("SELECT id FROM items WHERE google_id = ?");
    $stmt->execute([$google_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Book already exists, return its ID
        echo json_encode([
            'success' => true,
            'item_id' => $existing['id'],
            'message' => 'Kitap zaten veritabanında mevcut'
        ]);
        exit;
    }

    // Insert new book
    $stmt = $pdo->prepare("
        INSERT INTO items (google_id, type, title, author, description, cover_image, language, publication_year, page_count, created_at)
        VALUES (?, 'book', ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $google_id,
        $title,
        $author,
        $description,
        $cover_image,
        $language,
        $publication_year,
        $page_count
    ]);

    $item_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'item_id' => $item_id,
        'message' => 'Kitap başarıyla veritabanına eklendi'
    ]);

} catch (PDOException $e) {
    error_log("Error saving Google book: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>