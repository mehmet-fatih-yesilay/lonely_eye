<?php
/**
 * Upload Profile Photo API
 * Handles profile photo uploads
 */

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Dosya yüklenirken bir hata oluştu.']);
    exit;
}

$file = $_FILES['profile_photo'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz dosya türü. Sadece JPG, PNG, GIF ve WebP desteklenir.']);
    exit;
}

// Validate file size
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'Dosya boyutu çok büyük. Maksimum 5MB olmalıdır.']);
    exit;
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
$upload_path = '../assets/uploads/' . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Dosya kaydedilirken bir hata oluştu.']);
    exit;
}

// Update database
try {
    $avatar_url = '/lonely_eye/assets/uploads/' . $filename;
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->execute([$avatar_url, $user_id]);

    // Update session
    $_SESSION['avatar'] = $avatar_url;

    echo json_encode([
        'success' => true,
        'message' => 'Profil fotoğrafı başarıyla güncellendi!',
        'avatar_url' => $avatar_url
    ]);

} catch (PDOException $e) {
    error_log("Profile Photo Upload Error: " . $e->getMessage());

    // Delete uploaded file if database update fails
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }

    echo json_encode(['success' => false, 'message' => 'Veritabanı güncellenirken bir hata oluştu.']);
}
?>