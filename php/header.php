<?php session_start(); ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
<header>
<nav class="nav navbar-dark bg-dark d-flex align-items-center" style="height:75px;">
        <a class="navbar-brand" href="index.php" style="padding:20px;">Online Parts Store</a>
        <a class="nav-link active text-light" href="index.php" style="padding:20px;">Shop For Parts</a>
        <!-- Navbar link name is temporary. Not sure what pages we need yet -->
        <!-- <a class="nav-link text-secondary" href="inventory.php" style="padding:20px;">Inventory</a> -->
        <div class="ml-auto">
            <a class="nav-link" href="/php/cart.php" style="padding:20px;">View Cart</a>
        </div>

    </nav>
</header>