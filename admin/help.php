<?php
session_start();
include '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get admin details from session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$profile_image = '../uploads/default.jpg';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Help - ReadScape Admin</title>
    <meta name="description" content="Help and documentation for ReadScape admin panel">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            box-shadow: 2px 0 5px rgba(0, 0, 0, .2);
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

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .help-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .help-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .help-header {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #3498db;
        }

        .help-section {
            margin-bottom: 2rem;
        }

        .help-section h2 {
            color: #34495e;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .help-section p {
            color: #555;
            line-height: 1.6;
        }

        .faq-item {
            margin-bottom: 1.5rem;
        }

        .faq-question {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .faq-answer {
            color: #555;
        }

        .contact-support {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
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

        .profile-info span {
            color: #fff;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="navbar-toggler-icon" onclick="openNav()" style="cursor: pointer; margin-right: 1rem;"></span>
                <img src="../images/Readscape.png" alt="ReadScape" class="rounded-circle" width="40" height="40">
                <span class="ms-2 text-white fw-bold">ReadScape</span>
                <div class="sidenav" id="Sidenav">
                    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                    <a href="../admin/admin_dashboard.php"><i class="fas fa-dashboard me-2"></i>Dashboard</a>
                    <a href="../admin/total_books.php"><i class="fas fa-book me-2"></i>Books</a>
                    <a href="../admin/customers.php"><i class="fas fa-users me-2"></i>Customers</a>
                    <a href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
                    <a href="help.php"><i class="fas fa-question-circle me-2"></i>Help</a>
                    <a href="../admin/manage_user.php"><i class="fas fa-user-cog me-2"></i>Manage Users</a>
                    <a href="./admin.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
                </div>
            </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="help-container">
        <div class="help-card">
            <h1 class="help-header"><i class="fas fa-question-circle me-2"></i>Help Center</h1>

            <div class="help-section">
                <h2><i class="fas fa-book me-2"></i>Getting Started</h2>
                <p>Welcome to the ReadScape Admin Panel. Here you'll find everything you need to manage your online bookstore effectively.</p>
            </div>

            <div class="help-section">
                <h2><i class="fas fa-list me-2"></i>Frequently Asked Questions</h2>
                
                <div class="faq-item">
                    <div class="faq-question">How do I add a new book?</div>
                    <div class="faq-answer">Navigate to Books section and click on "Add New Book". Fill in the required information including title, author, price, and upload a cover image.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How do I manage users?</div>
                    <div class="faq-answer">Go to the Manage Users section where you can view, edit, or remove user accounts. You can also search for specific users using the search function.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How do I view customer orders?</div>
                    <div class="faq-answer">Access the Customers section to view all orders. You can filter orders by date, status, or customer name.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How do I update my admin profile?</div>
                    <div class="faq-answer">Visit the Settings section to update your profile information, change your password, or modify notification preferences.</div>
                </div>
            </div>

            <div class="contact-support">
                <h2><i class="fas fa-headset me-2"></i>Need More Help?</h2>
                <p>If you can't find what you're looking for, our support team is here to help.</p>
                <p><i class="fas fa-envelope me-2"></i>Email: support@readscape.com</p>
                <p><i class="fas fa-phone me-2"></i>Phone: +63 912 345 6789</p>
                <p><i class="fas fa-clock me-2"></i>Support Hours: Monday - Friday, 9:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }
    </script>
</body>
</html>