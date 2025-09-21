<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Basic validation
if ($name === '' || strlen($name) > 100) {
    header("Location: profile.php");
    exit();
}
if (strlen($bio) > 255) {
    $bio = substr($bio, 0, 255);
}

// Handle profile picture upload
$profile_pic = null;
if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
    $allowed_ext = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    $size_ok = $_FILES['profile_pic']['size'] <= 2 * 1024 * 1024; // 2MB

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['profile_pic']['tmp_name']);
    finfo_close($finfo);
    $allowed_mime = ['image/jpeg','image/png','image/gif'];

    if (in_array($ext, $allowed_ext, true) && in_array($mime, $allowed_mime, true) && $size_ok) {
        if (!is_dir('uploads')) { @mkdir('uploads', 0755, true); }
        $profile_pic = 'profile_'.$user_id.'.'.$ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/'.$profile_pic);
    }
}

// Update DB
if($profile_pic){
    $stmt = $conn->prepare("UPDATE users SET name=?, bio=?, profile_pic=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $bio, $profile_pic, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET name=?, bio=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $bio, $user_id);
}
$stmt->execute();
$stmt->close();

header("Location: profile.php");
exit();
?>
