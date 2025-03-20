<?php
session_start();
include 'db_connect.php';

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

// Check if an ISBN is passed for single-item checkout (from "Buy Now")
$single_item = false;
$items = [];
$total_price = 0;
$shipping_cost = 100.00; // Default shipping cost (Standard Delivery)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['fname'] = $_POST['first_name'] ?? $_SESSION['fname'];
    $_SESSION['lname'] = $_POST['last_name'] ?? $_SESSION['lname'];
    $_SESSION['email'] = $_POST['email'] ?? $_SESSION['email'];
    $_SESSION['mobile'] = $_POST['mobile'] ?? $_SESSION['mobile'];
    // Submit to process_order.php
    header("Location: process_order.php");
    exit();
}

if (isset($_GET['isbn']) && !empty($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    $single_item = true;

    // Fetch the specific book
    $sql = "SELECT isbn, title, book_image, author, price FROM books WHERE isbn = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($book = mysqli_fetch_assoc($result)) {
        $items[] = [
            'isbn' => $book['isbn'],
            'title' => $book['title'],
            'book_image' => $book['book_image'],
            'author' => $book['author'],
            'price' => $book['price'],
            'quantity' => 1, // Quantity is 1 for "Buy Now"
            'total' => $book['price'] * 1
        ];
        $total_price = $book['price'];
    }
    mysqli_stmt_close($stmt);
} else {
    // Fetch cart items for regular checkout
    $sql = "SELECT books.isbn, books.title, books.book_image, books.author, books.price, cart.quantity, 
            (books.price * cart.quantity) AS total 
        FROM cart 
        JOIN books ON cart.isbn = books.isbn 
        WHERE cart.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($cart_item = mysqli_fetch_assoc($result)) {
        $items[] = $cart_item;
        $total_price += $cart_item['total'];
    }
    mysqli_stmt_close($stmt);
}

