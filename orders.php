<?php 
include 'db_connect.php'; 

// Initialize the search variable
$search_product_id = '';
if (isset($_GET['search_product_id'])) {
    $search_product_id = $_GET['search_product_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Coffee Shop Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            background: #1a1a2e;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
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
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
            transition: color 0.3s;
        }
        .sidebar ul li a:hover {
            color: #3498db;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
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
            color: white;
        }

        /* Orders Table Styles */
        .orders-table {
            overflow-x: auto;
            border-radius: 10px;
            background: rgba(15, 202, 239, 0.9);
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: purple;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        /* Table Row Styles */
        tr {
            transition: all 0.3s ease;
            background-color: rgba(9, 212, 230, 0.89);
        
        }
        tr:nth-child(even) {
            background-color: rgba(9, 109, 230, 0.05);
        }
        tr:hover {
            background-color: rgba(38, 166, 239, 0.92);
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Search Form Styles */
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            width: 200px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .search-form input[type="submit"] {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-form input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>The Coffee Hub</h2>
        <ul>
            <li><a href="Dashbord.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-coffee"></i> Products</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-receipt"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="report.php"><i class="fas fa-file-alt"></i> Report</a></li>
            <li><a href="admin_reviews.php"><i class="fas fa-sign-out-alt"></i> Reviews</a></li>
            <li><a href="add_blog.php"><i class="fas fa-sign-out-alt"></i> Blogs</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Orders</h2>
        </header>
        <main>
            <div class="orders-header">
                <h3>All Orders</h3>
                <!-- Search Form -->
                <form class="search-form" method="GET" action="">
                    <input type="text" name="search_product_id" placeholder="Enter Product ID" value="<?php echo htmlspecialchars($search_product_id); ?>">
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Order ID</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Price (₹)</th>
                            <th>Product ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Build the SQL query based on the search input
                        $query = "
                            SELECT 
                                o.id, 
                                o.Order_id, 
                                o.quantity, 
                                o.order_date, 
                                o.price, 
                                o.product_id 
                            FROM orders o
                        ";
                        if (!empty($search_product_id)) {
                            $query .= " WHERE o.product_id = " . intval($search_product_id);
                        }
                        $query .= " ORDER BY o.id DESC LIMIT 10 OFFSET 0";

                        $result = $conn->query($query);
                        $sr_no = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$sr_no}</td>
                                    <td>{$row['Order_id']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['order_date']}</td>
                                    <td>₹{$row['price']}</td>
                                    <td>{$row['product_id']}</td>
                                </tr>";
                                $sr_no++;
                            }
                        } else {
                            echo "<tr><td colspan='6'>No orders found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>