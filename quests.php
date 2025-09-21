<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$current_page = 'quests';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quests & Games | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
.page{max-width:1100px;margin:20px auto;padding:0 16px;}
.funquest-slider{
  width: 100%;
  height: 600px;
  border:none;
  border-radius:12px;
  box-shadow:0 6px 12px rgba(0,0,0,.08);
  transition: transform 0.3s;
}
.funquest-slider:hover{
  transform: scale(1.02);
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page">
  <h1>🎮 Quests & Mini Games</h1>
  <iframe class="funquest-slider" src="funquest/index.html"></iframe>
</main>
</body>
</html>
