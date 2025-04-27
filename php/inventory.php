<?php 
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT `Name`, `Price`, `In_Stock` FROM `Product`");
$stmt->execute();
$items = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
        crossorigin="anonymous">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">Admin Home</a>
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

    <main class="container mt-5">
        <h1 class="mb-4">Inventory</h1>

        <?php if (count($items) === 0): ?>
            <div class="alert alert-info">Inventory is currently empty.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Current Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['Name']) ?></td>
                            <td>$<?= number_format($item['Price'], 2) ?></td>
                            <td><?= number_format($item['In_Stock']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>

</html>
