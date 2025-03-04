<?php
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false]);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = $_GET['isbn'];

$sql = "DELETE FROM cart WHERE user_id = ? AND isbn = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $user_id, $isbn);
$success = mysqli_stmt_execute($stmt);

echo json_encode(["success" => $success]);
mysqli_stmt_close($stmt);
mysqli_close($conn);
