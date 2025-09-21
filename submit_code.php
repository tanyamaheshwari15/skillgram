<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$challenge_id = (int)$_POST['challenge_id'];
$code = $_POST['code'] ?? '';
$language = $_POST['language'] ?? 'Python';

// Insert submission
$stmt = $conn->prepare("INSERT INTO challenge_submissions (challenge_id, user_id, code, language, status, submitted_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("iiss", $challenge_id, $user_id, $code, $language);
$stmt->execute();
$stmt->close();

// --- Update XP + problems_solved first ---
$xp_gain = 20; 
$conn->query("UPDATE users 
              SET xp = xp + $xp_gain, 
                  problems_solved = problems_solved + 1 
              WHERE id=$user_id");

// --- Now check achievements ---
include 'check_achievements.php';
checkAchievements($conn, $user_id);

echo json_encode(['message'=>"Code submitted successfully! Status: Pending"]);
