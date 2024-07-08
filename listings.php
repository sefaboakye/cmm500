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

// Fetch properties from file
$properties = readJsonFile('properties.json');

// Handle property deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && isset($_SESSION['username'])) {
    $propertyIndex = $_POST['property_index'];
    if (isset($properties[$propertyIndex]) && $properties[$propertyIndex]['username'] === $_SESSION['username']) {
        array_splice($properties, $propertyIndex, 1);
        writeJsonFile('properties.json', $properties);
        $_SESSION['feedback'] = "Property deleted successfully.";
        header("Location: listings.php"); // Refresh to reflect changes
        exit;
    }
}

// Handle property update (this part should redirect to an update form or similar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_SESSION['username'])) {
    $propertyIndex = $_POST['property_index'];
    // Here you should redirect to an update form or similar
    header("Location: update_property.php?index=$propertyIndex");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings - Prime Housing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: blue;
            margin: 0;
            padding: 0;
            line-height: 1.6;
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

        header {
            background-image: url('images/hse1.jpeg');
            background-size: cover;
            background-position: center;
            color: tomato;
            text-align: left;
            padding: 60px 20px;
        }

        footer {
            background-color: skyblue;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        section {
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .property {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .feedback {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
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

    <section id="buyers">
        <h2>Available Properties</h2>
        <!-- Display user feedback -->
        <?php
        if (isset($_SESSION['feedback'])) {
            echo '<div class="feedback">' . $_SESSION['feedback'] . '</div>';
            unset($_SESSION['feedback']); // Clear feedback after displaying
        }
        ?>
        <!-- PHP code for displaying properties -->
        <?php
        // Display properties
        foreach ($properties as $index => $property) {
            echo '<div class="property">';
            echo '<h3>' . $property['title'] . '</h3>';
            echo '<p><strong>Price:</strong> ' . $property['price'] . '</p>';
            echo '<p><strong>Location:</strong> ' . $property['location'] . '</p>';
            echo '<p><strong>Bedrooms:</strong> ' . $property['bedrooms'] . '</p>';
            echo '<p><strong>Bathrooms:</strong> ' . $property['bathrooms'] . '</p>';
            echo '<p><strong>Kitchens:</strong> ' . $property['kitchens'] . '</p>';
            echo '<p><strong>Listed by:</strong> <a href="profile.php?username=' . urlencode($property['username']) . '">' . htmlspecialchars($property['username']) . '</a></p>';
            echo '<img src="images/' . $property['image'] . '" alt="' . $property['title'] . '" style="width: 100%;">';

            // Show delete and update buttons if the user is the owner of the property
            if (isset($_SESSION['username']) && $_SESSION['username'] === $property['username']) {
                echo '<form method="post" action="" style="display:inline-block;">';
                echo '<input type="hidden" name="property_index" value="' . $index . '">';
                echo '<input type="submit" name="delete" value="Delete">';
                echo '</form>';

                echo '<form method="post" action="" style="display:inline-block;">';
                echo '<input type="hidden" name="property_index" value="' . $index . '">';
                echo '<input type="submit" name="update" value="Update">';
                echo '</form>';
            }
            echo '</div>';
        }
        ?>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Prime Housing.Your dream is our reality.</p>
    </footer>
</body>

</html>