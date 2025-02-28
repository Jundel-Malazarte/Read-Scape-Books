<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = $_POST['isbn'];

    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['isbn'] !== $isbn);
    }

    echo json_encode(["status" => "success"]);
}
