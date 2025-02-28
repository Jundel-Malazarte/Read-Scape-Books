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

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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

// Fetch total books count
$total_books_query = mysqli_query($conn, "SELECT COUNT(*) FROM books");
$total_books = mysqli_fetch_row($total_books_query)[0];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Books</title>
    <link rel="icon" href="./images/Readscape.png">
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
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar input {
            width: 60%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            padding: 10px 15px;
            border: none;
            background-color: blue;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .search-bar button:hover {
            background-color: darkblue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #87CEEB;
            color: white;
            font-weight: bold;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
            /* Light grey for striped rows */
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

        .add-book-btn,
        .update-btn,
        .delete-btn {
            display: inline-block;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .add-book-btn {
            background-color: green;
            box-shadow: 0 4px 8px rgba(0, 128, 0, 0.3);
        }

        .add-book-btn:hover {
            background-color: darkgreen;
        }

        .update-btn {
            background-color: blue;
            box-shadow: 0 4px 8px rgba(0, 0, 255, 0.3);
        }

        .update-btn:hover {
            background-color: darkblue;
        }

        .delete-btn {
            background-color: red;
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.3);
        }

        .delete-btn:hover {
            background-color: darkred;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        <div class="search-container">
            <a href="add_book.php" class="add-book-btn">Add New Book</a>
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search by Title or Author" value="<?php echo htmlspecialchars($search); ?>">

                    <a href="total_books.php" class="add-book-btn">Reset</a>
                </form>
            </div>
        </div>


        <table>
            <thead>
                <tr>
                    <th>ISBN</th>
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
                <?php while ($row = mysqli_fetch_assoc($books_query)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                        <td><img src="../images/<?php echo $row['book_image'] ?: 'default_book.png'; ?>" class="book-img"></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo $row['copyright']; ?></td>
                        <td><?php echo $row['qty']; ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>₱<?php echo number_format($row['total'], 2); ?></td>
                        <td>
                            <a href="../admin/edit_book.php?isbn=<?php echo $row['isbn']; ?>" class="update-btn">Update</a>
                            <a href="../admin/delete_book.php?isbn=<?php echo $row['isbn']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>

<?php mysqli_close($conn); ?>