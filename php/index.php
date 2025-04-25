<?php session_start(); ?>

<!doctype html>
<html>

<?php include 'db_connect.php'; ?>
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<main>
<body>
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
    <!-- TEMPORARY CARD for formatting reference. Will be used as base for SQL -->
     <div class="d-flex flex-wrap">
     <?php
        $stmt = $pdo->query("SELECT * FROM Product WHERE In_Stock > 0");
        while ($row = $stmt->fetch()) {
            echo "<div class='card'  style='width:40%;margin:3%;'>
                    <div class='card-body'>
                    <h5 class='card-title'>{$row['Name']}</h2>
                    <p class='card-subtitle'>Price: \${$row['Price']}</p>
                    <form method='post' action='/php/cart.php'>
                        <input type='hidden' name='product_id' value='{$row['Product_ID']}'>
                        <label>Qty:</label>
                        <input type='number' name='quantity' value='1' min='1' max='{$row['In_Stock']}'>
                        <input class='btn btn-primary' type='submit' name='add_to_cart' value='Add to Cart'>
                    </form>
                    </div>
                </div>";
        }
        ?>
        </div>
    </main>
</body>

</html>
