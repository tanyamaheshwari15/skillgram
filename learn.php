<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$current_page = 'learn';

// Fetch courses dynamically
$courses = [];
$result = $conn->query("SELECT id, title, description, level, modules FROM courses ORDER BY id ASC");
while($row = $result->fetch_assoc()){
    $courses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Learn | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f5f6fa;margin:0;padding:0;}
.page{max-width:1100px;margin:80px auto 20px;padding:0 16px} /* top margin for navbar */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.card{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;box-shadow:0 6px 12px rgba(0,0,0,.04);transition:transform .2s;}
.card:hover{transform:translateY(-5px);}
.badge{display:inline-block;background:#eef3ff;color:#3b5bdb;padding:4px 10px;border-radius:999px;font-size:12px;margin-bottom:8px;}
.btn{display:inline-block;background:#6286fb;color:#fff;padding:8px 14px;border-radius:10px;text-decoration:none;margin-top:8px;transition:background .2s;}
.btn:hover{background:#3b5bdb;}
h1{margin:12px 0 20px;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="page">
  <h1>📚 Learning Paths</h1>
  <div class="grid">
    <?php if(count($courses) > 0):
        foreach($courses as $c): ?>
          <div class="card">
            <div class="badge"><?= htmlspecialchars($c['level']) ?></div>
            <h3><?= htmlspecialchars($c['title']) ?></h3>
            <p><?= htmlspecialchars($c['description']) ?></p>
            <p><b><?= (int)$c['modules'] ?></b> modules</p>
            <a class="btn" href="practice.php?course_id=<?= (int)$c['id'] ?>">Start</a>
          </div>
    <?php endforeach; else: ?>
      <p>No courses available yet. Check back later!</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
