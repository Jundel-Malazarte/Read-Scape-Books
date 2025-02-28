<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
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
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="total_books.php">Back to Books</a>
        </div>
    </div>

    <div class="container">
        <h1>Add New Book</h1>
        <form action="add_book.php" method="post" enctype="multipart/form-data">
            <input type="text" name="isbn" placeholder="ISBN" required>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="copyright" placeholder="Copyright Year" required>
            <input type="number" name="qty" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="file" name="book_image" accept="image/*">
            <button type="submit">Add Book</button>
        </form>
    </div>
</body>

</html>