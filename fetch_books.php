<?php
@include 'db_connect.php';

$sql = "SELECT * FROM books ORDER BY isbn DESC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $book_image = htmlspecialchars($row['book_image']);
    $title = htmlspecialchars($row['title']);
    $author = htmlspecialchars($row['author']);
    $price = number_format($row['price'], 2);
    $quantity = $row['qty'];

    // Ensure correct image path
    $image_path = "images/" . $book_image;


    // Check if image file exists; otherwise, use a placeholder
    if (!file_exists($image_path) || empty($book_image)) {
        $image_path = "uploads/default.jpg";
    }
?>

    <div class="book-card">
        <img src="<?php echo $image_path; ?>" alt="Book Image">
        <h3><?php echo $title; ?></h3>
        <p><strong>Author:</strong> <?php echo $author; ?></p>
        <p><strong>Price:</strong> ₱ <?php echo $price; ?></p>
        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
        <button class="add-btn">+</button>
    </div>

<?php
}
?>