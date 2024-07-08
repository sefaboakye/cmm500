<?php
// Start session
session_start();

// Function to read JSON file
function readJsonFile($filename)
{
    if (file_exists($filename)) {
        $data = json_decode(file_get_contents($filename), true);
        return $data ? $data : [];
    }
    return [];
}

// Handle user login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials
    $users = readJsonFile('users.json');
    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            if (isset($user['verified']) && $user['verified']) {
                // User is verified, proceed to 2FA
                $_SESSION['2fa_username'] = $username;
                header("Location: 2fa.php");
                exit;
            } else {
                echo '<p>Your email address is not verified. Please check your email.</p>';
            }
            exit;
        }
    }
    echo '<p>Invalid username or password.</p>';
}
?>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"],
        select,
        textarea {
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

    </section>

    <!-- Login Form -->
    <section>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" name="login" value="Login">
        </form>
    </section>

    <!-- Property Submission Form -->
    <?php if (isset($_SESSION['username'])): ?>
        <section>
            <h2>Submit Property</h2>
            <form method="post" enctype="multipart/form-data" action="">
                <label for="property_type">Property Type:</label>
                <input type="text" id="property_type" name="property_type" required>
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required><br>
                <label for="price">Price (USD $):</label>
                <input type="text" id="price" name="price" required>
                <label for="bedrooms">Bedrooms:</label>
                <input type="text" id="bedrooms" name="bedrooms" required><br>
                <label for="bathrooms">Bathrooms:</label>
                <input type="text" id="bathrooms" name="bathrooms" required>
                <label for="kitchens">Kitchens:</label>
                <input type="text" id="kitchens" name="kitchens" required><br>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" required>
                <input type="submit" name="submit" value="Submit Property">
            </form>

            <!-- Logout Form -->
            <form method="post" action="">
                <input type="submit" name="logout" value="Logout">
            </form>
        </section>
    <?php endif; ?>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Prime Housing. Your dream is our reality.</p>
    </footer>
</body>

</html>
