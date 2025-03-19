<?php
session_start();
@include '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['id']);
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['mobile']);
    $address = trim($_POST['address']);

    // Validate input
    if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($address)) {
        echo "<script>alert('All fields are required.'); window.location.href='edit_user.php?id=$user_id';</script>";
        exit();
    }

    // Update user details
    $sql = "UPDATE users SET fname = ?, lname = ?, email = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $fname, $lname, $email, $phone, $address, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User updated successfully.'); window.location.href='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error updating user.'); window.location.href='edit_user.php?id=$user_id';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: manage_user.php");
    exit();
}
?>  