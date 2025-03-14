<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$order_id = $_GET['order_id'] ?? 0;

// Update the order query to include all shipping information
$stmt = $conn->prepare(
    "SELECT o.*, oi.book_id, oi.quantity, oi.price, b.title 
     FROM orders o 
     LEFT JOIN order_items oi ON o.id = oi.order_id 
     LEFT JOIN books b ON oi.book_id = b.isbn 
     WHERE o.id = ? AND o.user_id = ?"
);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$order_details = [];
$order = null;
while ($row = $result->fetch_assoc()) {
    if (!$order) {
        $order = [
            'total' => $row['total'],
            'shipping_address' => $row['shipping_address'],
            'payment_method' => $row['payment_method'],
            'email' => $row['email'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'mobile' => $row['mobile']
        ];
    }
    if ($row['book_id']) {
        $order_details[] = [
            'book_id' => $row['book_id'],
            'title' => $row['title'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'total' => $row['price'] * $row['quantity']
        ];
    }
}

if (!$order) {
    echo "<script>alert('Order not found.'); window.location.href='dashboard.php';</script>";
    exit();
}

// Parse shipping address (assuming format: address, city, state, zipcode)
$address_parts = explode(", ", $order['shipping_address']);
$address = $address_parts[0] ?? '';
$city = $address_parts[1] ?? '';
$state = $address_parts[2] ?? '';
$zipcode = $address_parts[3] ?? '';

$shipping = 100.00; // Consistent with checkout.php and process_order.php
$subtotal = $order['total'] - $shipping;

// Close database connections
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Courier', monospace;
            font-size: 14px;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .receipt {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .checkmark-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .checkmark-circle {
            width: 60px;
            height: 60px;
            position: relative;
            display: inline-block;
            border-radius: 50%;
            border: 3px solid #28a745;
            background: #fff;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .checkmark {
            width: 30px;
            height: 15px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            border-bottom: 4px solid #28a745;
            border-left: 4px solid #28a745;
            opacity: 0;
            animation: drawCheck 0.5s ease forwards 0.5s;
        }

        @keyframes drawCheck {
            0% {
                width: 0;
                height: 0;
                opacity: 0;
            }

            50% {
                width: 30px;
                height: 0;
                opacity: 1;
            }

            100% {
                width: 30px;
                height: 15px;
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        .section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .footer a {
            color: #000;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #f1f1f1;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .footer a:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Order #<?php echo htmlspecialchars($order_id); ?></p>
        </div>

        <div class="checkmark-container">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
        </div>

        <div class="section">
            <h2>Shipping Information</h2>
            <?php
            $full_name = $order['first_name'] . ' ' . $order['last_name'];
            $email = $order['email'];
            $mobile = $order['mobile'];
            ?>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($mobile); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
            <p><strong>State/Province:</strong> <?php echo htmlspecialchars($state); ?></p>
            <p><strong>Zipcode:</strong> <?php echo htmlspecialchars($zipcode); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        </div>

        <div class="section">
            <h2>Order Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_details as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₱<?php echo number_format($item['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3">Subtotal</td>
                        <td>₱<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Shipping</td>
                        <td>₱<?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td>₱<?php echo number_format($order['total'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your order!</p>
            <p><a href="dashboard.php">Back to Home</a></p>
        </div>
    </div>

    <script>
        // JavaScript to ensure the animation runs on page load
        window.onload = function() {
            const checkmark = document.querySelector('.checkmark');
            checkmark.style.animation = 'none'; // Reset animation
            void checkmark.offsetWidth; // Trigger reflow
            checkmark.style.animation = 'drawCheck 0.5s ease forwards 0.5s'; // Restart animation
        };
    </script>
</body>

</html>