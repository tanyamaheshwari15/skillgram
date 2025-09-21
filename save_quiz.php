<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$quiz_id = (int)$data['quiz_id'];
$answers = $data['answers'] ?? [];

// --- Calculate score ---
$stmt = $conn->prepare("SELECT id, correct_option FROM quiz_questions WHERE quiz_id=?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$res = $stmt->get_result();
$total = $res->num_rows;
$score = 0;

while($row = $res->fetch_assoc()){
    if(isset($answers[$row['id']]) && $answers[$row['id']] === $row['correct_option']){
        $score++;
    }
}
$stmt->close();

// --- Save attempt ---
$stmt2 = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, score, attempted_at) VALUES (?, ?, ?, NOW())");
$stmt2->bind_param("iii", $quiz_id, $user_id, $score);
$stmt2->execute();
$stmt2->close();

// --- Update XP and problems solved ---
$xp_gain = $score * 10;   // 10 XP per correct answer
$problems_gain = $score;  // +1 problem solved per correct answer

$conn->query("UPDATE users 
              SET xp = xp + $xp_gain, 
                  problems_solved = problems_solved + $problems_gain 
              WHERE id=$user_id");

// --- Check for achievements ---
include 'check_achievements.php';
checkAchievements($conn, $user_id);

// --- Return response ---
echo json_encode([
    'status'=>'success',
    'score'=>$score,
    'total'=>$total,
    'xp_gain'=>$xp_gain
]);
