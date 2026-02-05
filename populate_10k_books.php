<?php
/**
 * Populate Database with 10,000 Real Books
 * Uses Google Books API to fetch real book data
 */

require_once 'includes/db.php';

set_time_limit(0); // No time limit for this script
ini_set('memory_limit', '512M');

echo "🚀 Starting massive book data population (10,000 books)...\n\n";

// Google Books API configuration
$apiKey = 'AIzaSyDummyKey'; // You can use without key for testing, but with key is better
$baseUrl = 'https://www.googleapis.com/books/v1/volumes';

// Function to fetch books from Google Books API
function fetchBooksFromAPI($query, $maxResults = 40, $startIndex = 0)
{
    global $baseUrl, $apiKey;

    $url = $baseUrl . '?' . http_build_query([
        'q' => $query,
        'maxResults' => $maxResults,
        'startIndex' => $startIndex,
        'langRestrict' => 'tr', // Turkish language
        'printType' => 'books',
        'orderBy' => 'relevance'
    ]);

    if ($apiKey && $apiKey !== 'AIzaSyDummyKey') {
        $url .= '&key=' . $apiKey;
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);

    if (!isset($data['items'])) {
        return [];
    }

    $books = [];
    foreach ($data['items'] as $item) {
        $volumeInfo = $item['volumeInfo'] ?? [];

        // Extract book information
        $title = $volumeInfo['title'] ?? 'Unknown';
        $authors = $volumeInfo['authors'] ?? ['Unknown'];
        $author = implode(', ', array_slice($authors, 0, 2)); // Max 2 authors
        $publishedDate = $volumeInfo['publishedDate'] ?? '2000';
        $year = (int) substr($publishedDate, 0, 4);
        $description = $volumeInfo['description'] ?? 'Açıklama mevcut değil.';
        $pageCount = $volumeInfo['pageCount'] ?? rand(150, 400);

        // Get cover image
        $coverImage = 'https://via.placeholder.com/128x192.png?text=No+Cover';
        if (isset($volumeInfo['imageLinks'])) {
            if (isset($volumeInfo['imageLinks']['thumbnail'])) {
                $coverImage = str_replace('http:', 'https:', $volumeInfo['imageLinks']['thumbnail']);
            } elseif (isset($volumeInfo['imageLinks']['smallThumbnail'])) {
                $coverImage = str_replace('http:', 'https:', $volumeInfo['imageLinks']['smallThumbnail']);
            }
        }

        // Truncate description
        if (strlen($description) > 500) {
            $description = substr($description, 0, 497) . '...';
        }

        $books[] = [
            'title' => $title,
            'author' => $author,
            'year' => $year,
            'pages' => $pageCount,
            'desc' => $description,
            'cover' => $coverImage
        ];
    }

    return $books;
}

