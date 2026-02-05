<?php
/**
 * Post Reply to Comment
 * Handles posting replies to reviews or other replies (nested)
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

$review_id = isset($data['review_id']) ? (int) $data['review_id'] : 0;
$parent_reply_id = isset($data['parent_reply_id']) ? (int) $data['parent_reply_id'] : null;
$comment = trim($data['comment'] ?? '');

// Validation
if ($review_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz yorum ID']);
    exit;
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Yanıt boş olamaz']);
    exit;
}

if (strlen($comment) > 1000) {
    echo json_encode(['success' => false, 'message' => 'Yanıt çok uzun (max 1000 karakter)']);
    exit;
}

try {
    // Verify review exists
    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Yorum bulunamadı']);
        exit;
    }

    // If replying to another reply, verify it exists
    if ($parent_reply_id !== null && $parent_reply_id > 0) {
        $stmt = $pdo->prepare("SELECT id FROM comment_replies WHERE id = ? AND review_id = ?");
        $stmt->execute([$parent_reply_id, $review_id]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Yanıtlanacak yorum bulunamadı']);
            exit;
        }
    }

    // Insert reply
    $stmt = $pdo->prepare("
        INSERT INTO comment_replies (review_id, parent_reply_id, user_id, comment, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $review_id,
        $parent_reply_id,
        $user_id,
        $comment
    ]);

    $reply_id = $pdo->lastInsertId();

    // Fetch the newly created reply with user info
    $stmt = $pdo->prepare("
        SELECT cr.*, u.username, u.avatar
        FROM comment_replies cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.id = ?
    ");
    $stmt->execute([$reply_id]);
    $reply = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Yanıt başarıyla eklendi',
        'reply' => $reply
    ]);

} catch (PDOException $e) {
    error_log("Post Reply Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
}
?>