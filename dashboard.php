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

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT isbn, title, book_image, author, copyright, qty, price, total FROM books";
if (!empty($search)) {
    $query .= " WHERE title LIKE ? OR author LIKE ?";
}



$stmt = mysqli_prepare($conn, $query);

if (!empty($search)) {
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
}

mysqli_stmt_execute($stmt);
$books_query = mysqli_stmt_get_result($stmt);

// Fetch total books count
$total_books_query = mysqli_query($conn, "SELECT COUNT(*) FROM books");
$total_books = mysqli_fetch_row($total_books_query)[0];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
    </style>
</head>

<body>
    <div class="navbar">
        <!-- Logo here! -->
        <div id="Sidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="changepass.php">Change password</a>
            <a href="cart.php">Cart</a>

        </div>
        <span style="font-size:30px;cursor:pointer;color:white;" onclick="openNav()">&#9776; <img src="./images/Readscape.png" alt="logo" class="readscape" width="50px" height="50px"></span>

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
    <!-- <h2>Welcome, <?php echo $fname . " " . $lname; ?></h2> -->

    <div class="search-header">
        <div class="header-text">
            <h2>Picked for you</h2>
        </div>
        <div class="search-box">
            <form method="GET" action="dashboard.php">
                <input type="text" name="search" id="search-input" placeholder="Search for books..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="book-list" id="book-list">
        <?php if (mysqli_num_rows($books_query) > 0) : ?>
            <?php while ($book = mysqli_fetch_assoc($books_query)) : ?>
                <div class="book-card">
                    <img src="<?php echo 'images/' . htmlspecialchars($book['book_image']); ?>" alt="Book Image" onerror="this.onerror=null; this.src='uploads/default.jpg';">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <!-- <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p> -->
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Year Published:</strong> <?php echo htmlspecialchars($book['copyright']); ?></p>
                    <p><strong>Stocks:</strong> <?php echo htmlspecialchars($book['qty']); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo htmlspecialchars($book['price']); ?></p>
                    <!-- <p><strong>Total:</strong> ₱<?php echo htmlspecialchars($book['total']); ?></p> -->
                    <div class="button-group">
                        <button class="add-cart-btn" onclick="addToCart('<?php echo htmlspecialchars($book['isbn']); ?>')">
                            🛒 Add to Cart
                        </button>

                        <button class="buy-now-btn" onclick="buyNow('<?php echo htmlspecialchars($book['isbn']); ?>')">
                        <a href="checkout.php">⚡ Buy Now</a>
                        </button>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else : ?>
            <p>No books found</p>
        <?php endif; ?>
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