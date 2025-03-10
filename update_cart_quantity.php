<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = $_POST['isbn'];
$new_quantity = intval($_POST['new_quantity']);

// Check stock
$sql = "SELECT qty FROM books WHERE isbn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if ($new_quantity > $book['qty']) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock available. Max: ' . $book['qty'],
        'available_stock' => $book['qty']
    ]);
    exit();
}

// Update quantity
$sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND isbn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $new_quantity, $user_id, $isbn);

$response = ['success' => false];
if ($stmt->execute()) {
    $sql = "SELECT SUM(books.price * cart.quantity) AS subtotal FROM cart JOIN books ON cart.isbn = books.isbn WHERE cart.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subtotal = $result->fetch_assoc()['subtotal'] ?? 0;
    $shipping = 100; // Default shipping
    $total_price = $subtotal + $shipping;

    $response = [
        'success' => true,
        'new_quantity' => $new_quantity,
        'new_subtotal' => number_format($subtotal, 2),
        'new_total_price' => number_format($total_price, 2)
    ];
}

echo json_encode($response);
$stmt->close();
$conn->close();
