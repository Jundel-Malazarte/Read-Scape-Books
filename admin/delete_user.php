<?php
session_start();
@include '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get user ID from query parameter
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    // Delete user from database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User deleted successfully.'); window.location.href='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='manage_user.php';</script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Invalid user ID.'); window.location.href='manage_user.php';</script>";
}

mysqli_close($conn);
?>