<!doctype html>
<html>

<head>
    <title>Buy Parts Now</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
    <!-- Probably better to put style in a css file. May do that later -->
    <nav class="nav navbar-dark bg-dark d-flex align-items-center" style="height:75px;">
        <a class="navbar-brand" href="#" style="padding:20px;">Online Parts Store</a>
        <a class="nav-link active text-light" href="#" style="padding:20px;">Shop For Parts</a>
        <!-- Navbar link name is temporary. Not sure what pages we need yet -->
        <a class="nav-link text-secondary" href="#" style="padding:20px;">Inventory</a>
        <div class="ml-auto">
            <a class="nav-link" href="/cart.php" style="padding:20px;">View Cart</a>
        </div>

    </nav>

    <!-- TEMPORARY CARD for formatting reference. Will be used as base for SQL -->
    <div class="d-flex flex-wrap">
        <div class="card" style="width:40%;margin:3%;">
            <div class="card-body">
                <h5 class="card-title">Part Name</h5>
                <h6 class="card-subtitle">Price: #</h6>
                <p class="card-text">Quantity remaining: #</p>
                <a href="#" class="btn btn-primary">Add to Cart</a>
            </div>
        </div>
        <div class="card" style="width:40%;margin:3%;">
            <div class="card-body">
                <h5 class="card-title">Temp 2</h5>
                <h6 class="card-subtitle">Price: #</h6>
                <p class="card-text">Quantity remaining: #</p>
                <a href="#" class="btn btn-primary">Add to Cart</a>
            </div>
        </div>
        <div class="card" style="width:40%;margin:3%;">
            <div class="card-body">
                <h5 class="card-title">Temp 3</h5>
                <h6 class="card-subtitle">Price: #</h6>
                <p class="card-text">Quantity remaining: #</p>
                <a href="#" class="btn btn-primary">Add to Cart</a>
            </div>
        </div>
    </div>

</body>

</html>
