<?php 
include 'db_connect.php'; 
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
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #8e44ad, #3498db);
            color: white;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styles */
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
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
        }
        
     
        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        /* Orders Table Styles */
        .orders-table {
            overflow-x: auto;
            border-radius: 10px;
            background: rgba(13, 108, 224, 0.2);
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: black;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
        }
        th, td {
            padding: 12px 15px; /* Add padding for better spacing */
            text-align: center; /* Center-align text */
            border-bottom: 1px solid #ddd; /* Add a subtle border between rows */
        }
        th {
            background-color: #3498db; /* Blue background for header */
            color: white; /* White text for header */
            font-weight: bold; /* Bold text for headers */
        }

        /* Table Row Styles */
        tr {
            transition: all 0.3s ease; /* Smooth transition for all properties */
            background-color:rgb(32, 206, 245); /* Default row background color */
        }
        tr:nth-child(even) {
            background-color:rgb(126, 155, 168); /* Light gray for even rows */
        }
        tr:hover {
            background-color:rgb(115, 21, 230); /* Light gray background on hover */
            transform: scale(1.02); /* Slightly scale up the row */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for a lifted effect */
        }

        /* Status Column Styles */
        .status {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            color: black !important; /* Ensure text is visible */
            background: rgba(224, 14, 193, 0.9) !important; /* Light background for contrast */
            border: 1px solid black; /* Add border for better visibility */
            transition: transform 0.3s ease, background-color 0.3s ease; /* Smooth transition for status */
        }
        .status.pending { background: #FFD700 !important; color: black !important; } /* Yellow */
        .status.completed { background: #2ECC71 !important; color: black !important; } /* Green */
        .status.cancelled { background: #E74C3C !important; color: black !important; } /* Red */
        .status:hover {
            transform: scale(1.1); /* Slightly scale up on hover */
            background-color: rgba(255, 255, 255, 0.7) !important; /* Lighten background on hover */
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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT 
                                o.order_id, 
                                o.quantity, 
                                o.order_date, 
                                o.price, 
                                o.status 
                            FROM orders o
                            ORDER BY o.order_id DESC
                            LIMIT 20 OFFSET 0
                        ";
                        $result = $conn->query($query);
                        $sr_no = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status = $row['status'] ? $row['status'] : "Pending"; // Default value if empty
                                echo "<tr>
                                    <td>{$sr_no}</td>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['order_date']}</td>
                                    <td>₹{$row['price']}</td>
                                    <td><span class='status'>{$status}</span></td>
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