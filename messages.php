<?php
/**
 * Messages Page - Enhanced Chat Interface
 * Features: Emoji Picker, Read Receipts, Real-time Updates, Emoji Reactions
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];
$selected_user_id = isset($_GET['user']) ? (int) $_GET['user'] : (isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0);

// Fetch users that current user is following (plus users who have sent messages)
$stmt = $pdo->prepare("
    SELECT DISTINCT
        u.id as user_id,
        u.username,
        u.avatar,
        (SELECT message FROM messages 
         WHERE (sender_id = ? AND receiver_id = u.id) 
            OR (sender_id = u.id AND receiver_id = ?)
         ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages 
         WHERE (sender_id = ? AND receiver_id = u.id) 
            OR (sender_id = u.id AND receiver_id = ?)
         ORDER BY created_at DESC LIMIT 1) as last_message_time,
        (SELECT COUNT(*) FROM messages 
         WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
    FROM users u
    WHERE u.id IN (
        SELECT following_id FROM follows WHERE follower_id = ?
        UNION
        SELECT sender_id FROM messages WHERE receiver_id = ?
        UNION
        SELECT receiver_id FROM messages WHERE sender_id = ?
    ) AND u.id != ?
    ORDER BY 
        CASE WHEN last_message_time IS NULL THEN 1 ELSE 0 END,
        last_message_time DESC,
        u.username ASC
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
        // Fetch messages with reactions
        $stmt = $pdo->prepare("
            SELECT m.*, u.username, u.avatar,
                   GROUP_CONCAT(DISTINCT CONCAT(mr.reaction, ':', mr.user_id) SEPARATOR '|') as reactions
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            LEFT JOIN message_reactions mr ON mr.message_id = m.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            GROUP BY m.id
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$current_user_id, $selected_user_id, $selected_user_id, $current_user_id]);
        $messages = $stmt->fetchAll();

        // Mark messages as read and set read_at timestamp
        $stmt = $pdo->prepare("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$selected_user_id, $current_user_id]);
    }
}

// Handle send message (POST)
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
                                    <?php
                                    if ($conv['last_message']) {
                                        echo htmlspecialchars(substr($conv['last_message'], 0, 40)) . (strlen($conv['last_message']) > 40 ? '...' : '');
                                    } else {
                                        echo '<em>Henüz mesaj yok</em>';
                                    }
                                    ?>
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
                        <p>Henüz kimseyi takip etmiyorsunuz</p>
                        <a href="discover.php" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-users"></i> İnsanları Keşfet
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Panel (Right) -->
        <div class="chat-panel">
            <?php if ($selected_user): ?>
                <!-- Chat Header -->
                <div class="chat-header">
                    <a href="profile.php?id=<?php echo $selected_user['id']; ?>">
                        <img src="<?php echo htmlspecialchars($selected_user['avatar']); ?>" alt="Avatar"
                            class="chat-avatar">
                    </a>
                    <div class="chat-user-info">
                        <h5>
                            <a href="profile.php?id=<?php echo $selected_user['id']; ?>"
                                style="color: inherit; text-decoration: none;">
                                <?php echo htmlspecialchars($selected_user['username']); ?>
                            </a>
                        </h5>
                        <span class="online-status" id="onlineStatus">●</span>
                    </div>
                </div>

                <!-- Messages -->
                <div class="chat-messages" id="chatMessages" data-user-id="<?php echo $selected_user_id; ?>">
                    <?php foreach ($messages as $msg): ?>
                        <?php
                        $isSent = $msg['sender_id'] === $current_user_id;
                        $reactions = [];
                        if (!empty($msg['reactions'])) {
                            foreach (explode('|', $msg['reactions']) as $r) {
                                list($emoji, $uid) = explode(':', $r);
                                if (!isset($reactions[$emoji]))
                                    $reactions[$emoji] = [];
                                $reactions[$emoji][] = $uid;
                            }
                        }
                        ?>
                        <div class="message-bubble <?php echo $isSent ? 'sent' : 'received'; ?>"
                            data-message-id="<?php echo $msg['id']; ?>">
                            <?php if (!$isSent): ?>
                                <img src="<?php echo htmlspecialchars($msg['avatar']); ?>" alt="Avatar" class="message-avatar">
                            <?php endif; ?>
                            <div class="message-content">
                                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                <div class="message-meta">
                                    <span class="message-time">
                                        <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                    </span>
                                    <?php if ($isSent): ?>
                                        <span class="read-status"
                                            title="<?php echo $msg['is_read'] ? ($msg['read_at'] ? 'Okundu: ' . date('d.m.Y H:i', strtotime($msg['read_at'])) : 'Okundu') : 'Gönderildi'; ?>">
                                            <?php if ($msg['is_read']): ?>
                                                <i class="fas fa-check-double" style="color: #38bdf8;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-check" style="color: var(--text-muted);"></i>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Reactions Display -->
                                <?php if (!empty($reactions)): ?>
                                    <div class="message-reactions">
                                        <?php foreach ($reactions as $emoji => $users): ?>
                                            <span class="reaction-badge" data-emoji="<?php echo htmlspecialchars($emoji); ?>">
                                                <?php echo $emoji; ?>                 <?php echo count($users); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Quick Reaction Button -->
                                <button class="reaction-trigger" onclick="showReactionPicker(this, <?php echo $msg['id']; ?>)">
                                    <i class="far fa-smile"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <div class="chat-input">
                    <form method="POST" action="" class="message-form" id="messageForm">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                        <button type="button" class="emoji-trigger" onclick="toggleEmojiPicker()">
                            <i class="far fa-smile"></i>
                        </button>
                        <input type="text" name="message" id="messageInput" class="form-control"
                            placeholder="Mesajınızı yazın..." required autocomplete="off">
                        <button type="submit" name="send_message" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>

                    <!-- Emoji Picker -->
                    <div class="emoji-picker" id="emojiPicker">
                        <div class="emoji-categories">
                            <button class="emoji-cat active" data-category="smileys">😀</button>
                            <button class="emoji-cat" data-category="gestures">👍</button>
                            <button class="emoji-cat" data-category="hearts">❤️</button>
                            <button class="emoji-cat" data-category="nature">🌸</button>
                            <button class="emoji-cat" data-category="food">🍕</button>
                        </div>
                        <div class="emoji-grid" id="emojiGrid">
                            <!-- Emojis loaded dynamically -->
                        </div>
                    </div>
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

<!-- Reaction Picker Popup -->
<div class="reaction-picker" id="reactionPicker">
    <button class="quick-reaction" data-emoji="❤️">❤️</button>
    <button class="quick-reaction" data-emoji="👍">👍</button>
    <button class="quick-reaction" data-emoji="😂">😂</button>
    <button class="quick-reaction" data-emoji="😮">😮</button>
    <button class="quick-reaction" data-emoji="😢">😢</button>
    <button class="quick-reaction" data-emoji="🔥">🔥</button>
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
        object-fit: cover;
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
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .chat-avatar:hover {
        transform: scale(1.1);
    }

    .chat-user-info {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chat-header h5 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .online-status {
        font-size: 0.75rem;
        color: #10b981;
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
        position: relative;
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
        object-fit: cover;
    }

    .message-content {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 0.75rem 1rem;
        position: relative;
    }

    .message-bubble.sent .message-content {
        background: var(--primary);
        border-color: var(--primary);
        color: #ffffff;
    }

    .message-content p {
        margin: 0 0 0.25rem 0;
        line-height: 1.5;
        color: inherit;
    }

    .message-bubble.received .message-content {
        color: var(--text-main);
    }

    .message-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .message-time {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .read-status {
        font-size: 0.75rem;
    }

    /* Reactions */
    .message-reactions {
        display: flex;
        gap: 0.25rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .reaction-badge {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 0.125rem 0.5rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .reaction-badge:hover {
        transform: scale(1.1);
    }

    .reaction-trigger {
        position: absolute;
        right: -30px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .message-bubble.sent .reaction-trigger {
        right: auto;
        left: -30px;
    }

    .message-bubble:hover .reaction-trigger {
        opacity: 1;
    }

    .reaction-trigger:hover {
        background: var(--primary);
        color: white;
    }

    /* Reaction Picker Popup */
    .reaction-picker {
        position: fixed;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 0.5rem;
        display: none;
        gap: 0.25rem;
        box-shadow: var(--shadow-lg);
        z-index: 1000;
    }

    .reaction-picker.show {
        display: flex;
    }

    .quick-reaction {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .quick-reaction:hover {
        background: var(--bg-glass);
        transform: scale(1.2);
    }

    /* Chat Input */
    .chat-input {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-glass);
        position: relative;
    }

    .message-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .message-form input {
        flex: 1;
    }

    .emoji-trigger {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        transition: all 0.2s ease;
        padding: 0.5rem;
    }

    .emoji-trigger:hover {
        color: var(--primary);
        transform: scale(1.1);
    }

    /* Emoji Picker */
    .emoji-picker {
        position: absolute;
        bottom: 100%;
        left: 1.5rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1rem;
        display: none;
        width: 320px;
        max-height: 300px;
        box-shadow: var(--shadow-lg);
        z-index: 1000;
    }

    .emoji-picker.show {
        display: block;
    }

    .emoji-categories {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
    }

    .emoji-cat {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .emoji-cat:hover,
    .emoji-cat.active {
        background: var(--bg-glass);
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 0.25rem;
        max-height: 200px;
        overflow-y: auto;
    }

    .emoji-btn {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .emoji-btn:hover {
        background: var(--bg-glass);
        transform: scale(1.2);
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

    /* Typing indicator */
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 1rem;
        align-items: center;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: var(--text-muted);
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-8px);
        }
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

        .emoji-picker {
            width: 280px;
            left: 0.5rem;
        }
    }
</style>

<script>
    // Emoji data
    const emojiData = {
        smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '🥸', '😎', '🤓', '🧐'],
        gestures: ['👍', '👎', '👊', '✊', '🤛', '🤜', '🤝', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✍️', '🦶', '🦵', '💪', '🦾', '🦿'],
        hearts: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '♥️', '💌', '💋', '👄', '💏', '💑', '🥰', '😍', '😘', '😻'],
        nature: ['🌸', '🌺', '🌹', '🌷', '🌻', '🌼', '💐', '🌿', '🌱', '🌲', '🌳', '🌴', '🌵', '🍀', '🍁', '🍂', '🍃', '🌾', '🌊', '🌈', '☀️', '🌙', '⭐', '🌟', '✨', '⚡', '🔥', '❄️', '💧', '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵'],
        food: ['🍕', '🍔', '🍟', '🌭', '🍿', '🧂', '🥓', '🥚', '🍳', '🧇', '🥞', '🧈', '🍞', '🥐', '🥖', '🥨', '🧀', '🥗', '🥙', '🥪', '🌮', '🌯', '🫔', '🥫', '🍝', '🍜', '🍲', '🍛', '🍣', '🍱', '🥟', '🍤', '🍙', '🍚', '🍘', '🍥', '🥠', '🍡', '🍧', '🍨', '🍦', '🥧', '🧁', '🍰', '🎂', '🍮', '🍭', '🍬', '🍫', '🍩', '🍪', '☕', '🍵', '🧋', '🥤', '🧃', '🍺', '🍻', '🥂', '🍷', '🥃']
    };

    let currentEmojiCategory = 'smileys';
    let currentMessageId = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Start polling for new messages
            startMessagePolling();
        }

        // Load initial emojis
        loadEmojis('smileys');

        // Category buttons
        document.querySelectorAll('.emoji-cat').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.emoji-cat').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                loadEmojis(this.dataset.category);
            });
        });

        // Close emoji picker when clicking outside
        document.addEventListener('click', function (e) {
            const picker = document.getElementById('emojiPicker');
            const trigger = document.querySelector('.emoji-trigger');
            if (picker && !picker.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) {
                picker.classList.remove('show');
            }

            const reactionPicker = document.getElementById('reactionPicker');
            if (reactionPicker && !reactionPicker.contains(e.target) && !e.target.closest('.reaction-trigger')) {
                reactionPicker.classList.remove('show');
            }
        });

        // Quick reaction buttons
        document.querySelectorAll('.quick-reaction').forEach(btn => {
            btn.addEventListener('click', function () {
                if (currentMessageId) {
                    addReaction(currentMessageId, this.dataset.emoji);
                }
            });
        });

        // Enter key to send message
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    // Form will submit naturally
                }
            });
        }
    });

    // Load emojis for category
    function loadEmojis(category) {
        const grid = document.getElementById('emojiGrid');
        if (!grid) return;

        grid.innerHTML = '';
        emojiData[category].forEach(emoji => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'emoji-btn';
            btn.textContent = emoji;
            btn.onclick = () => insertEmoji(emoji);
            grid.appendChild(btn);
        });
    }

    // Toggle emoji picker
    function toggleEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        picker.classList.toggle('show');
    }

    // Insert emoji into input
    function insertEmoji(emoji) {
        const input = document.getElementById('messageInput');
        if (input) {
            const start = input.selectionStart;
            const end = input.selectionEnd;
            input.value = input.value.substring(0, start) + emoji + input.value.substring(end);
            input.selectionStart = input.selectionEnd = start + emoji.length;
            input.focus();
        }
    }

    // Show reaction picker
    function showReactionPicker(trigger, messageId) {
        currentMessageId = messageId;
        const picker = document.getElementById('reactionPicker');
        const rect = trigger.getBoundingClientRect();
        picker.style.top = (rect.top - 50) + 'px';
        picker.style.left = rect.left + 'px';
        picker.classList.add('show');
    }

    // Add reaction to message
    function addReaction(messageId, emoji) {
        fetch('/lonely_eye/api/message_reaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message_id: messageId,
                reaction: emoji
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload to show new reaction (or update dynamically)
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));

        document.getElementById('reactionPicker').classList.remove('show');
    }

    // Poll for new messages (real-time simulation)
    let lastMessageId = 0;
    function startMessagePolling() {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;

        const userId = chatMessages.dataset.userId;

        // Get last message ID
        const messages = chatMessages.querySelectorAll('.message-bubble');
        if (messages.length > 0) {
            lastMessageId = parseInt(messages[messages.length - 1].dataset.messageId) || 0;
        }

        // Poll every 3 seconds
        setInterval(() => {
            fetch(`/lonely_eye/api/get_new_messages.php?user_id=${userId}&last_id=${lastMessageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = msg.id;
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }

                    // Update read status
                    if (data.read_updates) {
                        data.read_updates.forEach(update => {
                            const msgEl = document.querySelector(`[data-message-id="${update.id}"] .read-status i`);
                            if (msgEl) {
                                msgEl.className = 'fas fa-check-double';
                                msgEl.style.color = '#38bdf8';
                            }
                        });
                    }
                })
                .catch(error => console.error('Polling error:', error));
        }, 3000);
    }

    // Append new message to chat
    function appendMessage(msg) {
        const chatMessages = document.getElementById('chatMessages');
        const isSent = msg.sender_id === <?php echo $current_user_id; ?>;

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isSent ? 'sent' : 'received'}`;
        bubble.dataset.messageId = msg.id;

        bubble.innerHTML = `
            ${!isSent ? `<img src="${msg.avatar}" alt="Avatar" class="message-avatar">` : ''}
            <div class="message-content">
                <p>${msg.message.replace(/\n/g, '<br>')}</p>
                <div class="message-meta">
                    <span class="message-time">${msg.time}</span>
                    ${isSent ? `
                        <span class="read-status">
                            <i class="fas fa-check" style="color: var(--text-muted);"></i>
                        </span>
                    ` : ''}
                </div>
                <button class="reaction-trigger" onclick="showReactionPicker(this, ${msg.id})">
                    <i class="far fa-smile"></i>
                </button>
            </div>
        `;

        chatMessages.appendChild(bubble);
    }
</script>

<?php require_once 'includes/footer.php'; ?>