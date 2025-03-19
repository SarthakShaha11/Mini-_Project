<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ch";

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Connect to Database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lockout System: Prevent Brute Force
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3) {
    if (time() - $_SESSION['last_attempt'] < 300) { // 5 min lockout
        $_SESSION['error'] = "Too many failed attempts. Try again in 5 minutes.";
        header("Location: Admin_login.php");
        exit();
    } else {
        unset($_SESSION['login_attempts'], $_SESSION['last_attempt']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $admin_username = trim(strtolower($_POST["username"]));
    $admin_password = $_POST["password"];

    $sql = "SELECT * FROM Admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($admin_password, $row["password"])) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_username"] = $admin_username;

            // Remember Me Feature
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('admin_remember', $token, [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
                $update_token = $conn->prepare("UPDATE Admin SET remember_token = ? WHERE username = ?");
                $update_token->bind_param("ss", $token, $admin_username);
                $update_token->execute();
                $update_token->close();
            }

            unset($_SESSION['login_attempts'], $_SESSION['last_attempt']);
            header("Location: Dashbord.php");
            exit();
        } else {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt'] = time();
            $_SESSION["error"] = "Invalid password!";
        }
    } else {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt'] = time();
        $_SESSION["error"] = "Admin not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Coffee Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #4b2c20, #9c6b43, #c69c72);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: rgba(255, 248, 240, 0.1);
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        h2 {
            color: #f4e7d1;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #f4e7d1;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        input:focus {
            border-color: #f4e7d1;
            background: rgba(255, 255, 255, 0.15);
            outline: none;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: #f4e7d1;
        }
        .remember-me input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #6f4e37, #9c6b43);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: linear-gradient(45deg, #9c6b43, #6f4e37);
            transform: translateY(-2px);
        }
        .error {
            background: rgba(255, 87, 87, 0.2);
            color: #ffd3d3;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .return-link {
            margin-top: 20px;
        }
        .return-link a {
            color: #f4e7d1;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .return-link a:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?></div>
        <?php endif; ?>

        <h2>Admin Login</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
            <div class="remember-me"><input type="checkbox" name="remember" id="remember"><label for="remember">Remember Me</label></div>
            <button type="submit">Login</button>
        </form>
        <div class="return-link"><a href="index.php">← Return to Home</a></div>
    </div>
</body>
</html>
