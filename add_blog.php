<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']); // Read the image file

        // Insert data into blog table
        $stmt = $conn->prepare("INSERT INTO blog (title, content, author, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $content, $author, $image);

        if ($stmt->execute()) {
            header("Location: add_blog.php?status=success");
            exit();
        } else {
            header("Location: add_blog.php?status=error&message=" . urlencode($stmt->error));
            exit();
        }

        $stmt->close();
    } else {
        header("Location: add_blog.php?status=error&message=No image uploaded.");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            margin-top: 0;
            color: #ffffff;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ffffff;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.8);
            color: #000;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        input[type="submit"] {
            background: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background: #218838;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .uploaded-image {
            text-align: center;
            margin-top: 20px;
        }

        .uploaded-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
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

        <div class="container">
            <h1>Add New Blog</h1>

            <!-- Display success/error messages -->
            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<div class="message success">Blog added successfully!</div>';

                    // Fetch the latest blog post to display the image
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT image FROM blog ORDER BY id DESC LIMIT 1";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo '<div class="uploaded-image">';
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" alt="Uploaded Image">';
                        echo '</div>';
                    }

                    $conn->close();
                } elseif ($_GET['status'] == 'error') {
                    $error_message = isset($_GET['message']) ? $_GET['message'] : 'An error occurred.';
                    echo '<div class="message error">Error: ' . htmlspecialchars($error_message) . '</div>';
                }
            }
            ?>

            <!-- Blog Form -->
            <form action="add_blog.php" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="10" required></textarea>

                <label for="author">Author:</label>
                <input type="text" id="author" name="author" required>

                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <input type="submit" value="Add Blog">
            </form>
        </div>
    </div>
</body>
</html>