<?php
@include 'db_connect.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

// Check if the request is POST and has required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $user_id = $_SESSION['id'];
    $order_id = intval($_POST['order_id']);
    $new_status = 'canceled';

    // Verify that the order belongs to the user and is pending
    $sql = "SELECT user_id, status FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($order && $order['user_id'] == $user_id && strtolower($order['status']) === 'pending') {
        // Update order status
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);

        if (mysqli_stmt_execute($stmt)) {
            // Success - redirect to order.php with the same status filter
            $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
            header("Location: order.php?status=$status_filter&success=order_canceled");
        } else {
            // Error - redirect back to order.php
            header("Location: order.php?status=$status_filter&error=cancel_failed");
        }

        mysqli_stmt_close($stmt);
    } else {
        // Invalid order or not pending
        header("Location: order.php?status=$status_filter&error=invalid_order");
    }

    mysqli_close($conn);
    exit();
} else {
    // Invalid request
    header("Location: order.php");
    exit();
}
