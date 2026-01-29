<?php
/**
 * Index Page - Router
 * Redirects to dashboard if logged in, otherwise to login page
 */

// Include database connection (which also starts session)
require_once 'includes/db.php';

// Check if user is logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // User is logged in, redirect to dashboard
    header('Location: dashboard.php');
    exit;
} else {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit;
}
?>