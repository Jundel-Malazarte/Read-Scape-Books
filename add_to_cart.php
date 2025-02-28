<?php
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $isbn = $_POST['isbn'];

    // Fetch book details from the database
    $sql = "SELECT isbn, title, book_image, author, price FROM books WHERE isbn = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $book = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if ($book) {
        // Initialize cart session if not set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if the book is already in the cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['isbn'] === $isbn) {
                $item['quantity'] += 1; // Increase quantity
                $found = true;
                break;
            }
        }

        if (!$found) {
            $book['quantity'] = 1; // Default quantity
            $_SESSION['cart'][] = $book;
        }

        echo json_encode(["status" => "success", "message" => "Book added to cart"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Book not found"]);
    }
}
