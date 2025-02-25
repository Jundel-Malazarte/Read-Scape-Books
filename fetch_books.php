<!-- <?php
        @include 'db_connect.php';

        // $sql = "SELECT * FROM books ORDER BY isbn DESC";
        // $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $book_image = htmlspecialchars($row['book_image']);
            $title = htmlspecialchars($row['title']);
            $author = htmlspecialchars($row['author']);
            $price = number_format($row['price'], 2);
            $quantity = $row['qty'];

            // Ensure correct image path
            $image_path = "images/" . $book_image;


            // Check if image file exists; otherwise, use a placeholder
            if (!file_exists(filename: $image_path) || empty($book_image)) {
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
?> -->

<?php
@include 'db_connect.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$query = "SELECT isbn, title, book_image, author, copyright, qty, price, total FROM books";
if (!empty($search)) {
    $query .= " WHERE title LIKE ? OR author LIKE ?";
}

$stmt = mysqli_prepare($conn, $query);

if (!empty($search)) {
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
}

mysqli_stmt_execute($stmt);
$books_query = mysqli_stmt_get_result($stmt);

$output = "";

if (mysqli_num_rows($books_query) > 0) {
    while ($book = mysqli_fetch_assoc($books_query)) {
        $output .= '
            <div class="book-card">
                <img src="images/' . htmlspecialchars($book['book_image']) . '" alt="Book Image" onerror="this.onerror=null; this.src=\'uploads/default.jpg\';">
                <h3>' . htmlspecialchars($book['title']) . '</h3>
                <p><strong>ISBN:</strong> ' . htmlspecialchars($book['isbn']) . '</p>
                <p><strong>Author:</strong> ' . htmlspecialchars($book['author']) . '</p>
                <p><strong>Copyright:</strong> ' . htmlspecialchars($book['copyright']) . '</p>
                <p><strong>Stocks:</strong> ' . htmlspecialchars($book['qty']) . '</p>
                <p><strong>Price:</strong> ₱' . htmlspecialchars($book['price']) . '</p>
                <p><strong>Total:</strong> ₱' . htmlspecialchars($book['total']) . '</p>
                <button class="add-btn" onclick="addToCart(\'' . htmlspecialchars($book['isbn']) . '\')">Add</button>
            </div>';
    }
} else {
    $output = "<p>No books found</p>";
}

echo $output;
mysqli_close($conn);
?>