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
    if ($conn->query($sql)) {
        echo "<p class='success-message'>Review submitted successfully!</p>";
    } else {
        echo "<p class='error-message'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Page</title>
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

        /* Header Styles */
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

        /* Review Form */
        .review-form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            margin: 100px auto 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .review-form h2 {
            text-align: center;
            color: #6F4E37;
            font-size: 2em;
            margin-bottom: 20px;
            font-weight: 700;
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
            transition: color 0.3s ease;
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
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #6F4E37;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #56372e;
            transform: translateY(-2px);
        }

        /* Reviews Display Section */
        .reviews-section {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.5s ease-in-out;
        }

        .reviews-section h2 {
            text-align: center;
            color: #6F4E37;
            font-size: 2em;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .review-item {
            margin-bottom: 20px;
            padding: 20px;
            border-bottom: 1px solid #eee;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .review-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-item p {
            margin: 0;
            font-size: 14px;
            color: #4a2c2a;
        }

        .review-item strong {
            color: #6F4E37;
            font-size: 16px;
        }

        .review-item small {
            color: #888;
            font-size: 12px;
        }

        /* User Avatars */
        .review-item .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #6F4E37;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin-right: 15px;
        }

        .review-item .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Testimonials Carousel */
        .testimonials-carousel {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px 0;
        }

        .testimonials-carousel .review-item {
            flex: 0 0 auto;
            width: 300px;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        .footer {
            text-align: center;
            background: #6F4E37;
            color: white;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
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

        /* Success and Error Messages */
        .success-message {
            color: green;
            text-align: center;
            margin: 10px 0;
        }

        .error-message {
            color: red;
            text-align: center;
            margin: 10px 0;
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
        <h2>Customer Reviews</h2>
        <div class="testimonials-carousel">
            <?php
            // Fetch reviews from the database
            $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='review-item'>";
                    echo "<div class='user-info'>";
                    echo "<div class='avatar'>" . substr($row['user_name'], 0, 1) . "</div>";
                    echo "<p><strong>" . htmlspecialchars($row['user_name']) . "</strong> - " . str_repeat("★", $row['rating']) . "</p>";
                    echo "</div>";
                    echo "<p>" . htmlspecialchars($row['review_text']) . "</p>";
                    echo "<small>Posted on: " . $row['created_at'] . "</small>";
                    echo "</div>";
                }
            } else {
                echo "<p>No reviews yet. Be the first to review!</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>
</body>
</html>