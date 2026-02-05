<?php
/**
 * Get New Messages API
 * For real-time message polling
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum gerekli']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = (int) ($_GET['user_id'] ?? 0);
$last_message_id = (int) ($_GET['last_id'] ?? 0);

if ($other_user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı']);
    exit;
}

try {
    // Get new messages since last_id
    $stmt = $pdo->prepare("
        SELECT m.id, m.sender_id, m.receiver_id, m.message, m.is_read, 
               TIME_FORMAT(m.created_at, '%H:%i') as time,
               u.username, u.avatar
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE m.id > ?
          AND ((m.sender_id = ? AND m.receiver_id = ?) 
            OR (m.sender_id = ? AND m.receiver_id = ?))
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$last_message_id, $current_user_id, $other_user_id, $other_user_id, $current_user_id]);
    $newMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark received messages as read
    if (!empty($newMessages)) {
        $stmt = $pdo->prepare("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$other_user_id, $current_user_id]);
    }

    // Get read status updates for sent messages
    $stmt = $pdo->prepare("
        SELECT id FROM messages 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 1
          AND read_at > DATE_SUB(NOW(), INTERVAL 10 SECOND)
    ");
    $stmt->execute([$current_user_id, $other_user_id]);
    $readUpdates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'messages' => $newMessages,
        'read_updates' => $readUpdates
    ]);

} catch (PDOException $e) {
    error_log("Get New Messages Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
