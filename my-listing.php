<?php
require_once 'db.php';
session_start();

// 1. Security Check: Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$message = "";

// 2. Handle Deletion
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    try {
        // Ensure the product belongs to the user before deleting
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$delete_id, $current_user_id]);
        $message = "Listing deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error deleting listing: " . $e->getMessage();
    }
}

// 3. Fetch only this user's products
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$current_user_id]);
    $my_products = $stmt->fetchAll();
} catch (PDOException $e) {
    $my_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings | Sellerhub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Table-specific styling not covered in global CSS */
        .prod-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            background: #eee;
        }
        .action-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn-text-edit { color: var(--primary-color); text-decoration: none; font-weight: 600; }
        .btn-text-view { color: var(--gray-text); text-decoration: none; font-weight: 500; }
        .btn-text-delete { 
            background: none; 
            border: none; 
            color: var(--danger-color); 
            cursor: pointer; 
            font-weight: 600; 
            padding: 0; 
            font-family: inherit;
        }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="logo">Sellerhub</a>
    <nav>
        <span class="user-welcome">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Your Inventory</h1>
        <a href="list-item.php" class="btn btn-sell">+ List New Item</a>
    </div>

    <?php if ($message): ?>
        <div class="alert" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (count($my_products) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Product Details</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/60?text=None'; ?>" class="prod-img">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($product['title']); ?></strong><br>
                                <small style="color: #999;">Listed on <?php echo date('M d, Y', strtotime($product['created_at'])); ?></small>
                            </td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock_quantity']; ?></td>
                            <td class="action-links">
                                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-text-view">View</a>
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-text-edit">Edit</a>
                                
                                <form method="POST" onsubmit="return confirm('Delete this listing permanently?');" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn-text-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="form-card" style="text-align: center; padding: 4rem;">
            <h3>You don't have any active listings.</h3>
            <p>Ready to start selling on Sellerhub?</p>
            <br>
            <a href="list-item.php" class="btn btn-sell">Create Your First Listing</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Sellerhub Inc. - All Seller Controls Active</p>
</footer>

</body>
</html>