<?php
/**
 * Library - Complete Book Archive with Infinite Scroll
 * Horizontal category bar + infinite loading
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';

// ============================================
// FETCH GENRES FOR CATEGORY BAR
// ============================================
$stmt = $pdo->query("SELECT * FROM genres ORDER BY name");
$genres = $stmt->fetchAll();

// ============================================
// FETCH INITIAL BOOKS (First 40)
// ============================================
$limit = 40;
$books = [];

if (!empty($selected_category)) {
    // Filter by category
    $stmt = $pdo->prepare("
        SELECT i.id, i.title, i.author, i.cover_image, i.rating_score 
        FROM items i 
        LEFT JOIN genres g ON i.genre_id = g.id 
        WHERE g.name = ?
        ORDER BY i.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$selected_category, $limit]);
} else {
    // All books
    $stmt = $pdo->prepare("
        SELECT id, title, author, cover_image, rating_score 
        FROM items 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
}

$db_books = $stmt->fetchAll();

foreach ($db_books as $book) {
    $books[] = [
        'id' => $book['id'],
        'title' => $book['title'],
        'author' => $book['author'],
        'cover_image' => $book['cover_image'],
        'rating_score' => $book['rating_score'],
        'source' => 'database'
    ];
}

// Fill with API if needed
$items_needed = $limit - count($books);

if ($items_needed > 0) {
    $subjects = ['History', 'Fiction', 'Science', 'Philosophy', 'Psychology', 'Literature'];
    $random_subject = $subjects[array_rand($subjects)];
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=subject:$random_subject&orderBy=relevance&maxResults=$items_needed&langRestrict=tr";

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

        if (isset($data['items']) && !empty($data['items'])) {
            foreach ($data['items'] as $api_item) {
                $volumeInfo = $api_item['volumeInfo'] ?? [];

                $books[] = [
                    'id' => 0,
                    'title' => $volumeInfo['title'] ?? 'Untitled',
                    'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
                    'cover_image' => str_replace('http://', 'https://', $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop'),
                    'rating_score' => rand(35, 50) / 10,
                    'source' => 'api'
                ];

                if (count($books) >= $limit)
                    break;
            }
        }
    }
}

$page_title = "Kitaplar";
require_once 'includes/header.php';
?>

<style>
    .main-content {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    .library-header {
        margin-bottom: 2rem;
    }

    .library-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .library-header p {
        color: var(--text-muted);
        font-size: 1.125rem;
    }

    /* Horizontal Category Bar (Same as Dashboard) */
    .categories-section {
        margin-bottom: 2rem;
    }

    .categories-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .categories-scroll {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 1rem 0;
        scrollbar-width: thin;
    }

    .categories-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .categories-scroll::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    /* Items Grid */
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .item-card-modern {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .item-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .item-card-modern img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
    }

    .item-card-modern-body {
        padding: 1rem;
    }

    .item-card-modern-body h6 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--text-main);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-card-modern-body .author {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-card-modern-body .rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
    }

    .item-card-modern-body .rating i {
        color: #FFD700;
        font-size: 0.75rem;
    }

    .item-card-modern-body .rating span {
        color: var(--text-main);
        font-weight: 600;
    }

    /* Loading Indicator */
    .loading-indicator {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
        display: none;
    }

    .loading-indicator.active {
        display: block;
    }

    .loading-indicator i {
        font-size: 2rem;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .items-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .item-card-modern img {
            height: 220px;
        }
    }
</style>

