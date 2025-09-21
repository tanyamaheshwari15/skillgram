<?php
include 'db.php';
$current_page = 'post';
include 'navbar.php';

$user_id = $_SESSION['user_id'];

// Fetch user's posts
$posts = $conn->query("SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/post.css">
  <title>Post Skill - SkillGram</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<main>
  <div class="post-container">
    <h2>Share Your Skill Progress 🚀</h2>
    <!-- Post Form -->
    <form action="save_post.php" method="POST">
      <label for="title">Skill Title:</label>
      <input type="text" id="title" name="title" placeholder="e.g. Day 5 - Learned CSS Grid" required>

      <label for="desc">Description:</label>
      <textarea id="desc" name="desc" rows="4" placeholder="Describe your progress..." required></textarea>

      <button type="submit" class="submit-btn">Post</button>
    </form>

    <hr>

    <!-- User's Posts -->
    <h3>Your Posts</h3>
    <?php if($posts->num_rows > 0): ?>
      <?php while($post = $posts->fetch_assoc()): ?>
        <div class="user-post">
          <h4><?php echo htmlspecialchars($post['title']); ?></h4>
          <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
          <small>Posted on: <?php echo $post['created_at']; ?></small>
          <div class="post-actions">
            <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a> |
            <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No posts yet. Start sharing your skill progress!</p>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
