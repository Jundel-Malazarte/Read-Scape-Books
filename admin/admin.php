<?php
@include '../db_connect.php';
session_start();

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['pass'];

    // Check if admin exists
    $select = "SELECT * FROM admin_accounts WHERE username = '$username'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Verify hashed password
        if (password_verify($pass, $row["password"])) {
            $_SESSION["admin_login"] = true;
            $_SESSION["admin_id"] = $row["id"];
            $_SESSION["admin_username"] = $row["username"];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            // echo password_hash('admin1234@', PASSWORD_BCRYPT);
            echo "<script>alert('Incorrect username or password!');</script>";
            
        }
    } else {
        echo "<script>alert('Admin not found!');</script>";
    }
}
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
                <input type="text" class="form-control" id="username" name="username"
                    placeholder="Username" required>
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