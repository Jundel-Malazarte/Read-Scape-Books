<?php
@include '../db_connect.php';

if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];

    // Fetch book image before deleting
    $image_query = "SELECT book_image FROM books WHERE isbn = ?";
    $stmt = mysqli_prepare($conn, $image_query);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $book_image = $row['book_image'];

        // Delete book from database
        $delete_query = "DELETE FROM books WHERE isbn = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "s", $isbn);

        if (mysqli_stmt_execute($stmt)) {
            // Remove book image file if it's not the default
            if (!empty($book_image) && file_exists("../images/" . $book_image) && $book_image !== 'default_book.png') {
                unlink("../images/" . $book_image);
            }
            echo "<script>alert('Book deleted successfully'); window.location.href='total_books.php';</script>";
        } else {
            echo "<script>alert('Error deleting book'); window.location.href='total_books.php';</script>";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
