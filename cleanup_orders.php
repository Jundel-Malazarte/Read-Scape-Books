<?php
include 'db_connect.php';

// Replace the existing orders query with this
$sql = "SELECT orders.id AS order_id, orders.order_date, orders.status,
        GROUP_CONCAT(books.title SEPARATOR '|||') AS titles,
        GROUP_CONCAT(books.book_image SEPARATOR '|||') AS book_images,
        GROUP_CONCAT(books.author SEPARATOR '|||') AS authors,
        GROUP_CONCAT(order_items.quantity SEPARATOR '|||') AS quantities,
        GROUP_CONCAT(order_items.price SEPARATOR '|||') AS prices
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN books ON order_items.book_id = books.isbn
        WHERE orders.user_id = ? 
        AND orders.status IS NOT NULL 
        AND (orders.status = ? OR ? = 'all')
        GROUP BY orders.id
        ORDER BY orders.order_date DESC";

// Delete orders with null status
$delete_null_orders = "DELETE FROM orders WHERE status IS NULL";
$conn->query($delete_null_orders);

// Delete orphaned order items
$delete_orphaned_items = "DELETE oi FROM order_items oi 
                         LEFT JOIN orders o ON oi.order_id = o.id 
                         WHERE o.id IS NULL";
$conn->query($delete_orphaned_items);

// Update order insertion queries to include status
$stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");

echo "Cleanup completed";
