<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$stmt = $pdo->prepare("SELECT o.Order_Number, o.Tracking_Number, o.Order_DateTime, o.Status, o.Order_Total, s.Apt_Suite, s.Shipping_Addr, s.City, s.State, s.Zip
                       FROM `Order` o
                       JOIN Shipping_Location s ON o.Loc_ID = s.Loc_ID
                       WHERE o.Order_Number = ? AND o.Username = ?");
$stmt->execute([$orderId, $_SESSION['username']]);
$order = $stmt->fetch();

if (!$order) {
    echo "<p class='alert alert-danger mt-5 text-center'>Order not found or access denied.</p>";
    exit();
}

$productStmt = $pdo->prepare("SELECT p.Name, p.Price, po.Quantity
                              FROM Product_Order po
                              JOIN Product p ON po.Product_ID = p.Product_ID
                              WHERE po.Order_Number = ?");
$productStmt->execute([$orderId]);
$products = $productStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $orderId ?> Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body>
<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">Online Store</a>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Shop For Items</a>
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
    <h2>Order #<?= $orderId ?> Details</h2>
    <p><strong>Date:</strong> <?= $order['Order_DateTime'] ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['Status']) ?></p>
    <p><strong>Tracking #:</strong> <a href=""><?= htmlspecialchars($order['Tracking_Number']) ?></a></p>
    <p><strong>Total:</strong> $<?= number_format($order['Order_Total'], 2) ?></p>
    <p><strong>Shipping Address:</strong><br>
        <?= htmlspecialchars($order['Shipping_Addr']) ?><br>
        <?= htmlspecialchars($order['Apt_Suite']) ?><br>
        <?= htmlspecialchars($order['City']) ?>, <?= $order['State'] ?> <?= $order['Zip'] ?>
    </p>

    <h4 class="mt-4">Items:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price Each</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['Name']) ?></td>
                <td>$<?= number_format($p['Price'], 2) ?></td>
                <td><?= $p['Quantity'] ?></td>
                <td>$<?= number_format($p['Price'] * $p['Quantity'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
