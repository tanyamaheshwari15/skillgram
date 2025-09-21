<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

$current_page = 'discussion';

// Create Topic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);

    $stmt = $conn->prepare("INSERT INTO discussions (user_id, title, body, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $_SESSION['user_id'], $title, $body);
    $stmt->execute();
    $stmt->close();

    $_SESSION['msg'] = '✅ Topic created!';
    header('Location: discussion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Discussion | SkillGram</title>
<link rel="stylesheet" href="css/style.css"> <!-- Use unified theme -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page container">
  <h1>💬 Discussion Form</h1>

  <?php if(!empty($_SESSION['msg'])): ?>
      <div class="card"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <div class="flex grid-2">
    <!-- Create Topic -->
    <div class="card">
      <h3>Create Topic</h3>
      <form method="post">
        <input name="title" placeholder="Title (e.g., Help with DP problem)" required>
        <textarea name="body" placeholder="Describe your question..." rows="5"></textarea>
        <button class="btn">Post</button>
      </form>
    </div>

    <!-- Recent Topics -->
    <div class="card">
      <h3>Recent Topics</h3>
      <?php
      $sql = "SELECT d.id, d.title, d.created_at, u.name 
              FROM discussions d 
              JOIN users u ON d.user_id = u.id 
              ORDER BY d.created_at DESC LIMIT 10";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          while($t = $result->fetch_assoc()): ?>
            <div class="topic">
              <a href="discussion_view.php?id=<?= $t['id'] ?>" style="text-decoration:none;color:#111">
                <b><?= htmlspecialchars($t['title']) ?></b>
              </a>
              <div class="small">by <?= htmlspecialchars($t['name']) ?> • <?= date("M d, H:i", strtotime($t['created_at'])) ?></div>
            </div>
      <?php endwhile; 
      } else {
          echo "<p>No topics yet. Be the first!</p>";
      }
      ?>
    </div>
  </div>
</main>
</body>
</html>
