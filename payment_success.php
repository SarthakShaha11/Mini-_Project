<?php
session_start();

if (!isset($_SESSION['payment_status']) || $_SESSION['payment_status'] !== 'completed') {
    header("Location: index.php");
    exit();
}

$transaction_id = $_SESSION['payment_id'];
$order_id = $_SESSION['order_id'];
$total_amount = $_SESSION['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Success</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        text-align: center;
        padding: 50px;
    }
    .success-message {
        background-color: #4CAF50;
        color: white;
        padding: 20px;
        border-radius: 5px;
        display: inline-block;
    }
  </style>
</head>
<body>
  <div class="success-message">
    <h2>Payment Successful!</h2>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
    <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
    <p><strong>Amount Paid:</strong> $<?php echo number_format(floatval($total_amount), 2); ?></p>
  </div>
</body>
</html>