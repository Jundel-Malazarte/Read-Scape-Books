<?php
@include 'db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = isset($_POST['isbn']) ? intval($_POST['isbn']) : 0;

if ($isbn === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid book ISBN."]);
    exit();
}

// Check if book is already in the cart
$check_query = "SELECT quantity FROM cart WHERE user_id = ? AND isbn = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $isbn);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_item = mysqli_fetch_assoc($result);

if ($cart_item) {
    // If book exists, update quantity
    $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND isbn = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $isbn);
    $message = "Book quantity updated in cart!";
} else {
    // If not, insert new row
    $insert_query = "INSERT INTO cart (user_id, isbn, quantity) VALUES (?, ?, 1)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $isbn);
    $message = "Book added to cart!";
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => $message]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update cart."]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
