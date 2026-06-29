<?php
session_start();
include 'config/db.php';

// --- Verify user login ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";

// --- Cancel order logic (DELETE from DB) ---
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $order_id = intval($_GET['cancel']);
    
    // Check if this order belongs to the current user
    $check_sql = "SELECT * FROM orders WHERE id=? AND user_id=?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $order_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Delete related order_items first
        $conn->query("DELETE FROM order_items WHERE order_id='$order_id'");
        // Then delete main order
        $conn->query("DELETE FROM orders WHERE id='$order_id'");
        $success = "Order #$order_id cancelled and removed successfully.";
    }
}

// --- Fetch user orders ---
$sql = "SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - SmartCart</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
  background: #f5f6fa;
  font-family: 'Poppins', sans-serif;
}
.container {
  max-width: 1000px;
  margin-top: 60px;
}
h2.title {
  text-align: center;
  color: #2e8b57;
  font-weight: 600;
  margin-bottom: 40px;
}
.alert {
  max-width: 700px;
  margin: auto;
  border-radius: 10px;
}
.order-card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 5px 18px rgba(0,0,0,0.07);
  margin-bottom: 30px;
  padding: 25px;
  transition: 0.3s;
}
.order-card:hover {
  transform: translateY(-3px);
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
  margin-bottom: 15px;
}
.order-header h5 {
  color: #333;
  font-weight: 600;
}
.order-header small {
  color: #777;
}
.order-meta {
  margin-bottom: 10px;
}
.order-meta p {
  margin: 5px 0;
  font-size: 15px;
  color: #555;
}
.table th {
  background: #2e8b57;
  color: white;
}
.table td {
  vertical-align: middle;
}
.table img {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}
.btn-cancel {
  background: #dc3545;
  color: white;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 14px;
  transition: 0.3s;
}
.btn-cancel:hover {
  background: #b02a37;
}
.btn-view {
  background: #2e8b57;
  color: white;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 14px;
  transition: 0.3s;
}
.btn-view:hover {
  background: #27694b;
}
.empty-orders {
  text-align: center;
  margin-top: 50px;
}
.empty-orders img {
  width: 180px;
  margin-bottom: 20px;
}
</style>
</head>
<body>

<!-- Navbar / Header -->
<nav class="navbar navbar-dark bg-dark py-3">
  <div class="container d-flex justify-content-between align-items-center">
    <!-- Centered Title -->
    <div class="flex-grow-1 text-center">
      <span style="font-size:40px; font-weight:700; color:#fff;">👜 My Orders</span>
    </div>

    <!-- Back to Home Button -->
    <a href="index.php" class="btn btn-success"
       style="padding:12px 30px; font-size:20px; font-weight:600;">
       🏠 Back to Home
    </a>
  </div>
</nav>


<div class="container">
  <!-- <h2 class="title mt-4"><i class="fa-solid fa-box"></i> My Orders</h2>-->

  <?php if (!empty($success)): ?>
    <div class="alert alert-success text-center">
      <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success); ?>
    </div>
  <?php endif; ?>

  <?php if ($orders->num_rows > 0): ?>
    <?php while ($order = $orders->fetch_assoc()): ?>
      <div class="order-card">
        <div class="order-header">
          <div>
            <h5>Order #<?= $order['id']; ?></h5>
            <small><i class="fa-regular fa-calendar"></i>
              <?= date("F d, Y g:i A", strtotime($order['order_date'])); ?>
            </small>
          </div>
          <div>
            <span class="badge bg-success"><?= htmlspecialchars($order['status']); ?></span>
          </div>
        </div>

        <div class="order-meta">
          <p><strong>Total Amount:</strong> Rs <?= number_format($order['total_amount'], 2); ?></p>
          <p><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
        </div>

        <?php
        $items_sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = ?";
        $items_stmt = $conn->prepare($items_sql);
        $items_stmt->bind_param("i", $order['id']);
        $items_stmt->execute();
        $items = $items_stmt->get_result();
        ?>

        <?php if ($items->num_rows > 0): ?>
          <table class="table table-bordered align-middle mt-3">
            <thead>
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                  <td><img src="images/<?= htmlspecialchars($item['image']); ?>" alt=""></td>
                  <td><?= htmlspecialchars($item['name']); ?></td>
                  <td><?= $item['quantity']; ?></td>
                  <td>Rs <?= number_format($item['total'], 2); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <div class="text-end mt-3">
          <a href="?cancel=<?= $order['id']; ?>" class="btn-cancel me-2"
             onclick="return confirm('Are you sure you want to cancel this order?');">
             Cancel Order
          </a>
          <a href="checkout_success.php?order_id=<?= $order['id']; ?>" class="btn-view">View Details</a>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="empty-orders">
      <img src="assets/empty-cart.png" alt="No Orders">
      <h5>No orders found!</h5>
      <p class="text-muted">You haven’t placed any orders yet.</p> 
      <a href="products.php" class="btn btn-success btn-sm">🛒 Start Shopping</a>
    </div>
  <?php endif; ?>
</div>

</body>

</html>
