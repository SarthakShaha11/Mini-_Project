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
    <title>Blog - The Coffee Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f9f6f2, #e0d7cf);
            color: #4a2c2a;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6F4E37;
            padding: 2px 0px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header .logo img {
            width: 50px;
            height: auto;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .header .logo img:hover {
            transform: scale(1.05);
        }

        .navbar {
            display: flex;
            gap: 25px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding: 8px 12px;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #f9f6f2;
        }

        /* Icons */
        .icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icons i {
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .icons i:hover {
            color: #f9f6f2;
        }

        /* Blog Container */
        .container {
            max-width: 1000px;
            margin: 100px auto 20px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .container h1 {
            color: #6F4E37;
            font-size: 2.5em;
            margin-bottom: 40px;
            font-weight: 700;
            text-align: center;
            position: relative;
        }

        .container h1::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 4px;
            background: #6F4E37;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Blog Post Styles */
        .blog-post {
            margin-bottom: 40px;
            padding: 20px;
            border-radius: 10px;
            background: #f9f6f2;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .blog-post:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .blog-post img {
            max-width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .blog-post:hover img {
            transform: scale(1.05);
        }

        .blog-post h2 {
            margin-top: 0;
            color: #6F4E37;
            font-size: 1.8em;
            font-weight: 600;
        }

        .blog-post p {
            color: #4a2c2a;
            line-height: 1.6;
            max-width: 700px;
        }

        .blog-post strong {
            color: #6F4E37;
        }

        .blog-post em {
            color: #888;
            font-size: 0.9rem;
        }

        /* Read More Button */
        .read-more {
            background: #6F4E37;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .read-more:hover {
            background: #56372e;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 20px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
        }

        .footer p {
            margin: 0;
            font-size: 14px;
        }

        .footer .social-icons {
            margin-top: 10px;
        }

        .footer .social-icons a {
            color: white;
            font-size: 20px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: #f9f6f2;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            <a href="review.php">Reviews</a>
            <a href="blog.php">Blog</a>
            <a href="Admin_login.php">Admin</a>
        </nav>
        <div class="icons">
            <i class="fas fa-search"></i>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>

    <!-- Blog Container -->
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
                echo '<button class="read-more">Read More</button>';
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
        <p>&copy; <?php echo date("Y"); ?> The Coffee Hub. All rights reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>
</body>
</html>