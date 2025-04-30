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

            $stmt = $pdo->prepare("SELECT In_Stock FROM Product WHERE Product_ID = ?");
            foreach ($items as $item) {
                $stmt->execute([$item['Product_ID']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if (!$result) {
                    throw new Exception("Product not found: " . htmlspecialchars($item['Product_ID']));
                }
            
                if ($item['Quantity'] > $result['In_Stock']) {
                    throw new Exception("Not enough stock for " . htmlspecialchars($item['Name']) . ". Only {$result['In_Stock']} left.");
                }
            }
            
            // Insert shipping location
            $stmt = $pdo->prepare("INSERT INTO Shipping_Location (Username, Shipping_Addr, Apt_Suite, Zip, City, State) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $address, $apt, $zip, $city, $state]);
            $loc_id = $pdo->lastInsertId();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO `Order` (Username, Loc_ID, Order_Total, Status, Order_DateTime) VALUES (?, ?, ?, 'Processing', NOW())");
            $stmt->execute([$username, $loc_id, $cart_total]);
            $order_id = $pdo->lastInsertId();

            // Insert into Product_Order
            $stmt = $pdo->prepare("INSERT INTO Product_Order (Product_ID, Order_Number, Quantity) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $stmt->execute([$item['Product_ID'], $order_id, $item['Quantity']]);
            }

            $stmt = $pdo->prepare("UPDATE Product SET In_Stock = In_Stock - ? WHERE Product_ID = ?");
            foreach ($items as $item) {
                $stmt->execute([$item['Quantity'], $item['Product_ID']]);
            }

            // Clear cart
            $pdo->prepare("DELETE FROM Contains WHERE Cart_Number = ?")->execute([$cart_number]);
            $pdo->prepare("UPDATE Cart SET Cart_Total = 0.00 WHERE Cart_Number = ?")->execute([$cart_number]);

            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Checkout failed: " . $e->getMessage();
        } catch (Exception $e) {
            $error = "Checkout failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
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
<main>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Cart Summary -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Your Cart
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?= htmlspecialchars($item['Name']) ?>
                            <span>$<?= number_format($item['Price'], 2) ?> x <?= $item['Quantity'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="card-footer text-right">
                    <strong>Total:</strong> $<?= number_format($cart_total, 2) ?>
                </div>
            </div>
        </div>

        <!-- Shipping + Billing Form -->
        <div class="col-md-6">
            <form method="post">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Shipping Address</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="address">Address *</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="apt">Apt/Suite</label>
                            <input type="text" class="form-control" id="apt" name="apt">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="zip">ZIP Code *</label>
                                <input type="text" class="form-control" id="zip" name="zip" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="city">City *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="state">State *</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Billing Info (Fake)</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="cardname">Name on Card</label>
                            <input type="text" class="form-control" id="cardname" name="cardname">
                        </div>
                        <div class="form-group">
                            <label for="cardnumber">Card Number</label>
                            <input type="text" class="form-control" id="cardnumber" name="cardnumber">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="exp">Expiration Date</label>
                                <input type="text" class="form-control" id="exp" name="exp">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cvv">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block">Place Order</button>
            </form>
        </div>
    </div>
</div>
</body>
</main>
</html>
