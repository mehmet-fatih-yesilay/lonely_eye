<?php
/**
 * Messages Page - Chat Interface
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];
$selected_user_id = isset($_GET['user']) ? (int) $_GET['user'] : 0;

// Fetch conversations (users who have messaged with current user)
$stmt = $pdo->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN m.sender_id = ? THEN m.receiver_id 
            ELSE m.sender_id 
        END as user_id,
        u.username,
        u.avatar,
        (SELECT message FROM messages 
         WHERE (sender_id = ? AND receiver_id = user_id) 
            OR (sender_id = user_id AND receiver_id = ?)
         ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages 
         WHERE (sender_id = ? AND receiver_id = user_id) 
            OR (sender_id = user_id AND receiver_id = ?)
         ORDER BY created_at DESC LIMIT 1) as last_message_time,
        (SELECT COUNT(*) FROM messages 
         WHERE sender_id = user_id AND receiver_id = ? AND is_read = 0) as unread_count
    FROM messages m
    JOIN users u ON u.id = CASE 
        WHEN m.sender_id = ? THEN m.receiver_id 
        ELSE m.sender_id 
    END
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY last_message_time DESC
");
$stmt->execute([
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id
]);
$conversations = $stmt->fetchAll();

// Fetch messages for selected conversation
$messages = [];
$selected_user = null;

if ($selected_user_id > 0) {
    // Get selected user info
    $stmt = $pdo->prepare("SELECT id, username, avatar FROM users WHERE id = ?");
    $stmt->execute([$selected_user_id]);
    $selected_user = $stmt->fetch();

    if ($selected_user) {
        // Fetch messages
        $stmt = $pdo->prepare("
            SELECT m.*, u.username, u.avatar 
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$current_user_id, $selected_user_id, $selected_user_id, $current_user_id]);
        $messages = $stmt->fetchAll();

        // Mark messages as read
        $stmt = $pdo->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$selected_user_id, $current_user_id]);
    }
}

// Handle send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = trim($_POST['message'] ?? '');
    $receiver_id = (int) ($_POST['receiver_id'] ?? 0);

    if (!empty($message) && $receiver_id > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$current_user_id, $receiver_id, $message]);

        // Refresh page to show new message
        header("Location: messages.php?user=$receiver_id");
        exit;
    }
}

$page_title = "Mesajlar";
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="messages-container">
        <!-- Conversations List (Left) -->
        <div class="conversations-panel">
            <div class="conversations-header">
                <h4><i class="fas fa-comments"></i> Mesajlar</h4>
            </div>

            <div class="conversations-list">
                <?php if (count($conversations) > 0): ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="messages.php?user=<?php echo $conv['user_id']; ?>"
                            class="conversation-item <?php echo $selected_user_id === $conv['user_id'] ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($conv['avatar']); ?>" alt="Avatar"
                                class="conversation-avatar">
                            <div class="conversation-info">
                                <h6>
                                    <?php echo htmlspecialchars($conv['username']); ?>
                                </h6>
                                <p>
                                    <?php echo htmlspecialchars(substr($conv['last_message'], 0, 40)) . (strlen($conv['last_message']) > 40 ? '...' : ''); ?>
                                </p>
                            </div>
                            <?php if ($conv['unread_count'] > 0): ?>
                                <span class="unread-badge">
                                    <?php echo $conv['unread_count']; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-conversations">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Henüz mesajınız yok</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Panel (Right) -->
        <div class="chat-panel">
            <?php if ($selected_user): ?>
                <!-- Chat Header -->
                <div class="chat-header">
                    <img src="<?php echo htmlspecialchars($selected_user['avatar']); ?>" alt="Avatar" class="chat-avatar">
                    <h5>
                        <?php echo htmlspecialchars($selected_user['username']); ?>
                    </h5>
                </div>

                <!-- Messages -->
                <div class="chat-messages" id="chatMessages">
                    <?php foreach ($messages as $msg): ?>
                        <div class="message-bubble <?php echo $msg['sender_id'] === $current_user_id ? 'sent' : 'received'; ?>">
                            <?php if ($msg['sender_id'] !== $current_user_id): ?>
                                <img src="<?php echo htmlspecialchars($msg['avatar']); ?>" alt="Avatar" class="message-avatar">
                            <?php endif; ?>
                            <div class="message-content">
                                <p>
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </p>
                                <span class="message-time">
                                    <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <div class="chat-input">
                    <form method="POST" action="" class="d-flex gap-2">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                        <input type="text" name="message" class="form-control" placeholder="Mesajınızı yazın..." required
                            autofocus>
                        <button type="submit" name="send_message" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="chat-empty-state">
                    <i class="fas fa-comments fa-4x mb-3"></i>
                    <h3>Sohbet Başlat</h3>
                    <p class="text-muted">Sol taraftan bir konuşma seçin veya yeni bir sohbet başlatın</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .messages-container {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 1.5rem;
        height: calc(100vh - 4rem);
    }

    /* Conversations Panel */
    .conversations-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .conversations-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-glass);
    }

    .conversations-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .conversations-list {
        flex: 1;
        overflow-y: auto;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        color: var(--text-main);
        transition: all 0.3s ease;
        position: relative;
    }

    .conversation-item:hover {
        background: rgba(56, 189, 248, 0.05);
    }

    .conversation-item.active {
        background: rgba(56, 189, 248, 0.1);
        border-left: 3px solid var(--primary);
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--border-color);
    }

    .conversation-info {
        flex: 1;
        min-width: 0;
    }

    .conversation-info h6 {
        margin: 0 0 0.25rem 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .conversation-info p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .unread-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--primary);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        min-width: 20px;
        text-align: center;
    }

    .empty-conversations {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }

    .empty-conversations i {
        opacity: 0.5;
    }

    /* Chat Panel */
    .chat-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-glass);
    }

    .chat-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary);
    }

    .chat-header h5 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message-bubble {
        display: flex;
        gap: 0.75rem;
        max-width: 70%;
    }

    .message-bubble.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-bubble.received {
        align-self: flex-start;
    }

    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .message-content {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 0.75rem 1rem;
    }

    .message-bubble.sent .message-content {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .message-content p {
        margin: 0 0 0.25rem 0;
        line-height: 1.5;
    }

    .message-time {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .chat-input {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-glass);
    }

    .chat-input form {
        display: flex;
        gap: 0.75rem;
    }

    .chat-input input {
        flex: 1;
    }

    .chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }

    .chat-empty-state i {
        opacity: 0.3;
    }

    .chat-empty-state h3 {
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .messages-container {
            grid-template-columns: 1fr;
            height: auto;
        }

        .conversations-panel {
            max-height: 400px;
        }

        .chat-panel {
            min-height: 500px;
        }

        .message-bubble {
            max-width: 85%;
        }
    }
</style>

<script>
    // Auto-scroll to bottom of messages
    document.addEventListener('DOMContentLoaded', function () {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>