<?php
@include 'db_connect.php';
session_start();

// Redirect to login if not logged in
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

// Fetch purchased items
$sql = "SELECT books.title, books.book_image, books.author, order_items.quantity, order_items.price, orders.order_date 
        FROM orders 
        JOIN order_items ON orders.id = order_items.order_id 
        JOIN books ON order_items.book_id = books.isbn 
        WHERE orders.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$purchased_items = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .readscape {
            border-radius: 50%;
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

        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 90%;
            margin: 20px auto;
        }

        .header-text h2 {
            font-size: 22px;
            margin: 0;
        }

        .order-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 90%;
            margin: 20px auto;
        }

        .order-card {
            display: flex;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s;
        }

        .order-card:hover {
            transform: scale(1.02);
        }

        .order-card img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }

        .order-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .order-details h3 {
            font-size: 18px;
            margin: 0;
        }

        .order-details p {
            font-size: 15px;
            color: #444;
            margin: 5px 0;
        }

        .order-details .order-date {
            font-size: 14px;
            color: #888;
        }

        .order-details .order-total {
            font-size: 16px;
            font-weight: bold;
            color: #333;
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

    </style>
</head>

<body>
    <div class="navbar">
        <div id="Sidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="changepass.php">Change password</a>
            <a href="cart.php">Cart</a>
            <a href="order.php">My Orders</a>
            <a href="logout.php">Log Out</a>
        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">&#9776; 
            <img src="./images/Readscape.png" alt="logo" class="readscape" width="40px" height="40px"></span>
        <script>
            function openNav() {
                document.getElementById("Sidenav").style.width = "240px";
            }

            function closeNav() {
                document.getElementById("Sidenav").style.width = "0";
            }
        </script>
        <div class="profile-info">
            <a href="cart.php" style="position: relative; color: white; text-decoration: none;">
                🛒 Cart <span id="cart-counter" style="background: red; color: white; border-radius: 50%; padding: 5px 10px; font-size: 14px; position: absolute; top: -5px; right: -10px;">0</span>
            </a>
            <br>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="search-header">
        <div class="header-text">
            <h2>My Orders!</h2>
        </div>
    </div>
    <div class="container">
        <div class="order-list">
            <?php while ($item = mysqli_fetch_assoc($purchased_items)) { ?>
                <div class="order-card">
                    <img src="<?php echo htmlspecialchars($item['book_image']); ?>" alt="Book Image">
                    <div class="order-details">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p>Author: <?php echo htmlspecialchars($item['author']); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p class="order-total">Total Price: ₱<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></p>
                        <p class="order-date">Order Date: <?php echo htmlspecialchars($item['order_date']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        document.getElementById("search-input").addEventListener("keyup", function() {
            let query = this.value.trim();
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_books.php?q=" + encodeURIComponent(query), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("book-list").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });

        function buyNow(isbn) {
            alert("Redirecting to checkout for book ISBN: " + isbn);
            window.location.href = "checkout.php?isbn=" + isbn;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const bookList = document.querySelector(".book-list");

            bookList.addEventListener("wheel", function(event) {
                event.preventDefault();
                bookList.scrollLeft += event.deltaY; // Convert vertical scroll to horizontal
            });
        });

        function addToCart(isbn) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "add_to_cart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    updateCartCounter(); // Update the cart counter after adding
                }
            };

            xhr.send("isbn=" + encodeURIComponent(isbn));
        }

        function updateCartCounter() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "cart_counter.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("cart-counter").innerText = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Call updateCartCounter() when page loads to show correct count
        document.addEventListener("DOMContentLoaded", updateCartCounter);
    </script>
</body>

</html>