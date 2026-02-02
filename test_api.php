<?php
/**
 * API Test Script
 * Tests get_books.php endpoint and logs results
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing get_books.php API ===\n\n";

// Test 1: Check if file exists
$api_file = __DIR__ . '/api/get_books.php';
echo "1. File exists: " . (file_exists($api_file) ? "YES" : "NO") . "\n";

// Test 2: Check database connection
require_once 'includes/db.php';
echo "2. Database connected: " . (isset($pdo) ? "YES" : "NO") . "\n";

if (isset($pdo)) {
    // Test 3: Check items table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM items");
        $result = $stmt->fetch();
        echo "3. Items in database: " . $result['count'] . "\n";
    } catch (PDOException $e) {
        echo "3. Database error: " . $e->getMessage() . "\n";
    }
}

// Test 4: Simulate API call
echo "\n4. Simulating API call...\n";
$_GET['page'] = 1;
$_GET['limit'] = 5;
$_GET['lang'] = 'all';

ob_start();
include $api_file;
$output = ob_get_clean();

echo "5. API Output:\n";
echo $output . "\n";

// Test 5: Validate JSON
$json = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "\n6. JSON Valid: YES\n";
    echo "7. Books returned: " . count($json) . "\n";
    if (count($json) > 0) {
        echo "8. First book title: " . ($json[0]['title'] ?? 'N/A') . "\n";
    }
} else {
    echo "\n6. JSON Valid: NO\n";
    echo "7. JSON Error: " . json_last_error_msg() . "\n";
}

echo "\n=== Test Complete ===\n";
?>