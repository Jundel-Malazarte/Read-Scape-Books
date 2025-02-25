<?php
@include 'db_connect.php';

session_start();

// Check if the database connection exists
if (!isset($conn) || !$conn) {
    die("Database connection failed. Please check db_connect.php.");
}

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin.php");
    exit();
}

// Fetch user registrations per month
$query = "SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(id) AS count 
          FROM users 
          WHERE YEAR(created_at) = YEAR(CURDATE())
          GROUP BY MONTH(created_at)";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$months = [];
$counts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $counts[] = $row['count'];
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registrations Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>User Registrations Per Month</h1>
        <canvas id="registrationChart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('registrationChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>