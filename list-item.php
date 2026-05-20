<?php
require_once 'db.php';
session_start();

// Security: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Categories array for the dropdown
$categories = ['Electronics', 'Home & Garden', 'Fashion', 'Kitchen', 'Hobbies'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_url = trim($_POST['image_url']);
    $category = $_POST['category'];

    if (!empty($title) && !empty($price) && !empty($category)) {
        try {
            $sql = "INSERT INTO products (seller_id, title, description, price, stock_quantity, image_url, category) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$current_user_id, $title, $description, $price, $stock, $image_url, $category]);
            
            $message = "Product listed successfully! <a href='browse.php'>View Listings</a>";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell an Item | Sellerhub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <a href="index.php" class="logo">Sellerhub</a>
    <nav>
        <a href="my-listings.php">My Listings</a>
    </nav>
</header>

<div class="container">
    <div class="form-card" style="max-width: 600px; margin: 0 auto;">
        <h1>List Your Product</h1>
        
        <?php if ($message): ?>
            <div class="alert" style="background: <?php echo $message_type == 'success' ? '#d1fae5' : '#fee2e2'; ?>; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="list-item.php" method="POST">
            <div class="form-group">
                <label for="title">Product Title *</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="price">Price ($) *</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="stock">Quantity</label>
                    <input type="number" id="stock" name="stock" value="1">
                </div>
            </div>

            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="text" id="image_url" name="image_url">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <button type="submit" class="btn btn-sell" style="width: 100%;">Post Listing</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Sellerhub Inc. | <a href="about.php" style="color:white;">About Us</a></p>
</footer>

</body>
</html>