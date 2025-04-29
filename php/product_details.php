<?php
session_start();
require 'db_connect.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM Product WHERE Product_ID = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo "<p class='alert alert-danger text-center mt-5'>Product not found.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = intval($_POST['quantity']);

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

    // Add or update Contains
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

    echo "<p class='alert alert-success text-center mx-auto mt-3' style='max-width: 500px;'>Product added to cart</p>";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['Name']) ?> - Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/php/index.php">Online Store</a>
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/php/index.php">Shop For Items</a>
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

<main class="container mt-4">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($product['Name']) ?></h3>
            <h5 class="text-muted">Price: $<?= number_format($product['Price'], 2) ?></h5>
            <p class="mt-3"><?= nl2br(htmlspecialchars($product['Description'])) ?></p>
            <p><strong>In Stock:</strong> <?= $product['In_Stock'] ?></p>
            <form method="post">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?= $product['In_Stock'] ?>" style="max-width: 100px;">
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
