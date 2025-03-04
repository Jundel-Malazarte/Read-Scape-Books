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
$shipping_cost = 100.00; // Adjust to $5.17 if intended to match image
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

        .checkout-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            display: flex;
            gap: 30px;
        }

        .left-column {
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

        .order-summary {
            margin-top: 30px;
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

        .address-preview {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .address-preview p {
            font-size: 14px;
            color: #333;
            margin: 5px 0;
        }

        .edit-address-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .edit-address-btn:hover {
            background-color: #0056b3;
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

        .payment-methods {
            margin: 20px 0;
        }

        .payment-methods select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 5px;
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
            background-color: #000;
            /* Updated to black to match cart.php */
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            width: 100%;
        }

        .checkout-btn:hover {
            background-color: #333;
            /* Updated hover color to match cart.php */
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
        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰ <img src="./images/Readscape.png" alt="logo" width="40px" height="40px" style="border-radius:50%;"></span>
        <div class="profile-info">
            <a href="cart.php" style="position: relative;">
                🛒 Cart <span style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;"><?php echo $cart_count; ?></span>
            </a>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="checkout-container">
        <div class="left-column">
            <h2>Shipping Information</h2>
            <form id="checkout-form" action="process_checkout.php" method="post">
                <div class="shipping-form" id="shipping-form">
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

                    <button type="button" class="edit-address-btn" onclick="previewAddress()">Save Address</button>
                </div>

                <div class="address-preview" id="address-preview" style="display: none;">
                    <h3>Shipping Address</h3>
                    <p><strong>Email:</strong> <span id="preview-email"></span></p>
                    <p><strong>Name:</strong> <span id="preview-name"></span></p>
                    <p><strong>Mobile Number:</strong> <span id="preview-mobile"></span></p>
                    <p><strong>Address:</strong> <span id="preview-address"></span></p>
                    <p><strong>City:</strong> <span id="preview-city"></span></p>
                    <p><strong>State:</strong> <span id="preview-state"></span></p>
                    <p><strong>Zipcode:</strong> <span id="preview-zipcode"></span></p>
                    <button type="button" class="edit-address-btn" onclick="editAddress()">Edit Address</button>
                </div>
            </form>

            <h2 style="margin-top: 30px;">Order Summary</h2>
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <div class="order-summary">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Items</th>
                                <th>Author Name</th>
                                <th>Quantity</th>
                                <th>Price Each</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="item-details">
                                            <img src="images/<?php echo htmlspecialchars($item['book_image']); ?>" alt="Book">
                                            <div>
                                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['author'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-column">
            <div class="price-summary">
                <h3>Summary</h3>
                <?php if (empty($cart_items)): ?>
                    <p>No items in the cart.</p>
                <?php else: ?>
                    <div class="total-price">
                        Items: <?php echo $cart_count; ?><br>
                        Shipping: ₱<?php echo number_format($shipping_cost, 2); ?><br>
                        <hr style="margin: 15px 0;">
                        Total Price: ₱<?php echo number_format($grand_total, 2); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="payment-methods">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" form="checkout-form">
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="credit_card">Credit Card</option>
                </select>
            </div>

            <button type="submit" form="checkout-form" class="checkout-btn">Place Order</button>
        </div>
    </div>

    <!-- JavaScript for Navbar and Address Preview -->
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

            // Validate that all fields are filled
            if (!email || !firstName || !lastName || !mobile || !address || !city || !state || !zipcode) {
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

            // Hide form and show preview
            document.getElementById('shipping-form').style.display = 'none';
            document.getElementById('address-preview').style.display = 'block';
        }

        function editAddress() {
            // Hide preview and show form
            document.getElementById('address-preview').style.display = 'none';
            document.getElementById('shipping-form').style.display = 'block';
        }
    </script>
</body>

</html>