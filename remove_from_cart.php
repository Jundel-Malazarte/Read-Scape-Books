<?php
session_start();
@include 'db_connect.php';

if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    $user_id = $_SESSION['id'];

    $delete_query = "DELETE FROM cart WHERE user_id = ? AND isbn = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $isbn);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "total_price" => number_format($total_price, 2)]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to remove item."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

exit();
