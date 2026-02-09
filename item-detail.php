<?php
/**
 * Item Detail Page - Book/Magazine Details
 * Premium Design with Glassmorphism
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get item ID from URL
$item_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($item_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Fetch item details
$stmt = $pdo->prepare("
    SELECT i.*, g.name as genre_name, g.color_code 
    FROM items i 
    LEFT JOIN genres g ON i.genre_id = g.id 
    WHERE i.id = ?
");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: dashboard.php');
    exit;
}

// Increment view count
$stmt = $pdo->prepare("UPDATE items SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$item_id]);

// Handle review submission
$review_error = '';
$review_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $review_error = 'Lütfen 1-5 arası bir puan seçin.';
    } elseif (empty($comment)) {
        $review_error = 'Lütfen bir yorum yazın.';
    } else {
        try {
            // Check if user already reviewed this item
            $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND item_id = ?");
            $stmt->execute([$_SESSION['user_id'], $item_id]);

            if ($stmt->fetch()) {
                $review_error = 'Bu kitap için zaten yorum yaptınız.';
            } else {
                // Insert review
                $stmt = $pdo->prepare("
                    INSERT INTO reviews (user_id, item_id, rating, comment, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $item_id, $rating, $comment]);

                // Update item rating
                $stmt = $pdo->prepare("
                    UPDATE items 
                    SET rating_score = (
                        SELECT AVG(rating) FROM reviews WHERE item_id = ?
                    ) 
                    WHERE id = ?
                ");
                $stmt->execute([$item_id, $item_id]);

                $review_success = 'Yorumunuz başarıyla eklendi!';

                // Refresh page to show new review
                header("Refresh:2");
            }
        } catch (PDOException $e) {
            error_log("Review Error: " . $e->getMessage());
            $review_error = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

// Fetch reviews for this item
$stmt = $pdo->prepare("
    SELECT r.*, u.username, u.avatar 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.item_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$item_id]);
$reviews = $stmt->fetchAll();

// User data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$avatar = $_SESSION['avatar'];

$page_title = $item['title'];
require_once 'includes/header.php';
?>

<style>
    /* Sidebar Styles (Same as dashboard) */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color);
        padding: 2rem 1.5rem;
        overflow-y: auto;
        z-index: 999;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-logo i {
        font-size: 2rem;
        color: var(--primary);
    }

    .sidebar-logo h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
    }

    .sidebar-profile {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-glass);
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }

    .sidebar-profile img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary);
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem 0;
    }

    .sidebar-menu li {
        margin-bottom: 0.5rem;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .sidebar-menu a:hover {
        background: rgba(56, 189, 248, 0.1);
        color: var(--primary);
    }

    .sidebar-menu a i {
        width: 20px;
        text-align: center;
    }

    .main-content {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    /* Hero Section */
    .item-hero {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 3rem;
        margin-bottom: 3rem;
        box-shadow: var(--shadow-lg);
    }

    .item-hero-content {
        display: flex;
        gap: 3rem;
        align-items: flex-start;
    }

    .item-cover {
        flex-shrink: 0;
        width: 300px;
        height: 450px;
        border-radius: var(--radius-lg);
        object-fit: cover;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 3px solid var(--primary);
    }

    .item-info {
        flex: 1;
    }

    .item-info h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: var(--text-main);
    }

    .item-author {
        font-size: 1.25rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .item-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .item-rating i {
        color: #FFD700;
    }

    .item-rating .score {
        font-weight: 700;
        color: var(--text-main);
    }

    .item-meta {
        display: flex;
        gap: 2rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .item-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
    }

    .item-meta-item i {
        color: var(--primary);
    }

    .item-description {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 2rem;
        font-size: 1.05rem;
    }

    .item-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Reviews Section */
    .reviews-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 3rem;
    }

    .reviews-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .reviews-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .review-form {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .review-form h4 {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        color: var(--text-main);
    }

    .rating-select {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .rating-select input[type="radio"] {
        display: none;
    }

    .rating-select label {
        font-size: 2rem;
        color: var(--border-color);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .rating-select input[type="radio"]:checked~label,
    .rating-select label:hover,
    .rating-select label:hover~label {
        color: #FFD700;
    }

    .review-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .review-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .review-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary);
    }

    .review-user-info h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .review-user-info .review-date {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .review-rating {
        margin-left: auto;
        display: flex;
        gap: 0.25rem;
    }

    .review-rating i {
        color: #FFD700;
        font-size: 0.875rem;
    }

    .review-comment {
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* Reply Styles */
    .reply-btn {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .reply-form-container {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
    }

    .reply-input {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        resize: none;
    }

    .reply-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.25);
    }

    .replies-container {
        margin-left: 2rem;
        border-left: 2px solid var(--border-color);
        padding-left: 1rem;
    }

    .reply-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .reply-item:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
    }

    .reply-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .reply-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid var(--primary);
    }

    .reply-username {
        font-weight: 600;
        color: var(--text-main);
        margin: 0;
        font-size: 0.9rem;
    }

    .reply-date {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin: 0;
    }

    .reply-text {
        color: var(--text-muted);
        line-height: 1.6;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .nested-replies {
        margin-left: 1.5rem;
        margin-top: 0.75rem;
        border-left: 2px solid rgba(56, 189, 248, 0.3);
        padding-left: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .item-hero {
            padding: 1.5rem;
        }

        .item-hero-content {
            flex-direction: column;
            gap: 2rem;
        }

        .item-cover {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .item-info h1 {
            font-size: 1.75rem;
        }
    }
</style>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <i class="fas fa-eye"></i>
        <h3>Lonely Eye</h3>
    </div>

    <div class="sidebar-profile">
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
        <div class="sidebar-profile-info">
            <h5>
                <?php echo htmlspecialchars($username); ?>
            </h5>
            <p>Okuyucu</p>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
        <li><a href="discover.php"><i class="fas fa-users"></i> İnsanları Keşfet</a></li>
        <li><a href="library.php"><i class="fas fa-book"></i> Kitaplığım</a></li>
        <li><a href="messages.php"><i class="fas fa-envelope"></i> Mesajlar</a></li>
        <li><a href="profile.php?id=<?php echo $user_id; ?>"><i class="fas fa-user"></i> Profilim</a></li>
    </ul>

    <a href="logout.php" class="btn btn-outline-primary w-100">
        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
    </a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>

    <!-- Item Hero Section -->
    <div class="item-hero fade-in">
        <div class="item-hero-content">
            <img src="<?php echo htmlspecialchars($item['cover_image']); ?>"
                alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-cover">

            <div class="item-info">
                <h1>
                    <?php echo htmlspecialchars($item['title']); ?>
                </h1>

                <p class="item-author">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($item['author']); ?>
                </p>

                <div class="item-rating">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star<?php echo $i < floor($item['rating_score']) ? '' : '-o'; ?>"></i>
                    <?php endfor; ?>
                    <span class="score">
                        <?php echo number_format($item['rating_score'], 1); ?>
                    </span>
                    <span class="text-muted">(
                        <?php echo count($reviews); ?> yorum)
                    </span>
                </div>

                <?php if ($item['genre_name']): ?>
                    <div class="mb-3">
                        <span class="genre-tag"
                            style="background: <?php echo htmlspecialchars($item['color_code']); ?>; color: white;">
                            <i class="fas fa-bookmark"></i>
                            <?php echo htmlspecialchars($item['genre_name']); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="item-meta">
                    <div class="item-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>
                            <?php echo htmlspecialchars($item['publication_year']); ?>
                        </span>
                    </div>
                    <div class="item-meta-item">
                        <i class="fas fa-file-alt"></i>
                        <span>
                            <?php echo number_format($item['page_count']); ?> sayfa
                        </span>
                    </div>
                    <div class="item-meta-item">
                        <i class="fas fa-eye"></i>
                        <span>
                            <?php echo number_format($item['view_count']); ?> görüntülenme
                        </span>
                    </div>
                    <div class="item-meta-item">
                        <i class="fas fa-<?php echo $item['type'] === 'book' ? 'book' : 'newspaper'; ?>"></i>
                        <span>
                            <?php echo $item['type'] === 'book' ? 'Kitap' : 'Dergi'; ?>
                        </span>
                    </div>
                </div>

                <p class="item-description">
                    <?php echo htmlspecialchars($item['description'] ?? 'Bu eser hakkında henüz bir açıklama eklenmemiş. Ancak yüksek puanı ve okuyucu yorumları, kaliteli bir içerik olduğunu gösteriyor.'); ?>
                </p>

                <div class="item-actions">
                    <button id="btnFavorite" class="btn btn-primary btn-lg" onclick="toggleFavorite()">
                        <i class="fas fa-heart"></i> Favorilere Ekle
                    </button>
                    <button class="btn btn-secondary btn-lg">
                        <i class="fas fa-share-alt"></i> Paylaş
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <div class="reviews-header">
            <i class="fas fa-comments text-primary" style="font-size: 2rem;"></i>
            <h3>Yorumlar & İncelemeler</h3>
        </div>

        <!-- Review Form -->
        <div class="review-form">
            <h4><i class="fas fa-pen"></i> Yorum Yap</h4>

            <?php if ($review_success): ?>
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($review_success); ?>
                </div>
            <?php endif; ?>

            <?php if ($review_error): ?>
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($review_error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Puanınız</label>
                    <div class="rating-select" style="flex-direction: row-reverse; justify-content: flex-end;">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1"><i class="fas fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Yorumunuz</label>
                    <textarea name="comment" class="form-control" rows="4"
                        placeholder="Bu eser hakkında düşüncelerinizi paylaşın..." required></textarea>
                </div>

                <button type="submit" name="submit_review" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Yorumu Gönder
                </button>
            </form>
        </div>

        <!-- Reviews List -->
        <?php if (count($reviews) > 0): ?>
            <h4 class="mb-3">Tüm Yorumlar (
                <?php echo count($reviews); ?>)
            </h4>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card" data-review-id="<?php echo $review['id']; ?>">
                    <div class="review-header">
                        <img src="<?php echo htmlspecialchars($review['avatar']); ?>" alt="Avatar" class="review-avatar">
                        <div class="review-user-info">
                            <h5>
                                <?php echo htmlspecialchars($review['username']); ?>
                            </h5>
                            <p class="review-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?>
                            </p>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i < $review['rating'] ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="review-comment">
                        <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                    </p>

                    <!-- Reply Button -->
                    <button class="btn btn-sm btn-outline-primary reply-btn"
                        onclick="toggleReplyForm(<?php echo $review['id']; ?>, null, '<?php echo addslashes($review['username']); ?>')">
                        <i class="fas fa-reply"></i> Yanıtla
                    </button>

                    <!-- Reply Form (Hidden by default) -->
                    <div class="reply-form-container mt-3" id="reply-form-<?php echo $review['id']; ?>-null"
                        style="display: none;">
                        <div class="d-flex gap-2">
                            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar"
                                style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--primary);">
                            <div class="flex-grow-1">
                                <textarea class="form-control reply-input" rows="2" placeholder="Yanıtınızı yazın..."
                                    id="reply-text-<?php echo $review['id']; ?>-null"></textarea>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary"
                                        onclick="postReply(<?php echo $review['id']; ?>, null)">
                                        <i class="fas fa-paper-plane"></i> Gönder
                                    </button>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="toggleReplyForm(<?php echo $review['id']; ?>, null)">
                                        İptal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Replies Container -->
                    <div class="replies-container mt-3" id="replies-<?php echo $review['id']; ?>">
                        <!-- Replies will be loaded here -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Theme Toggle
    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.querySelector('.theme-toggle');
        const themeIcon = document.querySelector('.theme-icon');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || 'dark';
        htmlElement.setAttribute('data-theme', savedTheme);
        themeIcon.className = savedTheme === 'dark' ? 'fas fa-moon theme-icon' : 'fas fa-sun theme-icon';

        if (themeToggle) {
            themeToggle.addEventListener('click', function () {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                themeIcon.className = newTheme === 'dark' ? 'fas fa-moon theme-icon' : 'fas fa-sun theme-icon';
            });
        }

        // Check initial favorite status
        checkFavoriteStatus();
    });

    // Toggle Favorite Function
    function toggleFavorite() {
        const itemId = <?php echo $item_id; ?>;
        const googleId = '<?php echo $item['google_id'] ?? ''; ?>';
        const btn = document.getElementById('btnFavorite');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';

        const data = {
            item_id: itemId,
            google_id: googleId,
            title: '<?php echo addslashes($item['title']); ?>',
            author: '<?php echo addslashes($item['author']); ?>',
            description: '<?php echo addslashes($item['description'] ?? ''); ?>',
            cover_image: '<?php echo addslashes($item['cover_image']); ?>',
            language: '<?php echo $item['language'] ?? 'en'; ?>'
        };

        fetch('/lonely_eye/api/toggle_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    if (result.is_favorite) {
                        btn.className = 'btn btn-danger btn-lg';
                        btn.innerHTML = '<i class="fas fa-heart"></i> Favorilerden Çıkar';
                    } else {
                        btn.className = 'btn btn-primary btn-lg';
                        btn.innerHTML = '<i class="fas fa-heart"></i> Favorilere Ekle';
                    }

                    // Show success message
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Bir hata oluştu', 'error');
            })
            .finally(() => {
                btn.disabled = false;
            });
    }

    // Check if book is already favorited
    function checkFavoriteStatus() {
        const itemId = <?php echo $item_id; ?>;

        fetch(`/lonely_eye/api/check_favorite.php?item_id=${itemId}`)
            .then(response => response.json())
            .then(result => {
                if (result.is_favorite) {
                    const btn = document.getElementById('btnFavorite');
                    btn.className = 'btn btn-danger btn-lg';
                    btn.innerHTML = '<i class="fas fa-heart"></i> Favorilerden Çıkar';
                }
            })
            .catch(error => console.error('Error checking favorite:', error));
    }

    // Show notification
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} fade-in`;
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.innerHTML = `<i class="fas ${iconClass}"></i> ${message}`;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }

    // ============================================
    // REPLY SYSTEM FUNCTIONS
    // ============================================

    // Load replies when page loads
    document.addEventListener('DOMContentLoaded', function () {
        // Load replies for all reviews
        const reviews = document.querySelectorAll('[data-review-id]');
        reviews.forEach(review => {
            const reviewId = review.getAttribute('data-review-id');
            loadReplies(reviewId);
        });
    });

    // Toggle reply form visibility
    function toggleReplyForm(reviewId, parentReplyId, username = '') {
        const formId = `reply-form-${reviewId}-${parentReplyId}`;
        const form = document.getElementById(formId);

        if (form) {
            if (form.style.display === 'none') {
                // Hide all other reply forms first
                document.querySelectorAll('.reply-form-container').forEach(f => f.style.display = 'none');
                form.style.display = 'block';

                // Focus on textarea
                const textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                    if (username && parentReplyId !== null) {
                        textarea.placeholder = `@${username} kullanıcısına yanıt yazın...`;
                    }
                }
            } else {
                form.style.display = 'none';
            }
        }
    }

    // Post a reply
    function postReply(reviewId, parentReplyId) {
        const textareaId = `reply-text-${reviewId}-${parentReplyId}`;
        const textarea = document.getElementById(textareaId);
        const comment = textarea.value.trim();

        if (!comment) {
            showNotification('Yanıt boş olamaz', 'error');
            return;
        }

        if (comment.length > 1000) {
            showNotification('Yanıt çok uzun (max 1000 karakter)', 'error');
            return;
        }

        // Disable textarea and buttons
        textarea.disabled = true;
        const buttons = textarea.closest('.reply-form-container').querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);

        fetch('/lonely_eye/api/post_reply.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                review_id: reviewId,
                parent_reply_id: parentReplyId,
                comment: comment
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Yanıt başarıyla eklendi', 'success');

                    // Clear textarea
                    textarea.value = '';

                    // Hide form
                    toggleReplyForm(reviewId, parentReplyId);

                    // Reload replies
                    loadReplies(reviewId);
                } else {
                    showNotification(result.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Error posting reply:', error);
                showNotification('Bir hata oluştu', 'error');
            })
            .finally(() => {
                textarea.disabled = false;
                buttons.forEach(btn => btn.disabled = false);
            });
    }

    // Load replies for a review
    function loadReplies(reviewId) {
        const container = document.getElementById(`replies-${reviewId}`);
        if (!container) return;

        fetch(`/lonely_eye/api/get_replies.php?review_id=${reviewId}`)
            .then(response => response.json())
            .then(result => {
                if (result.success && result.replies.length > 0) {
                    container.innerHTML = renderReplies(result.replies, reviewId);
                } else {
                    container.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error loading replies:', error);
            });
    }

    // Render replies recursively (Instagram-style nested structure)
    function renderReplies(replies, reviewId, level = 0) {
        let html = '';

        replies.forEach(reply => {
            const date = new Date(reply.created_at);
            const formattedDate = date.toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            html += `
                <div class="reply-item" data-reply-id="${reply.id}">
                    <div class="reply-header">
                        <img src="${reply.avatar}" alt="${reply.username}" class="reply-avatar">
                        <div>
                            <p class="reply-username">${reply.username}</p>
                            <p class="reply-date"><i class="fas fa-clock"></i> ${formattedDate}</p>
                        </div>
                    </div>
                    <p class="reply-text">${escapeHtml(reply.comment)}</p>
                    
                    <button class="btn btn-sm btn-outline-primary reply-btn" 
                            onclick="toggleReplyForm(${reviewId}, ${reply.id}, '${escapeHtml(reply.username)}')">
                        <i class="fas fa-reply"></i> Yanıtla
                    </button>
                    
                    <!-- Nested Reply Form -->
                    <div class="reply-form-container mt-2" id="reply-form-${reviewId}-${reply.id}" style="display: none;">
                        <div class="d-flex gap-2">
                            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" 
                                 style="width: 35px; height: 35px; border-radius: 50%; border: 2px solid var(--primary);">
                            <div class="flex-grow-1">
                                <textarea class="form-control reply-input" rows="2" 
                                          placeholder="Yanıtınızı yazın..." 
                                          id="reply-text-${reviewId}-${reply.id}"></textarea>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="postReply(${reviewId}, ${reply.id})">
                                        <i class="fas fa-paper-plane"></i> Gönder
                                    </button>
                                    <button class="btn btn-sm btn-secondary" 
                                            onclick="toggleReplyForm(${reviewId}, ${reply.id})">
                                        İptal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            `;

            // Render nested replies
            if (reply.replies && reply.replies.length > 0) {
                html += `<div class="nested-replies">`;
                html += renderReplies(reply.replies, reviewId, level + 1);
                html += `</div>`;
            }

            html += `</div>`;
        });

        return html;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php require_once 'includes/footer.php'; ?>