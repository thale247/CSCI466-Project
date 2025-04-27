<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT `Order_Number` FROM `Order` WHERE Username = ?");
$stmt->execute([$username]);
$order = $stmt->fetch();

if ($order) {
    $orderNumber = $order['Order_Number'];
    $stmt = $pdo->prepare("SELECT `Order_DateTime`, `Loc_ID`, `Order_Total`, `Status` FROM `Order` WHERE `Username` = ?");
    $stmt->execute([$username]);
    $items = $stmt->fetchAll();
} else {
    $items = [];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
        crossorigin="anonymous">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">Online Parts Store</a>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Shop For Parts</a>
                <?php if (isset($_SESSION['username'])): ?>
                    <a class="nav-item nav-link" href="/php/orders.php">Orders</a>
                    <a class="nav-item nav-link" href="/php/sign_out.php">Sign Out</a>
                    <a class="nav-item nav-link" href="/php/cart.php">View Cart</a>
                <?php else: ?>
                    <a class="nav-item nav-link" href="/php/login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container mt-5">
        <h1 class="mb-4">Pending Orders</h1>

        <?php if (count($items) === 0): ?>
            <div class="alert alert-info">No current orders available.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Order Date & Time</th>
                        <th>Track Location</th>
                        <th>Status</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item['Order_DateTime'] ?></td>
                            <td><a href="#"><?= $item['Loc_ID'] ?></td>
                            <td><?= htmlspecialchars($item['Status']) ?></td>
                            <td>$<?= number_format($item['Order_Total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>

</html>
