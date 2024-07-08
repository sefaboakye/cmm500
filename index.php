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

// Function to write JSON file
function writeJsonFile($filename, $data)
{
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Initialize properties.json and users.json if they do not exist or are empty
if (!file_exists('properties.json') || !filesize('properties.json')) {
    writeJsonFile('properties.json', []);
}

if (!file_exists('users.json') || !filesize('users.json')) {
    writeJsonFile('users.json', []);
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Add new user to file
    $users = readJsonFile('users.json');
    $users[] = ['username' => $newUsername, 'password' => $newPassword];
    writeJsonFile('users.json', $users);

    echo '<p>Registration successful. You can now login.</p>';
}

// Handle user login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials
    $users = readJsonFile('users.json');
    $loggedIn = false;
    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username; // Set session variable
            $loggedIn = true;
            break;
        }
    }

    if ($loggedIn) {
        echo '<p>Login successful.</p>';
    } else {
        echo '<p>Invalid username or password.</p>';
    }
}

// Handle user logout
if (isset($_POST['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    echo '<p>Logged out successfully.</p>';
}

// Handle property deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && isset($_SESSION['username'])) {
    $propertyIndex = $_POST['property_index'];
    $properties = readJsonFile('properties.json');
    if (isset($properties[$propertyIndex]) && $properties[$propertyIndex]['username'] === $_SESSION['username']) {
        array_splice($properties, $propertyIndex, 1);
        writeJsonFile('properties.json', $properties);
        echo '<p>Property deleted successfully.</p>';
    } else {
        echo '<p>You do not have permission to delete this property.</p>';
    }
}

// Handle property update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_SESSION['username'])) {
    $propertyIndex = $_POST['property_index'];
    $properties = readJsonFile('properties.json');
    if (isset($properties[$propertyIndex]) && $properties[$propertyIndex]['username'] === $_SESSION['username']) {
        $properties[$propertyIndex]['title'] = $_POST['property_type'];
        $properties[$propertyIndex]['price'] = $_POST['price'];
        $properties[$propertyIndex]['location'] = $_POST['location'];
        $properties[$propertyIndex]['bedrooms'] = $_POST['bedrooms'];
        $properties[$propertyIndex]['bathrooms'] = $_POST['bathrooms'];
        $properties[$propertyIndex]['kitchens'] = $_POST['kitchens'];

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false && $_FILES["image"]["size"] <= 500000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $properties[$propertyIndex]['image'] = basename($_FILES["image"]["name"]);
                }
            }
        }

        writeJsonFile('properties.json', $properties);
        echo '<p>Property updated successfully.</p>';
    } else {
        echo '<p>You do not have permission to update this property.</p>';
    }
}

// Handle property submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && isset($_SESSION['username'])) {
    $propertyType = $_POST['property_type'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $kitchens = $_POST['kitchens'];

    // Image upload handling
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Add property to file
    $properties = readJsonFile('properties.json');
    $properties[] = [
        'username' => $_SESSION['username'],
        'title' => $propertyType,
        'location' => $location,
        'price' => $price,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'kitchens' => $kitchens,
        'image' => $_FILES["image"]["name"]
    ];
    writeJsonFile('properties.json', $properties);

    // Redirect to listings page
    header("Location: listings.php");
    exit;
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

        /* Carousel styles */
        .carousel {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .carousel img {
            width: 300px;
            height: 300px;
            margin: 10px;
            scroll-snap-align: start;
            transition: transform 0.3s;
        }

        .carousel img:hover {
            transform: scale(1.05);
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

    <section>
        <h2 style="font-style: italic;color: tomato">Welcome to Prime Housing</h2>
        <p style="font-style: italic;">Your ultimate destination for finding your dream home.</p>

        <div class="carousel">

            <img src="images/hse5.jpeg" alt="House 5">
            <img src="images/hse6.jpeg" alt="House 6">
            <img src="images/hse7.jpeg" alt="House 7">
            <img src="images/hse8.jpeg" alt="House 8">
            <img src="images/hse5.jpeg" alt="House 9">
            <img src="images/hse6.jpeg" alt="House 10">
            <img src="images/hse7.jpeg" alt="House 11">
            <img src="images/hse8.jpeg" alt="House 12">
        </div>
    </section>

    <section>
        <h2>Featured Listings</h2>
        <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse6.jpeg" alt="House 1" style="width:100%;max-width:300px;">
                <p><strong>3 Bedroom House</strong></p>
                <p>Price: $300,000</p>
                <p>Location: New York, NY</p>
            </div>
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse5.jpeg" alt="House 2" style="width:100%;max-width:300px;">
                <p><strong>2 Bedroom Apartment</strong></p>
                <p>Price: $200,000</p>
                <p>Location: Los Angeles, CA</p>
            </div>
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse4.jpeg" alt="House 3" style="width:100%;max-width:300px;">
                <p><strong>4 Bedroom Villa</strong></p>
                <p>Price: $500,000</p>
                <p>Location: Miami, FL</p>
            </div>
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse7.jpeg" alt="House 6" style="width:100%;max-width:300px;">
                <p><strong>3 Bedroom Villa</strong></p>
                <p>Price: $700,000</p>
                <p>Location: Glasgow, UK</p>
            </div>
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse8.jpeg" alt="House 6" style="width:100%;max-width:300px;">
                <p><strong>3 Bedroom Villa</strong></p>
                <p>Price: $600,000</p>
                <p>Location: Dundee, UK</p>
            </div>
            <div style="flex: 1; min-width: 300px; margin: 10px;">
                <img src="images/hse9.jpeg" alt="House 6" style="width:100%;max-width:300px;">
                <p><strong>4 Bedroom Villa</strong></p>
                <p>Price: $400,000</p>
                <p>Location: London, UK</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y-m-d H:i:s'); ?> Prime Housing. Your dream is our reality.</p>
    </footer>
</body>

</html>