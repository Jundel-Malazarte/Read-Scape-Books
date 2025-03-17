<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id']) || !isset($_POST['isbn'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['id'];
$isbn = $_POST['isbn'];

// Prepare and execute delete query
$sql = "DELETE FROM cart WHERE user_id = ? AND isbn = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $user_id, $isbn);

$response = [];
if (mysqli_stmt_execute($stmt)) {
    $response['success'] = true;
    $response['message'] = 'Item removed successfully';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to remove item';
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>

<script>
    // Replace the existing removeItem function in your script section
    function removeItem(isbn) {
        if (confirm('Are you sure you want to remove this item?')) {
            const formData = new FormData();
            formData.append('isbn', isbn);

            fetch('remove_cart_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the item row from the table
                        const row = document.querySelector(`tr[data-isbn="${isbn}"]`);
                        if (row) {
                            row.remove();
                        }

                        // Update cart count and totals
                        updateCartCounter();
                        updateTotals();

                        // Reload if no items left
                        const remainingItems = document.querySelectorAll('.cart-table tbody tr').length;
                        if (remainingItems === 0) {
                            window.location.reload();
                        }
                    } else {
                        alert('Failed to remove item: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
        }
    }

    // Add these helper functions
    function updateCartCounter() {
        const cartCounter = document.getElementById('cart-counter');
        const currentCount = parseInt(cartCounter.textContent);
        cartCounter.textContent = currentCount - 1;
    }

    function updateTotals() {
        let subtotal = 0;
        const shipping = 100; // Default shipping cost

        // Calculate new subtotal
        document.querySelectorAll('.cart-table tbody tr').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').textContent.replace('₱', '').replace(',', ''));
            const quantity = parseInt(row.querySelector('.item-quantity').textContent);
            subtotal += price * quantity;
        });

        // Update summary section
        const total = subtotal + shipping;
        document.querySelector('.total-price p:first-child').textContent = `Items: ${document.queryAll('.cart-table tbody tr').length}`;
        document.querySelector('.total-price p:last-child strong').textContent = `₱${total.toFixed(2)}`;
    }
</script>

<tr data-isbn="<?php echo htmlspecialchars($item['isbn']); ?>">
    <!-- ... rest of the row content ... -->
</tr>