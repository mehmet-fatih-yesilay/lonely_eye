<?php
/**
 * Discover - Find and Follow Other Users
 * Social networking page
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ============================================
// FETCH ALL USERS EXCEPT CURRENT USER
// ============================================
$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.username,
        u.email,
        u.avatar,
        u.created_at,
        COUNT(DISTINCT f1.follower_id) as follower_count,
        COUNT(DISTINCT f2.following_id) as following_count,
        EXISTS(
            SELECT 1 FROM follows 
            WHERE follower_id = ? AND following_id = u.id
        ) as is_following
    FROM users u
    LEFT JOIN follows f1 ON f1.following_id = u.id
    LEFT JOIN follows f2 ON f2.follower_id = u.id
    WHERE u.id != ?
    GROUP BY u.id
    ORDER BY follower_count DESC, u.created_at DESC
");
$stmt->execute([$user_id, $user_id]);
$users = $stmt->fetchAll();

// ============================================
// GET USER TITLES BASED ON ACTIVITY
// ============================================
function getUserTitle($follower_count)
{
    if ($follower_count >= 100)
        return "Efsane Okuyucu";
    if ($follower_count >= 50)
        return "Kitap Gurusu";
    if ($follower_count >= 20)
        return "Aktif Okuyucu";
    if ($follower_count >= 5)
        return "Kitap Kurdu";
    return "Yeni Üye";
}

$page_title = "İnsanları Keşfet";
require_once 'includes/header.php';
?>

<style>
    .main-content {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    .discover-header {
        margin-bottom: 2rem;
    }

    .discover-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .discover-header p {
        color: var(--text-muted);
        font-size: 1.125rem;
    }

    /* Search Bar */
    .search-section {
        margin-bottom: 2rem;
    }

    .search-bar {
        position: relative;
        max-width: 600px;
    }

    .search-bar input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        color: var(--text-main);
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-bar input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-glow);
    }

    .search-bar i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    /* User Cards Grid */
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .user-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        cursor: pointer;
        position: relative;
    }

    .user-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
        background: var(--bg-glass);
    }

    .user-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        border: 3px solid var(--primary);
        box-shadow: 0 0 20px var(--primary-glow);
        object-fit: cover;
        background: var(--bg-glass);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }

    .user-title {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .user-stats {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1.5rem;
        padding: 1rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .user-stat {
        text-align: center;
    }

    .user-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        display: block;
    }

    .user-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-follow {
        flex: 1;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-follow.following {
        background: var(--bg-glass);
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }

    .btn-follow.following:hover {
        background: #ff0055;
        color: white;
        border-color: #ff0055;
    }

    .btn-profile {
        display: none;
        /* Hidden - entire card is now clickable */
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .users-grid {
            grid-template-columns: 1fr;
        }

        .user-stats {
            gap: 1rem;
        }
    }
</style>

<!-- Include Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">

    <!-- Header -->
    <div class="discover-header">
        <h1>
            <i class="fas fa-users"></i>
            İnsanları Keşfet
        </h1>
        <p>Kitap tutkunlarıyla tanış, takip et ve etkileşime geç</p>
    </div>

    <!-- Search Bar -->
    <div class="search-section">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Kullanıcı ara...">
        </div>
    </div>

    <!-- Users Grid -->
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-user-friends"></i>
            <h3>Henüz Başka Kullanıcı Yok</h3>
            <p>İlk kullanıcılardan birisin! Arkadaşlarını davet et.</p>
        </div>
    <?php else: ?>
        <div class="users-grid" id="usersGrid">
            <?php foreach ($users as $user): ?>
                <div class="user-card" data-username="<?php echo htmlspecialchars($user['username']); ?>"
                    onclick="window.location.href='profile.php?id=<?php echo $user['id']; ?>'">
                    <!-- Avatar -->
                    <div class="user-avatar">
                        <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>"
                                alt="<?php echo htmlspecialchars($user['username']); ?>">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>

                    <!-- User Info -->
                    <h3 class="user-name">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h3>
                    <div class="user-title">
                        <i class="fas fa-award" style="color: #FFD700;"></i>
                        <?php echo getUserTitle($user['follower_count']); ?>
                    </div>

                    <!-- Stats -->
                    <div class="user-stats">
                        <div class="user-stat">
                            <span class="user-stat-value"><?php echo $user['follower_count']; ?></span>
                            <span class="user-stat-label">Takipçi</span>
                        </div>
                        <div class="user-stat">
                            <span class="user-stat-value"><?php echo $user['following_count']; ?></span>
                            <span class="user-stat-label">Takip</span>
                        </div>
                    </div>

                    <!-- Actions - Only follow button, clicking card goes to profile -->
                    <div class="user-actions">
                        <button class="btn btn-primary btn-follow <?php echo $user['is_following'] ? 'following' : ''; ?>"
                            onclick="event.stopPropagation(); toggleFollow(<?php echo $user['id']; ?>, this)">
                            <?php if ($user['is_following']): ?>
                                <i class="fas fa-user-check"></i>
                                Takip Ediliyor
                            <?php else: ?>
                                <i class="fas fa-user-plus"></i>
                                Takip Et
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
    // Live Search Functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('userSearch');
        const usersGrid = document.getElementById('usersGrid');

        if (searchInput && usersGrid) {
            const userCards = usersGrid.querySelectorAll('.user-card');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase().trim();

                userCards.forEach(card => {
                    const username = card.getAttribute('data-username').toLowerCase();

                    if (username.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });

    // Follow/Unfollow Function (Already defined in script.js, but adding inline for safety)
    function toggleFollow(userId, buttonElement) {
        const isFollowing = buttonElement.classList.contains('following');
        const action = isFollowing ? 'unfollow' : 'follow';

        // Send AJAX request
        fetch('/lonely_eye/api/follow.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId,
                action: action
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    if (action === 'follow') {
                        buttonElement.classList.add('following');
                        buttonElement.innerHTML = '<i class="fas fa-user-check"></i> Takip Ediliyor';
                    } else {
                        buttonElement.classList.remove('following');
                        buttonElement.innerHTML = '<i class="fas fa-user-plus"></i> Takip Et';
                    }

                    // Update follower count
                    const statValue = buttonElement.closest('.user-card').querySelector('.user-stat-value');
                    if (statValue) {
                        const currentCount = parseInt(statValue.textContent);
                        statValue.textContent = action === 'follow' ? currentCount + 1 : currentCount - 1;
                    }
                } else {
                    alert('İşlem başarısız: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            });
    }
</script>

<?php require_once 'includes/footer.php'; ?>