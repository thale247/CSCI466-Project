<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); ?>
<!doctype html>
<html lang="en">
<?php
include 'db_connect.php';

$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT * FROM `Order`");
$stmt->execute();
$items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_shipped'])) {
        $orderNumber = $_POST['Order_Number'];

        $stmt = $pdo->prepare("UPDATE `Order` SET `Status` = 'Shipped' WHERE Order_Number = ?");
        $stmt->execute([$orderNumber]);
    } elseif (isset($_POST['update_notes'])) {
        $orderNumber = $_POST['Order_Number'];
        $notes = $_POST['notes'];

        $stmt = $pdo->prepare("UPDATE `Order` SET `Notes` = ? WHERE Order_Number = ?");
        $stmt->execute([$notes, $orderNumber]);
    }

    header("Location: order_manager.php");
}


?>

<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
        crossorigin="anonymous">
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

    <?php if (count($items) === 0): ?>
        <div class="alert alert-info">No current orders available.</div>
    <?php else: ?>
        <main class="container mt-4">
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Order Number</th>
                            <th>Username</th>
                            <th>Location ID</th>
                            <th>Order Total</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= number_format($item['Order_Number']) ?></td>
                                <td><?= htmlspecialchars($item['Username']) ?></td>
                                <td><?= number_format($item['Loc_ID']) ?></td>
                                <td>$<?= number_format($item['Order_Total']) ?></td>
                                <td><?= ($item['Order_DateTime']) ?></td>
                                <td>
                                    <?= htmlspecialchars($item['Status']) ?>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="Order_Number" value="<?= $item['Order_Number'] ?>">
                                        <button class="btn btn-success btn-sm" type="submit" name="mark_shipped">Mark as Shipped</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="Order_Number" value="<?= $item['Order_Number'] ?>">
                                        <input type="text" class="form-control mr-2" name="notes" value="<?= ($item['Notes']) ?>" style="width: 200px;">
                                        <button class="btn btn-primary btn-sm" type="submit" name="update_notes">Update Notes</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            </div>
        </main>
</body>

</html>
