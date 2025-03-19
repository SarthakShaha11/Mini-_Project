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

// Fetch blogs from the database
$sql = "SELECT id, title, content, author, created_at, image FROM blog ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <style>
           body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #c2b280;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37;
            padding: 5px 5px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .header .logo img {
            width: 50px;
            height: auto;
            border-radius: 50%;
        }

        .navbar {
            display: flex;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 10px;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #d9c9b2;
        }

        .icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icons i {
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .icons i:hover {
            color: #d9c9b2;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .blog-post {
            margin-bottom: 30px;
            padding: 20px;
            border-bottom: 1px solid #ccc;
        }
        .blog-post img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .blog-post h2 {
            margin-top: 0;
            color: #333;
        }
        .blog-post p {
            color: #555;
        }
        .blog-post strong {
            color: #333;
        }
        .blog-post em {
            color: #777;
            font-size: 0.9rem;
        }
            /* Footer */
    .footer {
      text-align: center;
      background: #6F4E37;
      color: white;
      padding: 0px;
      position: fixed;
      width: 100%;
      bottom: 0;
    }
    </style>
</head>
<body>
    
    <!-- Header -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="logo.jpg" alt="The Coffee Hub Logo" />
        </a>
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="Product.php">Products</a>  
            <a href="review.php">Reviwes</a>
            <a href="blog.php">Blog</a>
            <a href="Admin_login.php">Admin</a>
        </nav>
        <div class="icons">
            <i class="fas fa-search"></i>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>

    <div class="container">
        <h1>Blog Posts</h1>

        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo '<div class="blog-post">';
                if (!empty($row["image"])) {
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row["image"]) . '" alt="Blog Image">';
                }
                echo "<h2>" . $row["title"] . "</h2>";
                echo "<p>" . $row["content"] . "</p>";
                echo "<p><strong>Author:</strong> " . $row["author"] . "</p>";
                echo "<p><em>" . $row["created_at"] . "</em></p>";
                echo "</div>";
            }
        } else {
            echo "<p>No blogs found</p>";
        }

        $conn->close();
        ?>
    </div>
     <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
    </footer>
</body>
</html>