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

// Fetch total users count
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) FROM users");
$total_users = mysqli_fetch_row($total_users_query)[0];

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$items_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, $_GET['page']) : 1;
$start = ($page - 1) * $items_per_page;

// Fetch all users' details (excluding password)
$sql = "SELECT id, fname, lname, email, phone, address, created_at FROM users";
$conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $conditions[] = "CONCAT(fname, ' ', lname) LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$users_query = mysqli_stmt_get_result($stmt);

$users = [];
while ($row = mysqli_fetch_assoc($users_query)) {
    $users[] = $row;
}

// Apply pagination
$total_pages = ceil(count($users) / $items_per_page);
$paginated_users = array_slice($users, $start, $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            text-align: left;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
        }

        .search-bar input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }

        .search-bar button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .search-bar .reset-btn {
            padding: 8px 15px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        .search-bar .reset-btn:hover {
            background-color: #c9302c;
        }

        .user-section {
            width: 100%;
        }

        .user-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .user-section table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            /* Adds vertical spacing between rows */
            margin-top: 10px;
        }

        .user-section th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            color: #333;
            font-weight: bold;
        }

        .user-section td {
            padding: 12px;
            text-align: center;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        .delete-btn {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination button:hover {
            background-color: #e9ecef;
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
        <div class="search-container">
            <h1>Total Users: <?php echo $total_users; ?></h1>
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search by Full Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                    <a href="total_users.php" class="reset-btn">Reset</a>
                </form>
            </div>
        </div>

        <div class="user-section">
            <?php if (empty($paginated_users)): ?>
                <p>No users found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Registration Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginated_users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td><a href="../admin/delete_user.php?id=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <button onclick="window.location.href='?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>'" <?php echo $page <= 1 ? 'disabled' : ''; ?>>Previous</button>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <button class="<?php echo $i === $page ? 'active' : ''; ?>" onclick="window.location.href='?page=<?php echo $i; ?>&search=<?php echo $search; ?>'"><?php echo $i; ?></button>
                    <?php endfor; ?>
                    <button onclick="window.location.href='?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>'" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>>Next</button>
                </div>
                <p>Showing <?php echo $start + 1; ?> to <?php echo min($start + $items_per_page, $total_users); ?> of <?php echo $total_users; ?> entries</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>