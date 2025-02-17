<?php
@include 'db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$profile_image = $_FILES['profile_image']['name'];

if ($profile_image) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_image);
    move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file);
} else {
    // If no new image is uploaded, keep the old image
    $sql = "SELECT profile_image FROM `users` WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $target_file = $row['profile_image'];
    }
    mysqli_stmt_close($stmt);
}

$sql = "UPDATE `users` SET fname = ?, lname = ?, profile_image = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssi", $fname, $lname, $target_file, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
} else {
    echo "<script>alert('Error updating profile: " . mysqli_error($conn) . "'); window.location.href='edit-profile.php';</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>