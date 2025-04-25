<!doctype html>
<html>



<?php
include 'db_connect.php';
include 'header.php';
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

include 'includes/header.php';
echo "<h1>Your Cart</h1>";
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
    echo "<form method='post' action='checkout.php'>";
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM Product WHERE Product_ID = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        $subtotal = $product['Price'] * $qty;
        $total += $subtotal;
        echo "<div>
                <h2>{$product['Name']}</h2>
                <p>Quantity: $qty</p>
                <p>Subtotal: \${$subtotal}</p>
              </div>";
    }
    echo "<p>Total: \${$total}</p>
          <input type='submit' value='Proceed to Checkout'>
          </form>";
}

?>

</body>

</html>
