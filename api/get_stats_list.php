<?php
/**
 * Get Statistics Lists - Followers, Following, Favorites
 * Returns real data for profile modals
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor']);
    exit;
}

$type = $_GET['type'] ?? '';
$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : $_SESSION['user_id'];

try {
    $results = [];

    switch ($type) {
        case 'followers':
            // Get users who follow this user
            $stmt = $pdo->prepare("
                SELECT u.id, u.username, u.avatar, u.bio
                FROM users u
                INNER JOIN follows f ON u.id = f.follower_id
                WHERE f.following_id = ?
                ORDER BY u.username
            ");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll();
            break;

        case 'following':
            // Get users this user follows
            $stmt = $pdo->prepare("
                SELECT u.id, u.username, u.avatar, u.bio
                FROM users u
                INNER JOIN follows f ON u.id = f.following_id
                WHERE f.follower_id = ?
                ORDER BY u.username
            ");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll();
            break;

        case 'favorites':
            // Get user's favorite books
            $stmt = $pdo->prepare("
                SELECT i.id, i.title, i.author, i.cover_image, i.rating_score
                FROM items i
                INNER JOIN favorites f ON i.id = f.item_id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll();
            break;

        case 'comments':
            // Get user's comments with book info
            $stmt = $pdo->prepare("
                SELECT r.id, r.comment, r.rating, r.created_at,
                       i.id as item_id, i.title, i.author, i.cover_image
                FROM reviews r
                INNER JOIN items i ON r.item_id = i.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll();
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Geçersiz tip']);
            exit;
    }

    echo json_encode([
        'success' => true,
        'type' => $type,
        'data' => $results,
        'count' => count($results)
    ]);

} catch (PDOException $e) {
    error_log("Get Stats List Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası'
    ]);
}
?>