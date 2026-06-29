<?php
session_start();
include(__DIR__ . "/config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all orders
$query = "SELECT id, fullname, phone, total_amount, status, order_date 
          FROM orders 
          ORDER BY id DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>

<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="manage_orders.php" class="active">Manage Orders</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Manage Orders</h2>

    <div class="card mt-4">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= $row['order_date'] ?></td>
                        </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>

</body>
</html>
