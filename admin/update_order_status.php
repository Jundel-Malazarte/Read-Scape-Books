<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Check if the request is POST and has required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status']; // Expecting "completed" or "canceled"

    // Validate status
    if (!in_array($new_status, ['completed', 'canceled'])) {
        header("Location: order_details.php?id=" . $order_id . "&error=invalid_status");
        exit();
    }

    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);

    if (mysqli_stmt_execute($stmt)) {
        // Success - redirect to view_orders.php
        header("Location: view_orders.php?success=status_updated");
    } else {
        // Error - redirect back to order_details.php
        header("Location: order_detail.php?id=" . $order_id . "&error=update_failed");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
} else {
    // Invalid request
    header("Location: view_orders.php");
    exit();
}
