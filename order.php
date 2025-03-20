<?php
@include 'db_connect.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

// Get pending orders count
$pending_orders_sql = "SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $pending_orders_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$pending_result = mysqli_stmt_get_result($stmt);
$pending_orders_count = mysqli_fetch_row($pending_result)[0];
mysqli_stmt_close($stmt);

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

// Get the selected status from URL or default to 'all'
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : ''; // For future search functionality

// Fetch orders with grouped items and status
$sql = "SELECT orders.id AS order_id, orders.order_date, orders.status,
        GROUP_CONCAT(books.title SEPARATOR '|||') AS titles,
        GROUP_CONCAT(books.book_image SEPARATOR '|||') AS book_images,
        GROUP_CONCAT(books.author SEPARATOR '|||') AS authors,
        GROUP_CONCAT(order_items.quantity SEPARATOR '|||') AS quantities,
        GROUP_CONCAT(order_items.price SEPARATOR '|||') AS prices
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN books ON order_items.book_id = books.isbn
        WHERE orders.user_id = ? AND (orders.status = ? OR ? = 'all')
        GROUP BY orders.id
        ORDER BY orders.order_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $user_id, $status_filter, $status_filter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = [
        'order_id' => $row['order_id'],
        'order_date' => $row['order_date'],
        'status' => $row['status'],
        'titles' => explode('|||', $row['titles']),
        'book_images' => explode('|||', $row['book_images']),
        'authors' => explode('|||', $row['authors']),
        'quantities' => explode('|||', $row['quantities']),
        'prices' => explode('|||', $row['prices']),
    ];
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #212529;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            font-size: 1.4rem;
        }

        .navbar-brand img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .profile-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
        }

        .cart-icon {
            position: relative;
            font-size: 1.2rem;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .order-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #212529;
            margin: 0;
        }

        .tab-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .tab-nav a {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: #6c757d;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 1.1rem;
        }

        .tab-nav a.active {
            background: #0d6efd;
            color: white;
        }

        .tab-nav a:hover {
            background-color: #e9ecef;
            color: #212529;
            transform: translateY(-1px);
        }

        .tab-nav a.active:hover {
            background: #0b5ed7;
            color: white;
        }

        .order-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Changed to 2 columns */
            gap: 2rem;
            padding: 1rem;
            max-width: 1600px;
            /* Increased max-width to accommodate 2 columns */
            margin: 0 auto;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            padding: 1.5rem;
            margin-bottom: 0;
            /* Remove bottom margin since grid handles spacing */
            transition: transform 0.3s ease;
            height: 100%;
            /* Ensure consistent height */
            display: flex;
            flex-direction: column;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .order-id {
            font-size: 1.4rem;
            font-weight: 600;
            color: #212529;
        }

        .order-date {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            width: fit-content;
            /* Add this to make status width fit content */
        }

        .status.completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status.canceled {
            background-color: #f8d7da;
            color: #842029;
        }

        .order-items table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .order-items td {
            padding: 1rem;
            vertical-align: top;
        }

        .order-items td.image {
            width: 140px;
            padding-right: 0;
        }

        .order-items td.details {
            width: calc(100% - 140px);
            padding-left: 1.5rem;
        }

        .order-items td.image img {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .book-info {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .see-more-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            width: 100%;
        }

        .see-more-btn:hover {
            background-color: #5a6268;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
        }

        .order-total {
            font-size: 1.3rem;
            font-weight: 600;
            color: #212529;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-pay {
            background-color: #198754;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            /* Remove underline */
        }

        .btn-cancel:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-pay:hover {
            background-color: #157347;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
            /* Remove underline on hover */
        }

        @media (max-width: 768px) {
            .tab-nav {
                flex-wrap: wrap;
            }

            .tab-nav a {
                flex: 1 1 45%;
                text-align: center;
            }

            .order-footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .action-buttons {
                width: 100%;
                justify-content: center;
            }

            .order-items td.image {
                width: 100px;
            }

            .order-items td.details {
                width: calc(100% - 100px);
            }

            .book-title {
                font-size: 1.1rem;
            }

            .book-info {
                font-size: 1rem;
            }
        }

        @media (max-width: 1200px) {
            .order-list {
                grid-template-columns: 1fr;
                /* Single column on smaller screens */
            }
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

    <div class="sidenav" id="Sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Home</a>
        <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
        <a href="changepass.php"><i class="fas fa-key me-2"></i>Change password</a>
        <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Cart</a>
        <a href="order.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
    </div>
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }
    </script>

    <div class="order-container">
        <div class="page-header">
            <h2>My Orders</h2>
        </div>

        <div class="tab-nav">
            <a href="order.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Orders</a>
            <a href="order.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
            <a href="order.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="order.php?status=canceled" class="<?php echo $status_filter === 'canceled' ? 'active' : ''; ?>">Canceled</a>
        </div>

        <div class="order-list">
            <?php if (empty($orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></span>
                            <span class="order-date">Order Date: <?php echo htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))); ?></span>
                        </div>
                        <p class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></p>
                        <div class="order-items">
                            <table>
                                <?php
                                $total_price = 0;
                                for ($i = 0; $i < count($order['titles']); $i++):
                                    $image_path = !empty($order['book_images'][$i]) ? 'images/' . htmlspecialchars($order['book_images'][$i]) : 'images/default_book.png';
                                    $subtotal = $order['prices'][$i] * $order['quantities'][$i];
                                    $total_price += $subtotal;
                                ?>
                                    <tr>
                                        <td class="image">
                                            <img src="<?php echo $image_path; ?>" alt="Book Image">
                                        </td>
                                        <td class="details">
                                            <p class="book-title"><?php echo htmlspecialchars($order['titles'][$i]); ?></p>
                                            <p class="book-info">Author: <?php echo htmlspecialchars($order['authors'][$i]); ?></p>
                                            <p class="book-info">Quantity: <?php echo htmlspecialchars($order['quantities'][$i]); ?></p>
                                            <p class="book-info">Price: ₱<?php echo number_format($order['prices'][$i], 2); ?></p>
                                            <p class="book-info">Subtotal: ₱<?php echo number_format($subtotal, 2); ?></p>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </table>
                        </div>
                        <!-- <hr style="margin: 15px 0;"> -->
                        <div class="order-footer">
                            <p class="order-total">Total: ₱<?php echo number_format($total_price, 2); ?></p>
                            <?php if (strtolower($order['status']) === 'pending'): ?>
                                <div class="action-buttons">
                                    <form action="cancel_order.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" class="btn-cancel">Cancel Order</button>
                                    </form>
                                    <a href="pay_now.php?order_id=<?php echo $order['order_id']; ?>"
                                        class="btn-pay">
                                        Pay Now
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        function toggleItems(button) {
            const card = button.closest('.order-card');
            const hiddenItems = card.querySelectorAll('.hidden-items');
            const isExpanded = card.classList.toggle('expanded');

            hiddenItems.forEach(item => {
                item.style.display = isExpanded ? 'table-row' : 'none';
            });

            button.textContent = isExpanded ? 'See Less ↑' : 'See More ↓';
            button.setAttribute('aria-expanded', isExpanded);
        }

        // Call updateCartCounter() when page loads
        document.addEventListener("DOMContentLoaded", updateCartCounter);


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
    </script>
</body>

</html>