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

// Function to write JSON file
function writeJsonFile($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Verify user
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $users = readJsonFile('users.json');
    foreach ($users as &$user) {
        if ($user['verification_token'] === $token) {
            $user['verified'] = true;
            unset($user['verification_token']);
            writeJsonFile('users.json', $users);
            echo '<p>Email verified successfully. You can now log in.</p>';
            exit;
        }
    }
    echo '<p>Invalid verification token.</p>';
}
?>
