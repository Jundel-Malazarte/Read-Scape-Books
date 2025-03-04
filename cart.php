<?php
@include 'db_connect.php';
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

// Fetch cart items
$sql = "SELECT books.isbn, books.title, books.book_image, books.price, books.author, cart.quantity, 
        (books.price * cart.quantity) AS total 
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

$total_price = 0;
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

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
            /* Adjust spacing as needed */
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

        /* Container for search and header */
        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 90%;
            margin: 20px auto;
        }

        /* Adjusted search input */
        .search-box {
            display: flex;
            align-items: center;
        }

        .search-box input {
            width: 350px;
            /* Slightly wider */
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        /* Bigger search button */
        .search-box button {
            padding: 12px 18px;
            font-size: 16px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background-color: #555;
        }

        /* Adjusted header */
        .header-text h2 {
            font-size: 22px;
            margin: 0;
        }


        .book-list {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 20px;
            max-width: 90%;
            /* Make the book list span a larger width */
            margin-left: auto;
            margin-right: auto;
        }

        .book-list::-webkit-scrollbar {
            height: 8px;
            /* Make scrollbar thinner */
        }

        .book-list::-webkit-scrollbar-thumb {
            background-color: #555;
            border-radius: 10px;
        }



        .book-card {
            width: 250px;
            /* Increased width */
            background: white;
            padding: 20px;
            /* Increased padding */
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.2s;
        }

        .book-card:hover {
            transform: scale(1.05);
            /* Slight zoom effect for better UX */
        }

        .book-card img {
            width: 100%;
            height: 275px;
            /* Increased height */
            object-fit: cover;
            border-radius: 10px;
        }

        .book-card h3 {
            font-size: 18px;
            /* Larger title */
            margin: 10px 0;
        }

        .book-card p {
            font-size: 15px;
            /* Increased text size */
            color: #444;
        }

        .buy-now-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 14px;
            /* Larger button */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            /* Increased font size */
            margin-top: 12px;
            width: 100%;
            transition: background 0.3s, transform 0.2s;
        }

        .buy-now-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .header-text {
            text-align: left;
            margin-top: 20px;
            margin-left: 20px;
        }

        span {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .readscape {
            width: 40px;
            /* Match this size with the font-size of the menu icon */
            height: 40px;
            /* Keep height and width equal */
            border-radius: 50%;
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

        .add-cart-btn {
            background-color: #dc3545;
            /* Red color */
            color: white;
            padding: 10px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 12px;
            width: 100%;
            transition: background 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            /* Space between icon and text */
        }

        .add-cart-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 12px;
        }

        .add-cart-btn,
        .buy-now-btn {
            flex: 1;
            text-align: center;
            padding: 10px 14px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .add-cart-btn {
            background-color: #dc3545;
            color: white;
        }

        .add-cart-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .buy-now-btn {
            background-color: #28a745;
            color: white;
        }

        .buy-now-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .cart-wrapper {
            background-color: #f9f9f9;
            padding: 20px;
        }

        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 20px;
        }

        .cart-left {
            flex: 7;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-header {
            font-size: 24px;
            font-weight: 700;
            color: #1a2b49;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }

        .cart-item img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-details {
            flex: 1;
        }

        .cart-details h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .cart-details p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .cart-details .price {
            font-size: 16px;
            font-weight: 700;
            color: #000;
        }

        .shipping-info {
            font-size: 12px;
            color: #999;
        }

        .cart-controls {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-control button {
            background-color: #555;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
        }

        .quantity-control button:hover {
            background-color: #777;
        }

        .quantity-control .quantity {
            font-size: 16px;
            min-width: 20px;
            text-align: center;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 18px;
            cursor: pointer;
        }

        .remove-btn:hover {
            color: darkred;
        }

        .cart-right {
            flex: 3;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .order-summary h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1a2b49;
            margin-bottom: 15px;
        }

        .summary-details p {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin: 10px 0;
        }

        .summary-details .total {
            font-size: 18px;
            font-weight: 700;
            color: #000;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background-color: #007bff;
            /* Blue background for Checkout button */
            color: white;
            padding: 15px;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background 0.3s, transform 0.2s;
            margin-bottom: 10px;
            /* Space between buttons */
        }

        .checkout-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .pay-with-link-btn {
            display: block;
            width: 100%;
            background-color: #28a745;
            /* Green background for Pay with Link button */
            color: white;
            padding: 15px;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background 0.3s, transform 0.2s;
        }

        .pay-with-link-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .cart-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding: 10px;
            border-top: 0px solid #ddd;
        }

        .total-price {
            font-size: 20px;
            font-weight: bold;
        }

        /* Remove button */
        .remove-btn {
            background: none;
            border: none;
            color: red;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remove-btn:hover {
            color: darkred;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-control button {
            background-color: #555;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .quantity-control button:hover {
            background-color: #777;
        }

        .quantity-control .quantity {
            font-size: 16px;
            min-width: 20px;
            text-align: center;
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
        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰ <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>
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


    <div class="cart-wrapper">
        <div class="cart-container">
            <div class="cart-left">
                <h2 class="cart-header">Your Cart (<?php echo $cart_count; ?> items)</h2>
                <?php while ($cart_item = mysqli_fetch_assoc($result)): ?>
                    <div class="cart-item" id="cart-item-<?php echo $cart_item['isbn']; ?>">
                        <img src="images/<?php echo htmlspecialchars($cart_item['book_image']); ?>" alt="Book">
                        <div class="cart-details">
                            <h3><?php echo htmlspecialchars($cart_item['title']); ?></h3>
                            <p>Author: <?php echo htmlspecialchars($cart_item['author'] ?? 'Unknown'); ?></p>
                            <p class="price">₱<?php echo number_format($cart_item['price'], 2, '.', ','); ?></p>
                            <p class="shipping-info">+ ₱50 shipping</p>
                        </div>
                        <div class="cart-controls">
                            <div class="quantity-control">
                                <button class="decrement-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', -1)">-</button>
                                <span class="quantity"><?php echo htmlspecialchars($cart_item['quantity']); ?></span>
                                <button class="increment-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', 1)">+</button>
                            </div>
                            <button class="remove-btn" onclick="removeFromCart('<?php echo $cart_item['isbn']; ?>')">🗑️</button>
                        </div>
                    </div>
                    <?php $total_price += $cart_item['total']; ?>
                <?php endwhile; ?>
            </div>
            <div class="cart-right">
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-details">
                        <p><span>Subtotal</span><span id="subtotal-price">₱<?php echo number_format($total_price, 2, '.', ','); ?></span></p>
                        <p><span>Shipping</span><span>₱100.00</span></p>
                        <p class="total"><span>Total</span><span id="total-price">₱<?php echo number_format($total_price + 100, 2, '.', ','); ?></span></p>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
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
                        location.reload(); // Refresh the page
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
                        // Update the quantity in the cart item
                        quantitySpan.innerText = response.new_quantity;
                        // Update the subtotal and total in the order summary
                        document.getElementById("subtotal-price").innerText = "₱" + response.new_subtotal;
                        document.getElementById("total-price").innerText = "₱" + response.new_total_price;
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("isbn=" + isbn + "&new_quantity=" + newQuantity);
        }
    </script>

</body>

</html>