<!-- Include Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">

    <!-- Header -->
    <div class="library-header">
        <h1>
            <i class="fas fa-book"></i>
            Kitap Arşivi
        </h1>
        <p>Binlerce kitap arasından keşfet, oku, paylaş</p>
    </div>

    <!-- Horizontal Category Bar -->
    <div class="categories-section">
        <h2><i class="fas fa-th-large"></i> Kategoriler</h2>
        <div class="categories-scroll">
            <a href="library.php"
                class="badge <?php echo empty($selected_category) ? 'badge-primary' : 'badge-secondary'; ?>"
                style="padding: 0.75rem 1.5rem; font-size: 0.875rem; text-decoration: none; white-space: nowrap;">
                <i class="fas fa-th"></i>
                Tümü
            </a>
            <?php
            $badge_classes = ['badge-primary', 'badge-secondary', 'badge-success', 'badge-warning', 'badge-info'];
            $badge_index = 0;
            foreach ($genres as $genre):
                $is_active = ($selected_category === $genre['name']);
                $badge_class = $is_active ? 'badge-primary' : $badge_classes[$badge_index % count($badge_classes)];
                $badge_index++;
                ?>
                <a href="library.php?category=<?php echo urlencode($genre['name']); ?>"
                    class="badge <?php echo $badge_class; ?>"
                    style="padding: 0.75rem 1.5rem; font-size: 0.875rem; text-decoration: none; white-space: nowrap;">
                    <i class="fas fa-bookmark"></i>
                    <?php echo htmlspecialchars($genre['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="items-grid" id="booksGrid">
        <?php foreach ($books as $book): ?>
            <?php if ($book['source'] === 'api'): ?>
                <!-- API Item (No link) -->
                <div class="item-card-modern">
                    <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                        alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="item-card-modern-body">
                        <h6><?php echo htmlspecialchars($book['title']); ?></h6>
                        <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo number_format($book['rating_score'], 1); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Database Item (With link) -->
                <a href="item-detail.php?id=<?php echo $book['id']; ?>" class="item-card-modern">
                    <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                        alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="item-card-modern-body">
                        <h6><?php echo htmlspecialchars($book['title']); ?></h6>
                        <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo number_format($book['rating_score'], 1); ?></span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Loading Indicator -->
    <div class="loading-indicator" id="loadingIndicator">
        <i class="fas fa-spinner"></i>
        <p>Daha fazla kitap yükleniyor...</p>
    </div>

</div>

<script>
    // Infinite Scroll Implementation
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    const category = '<?php echo addslashes($selected_category); ?>';

    window.addEventListener('scroll', function () {
        if (isLoading || !hasMore) return;

        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;

        // Load more when 200px from bottom
        if (scrollPosition >= pageHeight - 200) {
            loadMoreBooks();
        }
    });

    function loadMoreBooks() {
        isLoading = true;
        currentPage++;

        const loadingIndicator = document.getElementById('loadingIndicator');
        loadingIndicator.classList.add('active');

        let url = `/lonely_eye/api/get_books.php?page=${currentPage}&limit=40`;
        if (category) {
            url += `&category=${encodeURIComponent(category)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.books && data.books.length > 0) {
                    const booksGrid = document.getElementById('booksGrid');

                    data.books.forEach(book => {
                        const card = createBookCard(book);
                        booksGrid.appendChild(card);
                    });

                    if (data.books.length < 40) {
                        hasMore = false;
                    }
                } else {
                    hasMore = false;
                }
            })
            .catch(error => {
                console.error('Error loading books:', error);
                hasMore = false;
            })
            .finally(() => {
                isLoading = false;
                loadingIndicator.classList.remove('active');
            });
    }

    function createBookCard(book) {
        const card = document.createElement(book.source === 'database' ? 'a' : 'div');
        card.className = 'item-card-modern';

        if (book.source === 'database') {
            card.href = `item-detail.php?id=${book.id}`;
        }

        card.innerHTML = `
        <img src="${escapeHtml(book.image)}" alt="${escapeHtml(book.title)}">
        <div class="item-card-modern-body">
            <h6>${escapeHtml(book.title)}</h6>
            <p class="author">${escapeHtml(book.author)}</p>
            <div class="rating">
                <i class="fas fa-star"></i>
                <span>${book.rating.toFixed(1)}</span>
            </div>
        </div>
    `;

        return card;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php require_once 'includes/footer.php'; ?>