<?php
/**
 * DATABASE CLEANUP & CONTENT UPDATE
 * 
 * 1. Remove series books (Horror Tale, Fantasy Epic, etc.)
 * 2. Add world classics
 * 3. Add 2000 magazines
 * 4. Fix broken cover images
 */

set_time_limit(0);
ini_set('memory_limit', '512M');

require_once 'includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "═══════════════════════════════════════════════════════════════\n";
echo "   LONELY EYE - VERİTABANI TEMİZLİĞİ VE İÇERİK GÜNCELLEMESİ\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$stats = [
    'series_deleted' => 0,
    'classics_added' => 0,
    'magazines_added' => 0,
    'covers_fixed' => 0
];

// ============================================
// STEP 1: ANALYZE SERIES BOOKS
// ============================================
echo "📊 ADIM 1: SERİ KİTAPLARI ANALİZ ET\n";
echo "───────────────────────────────────────────────────────────────\n";

// Series patterns to detect
$seriesPatterns = [
    'Horror Tale %',
    'Fantasy Epic %',
    'Adventure Story %',
    'Mystery Case %',
    'Love Story %',
    'Sci-Fi Tale %',
    'Poetry Collection %',
    'Philosophy Work %',
    'History Book %',
    'Biography of %',
    'Art & Culture %',
    'Science Discovery %',
    'Technology Guide %'
];

// Count series books
$totalSeries = 0;
foreach ($seriesPatterns as $pattern) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM items WHERE title LIKE ?");
    $stmt->execute([$pattern]);
    $count = $stmt->fetch()['count'];
    if ($count > 0) {
        echo "  • {$pattern}: {$count} kitap\n";
        $totalSeries += $count;
    }
}

// Also detect numbered books with regex pattern
$stmt = $pdo->query("SELECT COUNT(*) as count FROM items WHERE title REGEXP '[A-Za-z]+ [A-Za-z]+ [0-9]+$' AND type = 'book'");
$numberedBooks = $stmt->fetch()['count'];
echo "\n  📌 Numaralı format (Örn: 'Horror Tale 28'): {$numberedBooks} kitap\n";

echo "\n  🎯 Toplam silinecek tahmini: {$totalSeries} seri kitap\n\n";

// ============================================
// STEP 2: DELETE SERIES BOOKS
// ============================================
echo "🗑️ ADIM 2: SERİ KİTAPLARI SİL\n";
echo "───────────────────────────────────────────────────────────────\n";

// Delete series books
$deletePatterns = [
    "title LIKE 'Horror Tale %'",
    "title LIKE 'Fantasy Epic %'",
    "title LIKE 'Adventure Story %'",
    "title LIKE 'Mystery Case %'",
    "title LIKE 'Love Story %'",
    "title LIKE 'Sci-Fi Tale %'",
    "title LIKE 'Poetry Collection %'",
    "title LIKE 'Philosophy Work %'",
    "title LIKE 'History Book %'",
    "title LIKE 'Art & Culture %'",
    "title LIKE 'Science Discovery %'",
    "title LIKE 'Technology Guide %'",
    "title REGEXP '^[A-Za-z]+ (Tale|Epic|Story|Case|Collection|Work|Book|Guide|Discovery) [0-9]+$'"
];

foreach ($deletePatterns as $pattern) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE {$pattern} AND type = 'book'");
    $stmt->execute();
    $deleted = $stmt->rowCount();
    if ($deleted > 0) {
        echo "  ✓ {$pattern}: {$deleted} silindi\n";
        $stats['series_deleted'] += $deleted;
    }
}

echo "\n  🎯 Toplam silinen: {$stats['series_deleted']} seri kitap\n\n";

// ============================================
// STEP 3: ADD WORLD CLASSICS
// ============================================
echo "📚 ADIM 3: DÜNYA KLASİKLERİ EKLE\n";
echo "───────────────────────────────────────────────────────────────\n";