// Search queries for different categories
$searchQueries = [
    // Turkish Literature (3000 books)
    'turkish' => [
        'Türk edebiyatı roman',
        'Türk yazarlar',
        'Türkçe roman',
        'Türk klasikleri',
        'Yaşar Kemal',
        'Orhan Pamuk',
        'Sabahattin Ali',
        'Ahmet Hamdi Tanpınar',
        'Oğuz Atay',
        'Nazım Hikmet',
        'Aziz Nesin',
        'Reşat Nuri Güntekin',
        'Halide Edip Adıvar',
        'Peyami Safa',
        'Kemal Tahir',
        'Necip Fazıl',
        'Attila İlhan',
        'Sait Faik',
        'Haldun Taner',
        'Fakir Baykurt',
        'Tarık Buğra',
        'Ömer Seyfettin',
        'Refik Halit Karay',
        'Yakup Kadri',
        'Memduh Şevket Esendal',
        'Cemil Meriç',
        'Nurettin Topçu',
        'İsmet Özel',
        'Rasim Özdenören',
        'Cahit Zarifoğlu',
        'Mustafa Kutlu',
        'Ayşe Kulin',
        'Zülfü Livaneli',
        'Ahmet Ümit',
        'Elif Şafak',
        'Mario Levi',
        'Buket Uzuner',
        'Murathan Mungan',
        'Latife Tekin',
        'Aslı Erdoğan',
        'Perihan Mağden',
        'Hakan Günday',
        'Barış Bıçakçı',
        'Emrah Serbes',
        'Gülten Akın',
        'Can Yücel',
        'Ece Ayhan',
        'Turgut Uyar',
        'Edip Cansever',
        'Cemal Süreya',
        'Türk şiir',
        'Türk hikaye',
        'Türk deneme',
        'Türk tiyatro',
        'Türk biyografi',
        'Türk anı',
        'Türk gezi',
        'Türk tarih',
        'Osmanlı edebiyatı',
        'Divan edebiyatı',
    ],

    // World Classics (3000 books)
    'classics' => [
        'Dostoyevski',
        'Tolstoy',
        'Kafka',
        'Camus',
        'Sartre',
        'Victor Hugo',
        'Balzac',
        'Stendhal',
        'Flaubert',
        'Zola',
        'Maupassant',
        'Proust',
        'Goethe',
        'Thomas Mann',
        'Hermann Hesse',
        'Nietzsche',
        'Schopenhauer',
        'Kant',
        'Hegel',
        'Shakespeare',
        'Dickens',
        'Jane Austen',
        'Brontë',
        'Oscar Wilde',
        'George Orwell',
        'Aldous Huxley',
        'Virginia Woolf',
        'James Joyce',
        'Hemingway',
        'Faulkner',
        'Steinbeck',
        'Mark Twain',
        'Edgar Allan Poe',
        'F. Scott Fitzgerald',
        'Jack London',
        'Herman Melville',
        'Cervantes',
        'García Márquez',
        'Borges',
        'Cortázar',
        'Vargas Llosa',
        'Pablo Neruda',
        'Octavio Paz',
        'Chekhov',
        'Gogol',
        'Pushkin',
        'Turgenev',
        'Bulgakov',
        'Solzhenitsyn',
        'Dante',
        'Boccaccio',
        'Calvino',
        'Umberto Eco',
        'Italo Svevo',
        'Platon',
        'Aristoteles',
        'Homer',
        'Sophocles',
        'Euripides',
        'world literature',
        'classic novels',
        'philosophy classics',
        'ancient literature',
        'renaissance literature',
        'enlightenment philosophy',
        'romanticism literature',
        'realism literature',
        'modernism literature',
        'existentialism',
        'absurdism',
        'magical realism',
        'Latin American literature',
        'Russian literature',
        'French literature',
        'German literature',
        'English literature',
        'American literature',
        'Spanish literature',
        'Italian literature',
        'Greek classics',
    ],

    // Islamic Literature (1000 books)
    'islamic' => [
        'Sadi Şirazi',
        'Gülistan',
        'Bostan',
        'İmam Gazali',
        'İhya',
        'Kimya-yı Saadet',
        'Mevlana',
        'Mesnevi',
        'Divan-ı Kebir',
        'Fihi Ma Fih',
        'Yunus Emre',
        'İbn Arabi',
        'Fusus',
        'Futuhat',
        'Rumi',
        'Attar',
        'Mantıku\'t-Tayr',
        'Hafız Şirazi',
        'İbn Sina',
        'Şifa',
        'İşaretler',
        'Farabi',
        'El-Medine',
        'İbn Haldun',
        'Mukaddime',
        'İbn Rüşd',
        'Tehafüt',
        'Bediüzzaman',
        'Risale-i Nur',
        'Sözler',
        'Mektubat',
        'İslam felsefesi',
        'Tasavvuf',
        'Sufi edebiyatı',
        'İslam tarihi',
        'Peygamber kıssaları',
        'Sahabe hayatları',
        'İslam ahlakı',
        'İslam bilimi',
        'İslam medeniyeti',
        'Osmanlı tasavvuf',
        'Türk-İslam düşüncesi',
        'İslam sanatı',
        'Kur\'an tefsiri',
        'Hadis şerhi',
        'Fıkıh',
        'Kelam',
        'İslam mantığı',
        'Arap edebiyatı',
        'Fars edebiyatı',
        'İslam şiiri',
    ],
];

