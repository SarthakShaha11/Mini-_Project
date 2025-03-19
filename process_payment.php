<?php
require_once 'vendor/autoload.php'; // Include Stripe PHP SDK
session_start();

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51ABC1234XYZ567890'); // Replace with your actual Secret Key

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $total_amount = $_POST['total_amount'];
    $payment_method = $_POST['payment_method'];
    $paymentMethodId = $_POST['paymentMethodId'];

    try {
        // Create a PaymentIntent using the PaymentMethod ID
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $total_amount * 100, // Convert to cents
            'currency' => 'usd',
            'payment_method' => $paymentMethodId,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => 'Payment for Order ID: ' . $order_id,
        ]);

        // Payment successful
        $transaction_id = $paymentIntent->id;
        $payment_status = 'completed';

        // Insert payment details into the database
        $stmt = $conn->prepare("INSERT INTO payment (order_id, transaction_id, payment_method, price, payment_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $order_id, $transaction_id, $payment_method, $total_amount, $payment_status);
        $stmt->execute();
        $stmt->close();

        // Store payment details in session
        $_SESSION['payment_id'] = $transaction_id;
        $_SESSION['payment_status'] = $payment_status;

        // Redirect to a success page
        header("Location: payment_success.php");
        exit();
    } catch (\Stripe\Exception\CardException $e) {
        // Handle card errors
        $_SESSION['payment_error'] = $e->getError()->message;
        header("Location: payment_error.php");
        exit();
    } catch (Exception $e) {
        // Handle other errors
        $_SESSION['payment_error'] = $e->getMessage();
        header("Location: payment_error.php");
        exit();
    }
}
?>