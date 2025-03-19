<?php
session_start();
// Initialize default date range (current month)
$start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-t'); // Last day of the current month

// Check if form is submitted with a custom date range
if (isset($_POST['filter'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch month-wise orders with product details within the date range
$query = "
    SELECT 
        DATE_FORMAT(o.order_date, '%M') AS month, 
        YEAR(o.order_date) AS year, 
        o.product_name,   -- Accessing directly from the orders table
        o.quantity,
        o.price,
        (o.quantity * o.price) AS total_price,
        o.order_date
    FROM orders o
    WHERE o.order_date BETWEEN '$start_date' AND '$end_date'
    ORDER BY o.order_date DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Orders Report</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #8e44ad, #3498db); /* Gradient Background */
            color: #ffffff;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(44, 62, 80, 0.9);
            color: rgb(16, 178, 219);
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
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
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background: #34495e;
        }

        /* Centered Content */
        .content {
            margin-left: 270px;
            padding: 20px;
        }

        h2 {
            margin-bottom: 10px;
        }
         
        /* Transparent Table */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.2); /* 80% transparent */
            color: white;
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(10px); /* Soft blur effect */
        }

        /* Table Header */
        th {
            background: rgba(76, 175, 80, 0.8);
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Transparent Table Cells */
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(0, 0, 0, 0.2);
        }

        /* Date Range Filter Form */
        .filter-form {
            margin: 20px auto;
            text-align: center;
        }

        input[type="date"], button {
            padding: 8px;
            margin: 5px;
            border: none;
            border-radius: 5px;
        }

        button {
            background: #4CAF50;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
<div class="main-content">
       
    <div class="sidebar">
        <h2>The Coffee Hub</h2>
        <ul>
            <li><a href="dashbord.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-coffee"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-receipt"></i> Orders</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="report.php" class="active"><i class="fas fa-chart-line"></i> Report</a></li>
            <li><a href="admin_reviews.php"><i class="fas fa-sign-out-alt"></i> Reviews</a></li>
            <li><a href="add_blog.php"><i class="fas fa-sign-out-alt"></i> Blogs</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Monthly Orders Report</h2>

        <!-- Date Range Filter Form -->
        <form method="POST" class="filter-form">
            <label>Start Date:</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
            <label>End Date:</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
            <button type="submit" name="filter">Filter</button>
        </form>

        <!-- Orders Table -->
        <table>
            <tr>
                <th>Date</th>
                <th>Month</th>
                <th>Year</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price ($)</th>
                <th>Total Price ($)</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo date('d', strtotime($row['order_date'])); ?></td>
                <td><?php echo $row['month']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo number_format($row['total_price'], 2); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
