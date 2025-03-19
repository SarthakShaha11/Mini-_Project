<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product ID is provided
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the cart
    $sql = "DELETE FROM cart WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Product removed successfully
        $_SESSION['message'] = "Product removed from cart.";
    } else {
        // Error removing product
        $_SESSION['message'] = "Error removing product from cart.";
    }

    $stmt->close();
} else {
    // No product ID provided
    $_SESSION['message'] = "No product selected for removal.";
}

$conn->close();

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>