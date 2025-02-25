<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
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

        .nav-links {
            display: flex;
            gap: 15px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="total_books.php">Back to Books</a>
        </div>
    </div>
    <div class="container">
        <h1>Edit Book</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="<?php echo $book_image; ?>" width="100" height="150" alt="Book Image">
            <input type="text" name="title" value="<?php echo $title; ?>" required>
            <input type="text" name="author" value="<?php echo $author; ?>" required>
            <input type="text" name="copyright" value="<?php echo $copyright; ?>" required>
            <input type="number" name="qty" value="<?php echo $qty; ?>" required>
            <input type="number" step="0.01" name="price" value="<?php echo $price; ?>" required>
            <input type="file" name="book_image" accept="image/*">
            <button type="submit">Update Book</button>
        </form>
    </div>
</body>

</html>