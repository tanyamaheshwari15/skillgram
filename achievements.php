<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$current_page = 'achievements';
$user_id = $_SESSION['user_id'];

// Fetch user stats dynamically
$xp = $streak = $problems_solved = 0;
$user_stats = $conn->query("SELECT * FROM users WHERE id=" . intval($user_id))->fetch_assoc();
if($user_stats){
    $xp = $user_stats['xp'] ?? 0;
    $streak = $user_stats['streak'] ?? 0;
    $problems_solved = $user_stats['problems_solved'] ?? 0;
}

// ✅ Fetch achievements with JOIN
$badges = [];
$sql = "SELECT a.badge_name, a.badge_desc, a.icon, ua.earned_at
        FROM user_achievements ua
        INNER JOIN achievements a ON ua.achievement_id = a.id
        WHERE ua.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()){
    $badges[] = [
        'icon'  => $row['icon'] ?? '🏆',
        'title' => $row['badge_name'] ?? 'Achievement',
        'desc'  => $row['badge_desc'] ?? '',
        'earned'=> $row['earned_at']
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Achievements | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f5f6fa;margin:0;padding:0;}
.page{max-width:1100px;margin:20px auto;padding:0 16px;}
.cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-top:20px;}
.card{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;box-shadow:0 6px 12px rgba(0,0,0,.04);text-align:center;transition:transform .2s;}
.card:hover{transform:translateY(-5px);}
.badge{font-size:36px;margin-bottom:8px;}
.small{color:#6b7280;font-size:12px;}
.stat{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:20px 0;}
.stat .box{background:#f7f7ff;border:1px solid #ececff;border-radius:12px;padding:14px;text-align:center;transition:background .2s;}
.stat .box:hover{background:#e0e4ff;}
.big{font-size:16px;color:#111;font-weight:600;margin-bottom:4px;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page">
  <h1>🏅 Achievements</h1>

  <div class="stat">
    <div class="box"><div class="big">XP</div><div><?= number_format($xp) ?></div></div>
    <div class="box"><div class="big">Streak</div><div><?= $streak ?> days</div></div>
    <div class="box"><div class="big">Problems Solved</div><div><?= $problems_solved ?></div></div>
  </div>

  <div class="cards">
    <?php if(count($badges) > 0): ?>
        <?php foreach($badges as $b): ?>
          <div class="card">
            <div class="badge"><?= htmlspecialchars($b['icon']) ?></div>
            <h3><?= htmlspecialchars($b['title']) ?></h3>
            <p class="small"><?= htmlspecialchars($b['desc']) ?></p>
            <p class="small">Earned on: <?= htmlspecialchars($b['earned']) ?></p>
          </div>
        <?php endforeach; ?>
    <?php else: ?>
      <p>No badges earned yet. Keep learning! 🔥</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
