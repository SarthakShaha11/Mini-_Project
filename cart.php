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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f9f6f2, #e0d7cf);
            color: #4a2c2a;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37;
            padding: 2px 0px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header .logo img {
            width: 50px;
            height: auto;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .header .logo img:hover {
            transform: scale(1.05);
        }

        .navbar {
            display: flex;
            gap: 25px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding: 8px 12px;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #f9f6f2;
        }

        /* Icons */
        .icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icons i {
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .icons i:hover {
            color: #f9f6f2;
        }

        /* Cart Container */
        .cart-container {
            max-width: 1000px;
            margin: 100px auto 20px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .cart-container h1 {
            color: #6F4E37;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
            position: relative;
        }

        .cart-container h1::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 4px;
            background: #6F4E37;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 2px solid #6F4E37;
        }

        th, td {
            padding: 12px;
            border: 1px solid #6F4E37;
            text-align: left;
        }

        th {
            background-color: #6F4E37;
            color: white;
            font-weight: 600;
        }

        td {
            color: #4a2c2a;
        }

        .total-row {
            background-color: #f5f5f5;
            font-size: 1.2em;
            font-weight: 600;
        }

        .total-row td {
            padding: 15px;
            text-align: right;
        }

        /* Buttons */
        .btn {
            background: #6F4E37;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background: #56372e;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .action-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        /* Footer */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 20px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
        }

        .footer p {
            margin: 0;
            font-size: 14px;
        }

        .footer .social-icons {
            margin-top: 10px;
        }

        .footer .social-icons a {
            color: white;
            font-size: 20px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: #f9f6f2;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
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
            <a href="review.php">Reviews</a>
            <a href="blog.php">Blog</a>
            <a href="Admin_login.php">Admin</a>
        </nav>
        <div class="icons">
            <i class="fas fa-search"></i>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>

    <!-- Cart Container -->
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
        <p>&copy; <?php echo date("Y"); ?> The Coffee Hub. All rights reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>
</body>
</html>