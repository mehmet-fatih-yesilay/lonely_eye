<?php
/**
 * Follow/Unfollow API
 * Handles follow and unfollow actions
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açmanız gerekiyor.']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek metodu.']);
    exit;
}

// Get user_id from POST data
$data = json_decode(file_get_contents('php://input'), true);
$target_user_id = isset($data['user_id']) ? (int) $data['user_id'] : 0;
$current_user_id = $_SESSION['user_id'];

// Validate target user ID
if ($target_user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz kullanıcı ID.']);
    exit;
}

// Prevent self-follow
if ($target_user_id === $current_user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Kendinizi takip edemezsiniz.']);
    exit;
}

try {
    // Check if already following
    $stmt = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$current_user_id, $target_user_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Already following - UNFOLLOW
        $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$current_user_id, $target_user_id]);

        echo json_encode([
            'status' => 'success',
            'action' => 'unfollowed',
            'message' => 'Takipten çıkıldı.'
        ]);
    } else {
        // Not following - FOLLOW
        $stmt = $pdo->prepare("INSERT INTO follows (follower_id, following_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$current_user_id, $target_user_id]);

        echo json_encode([
            'status' => 'success',
            'action' => 'followed',
            'message' => 'Takip edildi.'
        ]);
    }

} catch (PDOException $e) {
    error_log("Follow API Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.'
    ]);
}
?>