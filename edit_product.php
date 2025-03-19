<?php
// Include the database connection file
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database
    $sql = "SELECT * FROM product WHERE Product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $price = $row['price'];
        $image = $row['image'];
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "Product ID not provided.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['name'];
    $new_price = $_POST['price'];

    // Handle image upload
    if ($_FILES['image']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $new_image = $_FILES['image']['name'];
    } else {
        $new_image = $image; // Keep the old image if no new image is uploaded
    }

    // Update product in the database
    $update_sql = "UPDATE product SET name = '$new_name', price = $new_price, image = '$new_image' WHERE Product_id = $product_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "Product updated successfully!";
        header("Location: products.php"); // Redirect back to the products page
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #8e44ad, #3498db);
            color: white;
            margin: 0;
            padding: 0;
        }

        .edit-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .edit-form h2 {
            text-align: center;
        }

        .edit-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .edit-form input[type="text"],
        .edit-form input[type="number"],
        .edit-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
        }

        .edit-form input[type="submit"] {
            background: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .edit-form input[type="submit"]:hover {
            background: #2ecc71;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>The Coffee Hub</h2>
        <ul>
            <li><a href="Dashbord.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-coffee"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-receipt"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="report.php"><i class="fas fa-sign-out-alt"></i> Report</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h2>Edit Product</h2>
            <div class="user-wrapper">
                <img src="admin.jpg" alt="Admin" class="admin-profile">
                <div class="user-info">
                    <h4>Admin</h4>
                    <small>Super Admin</small>
                </div>
            </div>
        </header>

        <main>
            <div class="edit-form">
                <h2>Edit Product</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>

                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $price; ?>" required>

                    <label for="image">Product Image:</label>
                    <input type="file" id="image" name="image">
                    <small>Current Image: <?php echo $image; ?></small>

                    <input type="submit" value="Update Product">
                </form>
            </div>
        </main>
    </div>

</body>
</html>