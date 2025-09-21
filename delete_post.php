<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: post.php");
    exit();
}

// Ensure the post belongs to the current user before deleting
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($owner_id);
$stmt->fetch();
$stmt->close();

if (!$owner_id || (int)$owner_id !== $user_id) {
    header("Location: post.php");
    exit();
}

$del = $conn->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
$del->bind_param('ii', $id, $user_id);
$del->execute();
$del->close();

header("Location: post.php");
exit();
?>
