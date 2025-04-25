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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
<main>
    <header>
        <nav class="nav navbar-dark bg-dark d-flex align-items-center" style="height:75px;">
            <a class="navbar-brand" href="index.php" style="padding:20px;">Online Parts Store</a>
            <a class="nav-link active text-light" href="index.php" style="padding:20px;">Shop For Parts</a>
            <!-- Navbar link name is temporary. Not sure what pages we need yet -->
            <!-- <a class="nav-link text-secondary" href="inventory.php" style="padding:20px;">Inventory</a> -->
            <?php if (isset($_SESSION['username'])): ?>
                <a class="nav-link active text-light" href="/php/orders.php">Orders</a>
                <a class="nav-link active text-light" href="/php/sign_out.php">Sign Out</a>
                <div class="ml-auto">
                <a class="nav-link" href="/php/cart.php" style="padding:20px;">View Cart</a>
            </div>
            <?php else: ?>
                <a class="nav-link active text-light" href="/php/login.php">Login</a>
            <?php endif; ?>

        </nav>
    </header>
    <h1>Your Shopping Cart</h1>
    <?php if (count($items) === 0): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
                <th>Total</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['Name']) ?></td>
                    <td>$<?= number_format($item['Price'], 2) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $item['Product_ID'] ?>">
                            <input type="number" name="quantity" value="<?= $item['Quantity'] ?>" min="1">
                            <input type="submit" name="update" value="Update">
                            <input type="submit" name="remove" value="Remove">
                        </form>
                    </td>
                    <td>$<?= number_format($item['Quantity'] * $item['Price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form method="get" action="checkout.php">
            <input type="submit" value="Proceed to Checkout">
        </form>
    <?php endif; ?>
</main>
</body>
</html>
