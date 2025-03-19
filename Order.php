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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37;
            padding: 5px 6px;
            position: fixed;
            width: 100%;
            top: 0;
        }

        .header .logo img {
            width: 50px;
            height: auto;
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
        }

        .order-container {
            max-width: 800px;
            margin: 100px auto 20px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #6F4E37;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #6F4E37;
        }

        input[type="text"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 16px;
        }

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
        }

        .total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
            color: #6F4E37;
        }

        .btn {
            background: #6F4E37;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background 0.3s, transform 0.3s;
        }

        .btn:hover {
            background: #5a3f2d;
            transform: scale(1.05);
        }

        .btn:active {
            transform: scale(0.95);
        }

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
    </style>
</head>
<body>
<header class="header">
  <a href="index.php" class="logo">
    <img src="logo.jpg" alt="The Coffee Hub Logo" />
  </a>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="product.php">Products</a>
    <a href="review.php">Reviwes</a>
    <a href="blog.php">Blog</a>
    <a href="Admin_login.php">Admin</a>
  </nav>
  <div class="icons">
    <a href="search.php"><i class="fas fa-search"></i></a>
    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
  </div>
</header>

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
 <div class="footer">
    &copy; <?php echo date("Y"); ?> Your Company. All rights reserved.
  </div>
</body>
</html>