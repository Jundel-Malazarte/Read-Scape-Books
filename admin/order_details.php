<?php
@include '../db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get admin details from session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$profile_image = '../uploads/default.jpg';

// Get the order ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_orders.php");
    exit();
}
$order_id = intval($_GET['id']);

// Fetch order details
$sql = "SELECT o.id AS order_id, o.order_date, o.shipping_address, o.status, o.payment_receipt,
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
    $payment_receipt = $order['payment_receipt'];
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .profile-info span {
            color: #fff;
            font-weight: 500;
        }

        .profile-info .btn-danger {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        .profile-info .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        .container {
            max-width: 800px;
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
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

        .book-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .book-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .table> :not(caption)>*>* {
            padding: 1rem;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Receipt button styles */
        .receipt-btn {
            background-color: #198754;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .receipt-btn:hover {
            background-color: #157347;
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            align-items: center;
        }

        .btn-primary,
        .receipt-btn {
            padding: 0.5rem 1.2rem;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            padding: 0.5rem 1.2rem;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
            background-color: transparent;
            border: 1px solid #0d6efd;
            color: #0d6efd;
            width: 400px;
            min-width: 120px;
            justify-content: center;
        }

        .btn-primary:hover {
            background-color: transparent;
            color: #0a58ca;
            border-color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .receipt-btn {
            padding: 0.5rem 1.2rem;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
            background-color: #198754;
            color: white;
            text-decoration: none;
            border: none;
        }

        .receipt-btn:hover {
            background-color: #157347;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="nav-links">
                <a href="../admin/admin_dashboard.php" class="btn btn-outline-light">Home</a>
                <a href="view_orders.php" class="btn btn-outline-light">Back to Orders</a>
            </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Order Details - #<?php echo $order_id; ?></h1>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                Order status updated successfully!
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Order Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Order Date:</strong> <?php echo $order_date; ?></p>
                        <p class="mb-2"><strong>Customer:</strong> <?php echo $customer_name; ?></p>
                        <p class="mb-2"><strong>Email:</strong> <?php echo $customer_email; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Status:</strong>
                            <span class="status <?php echo strtolower($status); ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </p>
                        <p class="mb-2"><strong>Shipping Address:</strong><br><?php echo $shipping_address; ?></p>
                    </div>
                </div>

                <div class="mt-3">
                    <?php if (strtolower($status) === 'pending'): ?>
                        <form action="update_order_status.php" method="POST" class="d-inline-block me-2">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-sm">Mark as Complete</button>
                        </form>
                        <form action="update_order_status.php" method="POST" class="d-inline-block me-2">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
                        </form>
                    <?php endif; ?>
                    <form action="delete_order.php" method="POST" class="d-inline-block">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                            <i class="fas fa-trash-alt"></i> Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Order Items</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                </div>
                <div class="text-end mt-3">
                    <h4>Total: ₱<?php echo $total; ?></h4>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 btn-group">
            <a href="view_orders.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>Back
            </a>
            <?php if (!empty($order['payment_receipt']) && file_exists("../uploads/receipts/" . $order['payment_receipt'])): ?>
                <a href="../uploads/receipts/<?php echo $order['payment_receipt']; ?>"
                    class="receipt-btn"
                    target="_blank"
                    title="View payment receipt">
                    <i class="fas fa-file-invoice"></i>Receipt
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>