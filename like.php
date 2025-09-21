<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id > 0) {
    // Check if like already exists
    $chk = $conn->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
    $chk->bind_param('ii', $post_id, $user_id);
    $chk->execute();
    $chk->store_result();

    if ($chk->num_rows > 0) {
        // Unlike (delete)
        $del = $conn->prepare("DELETE FROM likes WHERE post_id=? AND user_id=?");
        $del->bind_param('ii', $post_id, $user_id);
        $del->execute();
        $del->close();
    } else {
        // Like (insert)
        $ins = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $ins->bind_param('ii', $post_id, $user_id);
        $ins->execute();
        $ins->close();
    }
    $chk->close();
}

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'home.php';
header("Location: $redirect");
exit();
?>


