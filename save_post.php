<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['desc'] ?? '');

    if ($title === '' || $content === '') {
        header("Location: post.php");
        exit();
    }

    if (!empty($_POST['id'])) {
        // Update existing post (owned by user)
        $post_id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssii", $title, $content, $post_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Create new post
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $title, $content);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: post.php");
    exit();
}
?>
