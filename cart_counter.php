<?php
@include 'db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo "0"; // No user logged in, return 0
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$count = mysqli_fetch_row($result)[0];

echo $count; // Output the count as plain text
mysqli_stmt_close($stmt);
mysqli_close($conn);
