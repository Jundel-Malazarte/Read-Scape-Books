<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$shipping_cost = 100.00;
$total_with_shipping = isset($_SESSION['total_with_shipping']) ? $_SESSION['total_with_shipping'] : 0;
$subtotal = $total_with_shipping - $shipping_cost;

// Get GCash number from session (assuming set in login.gcash.php or checkout.php)
$gcash_number = '+639428013424';

// Generate random reference ID (10 digits)
$reference_id = mt_rand(1000000000, 9999999999);
$payment_time = date('Y-m-d H:i:s');

// Process payment and order if confirmed
if (isset($_POST['confirm_payment'])) {
    // Verify GCash balance
    $stmt = $conn->prepare("SELECT balance FROM gcash_users2 WHERE mobile_number = ?");
    $stmt->bind_param("s", $gcash_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $gcash_user = $result->fetch_assoc();

    if (!$gcash_user || $gcash_user['balance'] < $total_with_shipping) {
        header("Location: payments.gcash.php?error=insufficient_balance");
        exit();
    }

    // Deduct from GCash balance
    $new_balance = $gcash_user['balance'] - $total_with_shipping;
    $stmt = $conn->prepare("UPDATE gcash_users2 SET balance = ? WHERE mobile_number = ?");
    $stmt->bind_param("ds", $new_balance, $gcash_number);
    $stmt->execute();

    // Start transaction for order processing
    $conn->begin_transaction();
    try {
        // Insert into orders table (adjusted to match your schema)
        $shipping_address = isset($_SESSION['address']) ?
            "{$_SESSION['address']}, {$_SESSION['city']}, {$_SESSION['state']}, {$_SESSION['zipcode']}" : '';
        $stmt = $conn->prepare(
            "INSERT INTO orders (user_id, total, shipping_address, payment_method, status, order_date, 
            email, first_name, last_name, mobile, address, city, state, zipcode) 
            VALUES (?, ?, ?, 'gcash', 'completed', ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "idsssssssssss",
            $user_id,
            $total_with_shipping,
            $shipping_address,
            $payment_time,
            $_SESSION['email'],
            $_SESSION['fname'],
            $_SESSION['lname'],
            $_SESSION['phone'],
            $_SESSION['address'],
            $_SESSION['city'],
            $_SESSION['state'],
            $_SESSION['zipcode']
        );
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Move items from cart to order_items
        $cart_stmt = $conn->prepare(
            "SELECT c.isbn, c.quantity, b.price 
             FROM cart c 
             JOIN books b ON c.isbn = b.isbn 
             WHERE c.user_id = ?"
        );
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        $insert_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $update_stock = $conn->prepare("UPDATE books SET qty = qty - ? WHERE isbn = ?");

        while ($item = $cart_result->fetch_assoc()) {
            $insert_item->bind_param("iiid", $order_id, $item['isbn'], $item['quantity'], $item['price']);
            $insert_item->execute();

            // Update stock
            $update_stock->bind_param("ii", $item['quantity'], $item['isbn']);
            $update_stock->execute();
        }

        // Clear the cart
        $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart->bind_param("i", $user_id);
        $clear_cart->execute();

        $conn->commit();
        $_SESSION['payment_success'] = true;
        header("Location: order.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Format amounts for display
$subtotal_formatted = number_format($subtotal, 2);
$shipping_formatted = number_format($shipping_cost, 2);
$total_formatted = number_format($total_with_shipping, 2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>GCash Payment Confirmation</title>
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/gcash.png" type="image/x-icon" style="border-radius: 50%;" />
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #007cff;
        }

        .container {
            display: grid;
        }

        .w-screen {
            width: 40%;
            justify-self: center;
            align-self: center;
            margin: auto;
        }

        .flex-col-w-full {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 500px;
            min-height: 400px;
        }

        .container-box {
            width: 100%;
            background-color: #fff;
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .pay-button {
            background-color: #007cff;
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            width: 50%;
            margin-top: 20px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .pay-button {
            background-color: #007cff;
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            width: 50%;
            margin: 20px auto;
            display: block;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>

<body class="overflow-hidden scroll-smooth">
    <div class="container" style="background-color: #007cff; width:100%; height:380px;">
        <div class="w-screen h-screen px-5">
            <div class="flex-col-w-full">
                <img src="./images/gcash.png" alt="gcash" class="h-60 object-contain" />
                <div class="container-box">
                    <div class="flex flex-col gap-4">
                        <div class="text-center">
                            <i class="fas fa-check-circle" style="color: blue; font-size: 3rem; margin-bottom: 10px;"></i>
                        </div>
                        <span class="text-black font-bold text-xl text-center" style="color: #283593;">ReadScape Payment</span>
                        <span class="text-black font-xl text-xl text-center"
                            style="background-color: #e0f2f1; 
                                     color: #283593; 
                                     font-weight: bold; 
                                     border-radius: 10px; 
                                     padding: 5px 10px; 
                                     display: inline-block; 
                                     line-height: 1.5;
                                     margin: 5px 0;">
                            <?php echo htmlspecialchars($gcash_number); ?>
                        </span>
                        <span class="text-black font-2xl text-center">Confirm Payment via GCash</span>

                        <!-- Amount Details -->
                        <div class="flex justify-between items-center py-2">
                            <span class="font-bold" style="color: #283593;">Subtotal</span>
                            <span class="font-bold text-black" style="color: #283593;">PHP <?php echo $subtotal_formatted; ?></span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-bold" style="color: #283593;">Shipping Fee</span>
                            <span class="font-bold text-black" style="color: #283593;">PHP <?php echo $shipping_formatted; ?></span>
                        </div>
                        <hr style="margin: 10px 0px;">
                        <div class="flex justify-between items-center py-2">
                            <span class="font-bold" style="color: #283593;">Total Amount</span>
                            <span class="font-bold text-black" style="color: #283593;">PHP <?php echo $total_formatted; ?></span>
                        </div>

                        <!-- Reference and Time -->
                        <div style="display: flex; justify-content: space-between; width: 100%;">
                            <span style="color: #283593; padding: 10px; text-align: left; width: 50%;">Reference ID: <?php echo $reference_id; ?></span>
                            <span style="color: #283593; padding: 10px; text-align: left; width: 50%;"><?php echo date('M d, Y h:ia', strtotime($payment_time)); ?></span>
                        </div>

                        <!-- Confirm Payment Button -->
                        <div class="button-container" style="display: flex; justify-content: center; gap: 20px; width: 100%;">
                            <form method="POST" action="" style="display: flex; justify-content: center;">
                                <!-- <button type="submit" name="confirm_payment" class="pay-button">
                                    Confirm Payment
                                </button> -->
                            </form>

                            <button onclick="downloadReceipt()" class="pay-button" style="background-color: white; color: #007cff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="fixed bottom-0 w-full left-0 px-6 py-2">
                            <div class="flex justify-between items-center">
                                <a class="text-black text-xs" href="">Help Center</a>
                                <span class="text-gcash-secondary-blue text-xs">v5.56.0:595</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
<script>
    function downloadReceipt() {
        const receiptBox = document.querySelector('.container-box');

        html2canvas(receiptBox, {
            backgroundColor: '#ffffff',
            scale: 2, // For better quality
            logging: false,
        }).then(canvas => {
            // Convert the canvas to a data URL
            const imageData = canvas.toDataURL('image/png');

            // Create a temporary link to download the image
            const link = document.createElement('a');
            link.href = imageData;
            link.download = 'ReadScape_Receipt_<?php echo $reference_id; ?>.png';

            // Trigger the download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
</script>

</html>