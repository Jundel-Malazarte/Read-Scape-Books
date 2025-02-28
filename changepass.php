<?php
ob_start(); // Start output buffering
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit(); // Stop further execution
}

$user_id = $_SESSION['id'];

$sql = "SELECT fname, lname, profile_image FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $fname = htmlspecialchars($row['fname']);
    $lname = htmlspecialchars($row['lname']);
    $profile_image = htmlspecialchars($row['profile_image']);
} else {
    echo "User not found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
ob_end_flush(); // End buffering
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="./images/Readscape.png">
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

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logout {
            margin-left: auto;
        }

        .container {
            background: white;
            padding: 30px;
            margin-top: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin: 90px auto 0;
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
            gap: 20px;
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

        /*Side nav*/
        span {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .readscape {
            width: 40px;
            /* Match this size with the font-size of the menu icon */
            height: 40px;
            /* Keep height and width equal */
            border-radius: 50%;
        }

        /** Slider nav */
        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #212121;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: white;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            color: #f1f1f1;
        }

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">&#9776;</span>
            <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px" style="margin-left: 10px;">
        </div>

        <div class="profile-info">
            <a href="cart.php" style="position: relative; color: white; text-decoration: none;">
                🛒 Cart <span id="cart-counter" style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;">0</span>
            </a>
            <br>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="changepass.php">Change password</a>
    </div>

    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }
    </script>

    <div class="container">
        <h1>Change Password</h1>
        <form action="" method="post">
            <label for="oldpwd">Old Password</label>
            <input type="password" id="oldpwd" name="oldpwd" required>

            <label for="newpwd">New Password</label>
            <input type="password" id="newpwd" name="newpwd" required>

            <label for="conpwd">Confirm Password</label>
            <input type="password" id="conpwd" name="conpwd" required>

            <button type="submit" class="btn">Update Password</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            @include 'db_connect.php';

            $old_password = $_POST['old-password'];
            $new_password = $_POST['new-password'];
            $confirm_password = $_POST['confirm-password'];

            if ($new_password !== $confirm_password) {
                echo "<p style='color: red;'>New passwords do not match.</p>";
            } else {
                $sql = "SELECT pass FROM `users` WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_assoc($result)) {
                    if (password_verify($old_password, $row['pass'])) {
                        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql = "UPDATE `users` SET pass = ? WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "si", $new_password_hashed, $user_id);
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<script>alert('Password Updated Successfully!'); window.location.href='changepass.php';</script>";
                        } else {
                            echo "<p style='color: red;'>Error updating password.</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>Old password is incorrect.</p>";
                    }
                } else {
                    echo "<p style='color: red;'>User not found.</p>";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }
        ?>
    </div>
</body>
<script>
    function updateCartCounter() {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "cart_counter.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("cart-counter").innerText = xhr.responseText;
            }
        };
        xhr.send();
    }

    document.addEventListener("DOMContentLoaded", updateCartCounter);
</script>

</html>