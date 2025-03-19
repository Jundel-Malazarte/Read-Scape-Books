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
    <title>ReadScape Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
    <style>
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

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .search-header {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            margin: 20px auto;
            max-width: 1600px;
            width: 95%;
        }

        .search-box input {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 0.75rem;
            width: 100%;
            max-width: 400px;
            font-size: 1.1rem;
        }

        .search-box button {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .search-box button:hover {
            background-color: #0b5ed7;
        }

        .book-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.5rem;
            padding: 5px;
            max-width: 1600px;
            margin: 0 auto;
            width: 95%;
            justify-items: center;
            /* Center items horizontally */
        }

        .book-card {
            width: 100%;
            /* Fill the available space */
            max-width: 350px;
            /* Maximum width for consistency */
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            transition: transform 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            /* Stack children vertically */
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
        }

        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 1px solid #dee2e6;
        }

        .book-info {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            /* Allow info section to grow */
            gap: 0.1rem; /* adjust the gap */
            /* Add consistent spacing between elements */
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #212529;
        }

        .book-details {
            font-size: 1rem;
            color: #6c757d;
            line-height: 1.5;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1rem;
            margin-top: auto;
            width: 100%;
            align-items: center;
        }

        .btn-cart,
        .btn-buy {
            width: 120%;
            font-size: 1.1rem;
            padding: 0.875rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-cart i,
        .btn-buy i {
            font-size: 1.2rem;
        }

        .btn-cart:hover,
        .btn-buy:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #212529;
            color: #f8f9fa;
            padding: 1rem 0;
            font-size: 1.1rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .footer-links a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #0d6efd;
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

        @media (max-width: 768px) {
            .book-list {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .footer-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        .navbar .fw-bold {
            font-size: 1.2rem;
        }

        .dropdown-menu {
            font-size: 1.1rem;
        }

        .search-header h2 {
            font-size: 2rem;
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
                        <span class="cart-badge" id="cart-counter">0</span>
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
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }
    </script>

    <div class="main-container">
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
                        <div class="book-info">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-details"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-details"><strong>Year Published:</strong> <?php echo htmlspecialchars($book['copyright']); ?></p>
                            <p class="book-details"><strong>Stocks:</strong> <?php echo htmlspecialchars($book['qty']); ?></p>
                            <p class="book-details"><strong>Price:</strong> ₱<?php echo htmlspecialchars($book['price']); ?></p>
                            <div class="button-group">
                                <button class="btn-cart" onclick="addToCart('<?php echo htmlspecialchars($book['isbn']); ?>')">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <button class="btn-buy" onclick="buyNow('<?php echo htmlspecialchars($book['isbn']); ?>')">
                                    <i class="fas fa-bolt"></i> Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No books found</p>
            <?php endif; ?>
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
            window.location.href = "checkout.php?isbn=" + encodeURIComponent(isbn);
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

    <div class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="./images/Readscape.png" alt="Readscape Logo">
                <strong>ReadScape</strong>
            </div>
            <div class="footer-links">
                <a href="./pages/footer/about.php">About Us</a>
                <a href="./pages/footer/contact.php">Contact</a>
                <a href="./pages/footer/privacy.php">Privacy Policy</a>
                <a href="./pages/footer/terms.php">Terms of Service</a>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> ReadScape. All rights reserved.</p>
        </div>
    </div>
    <style>
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 90%;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .footer-logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .footer-social a {
            color: white;
            margin: 0 10px;
            font-size: 20px;
        }

        .footer-bottom {
            margin-top: 20px;
        }

        .book-card {
            width: 250px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>