<!-- <?php
        @include '../db_connect.php';

        session_start();

        // Ensure session user is set
        if (!isset($_SESSION['id'])) {
            header("Location: ../admin/admin.php", true, 302);
            exit();
        }

        $user_id = $_SESSION['id'];

        // Fetch user details
        $sql = "SELECT fname, lname, email, phone, address FROM `users` WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $fname = htmlspecialchars($row['fname']);
            $lname = htmlspecialchars($row['lname']);
            $email = htmlspecialchars($row['email']);
            $phone = htmlspecialchars($row['phone']);
            $address = htmlspecialchars($row['address']);
        } else {
            $fname = "User";
            $lname = "Not Found";
            $email = "Not Found";
            $phone = "Not Found";
            $address = "Not Found";
        }

        mysqli_stmt_close($stmt);

        // Fetch all orders
        $orders_query = mysqli_query($conn, "SELECT orders.id, orders.order_date, users.fname, users.lname, users.email, users.phone, users.address FROM orders JOIN users ON orders.user_id = users.id");

        mysqli_close($conn);
        ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Ordered Users</title>
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
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .view-btn {
            display: inline-block;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .view-btn:hover {
            background-color: #555;
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
        <h1>Total User w Orders</h1>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($orders_query)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['order_date']}</td>
                        <td>{$row['fname']} {$row['lname']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                        <td><a href='view_order_details.php?id={$row['id']}' class='view-btn'>View</a></td>
                      </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html> -->