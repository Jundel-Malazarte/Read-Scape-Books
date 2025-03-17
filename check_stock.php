<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id']) || !isset($_POST['isbn'])) {
    echo json_encode(['stock' => 0]);
    exit();
}

$isbn = $_POST['isbn'];

// Check stock availability
$sql = "SELECT qty FROM books WHERE isbn = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $isbn);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['stock' => $row['qty']]);
} else {
    echo json_encode(['stock' => 0]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!-- Add these styles to your existing <style> section -->
<style>
    .btn-cart:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.65;
    }

    .btn-cart:disabled:hover {
        transform: none;
        opacity: 0.65;
    }

    .alert {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        max-width: 400px;
    }

    .alert-danger {
        background-color: #fff3f3;
        border-left: 4px solid #dc3545;
        color: #dc3545;
    }

    .alert-success {
        background-color: #f0fff4;
        border-left: 4px solid #28a745;
        color: #28a745;
    }

    .btn-close {
        opacity: 0.75;
        transition: opacity 0.3s ease;
    }

    .btn-close:hover {
        opacity: 1;
    }
</style>

<!-- Update the button in the book card section -->
<button class="btn-cart"
    onclick="addToCart('<?php echo htmlspecialchars($book['isbn']); ?>')"
    <?php echo ($book['qty'] <= 0) ? 'disabled' : ''; ?>>
    <i class="fas fa-shopping-cart"></i>
    <?php echo ($book['qty'] <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
</button>