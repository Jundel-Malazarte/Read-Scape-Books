<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];
$order_id = $_GET['order_id'] ?? 0;

// Fetch order details
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

// ... Rest of the order fetching code similar to order_success.php ...

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Now - Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Copy styles from order_success.php and add: */
        .payment-section {
            margin-top: 2rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .upload-section {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .preview-image {
            max-width: 300px;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <!-- Copy receipt section from order_success.php -->

        <div class="payment-section">
            <h2>Payment Upload</h2>
            <div class="upload-section">
                <form id="paymentForm" action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Upload GCash Receipt</label>
                        <input type="file" class="form-control" id="receipt" name="receipt"
                            accept="image/*" required onchange="previewImage(this)">
                    </div>
                    <div id="imagePreview"></div>
                    <button type="submit" class="btn btn-primary">Complete Payment</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('paymentForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('process_payment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment completed successfully!');
                        window.location.href = 'order.php';
                    } else {
                        alert(data.error || 'Error processing payment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing payment');
                });
        };
    </script>
</body>

</html>