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

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $subject = "Verify Your Email Address";
    $message = "Please click the link below to verify your email address:\n\n";
    $message .= "http://yourwebsite.com/verify.php?token=" . $token;
    mail($email, $subject, $message);
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $verificationToken = bin2hex(random_bytes(16)); // Generate a random token

    // Add new user to file
    $users = readJsonFile('users.json');
    $users[] = [
        'username' => $newUsername,
        'password' => $newPassword,
        'email' => $email,
        'verified' => false,
        'verification_token' => $verificationToken
    ];
    writeJsonFile('users.json', $users);

    // Send verification email
    sendVerificationEmail($email, $verificationToken);

    echo '<p>Registration successful. A verification email has been sent to your email address.</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Add your styles here -->
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Housing</title>
    <style>
        /* Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: blue;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        header {
            background-image: url('images/hse1.jpeg');
            background-size: cover;
            background-position: center;
            color: tomato;
            text-align: left;
            padding: 60px 20px;
        }
        nav {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
        }
        nav a:hover {
            background-color: #555;
        }
        footer {
            background-color: skyblue;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        section {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"], select, textarea {
            width: 35%;
            align-content: center;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        input[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Prime Housing</h1>
    </header>
    <nav>
    <p style="text-align=left"> <?php echo date(' H:i d-m-Y '); ?></p>
        <a href="index.php">Home</a>
        <a href="listings.php">Listings</a>
        <a href="register.php">Registration</a>
        <a href="login.php">Login</a>
    </nav>

    <!-- Registration Form -->
    <section>
    <h2>Register</h2>
    <form method="post" action="">
        <label for="new_username">Username:</label>
        <input type="text" id="new_username" name="new_username" required>
        <label for="new_password">Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <input type="submit" name="register" value="Register">
        </form>
    </section>

   

 

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Prime Housing. Your dream is our reality.</p>
    </footer>
</body>
</html>


