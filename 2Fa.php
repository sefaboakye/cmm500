<?php
// Start session
session_start();

// Function to read JSON file
function readJsonFile($filename) {
    if (file_exists($filename)) {
        $data = json_decode(file_get_contents($filename), true);
        return $data ? $data : [];
    }
    return [];
}

// Function to send 2FA code via email
function send2FACode($email, $code) {
    $subject = "Your 2FA Code";
    $message = "Your 2FA code is: " . $code;
    mail($email, $subject, $message);
}

if (!isset($_SESSION['2fa_username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['2fa_username'];
$users = readJsonFile('users.json');

// Generate and send 2FA code
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    foreach ($users as &$user) {
        if ($user['username'] == $username) {
            $user['2fa_code'] = rand(100000, 999999); // Generate a 6-digit code
            writeJsonFile('users.json', $users);
            send2FACode($user['email'], $user['2fa_code']);
            echo '<p>A 2FA code has been sent to your email address.</p>';
            break;
        }
    }
}

// Verify 2FA code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $code = $_POST['code'];
    foreach ($users as &$user) {
        if ($user['username'] == $username && $user['2fa_code'] == $code) {
            unset($_SESSION['2fa_username']);
            unset($user['2fa_code']);
            writeJsonFile('users.json', $users);
            $_SESSION['username'] = $username; // Login successful
            header("Location: listings.php");
            exit;
        }
    }
    echo '<p>Invalid 2FA code.</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification</title>
    <!-- Add your styles here -->
</head>
<body>
    <h2>2FA Verification</h2>
    <form method="post" action="">
        <label for="code">Enter 2FA Code:</label>
        <input type="text" id="code" name="code" required>
        <input type="submit" name="verify" value="Verify">
    </form>
</body>
</html>
