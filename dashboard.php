<?php
@include 'db_connect.php';

session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
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

        .profile-card {
            width: 400px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            display: block;
            margin-left: 0;
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

        .profile-content {
            display: flex;
            align-items: center;
        }

        .profile-content img {
            margin-right: 20px;
        }

        .profile-details {
            text-align: left;
        }

        .book-card {
            width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .book-card img {
            width: 100px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 15px;
            display: block;
        }

        .book-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
            color: #333;
        }

        .book-card p {
            margin: 8px 0;
            text-align: left;
            color: #555;
        }

        .book-card strong {
            font-weight: bold;
            color: #333;
        }

        .book-content {
            display: flex;
            align-items: center;
        }

        .book-content img {
            margin-right: 20px;
        }

        .book-details {
            text-align: left;
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
            if (!isset($_SESSION['id'])) {
                header("Location: sign-in.php", true, 302);
                exit();
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
        <!-- 
            <div class="form">
            <h2>Welcome, <?php echo $fname . " " . $lname; ?></h2>
            </div>
        -->
        <h2>Picked for you</h2>
        <!-- book card -->
        <div class="book-card">
            <img src="./images/book1.png" alt="Harry Potter">
            <h4>Product Name</h4>
            <div>
                <span>$299</span>
                <button>+</button>
            </div>
        </div>
    </div>

</body>

</html>