$worldClassics = [
    // Russian Literature
    ['Suç ve Ceza', 'Fyodor Dostoyevski', 'Raskolnikov\'un suç ve vicdan arasındaki mücadelesi', 1, 1866, 671],
    ['Karamazov Kardeşler', 'Fyodor Dostoyevski', 'Üç kardeşin hikayesi üzerinden insan doğasının analizi', 1, 1880, 824],
    ['Savaş ve Barış', 'Lev Tolstoy', 'Napolyon savaşları döneminde Rus ailelerinin destansı hikayesi', 1, 1869, 1225],
    ['Anna Karenina', 'Lev Tolstoy', 'Yasak aşk ve toplumsal baskı üzerine bir başyapıt', 1, 1877, 864],
    ['Ölü Canlar', 'Nikolay Gogol', 'Rusya\'nın sosyal yapısını hicveden satirik roman', 1, 1842, 432],

    // French Literature
    ['Sefiller', 'Victor Hugo', 'Jean Valjean\'ın kurtuluş arayışı', 1, 1862, 1463],
    ['Notre Dame\'ın Kamburu', 'Victor Hugo', 'Quasimodo\'nun trajik aşk hikayesi', 1, 1831, 544],
    ['Üç Silahşörler', 'Alexandre Dumas', 'D\'Artagnan ve arkadaşlarının maceraları', 12, 1844, 625],
    ['Monte Cristo Kontu', 'Alexandre Dumas', 'İntikam ve adalet üzerine epik bir hikaye', 12, 1844, 1312],
    ['Kırmızı ve Siyah', 'Stendhal', 'Julien Sorel\'in toplumsal yükseliş mücadelesi', 1, 1830, 576],
    ['Madame Bovary', 'Gustave Flaubert', 'Emma Bovary\'nin romantik hayalleri ve trajedisi', 1, 1857, 328],
    ['Germinal', 'Émile Zola', 'Maden işçilerinin hayatı ve mücadelesi', 1, 1885, 464],
    ['Yabancı', 'Albert Camus', 'Meursault\'un varoluşsal yolculuğu', 7, 1942, 159],
    ['Veba', 'Albert Camus', 'Oran şehrinde salgın hastalık ve insanlık durumu', 1, 1947, 308],

    // German Literature
    ['Dönüşüm', 'Franz Kafka', 'Gregor Samsa\'nın böceğe dönüşümü', 1, 1915, 55],
    ['Dava', 'Franz Kafka', 'Josef K.\'nın bilinmeyen bir suçla yargılanması', 1, 1925, 255],
    ['Şato', 'Franz Kafka', 'K.\'nın gizemli şatoya ulaşma çabası', 1, 1926, 352],
    ['Faust', 'Johann Wolfgang von Goethe', 'Faust\'un şeytanla anlaşması', 7, 1808, 464],
    ['Genç Werther\'in Acıları', 'Johann Wolfgang von Goethe', 'Umutsuz aşk ve gençlik', 1, 1774, 128],
    ['Bozkırkurdu', 'Hermann Hesse', 'Harry Haller\'ın içsel yolculuğu', 7, 1927, 237],
    ['Siddharta', 'Hermann Hesse', 'Aydınlanma arayışı', 7, 1922, 152],

    // English Literature
    ['Hamlet', 'William Shakespeare', 'Danimarka prensi Hamlet\'in intikam trajedisi', 11, 1600, 128],
    ['Romeo ve Juliet', 'William Shakespeare', 'İki düşman aileden gençlerin trajik aşkı', 11, 1597, 96],
    ['Macbeth', 'William Shakespeare', 'İhtiras ve suçun trajedisi', 11, 1606, 88],
    ['Kral Lear', 'William Shakespeare', 'Bir kralın düşüşü ve aile trajedisi', 11, 1606, 112],
    ['Othello', 'William Shakespeare', 'Kıskançlık ve ihanet trajedisi', 11, 1603, 104],
    ['Gurur ve Önyargı', 'Jane Austen', 'Elizabeth Bennet ve Mr. Darcy\'nin aşk hikayesi', 1, 1813, 432],
    ['Aşk ve Gurur', 'Jane Austen', 'Regency İngiltere\'sinde evlilik ve aşk', 1, 1811, 368],
    ['Jane Eyre', 'Charlotte Brontë', 'Bağımsız bir kadının aşk hikayesi', 1, 1847, 532],
    ['Uğultulu Tepeler', 'Emily Brontë', 'Heathcliff ve Catherine\'in tutkulu aşkı', 1, 1847, 416],
    ['1984', 'George Orwell', 'Totaliter bir gelecekte bireysel özgürlük', 4, 1949, 328],
    ['Hayvan Çiftliği', 'George Orwell', 'Politik alegori ve devrim eleştirisi', 4, 1945, 112],
    ['Cesur Yeni Dünya', 'Aldous Huxley', 'Distopik bir gelecek vizyonu', 4, 1932, 311],
    ['Frankenstein', 'Mary Shelley', 'Yaratıcı ve yaratık arasındaki trajedi', 4, 1818, 280],
    ['Dracula', 'Bram Stoker', 'Vampir efsanesinin klasiği', 8, 1897, 418],
    ['Dorian Gray\'in Portresi', 'Oscar Wilde', 'Güzellik, gençlik ve ahlak', 1, 1890, 254],

    // American Literature
    ['Moby Dick', 'Herman Melville', 'Kaptan Ahab\'ın beyaz balinayı avlama takıntısı', 12, 1851, 635],
    ['Büyük Gatsby', 'F. Scott Fitzgerald', 'Amerikan rüyasının çöküşü', 1, 1925, 180],
    ['Yaşlı Adam ve Deniz', 'Ernest Hemingway', 'Bir balıkçının mücadelesi', 1, 1952, 127],
    ['Silahlara Veda', 'Ernest Hemingway', 'Birinci Dünya Savaşı\'nda aşk ve kayıp', 1, 1929, 355],
    ['Çavdar Tarlasında Çocuklar', 'J.D. Salinger', 'Holden Caulfield\'ın isyanı', 1, 1951, 234],
    ['Bülbülü Öldürmek', 'Harper Lee', 'Irkçılık ve adalet arayışı', 1, 1960, 376],
    ['Gazap Üzümleri', 'John Steinbeck', 'Göç eden bir ailenin mücadelesi', 1, 1939, 464],
    ['Fareler ve İnsanlar', 'John Steinbeck', 'George ve Lennie\'nin dostluğu', 1, 1937, 107],

    // Spanish/Latin Literature
    ['Don Kişot', 'Miguel de Cervantes', 'Şövalyelik romanlarının parodisi', 1, 1605, 1056],
    ['Yüzyıllık Yalnızlık', 'Gabriel García Márquez', 'Buendía ailesinin destansı hikayesi', 1, 1967, 417],
    ['Kolera Günlerinde Aşk', 'Gabriel García Márquez', 'Elli yıl bekleyen bir aşk', 1, 1985, 348],
    ['Labirentler Evinin Bahçesi', 'Jorge Luis Borges', 'Felsefi kısa öyküler', 7, 1944, 160],

    // Turkish Literature
    ['İnce Memed', 'Yaşar Kemal', 'Anadolu\'da eşkıyalık ve direniş', 1, 1955, 420],
    ['Kürk Mantolu Madonna', 'Sabahattin Ali', 'Berlin\'de geçen bir aşk hikayesi', 1, 1943, 160],
    ['Tutunamayanlar', 'Oğuz Atay', 'Modern Türk edebiyatının başyapıtı', 11, 1971, 724],
    ['Tehlikeli Oyunlar', 'Oğuz Atay', 'Bilinç akışı tekniğiyle yazılmış roman', 11, 1973, 512],
    ['Benim Adım Kırmızı', 'Orhan Pamuk', 'Osmanlı minyatür sanatı üzerine', 1, 1998, 472],
    ['Kar', 'Orhan Pamuk', 'Kars\'ta geçen politik ve kişisel drama', 1, 2002, 428],
    ['Masumiyet Müzesi', 'Orhan Pamuk', '1970\'lerde İstanbul\'da bir aşk hikayesi', 1, 2008, 592],
    ['Saatleri Ayarlama Enstitüsü', 'Ahmet Hamdi Tanpınar', 'Doğu-Batı sentezi üzerine', 1, 1961, 416],
    ['Huzur', 'Ahmet Hamdi Tanpınar', 'İstanbul\'da aşk ve müzik', 1, 1949, 352],
    ['Çalıkuşu', 'Reşat Nuri Güntekin', 'Feride\'nin öğretmenlik maceraları', 1, 1922, 480],
    ['Yaprak Dökümü', 'Reşat Nuri Güntekin', 'Bir ailenin çöküşü', 1, 1930, 312],
    ['Sinekli Bakkal', 'Halide Edib Adıvar', 'Osmanlı\'dan Cumhuriyet\'e geçiş', 1, 1936, 384],
    ['Ateşten Gömlek', 'Halide Edib Adıvar', 'Kurtuluş Savaşı romanı', 3, 1922, 256],
    ['Yaban', 'Yakup Kadri Karaosmanoğlu', 'Köy gerçekliği ve aydın yalnızlığı', 1, 1932, 208],
    ['Kiralık Konak', 'Yakup Kadri Karaosmanoğlu', 'Kuşak çatışması', 1, 1922, 304],

    // Other World Classics
    ['Savaşın Sanatı', 'Sun Tzu', 'Askeri strateji klasiği', 3, -500, 128],
    ['Prens', 'Niccolò Machiavelli', 'Siyaset felsefesinin temeli', 7, 1532, 160],
    ['Devlet', 'Platon', 'İdeal toplum ve adalet üzerine', 7, -380, 416],
    ['Nikomakhos\'a Etik', 'Aristoteles', 'Erdem ve mutluluk felsefesi', 7, -350, 288],
    ['İtiraflar', 'Jean-Jacques Rousseau', 'Otobiyografinin başyapıtı', 9, 1782, 656],
    ['Toplum Sözleşmesi', 'Jean-Jacques Rousseau', 'Siyaset felsefesi', 7, 1762, 168],
    ['Böyle Buyurdu Zerdüşt', 'Friedrich Nietzsche', 'Üstinsan kavramı', 7, 1883, 336],
    ['İyinin ve Kötünün Ötesinde', 'Friedrich Nietzsche', 'Ahlak eleştirisi', 7, 1886, 224],
    ['Varlık ve Zaman', 'Martin Heidegger', 'Varoluşçu felsefe', 7, 1927, 589],
    ['Bulantı', 'Jean-Paul Sartre', 'Varoluşçu roman', 7, 1938, 253],
    ['İkinci Cins', 'Simone de Beauvoir', 'Feminist felsefenin temeli', 7, 1949, 800]
];

