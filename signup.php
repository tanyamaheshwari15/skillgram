<?php
include 'db.php'; // include the connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SkillGram - Sign Up</title>
  <link rel="stylesheet" href="css/signup.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <div class="signup-container">
    <h1 class="gradient-text">SkillGram</h1>
    <p>Create your account and start your skill journey 🚀</p>

    <h2><u>Sign Up</u></h2>
    <form method="POST" action="">
      <input type="text" name="name" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit" class="signup-btn">Sign Up</button>
    </form>

    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <div class="divider">or</div>
    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
         data-context="signup"
         data-ux_mode="popup"
         data-callback="handleGoogleCredential"
         data-auto_prompt="false">
    </div>
    <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="continue_with" data-shape="rectangular" data-logo_alignment="left"></div>

    <div class="login">
      Already have an account? <a href="index.php">Log in</a>.
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
