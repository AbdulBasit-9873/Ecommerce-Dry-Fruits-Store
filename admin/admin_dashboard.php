<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>

<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_products.php"><i class="fas fa-box"></i> Manage Products</a>
    <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
    <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <h2>Welcome Admin, <?= $_SESSION['admin_name']; ?> 👨‍🎓</h2>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="fas fa-box fa-3x text-primary"></i>
                <h4 class="mt-3">Products</h4>
                <p>Manage all products</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="fas fa-shopping-cart fa-3x text-success"></i>
                <h4 class="mt-3">Orders</h4>
                <p>View and track orders</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="fas fa-users fa-3x text-info"></i>
                <h4 class="mt-3">Users</h4>
                <p>Manage users & customers</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
