<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$current_page = 'projects';

// Fetch projects from DB
$stmt = $conn->prepare("SELECT id, title, description, tags FROM projects");
$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projects | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
.page{max-width:1100px;margin:20px auto;padding:0 16px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.card{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;box-shadow:0 6px 12px rgba(0,0,0,.04)}
.btn{display:inline-block;background:#6286fb;color:#fff;padding:8px 14px;border-radius:10px;text-decoration:none;margin-top:8px}
.tag{display:inline-block;background:#f6f8ff;color:#3949ab;padding:4px 10px;border-radius:999px;font-size:12px;margin-right:6px}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page">
  <h1>🛠️ Guided Mini Projects</h1>
  <div class="grid">
    <?php foreach($projects as $p): ?>
      <div class="card">
        <h3><?= htmlspecialchars($p['title']) ?></h3>
        <p><?= htmlspecialchars($p['description']) ?></p>
        <p>
          <?php 
            $tags = explode(',', $p['tags']); 
            foreach($tags as $t) echo "<span class='tag'>".htmlspecialchars(trim($t))."</span>"; 
          ?>
        </p>
        <a class="btn" href="quests.php?template=<?= urlencode($p['title']) ?>">Start</a>
      </div>
    <?php endforeach; ?>
  </div>
</main>
</body>
</html>
