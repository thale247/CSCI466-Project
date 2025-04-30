<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); 
?>
<!doctype html>
<html lang="en">
<?php 
include 'db_connect.php'; 
?>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/php/admin_index.php">Admin Home</a>
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/php/admin_index.php">Dashboard</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a class="nav-item nav-link" href="/php/inventory.php">Inventory</a>
                <a class="nav-item nav-link" href="/php/order_manager.php">Orders</a>
                <a class="nav-item nav-link" href="/php/sign_out.php">Sign Out</a>
            <?php else: ?>
                <a class="nav-item nav-link" href="/php/login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Welcome to the Admin Dashboard</h1>
        <p class="lead">Manage your store with ease using the tools below.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Inventory</h5>
                    <p class="card-text">Add, update, or delete product listings.</p>
                    <a href="/php/inventory.php" class="btn btn-light">Manage Inventory</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text">Review and process customer orders.</p>
                    <a href="/php/order_manager.php" class="btn btn-light">View Orders</a>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
