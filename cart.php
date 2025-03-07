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

        .readscape {
            border-radius: 50%;
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

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .cart-table th,
        .cart-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .cart-table th {
            font-size: 14px;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
        }

        .cart-table td {
            font-size: 14px;
            color: #333;
        }

        .cart-table .item-details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-table .item-details img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-table .item-details h3 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .cart-table .item-details p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .cart-table .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 2px 5px;
            width: fit-content;
        }

        .cart-table .quantity-control button {
            background-color: #f0f0f0;
            color: #333;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 3px;
        }

        .cart-table .quantity-control button:hover {
            background-color: #ddd;
        }

        .cart-table .quantity-control .quantity {
            font-size: 14px;
            min-width: 20px;
            text-align: center;
        }

        .cart-table .remove-btn {
            background: none;
            border: none;
            color: #666;
            font-size: 16px;
            cursor: pointer;
        }

        .cart-table .remove-btn:hover {
            color: #dc3545;
        }

        .cart-right {
            flex: 3;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .order-summary h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1a2b49;
            margin-bottom: 20px;
        }

        .summary-details {
            margin-bottom: 15px;
        }

        .summary-details p {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin: 10px 10px;
            line-height: 1.5;
        }

        .summary-details .total {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin: 10px 10px;
            line-height: 1.5;
        }

        .shipping-options {
            margin: 10px 10px;
        }

        .shipping-options select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 5px;
        }

        .payment-methods {
            margin: 10px 10px;
        }

        .payment-methods select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 5px;
        }

        .checkout-btn {
            display: block;
            max-width: 100%;
            background-color: #000;
            color: white;
            padding: 15px;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
            margin-top: 20px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background-color: #333;
        }

        .checkout-btn:disabled {
            background-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
        }

        .checkout-btn:disabled:hover {
            background-color: #cccccc;
        }

        @media (max-width: 768px) {
            .cart-container {
                flex-direction: column;
            }

            .cart-left,
            .cart-right {
                width: 100%;
            }

            .cart-table {
                display: block;
                overflow-x: auto;
            }
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
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰
            <img src="./images/Readscape.png" alt="logo" class="readscape" width="40px" height="40px"></span>
        <div class="profile-info">
            <a href="cart.php" style="position: relative; color: white; text-decoration: none;">
                🛒 Cart <span id="cart-counter" style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;"><?php echo $cart_count; ?></span>
            </a>
            <br>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><strong><?php echo $fname . " " . $lname; ?></strong></a>
            <a href="logout.php"><strong>Log Out</strong></a>
        </div>
    </div>

    <div class="cart-wrapper">
        <div class="cart-container">
            <div class="cart-left">
                <h2 class="cart-header">Your Cart (<?php echo $cart_count; ?> items)</h2>
                <table class="cart-table">
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
                        <?php while ($cart_item = mysqli_fetch_assoc($result)): ?>
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
                                        <button class="decrement-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', -1)">-</button>
                                        <span class="quantity"><?php echo htmlspecialchars($cart_item['quantity']); ?></span>
                                        <button class="increment-btn" onclick="updateQuantity('<?php echo $cart_item['isbn']; ?>', 1)">+</button>
                                    </div>
                                </td>
                                <td>₱<?php echo number_format($cart_item['price'], 2, '.', ','); ?></td>
                                <td>
                                    <button class="remove-btn" onclick="removeFromCart('<?php echo $cart_item['isbn']; ?>')">X</button>
                                </td>
                            </tr>
                            <?php $total_price += $cart_item['total']; ?>
                        <?php endwhile; ?>
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
                            <select id="shipping-method" name="shipping_method">
                                <option value="standard">Standard Delivery - ₱100.00</option>
                                <option value="express">Express Delivery - ₱200.00</option>
                            </select>
                        </div>
                        <p class="total"><span>Total Price</span><span id="total-price">₱<?php echo number_format($total_price + 100, 2, '.', ','); ?></span></p>
                    </div>
                    <a href="checkout.php" class="checkout-btn" id="checkout-btn" <?php if ($cart_count == 0) echo 'disabled'; ?>>Checkout</a>
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
                        document.getElementById("total-price").innerText = "₱" + response.new_total_price;

                        // Update cart count and button state after quantity change
                        updateCartState();
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("isbn=" + isbn + "&new_quantity=" + newQuantity);
        }

        // Function to update cart state (cart count and checkout button)
        function updateCartState() {
            const cartItems = document.querySelectorAll("#cart-items tr");
            const cartCount = cartItems.length;
            const checkoutBtn = document.getElementById("checkout-btn");

            if (cartCount === 0) {
                checkoutBtn.setAttribute("disabled", "disabled");
                checkoutBtn.setAttribute("title", "Add items to your cart to proceed to checkout");
            } else {
                checkoutBtn.removeAttribute("disabled");
                checkoutBtn.removeAttribute("title");
            }
        }

        // Initial cart state check
        window.onload = function() {
            updateCartState();

            // Add event listener to the checkout button to prevent default behavior if disabled
            const checkoutBtn = document.getElementById("checkout-btn");
            checkoutBtn.addEventListener("click", function(event) {
                if (checkoutBtn.hasAttribute("disabled")) {
                    event.preventDefault();
                    alert("Your cart is empty. Please add items to proceed to checkout.");
                }
            });
        };
    </script>
</body>

</html>