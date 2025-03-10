<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = $_GET['isbn'];

$sql = "DELETE FROM cart WHERE user_id = ? AND isbn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $isbn);

$response = ['success' => false];
if ($stmt->execute()) {
    $response = ['success' => true, 'message' => 'Item removed from cart successfully'];
} else {
    $response = ['success' => false, 'message' => 'Failed to remove item from cart'];
}

echo json_encode($response);
$stmt->close();
$conn->close();
