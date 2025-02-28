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
    }

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Profile</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="icon" href="./images/Readscape.png">
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

        /* Action button profile */
        .action-button {
            display: flex;
            text-decoration: none;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }

        .action-button a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
        }

        .action-button a.edit-profile {
            background-color: #1e88e5;
        }

        .action-button a.deactive-account {
            background-color: #f41304;
        }
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
        <!-- Logo here! -->
        <div id="Sidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="changepass.php">Change password</a>
            <a href="cart.php">Cart</a>
        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">&#9776; <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>

        <script>
            function openNav() {
                document.getElementById("Sidenav").style.width = "240px";
            }

            function closeNav() {
                document.getElementById("Sidenav").style.width = "0";
            }
        </script>
         <div class="profile-info">
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="logout.php">Log Out</a>
        </div>
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
            <div class="action-button">
                <a href="edit-profile.php" class="edit-profile">Edit Profile</a>
                <a href="deactive.php" class="deactive-account">Deactive Account</a>
            </div>
        </div>
    </div>

</body>

</html>