<?php
include 'db_connect.php'; 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize search term
$search_term = "";
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Fetch products from the database
$sql = "SELECT product_id, name, price, image, stock FROM product";
if (!empty($search_term)) {
    $sql .= " WHERE name LIKE '%" . $conn->real_escape_string($search_term) . "%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Coffee Shop Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
              background: linear-gradient(135deg, #1e1e2f, #2a2a40);
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            background: rgba(44, 62, 80, 0.9);
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            padding: 20px 0;
            margin: 0;
            background: #0f0f1a;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
        }

        .sidebar ul li:hover {
            background: #0f0f1a;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        /* Header Styles */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        header h2 {
            margin: 0;
            font-size: 24px;
            color: #ffffff;
        }

        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-profile {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3498db;
        }

        .user-info h4 {
            margin: 0;
            font-size: 16px;
        }

        .user-info small {
            color: #ddd;
        }

        /* Products Header Styles */
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .products-header h3 {
            font-size: 24px;
            margin: 0;
            color: #ffffff;
        }

        .products-header button {
            background: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .products-header button:hover {
            background: #2ecc71;
        }

        /* Search Form Styles */
        .search-form {
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            width: 300px;
            margin-right: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .search-form button {
            padding: 10px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-form button:hover {
            background: #2980b9;
        }

        /* Table Styles */
        .products-table table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
            color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Subtle border */
            color: white; /* Ensure text color is white */
        }

        .products-table th {
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent header background */
            color: white;
            font-weight: bold;
        }

        /* Row Hover Effect */
        .products-table tbody tr {
            transition: background-color 0.3s;
        }

        .products-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.93); /* Hover effect */
            cursor: pointer;
        }

        /* Button Styles */
        .btn {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.3s;
        }

        .edit-btn {
            background: #f39c12;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Image Styles */
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Feedback Message Styles */
        .feedback {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            color: #6F4E37;
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
            <li><a href="admin_reviews.php"><i class="fas fa-sign-out-alt"></i> Reviews</a></li>
            <li><a href="add_blog.php"><i class="fas fa-sign-out-alt"></i> Blogs</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h2>Products</h2>
            <div class="user-wrapper">
                <img src="admin.jpg" alt="Admin" class="admin-profile">
                <div class="user-info">
                    <h4>Admin</h4>
                    <small>Super Admin</small>
                </div>
            </div>
        </header>

        <main>
            <div class="products-header">
                <h3>All Products</h3>
                <button id="addProductBtn">Add Product</button>
            </div>

            <!-- Search Form -->
            <div class="search-form">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search by Product Name" value="<?php echo htmlspecialchars($search_term); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['product_id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>₹" . number_format($row['price'], 2) . "</td>
                                        <td><img src='uploads/{$row['image']}' alt='{$row['name']}' class='product-img'></td>
                                        <td>{$row['stock']}</td>
                                        <td>
                                            <a href='edit_product.php?id={$row["product_id"]}' class='btn edit-btn'>Edit</a>
                                            <a href='delete_product.php?id={$row["product_id"]}' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No products available.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('addProductBtn').addEventListener('click', function() {
            alert("Redirecting to Add Product Page!");
            window.location.href = "add_product.php";
        });
    </script>

</body>
</html>