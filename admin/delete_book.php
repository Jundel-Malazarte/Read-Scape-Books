<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Check if ISBN is provided
if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];

    // Fetch book image filename to delete the file
    $sql = "SELECT book_image FROM books WHERE isbn = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $book_image = $row['book_image'];

        // Delete book from database
        $delete_sql = "DELETE FROM books WHERE isbn = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "s", $isbn);

        if (mysqli_stmt_execute($delete_stmt)) {
            // Delete the book image if it's not the default image
            if (!empty($book_image) && file_exists("../images/" . $book_image)) {
                unlink("../images/" . $book_image);
            }

            $_SESSION['message'] = "Book deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting book.";
        }

        mysqli_stmt_close($delete_stmt);
    } else {
        $_SESSION['message'] = "Book not found.";
    }

    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = "Invalid request.";
}

mysqli_close($conn);

// Redirect back to total_books.php
header("Location: total_books.php");
exit();
