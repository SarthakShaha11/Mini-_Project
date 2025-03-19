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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f5f1; /* Light coffee-inspired background */
            color: #4B3D3D; /* Dark brown text */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37; /* Coffee brown */
            padding: 2px 0px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header .logo img {
            width: 50px;
            height: auto;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .header .logo img:hover {
            transform: scale(1.1);
        }

        .navbar {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            padding: 8px 12px;
            transition: color 0.3s, background-color 0.3s;
            border-radius: 4px;
        }

        .navbar a:hover {
            color: #6F4E37;
            background-color: #fff;
        }

        .icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .icons a {
            color: white;
            font-size: 22px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .icons a:hover {
            color: #D7CCC8;
        }

        .bill-container {
            max-width: 900px;
            margin: 120px auto 40px; /* Adjusted for fixed header */
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1, h2 {
            font-family: 'Playfair Display', serif;
            color: #6F4E37;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #6F4E37;
            color: white;
            font-weight: 500;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .total {
            text-align: right;
            font-size: 1.3em;
            margin-top: 25px;
            color: #6F4E37;
            font-weight: 500;
        }

        .details-section {
            margin-top: 30px;
            border-top: 2px solid #6F4E37;
            padding-top: 20px;
        }

        .details-section p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }

        .btn {
            background: #6F4E37;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background 0.3s, transform 0.3s;
            margin-top: 20px;
        }

        .btn:hover {
            background: #5a3f2d;
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
        }

        @media screen and (max-width: 768px) {
            .bill-container {
                padding: 20px;
            }

            h1 {
                font-size: 28px;
            }

            h2 {
                font-size: 24px;
            }
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
        <a href="review.php">Reviews</a>
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
    &copy; <?php echo date("Y"); ?> The Coffee Hub. All rights reserved.
</div>
</body>
</html>