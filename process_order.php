<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit();
}

$user_id = $_SESSION['id'];

// Update session values if new ones are provided
$_SESSION['fname'] = $_POST['first_name'] ?? $_SESSION['fname'];
$_SESSION['lname'] = $_POST['last_name'] ?? $_SESSION['lname'];
$_SESSION['email'] = $_POST['email'] ?? $_SESSION['email'];
$_SESSION['mobile'] = $_POST['mobile'] ?? $_SESSION['mobile'];

// Ensure all required fields are provided
$required_fields = ['email', 'first_name', 'last_name', 'mobile', 'address', 'city', 'state', 'zipcode', 'payment_method'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
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

// Sanitize user input
$shipping_info = [
    'email' => htmlspecialchars($_POST['email']),
    'first_name' => htmlspecialchars($_POST['first_name']),
    'last_name' => htmlspecialchars($_POST['last_name']),
    'mobile' => htmlspecialchars($_POST['mobile']),
    'address' => htmlspecialchars($_POST['address']),
    'city' => htmlspecialchars($_POST['city']),
    'state' => htmlspecialchars($_POST['state']),
    'zipcode' => htmlspecialchars($_POST['zipcode']),
    'payment_method' => htmlspecialchars($_POST['payment_method'])
];

$full_address = "{$shipping_info['address']}, {$shipping_info['city']}, {$shipping_info['state']}, {$shipping_info['zipcode']}";

// Fetch cart items (or single item)
$single_item = false;
$cart_items = [];
$shipping = 100.00; // Fixed shipping cost

if (isset($_GET['isbn']) && !empty($_GET['isbn'])) {
    $single_item = true;
    $isbn = $_GET['isbn'];

    // Fetch single book details
    $stmt = $conn->prepare("SELECT isbn AS book_id, title, price, qty AS stock FROM books WHERE isbn = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        $cart_items[] = [
            'book_id' => $book['book_id'],
            'title' => $book['title'],
            'price' => $book['price'],
            'quantity' => 1,
            'stock' => $book['stock']
        ];
    } else {
    ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Checkout Error</title>
            <script>
                window.onload = function() {
                    alert("Error: Book not found.");
                    window.location.href = "dashboard.php";
                };
            </script>
        </head>

        <body></body>

        </html>
    <?php
        exit();
    }
} else {
    // Fetch cart items for checkout
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
}

// Check if cart is empty
if (empty($cart_items)) {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout Error</title>
        <script>
            window.onload = function() {
                alert("No items in the cart.");
                window.location.href = "cart.php";
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
    ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Checkout Error</title>
            <script>
                window.onload = function() {
                    alert("Error: Not enough stock for '<?php echo htmlspecialchars($item['title']); ?>'. Available stock: <?php echo $item['stock']; ?>");
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

// Calculate total price
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total = $subtotal + $shipping;

// Start transaction
$conn->begin_transaction();
try {
    // Insert order record
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $total, $full_address, $shipping_info['payment_method']);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items and update stock
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    $update_stock_stmt = $conn->prepare("UPDATE books SET qty = qty - ? WHERE isbn = ?");

    foreach ($cart_items as $item) {
        $stmt->bind_param("iisd", $order_id, $item['book_id'], $item['quantity'], $item['price']);
        $stmt->execute();

        // Reduce stock
        $update_stock_stmt->bind_param("is", $item['quantity'], $item['book_id']);
        $update_stock_stmt->execute();
    }

    // Clear cart (only if checking out multiple items)
    if (!$single_item) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Redirect to order success page
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
                alert("Error processing order: <?php echo htmlspecialchars($e->getMessage()); ?>");
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