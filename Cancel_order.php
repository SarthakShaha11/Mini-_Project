<?php
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Check if order details exist in session
if (!isset($_SESSION['order_id']) || !isset($_SESSION['delivery_details'])) {
    header("Location: cart.php");
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['proceed_payment'])) {
        header("Location: payment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order - The Coffee Hub</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts for elegant typography -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f8f5f1;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #4E342E;
            padding: 2px 0px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            color: #4E342E;
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

        .order-container {
            max-width: 900px;
            margin: 120px auto 40px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: #4E342E;
            margin-bottom: 30px;
            font-weight: 600;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #4E342E;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .customer-details p {
            font-size: 18px;
            margin: 10px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4E342E;
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
            color: #4E342E;
            font-weight: 500;
        }

        .btn {
            background: #4E342E;
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
            margin: 10px;
        }

        .btn:hover {
            background: #3E2723;
            transform: scale(1.05);
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn-secondary {
            background: #D7CCC8;
            color: #4E342E;
        }

        .btn-secondary:hover {
            background: #BCAAA4;
        }

        /* Footer Styles */
        .footer {
            text-align: center;
            background: #4E342E;
            color: white;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
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

<div class="order-container">
    <h1>Confirm Your Order</h1>

    <div class="customer-details">
        <h2>Customer Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($delivery_details['name']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($delivery_details['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($delivery_details['address']); ?></p>
    </div>

    <div class="order-summary">
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
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
    </div>

    <form method="POST">
        <button type="submit" name="proceed_payment" class="btn">Proceed to Payment</button>
        <a href="cart.php" class="btn btn-secondary">Cancel Order</a>
    </form>
</div>

<!-- Footer -->
<div class="footer">
    &copy; <?php echo date("Y"); ?> The Coffee Hub. All rights reserved.
</div>
</body>
</html>