// Other categories (3000 books distributed)
$otherCategories = [
    'Science Fiction' => ['science fiction', 'sci-fi', 'cyberpunk', 'space opera', 'dystopia', 'utopia'],
    'Fantasy' => ['fantasy', 'epic fantasy', 'urban fantasy', 'dark fantasy', 'high fantasy'],
    'Mystery' => ['mystery', 'detective', 'crime', 'thriller', 'suspense', 'noir'],
    'Romance' => ['romance', 'love story', 'romantic fiction'],
    'Horror' => ['horror', 'gothic', 'supernatural', 'terror'],
    'Biography' => ['biography', 'autobiography', 'memoir', 'life story'],
    'History' => ['history', 'historical', 'ancient history', 'modern history', 'world war'],
    'Science' => ['science', 'physics', 'biology', 'chemistry', 'astronomy', 'mathematics'],
    'Psychology' => ['psychology', 'psychoanalysis', 'cognitive science', 'neuroscience'],
    'Philosophy' => ['philosophy', 'ethics', 'metaphysics', 'epistemology', 'logic'],
    'Self-Help' => ['self-help', 'personal development', 'motivation', 'success'],
    'Business' => ['business', 'economics', 'management', 'entrepreneurship', 'finance'],
    'Technology' => ['technology', 'computer science', 'programming', 'artificial intelligence'],
    'Art' => ['art', 'painting', 'sculpture', 'architecture', 'design'],
    'Music' => ['music', 'classical music', 'jazz', 'rock', 'music theory'],
    'Poetry' => ['poetry', 'poems', 'verse', 'sonnets'],
    'Drama' => ['drama', 'plays', 'theater', 'tragedy', 'comedy'],
    'Travel' => ['travel', 'adventure', 'exploration', 'journey'],
    'Cooking' => ['cooking', 'cuisine', 'recipes', 'gastronomy'],
    'Sports' => ['sports', 'football', 'basketball', 'athletics', 'fitness'],
];

