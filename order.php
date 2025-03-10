<?php
@include 'db_connect.php';
session_start();

// Redirect to login if not logged in
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
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

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .readscape {
            border-radius: 50%;
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

        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 90%;
            margin: 20px auto;
        }

        .header-text h2 {
            font-size: 22px;
            margin: 0;
        }

        .order-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 90%;
            margin: 20px auto;
            align-items: stretch;
        }

        .order-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s;
        }

        .order-card:hover {
            transform: scale(1.02);
        }

        .order-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .order-card .order-date {
            font-size: 14px;
            color: #888;
            margin-bottom: 15px;
        }

        .order-items {
            width: 100%;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .order-items td {
            vertical-align: top;
            padding: 10px 0;
        }

        .order-items td.image {
            width: 120px;
            padding-right: 20px;
        }

        .order-items td.image img {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        .order-items td.name {
            flex: 1;
        }

        .name p {
            font-size: 14px;
            color: #444;
            margin: 3px 0;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .order-total {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status.completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status.canceled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-buttons {
            margin: 0;
        }

        .cancel-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            font-weight: bold;
            background-color: #dc3545;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

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

        .tab-nav {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tab-nav a {
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            border-radius: 5px;
        }

        .tab-nav a.active {
            background: #333;
            color: white;
            font-weight: bold;
        }

        .tab-nav a:hover {
            background: #555;
            color: white;
        }

        @media (max-width: 768px) {
            .order-list {
                grid-template-columns: 1fr;
            }

            .order-card {
                width: 90%;
            }
        }

        /* Styles for See More button */
        .see-more-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .see-more-btn:hover {
            background-color: #0056b3;
        }

        /* Ensure hidden-items only controls visibility, not layout */
        .hidden-items {
            display: none;
        }

        /* Ensure hidden items maintain the same layout as order-item */
        .expanded .hidden-items {
            display: table-row;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div id="Sidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="changepass.php">Change password</a>
            <a href="cart.php">Cart</a>
            <a href="order.php">My Orders</a>
            <a href="logout.php">Log Out</a>
        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰<strong> ReadScape</strong> <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>
        <script>
            function openNav() {
                document.getElementById("Sidenav").style.width = "240px";
            }

            function closeNav() {
                document.getElementById("Sidenav").style.width = "0";
            }
        </script>
        <div class="profile-info">
            <a href="cart.php" style="position: relative; color: white; text-decoration: none;">
                🛒 Cart <span id="cart-counter" style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;">0</span>
            </a>
            <br>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><strong><?php echo $fname . " " . $lname; ?></strong></a>
            <a href="logout.php"><strong>Log Out</strong></a>
        </div>
    </div>

    <div class="search-header">
        <div class="header-text">
            <h2>My Orders!</h2>
        </div>
    </div>

    <div class="tab-nav">
        <a href="order.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Orders</a>
        <a href="order.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
        <a href="order.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="order.php?status=canceled" class="<?php echo $status_filter === 'canceled' ? 'active' : ''; ?>">Canceled</a>
    </div>

    <div class="container">
        <div class="order-list">
            <?php if (empty($orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                        <p class="order-date">Order Date: <?php echo htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))); ?></p>
                        <p class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></p>
                        <div class="order-items">
                            <table>
                                <?php
                                $total_price = 0;
                                $item_count = count($order['titles']);
                                // Display only the first item statically
                                $i = 0;
                                $image_path = !empty($order['book_images'][$i]) ? 'images/' . htmlspecialchars($order['book_images'][$i]) : 'images/default_book.png';
                                $subtotal = $order['prices'][$i] * $order['quantities'][$i];
                                $total_price += $subtotal;
                                ?>
                                <tr>
                                    <td class="image">
                                        <img src="<?php echo $image_path; ?>" alt="Book Image">
                                    </td>
                                    <td class="name">
                                        <p><strong><?php echo htmlspecialchars($order['titles'][$i]); ?></strong></p>
                                        <p>Author: <?php echo htmlspecialchars($order['authors'][$i]); ?></p>
                                        <p>Quantity: <?php echo htmlspecialchars($order['quantities'][$i]); ?></p>
                                        <p>Price: ₱<?php echo number_format($order['prices'][$i], 2); ?></p>
                                        <p>Subtotal: ₱<?php echo number_format($subtotal, 2); ?></p>
                                    </td>
                                </tr>
                                <?php if ($item_count > 1): ?>
                                    <?php
                                    // Hidden items for orders with more than one item
                                    for ($i = 1; $i < $item_count; $i++):
                                        $image_path = !empty($order['book_images'][$i]) ? 'images/' . htmlspecialchars($order['book_images'][$i]) : 'images/default_book.png';
                                        $subtotal = $order['prices'][$i] * $order['quantities'][$i];
                                        $total_price += $subtotal;
                                    ?>
                                        <tr class="hidden-items">
                                            <td class="image">
                                                <img src="<?php echo $image_path; ?>" alt="Book Image">
                                            </td>
                                            <td class="name">
                                                <p><strong><?php echo htmlspecialchars($order['titles'][$i]); ?></strong></p>
                                                <p>Author: <?php echo htmlspecialchars($order['authors'][$i]); ?></p>
                                                <p>Quantity: <?php echo htmlspecialchars($order['quantities'][$i]); ?></p>
                                                <p>Price: ₱<?php echo number_format($order['prices'][$i], 2); ?></p>
                                                <p>Subtotal: ₱<?php echo number_format($subtotal, 2); ?></p>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                    <button class="see-more-btn" onclick="toggleItems(this)" aria-expanded="false">See More ↓</button>
                                <?php endif; ?>
                            </table>
                        </div>
                        <hr style="margin: 15px 0;">
                        <div class="order-footer">
                            <p class="order-total">Total: ₱<?php echo number_format($total_price, 2); ?></p>
                            <?php if (strtolower($order['status']) === 'pending'): ?>
                                <div class="status-buttons">
                                    <form action="cancel_order.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" class="cancel-btn">Cancel Order</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

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
    </script>
</body>

</html>