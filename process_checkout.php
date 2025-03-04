<?php
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

// Ensure all form fields are provided
$required_fields = ['email', 'first_name', 'last_name', 'mobile', 'address', 'city', 'state', 'zipcode', 'payment_method'];
$missing_fields = [];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    // Output HTML with JavaScript to show error and redirect
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout Error</title>
        <script>
            window.onload = function() {
                alert("Error: All fields are required. Missing: <?php echo implode(', ', $missing_fields); ?>");
                window.location.href = "checkout.php";
            };
        </script>
    </head>

    <body></body>

    </html>
<?php
    exit();
}

// Store shipping information
$shipping_info = [
    'email' => $_POST['email'],
    'first_name' => $_POST['first_name'],
    'last_name' => $_POST['last_name'],
    'mobile' => $_POST['mobile'],
    'address' => $_POST['address'],
    'city' => $_POST['city'],
    'state' => $_POST['state'],
    'zipcode' => $_POST['zipcode'],
    'payment_method' => $_POST['payment_method']
];

$full_address = "{$shipping_info['address']}, {$shipping_info['city']}, {$shipping_info['state']}, {$shipping_info['zipcode']}";

// Fetch cart items with stock check
$stmt = $conn->prepare(
    "SELECT c.isbn AS book_id, b.title, b.price, c.quantity, b.qty AS stock 
     FROM cart c 
     JOIN books b ON c.isbn = b.isbn 
     WHERE c.user_id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Check for empty cart
if (empty($cart_items)) {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout Error</title>
        <style>
            .empty-cart-message {
                font-family: Arial, sans-serif;
                font-size: 16px;
                color: #666;
                text-align: center;
                margin-top: 50px;
            }
        </style>
        <script>
            window.onload = function() {
                const container = document.createElement('div');
                container.className = 'empty-cart-message';
                container.textContent = 'No items in the cart.';
                document.body.appendChild(container);
                setTimeout(function() {
                    window.location.href = "cart.php";
                }, 2000); // Redirect after 2 seconds
            };
        </script>
    </head>

    <body></body>

    </html>
    <?php
    exit();
}

// Validate stock levels
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock']) {
        $error_message = "Error: Not enough stock for '" . htmlspecialchars($item['title']) . "'. Available stock: " . $item['stock'];
    ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Checkout Error</title>
            <script>
                window.onload = function() {
                    alert("<?php echo $error_message; ?>");
                    window.location.href = "cart.php";
                };
            </script>
        </head>

        <body></body>

        </html>
    <?php
        exit();
    }
}

// Calculate order total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 50.00;
$total = $subtotal + $shipping;

// Start transaction
$conn->begin_transaction();
try {
    // Insert order record
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total, shipping_address, payment_method) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("idss", $user_id, $total, $full_address, $shipping_info['payment_method']);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items & update stock
    $stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)"
    );
    $update_stock_stmt = $conn->prepare(
        "UPDATE books SET qty = qty - ? WHERE isbn = ?"
    );

    foreach ($cart_items as $item) {
        $stmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
        $stmt->execute();

        // Reduce stock
        $update_stock_stmt->bind_param("ii", $item['quantity'], $item['book_id']);
        $update_stock_stmt->execute();
    }

    // Clear cart
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    // Commit transaction
    $conn->commit();

    // Redirect to order confirmation page
    header("Location: order_success.php?order_id=" . $order_id);
    exit();
} catch (Exception $e) {
    $conn->rollback();
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout Error</title>
        <script>
            window.onload = function() {
                alert("Error processing order: <?php echo $e->getMessage(); ?>");
                window.location.href = "cart.php";
            };
        </script>
    </head>

    <body></body>

    </html>
<?php
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .receipt {
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="section">
            <h2>Shipping Information</h2>
            <p>Name: <?= htmlspecialchars($shipping_info['first_name'] . ' ' . $shipping_info['last_name']) ?></p>
            <p>Email: <?= htmlspecialchars($shipping_info['email']) ?></p>
            <p>Phone: <?= htmlspecialchars($shipping_info['mobile']) ?></p>
            <p>Address: <?= htmlspecialchars($full_address) ?></p>
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
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3">Subtotal</td>
                        <td>₱<?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Shipping</td>
                        <td>₱<?= number_format($shipping, 2) ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td>₱<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your order!</p>
        </div>
    </div>
</body>

</html>