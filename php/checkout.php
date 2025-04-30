<?php
session_start();
require_once 'db_connect.php'; // assumes this sets up $pdo

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$cart_total = 0.00;

// Get the user’s cart
$stmt = $pdo->prepare("SELECT * FROM Cart WHERE Username = ?");
$stmt->execute([$username]);
$cart = $stmt->fetch();

if (!$cart) {
    die("No cart found for user.");
}

$cart_number = $cart['Cart_Number'];

// Get cart contents
$stmt = $pdo->prepare("SELECT Product.*, Contains.Quantity FROM Contains JOIN Product ON Contains.Product_ID = Product.Product_ID WHERE Cart_Number = ?");
$stmt->execute([$cart_number]);
$items = $stmt->fetchAll();

foreach ($items as $item) {
    $cart_total += $item['Price'] * $item['Quantity'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get shipping address
    $address = $_POST['address'];
    $apt = $_POST['apt'];
    $zip = $_POST['zip'];
    $city = $_POST['city'];
    $state = $_POST['state'];

    if (!$address || !$zip || !$city || !$state) {
        $error = "Please fill out all required address fields.";
    } else {
        try {
            // Insert shipping location
            $stmt = $pdo->prepare("INSERT INTO Shipping_Location (Username, Shipping_Addr, Apt_Suite, Zip, City, State) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $address, $apt, $zip, $city, $state]);
            $loc_id = $pdo->lastInsertId();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO `Order` (Username, Loc_ID, Order_Total, Status, Order_DateTime) VALUES (?, ?, ?, 'Created', NOW())");
            $stmt->execute([$username, $loc_id, $cart_total]);
            $order_id = $pdo->lastInsertId();

            // Insert into Product_Order
            $stmt = $pdo->prepare("INSERT INTO Product_Order (Product_ID, Order_Number, Quantity) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $stmt->execute([$item['Product_ID'], $order_id, $item['Quantity']]);
            }

            // Clear cart
            $pdo->prepare("DELETE FROM Contains WHERE Cart_Number = ?")->execute([$cart_number]);
            $pdo->prepare("UPDATE Cart SET Cart_Total = 0.00 WHERE Cart_Number = ?")->execute([$cart_number]);

            echo "<p>Order submitted successfully! Your order number is #$order_id.</p>";
            exit();
        } catch (PDOException $e) {
            $error = "Checkout failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
<h2>Checkout</h2>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<h3>Your Cart</h3>
<ul>
    <?php foreach ($items as $item): ?>
        <li><?= htmlspecialchars($item['Name']) ?> - $<?= number_format($item['Price'], 2) ?> x <?= $item['Quantity'] ?></li>
    <?php endforeach; ?>
</ul>
<p><strong>Total:</strong> $<?= number_format($cart_total, 2) ?></p>

<h3>Shipping Address</h3>
<form method="post">
    <label>Address: <input type="text" name="address" required></label><br>
    <label>Apt/Suite: <input type="text" name="apt"></label><br>
    <label>ZIP Code: <input type="text" name="zip" required></label><br>
    <label>City: <input type="text" name="city" required></label><br>
    <label>State: <input type="text" name="state" required></label><br><br>

    <h3>Billing Information (FAKE)</h3>
    <label>Name on Card: <input type="text" name="cardname"></label><br>
    <label>Card Number: <input type="text" name="cardnumber"></label><br>
    <label>Expiration Date: <input type="text" name="exp"></label><br>
    <label>CVV: <input type="text" name="cvv"></label><br><br>

    <button type="submit">Place Order</button>
</form>

</body>
</html>
