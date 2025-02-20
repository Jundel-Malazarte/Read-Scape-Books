<?php
@include '../db_connect.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Delete user from database
    $delete_query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User deleted successfully'); window.location.href='total_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user'); window.location.href='total_users.php';</script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
