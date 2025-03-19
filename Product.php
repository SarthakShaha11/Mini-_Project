<?php
session_start();

// Database connection
$servername   = "localhost";
$db_username  = "root";
$db_password  = "";
$dbname       = "ch";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database, including stock column
$sql = "SELECT product_id, name, price, image, stock FROM product";
$result = $conn->query($sql);

// Fetch cart data from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>THE COFFEE HUB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* CSS Styles */
        body {
            font-family: Arial, sans-serif;
            background: #d9c9b2;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37;
            padding: 5px 5px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .header .logo img {
            width: 50px;
            height: auto;
        }
        .navbar a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
        }
        .navbar a:hover {
            color: #d9c9b2;
        }
        .icons i {
            color: white;
            font-size: 20px;
            margin-left: 15px;
            cursor: pointer;
        }
        .icons i:hover {
            color: #d9c9b2;
        }
        .products {
            text-align: center;
            padding: 100px 20px 80px;
            margin-top: 60px;
        }
        .products h1 {
            color: #6F4E37;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            justify-content: center;
        }
        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            position: relative;
        }
        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .box img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .box h3 {
            margin: 10px 0;
            color: #6F4E37;
            font-size: 1.5em;
        }
        .price {
            color: #6F4E37;
            font-size: 1.3em;
            font-weight: bold;
            margin: 10px 0;
            display: block;
        }
        .stock {
            font-size: 1.1em;
            font-weight: bold;
            color: green;
            margin-bottom: 10px;
        }
        .out-of-stock {
            color: red;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #6F4E37;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
        }
        .btn {
            background: #6F4E37;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }
        .btn:hover {
            background: #56372e;
        }
        .error-message {
            color: red;
            font-size: 14px;
            display: none;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        /* ... (same as before) ... */
    </style>
</head>

<body>

    <!-- Header -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="logo.jpg" alt="The Coffee Hub Logo" />
        </a>
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="Product.php">Products</a>
            <a href="review.php">Reviwes</a>
            <a href="blog.php">Blog</a>
            <a href="Admin_login.php">Admin</a>
        </nav>
        <div class="icons">
            <i class="fas fa-search"></i>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>

    <!-- Products Section -->
    <section class="products" id="products">
        <h1>Our Products</h1>

        <div class="box-container">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $product_id = $row["product_id"];
                    $available_stock = $row["stock"];

                    // Check if the product is already in the cart
                    $cart_quantity = isset($cart[$product_id]) ? $cart[$product_id] : 0;
                    $remaining_stock = $available_stock - $cart_quantity;

                    // Disable the form if the product is out of stock
                    $is_out_of_stock = ($remaining_stock < 1);
                    ?>
                    <div class="box">
                        <img src="<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["name"]); ?>" />
                        <h3><?php echo htmlspecialchars($row["name"]); ?></h3>
                        <p class="price">₹<?php echo number_format($row["price"], 2); ?></p>
                        <p class="stock">Stock Available: 
                            <span class="<?php echo ($remaining_stock < 1) ? 'out-of-stock' : ''; ?>">
                                <?php echo $remaining_stock; ?>
                            </span>
                        </p>

                        <form action="cart.php" method="POST" onsubmit="return validateQuantity(this, <?php echo $remaining_stock; ?>);">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($row["name"]); ?>">
                            <input type="hidden" name="price" value="<?php echo $row["price"]; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $remaining_stock; ?>" class="quantity-input" <?php echo $is_out_of_stock ? 'disabled' : ''; ?>>
                            <p class="error-message">⚠ Quantity cannot exceed available stock!</p>
                            <button type="submit" class="btn" <?php echo $is_out_of_stock ? 'disabled' : ''; ?>>
                                <?php echo $is_out_of_stock ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
    </footer>

    <script>
    function validateQuantity(form, stock) {
        var quantityInput = form.querySelector(".quantity-input");
        var errorMessage = form.querySelector(".error-message");

        if (quantityInput.value > stock) {
            errorMessage.textContent = "⚠ Quantity cannot exceed available stock (" + stock + ")!";
            errorMessage.style.display = "block";
            return false;
        } else if (quantityInput.value < 1) {
            errorMessage.textContent = "⚠ Quantity must be at least 1!";
            errorMessage.style.display = "block";
            return false;
        } else {
            errorMessage.style.display = "none";
            return true;
        }
    }
    </script>

</body>
</html>