// Get genre IDs
$genreMap = [];
$stmt = $pdo->query("SELECT id, name FROM genres");
while ($row = $stmt->fetch()) {
    $genreMap[$row['name']] = $row['id'];
}

// Genre ID mapping for classics
$genreIds = [
    1 => $genreMap['Roman'] ?? 1,
    3 => $genreMap['Tarih'] ?? 3,
    4 => $genreMap['Bilim Kurgu'] ?? 4,
    7 => $genreMap['Felsefe'] ?? 7,
    8 => $genreMap['Polisiye'] ?? 8,
    9 => $genreMap['Biyografi'] ?? 9,
    11 => $genreMap['Edebiyat'] ?? 11,
    12 => $genreMap['Macera'] ?? 12
];

/**
 * Get cover from Open Library
 */
function getBookCover($title, $author)
{
    $query = urlencode($title);
    $url = "https://openlibrary.org/search.json?title={$query}&limit=1";

    $context = stream_context_create([
        'http' => ['timeout' => 8, 'user_agent' => 'LonelyEye/1.0']
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['docs'][0]['cover_i'])) {
            return "https://covers.openlibrary.org/b/id/{$data['docs'][0]['cover_i']}-M.jpg";
        }
    }

    // Fallback to UI avatars
    $initials = '';
    foreach (explode(' ', $title) as $word) {
        $initials .= mb_substr($word, 0, 1);
        if (strlen($initials) >= 2)
            break;
    }
    $colors = ['2563eb', '7c3aed', 'db2777', 'dc2626', 'ea580c', '16a34a'];
    $color = $colors[crc32($title) % count($colors)];
    return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background={$color}&color=fff&size=192&bold=true";
}

