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

// Fetch properties from file
$properties = readJsonFile('properties.json');

// Check if the user is logged in and the property index is provided
if (!isset($_SESSION['username']) || !isset($_GET['index'])) {
    header("Location: listings.php");
    exit;
}

$propertyIndex = $_GET['index'];
$property = $properties[$propertyIndex] ?? null;

// Check if the property exists and belongs to the user
if (!$property || $property['username'] !== $_SESSION['username']) {
    header("Location: listings.php");
    exit;
}

// Handle property update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $properties[$propertyIndex]['title'] = $_POST['title'];
    $properties[$propertyIndex]['price'] = $_POST['price'];
    $properties[$propertyIndex]['location'] = $_POST['location'];
    $properties[$propertyIndex]['bedrooms'] = $_POST['bedrooms'];
    $properties[$propertyIndex]['bathrooms'] = $_POST['bathrooms'];
    $properties[$propertyIndex]['kitchens'] = $_POST['kitchens'];
    
    // Optional: handle image update
    if (!empty($_FILES['image']['name'])) {
        $imagePath = 'images/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $properties[$propertyIndex]['image'] = basename($_FILES['image']['name']);
    }

    writeJsonFile('properties.json', $properties);
    $_SESSION['feedback'] = "Property updated successfully.";
    header("Location: listings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Property - Prime Housing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        header {
            background-color: tan;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        section {
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: tan;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Update Property</h1>
    </header>

    <section>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" value="<?php echo htmlspecialchars($property['bedrooms']); ?>" required>
            </div>
            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" value="<?php echo htmlspecialchars($property['bathrooms']); ?>" required>
            </div>
            <div class="form-group">
                <label for="kitchens">Kitchens</label>
                <input type="number" id="kitchens" name="kitchens" value="<?php echo htmlspecialchars($property['kitchens']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image">
                <img src="images/<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" style="width: 100px; height: auto;">
            </div>
            <input type="submit" value="Update Property">
        </form>
    </section>
</body>
</html>
