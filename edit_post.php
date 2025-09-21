<?php
include 'db.php';
$current_page = 'post';
include 'navbar.php'; // Do NOT call session_start() again

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: post.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch the post to edit
$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $content);

if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: post.php");
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Skill - SkillGram</title>
<link rel="stylesheet" href="css/post.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<main>
  <div class="post-container">
    <h2>Edit Your Skill Progress 🚀</h2>
    <form action="save_post.php" method="POST">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($post_id); ?>">
  <label for="title">Skill Title:</label>
  <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>

  <label for="desc">Description:</label>
  <textarea id="desc" name="desc" rows="4" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>

  <button type="submit" class="submit-btn">Update</button>
</form>

  </div>
</main>

</body>
</html>
