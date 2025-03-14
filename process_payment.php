<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

try {
    if (!isset($_FILES['receipt']) || !isset($_POST['order_id'])) {
        throw new Exception('Missing required fields');
    }

    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['id'];

    // Verify order belongs to user
    $stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid order');
    }

    // Upload receipt
    $receipt = $_FILES['receipt'];
    $filename = 'receipt_' . $order_id . '_' . time() . '.' . pathinfo($receipt['name'], PATHINFO_EXTENSION);
    $upload_path = 'uploads/receipts/' . $filename;

    if (!move_uploaded_file($receipt['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload receipt');
    }

    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed', payment_receipt = ? WHERE id = ?");
    $stmt->bind_param("si", $filename, $order_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update order status');
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
