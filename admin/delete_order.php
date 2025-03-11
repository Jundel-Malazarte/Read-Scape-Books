<?php
@include '../db_connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Delete order items first to maintain referential integrity
    $sql1 = "DELETE FROM order_items WHERE order_id = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "i", $order_id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Delete the order itself
    $sql2 = "DELETE FROM orders WHERE id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $order_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    mysqli_close($conn);

    header("Location: view_orders.php?success=order_deleted");
    exit();
} else {
    header("Location: view_orders.php?error=invalid_request");
    exit();
}
