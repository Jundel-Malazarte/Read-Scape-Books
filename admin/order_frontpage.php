<?php
@include '../db_connect.php';

session_start();

// Ensure session user is set
if (!isset($_SESSION['id'])) {
    header("Location: ../admin/admin.php", true, 302);
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user details
$sql = "SELECT fname, lname, profile_image FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $fname = htmlspecialchars($row['fname']);
    $lname = htmlspecialchars($row['lname']);
    $profile_image = $row['profile_image'];

    // Ensure correct path to the image
    $default_image = '../uploads/default.jpg';
    if (empty($profile_image) || !file_exists("../uploads/" . $profile_image)) {
        $profile_image = $default_image;
    } else {
        $profile_image = '../uploads/' . $profile_image;
    }
} else {
    $fname = "User";
    $lname = "Not Found";
    $profile_image = '../uploads/default.jpg';
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Frontpage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./images/Readscape.png">
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

        .dashboard-wrapper {
            width: 80%;
            margin: 50px auto;
            display: flex;
            justify-content: flex-start;
            gap: 20px;
        }

        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .dashboard-container a {
            text-decoration: none;
        }

        .card {
            width: 300px;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 60px;
            height: 60px;
        }

        .card-content {
            flex-grow: 2;
            text-align: right;
        }

        .card p {
            margin: 5px 0;
            font-size: 20px;
            color: #333;
        }

        .card h2 {
            margin: 0;
            font-size: 24px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="../admin/admin_dashboard.php">Home</a>
        </div>
        <div class="profile-info">
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="../admin/logout.php">Log Out</a>
        </div>
    </div>
    <div class="dashboard-wrapper">
        <div class="dashboard-container">
            <a href="total_ordered_books.php">
                <div class="card">
                    <img src="../images/book_icon.png" alt="Book Icon">
                    <div class="card-content">
                        <p><strong>BOOKS</strong></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="dashboard-container">
            <a href="total_ordered_users.php">
                <div class="card">
                    <img src="../images/user_icon.png" alt="User Icon">
                    <div class="card-content">
                        <p><strong>USERS</strong></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</body>

</html>