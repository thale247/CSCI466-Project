<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); ?>
<!doctype html>
<html lang="en">
<?php 
include 'db_connect.php'; 
?>
<head>
    <meta charset="UTF-8">
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/php/admin_index.php">Admin Home</a>
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/php/admin_index.php">Admin Home</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a class="nav-item nav-link" href="/php/inventory.php">Inventory</a>
                <a class="nav-item nav-link" href="/php/order_manager.php">Manage Orders</a>
                <a class="nav-item nav-link" href="/php/sign_out.php">Sign Out</a>
            <?php else: ?>
                <a class="nav-item nav-link" href="/php/login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="container mt-4">
    <div class="row">

    </div>
</main>
</body>
</html>
