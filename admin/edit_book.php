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

// Check if ISBN is provided
if (!isset($_GET['isbn'])) {
    header("Location: total_books.php");
    exit();
}

$isbn = mysqli_real_escape_string($conn, $_GET['isbn']);

// Fetch book details
$sql = "SELECT * FROM books WHERE isbn = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $isbn);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $title = htmlspecialchars($row['title']);
    $author = htmlspecialchars($row['author']);
    $copyright = htmlspecialchars($row['copyright']);
    $qty = (int)$row['qty'];
    $price = number_format((float)$row['price'], 2);
    $book_image = !empty($row['book_image']) ? "../images/" . $row['book_image'] : "../images/default_book.png";
} else {
    header("Location: total_books.php");
    exit();
}

mysqli_stmt_close($stmt);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = mysqli_real_escape_string($conn, $_POST['title']);
    $new_author = mysqli_real_escape_string($conn, $_POST['author']);
    $new_copyright = mysqli_real_escape_string($conn, $_POST['copyright']);
    $new_qty = (int)$_POST['qty'];
    $new_price = number_format((float)$_POST['price'], 2, '.', '');
    $new_total = number_format($new_qty * $new_price, 2, '.', '');

    // Handle book image upload
    if (!empty($_FILES['book_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES["book_image"]["name"]);
        $target_dir = "../images/";
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
            $book_image = $image_name;
        }
    }

    // Update query
    $update_sql = "UPDATE books SET title = ?, author = ?, copyright = ?, qty = ?, price = ?, total = ?, book_image = ? WHERE isbn = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sssiddss", $new_title, $new_author, $new_copyright, $new_qty, $new_price, $new_total, $book_image, $isbn);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Book updated successfully!'); window.location.href='total_books.php';</script>";
    } else {
        echo "<script>alert('Failed to update book.');</script>";
    }

    mysqli_stmt_close($update_stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
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

        .book-image {
            max-width: 150px;
            margin: 0 auto 1rem;
            display: block;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="nav-links">
                <a href="../admin/admin_dashboard.php" class="btn btn-outline-light">Home</a>
                <a href="total_books.php" class="btn btn-outline-light">Back to Books</a>
            </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Edit Book</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="<?php echo $book_image; ?>" class="book-image" alt="Book Image">
            <div class="mb-3">
                <input type="text" class="form-control" name="title" value="<?php echo $title; ?>" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="author" value="<?php echo $author; ?>" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="copyright" value="<?php echo $copyright; ?>" required>
            </div>
            <div class="mb-3">
                <input type="number" class="form-control" name="qty" value="<?php echo $qty; ?>" required>
            </div>
            <div class="mb-3">
                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $price; ?>" required>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="book_image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Update Book</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>