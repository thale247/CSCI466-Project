<?php session_start(); ?>
<!doctype html>
<html lang="en">
<?php include 'db_connect.php'; ?>
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
</head>
<body>

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

<main class="container mt-4">
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM Product WHERE In_Stock > 0");
        while ($row = $stmt->fetch()) {
            echo "<div class='col-md-6 col-lg-4 mb-4'>
                    <div class='card h-100'>
                        <div class='card-body d-flex flex-column'>
                            <h5 class='card-title'>{$row['Name']}</h5>
                            <p class='card-subtitle mb-3 text-muted'>Price: \${$row['Price']}</p>
                            <form method='post' action='/php/cart.php' class='mt-auto'>
                                <input type='hidden' name='product_id' value='{$row['Product_ID']}'>
                                <div class='form-group'>
                                    <label for='quantity_{$row['Product_ID']}'>Qty:</label>
                                    <input type='number' class='form-control' style='max-width: 100px;' 
                                           id='quantity_{$row['Product_ID']}' name='quantity' 
                                           value='1' min='1' max='{$row['In_Stock']}'>
                                </div>
                                <button class='btn btn-primary btn-block' type='submit' name='add_to_cart'>Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>";
        }
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
