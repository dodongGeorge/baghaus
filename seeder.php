<?php
require_once 'db.php';

$is_cli = (php_sapi_name() === 'cli');
$line_break = $is_cli ? "\n" : "<br>";

echo "--- Sellerhub Database Seeder (Version 2.0) ---" . $line_break;

// 1. Mock Data with Categories
$categories = ['Electronics', 'Home & Garden', 'Fashion', 'Kitchen', 'Hobbies'];

$titles = [
    'Electronics' => ['Wireless Headphones', 'Portable Bluetooth Speaker', 'Mechanical Keyboard'],
    'Home & Garden' => ['Minimalist Wall Clock', 'Bamboo Organizer', 'Mid-Century Lamp'],
    'Fashion' => ['Leather Camera Bag', 'Organic Cotton Scarf', 'Vintage Sunglasses'],
    'Kitchen' => ['Professional Chef Knife', 'Hand-Poured Soy Candle', 'Espresso Tamper'],
    'Hobbies' => ['Acoustic Guitar Strings', 'Yoga Mat', 'Sketchbook Set']
];

$descriptions = [
    'Excellent quality and highly rated by the community.',
    'A must-have for anyone looking to upgrade their lifestyle.',
    'Sleek, modern, and built to last for years.',
    'Ethically sourced and carefully crafted with attention to detail.'
];

try {
    $userCheck = $pdo->query("SELECT id FROM users LIMIT 1");
    $user = $userCheck->fetch();

    if (!$user) {
        echo "Error: Register a user first so products have a seller." . $line_break;
        exit;
    }

    $seller_id = $user['id'];
    $sql = "INSERT INTO products (seller_id, title, description, price, stock_quantity, image_url, category) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    for ($i = 1; $i <= 20; $i++) {
        $cat   = $categories[array_rand($categories)];
        $title = $titles[$cat][array_rand($titles[$cat])] . " " . $i;
        $desc  = $descriptions[array_rand($descriptions)];
        $price = rand(10, 450) + (rand(0, 99) / 100);
        $stock = rand(1, 15);
        $img   = "https://picsum.photos/seed/" . rand(1, 1000) . "/500/400"; // Random unique images

        $stmt->execute([$seller_id, $title, $desc, $price, $stock, $img, $cat]);
    }

    echo "Successfully seeded 20 products across " . count($categories) . " categories!" . $line_break;
    echo "<a href='browse.php'>View Marketplace</a>";

} catch (PDOException $e) {
    echo "Seeding failed: " . $e->getMessage() . $line_break;
}
?>