<?php
/**
 * Toggle Favorite - Add/Remove books from favorites
 * Uses interact_with_book.php bridge for Google books
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$item_id = $data['item_id'] ?? 0;
$google_id = $data['google_id'] ?? '';

try {
    // HYBRID LOGIC: If this is a Google book (id=0), save it first
    if ($item_id == 0 && !empty($google_id)) {
        // Check if already exists
        $stmt = $pdo->prepare("SELECT id FROM items WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            $item_id = $existing['id'];
        } else {
            // Save Google book to database
            $stmt = $pdo->prepare("
                INSERT INTO items (google_id, type, title, author, description, cover_image, language, created_at)
                VALUES (?, 'book', ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $google_id,
                $data['title'] ?? 'Untitled',
                $data['author'] ?? 'Unknown',
                $data['description'] ?? '',
                $data['cover_image'] ?? '',
                $data['language'] ?? 'en'
            ]);

            $item_id = $pdo->lastInsertId();
        }
    }

    if ($item_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz kitap ID']);
        exit;
    }

    // Check if favorite exists
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$user_id, $item_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Remove from favorites
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND item_id = ?");
        $stmt->execute([$user_id, $item_id]);

        echo json_encode([
            'success' => true,
            'is_favorite' => false,
            'message' => 'Favorilerden çıkarıldı'
        ]);
    } else {
        // Add to favorites
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, item_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $item_id]);

        echo json_encode([
            'success' => true,
            'is_favorite' => true,
            'message' => 'Favorilere eklendi'
        ]);
    }

} catch (PDOException $e) {
    error_log("Toggle Favorite Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
}
?>