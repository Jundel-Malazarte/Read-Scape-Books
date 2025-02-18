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

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        input[type="file"]:focus {
            border-color: #1e88e5;
            outline: none;
        }

        .btn {
            width: 45%;
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
        

        .cancel,
        .cancel a {
            margin-top: 10px;
            background-color: #f41304;
        }

        .cancel:hover {
            background-color: #f41304;
            opacity: 0.8;
        }

        .action-button {
            display: flex;
            justify-content: center;
            gap: 15px;
            /* Adds space between buttons */
            width: 100%;
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
            <a href="sign-in.php">Log Out</a>
        </div>
    </div>
    <div class="profile-card">
        <div>
            <h2>Update Profile</h2>
        </div>
        <!-- Profile Image Preview -->
        <img id="profilePreview" src="<?php echo $profile_image ? $profile_image : 'uploads/default.jpg'; ?>" alt="Profile Picture">

        <div class="profile-details">
            <form action="update-profile.php" method="post" enctype="multipart/form-data">
                <!-- Profile Image Upload -->
                <label for="profile_image"><strong>Profile Image:</strong></label>
                <input type="file" id="profile_image" name="profile_image" accept="uploads/*" onchange="previewImage(event)">
                <br>

                <!-- First Name Field -->
                <label for="fname"><strong>First Name:</strong></label>
                <input type="text" id="fname" name="fname" value="<?php echo $fname; ?>" required>
                <br>

                <!-- Last Name Field -->
                <label for="lname"><strong>Last Name:</strong></label>
                <input type="text" id="lname" name="lname" value="<?php echo $lname; ?>" required>
                <br>

                <!-- Action Buttons -->
                <div class="action-button">
                    <button type="submit" class="btn">Update</button>
                    <a href="profile.php" class="btn cancel">Cancel</a>
                </div>
            </form>
        </div>

        <!-- JavaScript for Image Preview -->
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const profilePreview = document.getElementById('profilePreview');
                    profilePreview.src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>

    </div>
</body>

</html>