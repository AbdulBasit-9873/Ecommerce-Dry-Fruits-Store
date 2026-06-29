<?php
session_start();
include(__DIR__ . "/config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// --- Handle Delete User ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['admin_id']) {
        $conn->query("DELETE FROM users WHERE id='$delete_id'");
    }
    header("Location: manage_users.php");
    exit;
}

// --- Fetch Users ---
$query = "SELECT id, name, email, phone, role, created_at, profile_image FROM users ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
.sidebar {
    width: 220px;
    position: fixed;
    height: 100%;
    background: #343a40;
    color: white;
    padding: 20px;
}
.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    margin: 10px 0;
    padding: 8px;
    border-radius: 5px;
}
.sidebar a.active, .sidebar a:hover { background-color: #495057; }
.main-content { margin-left: 240px; padding: 20px; }
h2 { margin-bottom: 20px; }
img.user-img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
</style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="manage_orders.php">Manage Orders</a>
    <a href="manage_users.php" class="active">Manage Users</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Manage Users</h2>
    <a href="add_user.php" class="btn btn-success mb-3">Add New User</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if($row['profile_image']): ?>
                                    <img src="../uploads/<?= htmlspecialchars($row['profile_image']); ?>" class="user-img" alt="Profile">
                                <?php else: ?>
                                    <img src="../uploads/default_user.png" class="user-img" alt="Profile">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="manage_users.php?delete_id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user?');" 
                                   class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
