<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Dashboard</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
         <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }
        .navbar a:hover {
            background-color: #555;
            border-radius: 5px;
        }
        .nav-links {
            display: flex;
            gap: 15px;
        }
        .logout {
            margin-left: auto;
        }
        .container {
            margin-left: 2rem;
        }
    </style>
    </head>
    <body>
        <!-- <nav><h1>Welcome to Dashboard</h1>
        <button><a href="sign-in.php">Logout</a></button>
        <script src="" async defer></script></nav> -->

        <div class="navbar">
        <div class="nav-links">
            <a href="#menu">Menu</a>
            <a href="#profile">Profile</a>
            <a href="#contact">Contact</a>
            <a href="#account">Change password</a>
        </div>
        <div>
            <a href=""><?php
                @include 'db_connect.php';

                session_start();
                

                if (!isset($_SESSION['id'])) {
                    // Redirect to login page if not logged in
                    header("Location: sign-in.php");
                    exit();
                }

                $user_id = $_SESSION['id']; // Get logged-in user's ID

                $sql = "SELECT fname, lname FROM `users` WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_assoc($result)) {
                    $fname = htmlspecialchars($row['fname']);
                    $lname = htmlspecialchars($row['lname']);

                    echo "Welcome, $fname $lname!";
                } else {
                    echo "User not found.";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            ?></a>
            <a href="sign-in.php">Log Out</a></div>
        </div>
        <div class="container">
            
        </div>
    </body>
</html>