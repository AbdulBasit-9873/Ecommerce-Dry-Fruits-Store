<?php
session_start();
include "config/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch All Orders for Logged-in User ---
$sql = "SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">My Orders</h2>

    <?php if ($orders->num_rows > 0) { ?>
        
        <?php while ($order = $orders->fetch_assoc()) { ?>
            <div class="card mb-3">
                <div class="card-body">

                    <h5 class="card-title">
                        Order #<?= $order['id']; ?>
                    </h5>

                    <p class="card-text">
                        <strong>Total Amount:</strong> Rs <?= number_format($order['total_amount']); ?><br>
                        <strong>Status:</strong> 
                        <span class="badge bg-info"><?= ucfirst($order['status']); ?></span><br>

                        <strong>Payment Method:</strong> <?= ucfirst($order['payment_method']); ?><br>

                        <strong>Date:</strong> 
                        <?= date("F j, Y, g:i a", strtotime($order['created_at'])); ?>
                    </p>

                    <!-- CANCEL BUTTON (only if status is pending) -->
                    <?php if ($order['status'] == "pending") { ?>
                        <a href="cancel_order.php?id=<?= $order['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to cancel this order?');">
                           Cancel Order
                        </a>
                    <?php } ?>

                    <a href="order_details.php?id=<?= $order['id']; ?>" class="btn btn-primary btn-sm">
                        View Details
                    </a>

                </div>
            </div>
        <?php } ?>

    <?php } else { ?>
        <div class="alert alert-warning">No orders found.</div>
    <?php } ?>

</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
