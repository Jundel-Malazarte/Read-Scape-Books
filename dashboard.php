<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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

        .container {
            margin-left: 2rem;
        }

        /* Profile Card Styles */
        .profile-card {
            width: 400px;
            /* Adjusted width */
            margin: 30px auto;
            /* Centered with more top margin */
            padding: 20px;
            background-color: #fff;
            /* White background for the card */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Subtle shadow effect */
        }

        .profile-card img {
            width: 120px;
            /* Adjusted size */
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            /* Increased spacing */
            display: block;
            /* Ensures proper margin */
            margin-left: 0;
            /* Remove automatic margin */
        }

        .profile-card h2 {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
            color: #333;
        }

        .profile-card p {
            margin: 8px 0;
            text-align: left;
            color: #555;
        }

        .profile-card strong {
            font-weight: bold;
            color: #333;
        }

        /* Added container for aligning image and text to the left */
        .profile-content {
            display: flex;
            align-items: center;
            /* Vertically align image and text */
        }

        .profile-content img {
            margin-right: 20px;
            /* Add spacing between image and text */
        }

        .profile-details {
            text-align: left;
            /* Align the text to the left */
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
        <div class="profile-info">
            <?php
            @include 'db_connect.php';

            session_start();

            if (!isset($_SESSION['id'])) {
                // Redirect to login page if not logged in
                header("Location: sign-in.php", true, 302);
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
                $profile_image = $profile_image ? $profile_image : 'uploads/default.jpg';

                echo "<img src='$profile_image' alt='Profile Image'>";
                echo "<a href='profile.php'>$fname $lname</a>";
            } else {
                echo "User not found.";
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            ?>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <div class="form">
            <h1>Welcome, <?php echo $fname . " " . $lname; ?></h1>
        </div>
    </div>

</body>

</html>