<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); ?>
<!doctype html>
<html lang="en">
<?php 
include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $stmt = $pdo->prepare("UPDATE Product SET In_Stock = In_Stock - ? WHERE Product_ID = ?");
        $stmt->execute(array($_POST['quantity'], $_POST['product_id']));
    }
    else if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("UPDATE Product SET In_Stock = In_Stock + ? WHERE Product_ID = ?");
        $stmt->execute(array($_POST['quantity'], $_POST['product_id']));
    }
    else if(isset($_POST['add_item'])) {
        $stmt = $pdo->prepare("INSERT INTO Product (`Name`, Price, In_Stock) VALUES (?, ?, ?)");
        $stmt->execute(array($_POST['item_name'] ,$_POST['item_price'], $_POST['item_qty']));
    }
}


$stmt = $pdo->prepare("SELECT p.Product_ID, p.Name, p.Price, p.In_Stock FROM Product p");
$stmt->execute();
$items = $stmt->fetchAll();
?>
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/php/admin_index.php">Admin Home</a>
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="/php/admin_index.php">Admin Home</a>
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

<main class="container mt-4">
    <div class="row">
    <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th style="width: 220px;">In Stock</th>
                    <th>Modify Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= number_format($item['Product_ID']) ?></td>
                        <td><?= htmlspecialchars($item['Name']) ?></td>
                        <td>$<?= number_format($item['Price'], 2) ?></td>
                        <td><?= number_format($item['In_Stock']) ?></td>
                        <td>
                            <form method="post" class="form-inline">
                                <label for="quantity_<?= $item['Product_ID'] ?>" class="mr-2">Qty:</label>
                                <input type="number" 
                                    class="form-control mr-2" 
                                    style="width: 80px;" 
                                    id="quantity_<?= $item['Product_ID'] ?>" 
                                    name="quantity" 
                                    value="1" 
                                    min="1" >
                                <input type="hidden" name="product_id" value="<?= $item['Product_ID'] ?>">
                                <input type="hidden" name="add">
                                <button class="btn btn-success btn-sm mr-1" type="submit" name="add_qty">Add</button>
                            </form>
                            <form method="post" class="form-inline">
                            <label for="quantity_<?= $item['Product_ID'] ?>" class="mr-2">Qty:</label>
                                <input type="number" 
                                    class="form-control mr-2" 
                                    style="width: 80px;" 
                                    id="quantity_<?= $item['Product_ID'] ?>" 
                                    name="quantity" 
                                    value="1" 
                                    min="1" 
                                    max="<?= $item['In_Stock'] ?>">
                                <input type="hidden" name="product_id" value="<?= $item['Product_ID'] ?>">
                                <input type="hidden" name="remove">
                                <button class="btn btn-danger btn-sm" type="submit" name="remove_qty">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post" class="form-inline">
            <label for="item_name" class="mr-2">Item Name:</label>
            <input type="text" name="item_name" id="item_name" class="form-control mr-2">
            <label for="item_price" class="mr-2">Item Price:</label>
            <input type="text" id="item_price" name="item_price" class="form-control mr-2">
            <label for="item_qty" class="mr-2">Item In Stock:</label>
            <input type="number" 
                    class="form-control mr-2" 
                    style="width: 80px;" 
                    name="item_qty" 
                    id="item_qty"
                    value="1" 
                    min="1">
            <input type="hidden" name="add_item">
            <button class="btn btn-success btn-sm mr-1" type="submit" name="add_qty">Add New Item</button>
        </form>
    </div>
</main>
</body>
</html>
