<?php
/**
 * Direct API Test - Minimal version
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Include database
require_once __DIR__ . '/includes/db.php';

// Simulate GET parameters
$_GET['page'] = 1;
$_GET['limit'] = 5;
$_GET['lang'] = 'all';
$_GET['category'] = '';
$_GET['search'] = '';

// Include the API file
include __DIR__ . '/api/get_books.php';

// Get the output
$output = ob_get_clean();

// Display results
echo "=== API Response ===\n";
echo $output . "\n\n";

// Parse JSON
$json = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "=== Parsed Successfully ===\n";
    echo "Books count: " . count($json) . "\n";

    if (count($json) > 0) {
        echo "\nFirst book:\n";
        print_r($json[0]);
    } else {
        echo "\nWARNING: Empty array returned!\n";

        // Debug: Check database directly
        echo "\n=== Direct Database Check ===\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM items");
        $result = $stmt->fetch();
        echo "Total items in DB: " . $result['count'] . "\n";

        if ($result['count'] > 0) {
            echo "\n=== Sample Books from DB ===\n";
            $stmt = $pdo->query("SELECT id, title, author FROM items LIMIT 3");
            while ($row = $stmt->fetch()) {
                echo "- ID: {$row['id']}, Title: {$row['title']}, Author: {$row['author']}\n";
            }
        }
    }
} else {
    echo "=== JSON Parse Error ===\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
?>