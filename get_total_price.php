<?php
@include 'db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT SUM(books.price * cart.quantity) AS total 
        FROM cart 
        JOIN books ON cart.isbn = books.isbn 
        WHERE cart.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total = mysqli_fetch_assoc($result)['total'] ?? 0;

echo json_encode(["success" => true, "total_price" => number_format($total, 2, '.', '')]);
mysqli_stmt_close($stmt);
mysqli_close($conn);
