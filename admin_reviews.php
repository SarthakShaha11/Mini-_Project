<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "ch";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reviews from the database
$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reviews</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #8e44ad, #3498db);
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            background: #2c3e50;
            color: rgb(16, 178, 219);
            width: 200px;
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
            margin-left: 200px; /* Adjusted to match sidebar width */
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

        h1 {
            text-align: center;
            color: #5a3a22;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #000; /* Ensure text is visible */
        }

        table th {
            background-color: #5a3a22;
            color: #fff;
        }

        table tr:hover {
            background-color: #f9f9f9;
        }

        .no-reviews {
            text-align: center;
            color: #777;
            margin-top: 20px;
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
        <h1>Admin Reviews</h1>
        <?php
        if ($result->num_rows > 0) {
            // Display reviews in a table
            echo "<table>";
            echo "<tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Rating</th>
                    <th>Review Text</th>
                    <th>Posted On</th>
                  </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                echo "<td>" . str_repeat("★", $row['rating']) . "</td>";
                echo "<td>" . htmlspecialchars($row['review_text']) . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='no-reviews'>No reviews found.</p>";
        }
        ?>
    </div>
</body>
</html>