<?php
@include '../db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Get admin details from session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$profile_image = '../uploads/default.jpg'; // Default admin image

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
    <title>Manage Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #212529;
            padding: 1rem;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #343a40;
            border-radius: 5px;
        }

        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1100;
            top: 0;
            left: 0;
            background-color: #212529;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, .2);
        }

        .sidenav a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            background-color: #343a40;
            color: #fff;
        }

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .profile-info span {
            color: #fff;
            font-weight: 500;
        }

        .profile-info .btn-danger {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        .profile-info .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        .container {
            max-width: 1400px;
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .update-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background-color: #0d6efd;
            color: #fff;
        }

        .delete-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            opacity: 0.9;
        }

        .search-container {
            margin-bottom: 2rem;
        }

        .pagination {
            margin-top: 2rem;
            justify-content: center;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="navbar-toggler-icon" onclick="openNav()" style="cursor: pointer; margin-right: 1rem;"></span>
                    <img src="../images/Readscape.png" alt="ReadScape" class="rounded-circle" width="40" height="40">
                    <span class="ms-2 text-white fw-bold">ReadScape</span>
                        <div class="sidenav" id="Sidenav">
                            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                            <a href="../admin/admin_dashboard.php"><i class="fas fa-dashboard me-2"></i>Dashboard</a>
                            <a href="../admin/total_books.php"><i class="fas fa-book me-2"></i>Books</a>
                            <a href="../admin/customers.php"><i class="fas fa-users me-2"></i>Customers</a>
                            <a href="#"><i class="fas fa-cog me-2"></i>Settings</a>
                            <a href="#"><i class="fas fa-question-circle me-2"></i>Help</a>
                            <a href="../admin/manage_user.php"><i class="fas fa-user-cog me-2"></i>Manage Users</a>
                            <a href="./admin.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
                        </div>
                        <script>
                            function openNav() {
                                document.getElementById("Sidenav").style.width = "240px";
                            }

                            function closeNav() {
                                document.getElementById("Sidenav").style.width = "0";
                            }
                        </script>
                    </div>
            <div class="profile-info">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                <span class="text-white"><?php echo $fname . " " . $lname; ?></span>
                <a href="../admin/logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Users: <?php echo $total_users; ?></h2>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" class="form-control" name="search" placeholder="Search by Full Name"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="total_users.php" class="btn btn-danger">Reset</a>
                </form>
            </div>
        </div>

        <?php if (empty($paginated_users)): ?>
            <div class="alert alert-info">No users found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
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
                                <td>
                                    <a href="../admin/delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <p class="text-muted">Showing <?php echo $start + 1; ?> to <?php echo min($start + $items_per_page, $total_users); ?> of <?php echo $total_users; ?> entries</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>