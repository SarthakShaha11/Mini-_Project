<?php
include 'db_connect.php'; // Connect to the database

// Fetch product count
$productCountQuery = "SELECT COUNT(*) as total FROM product";
$productCountResult = $conn->query($productCountQuery);
$productCount = ($productCountResult && $productCountResult->num_rows > 0) ? $productCountResult->fetch_assoc()['total'] : 0;

// Fetch customer count
$customerCountQuery = "SELECT COUNT(*) as total FROM usertable";
$customerCountResult = $conn->query($customerCountQuery);
$customerCount = ($customerCountResult && $customerCountResult->num_rows > 0) ? $customerCountResult->fetch_assoc()['total'] : 0;

// Fetch order count
$orderCountQuery = "SELECT COUNT(order_id) as total FROM orders";
$orderCountResult = $conn->query($orderCountQuery);
$orderCount = ($orderCountResult && $orderCountResult->num_rows > 0) ? $orderCountResult->fetch_assoc()['total'] : 0;

// Fetch first 5 orders (using correct column names)
$recentOrdersQuery = "SELECT order_id, customer_name, product_name, price FROM orders ORDER BY order_id ASC LIMIT 5";
$recentOrdersResult = $conn->query($recentOrdersQuery);

// Fetch the latest 5 customers
$newCustomersQuery = "SELECT User_id, user_name, email FROM usertable ORDER BY User_id DESC LIMIT 5";
$newCustomersResult = $conn->query($newCustomersQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #8e44ad, #3498db);
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background: #2c3e50;
            color: rgb(16, 178, 219);
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            color: white;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px;
            text-align: left;
        }

        .sidebar ul li a {
            color: rgb(19, 161, 197);
            text-decoration: none;
            display: block;
        }

        .sidebar ul li a:hover {
            background: #34495e;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
            border: 2px solid white;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-single {
            background: #2980b9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 0 10px;
            transition: transform 0.3s;
            text-align: center;
            color: white; /* Ensure text is visible */
        }

        .card-single:hover {
            transform: translateY(-5px);
        }

        .recent-orders, .latest-customers {
            margin-top: 20px;
            background: white;
            color: black;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #2980b9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>The Coffee Hub</h2>
        <ul>
            <li><a href="Dashbord.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-coffee"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-receipt"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="report.php"><i class="fas fa-chart-line"></i> Report</a></li>
            <li><a href="admin_reviews.php"><i class="fas fa-sign-out-alt"></i> Reviews</a></li>
            <li><a href="add_blog.php"><i class="fas fa-sign-out-alt"></i> Blogs</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h2>Dashboard</h2>
            <div class="user-wrapper">
                <img src="admin.jpg" alt="Admin" class="admin-profile">
                <div>
                    <h4>Sarthak</h4>
                    <small>Super Admin</small>
                </div>
            </div>
        </header>

        <main>
            <div class="cards">
                <div class="card-single"><h3><?php echo $orderCount; ?></h3><span>Orders</span></div>
                <div class="card-single"><h3><?php echo $customerCount; ?></h3><span>Customers</span></div>
                <div class="card-single"><h3>$6,000</h3><span>Revenue</span></div>
                <div class="card-single"><h3><?php echo $productCount; ?></h3><span>Products</span></div>
            </div>

            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr><th>#</th><th>Order ID</th><th>Customer Name</th><th>Product Name</th><th>Price</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        if ($recentOrdersResult->num_rows > 0) {
                            while ($row = $recentOrdersResult->fetch_assoc()) {
                                echo "<tr><td>{$counter}</td><td>{$row['order_id']}</td><td>{$row['customer_name']}</td><td>{$row['product_name']}</td><td>₹{$row['price']}</td></tr>";
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No recent orders found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="latest-customers">
                <h2>Newly Joined Customers</h2>
                <table>
                    <thead><tr><th>#</th><th>User ID</th><th>Username</th><th>Email</th></tr></thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        while ($row = $newCustomersResult->fetch_assoc()) {
                            echo "<tr><td>{$counter}</td><td>{$row['User_id']}</td><td>{$row['user_name']}</td><td>{$row['email']}</td></tr>";
                            $counter++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>