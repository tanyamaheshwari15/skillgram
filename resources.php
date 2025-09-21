<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$current_page = 'resources';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resources | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
.page{max-width:1100px;margin:20px auto;padding:0 16px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.card{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;box-shadow:0 6px 12px rgba(0,0,0,.04)}
a.btn{display:inline-block;background:#6286fb;color:#fff;padding:8px 14px;border-radius:10px;text-decoration:none;margin-top:8px}
.small{color:#6b7280;font-size:12px}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="page">
  <h1>📎 Resources</h1>
  <div class="grid">

    <?php
    // Fetch resources
    $stmt = $conn->prepare("SELECT * FROM resources ORDER BY created_at DESC");
    $stmt->execute();
    $resources = $stmt->get_result();

    if($resources->num_rows > 0){
        while($r = $resources->fetch_assoc()): ?>
          <div class="card">
            <h3><?= htmlspecialchars($r['title']) ?></h3>
            <p class="small"><?= htmlspecialchars($r['category']) ?></p>
            <a class="btn" href="<?= htmlspecialchars($r['link']) ?>" target="_blank">Open</a>
          </div>
    <?php 
        endwhile;
    }

    // Fetch notes (used as additional resources)
    $notes_stmt = $conn->prepare("SELECT title, content, file_path FROM notes ORDER BY created_at DESC");
    $notes_stmt->execute();
    $notes = $notes_stmt->get_result();

    if($notes->num_rows > 0){
        while($n = $notes->fetch_assoc()): ?>
          <div class="card">
            <h3><?= htmlspecialchars($n['title']) ?></h3>
            <p class="small"><?= htmlspecialchars($n['content']) ?></p>
            <?php if(!empty($n['file_path'])): ?>
              <a class="btn" href="<?= htmlspecialchars($n['file_path']) ?>" target="_blank">Download</a>
            <?php endif; ?>
          </div>
    <?php
        endwhile;
    }

    if($resources->num_rows == 0 && $notes->num_rows == 0){
        echo "<p>No resources available yet.</p>";
    }
    ?>

  </div>
</main>
</body>
</html>