try {
    // Clear existing items
    echo "🗑️  Clearing existing book data...\n";
    $pdo->exec("DELETE FROM items WHERE id > 0");
    $pdo->exec("ALTER TABLE items AUTO_INCREMENT = 1");
    echo "✅ Existing data cleared\n\n";

    $totalInserted = 0;
    $batchSize = 100;

    // Prepare insert statement
    $stmt = $pdo->prepare("
        INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score)
        VALUES ('book', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Helper function to insert books
    function insertBooks($books, $genreId, &$stmt, &$totalInserted)
    {
        foreach ($books as $book) {
            $viewCount = rand(50, 5000);
            $rating = round(3 + (rand(0, 200) / 100), 2);

            try {
                $stmt->execute([
                    $book['title'],
                    $book['author'],
                    $book['desc'],
                    $book['cover'],
                    $genreId,
                    $book['year'],
                    $book['pages'],
                    $viewCount,
                    $rating
                ]);
                $totalInserted++;

                if ($totalInserted % 100 == 0) {
                    echo "  ✓ {$totalInserted} books inserted...\n";
                }
            } catch (PDOException $e) {
                // Skip duplicates
                continue;
            }
        }
    }

    // 1. Turkish Literature (3000 books) - Genre 1
    echo "📚 Fetching Turkish Literature (Target: 3000 books)...\n";
    $turkishCount = 0;
    foreach ($searchQueries['turkish'] as $query) {
        if ($turkishCount >= 3000)
            break;

        for ($startIndex = 0; $startIndex < 200; $startIndex += 40) {
            if ($turkishCount >= 3000)
                break;

            $books = fetchBooksFromAPI($query, 40, $startIndex);
            if (empty($books))
                break;

            insertBooks($books, 1, $stmt, $totalInserted);
            $turkishCount += count($books);

            usleep(100000); // 100ms delay to avoid rate limiting
        }
    }
    echo "✅ Turkish Literature: {$turkishCount} books\n\n";

    // 2. World Classics (3000 books) - Genre 1
    echo "🌍 Fetching World Classics (Target: 3000 books)...\n";
    $classicsCount = 0;
    foreach ($searchQueries['classics'] as $query) {
        if ($classicsCount >= 3000)
            break;

        for ($startIndex = 0; $startIndex < 200; $startIndex += 40) {
            if ($classicsCount >= 3000)
                break;

            $books = fetchBooksFromAPI($query, 40, $startIndex);
            if (empty($books))
                break;

            insertBooks($books, 1, $stmt, $totalInserted);
            $classicsCount += count($books);

            usleep(100000);
        }
    }
    echo "✅ World Classics: {$classicsCount} books\n\n";

    // 3. Islamic Literature (1000 books) - Genre 14 (Religion)
    echo "☪️  Fetching Islamic Literature (Target: 1000 books)...\n";
    $islamicCount = 0;
    foreach ($searchQueries['islamic'] as $query) {
        if ($islamicCount >= 1000)
            break;

        for ($startIndex = 0; $startIndex < 200; $startIndex += 40) {
            if ($islamicCount >= 1000)
                break;

            $books = fetchBooksFromAPI($query, 40, $startIndex);
            if (empty($books))
                break;

            insertBooks($books, 14, $stmt, $totalInserted);
            $islamicCount += count($books);

            usleep(100000);
        }
    }
    echo "✅ Islamic Literature: {$islamicCount} books\n\n";

    // 4. Other Categories (3000 books distributed)
    echo "📖 Fetching Other Categories (Target: 3000 books)...\n";
    $otherCount = 0;
    $booksPerCategory = ceil(3000 / count($otherCategories));

    $genreMap = [
        'Science Fiction' => 4,
        'Fantasy' => 5,
        'Mystery' => 8,
        'Romance' => 6,
        'Horror' => 11,
        'Biography' => 9,
        'History' => 3,
        'Science' => 13,
        'Psychology' => 10,
        'Philosophy' => 8,
        'Self-Help' => 7,
        'Business' => 7,
        'Technology' => 13,
        'Art' => 12,
        'Music' => 12,
        'Poetry' => 2,
        'Drama' => 12,
        'Travel' => 12,
        'Cooking' => 7,
        'Sports' => 7,
    ];

    foreach ($otherCategories as $category => $queries) {
        if ($otherCount >= 3000)
            break;

        $categoryCount = 0;
        $genreId = $genreMap[$category] ?? 1;

        foreach ($queries as $query) {
            if ($categoryCount >= $booksPerCategory || $otherCount >= 3000)
                break;

            for ($startIndex = 0; $startIndex < 100; $startIndex += 40) {
                if ($categoryCount >= $booksPerCategory || $otherCount >= 3000)
                    break;

                $books = fetchBooksFromAPI($query, 40, $startIndex);
                if (empty($books))
                    break;

                insertBooks($books, $genreId, $stmt, $totalInserted);
                $categoryCount += count($books);
                $otherCount += count($books);

                usleep(100000);
            }
        }

        echo "  ✓ {$category}: {$categoryCount} books\n";
    }
    echo "✅ Other Categories: {$otherCount} books\n\n";

    // Summary
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 DATABASE POPULATION COMPLETE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Turkish Literature: ~{$turkishCount}\n";
    echo "📊 World Classics: ~{$classicsCount}\n";
    echo "📊 Islamic Literature: ~{$islamicCount}\n";
    echo "📊 Other Categories: ~{$otherCount}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 GRAND TOTAL: {$totalInserted} books\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    if ($totalInserted < 10000) {
        echo "⚠️  Note: Reached {$totalInserted} books. Google Books API has limitations.\n";
        echo "    To reach 10,000 books, run this script multiple times or use an API key.\n\n";
    }

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>