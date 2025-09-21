<?php
function checkAchievements($conn, $user_id) {
    // Get current stats
    $stmt = $conn->prepare("SELECT xp, streak, problems_solved FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Normalize keys
    $normalized = [
        'xp' => (int)$stats['xp'],
        'streak' => (int)$stats['streak'],
        'problems_solved' => (int)$stats['problems_solved']
    ];

    // Get all achievements
    $achievements = $conn->query("SELECT * FROM achievements");
    while ($a = $achievements->fetch_assoc()) {
        $type  = strtolower(trim($a['condition_type']));  
        $value = (int)$a['condition_value'];

        // ✅ Unlock if user stat meets or exceeds requirement
        if (isset($normalized[$type]) && $normalized[$type] >= $value) {
            // Check if already earned
            $check = $conn->prepare("SELECT id FROM user_achievements WHERE user_id=? AND achievement_id=?");
            $check->bind_param("ii", $user_id, $a['id']);
            $check->execute();
            $check->store_result();

            if ($check->num_rows == 0) {
                // Insert achievement
                $ins = $conn->prepare(
                    "INSERT INTO user_achievements (user_id, achievement_id, earned_at) VALUES (?, ?, NOW())"
                );
                $ins->bind_param("ii", $user_id, $a['id']);
                $ins->execute();
                $ins->close();

                error_log("🏆 Achievement unlocked for user $user_id: ".$a['badge_name']);
            }
            $check->close();
        }
    }

    error_log("Checking achievements for user $user_id with streak ".$normalized['streak']);
}
?>
