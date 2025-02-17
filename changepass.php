<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #eceff1;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
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
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin: 80px auto 0;
            /* Adjusted margin to prevent navbar overlap */
        }

        h1 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            width: 100%;
            color: #444;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #1e88e5;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #1e88e5;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background: #1e88e5;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="./profile.php">Profile</a>
            <a href="#contact">Contact</a>
            <a href="./changepass.php">Change password</a>
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
            <a href="sign-in.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <h1>Change Password</h1>
        <form action="" method="post">
            <label for="old-password">Old Password</label>
            <input type="password" id="old-password" name="old-password" required>

            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new-password" required>

            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</body>

</html>