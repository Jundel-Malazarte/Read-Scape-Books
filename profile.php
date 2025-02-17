<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <link rel="icon" href="./images/icon.png">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            /* Light background for better contrast */
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

        /* Profile Card Styles */
        .profile-card {
            width: 1000px;
            /* Adjusted width */
            margin: 30px auto;
            /* Centered with more top margin */
            padding: 20px;
            background-color: #fff;
            /* White background for the card */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Subtle shadow effect */
            text-align: center;
            /* Center content within the card */
        }

        .profile-card {
            width: 1000px;
            /* Adjusted width */
            margin: 30px auto;
            /* Centered with more top margin */
            padding: 20px;
            background-color: #fff;
            /* White background for the card */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Subtle shadow effect */
            text-align: center;
            /* Center content within the card */
        }

        .profile-card img {
            width: 180px;
            /* Adjusted size */
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            /* Increased spacing */
            /* Ensures proper margin */
            margin-left: auto;
            margin-right: auto;
            display: block;
            /* Remove automatic margin */
        }

        .profile-card h2 {
            margin-top: 0;
            margin-bottom: 10px;
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

        /* Profile Details Styles */
        .profile-details {
            text-align: center;
            /* Center the text */
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="#contact">Contact</a>
            <a href="./changepass.php">Change password</a>
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

            $sql = "SELECT fname, lname FROM `users` WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $fname = htmlspecialchars($row['fname']);
                $lname = htmlspecialchars($row['lname']);

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
    <div class="container">

    </div>

    <?php
    @include 'db_connect.php';


    if (!isset($_SESSION['id'])) {
        header("Location: sign-in.php");
        exit();
    }

    $user_id = $_SESSION['id'];

    $sql = "SELECT fname, lname, profile_image, email FROM `users` WHERE id = ?"; //added email
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $fname = htmlspecialchars($row['fname']);
        $lname = htmlspecialchars($row['lname']);
        $profile_image = htmlspecialchars($row['profile_image']);
        $email = htmlspecialchars($row['email']); //added email
    } else {
        echo "User not found.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>

    <div class="profile-card">
        <div>
            <h2>User Profile</h2>
        </div>


        <img src="<?php echo $profile_image ? $profile_image : 'uploads/default.jpg'; ?>" alt="Profile Picture">
        <div class="profile-details">
            <p><strong>First Name:</strong> <?php echo "$fname "; ?></p>
            <p><strong>Last Name:</strong> <?php echo "$lname"; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        </div>

    </div>

</body>

</html>