$total_with_shipping = $total_price + $shipping_cost;
$cart_count = count($items);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        /* Navbar Styles */
        .navbar {
            background-color: #212529;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar .fw-bold {
            font-size: 1.2rem;
        }

        .dropdown-menu {
            font-size: 1.1rem;
        }

        /* Sidenav Styles */
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

        /* Your existing checkout styles below... */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-bottom: 100px;
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

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            /* Add this */
            grid-template-columns: 3fr 1fr;
            /* Add this */
            gap: 2rem;
            /* Add this */
        }

        .left-column,
        .right-column {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            padding: 2rem;
            height: fit-content;
        }

        .right-column {
            position: sticky;
            top: 2rem;
            align-self: start;
        }

        .shipping-form input,
        .shipping-form textarea,
        .shipping-form select {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .address-preview {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin-top: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            border: 1px solid #dee2e6;
        }

        .address-preview h3 {
            color: #212529;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .address-preview p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .address-preview p strong {
            color: #495057;
            min-width: 140px;
        }

        .address-preview p span {
            color: #212529;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }

        .edit-address-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .edit-address-btn:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .edit-address-btn i {
            font-size: 0.9rem;
        }

        .address-preview h3 {
            color: #212529;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .cart-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cart-table th {
            background-color: #f8f9fa;
            color: #212529;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        .cart-table th:not(:last-child) {
            padding-right: 2rem;
            /* Add space between headers */
        }

        .cart-table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        /* Column widths */
        .cart-table th:nth-child(1) {
            width: 35%;
        }

        /* Items column */
        .cart-table th:nth-child(2) {
            width: 20%;
        }

        /* Author column */
        .cart-table th:nth-child(3) {
            width: 15%;
        }

        /* Quantity column */
        .cart-table th:nth-child(4) {
            width: 15%;
        }

        /* Price column */
        .cart-table th:nth-child(5) {
            width: 10%;
        }

        /* Total column */
        .cart-table th:nth-child(6) {
            width: 5%;
        }

        /* Action column */

        /* Remove button styling */
        .remove-btn {
            color: #fff;
            background-color: #dc3545;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .remove-btn:hover {
            background-color: #bb2d3b;
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .item-details {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .item-details img {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .item-details h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin: 0;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .price-summary h3 {
            color: #212529;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .total-price p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            color: #6c757d;
        }

        .total-price strong {
            color: #212529;
        }

        .btn-edit {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .checkout-btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover:not([disabled]) {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .checkout-btn[disabled] {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .right-column {
                margin-top: 2rem;
            }
        }

        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
                /* Stack columns on mobile */
            }

            .right-column {
                position: static;
                /* Remove sticky positioning on mobile */
                margin-top: 2rem;
            }
        }

        /* Add these CSS rules */
        .order-summary {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            border: 1px solid #dee2e6;
            margin-top: 2rem;
        }

        .order-summary h2 {
            color: #212529;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
        }

        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 1.5rem;
        }

        .cart-table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid #dee2e6;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .cart-table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cart-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .item-details {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .item-details img {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, .15);
        }

        .item-details h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin: 0 0 0.5rem 0;
        }

        .item-price {
            font-weight: 600;
            color: #212529;
        }

        .item-quantity {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
        }

        .remove-btn {
            color: #fff;
            background-color: #dc3545;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            padding: 0;
            margin: 0 auto;
        }

        .remove-btn:hover {
            background-color: #bb2d3b;
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Summary section at bottom of table */
        .order-totals {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #dee2e6;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            font-size: 1.1rem;
        }

        .total-row.final {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            border-top: 2px solid #dee2e6;
            margin-top: 0.5rem;
            padding-top: 1rem;
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

    <div class="sidenav" id="Sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Home</a>
        <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
        <a href="changepass.php"><i class="fas fa-key me-2"></i>Change password</a>
        <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Cart</a>
        <a href="order.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
    </div>

    <div class="container">
        <div class="left-column">
            <h2>Shipping Information</h2>
            <form id="checkout-form" method="POST" action="process_order.php<?php echo isset($_GET['isbn']) ? '?isbn=' . urlencode($_GET['isbn']) : ''; ?>">
                <!-- Existing form fields -->
                <div class="shipping-form">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $fname; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $lname; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="+639123456789" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="zipcode" class="form-label">Zipcode</label>
                        <input type="text" class="form-control" id="zipcode" name="zipcode" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100" onclick="previewAddress()">Save Address</button>
                </div>

                <div class="address-preview" id="address-preview" style="display: none;">
                    <h3><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h3>
                    <p>
                        <strong>Email:</strong>
                        <span id="preview-email"></span>
                    </p>
                    <p>
                        <strong>Name:</strong>
                        <span id="preview-name"></span>
                    </p>
                    <p>
                        <strong>Mobile Number:</strong>
                        <span id="preview-mobile"></span>
                    </p>
                    <p>
                        <strong>Address:</strong>
                        <span id="preview-address"></span>
                    </p>
                    <p>
                        <strong>City:</strong>
                        <span id="preview-city"></span>
                    </p>
                    <p>
                        <strong>State/Province:</strong>
                        <span id="preview-state"></span>
                    </p>
                    <p>
                        <strong>Zipcode:</strong>
                        <span id="preview-zipcode"></span>
                    </p>
                    <p>
                        <strong>Payment Method:</strong>
                        <span id="preview-payment-method"></span>
                    </p>
                    <button type="button" class="edit-address-btn" onclick="editAddress()">
                        <i class="fas fa-edit"></i>
                        Edit Address
                    </button>
                </div>
            </form>


            <div class="order-summary">
                <h2><i class="fas fa-shopping-basket me-2"></i>Order Summary</h2>
                <?php if (!empty($items)): ?>
                    <div class="table-responsive">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Items</th>
                                    <th>Author Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price Each</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr data-isbn="<?php echo htmlspecialchars($item['isbn']); ?>">
                                        <td>
                                            <div class="item-details">
                                                <img src="images/<?php echo htmlspecialchars($item['book_image']); ?>" alt="Book">
                                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['author'] ?? 'Unknown'); ?></td>
                                        <td class="text-center">
                                            <span class="item-quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                        </td>
                                        <td class="text-end item-price">₱<?php echo number_format($item['price'], 2, '.', ','); ?></td>
                                        <td class="text-end item-price">₱<?php echo number_format($item['total'], 2, '.', ','); ?></td>
                                        <!-- <td class="text-center">
                                            <button class="remove-btn" onclick="removeItem('<?php echo $item['isbn']; ?>')" title="Remove item">×</button>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="h5 text-muted">No items in the checkout.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-column">
            <div class="price-summary">
                <h3>Summary</h3>
                <?php if (!empty($items)): ?>
                    <div class="total-price">
                        <p>Items: <?php echo $cart_count; ?></p>
                        <p>Shipping: ₱<?php echo number_format($shipping_cost, 2, '.', ','); ?></p>
                        <hr style="margin: 15px 0;">
                        <p><strong>Total Price:</strong> ₱<?php echo number_format($total_with_shipping, 2, '.', ','); ?></p>
                    </div>
                <?php else: ?>
                    <p>No items in the checkout.</p>
                <?php endif; ?>
            </div>

            <button type="button" onclick="submitOrder()" class="checkout-btn">Place Order</button>
        </div>
    </div>

    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

        function previewAddress() {
            // Get form values
            const email = document.getElementById('email').value;
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const mobile = document.getElementById('mobile').value;
            const address = document.getElementById('address').value;
            const city = document.getElementById('city').value;
            const state = document.getElementById('state').value;
            const zipcode = document.getElementById('zipcode').value;
            const paymentMethod = document.getElementById('payment_method').value;

            // Validate that all fields are filled
            if (!email || !firstName || !lastName || !mobile || !address || !city || !state || !zipcode || !paymentMethod) {
                alert("Please fill in all shipping information fields.");
                return;
            }

            // Populate preview
            document.getElementById('preview-email').textContent = email;
            document.getElementById('preview-name').textContent = firstName + " " + lastName;
            document.getElementById('preview-mobile').textContent = mobile;
            document.getElementById('preview-address').textContent = address;
            document.getElementById('preview-city').textContent = city;
            document.getElementById('preview-state').textContent = state;
            document.getElementById('preview-zipcode').textContent = zipcode;
            document.getElementById('preview-payment-method').textContent = paymentMethod;

            // Hide the shipping form inputs and show the preview
            document.querySelector('.shipping-form').style.display = 'none';
            document.getElementById('address-preview').style.display = 'block';
        }

        function editAddress() {
            // Hide preview and show the shipping form inputs
            document.getElementById('address-preview').style.display = 'none';
            document.querySelector('.shipping-form').style.display = 'block';
        }

        function submitOrder() {
            const form = document.getElementById('checkout-form');
            const paymentMethod = document.getElementById('payment_method').value;

            // Validate form fields
            const requiredFields = ['email', 'first_name', 'last_name', 'mobile', 'address', 'city', 'state', 'zipcode'];
            for (let field of requiredFields) {
                if (!document.getElementById(field).value) {
                    alert('Please fill in all required fields');
                    return;
                }
            }

            if (paymentMethod === 'gcash') {
                // Store form data in session before redirecting
                const formData = new FormData(form);
                fetch('store_checkout_data.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'login.gcash.php';
                        } else {
                            alert(data.error || 'Error storing checkout data');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error processing your request');
                    });
            } else {
                form.submit();
            }
        }

        // Replace the existing removeItem function in checkout.php
        function removeItem(isbn) {
            if (confirm('Are you sure you want to remove this item?')) {
                const formData = new FormData();
                formData.append('isbn', isbn);

                fetch('remove_cart_item.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the item row from the table
                            const row = document.querySelector(`tr[data-isbn="${isbn}"]`);
                            if (row) {
                                row.remove();
                            }

                            // Update cart counter
                            const cartCounter = document.getElementById('cart-counter');
                            const currentCount = parseInt(cartCounter.textContent);
                            cartCounter.textContent = currentCount - 1;

                            // Update totals
                            let subtotal = 0;
                            const shipping = 100;
                            document.querySelectorAll('.cart-table tbody tr').forEach(row => {
                                const total = parseFloat(row.querySelector('.item-price:last-child').textContent.replace('₱', '').replace(',', ''));
                                subtotal += total;
                            });

                            // Update summary display
                            const total = subtotal + shipping;
                            document.querySelector('.total-price p:first-child').textContent = `Items: ${document.querySelectorAll('.cart-table tbody tr').length}`;
                            document.querySelector('.total-price p:last-child strong').textContent = `₱${total.toFixed(2)}`;

                            // If no items left, reload the page
                            if (document.querySelectorAll('.cart-table tbody tr').length === 0) {
                                window.location.reload();
                            }
                        } else {
                            alert('Failed to remove item: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error removing item. Please try again.');
                    });
            }
        }

        function updateOrderCounter() {
            fetch('order_counter.php')
                .then(response => response.text())
                .then(count => {
                    document.getElementById("order-counter").innerText = count;
                })
                .catch(error => console.error('Error updating order counter:', error));
        }

        // Update both counters when page loads
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