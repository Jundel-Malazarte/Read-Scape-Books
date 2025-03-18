<?php
@include '../db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get admin details from session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$profile_image = '../uploads/default.jpg'; // Default admin image

// Get the last ISBN from the database
$last_isbn_query = "SELECT MAX(CAST(isbn AS UNSIGNED)) as last_isbn FROM books";
$result = mysqli_query($conn, $last_isbn_query);
$row = mysqli_fetch_assoc($result);
$next_isbn = ($row['last_isbn'] ? $row['last_isbn'] + 1 : 1000); // Start from 1000 if no books exist

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the next ISBN
    $last_isbn_query = "SELECT MAX(CAST(isbn AS UNSIGNED)) as last_isbn FROM books";
    $result = mysqli_query($conn, $last_isbn_query);
    $row = mysqli_fetch_assoc($result);
    $isbn = ($row['last_isbn'] ? $row['last_isbn'] + 1 : 1000);

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $copyright = mysqli_real_escape_string($conn, $_POST['copyright']);
    $qty = (int)$_POST['qty'];
    $price = (float)$_POST['price'];
    $total = $qty * $price;

    // Handle book image upload
    $book_image = "default.jpg"; // Default image in case of failure
    if (!empty($_FILES['book_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES["book_image"]["name"]);
        $target_dir = "../images/";
        $target_file = $target_dir . $image_name;

        // Ensure directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
            $book_image = $image_name;
        } else {
            die("File upload failed: " . $_FILES['book_image']['error']);
        }
    }

    // Insert book into database
    $sql = "INSERT INTO books (isbn, title, book_image, author, copyright, qty, price, total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssiid", $isbn, $title, $book_image, $author, $copyright, $qty, $price, $total);


    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Book added successfully!'); window.location.href='total_books.php';</script>";
    } else {
        echo "<script>alert('Failed to add book.');</script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #212529;
            padding: 1rem;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #343a40;
            border-radius: 5px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .container {
            max-width: 600px;
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .form-control {
            margin-bottom: 1rem;
        }

        .btn-success {
            width: 100%;
            padding: 0.75rem;
            font-weight: 500;
        }

        h1 {
            color: #212529;
            margin-bottom: 2rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="nav-links">
                <a href="../admin/admin_dashboard.php" class="btn btn-outline-light">Home</a>
                <a href="total_books.php" class="btn btn-outline-light">View Books</a>
            </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Add New Book</h1>
        <form action="add_book.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">

                <input type="number" class="form-control" name="isbn" value="<?php echo $next_isbn; ?>" readonly>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="title" placeholder="Title" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="author" placeholder="Author" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="copyright" placeholder="Copyright Year" required>
            </div>
            <div class="mb-3">
                <input type="number" class="form-control" name="qty" placeholder="Quantity" required>
            </div>
            <div class="mb-3">
                <input type="number" step="0.01" class="form-control" name="price" placeholder="Price" required>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="book_image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Add Book</button>
        </form>
    </div>

    <!-- Add Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>