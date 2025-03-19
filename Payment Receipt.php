<?php
session_start();

// Check if session variables exist
if (!isset($_SESSION['order_id']) || !isset($_SESSION['total_amount'])) {
    header("Location: cart.php");
    exit();
}

$order_id = $_SESSION['order_id'];
$total_amount = $_SESSION['total_amount'];

require 'vendor/autoload.php'; // Ensure Stripe PHP Library is installed

// ✅ Correct Stripe API Keys (Use Test Keys from Stripe Dashboard)
\Stripe\Stripe::setApiKey('sk_test_51YOUR_SECRET_KEY_HERE'); // Replace with actual Secret Key
$publishableKey = 'pk_test_51YOUR_PUBLISHABLE_KEY_HERE'; // Replace with actual Publishable Key

// UPI Details
$upi_id = "yashdhumal890@okicici"; // Tumhari UPI ID
$upi_url = "upi://pay?pa=$upi_id&pn=Yash%20Dhumal&am=$total_amount&cu=INR&tn=Order%20Payment";
$qr_code_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($upi_url);

// Handle Stripe Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment_method']) && $_POST['payment_method'] === "stripe" && isset($_POST['stripeToken'])) {
        $token = $_POST['stripeToken'];
        $amount = floatval($total_amount) * 100; // Convert to cents

        try {
            $charge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Payment for Order #' . $order_id,
            ]);

            $_SESSION['payment_id'] = $charge->id;
            header("Location: payment_success.php"); // Redirect on success
            exit();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $error = $e->getMessage();
        }
    } elseif (isset($_POST['payment_method']) && $_POST['payment_method'] === "upi") {
        // Fake Success Message for UPI
        $_SESSION['payment_id'] = "UPI_FAKE_SUCCESS";
        header("Location: payment_success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .container { max-width: 400px; margin: auto; padding: 20px; border: 2px solid #ddd; border-radius: 10px; }
        img { margin-top: 10px; }
        .pay-btn { background: #007bff; color: white; padding: 10px 20px; font-size: 18px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }
        .pay-btn:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Choose Payment Method</h2>
        <p>Order ID: <?php echo htmlspecialchars($order_id); ?></p>
        <p>Total Amount: ₹<?php echo number_format(floatval($total_amount), 2, '.', ''); ?></p>
        
        <!-- Payment Method Selection -->
        <form id="payment-form" method="POST">
            <input type="radio" name="payment_method" value="stripe" checked onclick="togglePayment('stripe')"> Credit/Debit Card (Stripe)
            <br>
            <input type="radio" name="payment_method" value="upi" onclick="togglePayment('upi')"> UPI Payment
            <br><br>

            <!-- Stripe Payment Form -->
            <div id="stripe-payment">
                <div id="card-element"></div>
                <div id="card-errors" role="alert"></div>
                <input type="hidden" name="stripeToken" id="stripe-token">
            </div>

            <!-- UPI Payment Section -->
            <div id="upi-payment" style="display: none;">
                <p>Scan QR Code to Pay:</p>
                <img src="<?php echo $qr_code_url; ?>" alt="UPI QR Code">
                <br>
                <a class="pay-btn" href="<?php echo $upi_url; ?>">Pay Now via UPI</a>
            </div>

            <br>
            <button type="submit">Pay Now</button>
        </form>
    </div>

    <script>
        var stripe = Stripe('<?php echo $publishableKey; ?>');
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
        });

        document.getElementById('payment-form').addEventListener('submit', function(event) {
            var selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (selectedMethod === "stripe") {
                event.preventDefault();
                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        document.getElementById('card-errors').textContent = result.error.message;
                    } else {
                        document.getElementById('stripe-token').value = result.token.id;
                        event.target.submit();
                    }
                });
            }
        });

        function togglePayment(method) {
            if (method === "stripe") {
                document.getElementById('stripe-payment').style.display = "block";
                document.getElementById('upi-payment').style.display = "none";
            } else {
                document.getElementById('stripe-payment').style.display = "none";
                document.getElementById('upi-payment').style.display = "block";
            }
        }
    </script>
</body>
</html>
