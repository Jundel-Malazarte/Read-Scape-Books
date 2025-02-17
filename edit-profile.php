<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Profile</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./images/icon.png">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 5px;
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
            gap: 10px;
        }

        .logout {
            margin-left: auto;
        }

        .container {
            margin-left: 2rem;
        }

        /* Profile Card Styles */
        .profile-card {
            width: 400px;
            margin: 10px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .profile-card img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 5px;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }

        .profile-card h2 {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .profile-card p {
            margin: 8px 0;
            text-align: center;
            color: #555;
        }

        .profile-card strong {
            font-weight: bold;
            color: #333;
        }

        .profile-details {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            width: 100%;
            color: #444;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        input[type="text"]:focus, input[type="file"]:focus {
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

        .cancel, .cancel a {
            margin-top: 10px; 
            background-color: #f41304;
        }

        .cancel:hover {
            background-color: #f41304;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="#contact">Contact</a>
            <a href="changepass.php">Change password</a>
        </div>
        <div>
            <?php
            @include 'db_connect.php';

            session_start();

            if (!isset($_SESSION['id'])) {
                // Redirect to login page if not logged in
                header("Location: sign-in.php");
                exit();
            }

            $user_id = $_SESSION['id']; // Get logged-in user's ID

            $sql = "SELECT fname, lname, profile_image FROM `users` WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $fname = htmlspecialchars($row['fname']);
                $lname = htmlspecialchars($row['lname']);
                $profile_image = htmlspecialchars($row['profile_image']);
                echo "<a href='#' >Welcome, $fname $lname!</a>"; //added anchor tag
            } else {
                echo "User not found.";
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            ?>
            <a href="sign-in.php">Log Out</a>
        </div>
    </div>
    <div class="profile-card">
        <div>
            <h2>Update Profile</h2>
        </div>
        <img src="<?php echo $profile_image ? $profile_image : 'uploads/default.jpg'; ?>" alt="Profile Picture">
        <div class="profile-details">
            <form action="update-profile.php" method="post" enctype="multipart/form-data">
                <label for="fname"><strong>First Name:</strong></label>
                <input type="text" id="fname" name="fname" value="<?php echo $fname; ?>" required>
                <br>
                <label for="lname"><strong>Last Name:</strong></label>
                <input type="text" id="lname" name="lname" value="<?php echo $lname; ?>" required>
                <br>
                <label for="profile_image"><strong>Profile Image:</strong></label>
                <input type="file" id="profile_image" name="profile_image">
                <br>
                <div class="action-button">
                    <button type="submit" class="btn">Update</button>
                    <a href="profile.php" class="btn cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>