<?php
@include '../db_connect.php';
session_start();

// Add error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/Registration/admin/admin_error.log');

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['pass'];

    // Debug login attempt
    error_log("Login attempt - Email: $email, Password: $pass");

    if ($email === 'admin@gmail.com' && $pass === 'admin123') {
        // Set session variables
        $_SESSION = array(
            "login" => true,
            "id" => 1,
            "fname" => 'Admin',
            "lname" => 'User',
            "email" => $email,
            "role" => 'admin'
        );

        // Debug session
        error_log("Session set: " . print_r($_SESSION, true));

        // Handle remember me
        if (isset($_POST['remember'])) {
            setcookie('email', $email, time() + (86400 * 30), "/");
            setcookie('pass', $pass, time() + (86400 * 30), "/");
        }

        // Redirect with relative path
        header("Location: admin_dashboard.php");
        exit();
    } else {
        error_log("Invalid credentials: $email, $pass");
        echo "<script>
            alert('Invalid admin credentials');
            window.location.href='admin.php';
        </script>";
    }
}

$email = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$pass = isset($_COOKIE['pass']) ? $_COOKIE['pass'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: contain;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #212121;
            box-shadow: 0 0 0 0.2rem rgba(33, 33, 33, 0.25);
        }

        .btn-login {
            background-color: #212121;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #424242;
            color: white;
        }

        .links-container {
            text-align: center;
        }

        .links-container a {
            color: #212121;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .links-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <h1 class="mb-4">Admin Login</h1>
            <img src="../images/Readscape.png" alt="readscape" class="mb-4">
        </div>

        <form action="" method="post" autocomplete="off">
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Email" required>
            </div>

            <div class="mb-3">
                <input type="password" class="form-control" id="pass" name="pass"
                    placeholder="Password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-login">Login</button>
            <div class="links-container">
                <a href="../sign-in.php" class="text-center mt-4">Switch to user</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>