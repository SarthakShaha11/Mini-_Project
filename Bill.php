<?php
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Redirect if no order details exist
if (!isset($_SESSION['order_id']) || !isset($_SESSION['delivery_details'])) {
    header("Location: order.php"); // Redirect to order page if no details are found
    exit();
}

// Get session data
$order_id = $_SESSION['order_id'];
$delivery_details = $_SESSION['delivery_details'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details from database
$sql = "SELECT product_name, quantity, price FROM orders WHERE Order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity']; // Calculate subtotal
    $total_amount += $row['subtotal']; // Update total amount
    $cart_items[] = $row; // Store order details in an array
}

$stmt->close();
$conn->close();

// Fetch payment details from session
$payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : 'Cash on Delivery';
$transaction_id = isset($_SESSION['transaction_id']) ? $_SESSION['transaction_id'] : 'N/A';

// Clear session data (optional)
unset($_SESSION['cart_items']);
unset($_SESSION['total_amount']);
unset($_SESSION['delivery_details']);
unset($_SESSION['payment_method']);
unset($_SESSION['transaction_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Bill - The Coffee Hub</title>
    <style>
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
            padding: 5px 7px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .header .logo img {
            width: 50px;
            height: auto;
            border-radius: 50%;
        }

        .navbar {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 10px;
            transition: color 0.3s, background-color 0.3s;
        }

        .navbar a:hover {
            color: #6F4E37;
            background-color: #fff;
            border-radius: 4px;
        }

        .icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .icons a {
            color: white;
            font-size: 20px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .icons a:hover {
            color: #c2b280;
        }

        .bill-container {
            max-width: 800px;
            margin: 80px auto 20px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #e6f7ff;
        }
        h1, h2 {
            color: #6F4E37;
            text-align: center;
        }
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
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
            color: #6F4E37;
        }
        .details-section {
            margin-top: 30px;
            border-top: 2px solid #6F4E37;
            padding-top: 20px;
        }
        .details-section h2 {
            margin-bottom: 10px;
        }
        .details-section p {
            margin: 5px 0;
            font-size: 16px;
        }
        .btn {
            background: #6F4E37;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        .btn:hover {
            background: #5a3f2d;
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
    <div class="bill-container">
        <h1>Order Bill</h1>

        <!-- Delivery Details -->
        <div class="details-section">
            <h2>Delivery Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($delivery_details['name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($delivery_details['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($delivery_details['address']); ?></p>
        </div>

        <!-- Payment Details -->
        <div class="details-section">
            <h2>Payment Details</h2>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
            <?php if ($transaction_id !== 'N/A'): ?>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <h2>Order Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                    <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total">
            <strong>Total Amount: ₹<?php echo number_format($total_amount, 2); ?></strong>
        </div>

        <!-- Confirmation Message -->
        <div class="details-section">
            <h2>Thank You for Your Order!</h2>
            <p>Your order has been successfully placed. We will process it shortly and deliver it to the provided address.</p>
        </div>

        <!-- Button to Return to Home -->
        <button class="btn" onclick="window.location.href='index.php'">Return to Home</button>
    </div>
<!-- Footer -->
<div class="footer">
    &copy; <?php echo date("Y"); ?> Your Company. All rights reserved.
  </div>
</body>
</html>
