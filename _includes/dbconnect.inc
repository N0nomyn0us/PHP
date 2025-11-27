<?php
$host = 'localhost';
$db   = 'cw2_students';  // Change if you used a different DB name
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';
$port = 3307;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
