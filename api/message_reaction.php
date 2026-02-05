<?php
/**
 * Message Reaction API
 * Add or remove emoji reactions to messages
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum gerekli']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$message_id = (int) ($data['message_id'] ?? 0);
$reaction = $data['reaction'] ?? '';

if ($message_id <= 0 || empty($reaction)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz parametreler']);
    exit;
}

try {
    // Check if message exists and user has access
    $stmt = $pdo->prepare("
        SELECT id FROM messages 
        WHERE id = ? AND (sender_id = ? OR receiver_id = ?)
    ");
    $stmt->execute([$message_id, $user_id, $user_id]);

    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Mesaj bulunamadı']);
        exit;
    }

    // Check if reaction already exists
    $stmt = $pdo->prepare("
        SELECT id, reaction FROM message_reactions 
        WHERE message_id = ? AND user_id = ?
    ");
    $stmt->execute([$message_id, $user_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['reaction'] === $reaction) {
            // Remove reaction (toggle off)
            $stmt = $pdo->prepare("DELETE FROM message_reactions WHERE id = ?");
            $stmt->execute([$existing['id']]);
            echo json_encode(['success' => true, 'action' => 'removed']);
        } else {
            // Update to new reaction
            $stmt = $pdo->prepare("UPDATE message_reactions SET reaction = ? WHERE id = ?");
            $stmt->execute([$reaction, $existing['id']]);
            echo json_encode(['success' => true, 'action' => 'updated']);
        }
    } else {
        // Add new reaction
        $stmt = $pdo->prepare("
            INSERT INTO message_reactions (message_id, user_id, reaction) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$message_id, $user_id, $reaction]);
        echo json_encode(['success' => true, 'action' => 'added']);
    }

} catch (PDOException $e) {
    error_log("Message Reaction Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
