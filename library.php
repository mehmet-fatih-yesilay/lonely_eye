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

// Note: Initial books are now loaded via JavaScript API call
// This ensures consistency with infinite scroll system

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

    <!-- Search Bar -->
    <div class="search-section mb-4">
        <div class="input-group input-group-lg">
            <span class="input-group-text" style="background: var(--bg-card); border-color: var(--border-color);">
                <i class="fas fa-search" style="color: var(--primary);"></i>
            </span>
            <input type="text" id="searchInput" class="form-control form-control-lg"
                placeholder="Kitap veya yazar ara..."
                style="background: var(--bg-card); border-color: var(--border-color); color: var(--text-main);">
        </div>
    </div>

    <!-- Language Filter -->
    <div class="categories-section">
        <h2><i class="fas fa-language"></i> Dil / Menşei</h2>
        <div class="categories-scroll">
            <button class="badge badge-primary lang-filter active" data-lang="">
                <i class="fas fa-globe"></i> Tümü
            </button>
            <button class="badge badge-secondary lang-filter" data-lang="tr">
                <i class="fas fa-flag"></i> Yerli (Türkçe)
            </button>
            <button class="badge badge-secondary lang-filter" data-lang="en">
                <i class="fas fa-flag"></i> Yabancı (İngilizce)
            </button>
        </div>
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

    <!-- Items Grid - Populated by JavaScript -->
    <div class="items-grid" id="book-grid">
        <!-- Books will be loaded here via JavaScript infinite scroll -->
    </div>

    <!-- Scroll Sentinel - IntersectionObserver Bekçisi -->
    <div id="scroll-sentinel" style="height: 50px; width: 100%; text-align: center; padding: 20px;">
        <span id="loading-text" class="text-muted">Kitaplar yükleniyor...</span>
        <div class="spinner-border text-primary spinner-border-sm" role="status" style="display:inline-block;"></div>
    </div>

</div>

