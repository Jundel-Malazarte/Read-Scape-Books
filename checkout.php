<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

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

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .left-column {
            flex: 2;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .right-column {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        .item-details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-details img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .item-details h3 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .shipping-form,
        .address-preview {
            margin-top: 20px;
        }

        .shipping-form label,
        .address-preview p {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .shipping-form input,
        .shipping-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .shipping-form textarea {
            height: 100px;
            resize: vertical;
        }

        .edit-address-btn,
        .checkout-btn {
            background-color: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }

        .edit-address-btn:hover,
        .checkout-btn:hover {
            background-color: #333;
        }

        .address-preview {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }

        .price-summary h3,
        .payment-methods h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1a2b49;
            margin-bottom: 20px;
        }

        .total-price {
            font-size: 16px;
            margin-top: 15px;
        }

        .payment-methods select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 5px;
        }

        .readscape {
            border-radius: 50%;
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
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">☰<strong> ReadScape</strong> <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>
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

    <div class="container">
        <div class="left-column">
            <h2>Shipping Information</h2>
            <!-- Inside the <form> in checkout.php -->
            <form id="checkout-form" method="POST" action="process_order.php<?php echo isset($_GET['isbn']) ? '?isbn=' . urlencode($_GET['isbn']) : ''; ?>">
                <!-- Existing form fields -->
                <div class="shipping-form">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $fname; ?>" required>

                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $lname; ?>" required>

                    <label for="mobile">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="+639123456789" required>

                    <label for="address">Address</label>
                    <textarea id="address" name="address" required></textarea>

                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>

                    <label for="state">State/Province</label>
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
                    <p><strong>State/Province:</strong> <span id="preview-state"></span></p>
                    <p><strong>Zipcode:</strong> <span id="preview-zipcode"></span></p>
                    <button type="button" class="edit-address-btn" onclick="editAddress()">Edit Address</button>
                </div>
            </form>

            <h2 style="margin-top: 30px;">Order Summary</h2>
            <div class="order-summary" id="order-summary">
                <?php if (!empty($items)): ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Items</th>
                                <th>Author Name</th>
                                <th>Quantity</th>
                                <th>Price Each</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
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
                                    <td>₱<?php echo number_format($item['price'], 2, '.', ','); ?></td>
                                    <td>₱<?php echo number_format($item['total'], 2, '.', ','); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No items in the checkout.</p>
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

            <div class="payment-methods">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="gcash">GCash</option>
                </select>
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

            // Hide the shipping form inputs and show the preview
            document.querySelector('.shipping-form').style.display = 'none';
            document.getElementById('address-preview').style.display = 'block';
        }

        function editAddress() {
            // Hide preview and show the shipping form inputs
            document.getElementById('address-preview').style.display = 'none';
            document.querySelector('.shipping-form').style.display = 'block';
        }

        // Check if items exist and adjust display
        window.onload = function() {
            const orderSummary = document.getElementById('order-summary');
            if (!orderSummary.querySelector('table')) {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = 'No items in the checkout.';
                orderSummary.appendChild(emptyMessage);
            }
        };

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
                }).then(() => {
                    window.location.href = 'login.gcash.php';
                });
            } else {
                form.submit();
            }
        }
    </script>
</body>

</html>