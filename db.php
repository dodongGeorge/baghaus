<?php
// Database Configuration
$host     = 'localhost';
$db_name  = 'sellerhub_db';
$username = 'root'; // Default for XAMPP/MAMP is 'root'
$password = '';     // Default for XAMPP is empty, MAMP is 'root'
$charset  = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// Options for PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create the connection
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // If connection fails, stop and show error
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>