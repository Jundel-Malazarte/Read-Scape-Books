<?php

@include 'db_connect.php';

session_start();

if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id']; // Get logged-in user's ID

// Get pending orders count
$pending_orders_sql = "SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $pending_orders_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$pending_result = mysqli_stmt_get_result($stmt);
$pending_orders_count = mysqli_fetch_row($pending_result)[0];
mysqli_stmt_close($stmt);

// Update the SQL query to include email
$sql = "SELECT fname, lname, email, profile_image FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $fname = htmlspecialchars($row['fname']);
    $lname = htmlspecialchars($row['lname']);
    $email = htmlspecialchars($row['email']); // Add this line
    $profile_image = htmlspecialchars($row['profile_image']);
} else {
    // Handle case where user is not found
    header("Location: sign-in.php");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background-color: #212529;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1100;
            top: 0;
            left: 0;
            background-color: #212529;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
        }

        .sidenav a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            background-color: #343a40;
            color: #fff;
        }

        .profile-card {
            max-width: 800px;
            margin: 2rem auto;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            overflow: hidden;
        }

        .profile-header {
            background-color: #212529;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .profile-image {
            width: 180px;
            height: 180px;
            overflow: hidden;
            border-radius: 50%;
            border: 5px solid #fff;
            margin: -90px auto 1rem;
            margin-top: -0px;
            position: relative;
            z-index: 1;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            object-fit: cover;
            display: block;
        }

        .profile-body {
            padding: 1.5rem;
        }

        .profile-info {
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        .profile-info p {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .profile-info strong {
            color: #212529;
            min-width: 120px;
            display: inline-block;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .btn-edit {
            background-color: #0d6efd;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-deactivate {
            background-color: #dc3545;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-edit:hover,
        .btn-deactivate:hover {
            transform: translateY(-2px);
            opacity: 0.9;
            color: white;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .profile-card {
                margin: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        .order-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="navbar-toggler-icon" onclick="openNav()" style="cursor: pointer; margin-right: 1rem;"></span>
                <img src="./images/Readscape.png" alt="ReadScape" class="rounded-circle" width="40" height="40">
                <span class="ms-2 text-white fw-bold">ReadScape</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <a href="cart.php" class="btn btn-outline-light">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-counter">0</span>
                    </a>
                </div>
                <div class="position-relative me-3">
                    <a href="order.php" class="btn btn-outline-light">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-badge order-badge" id="order-counter"><?php echo $pending_orders_count; ?></span>
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <img src="<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <?php echo $fname . " " . $lname; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="order.php">My Orders</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidenav -->
    <div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Home</a>
        <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
        <a href="changepass.php"><i class="fas fa-key me-2"></i>Change Password</a>
        <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Cart</a>
        <a href="order.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
    </div>

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-header">
            <h2 class="mb-0">User Profile</h2>
        </div>

        <img src="<?php echo $profile_image ? $profile_image : 'uploads/default.jpg'; ?>" alt="Profile Picture" class="profile-image">

        <div class="profile-body">
            <div class="profile-info">
                <p><strong>First Name:</strong> <?php echo $fname; ?></p>
                <p><strong>Last Name:</strong> <?php echo $lname; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
            </div>

            <div class="action-buttons">
                <a href="edit-profile.php" class="btn-edit">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
                <a href="deactive.php" class="btn-deactivate">
                    <i class="fas fa-user-times me-2"></i>Deactivate Account
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

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

        function updateOrderCounter() {
            fetch('order_counter.php')
                .then(response => response.text())
                .then(count => {
                    document.getElementById("order-counter").innerText = count;
                })
                .catch(error => console.error('Error updating order counter:', error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateCartCounter(); // If this exists
            updateOrderCounter();
        });

        // Update counters periodically
        setInterval(function() {
            updateCartCounter();
            updateOrderCounter();
        }, 30000); // Update every 30 seconds

        document.addEventListener("DOMContentLoaded", updateCartCounter);
    </script>

</body>

</html>