<?php
/**
 * Fast 10,000 Real Books Population
 * Pre-compiled dataset with real books, authors, years, and cover images
 */

require_once 'includes/db.php';

set_time_limit(0);
ini_set('memory_limit', '1024M');

echo "🚀 Starting FAST 10,000 real books population...\n\n";

// Helper function to generate cover URL
function getCover($isbn)
{
    if ($isbn) {
        return "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg";
    }
    return "https://via.placeholder.com/128x192.png?text=No+Cover";
}

// Load book data from JSON files
$booksData = [];

// Turkish Literature - 3000 books
echo "📚 Loading Turkish Literature data...\n";
include 'book_data/turkish_books.php';
$booksData = array_merge($booksData, $turkishBooks ?? []);

// World Classics - 3000 books  
echo "🌍 Loading World Classics data...\n";
include 'book_data/world_classics.php';
$booksData = array_merge($booksData, $worldClassics ?? []);

// Islamic Literature - 1000 books
echo "☪️  Loading Islamic Literature data...\n";
include 'book_data/islamic_books.php';
$booksData = array_merge($booksData, $islamicBooks ?? []);

// Other Categories - 3000 books
echo "📖 Loading Other Categories data...\n";
include 'book_data/other_books.php';
$booksData = array_merge($booksData, $otherBooks ?? []);

try {
    // Clear existing items
    echo "\n🗑️  Clearing existing book data...\n";
    $pdo->exec("DELETE FROM items WHERE id > 0");
    $pdo->exec("ALTER TABLE items AUTO_INCREMENT = 1");
    echo "✅ Existing data cleared\n\n";

    // Prepare insert statement
    $stmt = $pdo->prepare("
        INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score)
        VALUES ('book', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $totalInserted = 0;

    echo "💾 Inserting books into database...\n";

    foreach ($booksData as $book) {
        $viewCount = rand(50, 5000);
        $rating = round(3 + (rand(0, 200) / 100), 2);

        try {
            $stmt->execute([
                $book['title'],
                $book['author'],
                $book['desc'],
                $book['cover'],
                $book['genre'],
                $book['year'],
                $book['pages'],
                $viewCount,
                $rating
            ]);

            $totalInserted++;

            if ($totalInserted % 500 == 0) {
                echo "  ✓ {$totalInserted} books inserted...\n";
            }
        } catch (PDOException $e) {
            // Skip duplicates
            continue;
        }
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 DATABASE POPULATION COMPLETE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 TOTAL BOOKS INSERTED: {$totalInserted}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>