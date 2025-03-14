<?php
@include '../db_connect.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['pass'];

    // Check if user exists
    $select = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Verify hashed password
        if (password_verify($pass, $row["pass"])) {
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            $_SESSION["fname"] = $row["fname"];

            // Set cookies if "Remember Me" is checked
            if (isset($_POST['remember'])) {
                setcookie('email', $email, time() + (86400 * 30), "/"); // 30 days
                setcookie('pass', $pass, time() + (86400 * 30), "/"); // 30 days
            } else {
                // Clear cookies if "Remember Me" is not checked
                setcookie('email', '', time() - 3600, "/");
                setcookie('pass', '', time() - 3600, "/");
            }

            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect email or password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}

// Retrieve email and password from cookies if they exist
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

        .options-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #212121;
        }

        .forgot-password a {
            color: #212121;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            text-decoration: underline;
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

        .switch-user {
            text-align: center;
            margin-top: 1rem;
        }

        .switch-user a {
            color: #212121;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .switch-user a:hover {
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
                <input type="text" class="form-control" id="email" name="email"
                    placeholder="Email" value="<?php echo $email; ?>" required>
            </div>

            <div class="mb-3">
                <input type="password" class="form-control" id="pass" name="pass"
                    placeholder="Password" value="<?php echo $pass; ?>" required>
            </div>

            <div class="options-container">
                <div class="remember-me">
                    <input type="checkbox" class="form-check-input" id="checkbox" name="remember"
                        <?php if ($email && $pass) echo 'checked'; ?>>
                    <label class="form-check-label" for="checkbox">Remember Me</label>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-login">Login</button>

            <div class="switch-user">
                <a href="../sign-in.php">Switch to User</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>