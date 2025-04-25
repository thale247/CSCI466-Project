<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch or create user's cart
$stmt = $pdo->prepare("SELECT Cart_Number FROM Cart WHERE Username = ?");
$stmt->execute([$username]);
$cart = $stmt->fetch();

if (!$cart) {
    $stmt = $pdo->prepare("INSERT INTO Cart (Username, Cart_Total) VALUES (?, 0.00)");
    $stmt->execute([$username]);
    $cartNumber = $pdo->lastInsertId();
} else {
    $cartNumber = $cart['Cart_Number'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    if (isset($_POST['update'])) {
        if ($quantity > 0) {
            $stmt = $pdo->prepare("UPDATE Contains SET Quantity = ? WHERE Cart_Number = ? AND Product_ID = ?");
            $stmt->execute([$quantity, $cartNumber, $productId]);
        }
    } elseif (isset($_POST['remove'])) {
        $stmt = $pdo->prepare("DELETE FROM Contains WHERE Cart_Number = ? AND Product_ID = ?");
        $stmt->execute([$cartNumber, $productId]);
    } elseif (isset($_POST['add_to_cart'])) {
        $checkStmt = $pdo->prepare("SELECT Quantity FROM Contains WHERE Cart_Number = ? AND Product_ID = ?");
        $checkStmt->execute([$cartNumber, $productId]);

        if ($row = $checkStmt->fetch()) {
            $newQuantity = $row['Quantity'] + $quantity;
            $updateStmt = $pdo->prepare("UPDATE Contains SET Quantity = ? WHERE Cart_Number = ? AND Product_ID = ?");
            $updateStmt->execute([$newQuantity, $cartNumber, $productId]);
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO Contains (Cart_Number, Product_ID, Quantity) VALUES (?, ?, ?)");
            $insertStmt->execute([$cartNumber, $productId, $quantity]);
        }
    }

    // Update cart total
    $totalStmt = $pdo->prepare("SELECT SUM(p.Price * c.Quantity) FROM Contains c JOIN Product p ON c.Product_ID = p.Product_ID WHERE c.Cart_Number = ?");
    $totalStmt->execute([$cartNumber]);
    $newTotal = $totalStmt->fetchColumn();
    $updateCartTotal = $pdo->prepare("UPDATE Cart SET Cart_Total = ? WHERE Cart_Number = ?");
    $updateCartTotal->execute([$newTotal, $cartNumber]);
}

$stmt = $pdo->prepare("SELECT p.Product_ID, p.Name, p.Price, ct.Quantity FROM Product p JOIN Contains ct ON p.Product_ID = ct.Product_ID WHERE ct.Cart_Number = ?");
$stmt->execute([$cartNumber]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
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
    <h1 class="mb-4">Your Shopping Cart</h1>

    <?php if (count($items) === 0): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th style="width: 220px;">Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['Name']) ?></td>
                        <td>$<?= number_format($item['Price'], 2) ?></td>
                        <td>
                            <form method="POST" class="form-inline">
                                <input type="hidden" name="product_id" value="<?= $item['Product_ID'] ?>">
                                <input type="number" class="form-control mr-2" name="quantity" value="<?= $item['Quantity'] ?>" min="1" style="width: 70px;">
                                <button type="submit" name="update" class="btn btn-sm btn-outline-primary mr-1">Update</button>
                                <button type="submit" name="remove" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                        <td>$<?= number_format($item['Quantity'] * $item['Price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="get" action="checkout.php" class="text-right">
            <button type="submit" class="btn btn-success">Proceed to Checkout</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
