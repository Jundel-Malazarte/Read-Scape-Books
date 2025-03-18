<?php
session_start();
include '../db_connect.php';

// Single session check
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get admin details from session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$profile_image = '../uploads/default.jpg';

// Fetch total users count
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) FROM users");
$total_users = mysqli_fetch_row($total_users_query)[0];

$total_books_query = mysqli_query($conn, "SELECT COUNT(*) FROM books");
$total_books = mysqli_fetch_row($total_books_query)[0];

// Fetch total ordered users count
$total_ordered_users_query = mysqli_query($conn, "SELECT COUNT(*) FROM orders");
$total_ordered_users = mysqli_fetch_row($total_ordered_users_query)[0];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #212529;
            padding: 1rem;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #343a40;
            border-radius: 5px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .dashboard-wrapper {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .card {
            background: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            padding: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .card-content {
            text-align: right;
        }

        .card-content p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .card-content h2 {
            color: #212529;
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
        }

        .card-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        .card-link:hover {
            color: inherit;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="nav-links">
                <a href="../admin/admin_dashboard.php" class="btn btn-outline-light">Home</a>
            </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <h1>Admin Dashboard</h1>
        <hr>
        <h2>Quick Stats</h2>
        <div class="dashboard-cards">
            <a href="total_users.php" class="card-link">
                <div class="card">
                    <div class="card-inner">
                        <img src="../images/user_icon.png" alt="User Icon" class="card-icon">
                        <div class="card-content">
                            <p>Total Users</p>
                            <h2><?php echo $total_users; ?></h2>
                        </div>
                    </div>
                </div>
            </a>

            <a href="total_books.php" class="card-link">
                <div class="card">
                    <div class="card-inner">
                        <img src="../images/book_icon.png" alt="Book Icon" class="card-icon">
                        <div class="card-content">
                            <p>Total Books</p>
                            <h2><?php echo $total_books; ?></h2>
                        </div>
                    </div>
                </div>
            </a>

            <a href="view_orders.php" class="card-link">
                <div class="card">
                    <div class="card-inner">
                        <img src="../images/total-orders.png" alt="Order Icon" class="card-icon">
                        <div class="card-content">
                            <p>Total Orders</p>
                            <h2><?php echo $total_ordered_users; ?></h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>