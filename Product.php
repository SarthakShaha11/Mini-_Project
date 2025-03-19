<?php
session_start();

// Database connection
$servername   = "localhost";
$db_username  = "root";
$db_password  = "";
$dbname       = "ch";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database, including stock column
$sql = "SELECT product_id, name, price, image, stock FROM product";
$result = $conn->query($sql);

// Fetch cart data from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>THE COFFEE HUB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f9f6f2, #e0d7cf);
            margin: 0;
            padding: 0;
            color: #4a2c2a;
            line-height: 1.6;
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

        /* Products Section */
        .products {
            text-align: center;
            padding: 150px 20px 80px;
            margin-top: 60px;
        }

        .products h1 {
            color: #6F4E37;
            font-size: 2.8em;
            margin-bottom: 20px;
            font-weight: 700;
            animation: fadeIn 1s ease-in-out;
        }

        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            justify-content: center;
            padding: 20px;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .box img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .box h3 {
            margin: 10px 0;
            color: #6F4E37;
            font-size: 1.6em;
            font-weight: 600;
        }

        .price {
            color: #6F4E37;
            font-size: 1.4em;
            font-weight: bold;
            margin: 10px 0;
            display: block;
        }

        .stock {
            font-size: 1.1em;
            font-weight: bold;
            color: green;
            margin-bottom: 10px;
        }

        .out-of-stock {
            color: red;
        }

        .quantity-input {
            width: 80px;
            padding: 8px;
            border: 1px solid #6F4E37;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .btn {
            background: #6F4E37;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background: #56372e;
            transform: translateY(-2px);
        }

        .btn:disabled {
            background: #a89f9b;
            cursor: not-allowed;
        }

        .error-message {
            color: red;
            font-size: 14px;
            display: none;
            margin-top: 5px;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 80px 20px;
            background: white;
            text-align: center;
        }

        .testimonials h2 {
            color: #6F4E37;
            font-size: 2.5em;
            margin-bottom: 40px;
            font-weight: 700;
        }

        .testimonial-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .testimonial {
            background: #f9f6f2;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .testimonial:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .testimonial img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .testimonial p {
            font-size: 14px;
            color: #6F4E37;
            margin-bottom: 10px;
        }

        .testimonial h4 {
            font-size: 16px;
            color: #4a2c2a;
            font-weight: 600;
        }

        /* Newsletter Section */
        .newsletter {
            padding: 60px 20px;
            background: #6F4E37;
            color: white;
            text-align: center;
        }

        .newsletter h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .newsletter p {
            font-size: 16px;
            margin-bottom: 30px;
        }

        .newsletter form {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .newsletter input[type="email"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 300px;
            font-size: 16px;
        }

        .newsletter .btn {
            background: white;
            color: #6F4E37;
            font-weight: 600;
        }

        .newsletter .btn:hover {
            background: #f9f6f2;
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

    <!-- Products Section -->
    <section class="products" id="products">
        <h1>Our Products</h1>

        <div class="box-container">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $product_id = $row["product_id"];
                    $available_stock = $row["stock"];

                    // Check if the product is already in the cart
                    $cart_quantity = isset($cart[$product_id]) ? $cart[$product_id] : 0;
                    $remaining_stock = $available_stock - $cart_quantity;

                    // Disable the form if the product is out of stock
                    $is_out_of_stock = ($remaining_stock < 1);
                    ?>
                    <div class="box">
                        <img src="<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["name"]); ?>" />
                        <h3><?php echo htmlspecialchars($row["name"]); ?></h3>
                        <p class="price">₹<?php echo number_format($row["price"], 2); ?></p>
                        <p class="stock">Stock Available: 
                            <span class="<?php echo ($remaining_stock < 1) ? 'out-of-stock' : ''; ?>">
                                <?php echo $remaining_stock; ?>
                            </span>
                        </p>

                        <form action="cart.php" method="POST" onsubmit="return validateQuantity(this, <?php echo $remaining_stock; ?>);">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($row["name"]); ?>">
                            <input type="hidden" name="price" value="<?php echo $row["price"]; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $remaining_stock; ?>" class="quantity-input" <?php echo $is_out_of_stock ? 'disabled' : ''; ?>>
                            <p class="error-message">⚠ Quantity cannot exceed available stock!</p>
                            <button type="submit" class="btn" <?php echo $is_out_of_stock ? 'disabled' : ''; ?>>
                                <?php echo $is_out_of_stock ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonial-container">
            <div class="testimonial">
                <img src="customer1.jpg" alt="Customer 1" />
                <p>"The best coffee I've ever had! Highly recommend!"</p>
                <h4>- John Doe</h4>
            </div>
            <div class="testimonial">
                <img src="customer2.jpg" alt="Customer 2" />
                <p>"Amazing flavors and excellent service. Will definitely come back!"</p>
                <h4>- Jane Smith</h4>
            </div>
            <div class="testimonial">
                <img src="customer3.jpg" alt="Customer 3" />
                <p>"Perfect start to my day. Love the organic options!"</p>
                <h4>- Mike Johnson</h4>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <h2>Subscribe to Our Newsletter</h2>
        <p>Get the latest updates, exclusive offers, and more!</p>
        <form action="#" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required />
            <button type="submit" class="btn">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
        <div class="social-icons">
            <a href="https://m.facebook.com/profile.php?id=100081919247202"><i class="fab fa-facebook-f"></i></a>
            <a href="https://x.com/Yashjain854?t=CsVfBFW7omjEtLinx_MJOA&s=09"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/sarthakshah2005/?hl=en"><i class="fab fa-instagram"></i></a>
            <a href="https://www.linkedin.com/in/sarthak-shaha-74b728357?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app "><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>

    <script>
    function validateQuantity(form, stock) {
        var quantityInput = form.querySelector(".quantity-input");
        var errorMessage = form.querySelector(".error-message");

        if (quantityInput.value > stock) {
            errorMessage.textContent = "⚠ Quantity cannot exceed available stock (" + stock + ")!";
            errorMessage.style.display = "block";
            return false;
        } else if (quantityInput.value < 1) {
            errorMessage.textContent = "⚠ Quantity must be at least 1!";
            errorMessage.style.display = "block";
            return false;
        } else {
            errorMessage.style.display = "none";
            return true;
        }
    }
    </script>

</body>
</html>