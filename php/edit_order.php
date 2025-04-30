<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connect.php';

if (!isset($_GET['order'])) {
    die("Order number not specified.");
}

$orderNumber = $_GET['order'];

$stmt = $pdo->prepare("
    SELECT o.*, u.Email, u.First_Name, u.Last_Name, 
           s.Shipping_Addr, s.Apt_Suite, s.City, s.State, s.Zip
    FROM `Order` o
    JOIN `User` u ON o.Username = u.Username
    JOIN `Shipping_Location` s ON o.Loc_ID = s.Loc_ID
    WHERE o.Order_Number = ?
");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $tracking = $_POST['tracking'];
    $notes = $_POST['notes'];

    $update = $pdo->prepare("UPDATE `Order` SET `Status` = ?, `Tracking_Number` = ?, `Notes` = ? WHERE Order_Number = ?");
    $update->execute([$status, $tracking, $notes, $orderNumber]);

    header("Location: order_manager.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Order #<?= $orderNumber ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/php/admin_index.php">Admin Home</a>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="/php/admin_index.php">Dashboard</a>
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
        <h2>Edit Order #<?= $orderNumber ?></h2>

        <div class="mb-4">
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['First_Name'] . " " . $order['Last_Name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['Email']) ?></p>
            <p><strong>Shipping Address:</strong><br>
                <?= htmlspecialchars($order['Shipping_Addr']) ?><br>
                <?php if (!empty($order['Apt_Suite'])): ?>
                    <?= htmlspecialchars($order['Apt_Suite']) ?><br>
                <?php endif; ?>
                <?= htmlspecialchars($order['City']) ?>, <?= htmlspecialchars($order['State']) ?> <?= htmlspecialchars($order['Zip']) ?>
            </p>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <?php foreach (['Processing', 'Shipped', 'Delivered', 'Cancelled'] as $statusOption): ?>
                        <option value="<?= $statusOption ?>" <?= $order['Status'] === $statusOption ? 'selected' : '' ?>>
                            <?= $statusOption ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tracking Number</label>
                <input type="text" name="tracking" class="form-control" value="<?= $order['Tracking_Number'] ?>">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="4"><?= $order['Notes'] ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Order</button>
            <a href="order_manager.php" class="btn btn-secondary">Cancel</a>
        </form>
    </main>
</body>
</html>
