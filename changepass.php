<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="./images/icon.png">
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
            margin-top: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin: 90px auto 0; /* Adjusted margin to prevent navbar overlap */
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
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="./profile.php">Profile</a>
            <a href="#contact">Contact</a>
            <a href="changepass.php">Change password</a>
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

    <?php
    session_start();
    @include 'db_connect.php';

    if (!isset($_SESSION['id'])) {
        header("Location: sign-in.php");
        exit();
    }

    $id = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oldpwd = $_POST['oldpwd'];
        $newpwd = $_POST['newpwd'];
        $conpwd = $_POST['conpwd'];

        // Fetch current hashed password
        $sql = "SELECT pass FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $hashed_pass = $row['pass'];

            // Verify old password
            if (password_verify($oldpwd, $hashed_pass)) {
                // Check if new passwords match
                if ($newpwd === $conpwd) {
                    // Hash new password
                    $new_hashed_pass = password_hash($newpwd, PASSWORD_BCRYPT);

                    // Update password in the database
                    $update_sql = "UPDATE users SET pass = ? WHERE id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "si", $new_hashed_pass, $id);

                    if (mysqli_stmt_execute($update_stmt)) {
                        echo "<script>alert('Password updated successfully!'); window.location.href='profile.php';</script>";
                        exit();
                    } else {
                        echo "<script>alert('Error updating password: " . mysqli_error($conn) . "');</script>";
                    }

                    mysqli_stmt_close($update_stmt);
                } else {
                    echo "<script>alert('New passwords do not match.');</script>";
                }
            } else {
                echo "<script>alert('Your old password is incorrect!');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
    ?>


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
</html>