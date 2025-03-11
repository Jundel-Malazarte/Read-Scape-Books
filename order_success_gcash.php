<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['payment_success']) || $_SESSION['payment_success'] !== true) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_SESSION['order_id'] ?? '';
$reference_id = $_SESSION['reference_id'] ?? '';

// Get order details
$stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email, u.phone 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - ReadScape</title>
    <link rel="stylesheet" href="./assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h2 class="mt-3">Order Successful!</h2>
                        <p class="text-muted">Thank you for your purchase</p>

                        <div class="mt-4 text-start">
                            <h5>Order Details:</h5>
                            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                            <p><strong>Reference ID:</strong> <?php echo $reference_id; ?></p>
                            <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['order_date'])); ?></p>
                            <p><strong>Payment Method:</strong> GCash</p>
                            <p><strong>Total Amount:</strong> PHP <?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>

                        <div class="mt-4">
                            <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Clear payment session variables
    unset($_SESSION['payment_success']);
    unset($_SESSION['order_id']);
    unset($_SESSION['reference_id']);
    unset($_SESSION['total_with_shipping']);
    ?>
</body>

</html>