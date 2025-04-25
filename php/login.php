<?php
include 'db_connect.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM User WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user['Password'] === $password) { // Use password_verify() in production
        $_SESSION['username'] = $user['Username'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
<h1>Login</h1>
<form method="post">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <input type="submit" value="Login">
</form>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php include 'includes/footer.php'; ?>
</main>
</body>
</html>