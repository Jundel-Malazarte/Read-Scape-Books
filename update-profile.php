<?php
session_start(); // Start session
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id']; // Get logged-in user ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);

    $update_image = ""; // Variable to store image query part

    // Check if an image file is uploaded
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/"; // Ensure this directory exists with correct permissions
        $file_name = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $file_name; // Generate unique file name
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $update_image = ", profile_image=?";
            } else {
                echo "<script>alert('Error uploading image.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, GIF allowed.');</script>";
            exit();
        }
    }

    // Prepare SQL query dynamically
    if ($update_image) {
        $sql = "UPDATE users SET fname=?, lname=? $update_image WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $fname, $lname, $target_file, $user_id);
    } else {
        $sql = "UPDATE users SET fname=?, lname=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $fname, $lname, $user_id);
    }

    // Execute query
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
