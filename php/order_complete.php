<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username']) || !isset($_GET['order_id']) || !isset($_SESSION['last_four'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$order_id = $_GET['order_id'];
$last_four = $_SESSION['last_four'];

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.Order_Number, o.Order_DateTime, o.Order_Total, s.Shipping_Addr, s.Apt_Suite, s.City, s.State, s.Zip
    FROM `Order` o
    JOIN Shipping_Location s ON o.Loc_ID = s.Loc_ID
    WHERE o.Order_Number = ? AND o.Username = ?
");
$stmt->execute([$order_id, $username]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

// Fetch items
$stmt = $pdo->prepare("
    SELECT p.Name, p.Price, po.Quantity
    FROM Product_Order po
    JOIN Product p ON po.Product_ID = p.Product_ID
    WHERE po.Order_Number = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
</head>
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
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-success">Thank you for your order!</h2>
    <p>Your order has been placed successfully.</p>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Order Summary</div>
        <div class="card-body">
            <p><strong>Order Number:</strong> <?= htmlspecialchars($order['Order_Number']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($order['Order_DateTime']) ?></p>
            <p><strong>Total:</strong> $<?= number_format($order['Order_Total'], 2) ?></p>
            <p><strong>Paid with:</strong> **** **** **** <?= htmlspecialchars($last_four) ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Shipping Address</div>
        <div class="card-body">
            <p><?= htmlspecialchars($order['Shipping_Addr']) ?> <?= $order['Apt_Suite'] ? ', ' . htmlspecialchars($order['Apt_Suite']) : '' ?></p>
            <p><?= htmlspecialchars($order['City']) ?>, <?= htmlspecialchars($order['State']) ?> <?= htmlspecialchars($order['Zip']) ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Items</div>
        <ul class="list-group list-group-flush">
            <?php foreach ($items as $item): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?= htmlspecialchars($item['Name']) ?>
                    <span>$<?= number_format($item['Price'], 2) ?> x <?= $item['Quantity'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <a href="index.php" class="btn btn-primary">Back to Home</a>
</div>
</body>
</main>
</html>
