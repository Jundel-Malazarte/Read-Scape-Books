<?php
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = $_POST['isbn'];
$new_quantity = intval($_POST['new_quantity']);

if ($new_quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity.']);
    exit();
}

// Update the quantity in the cart
$sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND isbn = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $new_quantity, $user_id, $isbn);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    // Recalculate the subtotal for the entire cart
    $sql = "SELECT SUM(books.price * cart.quantity) AS subtotal 
            FROM cart 
            JOIN books ON cart.isbn = books.isbn 
            WHERE cart.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $subtotal = $row['subtotal'];
    $total = $subtotal + 50; // Fixed shipping cost of ₱50

    // Return the updated values
    echo json_encode([
        'success' => true,
        'new_quantity' => $new_quantity,
        'new_subtotal' => number_format($subtotal, 2, '.', ','),
        'new_total_price' => number_format($total, 2, '.', ',')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
