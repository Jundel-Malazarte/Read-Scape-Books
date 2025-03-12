<?php
session_start();
header('Content-Type: application/json');

// Store checkout data in session
$_SESSION['checkout_data'] = [
    'email' => $_POST['email'] ?? '',
    'first_name' => $_POST['first_name'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'mobile' => $_POST['mobile'] ?? '',
    'address' => $_POST['address'] ?? '',
    'city' => $_POST['city'] ?? '',
    'state' => $_POST['state'] ?? '',
    'zipcode' => $_POST['zipcode'] ?? '',
    'payment_method' => $_POST['payment_method'] ?? ''
];

// Store individual session variables
$_SESSION['email'] = $_POST['email'] ?? '';
$_SESSION['fname'] = $_POST['first_name'] ?? '';
$_SESSION['lname'] = $_POST['last_name'] ?? '';
$_SESSION['phone'] = $_POST['mobile'] ?? '';

if (!empty($_SESSION['checkout_data']['mobile']) && !empty($_SESSION['checkout_data']['email'])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Mobile number and email are required'
    ]);
}
