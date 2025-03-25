<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user details
$sql = "SELECT fname, lname, profile_image FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$fname = htmlspecialchars($user['fname']);
$lname = htmlspecialchars($user['lname']);
$profile_image = htmlspecialchars($user['profile_image']) ?: "uploads/default.jpg";

// Get cart count
$cart_count_sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $cart_count_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_count_result = mysqli_stmt_get_result($stmt);
$cart_count = mysqli_fetch_row($cart_count_result)[0];
mysqli_stmt_close($stmt);

// Get pending orders count
$pending_orders_sql = "SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $pending_orders_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$pending_result = mysqli_stmt_get_result($stmt);
$pending_orders_count = mysqli_fetch_row($pending_result)[0];
mysqli_stmt_close($stmt);

// Fetch order details
$order_id = $_GET['order_id'] ?? 0;
$stmt = $conn->prepare(
    "SELECT o.*, oi.book_id, oi.quantity, oi.price, b.title 
     FROM orders o 
     LEFT JOIN order_items oi ON o.id = oi.order_id 
     LEFT JOIN books b ON oi.book_id = b.isbn 
     WHERE o.id = ? AND o.user_id = ?"
);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if order exists
if ($result->num_rows === 0) {
    header("Location: order.php");
    exit();
}

$order = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Now - Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        /* Existing styles */
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

        .payment-section {
            margin-top: 2rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .upload-section {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .preview-image {
            max-width: 300px;
            margin-top: 1rem;
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
                        <span class="cart-badge" id="cart-counter"><?php echo $cart_count; ?></span>
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
    <div class="sidenav" id="Sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Home</a>
        <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
        <a href="changepass.php"><i class="fas fa-key me-2"></i>Change password</a>
        <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Cart</a>
        <a href="order.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
    </div>

    <!-- Your existing content -->
    <div class="container mt-4">
        <div class="receipt">
            <!-- Copy receipt section from order_success.php -->

            <div class="payment-section">
                <h2>Payment Upload</h2>
                <div class="upload-section">
                    <form id="paymentForm" action="process_payment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <div class="mb-3">
                            <label for="receipt" class="form-label">Upload GCash Receipt</label>
                            <input type="file" class="form-control" id="receipt" name="receipt"
                                accept="image/*" required onchange="previewImage(this)">
                        </div>
                        <div id="imagePreview"></div>
                        <button type="submit" class="btn btn-primary">Complete Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updateCartCounter() {
            fetch('cart_counter.php')
                .then(response => response.text())
                .then(count => {
                    document.getElementById("cart-counter").innerText = count;
                })
                .catch(error => console.error('Error updating cart counter:', error));
        }

        function updateOrderCounter() {
            fetch('order_counter.php')
                .then(response => response.text())
                .then(count => {
                    document.getElementById("order-counter").innerText = count;
                })
                .catch(error => console.error('Error updating order counter:', error));
        }

        // Update counters when page loads
        document.addEventListener("DOMContentLoaded", function() {
            updateCartCounter();
            updateOrderCounter();
        });

        // Update counters periodically
        setInterval(function() {
            updateCartCounter();
            updateOrderCounter();
        }, 30000);

        document.getElementById('paymentForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('process_payment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment completed successfully!');
                        window.location.href = 'order.php';
                    } else {
                        alert(data.error || 'Error processing payment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing payment');
                });
        };
    </script>
</body>

</html>