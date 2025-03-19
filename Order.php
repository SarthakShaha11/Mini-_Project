<?php
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Check if cart has items, but only redirect if not on the search page
if (!isset($_SESSION['cart_items']) || empty($_SESSION['cart_items'])) {
    $current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
    if ($current_page != 'search.php') { // Only redirect if not on search.php
        header("Location: cart.php");
        exit();
    }
}

// Get session data
$cart_items = $_SESSION['cart_items'];

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    // Use the correct key name: 'product_price' instead of 'price'
    $price = isset($item['product_price']) && is_numeric($item['product_price']) ? (float)$item['product_price'] : 0;
    $quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;

    // Ensure price and quantity are valid
    if ($price <= 0 || $quantity <= 0) {
        continue; // Skip this item
    }

    $subtotal = $price * $quantity;
    $total_amount += $subtotal;
}

// Store total amount in session
$_SESSION['total_amount'] = $total_amount;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product removal
if (isset($_GET["remove"]) && isset($_SESSION["cart_items"])) {
    $remove_id = $_GET["remove"];

    // Remove product from the session cart
    foreach ($_SESSION["cart_items"] as $key => $item) {
        if ($item["product_id"] == $remove_id) {
            unset($_SESSION["cart_items"][$key]);
            break;
        }
    }

    // Remove product from the database cart (if applicable)
    $delete_sql = "DELETE FROM cart WHERE product_id = $remove_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Product removed successfully";
    } else {
        echo "Error removing product: " . $conn->error;
    }

    // Redirect to avoid resubmission
    header("Location: order.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate user input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($phone) || empty($address)) {
        die("Please fill in all fields.");
    }

    // Store delivery details in session
    $_SESSION['delivery_details'] = [
        'name' => $name,
        'phone' => $phone,
        'address' => $address
    ];

    // Generate a unique order ID
    $order_id = "ORD_" . time() . "_" . rand(1000, 9999);
    $status = "Pending"; // Default order status

    // Insert order details into the orders table for each item
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO orders (Order_id, product_id, quantity, price, product_name, status) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siidss", $order_id, $item['product_id'], $item['quantity'], $item['product_price'], $item['product_name'], $status);

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
    }

    // Store order ID in session
    $_SESSION['order_id'] = $order_id;

    // Redirect to payment page
    header("Location: Cancel_order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - The Coffee Hub</title>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
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

        /* Order Container */
        .order-container {
            max-width: 800px;
            margin: 100px auto 20px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .order-container h1 {
            color: #6F4E37;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
            position: relative;
        }

        .order-container h1::after {
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

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #6F4E37;
        }

        input[type="text"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        textarea:focus {
            border-color: #6F4E37;
            outline: none;
        }

        /* Order Summary */
        .order-summary {
            margin-top: 30px;
            border-top: 2px solid #6F4E37;
            padding-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #6F4E37;
            color: white;
            font-weight: 600;
        }

        .total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
            color: #6F4E37;
            font-weight: 600;
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

        /* Footer */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 0px;
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
            font-size: 15px;
            margin: 0 0px;
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

    <!-- Order Container -->
    <div class="order-container">
        <h1>Order Details</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
            </div>
            <div class="form-group">
                <label for="address">Delivery Address:</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₹<?php echo number_format($item['product_price'] ?? 0, 2); ?></td>
                            <td>₹<?php echo number_format(($item['product_price'] ?? 0) * ($item['quantity'] ?? 0), 2); ?></td>
                            <td>
                                <a href="order.php?remove=<?php echo $item['product_id']; ?>" class="btn-danger">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">
                    <strong>Total Amount: ₹<?php echo number_format($total_amount, 2); ?></strong>
                </div>
            </div>

            <button type="submit" class="btn">Proceed to Payment</button>
        </form>
    </div>

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