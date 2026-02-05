<?php
/**
 * Get Replies for a Review
 * Returns all replies in a nested structure
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$review_id = isset($_GET['review_id']) ? (int) $_GET['review_id'] : 0;

if ($review_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz yorum ID']);
    exit;
}

try {
    // Fetch all replies for this review
    $stmt = $pdo->prepare("
        SELECT cr.*, u.username, u.avatar
        FROM comment_replies cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.review_id = ?
        ORDER BY cr.created_at ASC
    ");
    $stmt->execute([$review_id]);
    $allReplies = $stmt->fetchAll();

    // Organize replies into nested structure
    $repliesById = [];
    $rootReplies = [];

    // First pass: index all replies by ID
    foreach ($allReplies as $reply) {
        $reply['replies'] = [];
        $repliesById[$reply['id']] = $reply;
    }

    // Second pass: build tree structure
    foreach ($repliesById as $id => $reply) {
        if ($reply['parent_reply_id'] === null) {
            // Root level reply (direct reply to review)
            $rootReplies[] = &$repliesById[$id];
        } else {
            // Nested reply
            if (isset($repliesById[$reply['parent_reply_id']])) {
                $repliesById[$reply['parent_reply_id']]['replies'][] = &$repliesById[$id];
            }
        }
    }

    echo json_encode([
        'success' => true,
        'replies' => $rootReplies,
        'total_count' => count($allReplies)
    ]);

} catch (PDOException $e) {
    error_log("Get Replies Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası'
    ]);
}
?>