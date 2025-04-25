<!doctype html>
<html>

<?php include 'db_connect.php'; 
      include 'header.php';?>

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

</body>

</html>
