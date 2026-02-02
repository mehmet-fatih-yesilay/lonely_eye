<?php
/**
 * Interact with Book - Bridge for Google Books
 * Saves Google books to database before allowing interactions
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
            'message' => 'Kitap zaten kayıtlı',
            'already_exists' => true
        ]);
        exit;
    }

    // Insert new book into database
    $stmt = $pdo->prepare("
        INSERT INTO items (google_id, type, title, author, description, cover_image, language, created_at)
        VALUES (?, 'book', ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $google_id,
        $title,
        $author,
        $description,
        $cover_image,
        $language
    ]);

    $item_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'item_id' => $item_id,
        'message' => 'Kitap başarıyla kaydedildi',
        'already_exists' => false
    ]);

} catch (PDOException $e) {
    error_log("Error in interact_with_book.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>