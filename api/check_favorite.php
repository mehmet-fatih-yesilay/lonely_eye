<?php
/**
 * Check Favorite Status
 * Returns whether a book is favorited by current user
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'is_favorite' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0;

if ($item_id <= 0) {
    echo json_encode(['success' => false, 'is_favorite' => false]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$user_id, $item_id]);
    $favorite = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'is_favorite' => $favorite ? true : false
    ]);

} catch (PDOException $e) {
    error_log("Check Favorite Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'is_favorite' => false]);
}
?>