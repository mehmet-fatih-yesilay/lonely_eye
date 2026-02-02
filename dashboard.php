<?php
/**
 * Dashboard - Main User Interface
 * Premium Design with Smart Content Algorithm
 */

require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ============================================
// FETCH FEATURED ITEM (Highest Rated)
// ============================================
$stmt = $pdo->query("
    SELECT i.*, g.name as genre_name, g.color_code 
    FROM items i 
    LEFT JOIN genres g ON i.genre_id = g.id 
    ORDER BY i.rating_score DESC, i.view_count DESC 
    LIMIT 1
");
$featured_item = $stmt->fetch();

// ============================================
// FETCH ALL GENRES
// ============================================
$stmt = $pdo->query("SELECT * FROM genres ORDER BY name");
$genres = $stmt->fetchAll();

// ============================================
// SMART CONTENT ALGORITHM
// Ensures minimum 12 items displayed
// ============================================
$min_items = 12;
$items = [];

// STEP A: Fetch from database
$stmt = $pdo->query("
    SELECT i.*, g.name as genre_name, g.color_code 
    FROM items i 
    LEFT JOIN genres g ON i.genre_id = g.id 
    ORDER BY i.created_at DESC 
    LIMIT $min_items
");
$db_items = $stmt->fetchAll();
$items = $db_items;

// STEP B & C: Fill with Google Books API if needed
$items_needed = $min_items - count($items);

if ($items_needed > 0) {
    // Fetch from Google Books API (max 40 items)
    $api_max = min($items_needed, 40); // API limit is 40
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=bestseller&orderBy=relevance&maxResults=$api_max&langRestrict=tr";

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

                // Create item array compatible with our structure
                $items[] = [
                    'id' => 0, // Temporary ID for API items
                    'title' => $volumeInfo['title'] ?? 'Untitled',
                    'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
                    'cover_image' => str_replace('http://', 'https://', $volumeInfo['imageLinks']['thumbnail'] ?? 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=450&fit=crop'),
                    'rating_score' => rand(35, 50) / 10,
                    'genre_name' => 'Genel',
                    'is_api' => true // Flag to identify API items
                ];

                if (count($items) >= $min_items)
                    break;
            }
        }
    }
}

$page_title = "Ana Sayfa";
require_once 'includes/header.php';
?>

