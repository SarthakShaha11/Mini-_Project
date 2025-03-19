<?php
// Include the database connection file
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the image file name from the database
    $sql = "SELECT image FROM product WHERE Product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image = $row['image'];

        // Delete the image file from the uploads folder
        if ($image && file_exists("uploads/$image")) {
            unlink("uploads/$image"); // Delete the file
        }

        // Delete the product from the database
        $delete_sql = "DELETE FROM product WHERE Product_id = $product_id";
        if ($conn->query($delete_sql) === TRUE) {
            echo "Product deleted successfully!";
        } else {
            echo "Error deleting product: " . $conn->error;
        }
    } else {
        echo "Product not found.";
    }
} else {
    echo "Product ID not provided.";
}

// Redirect back to the products page
header("Location: products.php");
exit();
?>