$insertStmt = $pdo->prepare("
    INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, rating_score, view_count)
    VALUES ('book', ?, ?, ?, ?, ?, ?, ?, ROUND(3.5 + RAND() * 1.5, 2), FLOOR(RAND() * 5000))
    ON DUPLICATE KEY UPDATE title = title
");

foreach ($worldClassics as $book) {
    [$title, $author, $description, $genreKey, $year, $pages] = $book;

    // Check if already exists
    $checkStmt = $pdo->prepare("SELECT id FROM items WHERE title = ? AND author = ?");
    $checkStmt->execute([$title, $author]);
    if ($checkStmt->fetch()) {
        continue; // Skip existing
    }

    $cover = getBookCover($title, $author);
    $genreId = $genreIds[$genreKey] ?? 1;

    try {
        $insertStmt->execute([$title, $author, $description, $cover, $genreId, $year, $pages]);
        $stats['classics_added']++;
        echo "  ✓ {$title} - {$author}\n";
    } catch (Exception $e) {
        echo "  ✗ {$title}: " . $e->getMessage() . "\n";
    }

    usleep(100000); // 100ms delay for API
}

echo "\n  🎯 Eklenen klasik: {$stats['classics_added']}\n\n";

// ============================================
// STEP 4: ADD MAGAZINES (2000)
// ============================================
echo "📰 ADIM 4: DERGİLER EKLE (2000 adet)\n";
echo "───────────────────────────────────────────────────────────────\n";

