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

// Tab filter functionality
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$valid_statuses = ['all', 'completed', 'pending', 'canceled'];
if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = 'all';
}

// Fetch total orders count based on status
$total_orders_query = "SELECT COUNT(*) FROM orders";
if ($status_filter !== 'all') {
    $total_orders_query .= " WHERE status = ?";
    $stmt = mysqli_prepare($conn, $total_orders_query);
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
    mysqli_stmt_execute($stmt);
    $total_orders = mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0];
    mysqli_stmt_close($stmt);
} else {
    $total_orders_query = mysqli_query($conn, $total_orders_query);
    $total_orders = mysqli_fetch_row($total_orders_query)[0];
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$items_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, $_GET['page']) : 1;
$start = ($page - 1) * $items_per_page;

// Fetch all orders with details, grouped by order_id
$sql = "SELECT o.id AS order_id, 
        GROUP_CONCAT(b.title SEPARATOR ', ') AS titles, 
        GROUP_CONCAT(b.book_image SEPARATOR ',') AS book_images, 
        o.shipping_address, o.order_date, 
        (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) AS total, 
        o.status, u.fname, u.lname
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN books b ON oi.book_id = b.isbn";
$conditions = [];
$params = [];
$types = '';

if ($status_filter !== 'all') {
    $conditions[] = "o.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($search)) {
    $conditions[] = "CONCAT(u.fname, ' ', u.lname) LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " GROUP BY o.id ORDER BY o.id DESC";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Apply pagination
$total_pages = ceil(count($orders) / $items_per_page);
$paginated_orders = array_slice($orders, $start, $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers</title>
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
            max-width: 1200px;
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .product-cell {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .book-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .book-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            display: inline-block;
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

        .tab-nav {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .tab-nav a {
            padding: 0.75rem 1rem;
            color: #6c757d;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-right: 0.5rem;
        }

        .tab-nav a.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }

        .search-container {
            margin-bottom: 2rem;
        }

        .search-bar {
            display: flex;
            gap: 0.5rem;
        }

        .search-bar input {
            max-width: 300px;
        }

        .pagination {
            margin-top: 2rem;
            justify-content: center;
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
                            <a href="#"><i class="fas fa-cog me-2"></i>Settings</a>
                            <a href="#"><i class="fas fa-question-circle me-2"></i>Help</a>
                            <a href="../admin/manage_user.php"><i class="fas fa-user-cog me-2"></i>Manage Users</a>
                            <a href="./admin.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
                        </div>
                        <script>
                            function openNav() {
                                document.getElementById("Sidenav").style.width = "240px";
                            }

                            function closeNav() {
                                document.getElementById("Sidenav").style.width = "0";
                            }
                        </script>
                    </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customers: <?php echo $total_orders; ?></h2>
            <div class="search-bar">
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                    <input type="text" class="form-control" name="search" placeholder="Search by Full Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="view_orders.php?status=<?php echo htmlspecialchars($status_filter); ?>" class="btn btn-danger">Reset</a>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Order status updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> Order has been successfully deleted.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="tab-nav">
            <a href="view_orders.php?status=all&search=<?php echo htmlspecialchars($search); ?>"
                class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Orders</a>
        </div>

        <?php if (empty($paginated_orders)): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Address</th>
                        <th>Shipped To</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginated_orders as $order): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td>
                                <?php
                                $titles = explode(', ', $order['titles']);
                                $images = explode(',', $order['book_images']);
                                echo '<div class="product-cell">';
                                for ($i = 0; $i < count($titles); $i++):
                                    $image = !empty($images[$i]) ? htmlspecialchars(trim($images[$i])) : 'default_book.png';
                                ?>
                                    <div class="book-item">
                                        <img src="../images/<?php echo $image; ?>" alt="Product">
                                        <span><?php echo htmlspecialchars($titles[$i]); ?></span>
                                    </div>
                                <?php endfor; ?>
    </div>
    </td>
        <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
        <td><?php echo htmlspecialchars($order['fname'] . ' ' . $order['lname']); ?></td>
        <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
        <td>₱<?php echo number_format($order['total'], 2); ?></td>
        <td><span class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>">Next</a>
        </li>
    </ul>
</nav>
<p>Showing <?php echo $start + 1; ?> to <?php echo min($start + $items_per_page, $total_orders); ?> of <?php echo $total_orders; ?> entries</p>
<?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>