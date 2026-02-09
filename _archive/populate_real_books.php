<?php
/**
 * 3000 GERÇEK KİTAP VERİTABANI POPÜLASYON SCRİPTİ
 * 
 * Bu script:
 * - Mevcut tüm kitapları siler
 * - Gerçek kitap verisini ekler
 * - Google Books & Open Library API'den kapak resimlerini çeker
 * 
 * Dağılım:
 * - 2500 Türkçe kitap
 * - 500 Yabancı kitap
 * - 500 Tarih kitabı
 * - 500 İslami kitap
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '512M');

require_once 'includes/db.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "      3000 GERÇEK KİTAP VERİTABANI OLUŞTURMA\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Kitap verilerini yükle
echo "📚 Veri dosyaları yükleniyor...\n";

$turkLit = include 'book_data/turkish_literature.php';
$tarih = include 'book_data/history_books.php';
$islami = include 'book_data/islamic_books_new.php';
$yabanci = include 'book_data/world_classics_new.php';
$ekKategoriler = include 'book_data/additional_categories.php';

// Tüm kitapları birleştir
$allBooks = [];

// Türk Edebiyatı Roman
foreach ($turkLit['turkEdebiyatRoman'] as $book) {
    $allBooks[] = [
        'title' => $book[0],
        'author' => $book[1],
        'year' => $book[2],
        'pages' => $book[3],
        'isbn' => $book[4],
        'genre_id' => $book[5],
        'language' => 'tr'
    ];
}

// Türk Şiiri
foreach ($turkLit['turkSiir'] as $book) {
    $allBooks[] = [
        'title' => $book[0],
        'author' => $book[1],
        'year' => $book[2],
        'pages' => $book[3],
        'isbn' => $book[4],
        'genre_id' => $book[5],
        'language' => 'tr'
    ];
}

// Tarih Kitapları
foreach ($tarih as $book) {
    $allBooks[] = [
        'title' => $book[0],
        'author' => $book[1],
        'year' => $book[2],
        'pages' => $book[3],
        'isbn' => $book[4],
        'genre_id' => $book[5],
        'language' => 'tr'
    ];
}

// İslami Kitaplar
foreach ($islami as $book) {
    $allBooks[] = [
        'title' => $book[0],
        'author' => $book[1],
        'year' => $book[2],
        'pages' => $book[3],
        'isbn' => $book[4],
        'genre_id' => $book[5],
        'language' => 'tr'
    ];
}

// Yabancı Klasikler
foreach ($yabanci as $book) {
    $allBooks[] = [
        'title' => $book[0],
        'author' => $book[1],
        'year' => $book[2],
        'pages' => $book[3],
        'isbn' => $book[4],
        'genre_id' => $book[5],
        'language' => isset($book[6]) ? $book[6] : 'en'
    ];
}

// Ek Kategoriler
foreach (['kisiselGelisim', 'psikoloji', 'felsefe', 'bilimKurguFantastik', 'biyografi', 'bilim'] as $cat) {
    if (isset($ekKategoriler[$cat])) {
        foreach ($ekKategoriler[$cat] as $book) {
            $allBooks[] = [
                'title' => $book[0],
                'author' => $book[1],
                'year' => $book[2],
                'pages' => $book[3],
                'isbn' => $book[4],
                'genre_id' => $book[5],
                'language' => 'tr'
            ];
        }
    }
}

$bookCount = count($allBooks);
echo "✅ Toplam {$bookCount} kitap yüklendi.\n\n";

// Kapak resmi getirme fonksiyonları
function fetchCoverFromGoogleBooks(string $title, string $author): ?string
{
    $query = urlencode(trim($title) . ' ' . trim($author));
    $url = "https://www.googleapis.com/books/v1/volumes?q={$query}&maxResults=1&langRestrict=tr";

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);
    if (!$response)
        return null;

    $data = json_decode($response, true);
    if (!$data || !isset($data['items'][0]['volumeInfo']['imageLinks']))
        return null;

    $imageLinks = $data['items'][0]['volumeInfo']['imageLinks'];
    $coverUrl = $imageLinks['thumbnail'] ?? $imageLinks['smallThumbnail'] ?? null;

    if ($coverUrl) {
        // Daha yüksek çözünürlük için zoom parametresini değiştir
        $coverUrl = str_replace('zoom=1', 'zoom=2', $coverUrl);
        $coverUrl = str_replace('http://', 'https://', $coverUrl);
    }

    return $coverUrl;
}

function fetchCoverFromOpenLibrary(string $isbn): ?string
{
    $url = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg?default=false";

    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        return $url;
    }
    return null;
}

function generatePlaceholder(string $title): string
{
    // Başlıktan kısaltma oluştur
    $words = explode(' ', trim($title));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= mb_substr($word, 0, 1, 'UTF-8');
            if (mb_strlen($initials, 'UTF-8') >= 2)
                break;
        }
    }
    if (mb_strlen($initials, 'UTF-8') < 2) {
        $initials = mb_substr($title, 0, 2, 'UTF-8');
    }
    $initials = strtoupper($initials);

    // Renk paletinden rastgele seç
    $colors = ['1e3a5f', '2d4a3e', '5c3d2e', '3d314a', '4a3c2a', '2e4a5c', '3a2d4a', '4a2d3a'];
    $bgColor = $colors[array_rand($colors)];

    return "https://placehold.co/300x450/{$bgColor}/FFF?text=" . urlencode($initials);
}

function getBestCover(string $title, string $author, string $isbn): string
{
    // 1. ISBN ile Open Library dene
    if (!empty($isbn)) {
        $cover = fetchCoverFromOpenLibrary($isbn);
        if ($cover)
            return $cover;
    }

    // 2. Google Books API dene
    $cover = fetchCoverFromGoogleBooks($title, $author);
    if ($cover)
        return $cover;

    // 3. Placeholder oluştur
    return generatePlaceholder($title);
}

// Veritabanı işlemleri
echo "🗑️ Mevcut veriler siliniyor...\n";
flush();

try {
    // Foreign key kontrollerini geçici olarak kapat
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // İlişkili tabloları temizle - TRUNCATE yerine DELETE kullan (daha güvenli)
    $pdo->exec("DELETE FROM comment_replies");
    $pdo->exec("DELETE FROM favorites");
    $pdo->exec("DELETE FROM reviews");

    // Items tablosunu temizle
    $pdo->exec("DELETE FROM items");

    // Foreign key kontrollerini aç
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Auto increment değerlerini sıfırla
    $pdo->exec("ALTER TABLE items AUTO_INCREMENT = 1");

    echo "✅ Mevcut veriler silindi.\n\n";
    flush();
} catch (Exception $e) {
    echo "❌ Veritabanı temizleme hatası: " . $e->getMessage() . "\n";
    exit(1);
}

// Kitapları ekle
echo "📖 Kitaplar ekleniyor...\n";

$insertStmt = $pdo->prepare("
    INSERT INTO items (type, title, author, description, cover_image, genre_id, language, publication_year, page_count, view_count, rating_score)
    VALUES ('book', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$total = count($allBooks);
$inserted = 0;
$covers_found = 0;
$placeholders = 0;
$startTime = microtime(true);
$batchSize = 20;

// Kitapları karıştır (daha iyi dağılım için)
shuffle($allBooks);

foreach ($allBooks as $index => $book) {
    // Her 20 kitapta bir API çağrısı yap (rate limiting)
    if ($index % $batchSize === 0) {
        usleep(100000); // 100ms bekle
    }

    // Kapak resmini al
    $coverImage = getBestCover($book['title'], $book['author'], $book['isbn']);

    if (strpos($coverImage, 'placehold.co') !== false) {
        $placeholders++;
    } else {
        $covers_found++;
    }

    // Açıklama oluştur
    $description = "{$book['author']} tarafından yazılmış, {$book['year']} yılında yayınlanan bu eser, Türk ve dünya edebiyatının önemli yapıtlarından biridir.";

    // Rastgele view count ve rating
    $viewCount = rand(100, 10000);
    $ratingScore = round(3.5 + (mt_rand(0, 15) / 10), 2);

    try {
        $insertStmt->execute([
            $book['title'],
            $book['author'],
            $description,
            $coverImage,
            $book['genre_id'],
            $book['language'],
            $book['year'],
            $book['pages'],
            $viewCount,
            $ratingScore
        ]);
        $inserted++;
    } catch (Exception $e) {
        echo "❌ Hata: {$book['title']} - {$e->getMessage()}\n";
    }

    // İlerleme göster
    if (($index + 1) % 50 === 0) {
        $elapsed = microtime(true) - $startTime;
        $rate = ($index + 1) / $elapsed;
        $remaining = ($total - $index - 1) / $rate;
        printf(
            "⏱️ İlerleme: %d/%d (%.1f%%) - Kapak: %d, Placeholder: %d - Kalan: %.0f sn\n",
            $index + 1,
            $total,
            (($index + 1) / $total) * 100,
            $covers_found,
            $placeholders,
            $remaining
        );
    }
}

$totalTime = microtime(true) - $startTime;

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "   TAMAMLANDI!\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📚 Toplam eklenen kitap: {$inserted}\n";
echo "🖼️ Gerçek kapak bulunan: {$covers_found}\n";
echo "📝 Placeholder kullanılan: {$placeholders}\n";
echo "⏱️ Toplam süre: " . round($totalTime, 1) . " saniye\n";

// Kategori dağılımını göster
echo "\n📊 Kategori Dağılımı:\n";
$stmt = $pdo->query("
    SELECT g.name, COUNT(*) as count 
    FROM items i 
    JOIN genres g ON i.genre_id = g.id 
    GROUP BY g.id, g.name 
    ORDER BY count DESC
");
while ($row = $stmt->fetch()) {
    printf("   %-20s: %d\n", $row['name'], $row['count']);
}

// Dil dağılımını göster
echo "\n🌍 Dil Dağılımı:\n";
$stmt = $pdo->query("
    SELECT language, COUNT(*) as count 
    FROM items 
    GROUP BY language 
    ORDER BY count DESC
");
while ($row = $stmt->fetch()) {
    $langName = match ($row['language']) {
        'tr' => 'Türkçe',
        'en' => 'İngilizce',
        'fr' => 'Fransızca',
        'de' => 'Almanca',
        'ru' => 'Rusça',
        'es' => 'İspanyolca',
        'it' => 'İtalyanca',
        'pt' => 'Portekizce',
        'ja' => 'Japonca',
        default => $row['language']
    };
    printf("   %-15s: %d\n", $langName, $row['count']);
}

echo "\n✅ Veritabanı başarıyla güncellendi!\n";
