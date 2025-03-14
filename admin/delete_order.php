<?php
@include '../db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete order items first (due to foreign key constraint)
        $delete_items = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $delete_items->bind_param("i", $order_id);
        $delete_items->execute();

        // Delete the order
        $delete_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $delete_order->bind_param("i", $order_id);
        $delete_order->execute();

        // Commit transaction
        $conn->commit();

        header("Location: view_orders.php?success=order_deleted");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header("Location: order_details.php?id=" . $order_id . "&error=delete_failed");
        exit();
    }
} else {
    header("Location: view_orders.php");
    exit();
}
