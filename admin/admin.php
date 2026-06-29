<?php
session_start();
include "../config/db.php"; // adjust path if your db.php location different

// Admin check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle status update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $oid = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $allowed = ['pending','approved','shipped','delivered','cancelled'];
    if (in_array($new_status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $oid);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_orders.php");
    exit;
}

// Fetch orders with user name and total
$sql = "SELECT o.id, o.user_id, u.name AS user_name, o.total, o.status, o.created_at 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Orders</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        select { padding:4px; }
    </style>
</head>
<body>
    <h2>Admin — Orders</h2>
    <p><a href="../admin_dashboard.php">Back to Dashboard</a></p>

    <table>
        <tr><th>Order ID</th><th>User</th><th>Total</th><th>Status</th><th>Placed At</th><th>Action</th></tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name'] ?? 'Guest'); ?></td>
                <td><?php echo number_format($row['total'],2); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <select name="new_status">
                            <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>pending</option>
                            <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>approved</option>
                            <option value="shipped" <?php if($row['status']=='shipped') echo 'selected'; ?>>shipped</option>
                            <option value="delivered" <?php if($row['status']=='delivered') echo 'selected'; ?>>delivered</option>
                            <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                    <a href="order_view.php?id=<?php echo $row['id']; ?>">View</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