// Get dergi genre ID
$dergiGenreId = $genreMap['Dergi'] ?? 15;

// Magazine templates
$magazines = [
    // Turkish Magazines
    ['National Geographic Türkiye', 'Doğa, bilim ve keşif dergisi', 'nat_geo'],
    ['Bilim ve Teknik', 'TÜBİTAK popüler bilim dergisi', 'bilim_teknik'],
    ['Atlas', 'Gezi ve keşif dergisi', 'atlas'],
    ['NTV Tarih', 'Tarih ve kültür dergisi', 'ntv_tarih'],
    ['Skylife', 'THY uçak içi dergisi', 'skylife'],
    ['Capital', 'İş ve ekonomi dergisi', 'capital'],
    ['Para', 'Finans ve yatırım dergisi', 'para'],
    ['Esquire Türkiye', 'Erkek yaşam tarzı dergisi', 'esquire'],
    ['Elle Türkiye', 'Moda ve yaşam dergisi', 'elle'],
    ['Vogue Türkiye', 'Moda dergisi', 'vogue'],
    ['GQ Türkiye', 'Erkek moda ve yaşam', 'gq'],
    ['Marie Claire', 'Kadın yaşam dergisi', 'marie_claire'],
    ['Cosmopolitan Türkiye', 'Kadın dergisi', 'cosmo'],
    ['Men\'s Health Türkiye', 'Sağlık ve fitness', 'mens_health'],
    ['Runner\'s World Türkiye', 'Koşu ve fitness', 'runners'],

    // International Magazines
    ['Time', 'Dünya haberleri ve analiz', 'time'],
    ['The Economist', 'Ekonomi ve politika', 'economist'],
    ['Forbes', 'İş dünyası ve girişimcilik', 'forbes'],
    ['Fortune', 'İş stratejisi', 'fortune'],
    ['Bloomberg Businessweek', 'İş haberleri', 'bloomberg'],
    ['Harvard Business Review', 'Yönetim ve strateji', 'hbr'],
    ['Scientific American', 'Popüler bilim', 'sci_american'],
    ['Nature', 'Bilimsel araştırma', 'nature'],
    ['Science', 'Bilim dergisi', 'science'],
    ['New Scientist', 'Bilim haberleri', 'new_scientist'],
    ['Wired', 'Teknoloji ve kültür', 'wired'],
    ['MIT Technology Review', 'Teknoloji', 'mit_tech'],
    ['Popular Science', 'Popüler bilim', 'pop_science'],
    ['Popular Mechanics', 'Mekanik ve teknoloji', 'pop_mechanics'],
    ['National Geographic', 'Doğa ve keşif', 'nat_geo_int'],
    ['The New Yorker', 'Kültür ve edebiyat', 'new_yorker'],
    ['Rolling Stone', 'Müzik ve pop kültür', 'rolling_stone'],
    ['Vanity Fair', 'Kültür ve politika', 'vanity_fair'],
    ['Architectural Digest', 'Mimari ve tasarım', 'arch_digest'],
    ['Conde Nast Traveler', 'Seyahat', 'cnt'],
    ['Travel + Leisure', 'Gezi ve tatil', 'travel_leisure'],
    ['Food & Wine', 'Yemek ve şarap', 'food_wine'],
    ['Bon Appétit', 'Mutfak kültürü', 'bon_appetit'],
    ['Sports Illustrated', 'Spor haberleri', 'sports_ill'],
    ['ESPN The Magazine', 'Spor', 'espn']
];

$magazineInsertStmt = $pdo->prepare("
    INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, rating_score, view_count)
    VALUES ('magazine', ?, 'Çeşitli Yazarlar', ?, ?, ?, ?, ?, ROUND(3.5 + RAND() * 1.5, 2), FLOOR(RAND() * 3000))
