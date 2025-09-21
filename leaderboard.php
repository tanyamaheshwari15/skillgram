<?php
session_start();
include 'db.php';
$current_page = 'leaderboard';
include 'navbar.php';

// Fetch all users
$users = [];
$res = $conn->query("SELECT id, name, profile_pic, xp, streak, problems_solved FROM users");
while ($row = $res->fetch_assoc()) $users[] = $row;
// My ranks per category
$my_id = $_SESSION['user_id'];
function findCategoryRank($users, $field, $my_id) {
    usort($users, fn($a,$b) => $b[$field] <=> $a[$field]);
    foreach($users as $i => $u) if($u['id'] == $my_id) return $i + 1;
    return null;
}

$myStreakRank = findCategoryRank($users, 'streak', $my_id);
$myXPRank = findCategoryRank($users, 'xp', $my_id);
$myProblemsRank = findCategoryRank($users, 'problems_solved', $my_id);


// My ID & rank

function findRank($users,$my_id){ foreach($users as $i=>$u) if($u['id']==$my_id) return $i+1; return null; }
$myRank = findRank($users,$my_id);

// Top Badge Earner
$topBadgeUser = $conn->query("
  SELECT u.id,u.name,u.profile_pic,COUNT(ua.id) as badges
  FROM users u
  LEFT JOIN user_achievements ua ON u.id = ua.user_id
  GROUP BY u.id
  ORDER BY badges DESC
  LIMIT 1
")->fetch_assoc();

// Fetch logged-in user's language skills
$skills = [];
$res = $conn->query("SELECT language, score FROM user_skills WHERE user_id = $my_id");
while ($row = $res->fetch_assoc()) {
    $skills[$row['language']] = $row['score'];
}

// Ensure the 3 languages exist
$skill_labels = ['Python', 'Java', 'C/C++'];
$skill_scores = [];
foreach ($skill_labels as $lang) {
    $skill_scores[] = $skills[$lang] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SkillGram - Leaderboard</title>
<link rel="stylesheet" href="css/leaderboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.avatar-sm { width: 35px; height: 35px; border-radius: 50%; margin-right: 8px; vertical-align: middle; }
.my-rank { margin: 16px 0; font-weight: bold; font-size: 18px; }
.card { padding: 16px; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-top: 20px; }
.badge-card { text-align: center; margin-top: 20px; background: #fff; padding: 16px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.leaderboard { width: 100%; border-collapse: collapse; margin-top: 20px; }
.leaderboard th, .leaderboard td { padding: 12px 8px; text-align: left; border-bottom: 1px solid #eee; }
.leaderboard tr.highlight { background: #f0f8ff; font-weight: bold; }
</style>
</head>
<body>
<main>
<div class="leaderboard-container">

<!-- Combined Table -->
<table class="leaderboard">
<thead>
<tr>
<th>Rank</th>
<th>User</th>
<th>Streak</th>
<th>XP</th>
<th>Problems Solved</th>
</tr>
</thead>
<tbody>
<?php $rank=1; foreach($users as $u): ?>
<tr class="<?= $u['id']==$my_id?'highlight':'' ?>">
<td>#<?= $rank ?><?= $rank==1?' 🥇':($rank==2?' 🥈':($rank==3?' 🥉':'')) ?></td>
<td><img src="uploads/<?= htmlspecialchars($u['profile_pic']??'default.jpg') ?>" class="avatar-sm"> <?= htmlspecialchars($u['name']) ?></td>
<td><?= $u['streak'] ?></td>
<td><?= $u['xp'] ?></td>
<td><?= $u['problems_solved'] ?></td>
</tr>
<?php $rank++; endforeach; ?>
</tbody>
</table>

<!-- Bar Chart -->
<div class="card">
<canvas id="barChart"></canvas>
</div>

<!-- Radar Chart for Language Skills -->
<div class="card">
<h3>📝 Your Language Skills</h3>
<canvas id="skillsRadar"></canvas>
</div>

<!-- Top Badge -->
<div class="badge-card">
<img src="uploads/<?= htmlspecialchars($topBadgeUser['profile_pic']) ?>" class="avatar-sm" alt="User">
<h3>@<?= htmlspecialchars($topBadgeUser['name']) ?></h3>
<p>Earned <b><?= $topBadgeUser['badges'] ?></b> badges 🏆<br>
🔥 Your Streak Rank: <b>#<?= $myStreakRank ?></b><br>
⚡ Your XP Rank: <b>#<?= $myXPRank ?></b><br>
💻 Your Problems Solved Rank: <b>#<?= $myProblemsRank ?></b></p>
</div>

</div>
</main>

<script>
// Chart data
const users = <?= json_encode($users) ?>;
const labels = users.map(u=>u.name);
const streakData = users.map(u=>u.streak);
const xpData = users.map(u=>u.xp);
const problemsData = users.map(u=>u.problems_solved);

// Gradient for bar chart
function createGradient(ctx, colorStart, colorEnd) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    return gradient;
}

// Bar Chart - Grouped
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: labels,
       datasets: [
    { 
        label: 'Streak', 
        data: streakData, 
        backgroundColor: createGradient(barCtx, '#dfa5a5ff', '#510404ff'), 
        borderColor: '#610f0fff',
        borderWidth: 2,
        borderRadius: 6 
    },
    { 
        label: 'XP', 
        data: xpData, 
        backgroundColor: createGradient(barCtx, '#8aa8ceff', '#022547ff'), 
        borderColor: '#0c3858ff',
        borderWidth: 2,
        borderRadius: 6 
    },
    { 
        label: 'Problems Solved', 
        data: problemsData, 
        backgroundColor: createGradient(barCtx, '#a1d2a4ff', '#023a02ff'), 
        borderColor: '#0c5b0cff',
        borderWidth: 2,
        borderRadius: 6 
    }
]
    },
    options: {
        responsive:true,
        plugins:{ legend:{position:'bottom'} },
        scales: { x:{ stacked:false }, y:{ stacked:false, beginAtZero:true } }
    }
});

// Radar Chart - Language Skills
const radarCtx = document.getElementById('skillsRadar').getContext('2d');
new Chart(radarCtx, {
    type: 'radar',
    data: {
        labels: <?= json_encode($skill_labels) ?>,
        datasets: [{
            label: 'Skill Score',
            data: <?= json_encode($skill_scores) ?>,
            fill: true,
            backgroundColor: '#146f6d59',
            borderColor: '#146f69ff',
            pointBackgroundColor: '#051a3fff',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#5f9aff'
        }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{position:'bottom'} },
        scales: { r: { beginAtZero:true, max:100 } }
    }
});
</script>
</body>
</html>
