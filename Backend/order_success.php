<?php
session_start();
include 'config/db.php';

if(!isset($_GET['order_id'])){
    header("Location: index.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$order_q = $conn->query("SELECT * FROM orders WHERE id='$order_id'");
if($order_q->num_rows==0){
    echo "<h2>Invalid Order ID</h2>";
    exit;
}

$order = $order_q->fetch_assoc();

// Fetch order items
$items_q = $conn->query("SELECT * FROM order_items WHERE order_id='$order_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success - Smart Cart</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}
.success-card {
    max-width: 800px;
    margin: 60px auto;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    background: #fff;
}
.success-header {
    background: #28a745;
    color: #fff;
    padding: 25px;
    border-radius: 15px 15px 0 0;
    text-align: center;
}
.success-header h1 {
    font-size: 2rem;
    margin: 0;
}
.success-body {
    padding: 30px;
}
.table th, .table td {
    vertical-align: middle;
}
.btn-custom {
    width: 200px;
}
footer {
    background: #343a40;
    color: #fff;
    padding: 25px 0;
    margin-top: 60px;
    text-align: center;
}
footer a {
    color: #ffc107;
    text-decoration: none;
}
footer a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="card success-card">
    <div class="success-header">
        <h1>🎉 Order Placed Successfully!</h1>
    </div>
    <div class="success-body">
        <p class="fs-5 text-center">Thank you, <b><?php echo htmlspecialchars($order['first_name'].' '.$order['last_name']); ?></b>, for shopping with us!</p>
        <p class="text-center">Your <b>Order ID</b>: <span class="text-primary">#<?php echo $order['id']; ?></span></p>
        <p class="text-center">Total Amount: <b class="text-success">Rs<?php echo number_format($order['total_amount'],2); ?></b></p>

        <h5 class="mt-4">Order Items:</h5>
        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php while($item = $items_q->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>Rs<?php echo number_format($item['price'],2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>Rs<?php echo number_format($item['total'],2); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-success btn-custom me-2">🛒 Continue Shopping</a>
            <a href="user_orders.php" class="btn btn-outline-dark btn-custom">📦 View My Orders</a>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        &copy; <?php echo date("Y"); ?> Smart Cart. All rights reserved. <br>
        <a href="#">About Us</a> | <a href="#">Contact</a> | <a href="#">Privacy Policy</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
