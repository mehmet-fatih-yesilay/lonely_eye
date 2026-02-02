<?php
/**
 * INTERACT WITH BOOK - KÖPRÜ
 * Google kitapları veritabanına sessizce kaydeder
 */

ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

// Kullanıcı kontrolü
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor']);
    exit;
}

// POST verisi al
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$google_id = $data['google_id'] ?? '';
$title = $data['title'] ?? '';
$author = $data['author'] ?? '';
$description = $data['description'] ?? '';
$image = $data['image'] ?? '';
$language = $data['language'] ?? 'en';

if (empty($google_id) || empty($title)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Geçersiz kitap verisi']);
    exit;
}

try {
    // Kitap zaten var mı kontrol et
    $stmt = $pdo->prepare("SELECT id FROM items WHERE google_id = ?");
    $stmt->execute([$google_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Zaten kayıtlı, ID'sini döndür
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'item_id' => $existing['id'],
            'message' => 'Kitap zaten kayıtlı',
            'already_exists' => true
        ]);
        exit;
    }

    // Yeni kitap ekle
    $stmt = $pdo->prepare("
        INSERT INTO items 
        (google_id, type, title, author, description, cover_image, language, created_at)
        VALUES (?, 'book', ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $google_id,
        $title,
        $author,
        $description,
        $image,
        $language
    ]);

    $new_id = $pdo->lastInsertId();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'item_id' => $new_id,
        'message' => 'Kitap başarıyla kaydedildi',
        'already_exists' => false
    ]);

} catch (PDOException $e) {
    error_log("Error in interact_with_book.php: " . $e->getMessage());
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası oluştu'
    ]);
}
?>