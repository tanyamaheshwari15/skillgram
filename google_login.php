<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

header('Content-Type: application/json');

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $idToken = $data['id_token'] ?? '';
    if (!$idToken) {
        echo json_encode(['status'=>'error','message'=>'Missing token']);
        exit;
    }

    // Verify token with Google
    $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $resp = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        echo json_encode(['status'=>'error','message'=>'Could not reach Google: '.$curlErr]);
        exit;
    }
    $info = json_decode($resp, true);
    if ($http !== 200) {
        $msg = isset($info['error_description']) ? $info['error_description'] : 'Token verification failed (HTTP '.$http.')';
        echo json_encode(['status'=>'error','message'=>$msg]);
        exit;
    }
    if (!isset($info['aud']) || !isset($info['email'])) {
        echo json_encode(['status'=>'error','message'=>'Invalid token payload']);
        exit;
    }

    // TODO: replace with your actual client ID
    $expectedAud = 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com';
    if ($expectedAud === 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com') {
        echo json_encode(['status'=>'error','message'=>'Server not configured: set your Google Client ID on server']);
        exit;
    }
    if ($info['aud'] !== $expectedAud) {
        echo json_encode(['status'=>'error','message'=>'Audience mismatch: token was issued for a different client']);
        exit;
    }

    $email = $info['email'];
    $name = $info['name'] ?? strtok($email, '@');

    // Find or create user
    $stmt = $conn->prepare('SELECT id FROM users WHERE email=?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($uid);
    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        $defaultPass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $ins = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?,?,?)');
        $ins->bind_param('sss', $name, $email, $defaultPass);
        $ins->execute();
        $uid = $ins->insert_id;
        $ins->close();
    }

    $_SESSION['user_id'] = (int)$uid;
    echo json_encode(['status'=>'ok']);
} catch (Throwable $e) {
    echo json_encode(['status'=>'error','message'=>'Server error']);
}
?>


