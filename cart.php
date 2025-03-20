<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$cart = $_SESSION['cart'] ?? [];

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

// Fetch cart items with stock check
$sql = "SELECT books.isbn, books.title, books.book_image, books.price, books.author, cart.quantity, 
        books.qty AS stock, (books.price * cart.quantity) AS item_total 
    FROM cart 
    JOIN books ON cart.isbn = books.isbn 
    WHERE cart.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch cart count
$sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
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

$total_price = 0;
$stock_errors = []; // Array to store stock-related errors
$cart_items = []; // Array to store cart items for display
while ($cart_item = mysqli_fetch_assoc($result)) {
    if ($cart_item['quantity'] > $cart_item['stock']) {
        $stock_errors[] = "Not enough stock for '" . htmlspecialchars($cart_item['title']) . "'. Available: " . $cart_item['stock'];
    }
    $total_price += $cart_item['item_total']; // Sum the item totals
    $cart_items[] = $cart_item; // Store items for display
}
// No need to reset pointer since we’re using a separate array now

// Default shipping cost (can be updated via the dropdown)
$shipping_cost = 100.00; // Standard shipping from your dropdown
$total_with_shipping = $total_price + $shipping_cost;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-bottom: 100px;
        }

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

        /* Profile section in navbar */
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

        /* Your existing cart styles below... */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding-bottom: 2rem;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #212529;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        /* Main Container Styles */
        .cart-wrapper {
            padding: 2rem;
            background-color: #f8f9fa;
        }

        .cart-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            padding: 1rem;
        }

        /* Left Cart Section */
        .cart-left {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
        }

        .cart-header {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        /* Table Styles */
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        .cart-table thead th {
            padding: 1rem;
            color: #6c757d;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .cart-table tbody td {
            padding: 1.5rem 1rem;
            vertical-align: middle;
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        /* Item Details */
        .item-details {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .item-details img {
            width: 100px;
            height: 150px;
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

        /* Quantity Controls */
        .quantity-control {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.5rem;
        }

        .quantity-control button {
            background: #e9ecef;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-control button:hover:not([disabled]) {
            background: #dee2e6;
        }

        .quantity-control .quantity {
            padding: 0 1rem;
            font-weight: 600;
        }

        /* Right Cart Section */
        .cart-right {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .order-summary h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-details p {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            color: #495057;
        }

        .summary-details .total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #212529;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
        }

        /* Buttons */
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: #0d6efd;
            color: white;
            text-align: center;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 1.5rem;
        }

        .checkout-btn:hover:not([disabled]) {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .checkout-btn[disabled] {
            background-color: #6c757d !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
            pointer-events: none;
            opacity: 0.65;
        }

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

        /* Select Dropdowns */
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            color: #495057;
            margin-top: 0.5rem;
        }

        /* Error Messages */
        .stock-error {
            background: #f8d7da;
            color: #842029;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .cart-right {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .item-details {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .cart-table thead {
                display: none;
            }

            .cart-table tbody td {
                display: block;
                text-align: center;
                padding: 0.5rem;
            }

            .cart-table td::before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                margin-bottom: 0.5rem;
            }
        }


        .alert .btn-close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            padding: 0.5rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .alert .btn-close:hover {
            opacity: 1;
        }

        .alert {
            position: relative;
            padding-right: 3rem;
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

    <div class="cart-wrapper container-fluid">
        <div class="cart-container">
            <div class="cart-left">
                <h2 class="cart-header">Your Cart (<?php echo $cart_count; ?> items)</h2>

                <?php if (!empty($stock_errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong><i class="fas fa-exclamation-circle me-2"></i>Stock Issues Found!</strong>
                        <?php foreach ($stock_errors as $error): ?>
                            <div class="mt-2"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <table class="cart-table table">
                    <thead>
                        <tr>
                            <th>Item/s</th>
                            <th>Author Name</th>
                            <th>Quantity</th>
                            <th>Price Each</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        <?php foreach ($cart_items as $cart_item): ?>
                            <tr id="cart-item-<?php echo $cart_item['isbn']; ?>">
                                <td>
                                    <div class="item-details">
                                        <img src="images/<?php echo htmlspecialchars($cart_item['book_image']); ?>" alt="Book">
                                        <div>
                                            <h3><?php echo htmlspecialchars($cart_item['title']); ?></h3>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($cart_item['author'] ?? 'Unknown'); ?></td>
                                <td>
                                    <div class="quantity-control">
                                        <button class="decrement-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', -1)" <?php echo ($cart_item['quantity'] <= 1) ? 'disabled' : ''; ?>>-</button>
                                        <span class="quantity"><?php echo htmlspecialchars($cart_item['quantity']); ?></span>
                                        <button class="increment-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', 1)" <?php echo ($cart_item['quantity'] >= $cart_item['stock']) ? 'disabled' : ''; ?>>+</button>
                                    </div>
                                </td>
                                <td>₱<?php echo number_format($cart_item['price'], 2, '.', ','); ?></td>
                                <td>
                                    <button class="remove-btn btn-close-white" onclick="removeFromCart('<?php echo $cart_item['isbn']; ?>')"
                                        title="Remove item">
                                        ×
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="cart-right">
                <div class="order-summary">
                    <h3>Summary</h3>
                    <div class="summary-details">
                        <p><span>Items <?php echo $cart_count; ?></span><span id="subtotal-price">₱<?php echo number_format($total_price, 2, '.', ','); ?></span></p>
                        <div class="shipping-options">
                            <span>Shipping</span>
                            <select id="shipping-method" name="shipping_method" onchange="updateTotal()">
                                <option value="100">Standard Delivery - ₱100.00</option>
                            </select>
                        </div>
                        <p class="total"><span>Total Price</span><span id="total-price">₱<?php echo number_format($total_with_shipping, 2, '.', ','); ?></span></p>
                    </div>
                    <a href="checkout.php" class="checkout-btn" id="checkout-btn" <?php echo (!empty($stock_errors) || $cart_count == 0) ? 'disabled' : ''; ?>>Checkout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

        function removeFromCart(isbn) {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "remove_from_cart.php?isbn=" + isbn, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert("Failed to remove item from cart.");
                    }
                }
            };
            xhr.send();
        }

        function updateQuantity(isbn, change) {
            const cartItem = document.getElementById("cart-item-" + isbn);
            const quantitySpan = cartItem.querySelector(".quantity");
            const currentQuantity = parseInt(quantitySpan.innerText);
            const newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
                alert("Quantity cannot be less than 1. Use the remove button to delete the item.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_cart_quantity.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        quantitySpan.innerText = response.new_quantity;
                        document.getElementById("subtotal-price").innerText = "₱" + response.new_subtotal;
                        updateTotal(); // Update total price including shipping
                        updateCartState(); // Update cart state and button
                    } else {
                        alert(response.message);
                        location.reload(); // Reload to reflect stock changes
                    }
                }
            };
            xhr.send("isbn=" + isbn + "&new_quantity=" + newQuantity);
        }

        function updateTotal() {
            const shippingMethod = parseFloat(document.getElementById("shipping-method").value);
            const subtotal = parseFloat(document.getElementById("subtotal-price").innerText.replace('₱', '').replace(',', ''));
            const totalPrice = subtotal + shippingMethod;
            document.getElementById("total-price").innerText = "₱" + totalPrice.toFixed(2);
        }

        // Add this function to handle stock validation
        function hasStockIssues() {
            return document.querySelector('.alert-danger') !== null;
        }

        // Update the updateCartState function
        function updateCartState() {
            const cartItems = document.querySelectorAll("#cart-items tr");
            const cartCount = cartItems.length;
            const checkoutBtn = document.getElementById("checkout-btn");
            const stockIssues = hasStockIssues();

            if (cartCount === 0 || stockIssues) {
                checkoutBtn.setAttribute("disabled", "disabled");
                checkoutBtn.style.backgroundColor = "#6c757d";
                checkoutBtn.style.cursor = "not-allowed";

                if (stockIssues) {
                    checkoutBtn.setAttribute("title", "Please resolve stock issues before proceeding to checkout");
                } else {
                    checkoutBtn.setAttribute("title", "Add items to your cart to proceed to checkout");
                }
            } else {
                checkoutBtn.removeAttribute("disabled");
                checkoutBtn.removeAttribute("title");
                checkoutBtn.style.backgroundColor = "#0d6efd";
                checkoutBtn.style.cursor = "pointer";
            }
        }

        function updateOrderCounter() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "order_counter.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("order-counter").innerText = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Update the DOMContentLoaded event listener
        document.addEventListener("DOMContentLoaded", function() {
            updateCartCounter(); // If this exists
            updateOrderCounter();
        });

        // Update the checkout button event listener
        window.onload = function() {
            updateCartState();
            updateTotal();

            const checkoutBtn = document.getElementById("checkout-btn");
            checkoutBtn.addEventListener("click", function(event) {
                if (checkoutBtn.hasAttribute("disabled")) {
                    event.preventDefault();
                    if (hasStockIssues()) {
                        alert("Cannot proceed to checkout. Please resolve stock issues first.");
                    } else {
                        alert("Cannot proceed to checkout. Please add items to your cart.");
                    }
                }
            });
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>