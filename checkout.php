<?php
@include 'db_connect.php';
session_start();

// Redirect to sign-in if user is not logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user details with error handling
$sql = "SELECT fname, lname, profile_image FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $fname = htmlspecialchars($user['fname']);
    $lname = htmlspecialchars($user['lname']);
    $profile_image = htmlspecialchars($user['profile_image']) ?: "uploads/default.jpg";
} else {
    echo "Error: User not found.";
    exit();
}
mysqli_stmt_close($stmt);

// Fetch cart items with error handling
$sql = "SELECT books.isbn, books.title, books.book_image, books.price, books.author, cart.quantity, 
        (books.price * cart.quantity) AS total 
    FROM cart 
    JOIN books ON cart.isbn = books.isbn 
    WHERE cart.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total_price = 0;
$cart_items = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        $total_price += $row['total'];
    }
} else {
    $cart_items = []; // Ensure empty array if no items
}
mysqli_stmt_close($stmt);

// Fixed shipping cost (as per code, though image suggests $5.17)
$shipping_cost = 50.00; // Adjust to $5.17 if intended to match image
$grand_total = $total_price + $shipping_cost;

// Fetch cart count for navbar
$sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_count_result = mysqli_stmt_get_result($stmt);
$cart_count = mysqli_fetch_row($cart_count_result)[0];
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Readscape</title>
    <link rel="icon" href="images/Readscape.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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

        .navbar img {
            border-radius: 50%;
        }

        .readscape-logo {
            display: flex;
            align-items: center;
            gap: 10px;
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

        /** Profile info */
        
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


        /** Slider nav */
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

        span {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            display: flex;
            gap: 30px;
        }

        .left-column {
            padding: 5px;
            flex: 2;
        }

        .right-column {
            flex: 1;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            height: fit-content;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a2b49;
            margin-bottom: 20px;
        }

        .shipping-form {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .order-summary {
            margin-top: 30px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .cart-item img {
            width: 80px;
            height: 100px;
            margin-right: 15px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-details h3 {
            font-size: 18px;
            margin: 0 0 5px;
        }

        .cart-details p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .price-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .payment-section {
            margin-top: 30px;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .checkout-btn {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }

        .checkout-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .checkout-container {
                flex-direction: column;
            }

            .left-column,
            .right-column {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
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
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰ 
            <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>
        <div class="profile-info">
            <a href="cart.php" style="position: relative; color: white; text-decoration: none;">
                🛒 Cart <span id="cart-counter" style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;"><?php echo $cart_count; ?></span>
            </a>
            <br>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="checkout-container">
        <div class="left-column">
            <h2>Shipping Information</h2>
            <form id="checkout-form" action="process_checkout.php" method="post" onsubmit="return validateCheckout()">
                <div class="shipping-form">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>

                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $fname; ?>" required>

                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $lname; ?>" required>

                    <label for="mobile">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" required>

                    <label for="address">Address</label>
                    <textarea id="address" name="address" required></textarea>

                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>

                    <label for="state">State</label>
                    <input type="text" id="state" name="state" required>

                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" required>
                </div>
            </form>

            <h2 style="margin-top: 30px;">Order Summary</h2>
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <div class="order-summary">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="images/<?php echo htmlspecialchars($item['book_image']); ?>" alt="Book">
                            <div class="cart-details">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p>Author: <?php echo htmlspecialchars($item['author'] ?? 'Unknown'); ?></p>
                                <p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
                                <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-column">
            <div class="price-summary">
                <h3>Price Details</h3>
                <?php if (empty($cart_items)): ?>
                    <p>No items in the cart.</p>
                <?php else: ?>
                    <div class="total-price">
                        Subtotal: ₱<?php echo number_format($total_price, 2); ?><br>
                        Shipping: ₱<?php echo number_format($shipping_cost, 2); ?><br>
                        <hr style="margin: 15px 0;">
                        Total: ₱<?php echo number_format($grand_total, 2); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="payment-section">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" form="checkout-form" style="width: 100%; margin-top: 10px;">
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                </select>
                <button type="submit" form="checkout-form" class="checkout-btn">Place Order</button>
            </div>
        </div>
    </div>

    <!-- JavaScript for Navbar -->
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

        function validateCheckout() {
            <?php if (empty($cart_items)): ?>
                alert("Your cart is empty. Cannot proceed to checkout.");
                return false;
            <?php endif; ?>
            return true;
        }
    </script>
</body>

</html>