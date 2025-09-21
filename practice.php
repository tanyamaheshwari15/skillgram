<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$current_page = 'practice';
$user_id = $_SESSION['user_id'];
$selected_diff = $_GET['difficulty'] ?? '';
$selected_topic = $_GET['topic'] ?? '';
$selected_lang = $_GET['lang'] ?? '';

// Map UI language button to internal label
$lang_label = '';
if ($selected_lang === 'python') $lang_label = 'Python';
if ($selected_lang === 'cpp') $lang_label = 'C++';
if ($selected_lang === 'java') $lang_label = 'Java';

// Fetch Coding Challenges
$cc_sql = "SELECT id, title, difficulty, created_at FROM coding_challenges WHERE 1=1";
$cc_params = [];
$cc_types = '';

if ($selected_diff) {
    $cc_sql .= " AND difficulty=?";
    $cc_params[] = $selected_diff;
    $cc_types .= 's';
}

if ($lang_label) {
    // Filter by language hint in title (e.g., "[Python]", "Python")
    $cc_sql .= " AND title LIKE ?";
    $cc_params[] = '%'.$lang_label.'%';
    $cc_types .= 's';
}

// Coding Challenges statement
$cc_stmt = $conn->prepare($cc_sql);
if ($cc_params) $cc_stmt->bind_param($cc_types, ...$cc_params);
$cc_stmt->execute();
$cc_result = $cc_stmt->get_result();
$coding_challenges = $cc_result->fetch_all(MYSQLI_ASSOC);
$cc_stmt->close();

// Fetch MCQ Quizzes
$q_sql = "SELECT q.id, q.title, q.topic, u.name AS created_by
          FROM quizzes q
          LEFT JOIN users u ON q.created_by=u.id
          WHERE 1=1";
$q_params = [];
$q_types = '';

if ($selected_topic) {
    $q_sql .= " AND q.topic=?";
    $q_params[] = $selected_topic;
    $q_types .= 's';
}

if ($lang_label) {
    // Filter quizzes by topic or title matching selected language
    $q_sql .= " AND (q.topic = ? OR q.title LIKE ?)";
    $q_params[] = $lang_label;
    $q_params[] = '%'.$lang_label.'%';
    $q_types .= 'ss';
}

$q_stmt = $conn->prepare($q_sql);
if ($q_params) $q_stmt->bind_param($q_types, ...$q_params);
$q_stmt->execute();
$q_result = $q_stmt->get_result();
$quizzes = $q_result->fetch_all(MYSQLI_ASSOC);
$q_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Practice | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f5f6fa;margin:0;padding:0;}
.page{max-width:1100px;margin:80px auto 20px;padding:0 16px}
.table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 6px 12px rgba(0,0,0,.05);} 
.table th,.table td{padding:12px;border-bottom:1px solid #eee;text-align:left;}
.table th{background:#f8f9fa;}
.badge{background:#eafcf1;color:#15803d;padding:4px 10px;border-radius:999px;font-size:12px;}
.filters{display:flex;gap:10px;margin:10px 0 16px;flex-wrap:wrap;}
select{padding:8px;border:1px solid #ddd;border-radius:8px;}
.link{color:#6286fb;text-decoration:none;}
.link:hover{text-decoration:underline;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page">
  <h1>🧩 Practice</h1>

<div class="filters">
  <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
    <!-- Language buttons -->
    <input type="hidden" name="difficulty" value="<?= htmlspecialchars($selected_diff) ?>">
    <input type="hidden" name="topic" value="<?= htmlspecialchars($selected_topic) ?>">
    <button type="submit" name="lang" value="python" style="padding:8px 12px;border:1px solid #ddd;border-radius:999px;background:<?= $selected_lang==='python'?'#6286fb':'#fff' ?>;color:<?= $selected_lang==='python'?'#fff':'#333' ?>;cursor:pointer;">Python</button>
    <button type="submit" name="lang" value="cpp" style="padding:8px 12px;border:1px solid #ddd;border-radius:999px;background:<?= $selected_lang==='cpp'?'#6286fb':'#fff' ?>;color:<?= $selected_lang==='cpp'?'#fff':'#333' ?>;cursor:pointer;">C/C++</button>
    <button type="submit" name="lang" value="java" style="padding:8px 12px;border:1px solid #ddd;border-radius:999px;background:<?= $selected_lang==='java'?'#6286fb':'#fff' ?>;color:<?= $selected_lang==='java'?'#fff':'#333' ?>;cursor:pointer;">Java</button>
    <!-- Difficulty filter -->
    <select name="difficulty" onchange="this.form.submit()">
      <option value="">All difficulties</option>
      <?php
        $diffs = ['Easy','Medium','Hard'];
        foreach($diffs as $d) echo "<option ".($selected_diff===$d?'selected':'').">$d</option>";
      ?>
    </select>

    <!-- Topic filter -->
    <select name="topic" onchange="this.form.submit()">
      <option value="">All topics</option>
      <?php
        $topic_result = $conn->query("SELECT DISTINCT topic FROM quizzes");
        while($row = $topic_result->fetch_assoc()){
          $t = $row['topic'];
          echo "<option ".($selected_topic===$t?'selected':'').">$t</option>";
        }
      ?>
    </select>
  </form>
</div>

<?php if($selected_lang): ?>
<h2>Coding Challenges</h2>
<table class="table">
<thead>
<tr><th>#</th><th>Title</th><th>Difficulty</th><th>Created At</th><th></th></tr>
</thead>
<tbody>
<?php if(count($coding_challenges)):
foreach($coding_challenges as $i=>$c): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($c['title']) ?></td>
<td><span class="badge"><?= htmlspecialchars($c['difficulty']) ?></span></td>
<td><?= htmlspecialchars($c['created_at']) ?></td>
<td><a class="link" href="problems.php?type=coding&id=<?= (int)$c['id'] ?>">Start →</a></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5">No coding challenges found.</td></tr>
<?php endif; ?>
</tbody>
</table>

<h2>MCQ Quizzes</h2>
<table class="table">
<thead>
<tr><th>#</th><th>Title</th><th>Topic</th><th>Created By</th><th></th></tr>
</thead>
<tbody>
<?php if(count($quizzes)):
foreach($quizzes as $i=>$q): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($q['title']) ?></td>
<td><?= htmlspecialchars($q['topic']) ?></td>
<td><?= htmlspecialchars($q['created_by']) ?></td>
<td><a class="link" href="problems.php?type=quiz&id=<?= (int)$q['id'] ?>">Start →</a></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5">No quizzes found.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php else: ?>
<p>Please select a language above to view questions.</p>
<?php endif; ?>
</main>
</body>
</html>