<style>
    /* Main Layout */
    .main-content {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    /* Hero Section */
    .hero-section {
        position: relative;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        filter: blur(20px);
        opacity: 0.3;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        padding: 3rem;
        background: var(--bg-glass);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .hero-cover {
        width: 100%;
        height: 450px;
        object-fit: cover;
        border-radius: var(--radius-lg);
        border: 3px solid var(--primary);
        box-shadow: 0 10px 40px rgba(56, 189, 248, 0.4);
    }

    .hero-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: rgba(56, 189, 248, 0.2);
        border: 1px solid var(--primary);
        border-radius: var(--radius-lg);
        color: var(--primary);
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        width: fit-content;
    }

    .hero-info h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: var(--text-main);
    }

    .hero-author {
        font-size: 1.25rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .hero-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 1.125rem;
    }

    .hero-rating i {
        color: #FFD700;
    }

    .hero-description {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 2rem;
        max-height: 100px;
        overflow: hidden;
    }

    .hero-meta {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .hero-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
    }

    .hero-meta-item i {
        color: var(--primary);
    }

    /* Categories Section */
    .categories-section {
        margin-bottom: 2rem;
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

    .category-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--bg-card);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-xl);
        color: var(--text-main);
        font-weight: 600;
        white-space: nowrap;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .category-pill:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Items Grid */
    .items-section h2 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: var(--text-main);
    }

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

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .hero-content {
            grid-template-columns: 1fr;
            padding: 2rem 1rem;
        }

        .hero-cover {
            height: 300px;
        }

        .hero-info h1 {
            font-size: 1.75rem;
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

    <!-- Hero Section (Featured Item) -->
    <?php if ($featured_item): ?>
        <div class="hero-section fade-in">
            <div class="hero-background"
                style="background-image: url('<?php echo htmlspecialchars($featured_item['cover_image']); ?>');"></div>
            <div class="hero-content">
                <img src="<?php echo htmlspecialchars($featured_item['cover_image']); ?>"
                    alt="<?php echo htmlspecialchars($featured_item['title']); ?>" class="hero-cover">
                <div class="hero-info">
                    <span class="hero-badge">
                        <i class="fas fa-crown"></i> ÖNE ÇIKAN
                    </span>
                    <h1><?php echo htmlspecialchars($featured_item['title']); ?></h1>
                    <p class="hero-author">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($featured_item['author']); ?>
                    </p>
                    <div class="hero-rating">
                        <i class="fas fa-star"></i>
                        <span><?php echo number_format($featured_item['rating_score'], 1); ?></span>
                        <span class="text-muted">(<?php echo $featured_item['view_count']; ?> görüntülenme)</span>
                    </div>
                    <p class="hero-description">
                        <?php echo htmlspecialchars(substr($featured_item['description'], 0, 200)) . '...'; ?>
                    </p>
                    <div class="hero-meta">
                        <div class="hero-meta-item">
                            <i class="fas fa-bookmark"></i>
                            <span><?php echo htmlspecialchars($featured_item['genre_name']); ?></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo $featured_item['publication_year']; ?></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="fas fa-file-alt"></i>
                            <span><?php echo $featured_item['page_count']; ?> sayfa</span>
                        </div>
                    </div>
                    <a href="item-detail.php?id=<?php echo $featured_item['id']; ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-book-open"></i> İncele ve Tartış
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="search-section">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Ne okumak istersin? Kitap, yazar veya kategori ara...">
        </div>
    </div>

    <!-- Categories -->
    <div class="categories-section">
        <h2><i class="fas fa-th-large"></i> Kategoriler</h2>
        <div class="categories-scroll">
            <?php
            $badge_classes = ['badge-primary', 'badge-secondary', 'badge-success', 'badge-warning', 'badge-info'];
            $badge_index = 0;
            foreach ($genres as $genre):
                $badge_class = $badge_classes[$badge_index % count($badge_classes)];
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

    <!-- Recent Items -->
    <div class="items-section">
        <h2><i class="fas fa-clock"></i> Son Eklenenler</h2>
        <div class="items-grid" id="itemsGrid">
            <?php foreach ($items as $item): ?>
                <?php if (isset($item['is_api']) && $item['is_api']): ?>
                    <!-- API Item (No link, just display) -->
                    <div class="item-card-modern">
                        <img src="<?php echo htmlspecialchars($item['cover_image']); ?>"
                            alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="item-card-modern-body">
                            <h6><?php echo htmlspecialchars($item['title']); ?></h6>
                            <p class="author"><?php echo htmlspecialchars($item['author']); ?></p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($item['rating_score'], 1); ?></span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Database Item (With link) -->
                    <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="item-card-modern">
                        <img src="<?php echo htmlspecialchars($item['cover_image']); ?>"
                            alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="item-card-modern-body">
                            <h6><?php echo htmlspecialchars($item['title']); ?></h6>
                            <p class="author"><?php echo htmlspecialchars($item['author']); ?></p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($item['rating_score'], 1); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Scroll Sentinel for Infinite Scroll -->
        <div id="scroll-sentinel" style="height: 50px; width: 100%; text-align: center; padding: 20px;">
            <span id="loading-text" class="text-muted">Daha fazla kitap aranıyor...</span>
            <div class="spinner-border text-primary spinner-border-sm" role="status" style="display:none;"></div>
        </div>
    </div>

</div>

<script>
    // ============================================
    // INFINITE SCROLL IMPLEMENTATION
    // ============================================
    let currentPage = 1;
    let isLoading = false;
    let observer;
    const limit = 42;

    document.addEventListener('DOMContentLoaded', function () {
        // Setup IntersectionObserver
        setupObserver();

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(this.delay);
                this.delay = setTimeout(function () {
                    filterBooks();
                }, 500);
            });
        }
    });

    function setupObserver() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading) {
                    console.log("✅ Bekçi görüldü! Sayfa:", currentPage + 1);
                    currentPage++;
                    loadMoreBooks();
                }
            });
        }, options);

        const sentinel = document.getElementById('scroll-sentinel');
        if (sentinel) {
            observer.observe(sentinel);
            console.log("✅ Dashboard IntersectionObserver kuruldu");
        }
    }

    function loadMoreBooks(retryCount = 0) {
        if (isLoading) return;

        isLoading = true;
        const sentinel = document.getElementById('scroll-sentinel');
        const spinner = sentinel ? sentinel.querySelector('.spinner-border') : null;
        const text = sentinel ? sentinel.querySelector('#loading-text') : null;

        if (spinner) spinner.style.display = 'inline-block';
        if (text) text.textContent = retryCount > 0 ? `Yeniden deneniyor (${retryCount}/3)...` : "Yeni kitaplar yükleniyor...";

        const url = `api/get_books.php?page=${currentPage}&limit=${limit}&lang=all`;

        console.log(`📡 Dashboard API İsteği (Sayfa ${currentPage}):`, url);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log(`📦 Gelen veri (Sayfa ${currentPage}):`, data.length, "kitap");

                if (data.length > 0) {
                    renderBooks(data);
                    if (text) text.textContent = "Daha fazlası için kaydırın...";
                    retryCount = 0;
                } else {
                    if (text) text.textContent = "✓ Tüm kitaplar yüklendi";
                    if (observer && sentinel) {
                        observer.unobserve(sentinel);
                        console.log("✅ Dashboard: Tüm sonuçlar yüklendi");
                    }
                }
                isLoading = false;
                if (spinner) spinner.style.display = 'none';
            })
            .catch(err => {
                console.error('❌ Dashboard Hata:', err);

                if (retryCount < 3) {
                    console.log(`🔄 Yeniden deneniyor... (${retryCount + 1}/3)`);
                    isLoading = false;
                    setTimeout(() => {
                        loadMoreBooks(retryCount + 1);
                    }, 1000 * (retryCount + 1));
                } else {
                    isLoading = false;
                    if (text) {
                        text.innerHTML = `❌ Bağlantı hatası. <a href="#" onclick="location.reload()" style="color:var(--primary)">Yenileyin</a>`;
                    }
                    if (spinner) spinner.style.display = 'none';
                }
            });
    }

    function renderBooks(books) {
        const container = document.getElementById('itemsGrid');

        books.forEach(book => {
            const isGoogle = book.source === 'google';
            const detailLink = isGoogle
                ? `item-detail.php?google_id=${book.id}`
                : `item-detail.php?id=${book.id}`;

            const card = document.createElement(isGoogle ? 'div' : 'a');
            card.className = 'item-card-modern';

            if (!isGoogle) {
                card.href = detailLink;
            } else {
                card.style.cursor = 'pointer';
                card.onclick = function () {
                    window.location.href = detailLink;
                };
            }

            card.innerHTML = `
                <img src="${book.image}" 
                     alt="${escapeHtml(book.title)}"
                     onerror="this.src='assets/img/default_book.png'">
                <div class="item-card-modern-body">
                    <h6>${escapeHtml(book.title)}</h6>
                    <p class="author">${escapeHtml(book.author)}</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <span>${book.rating > 0 ? book.rating.toFixed(1) : '0.0'}</span>
                    </div>
                </div>
                ${isGoogle ? '<span class="badge bg-warning position-absolute" style="top:8px;right:8px;font-size:0.6rem;z-index:10;">Google</span>' : ''}
            `;

            container.appendChild(card);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function filterBooks() {
        const searchInput = document.getElementById('searchInput');
        const itemsGrid = document.getElementById('itemsGrid');
        const items = itemsGrid.querySelectorAll('.item-card-modern');
        const searchTerm = searchInput.value.toLowerCase().trim();

        items.forEach(item => {
            const title = item.querySelector('h6')?.textContent.toLowerCase() || '';
            const author = item.querySelector('.author')?.textContent.toLowerCase() || '';

            if (title.includes(searchTerm) || author.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>