");

$issueCount = 0;
$targetMagazines = 2000;

// Generate magazine issues
foreach ($magazines as $mag) {
    [$name, $desc, $code] = $mag;

    // Generate issues from 2020 to 2026, various months
    for ($year = 2020; $year <= 2026; $year++) {
        for ($month = 1; $month <= 12; $month++) {
            if ($issueCount >= $targetMagazines)
                break 3;

            $issueTitle = "{$name} - " . sprintf("%02d", $month) . "/{$year}";
            $issueDesc = "{$desc} - {$year} {$month}. sayı";

            // Check if exists
            $checkStmt = $pdo->prepare("SELECT id FROM items WHERE title = ?");
            $checkStmt->execute([$issueTitle]);
            if ($checkStmt->fetch())
                continue;

            // Generate cover placeholder
            $colors = ['1e40af', '7c3aed', 'be185d', 'b91c1c', 'c2410c', '15803d', '0e7490'];
            $color = $colors[$issueCount % count($colors)];
            $cover = "https://ui-avatars.com/api/?name=" . urlencode(substr($name, 0, 2)) . "&background={$color}&color=fff&size=192&bold=true";

            $pageCount = rand(80, 200);

            try {
                $magazineInsertStmt->execute([$issueTitle, $issueDesc, $cover, $dergiGenreId, $year, $pageCount]);
                $issueCount++;

                if ($issueCount % 100 == 0) {
                    echo "  📰 {$issueCount} dergi eklendi...\n";
                }
            } catch (Exception $e) {
                // Skip duplicates
            }
        }
    }
}

$stats['magazines_added'] = $issueCount;
echo "\n  🎯 Eklenen dergi: {$stats['magazines_added']}\n\n";

// ============================================
// STEP 5: FIX BROKEN COVERS
// ============================================
echo "🖼️ ADIM 5: BOZUK KAPAKLARI DÜZELT\n";
echo "───────────────────────────────────────────────────────────────\n";

// Find books with broken/empty covers
$stmt = $pdo->query("
    SELECT id, title, author, cover_image FROM items 
    WHERE type = 'book' 
    AND (
        cover_image IS NULL 
        OR cover_image = '' 
        OR cover_image LIKE '%unsplash%'
        OR cover_image LIKE '%placehold%'
    )
    LIMIT 500
");

$brokenCovers = $stmt->fetchAll();
echo "  Bozuk kapak sayısı: " . count($brokenCovers) . "\n\n";

$updateCoverStmt = $pdo->prepare("UPDATE items SET cover_image = ? WHERE id = ?");

foreach ($brokenCovers as $book) {
    $cover = getBookCover($book['title'], $book['author']);
    $updateCoverStmt->execute([$cover, $book['id']]);
    $stats['covers_fixed']++;

    if ($stats['covers_fixed'] % 50 == 0) {
        echo "  🖼️ {$stats['covers_fixed']} kapak düzeltildi...\n";
    }
    usleep(100000);
}

echo "\n  🎯 Düzeltilen kapak: {$stats['covers_fixed']}\n\n";

// ============================================
// FINAL SUMMARY
// ============================================
echo "═══════════════════════════════════════════════════════════════\n";
echo "   ÖZET RAPOR\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  🗑️ Silinen seri kitap: {$stats['series_deleted']}\n";
echo "  📚 Eklenen klasik: {$stats['classics_added']}\n";
echo "  📰 Eklenen dergi: {$stats['magazines_added']}\n";
echo "  🖼️ Düzeltilen kapak: {$stats['covers_fixed']}\n";
echo "═══════════════════════════════════════════════════════════════\n";

// Verify counts
$stmt = $pdo->query("SELECT COUNT(*) as c FROM items WHERE type = 'book'");
$bookCount = $stmt->fetch()['c'];
$stmt = $pdo->query("SELECT COUNT(*) as c FROM items WHERE type = 'magazine'");
$magCount = $stmt->fetch()['c'];

echo "\n  📊 Güncel durum:\n";
echo "     - Toplam kitap: {$bookCount}\n";
echo "     - Toplam dergi: {$magCount}\n";
echo "\n✅ Veritabanı güncelleme tamamlandı!\n";
?>