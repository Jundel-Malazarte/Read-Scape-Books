<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['id'];

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
    $fname = "Admin";
    $lname = "User";
    $profile_image = '../uploads/default.jpg';
}

mysqli_stmt_close($stmt);

// Fetch total users count
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) FROM users");
$total_users = mysqli_fetch_row($total_users_query)[0];

// Fetch all users' details (excluding password)
$users_query = mysqli_query($conn, "SELECT id, fname, lname, email, phone, address, created_at FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
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

        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            /* Rounded corners */
            overflow: hidden;
            /* Ensures border-radius applies to table */
        }

        th,
        td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #87CEEB;
            /* Baby blue */
            color: white;
            font-weight: bold;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
            /* Light grey for striped rows */
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }



        .delete-btn {
            display: inline-block;
            background-color: red;
            /* Red background */
            color: white;
            /* White text */
            padding: 10px 20px;
            /* Padding for the button */
            border-radius: 5px;
            /* Rounded corners */
            text-decoration: none;
            /* Remove underline from link */
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.3);
            /* Box shadow effect */
            transition: background-color 0.3s ease;
            /* Smooth transition for hover effect */
            margin-top: 10px;
            /* Space between elements */
        }

        .delete-btn:hover {
            background-color: darkred;
            /* Darker red on hover */
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

    <div class="container">
        <h1>Total Users: <?php echo $total_users; ?></h1>

        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Registration Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($users_query)) {
                    echo "<tr>
                        <td>{$row['fname']} {$row['lname']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['created_at']}</td>
                        <td><a href='../admin/delete.php?id={$row['id']}' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a></td>
                      </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>

<?php
mysqli_close($conn);
?>