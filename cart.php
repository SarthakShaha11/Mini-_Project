<?php
session_start();

// Database connection
$servername = "127.0.0.1:3306";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "ch";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding products to the cart
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $product_id = $_POST["product_id"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];

    // Check current stock
    $sql = "SELECT stock FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    // Check if the product is already in the cart
    $cart = isset($_SESSION['cart_items']) ? $_SESSION['cart_items'] : [];
    $cart_quantity = 0;
    foreach ($cart as $item) {
        if ($item['product_id'] == $product_id) {
            $cart_quantity = $item['quantity'];
            break;
        }
    }
    $remaining_stock = $stock - $cart_quantity;

    // Validate quantity
    if ($quantity > $remaining_stock) {
        echo "<script>alert('Quantity cannot exceed available stock!'); window.history.back();</script>";
        exit();
    } else {
        // Update stock in the database
        $new_stock = $stock - $quantity;
        $update_sql = "UPDATE product SET stock = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_stock, $product_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Initialize cart if not already set
        if (!isset($_SESSION['cart_items'])) {
            $_SESSION['cart_items'] = [];
        }

        // Check if the product already exists in the cart
        $product_exists = false;
        foreach ($_SESSION['cart_items'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $quantity; // Update quantity if product exists
                $product_exists = true;
                break;
            }
        }

        // If the product is not in the cart, add it
        if (!$product_exists) {
            $_SESSION['cart_items'][] = [
                'product_id' => $product_id,
                'product_name' => $name,
                'product_price' => $price,
                'quantity' => $quantity
            ];
        }

        // Redirect to cart page
        echo "<script>alert('Product added to cart successfully!'); window.location.href = 'cart.php';</script>";
        exit();
    }
}

// Handle product removal
if (isset($_GET["remove"]) && isset($_SESSION["cart_items"])) {
    $remove_id = $_GET["remove"];

    // Remove product from the session cart
    foreach ($_SESSION["cart_items"] as $key => $item) {
        if ($item["product_id"] == $remove_id) {
            // Restore stock to the database
            $restore_sql = "UPDATE product SET stock = stock + ? WHERE product_id = ?";
            $restore_stmt = $conn->prepare($restore_sql);
            $restore_stmt->bind_param("ii", $item["quantity"], $remove_id);
            $restore_stmt->execute();
            $restore_stmt->close();

            // Remove item from cart
            unset($_SESSION["cart_items"][$key]);
            break;
        }
    }

    // Redirect to avoid resubmission
    header("Location: cart.php");
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cart - THE COFFEE HUB</title>
    <link rel="stylesheet" href="Style-1.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <style>
        /* Your existing CSS styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #c2b280;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styles */
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
            border-radius: 50%;
        }

        .navbar {
            display: flex;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 10px;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #d9c9b2;
        }

        .icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icons i {
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .icons i:hover {
            color: #d9c9b2;
        }

        /* Cart Section Styles */
        .cart-container {
            padding: 100px 20px 80px;
            margin-top: 60px;
            flex: 1;
        }

        h1 {
            text-align: center;
            color: #6F4E37;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background: #6F4E37;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td[colspan="4"] {
            text-align: center;
            font-weight: bold;
            color: #6F4E37;
        }

        .total-row {
            background-color: #f5f5f5;
            font-size: 1.1em;
        }

        .total-row td {
            padding: 15px;
        }

        /* Buttons */
        .btn {
            padding: 10px 15px;
            background: #6F4E37;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }

        .btn:hover {
            background: #56372e;
        }

        .action-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px auto;
        }

        .action-btns form {
            display: inline-block;
        }

        /* Footer Styles */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            .header {
                padding: 10px;
            }

            .navbar a {
                font-size: 16px;
            }

            .icons i {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .cart-container {
                padding: 80px 10px 60px;
            }

            h1 {
                font-size: 1.8rem;
            }

            .btn {
                padding: 8px 12px;
                font-size: 14px;
            }

            .action-btns {
                flex-direction: column;
                gap: 10px;
            }
        }

        /* Remove Button Styles */
        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-danger:hover {
            background: #c82333;
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

    <!-- Cart Section -->
    <section class="cart-container">
        <h1>Your Cart</h1>

        <?php
        if (isset($_SESSION["cart_items"]) && !empty($_SESSION["cart_items"])) {
            echo '
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';

            $total = 0;
            foreach ($_SESSION["cart_items"] as $item) {
                $subtotal = $item["product_price"] * $item["quantity"];
                $total += $subtotal;
                echo '
                <tr>
                    <td>' . htmlspecialchars($item["product_name"]) . '</td>
                    <td>₹' . number_format($item["product_price"], 2) . '</td>
                    <td>' . htmlspecialchars($item["quantity"]) . '</td>
                    <td>₹' . number_format($subtotal, 2) . '</td>
                    <td>
                        <a href="cart.php?remove=' . $item["product_id"] . '" class="btn-danger">Remove</a>
                    </td>
                </tr>';
            }

            echo '
                <tr class="total-row">
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>₹' . number_format($total, 2) . '</strong></td>
                    <td></td>
                </tr>
                </tbody>
            </table>';

            echo '
            <div class="action-btns">
                <a href="Product.php" class="btn">Continue Shopping</a>
                <a href="order.php" class="btn">Place Order</a>
            </div>';
        } else {
            echo '<p>Your cart is empty.</p>';
        }
        ?>
    </section> 

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
    </footer>

</body>
</html>