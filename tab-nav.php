<?php
// Get the selected status from URL or default to 'all'
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : ''; // For future search functionality
?>

<div class="tab-nav">
    <a href="order.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Orders</a>
    <a href="order.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
    <a href="order.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
    <a href="order.php?status=canceled" class="<?php echo $status_filter === 'canceled' ? 'active' : ''; ?>">Canceled</a>
</div>

<style>
    .tab-nav {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .tab-nav a {
        padding: 10px 15px;
        text-decoration: none;
        color: #333;
        background: #e0e0e0;
        border-radius: 5px;
    }

    .tab-nav a.active {
        background: #333;
        color: white;
        font-weight: bold;
    }

    .tab-nav a:hover {
        background: #555;
        color: white;
    }
</style>