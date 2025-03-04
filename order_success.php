<?php
session_start();
@include 'db_connect.php';

// Check if the user is logged in and `order_id` is set
if (!isset($_SESSION['id']) || !isset($_GET['order_id'])) {
    header("Location: dashboard.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['id'];
$fixed_shipping_fee = 50.00; // Set shipping fee

// Fetch order details
$stmt = $conn->prepare(
    "SELECT shipping_address, payment_method, order_date FROM orders WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found."); // Stop execution if order is not found
}

// Fetch user details
$stmt = $conn->prepare("SELECT fname, lname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found."); // Stop execution if user is not found
}

// Fetch order items
$stmt = $conn->prepare(
    "SELECT b.title, oi.quantity, oi.price FROM order_items oi 
    JOIN books b ON oi.book_id = b.isbn WHERE oi.order_id = ?"
);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate subtotal
$subtotal = 0;
if ($order_items) {
    foreach ($order_items as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
}

// Calculate total
$final_total = $subtotal + $fixed_shipping_fee;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Success</title>
    <style>
        body {
            font-family: Courier, monospace;
            text-align: center;
        }

        .receipt {
            width: 280px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            text-align: left;
        }

        .store-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .info,
        .total {
            font-size: 14px;
            margin-top: 5px;
        }

        table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .buttons button {
            padding: 8px 15px;
            font-size: 14px;
            margin: 5px;
            cursor: pointer;
        }

        @media print {
            .buttons {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="store-title">BOOK STORE</div>
        <div class="info">
            Order ID: <?= htmlspecialchars($order_id) ?><br>
            Date: <?= htmlspecialchars($order['order_date'] ?? 'N/A') ?><br>
            Customer: <?= htmlspecialchars(($user['fname'] ?? '') . ' ' . ($user['lname'] ?? '')) ?><br>
            Address: <?= htmlspecialchars($order['shipping_address'] ?? 'N/A') ?><br>
            Payment: <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?>
        </div>
        <table>
            <tbody>
                <?php if ($order_items): ?>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td class="right">x<?= $item['quantity'] ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="right">₱<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="total">
            Subtotal: <span class="right">₱<?= number_format($subtotal, 2) ?></span><br>
            Shipping: <span class="right">₱<?= number_format($fixed_shipping_fee, 2) ?></span><br>
            Total: <span class="right">₱<?= number_format($final_total, 2) ?></span>
        </div>
    </div>

    <div class="buttons">
        <button onclick="window.print()">Print Receipt</button>
        <a href="dashboard.php"><button>Go Back to Dashboard</button></a>
    </div>
</body>

</html>