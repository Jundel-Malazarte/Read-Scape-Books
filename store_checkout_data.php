<?php
session_start();

// Store checkout form data in session
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

echo 'success';
