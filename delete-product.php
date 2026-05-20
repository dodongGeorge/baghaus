<?php
/**
 * SELLERHUB DELETE PRODUCT
 * Handles deletion of a seller's own listing
 */
require_once 'db.php';
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id    = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: my-listings.php");
    exit;
}

try {
    // Verify the product belongs to the logged-in user before deleting
    $check = $pdo->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
    $check->execute([$product_id, $user_id]);

    if (!$check->fetch()) {
        // Product doesn't exist or doesn't belong to this user
        header("Location: my-listings.php?error=unauthorized");
        exit;
    }

    // Safe to delete
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$product_id, $user_id]);

    header("Location: my-listings.php?deleted=1");
    exit;

} catch (PDOException $e) {
    header("Location: my-listings.php?error=db");
    exit;
}
?>
