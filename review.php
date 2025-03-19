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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    // Insert review into the database
    $sql = "INSERT INTO reviews (user_name, rating, review_text) VALUES ('$user_name', '$rating', '$review_text')";
    if ($conn->query($sql)) { // Fixed: Added missing parenthesis
        echo "<p>Review submitted successfully!</p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Page</title>
    <style>
        /* Light coffee-themed CSS */
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

        .review-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review-form h2 {
            text-align: center;
            color: #5a3a22;
        }
        .rating {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: #ffcc00; /* Gold color for selected stars */
        }
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffcc00; /* Gold color on hover */
        }
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #5a3a22;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #3a2416;
        }
        .reviews-section {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .reviews-section h2 {
            text-align: center;
            color: #5a3a22;
        }
        .review-item {
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .review-item:last-child {
            border-bottom: none;
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
    <!-- Review Submission Form -->
    <div class="review-form">
        <h2>Leave a Review</h2>
        <form action="review.php" method="POST">
            <label for="user_name">Your Name:</label>
            <input type="text" id="user_name" name="user_name" required>

            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5">★</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">★</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">★</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">★</label>
            </div>

            <label for="review_text">Your Review:</label>
            <textarea id="review_text" name="review_text" rows="5" required></textarea>

            <input type="submit" value="Submit Review">
        </form>
    </div>

    <!-- Reviews Display Section -->
    <div class="reviews-section">
        <?php
        // Fetch reviews from the database
        $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Customer Reviews</h2>";
            while ($row = $result->fetch_assoc()) {
                echo "<div class='review-item'>";
                echo "<p><strong>" . htmlspecialchars($row['user_name']) . "</strong> - " . str_repeat("★", $row['rating']) . "</p>";
                echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
                echo "<small>Posted on: " . $row['created_at'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No reviews yet. Be the first to review!</p>";
        }
        ?>
    </div>
</body>
</html>