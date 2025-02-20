<?php
@include '../db_connect.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['id'];

$sql = "SELECT fname, lname, profile_image FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $fname = htmlspecialchars($row['fname']);
    $lname = htmlspecialchars($row['lname']);
    $profile_image = $row['profile_image'];

    // Ensure correct path to the image
    $default_image = '../uploads/default.jpg';
    if (empty($profile_image) || !file_exists("../uploads/" . $profile_image)) {
        $profile_image = $default_image;
    } else {
        $profile_image = '../uploads/' . $profile_image;
    }
} else {
    $fname = "Admin";
    $lname = "User";
    $profile_image = '../uploads/default.jpg';
}

mysqli_stmt_close($stmt);

// Fetch total books count
$total_books_query = mysqli_query($conn, "SELECT COUNT(*) FROM books");
$total_books = mysqli_fetch_row($total_books_query)[0];

// Fetch all books' details
$books_query = mysqli_query($conn, "SELECT isbn, title, book_image, author, copyright, qty, price, total FROM books");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }

        .navbar a:hover {
            background-color: #555;
            border-radius: 5px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #87CEEB;
            /* Baby blue */
            color: white;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .book-img {
            width: 50px;
            height: 75px;
            object-fit: cover;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .add-book-btn {
            display: inline-block;
            /* Ensures padding and margin work correctly */
            background-color: green;
            /* Green background */
            color: white;
            /* White text */
            padding: 10px 20px;
            /* Padding for the button */
            border-radius: 5px;
            /* Rounded corners */
            text-decoration: none;
            /* Remove underline from link */
            box-shadow: 0 4px 8px rgba(0, 128, 0, 0.3);
            /* Box shadow effect */
            transition: background-color 0.3s ease;
            /* Smooth transition for hover effect */
            margin-top: 10px;
            /* Space between h1 and button */
        }

        .add-book-btn:hover {
            background-color: darkgreen;
            /* Darker green on hover */
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="../admin/admin_dashboard.php">Home</a>
        </div>
        <div class="profile-info">
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
            <a href="profile.php"><?php echo $fname . " " . $lname; ?></a>
            <a href="../admin/logout.php">Log Out</a>
        </div>
    </div>


    <div class="container">
        <h1>Total Books: <?php echo $total_books; ?></h1>
        <a href="add_book.php" class="add-book-btn">Add New Book</a>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Copyright</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($books_query)) {
                    $book_image = !empty($row['book_image']) ? "../images/" . $row['book_image'] : "../images/default_book.png";

                    echo "<tr>
            <td><img src='$book_image' class='book-img' alt='Book Image'></td>
            <td>{$row['title']}</td>
            <td>{$row['author']}</td>
            <td>{$row['copyright']}</td>
            <td>{$row['qty']}</td>
            <td>₱" . number_format($row['price'], 2) . "</td>
            <td>₱" . number_format($row['total'], 2) . "</td>
            <td><a href='../admin/delete_book.php?isbn={$row['isbn']}' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this book?');\">Delete</a></td>
          </tr>";
                }
                ?>
            </tbody>

        </table>
    </div>

</body>

</html>

<?php
mysqli_close($conn);
?>