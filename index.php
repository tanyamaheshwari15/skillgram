<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php'; // include the connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, last_login, streak FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $last_login, $streak);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;

            // --- Handle streak update ---
            $today = date("Y-m-d");
            if ($last_login === date("Y-m-d", strtotime("-1 day"))) {
                // Continue streak
                $streak++;
            } elseif ($last_login !== $today) {
                // Reset streak if missed
                $streak = 1;
            }

            // ✅ Update streak before checking achievements
            $update = $conn->prepare("UPDATE users SET streak=?, last_login=? WHERE id=?");
            $update->bind_param("isi", $streak, $today, $id);
            $update->execute();
            $update->close();

            // ✅ Now call achievements check with updated streak
            include 'check_achievements.php';
            checkAchievements($conn, $id);

            header("Location: home.php"); // redirect to home page
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SkillGram - Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <h1 class="gradient-text">SkillGram</h1>
    <p>Turn Distractions Into Daily Skill Progress</p>

    <h2><u>Log In</u></h2>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <a href="#" class="forgot">Forgot password?</a>
      <button type="submit" class="login-btn">Log In</button>
    </form>

    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <div class="divider">or</div>
    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
         data-context="signin"
         data-ux_mode="popup"
         data-callback="handleGoogleCredential"
         data-auto_prompt="false">
    </div>
    <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="continue_with" data-shape="rectangular" data-logo_alignment="left"></div>

    <div class="signup">
      Don't have an account? <a href="signup.php">Sign up</a>.
    </div>
  </div>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script>
    async function handleGoogleCredential(response) {
      try {
        const res = await fetch('google_login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_token: response.credential })
        });
        const data = await res.json();
        if (data.status === 'ok') {
          window.location.href = 'home.php';
        } else {
          alert(data.message || 'Google sign-in failed');
        }
      } catch (e) {
        alert('Network error during Google sign-in');
      }
    }
  </script>
</body>
</html>