<script>
    let currentPage = 1;
    let isLoading = false;
    let currentLang = 'all';
    let currentCategory = '<?php echo isset($_GET["category"]) ? htmlspecialchars($_GET["category"]) : ""; ?>';
    const limit = 42;
    let observer;

    document.addEventListener('DOMContentLoaded', function () {
        console.log("📚 Library page loaded");

        // 1. Setup IntersectionObserver
        setupObserver();

        // 2. Load initial books (page 1)
        loadBooks();

        // 3. Arama inputu (debounce ile)
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(this.delay);
                this.delay = setTimeout(function () {
                    resetAndLoad();
                }, 800);
            });
        }

        // 4. Dil Filtreleri
        const langButtons = document.querySelectorAll('.lang-filter'); // Changed from .lang-filter-btn to .lang-filter to match existing HTML
        langButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                // Tüm butonlardan active kaldır
                langButtons.forEach(btn => {
                    btn.classList.remove('active', 'badge-primary'); // Keep badge-primary/secondary logic
                    btn.classList.add('badge-secondary');
                });

                // Tıklanan butona active ekle
                this.classList.add('active', 'badge-primary'); // Keep badge-primary/secondary logic
                this.classList.remove('badge-secondary');

                // Dil değerini güncelle
                currentLang = this.getAttribute('data-lang') || 'all';
                resetAndLoad();
            });
        });

        // 5. Kategori Butonları (Eğer varsa)
        const categoryButtons = document.querySelectorAll('.category-btn');
        categoryButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                // Kategori değerini al
                const category = this.getAttribute('data-category') || '';
                currentCategory = category;

                // Tüm butonlardan active kaldır
                categoryButtons.forEach(btn => {
                    btn.classList.remove('badge-primary');
                    btn.classList.add('badge-secondary');
                });

                // Tıklanan butona active ekle
                this.classList.add('badge-primary');
                this.classList.remove('badge-secondary');

                resetAndLoad();
            });
        });
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
                    loadBooks();
                }
            });
        }, options);

        const sentinel = document.getElementById('scroll-sentinel');
        if (sentinel) {
            observer.observe(sentinel);
            console.log("✅ IntersectionObserver kuruldu");
        } else {
            console.error("❌ Bekçi elementi bulunamadı!");
        }
    }

    function resetAndLoad() {
        currentPage = 1;
        document.getElementById('book-grid').innerHTML = '';
        loadBooks();
    }

    function loadBooks(retryCount = 0) {
        if (isLoading) {
            console.log("⏳ Zaten yükleniyor, bekle...");
            return;
        }

        isLoading = true;

        const sentinel = document.getElementById('scroll-sentinel');
        const spinner = sentinel ? sentinel.querySelector('.spinner-border') : null;
        const text = sentinel ? sentinel.querySelector('#loading-text') : null;

        if (spinner) spinner.style.display = 'inline-block';
        if (text) text.textContent = retryCount > 0 ? `Yeniden deneniyor (${retryCount}/3)...` : "Kütüphane taranıyor...";

        // Get search term
        let searchTerm = '';
        const searchEl = document.getElementById('searchInput');
        if (searchEl) searchTerm = searchEl.value;

        // Build API URL
        const url = `api/get_books.php?page=${currentPage}&limit=${limit}&lang=${currentLang}&category=${encodeURIComponent(currentCategory)}&search=${encodeURIComponent(searchTerm)}`;

        console.log(`📡 API İsteği (Sayfa ${currentPage}):`, url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`📦 Gelen veri (Sayfa ${currentPage}):`, data.length, "kitap");

                if (data.length > 0) {
                    renderBooks(data);
                    if (text) text.textContent = "Daha fazlası için kaydırın...";

                    // Reset retry count on success
                    retryCount = 0;
                } else {
                    // No more results
                    if (currentPage === 1) {
                        const container = document.getElementById('book-grid');
                        container.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding:3rem;"><h4 class="text-muted">Kitap bulunamadı</h4><p class="text-muted">Farklı bir arama terimi veya kategori deneyin.</p></div>';
                    } else {
                        if (text) text.textContent = "✓ Tüm kitaplar yüklendi";
                        // Disconnect observer when no more results
                        if (observer && sentinel) {
                            observer.unobserve(sentinel);
                            console.log("✅ Tüm sonuçlar yüklendi, observer durduruldu");
                        }
                    }
                }

                isLoading = false;
                if (spinner) spinner.style.display = 'none';
            })
            .catch(err => {
                console.error('❌ Hata:', err);

                // Retry logic (max 3 attempts)
                if (retryCount < 3) {
                    console.log(`🔄 Yeniden deneniyor... (${retryCount + 1}/3)`);
                    isLoading = false;
                    setTimeout(() => {
                        loadBooks(retryCount + 1);
                    }, 1000 * (retryCount + 1)); // Exponential backoff
                } else {
                    // Max retries reached
                    isLoading = false;
                    if (text) {
                        text.innerHTML = `❌ Bağlantı hatası. <a href="#" onclick="location.reload()" style="color:var(--primary)">Sayfayı yenileyin</a>`;
                    }
                    if (spinner) spinner.style.display = 'none';
                }
            });
    }

    function renderBooks(books) {
        const container = document.getElementById('book-grid');

        books.forEach(book => {
            const isGoogle = book.source === 'google';
            const detailLink = isGoogle
                ? `item-detail.php?google_id=${book.id}`
                : `item-detail.php?id=${book.id}`;

            // Create element (a for local, div for Google - but make both clickable)
            const card = document.createElement(isGoogle ? 'div' : 'a');
            card.className = 'item-card-modern';

            if (!isGoogle) {
                card.href = detailLink;
            } else {
                // Make Google books clickable
                card.style.cursor = 'pointer';
                card.onclick = function () {
                    window.location.href = detailLink;
                };
            }

            // Build inner HTML
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

    function setLang(lang) {
        currentLang = lang;
        resetAndLoad();
    }
</script>

<?php require_once 'includes/footer.php'; ?>