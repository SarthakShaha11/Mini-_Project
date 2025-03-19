<?php
session_start(); // Start the session

// Check if necessary session variables exist
if (!isset($_SESSION['order_id']) || !isset($_SESSION['total_amount']) || !isset($_SESSION['cart_items'])) {
    // Redirect back to cart if session variables are missing
    header("Location: cart.php");
    exit();
}

// Initialize variables
$order_id = $_SESSION['order_id'];
$total_amount = $_SESSION['total_amount'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "creamy_delights"; // Corrected database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'] ?? ''; // Use null coalescing operator to avoid undefined key warning
    $price = $total_amount;
    $payment_status = 'Completed'; // Assuming payment is completed on form submission

    // Validate transaction_id
    if (empty($transaction_id)) {
        die("Transaction ID is required.");
    }

    // Insert payment data into the database
    $stmt = $conn->prepare("INSERT INTO payment (order_id, transaction_id, price, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $order_id, $transaction_id, $price, $payment_method, $payment_status);

    if ($stmt->execute()) {
        // Payment successfully inserted
        $payment_id = $stmt->insert_id;
        $_SESSION['payment_id'] = $payment_id;
        header("Location: payment_success.php"); // Redirect to a success page
        exit();
    } else {
        // Handle error
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch payment details if payment_id is set
$payment_id = isset($_SESSION['payment_id']) ? $_SESSION['payment_id'] : ''; 
$payment_details = null;

if ($payment_id) {
    $stmt = $conn->prepare("SELECT * FROM payment WHERE payment_id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_details = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>THE COFFEE HUB - Payment</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        justify-content: space-between; /* Space between logo and navbar */
        align-items: center;
        background: #6F4E37; /* Coffee brown */
        padding: 2px 0px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .header .logo {
        order: -1; /* Move logo to the left */
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
        justify-content: center; /* Center the navbar text */
        align-items: center;
        flex: 1; /* Take up remaining space */
        margin: 0 20px; /* Add spacing */
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

    .payment-container {
        max-width: 900px;
        margin: 120px auto 40px; /* Adjusted for fixed header */
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .tab {
        display: flex;
        justify-content: center;
        background-color: #6F4E37;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .tab button {
        background-color: inherit;
        color: white;
        padding: 14px 20px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s, transform 0.3s;
        flex: 1;
        text-align: center;
    }

    .tab button:hover {
        background-color: #5a3f2d;
    }

    .tab button.active {
        background-color: #4CAF50;
    }

    .tabcontent {
        display: none;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    input[type="text"], input[type="password"], input[type="month"], input[type="submit"], input[type="reset"], select {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        box-sizing: border-box;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    input[type="text"]:focus, input[type="password"]:focus, input[type="month"]:focus, select:focus {
        border-color: #6F4E37;
        box-shadow: 0 0 8px rgba(111, 78, 55, 0.3);
    }

    input[type="submit"], input[type="reset"] {
        background-color: #6F4E37;
        color: white;
        cursor: pointer;
        border: none;
        font-weight: 500;
        transition: background-color 0.3s, transform 0.3s;
    }

    input[type="submit"]:hover, input[type="reset"]:hover {
        background-color: #5a3f2d;
        transform: translateY(-2px);
    }

    .error-message {
        color: red;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid red;
        border-radius: 4px;
        background-color: #fff3f3;
    }

    @media screen and (max-width: 768px) {
        .payment-container {
            padding: 20px;
        }

        .tab button {
            padding: 10px;
            font-size: 14px;
        }
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

        /* Footer */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
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
  <script>
    function openPaymentMethod(evt, methodName) {
        var tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";  
        }
        var tablinks = document.getElementsByClassName("tablinks");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(methodName).style.display = "block";  
        evt.currentTarget.className += " active";
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("defaultOpen").click();
    });

    function showOrderPlaced() {
        var processingMsg = document.createElement('div');
        processingMsg.id = 'processingMessage';
        processingMsg.innerHTML = '<h3>Processing Payment...</h3><p>Please do not refresh or close this page.</p>';
        processingMsg.style.position = 'fixed';
        processingMsg.style.top = '50%';
        processingMsg.style.left = '50%';
        processingMsg.style.transform = 'translate(-50%, -50%)';
        processingMsg.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
        processingMsg.style.padding = '20px';
        processingMsg.style.borderRadius = '5px';
        processingMsg.style.boxShadow = '0 0 10px rgba(0,0,0,0.2)';
        processingMsg.style.zIndex = '1000';
        document.body.appendChild(processingMsg);

        var forms = document.getElementsByTagName('form');
        for(var i = 0; i < forms.length; i++) {
            forms[i].style.opacity = '0.5';
            forms[i].style.pointerEvents = 'none';
        }

        return true;
    }
  </script>
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
            <i class="fas fa-search"></i>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
</header>

<main class="payment-container">
    <div class="tab">
        <button class="tablinks" onclick="openPaymentMethod(event, 'CreditCard')" id="defaultOpen">Credit Card</button>
        <button class="tablinks" onclick="openPaymentMethod(event, 'DebitCard')">Debit Card</button>
        <button class="tablinks" onclick="openPaymentMethod(event, 'InternetBanking')">Internet Banking</button>
        <button class="tablinks" onclick="openPaymentMethod(event, 'WalletCashCard')">Wallet/Cash Card</button>
        <button class="tablinks" onclick="openPaymentMethod(event, 'COD')">Cash on Delivery</button>
    </div>

    <!-- Credit Card Tab -->
    <div id="CreditCard" class="tabcontent">
        <h3>Pay by Credit Card</h3>
        <form action="Bill.php" method="POST" onsubmit="return showOrderPlaced();">
            <input type="text" name="card" placeholder="Card Number" pattern="[0-9]{16}" required>
            <input type="month" name="month" placeholder="Expiration Date" required>
            <input type="text" name="cvv" placeholder="CVV/CVC" pattern="[0-9]{3,4}" required>
            <input type="text" name="name" placeholder="Card Holder Name" required>
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format(floatval($total_amount), 2, '.', ''); ?>">
            <input type="hidden" name="payment_method" value="Credit Card">
            <input type="hidden" name="transaction_id" value="<?php echo uniqid('CC_'); ?>">
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </div>

    <!-- Debit Card Tab -->
    <div id="DebitCard" class="tabcontent">
        <h3>Pay by Debit Card</h3>
        <form action="Bill.php" method="POST" onsubmit="return showOrderPlaced();">
            <input type="text" name="card" placeholder="Card Number" pattern="[0-9]{16}" required>
            <input type="month" name="month" placeholder="Expiration Date" required>
            <input type="text" name="cvv" placeholder="CVV/CVC" pattern="[0-9]{3,4}" required>
            <input type="text" name="name" placeholder="Card Holder Name" required>
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format(floatval($total_amount), 2, '.', ''); ?>">
            <input type="hidden" name="payment_method" value="Debit Card">
            <input type="hidden" name="transaction_id" value="<?php echo uniqid('DC_'); ?>">
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </div>

    <!-- Internet Banking Tab -->
    <div id="InternetBanking" class="tabcontent">
        <h3>Pay by Internet Banking</h3>
        <form action="Bill.php" method="POST" onsubmit="return showOrderPlaced();">
            <select name="bank_name" required>
                <option value="">Select Your Bank</option>
                <option value="State Bank of India">State Bank of India</option>
                <option value="HDFC Bank">HDFC Bank</option>
                <option value="ICICI Bank">ICICI Bank</option>
                <option value="Axis Bank">Axis Bank</option>
                <option value="Kotak Mahindra Bank">Kotak Mahindra Bank</option>
                <option value="Punjab National Bank">Punjab National Bank</option>
                <option value="Bank of Baroda">Bank of Baroda</option>
                <option value="IDBI Bank">IDBI Bank</option>
                <option value="Canara Bank">Canara Bank</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="account_number" placeholder="Account Number" pattern="[0-9]{9,18}" required>
            <select name="account_type" required>
                <option value="">Select Account Type</option>
                <option value="saving">Saving</option>
                <option value="current">Current</option>
            </select>
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format(floatval($total_amount), 2, '.', ''); ?>">
            <input type="hidden" name="payment_method" value="Internet Banking">
            <input type="hidden" name="transaction_id" value="<?php echo uniqid('IB_'); ?>">
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </div>

    <!-- Wallet/Cash Card Tab -->
    <div id="WalletCashCard" class="tabcontent">
        <h3>Pay by Wallet/Cash Card</h3>
        <form action="Bill.php" method="POST" onsubmit="return showOrderPlaced();">
            <input type="text" name="card" placeholder="Card Number" pattern="[0-9]{16}" required>
            <input type="password" name="pwd" placeholder="Enter Pin" pattern="[0-9]{4,6}" required>
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format(floatval($total_amount), 2, '.', ''); ?>">
            <input type="hidden" name="payment_method" value="Wallet/Cash Card">
            <input type="hidden" name="transaction_id" value="<?php echo uniqid('WC_'); ?>">
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </div>

    <!-- Cash on Delivery Tab -->
    <div id="COD" class="tabcontent">
        <h3>Cash on Delivery</h3>
        <form action="Bill.php" method="POST" onsubmit="return showOrderPlaced();">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format(floatval($total_amount), 2, '.', ''); ?>">
            <input type="hidden" name="payment_method" value="Cash on Delivery">
            <input type="hidden" name="transaction_id" value="<?php echo uniqid('COD_'); ?>">
            <input type="submit" value="Submit">
        </form>
    </div>
</main>

<footer class="footer">
    &copy; <?php echo date("Y"); ?> The Coffee Hub. All rights reserved.
</footer>
</body>
</html>