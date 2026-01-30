<?php
/**
 * Search Page - Multi-Source Search Engine
 * Searches in: Local Books, Users, Google Books API
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'books';

$local_books = [];
$users = [];
$google_books = [];

// ============================================
// SEARCH LOCAL BOOKS
// ============================================
if (!empty($query)) {
    try {
        $search_term = "%$query%";
        $stmt = $pdo->prepare("
            SELECT id, title, author, cover_image, rating_score 
            FROM items 
            WHERE title LIKE ? OR author LIKE ?
            ORDER BY rating_score DESC
            LIMIT 50
        ");
        $stmt->execute([$search_term, $search_term]);
        $local_books = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Search Error: " . $e->getMessage());
    }

    // ============================================
    // SEARCH USERS
    // ============================================
    try {
        $stmt = $pdo->prepare("
            SELECT id, username, avatar, email,
                   (SELECT COUNT(*) FROM follows WHERE following_id = users.id) as follower_count
            FROM users 
            WHERE username LIKE ? OR email LIKE ?
            ORDER BY follower_count DESC
            LIMIT 20
        ");
        $stmt->execute([$search_term, $search_term]);
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("User Search Error: " . $e->getMessage());
    }

    // ============================================
    // SEARCH GOOGLE BOOKS API
    // ============================================
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query);
    $api_url .= "&maxResults=40&orderBy=relevance";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['items'])) {
            $google_books = $data['items'];
        }
    }
}

$page_title = "Arama: " . htmlspecialchars($query);
require_once 'includes/header.php';
?>

<style>
    .search-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .search-header {
        margin-bottom: 2rem;
    }

    .search-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .search-header .query {
        color: var(--primary);
    }

    .search-stats {
        color: var(--text-muted);
        font-size: 1rem;
    }

    /* Tabs */
    .search-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border-color);
    }

    .tab-btn {
        padding: 1rem 2rem;
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        position: relative;
        bottom: -2px;
    }

    .tab-btn:hover {
        color: var(--primary);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Books Grid */
    .books-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .book-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .book-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    .book-card-body {
        padding: 1rem;
    }

    .book-card-body h6 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .book-card-body .author {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Users Grid */
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
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .user-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid var(--primary);
        margin: 0 auto 1rem;
    }

    .user-card h5 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .user-card .stats {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 1rem;
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
</style>

<!-- Sidebar -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php include 'includes/sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content"
    style="<?php echo isset($_SESSION['user_id']) ? 'margin-left: 280px;' : ''; ?> padding: 2rem;">
    <div class="search-container">
        <!-- Header -->
        <div class="search-header">
            <h1>
                <i class="fas fa-search"></i>
                Arama: <span class="query">"<?php echo htmlspecialchars($query); ?>"</span>
            </h1>
            <p class="search-stats">
                <?php
                $total = count($local_books) + count($users) + count($google_books);
                echo "$total sonuç bulundu";
                ?>
            </p>
        </div>

        <!-- Tabs -->
        <div class="search-tabs">
            <button class="tab-btn <?php echo $active_tab === 'books' ? 'active' : ''; ?>" data-tab="books">
                <i class="fas fa-book"></i> Kitaplar (<?php echo count($local_books); ?>)
            </button>
            <button class="tab-btn <?php echo $active_tab === 'users' ? 'active' : ''; ?>" data-tab="users">
                <i class="fas fa-users"></i> Kullanıcılar (<?php echo count($users); ?>)
            </button>
            <button class="tab-btn <?php echo $active_tab === 'google' ? 'active' : ''; ?>" data-tab="google">
                <i class="fab fa-google"></i> Google Sonuçları (<?php echo count($google_books); ?>)
            </button>
        </div>

        <!-- Local Books Tab -->
        <div class="tab-content <?php echo $active_tab === 'books' ? 'active' : ''; ?>" id="books">
            <?php if (!empty($local_books)): ?>
                <div class="books-grid">
                    <?php foreach ($local_books as $book): ?>
                        <a href="item-detail.php?id=<?php echo $book['id']; ?>" class="book-card">
                            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                                alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="book-card-body">
                                <h6><?php echo htmlspecialchars($book['title']); ?></h6>
                                <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="rating">
                                    <i class="fas fa-star" style="color: #FFD700;"></i>
                                    <span><?php echo number_format($book['rating_score'], 1); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h3>Kitap Bulunamadı</h3>
                    <p>Veritabanında bu arama için sonuç yok.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Users Tab -->
        <div class="tab-content <?php echo $active_tab === 'users' ? 'active' : ''; ?>" id="users">
            <?php if (!empty($users)): ?>
                <div class="users-grid">
                    <?php foreach ($users as $user): ?>
                        <div class="user-card">
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>"
                                alt="<?php echo htmlspecialchars($user['username']); ?>">
                            <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                            <p class="stats">
                                <i class="fas fa-users"></i> <?php echo $user['follower_count']; ?> Takipçi
                            </p>
                            <a href="profile.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Profili Gör
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Kullanıcı Bulunamadı</h3>
                    <p>Bu arama için kullanıcı sonucu yok.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Google Books Tab -->
        <div class="tab-content <?php echo $active_tab === 'google' ? 'active' : ''; ?>" id="google">
            <?php if (!empty($google_books)): ?>
                <div class="books-grid">
                    <?php foreach ($google_books as $item): ?>
                        <?php
                        $volumeInfo = $item['volumeInfo'] ?? [];
                        $title = $volumeInfo['title'] ?? 'Untitled';
                        $authors = isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown';
                        $thumbnail = $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop';
                        $thumbnail = str_replace('http://', 'https://', $thumbnail);
                        ?>
                        <div class="book-card">
                            <img src="<?php echo htmlspecialchars($thumbnail); ?>"
                                alt="<?php echo htmlspecialchars($title); ?>">
                            <div class="book-card-body">
                                <h6><?php echo htmlspecialchars($title); ?></h6>
                                <p class="author"><?php echo htmlspecialchars($authors); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fab fa-google"></i>
                    <h3>Google'da Sonuç Yok</h3>
                    <p>Bu arama için Google Books'ta sonuç bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Tab Switching
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const tabName = this.getAttribute('data-tab');

                // Remove active class from all
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked
                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>