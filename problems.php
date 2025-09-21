<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$current_page = 'practice';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$force_type = $_GET['type'] ?? '';
$user_id = $_SESSION['user_id'];

$is_quiz = false;
$is_coding = false;

// --- Resolve type explicitly if provided ---
$quiz = null; $coding = null;
if ($force_type === 'quiz') {
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif ($force_type === 'coding') {
    $stmt = $conn->prepare("SELECT * FROM coding_challenges WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $coding = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    // Backward compatibility: try quiz first, then coding
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($quiz) {
    $is_quiz = true;

    // Quiz questions
    $stmt2 = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id=?");
    $stmt2->bind_param('i', $id);
    $stmt2->execute();
    $questions = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    // Previous attempts
    $stmt3 = $conn->prepare("SELECT * FROM quiz_attempts WHERE quiz_id=? AND user_id=?");
    $stmt3->bind_param('ii', $id, $user_id);
    $stmt3->execute();
    $prev_attempts = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();
} else {
    // --- Check if Coding Challenge ---
    if (!$coding) {
        $stmt = $conn->prepare("SELECT * FROM coding_challenges WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $coding = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($coding) {
        $is_coding = true;

        // Previous submissions
        $stmt4 = $conn->prepare("SELECT * FROM challenge_submissions WHERE challenge_id=? AND user_id=? ORDER BY submitted_at DESC");
        $stmt4->bind_param('ii', $id, $user_id);
        $stmt4->execute();
        $submissions = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt4->close();

        // Optional: fetch test cases if exists
        $stmt5 = $conn->prepare("SELECT * FROM challenge_tests WHERE challenge_id=?");
        $stmt5->bind_param('i', $id);
        $stmt5->execute();
        $testcases = $stmt5->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt5->close();
    } else {
        echo "No such quiz or coding challenge found.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($quiz['title'] ?? $coding['title']) ?> | SkillGram</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
body{font-family:'Poppins',sans-serif;background:#f5f6fa;margin:0;padding:0;}
.page{max-width:1100px;margin:20px auto;padding:0 16px;}
.card{background:#fff;border:1px solid #eee;border-radius:14px;padding:16px;box-shadow:0 6px 12px rgba(0,0,0,.04);margin-bottom:16px;}
.btn{background:#6286fb;color:#fff;border:none;padding:10px 14px;border-radius:10px;cursor:pointer;}
.badge{background:#eef3ff;color:#3b5bdb;padding:4px 10px;border-radius:999px;font-size:12px;}
.result{white-space:pre-wrap;background:#f7f7ff;border-radius:10px;padding:12px;border:1px solid #eaeaff;margin-top:16px;}
label{display:block;margin-bottom:6px;}
textarea,input,select{width:100%;padding:8px;margin:6px 0;border:1px solid #ddd;border-radius:8px;font-family:monospace;}
.prev-sub{background:#f1f1f1;padding:10px;border-radius:8px;margin-bottom:10px;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="page">

<?php if($is_quiz): ?>
    <h1><?= htmlspecialchars($quiz['title']) ?> <span class="badge"><?= htmlspecialchars($quiz['difficulty']) ?></span></h1>
    <p><strong>Topic:</strong> <?= htmlspecialchars($quiz['topic']) ?></p>

    <form id="quizForm">
    <?php foreach($questions as $i => $q): ?>
      <div class="card">
        <h4>Q<?= $i+1 ?>. <?= htmlspecialchars($q['question']) ?></h4>
        <label><input type="radio" name="q<?= $q['id'] ?>" value="A"> <?= htmlspecialchars($q['option_a']) ?></label>
        <label><input type="radio" name="q<?= $q['id'] ?>" value="B"> <?= htmlspecialchars($q['option_b']) ?></label>
        <label><input type="radio" name="q<?= $q['id'] ?>" value="C"> <?= htmlspecialchars($q['option_c']) ?></label>
        <label><input type="radio" name="q<?= $q['id'] ?>" value="D"> <?= htmlspecialchars($q['option_d']) ?></label>
      </div>
    <?php endforeach; ?>
      <button class="btn" type="submit">Submit Answers</button>
    </form>
    <div id="result" class="result">Your score will appear here…</div>

    <script>
    document.getElementById('quizForm').addEventListener('submit', e => {
      e.preventDefault();
      const form = e.target;
      let score = 0;
      let total = <?= count($questions) ?>;
      let details = "";
      let answers = {};

      <?php foreach($questions as $q): ?>
        const ans<?= $q['id'] ?> = form['q<?= $q['id'] ?>'].value;
        answers[<?= $q['id'] ?>] = ans<?= $q['id'] ?>;
        if(ans<?= $q['id'] ?> === "<?= $q['correct_option'] ?>") {
          score++;
          details += "Q<?= $q['id'] ?>: ✅ Correct\n";
        } else {
          details += "Q<?= $q['id'] ?>: ❌ Wrong (Correct: <?= $q['correct_option'] ?>)\n";
        }
      <?php endforeach; ?>

      document.getElementById('result').textContent = `You scored ${score} / ${total}\n\n${details}`;

      fetch('save_quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ quiz_id: <?= $quiz['id'] ?>, answers: answers })
      });
    });
    </script>

<?php elseif($is_coding): ?>
    <h1><?= htmlspecialchars($coding['title']) ?> <span class="badge"><?= htmlspecialchars($coding['difficulty']) ?></span></h1>
    <p><?= nl2br(htmlspecialchars($coding['description'])) ?></p>

    <div class="card">
        <h4>Sample Input</h4>
        <pre><?= htmlspecialchars($coding['sample_input']) ?></pre>
    </div>
    <div class="card">
        <h4>Expected Output</h4>
        <pre><?= htmlspecialchars($coding['sample_output']) ?></pre>
    </div>

    <form id="codeForm">
      <textarea id="userCode" name="code" rows="10" placeholder="Write your code here..."></textarea>
      <input type="hidden" name="challenge_id" value="<?= $coding['id'] ?>">
      <select name="language">
        <option value="Python">Python</option>
        <option value="C++">C++</option>
        <option value="Java">Java</option>
      </select>
      <button type="submit" class="btn">Run & Submit</button>
    </form>

    <h3>Previous Submissions</h3>
    <?php foreach($submissions as $sub): ?>
      <div class="prev-sub">
        <strong><?= htmlspecialchars($sub['language']) ?></strong> - <?= htmlspecialchars($sub['status']) ?> <br>
        <small><?= htmlspecialchars($sub['submitted_at']) ?></small>
        <pre><?= htmlspecialchars($sub['code']) ?></pre>
      </div>
    <?php endforeach; ?>

    <pre id="codeResult" class="result"></pre>

    <script>
    document.getElementById('codeForm').addEventListener('submit', async e => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const res = await fetch('submit_code.php', { method:'POST', body:formData });
      const data = await res.json();
      document.getElementById('codeResult').textContent = data.message;
    });
    </script>

<?php endif; ?>

</main>
</body>
</html>
