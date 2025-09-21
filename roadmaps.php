<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$current_page = 'roadmaps';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Roadmaps | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
.page{max-width:1100px;margin:20px auto;padding:0 16px}
.roadmap{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;margin-bottom:16px;box-shadow:0 6px 12px rgba(0,0,0,.04)}
.steps{margin-top:12px;padding-left:20px}
.steps li{margin-bottom:8px}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="page">
  <h1>🛣️ Roadmaps</h1>

  <?php
  $stmt = $conn->prepare("SELECT * FROM roadmaps ORDER BY created_at DESC");
  $stmt->execute();
  $roadmaps = $stmt->get_result();

  if($roadmaps->num_rows > 0){
      while($r = $roadmaps->fetch_assoc()):
  ?>
    <div class="roadmap">
      <h2><?= htmlspecialchars($r['title']) ?></h2>
      <p><?= htmlspecialchars($r['description']) ?></p>

      <?php
      $steps_stmt = $conn->prepare("SELECT * FROM roadmap_steps WHERE roadmap_id=? ORDER BY step_number ASC");
      $steps_stmt->bind_param("i", $r['id']);
      $steps_stmt->execute();
      $steps = $steps_stmt->get_result();
      if($steps->num_rows > 0):
      ?>
        <ul class="steps">
          <?php while($s = $steps->fetch_assoc()): ?>
            <li><strong>Step <?= $s['step_number'] ?>:</strong> <?= htmlspecialchars($s['title']) ?> - <?= htmlspecialchars($s['content']) ?></li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>No steps added yet.</p>
      <?php endif; ?>
    </div>
  <?php
      endwhile;
  } else {
      echo "<p>No roadmaps available yet.</p>";
  }
  ?>

</main>
</body>
</html>
