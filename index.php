<?php
// index.php

// Database connection
$servername   = "localhost";
$db_username  = "root";
$db_password  = "";
$dbname       = "ch";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT product_id, name, price, image FROM Product";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>THE COFFEE HUB</title>
  <link rel="stylesheet" href="Style-1.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
  <style>
    /* General Styling */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9f6f2; /* Light beige background */
      margin: 0;
      padding: 0;
      color: #4a2c2a; /* Dark brown text */
      line-height: 1.6;
    }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #6F4E37; /* Coffee brown */
      padding: 0px 0px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
      color: #f9f6f2; /* Light beige */
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
      color: #f9f6f2; /* Light beige */
    }

    /* Hero Section */
    .home {
      text-align: center;
      padding: 180px 20px 100px;
      background: url('bg.jpg') no-repeat center center/cover;
      color: white;
      position: relative;
    }

    .home::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4); /* Dark overlay */
    }

    .home .content {
      position: relative;
      z-index: 1;
    }

    .home h1 {
      font-size: 56px;
      font-weight: 700;
      margin-bottom: 20px;
      animation: fadeIn 1.5s ease-in-out;
    }

    .home p {
      font-size: 18px;
      max-width: 800px;
      margin: 0 auto 30px;
      animation: fadeIn 2s ease-in-out;
    }

    .btn {
      display: inline-block;
      padding: 14px 32px;
      background: #6F4E37;
      color: white;
      border: none;
      cursor: pointer;
      text-decoration: none;
      font-size: 16px;
      border-radius: 5px;
      transition: background 0.3s ease, transform 0.3s ease;
      animation: fadeIn 2.5s ease-in-out;
    }

    .btn:hover {
      background: #4a2c2a; /* Darker brown */
      transform: translateY(-3px);
    }

    /* About Us Section */
    .about-us {
      padding: 80px 20px;
      background: white;
      text-align: center;
    }

    .about-us h2 {
      font-size: 36px;
      margin-bottom: 20px;
      color: #4a2c2a;
      animation: fadeIn 1s ease-in-out;
    }

    .about-us p {
      font-size: 18px;
      max-width: 800px;
      margin: 0 auto 40px;
      animation: fadeIn 1.5s ease-in-out;
    }

    .about-us .features {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .about-us .feature {
      background: #f9f6f2;
      padding: 20px;
      border-radius: 10px;
      width: 250px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .about-us .feature:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .about-us .feature h3 {
      font-size: 20px;
      margin-bottom: 10px;
      color: #4a2c2a;
    }

    .about-us .feature p {
      font-size: 14px;
      color: #6F4E37;
    }

    /* Center Button */
    .center-button {
      text-align: center;
      margin-top: 30px;
    }

    /* Footer */
    .footer {
      text-align: center;
      background: #6F4E37;
      color: white;
      padding: 20px;
      margin-top: 80px;
    }

    .footer p {
      margin: 0;
      font-size: 14px;
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

<header class="header">
  <a href="index.html" class="logo">
    <img src="logo.jpg" alt="The Coffee Hub Logo">
  </a>

  <nav class="navbar" aria-label="Main Navigation">
    <a href="index.php">Home</a>
    <a href="Product.php">Products</a>
    <a href="review.php">Reviews</a>
    <a href="blog.php">Blog</a>
    <a href="Admin_login.php">Admin</a>
  </nav>

  <div class="icons">
    <i class="fas fa-search" id="search-btn"></i>
    <a href="cart.php"><i class="fas fa-shopping-cart" id="cart-btn"></i></a>
  </div>
</header>

<section class="home" id="home">
  <div class="content">
    <h1>Fresh Coffee in the Morning</h1>
    <p>Experience the rich and bold flavors of our exquisite coffee blends, crafted to awaken your senses and start your day right. We roast the best organic coffee! Experience the highest quality 100% Arabica coffee beans from along the equatorial belt. We focus on roasting delicious certified organic coffee and Fair Trade Certified coffee. Indulge yourself and drink the best organic coffee!</p>
  </div>
</section>

<!-- About Us Section -->
<section class="about-us">
  <h2>About Us</h2>
  <p>What Makes Our Coffee Special? Lorem ipsum dolor sit amet consectetur adipiscing elit, voluptateci od Eu nisi, com frangent ligueris? Excepteur sint occaecat dolore eu? Temporibus Aenei? Quad Nemo Freshi. Ciguidrate. Ex, vel? Lorem ipsum dolor sit amet consectetur adipiscing elit. Calit amet form Quad Vientitia, Nitril vulputati. Colpis libere. Contestetur dicoercet.</p>
  <div class="features">
    <div class="feature">
      <h3>Fresh Coffee</h3>
      <p>✅ ✅ ✅ ✅ ✅ $15.99 o.m.</p>
    </div>
    <div class="feature">
      <h3>Organic Beans</h3>
      <p>Our coffee beans are 100% organic and sustainably sourced.</p>
    </div>
    <div class="feature">
      <h3>Fair Trade</h3>
      <p>We support fair trade practices to ensure ethical sourcing.</p>
    </div>
  </div>
  <!-- Centered Button -->
  <div class="center-button">
    <a href="Product.php" class="btn">Get it now</a>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2024 The Coffee Hub. All rights reserved.</p>
</footer>

<script src="Script1.js"></script>

</body>
</html>