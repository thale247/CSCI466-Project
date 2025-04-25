<?php
$host = '192.168.86.28';
$dbname = 'z1986037'; // FILL THESE IN FOR YOUR SERVER
$username = 'thomas';
$password = 'ILikeCheese247!'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
