<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Fetch logged-in admin details
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

// Get the order ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_orders.php");
    exit();
}
$order_id = intval($_GET['id']);

// Fetch order details
$sql = "SELECT o.id AS order_id, o.order_date, o.shipping_address, o.status, 
        u.fname, u.lname, u.email, 
        (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) AS total
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($order = mysqli_fetch_assoc($result)) {
    $order_id = htmlspecialchars($order['order_id']);
    $order_date = date('d/m/Y', strtotime($order['order_date']));
    $shipping_address = htmlspecialchars($order['shipping_address']);
    $status = htmlspecialchars($order['status']);
    $customer_name = htmlspecialchars($order['fname'] . ' ' . $order['lname']);
    $customer_email = htmlspecialchars($order['email']);
    $total = number_format($order['total'], 2);
} else {
    header("Location: view_orders.php");
    exit();
}
mysqli_stmt_close($stmt);

// Fetch order items (books)
$sql = "SELECT b.title, b.book_image, oi.price, oi.quantity
        FROM order_items oi
        JOIN books b ON oi.book_id = b.isbn
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$order_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $order_items[] = [
        'title' => htmlspecialchars($row['title']),
        'book_image' => htmlspecialchars($row['book_image']),
        'price' => number_format($row['price'], 2),
        'quantity' => $row['quantity'],
        'subtotal' => number_format($row['price'] * $row['quantity'], 2)
    ];
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
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
            width: 40%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .order-info {
            margin-bottom: 30px;
        }

        .order-info p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }

        .order-info p strong {
            color: #333;
        }

        .status {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status.completed {
            /* Changed to .completed */
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
            margin-top: 10px;
        }

        .complete-btn,
        .cancel-btn {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 12px;
            margin-right: 10px;
        }

        .complete-btn {
            background-color: #28a745;
        }

        .complete-btn:hover {
            background-color: #218838;
        }

        .cancel-btn {
            background-color: #dc3545;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .order-items {
            margin-top: 20px;
        }

        .order-items table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .order-items th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            color: #333;
            font-weight: bold;
        }

        .order-items td {
            padding: 12px;
            text-align: center;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .order-items td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .order-items .book-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="../admin/admin_dashboard.php">Home</a>
            <a href="view_orders.php">View Orders</a>
        </div>
        <div class="profile-info">
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="../admin/logout.php">Log Out</a>
        </div>
    </div>

    <div class="container">
        <h1>Order Details - #<?php echo $order_id; ?></h1>
        <?php
        if (isset($_GET['success']) && $_GET['success'] === 'status_updated') {
            echo '<p style="color: green;">Order status updated successfully!</p>';
        } elseif (isset($_GET['error'])) {
            echo '<p style="color: red;">Error: ' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <div class="order-info">
            <p><strong>Order Date:</strong> <?php echo $order_date; ?></p>
            <p><strong>Customer:</strong> <?php echo $customer_name; ?></p>
            <p><strong>Email:</strong> <?php echo $customer_email; ?></p>
            <p><strong>Shipping Address:</strong> <?php echo $shipping_address; ?></p>
            <p><strong>Status:</strong> <span class="status <?php echo strtolower($status); ?>"><?php echo ucfirst($status); ?></span></p>
            <?php if (strtolower($status) === 'pending'): ?>
                <div class="status-buttons">
                    <form action="update_order_status.php" method="POST" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="status" value="completed"> <!-- Changed to "completed" -->
                        <button type="submit" class="complete-btn">Mark as Complete</button>
                    </form>
                    <form action="update_order_status.php" method="POST" style="display: inline;"> <!-- Fixed path -->
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="status" value="canceled">
                        <button type="submit" class="cancel-btn">Cancel Order</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <div class="book-item">
                                    <img src="../images/<?php echo $item['book_image'] ?: 'default_book.png'; ?>" alt="Product">
                                    <span><?php echo $item['title']; ?></span>
                                </div>
                            </td>
                            <td>₱<?php echo $item['price']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₱<?php echo $item['subtotal']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                Total: ₱<?php echo $total; ?>
            </div>
        </div>

        <a href="view_orders.php" class="back-btn">Back to Orders</a>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>