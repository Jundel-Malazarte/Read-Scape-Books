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
    <title>View Orders</title>
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
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
        }

        .search-bar input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }

        .search-bar button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .search-bar .reset-btn {
            padding: 8px 15px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        .search-bar .reset-btn:hover {
            background-color: #c9302c;
        }

        .order-section {
            width: 100%;
        }

        .order-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .order-section table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 10px;
        }

        .order-section th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            color: #333;
            font-weight: bold;
        }

        .order-section td {
            padding: 12px;
            text-align: center;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .order-section td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .product-cell {
            display: flex;
            flex-direction: column;
            justify-content: left;
            align-items: left;
            gap: 5px;
            width: auto;
            margin: 0 auto;
            max-width: 100%;
        }

        .product-cell .book-item {
            display: flex;
            align-items: center;
            gap: 5px;
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

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination button:hover {
            background-color: #e9ecef;
        }

        .tab-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-nav a {
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            border-bottom: 2px solid transparent;
            transition: border-bottom 0.3s ease, color 0.3s ease;
        }

        .tab-nav a.active {
            color: #007bff;
            border-bottom: 2px solid #007bff;
        }

        .tab-nav a:hover {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="../admin/admin_dashboard.php">Home</a>
        </div>
        <div class="profile-info">
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="../admin/logout.php">Log Out</a>
        </div>
    </div>

    <div class="container">
        <div class="search-container">
            <h1>Order <?php echo $total_orders; ?> orders found</h1>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
                <p style="color: green;">Order status updated successfully!</p>
            <?php endif; ?>
            <div class="search-bar">
                <form method="GET">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                    <input type="text" name="search" placeholder="Search by Full Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                    <a href="view_orders.php?status=<?php echo htmlspecialchars($status_filter); ?>" class="reset-btn">Reset</a>
                </form>
            </div>
        </div>

        <div class="order-section">
            <div class="tab-nav">
                <a href="view_orders.php?status=all&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Orders</a>
                <a href="view_orders.php?status=completed&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="view_orders.php?status=pending&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="view_orders.php?status=canceled&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $status_filter === 'canceled' ? 'active' : ''; ?>">Cancel</a>
            </div>

            <?php if (empty($paginated_orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Address</th>
                            <th>Shipped To</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
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
        <td class="actions"><a href="order_details.php?id=<?php echo $order['order_id']; ?>"><i class="fas fa-eye"></i></a></td> <!-- Fixed typo -->
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="pagination">
        <button onclick="window.location.href='?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>'" <?php echo $page <= 1 ? 'disabled' : ''; ?>>Previous</button>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <button class="<?php echo $i === $page ? 'active' : ''; ?>" onclick="window.location.href='?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>'"><?php echo $i; ?></button>
        <?php endfor; ?>
        <button onclick="window.location.href='?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>'" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>>Next</button>
    </div>
    <p>Showing <?php echo $start + 1; ?> to <?php echo min($start + $items_per_page, $total_orders); ?> of <?php echo $total_orders; ?> entries</p>
<?php endif; ?>
    </div>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>