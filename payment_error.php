<?php
session_start();

if (!isset($_SESSION['payment_error'])) {
    header("Location: index.php");
    exit();
}

$error_message = $_SESSION['payment_error'];
unset($_SESSION['payment_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Error</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        text-align: center;
        padding: 50px;
    }
    .error-message {
        background-color: #f44336;
        color: white;
        padding: 20px;
        border-radius: 5px;
        display: inline-block;
    }
  </style>
</head>
<body>
  <div class="error-message">
    <h2>Payment Failed</h2>
    <p><?php echo htmlspecialchars($error_message); ?></p>
    <a href="index.php" style="color: white; text-decoration: underline;">Try Again</a>
  </div>
</body>
</html>