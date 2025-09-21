<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$follower_id = $_SESSION['user_id'];

if (isset($_GET['follow_id'])) {
    $user_id = (int)$_GET['follow_id'];
    if ($user_id === (int)$follower_id) {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'profile.php';
        header("Location: $redirect");
        exit();
    }

    // Check if already followed
    $check = $conn->prepare("SELECT id FROM followers WHERE user_id=? AND follower_id=?");
    $check->bind_param("ii", $user_id, $follower_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        // Follow
        $stmt = $conn->prepare("INSERT INTO followers (user_id, follower_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $follower_id);
        $stmt->execute();
        $stmt->close();
        $action = 'followed';
    } else {
        // Unfollow
        $stmt = $conn->prepare("DELETE FROM followers WHERE user_id=? AND follower_id=?");
        $stmt->bind_param("ii", $user_id, $follower_id);
        $stmt->execute();
        $stmt->close();
        $action = 'unfollowed';
    }

    $check->close();
}

// Redirect back to the page user came from
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'profile.php';
// Support fetch() call by returning JSON when requested
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode(['status'=>'ok','action'=>$action ?? 'none']);
    exit();
}
header("Location: $redirect");
exit();
?>
