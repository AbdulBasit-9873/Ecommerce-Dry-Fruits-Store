<?php
session_start();
include 'config/db.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// ✅ Fetch Order Details
$order_q = $conn->prepare("
    SELECT o.*, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$order_q->bind_param("i", $order_id);
$order_q->execute();
$order = $order_q->get_result()->fetch_assoc();

if (!$order) {
    echo "<h3>Order not found!</h3>";
    exit();
}

// ✅ Fetch Items
$item_q = $conn->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$item_q->bind_param("i", $order_id);
$item_q->execute();
$items = $item_q->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Order Confirmation</title>
<style>
/* Reset & base */
* { box-sizing: border-box; }
body {
  font-family: "Segoe UI", Tahoma, Arial, sans-serif;
  background: #f7f9fb;
  margin: 0;
  color: #263238;
}

/* Container */
.container {
  max-width: 1100px;
  margin: 36px auto;
  background: #fff;
  box-shadow: 0 8px 24px rgba(22, 54, 84, 0.08);
  border-radius: 12px;
  padding: 22px 28px;
}

/* Heading */
h1 {
  text-align: center;
  color: #0d6efd;
  margin: 6px 0 10px;
  font-weight: 600;
}
.success {
  text-align: center;
  font-size: 14px;
  background: #e6f4ea;
  color: #166534;
  padding: 10px;
  border-radius: 6px;
  margin: 10px auto 18px;
  display: inline-block;
}

/* Layout: left and right */
.order-wrapper {
  display: grid;
  grid-template-columns: 1fr 360px; /* left flexible, right fixed */
  gap: 26px;
  align-items: start;
}

/* Make it responsive */
@media (max-width: 880px) {
  .order-wrapper {
    grid-template-columns: 1fr;
  }
}

/* LEFT SIDE */
.left-side {
  min-width: 0;
}
.summary-box {
  background: #ffffff;
  border-radius: 10px;
  padding: 16px;
  border: 1px solid #eef3fb;
}

.summary-box h2 {
  margin: 0 0 14px 0;
  font-size: 18px;
  color: #222;
  padding-bottom: 8px;
  border-bottom: 2px solid #e7f0ff;
}

/* Table styling with aligned columns */
.order-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 12px;
}
.order-table thead th {
  text-align: left;
  font-size: 13px;
  padding: 10px 8px;
  color: #44525b;
  border-bottom: 1px solid #eef3fb;
  background: #fbfdff;
}
.order-table tbody td {
  padding: 12px 8px;
  border-bottom: 1px solid #f4f6f8;
  vertical-align: middle;
  font-size: 14px;
  color: #263238;
}

/* Specific column widths and text alignment */
.col-product { width: 60%; text-align: left; }
.col-qty     { width: 12%; text-align: center; color: #555; }
.col-price   { width: 28%; text-align: right; font-weight: 600; color: #0d6efd; }

/* Small product name style */
.product-name { display: block; font-weight: 600; color: #1f2d33; }
.product-meta { font-size: 13px; color: #6b7a83; margin-top: 4px; }

/* Totals area */
.totals-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 14px;
}
.totals {
  width: 320px;
  border-radius: 8px;
  background: #fbfdff;
  padding: 12px;
  border: 1px solid #eef6ff;
}
.totals .line { display:flex; justify-content:space-between; margin:8px 0; color:#44525b; }
.totals .total { font-size:16px; font-weight:700; color:#0d6efd; margin-top:6px; }

/* RIGHT SIDE */
.right-side {
  position: relative;
}
.right-card {
  background: #f5f8fb;
  border-radius: 10px;
  padding: 18px;
  border: 1px solid #e8eef6;
}
.right-card h2 {
  margin: 0 0 10px;
  font-size: 16px;
  color: #222;
  border-bottom: 2px solid #e7f0ff;
  padding-bottom: 8px;
}
.right-card p { margin: 8px 0; font-size: 14px; color: #36454a; }
.right-card p b { color: #1f2d33; }

/* Buttons */
.actions { margin-top: 14px; display:flex; gap:10px; flex-wrap:wrap; }
.btn {
  display:inline-block;
  padding:10px 14px;
  border-radius:8px;
  text-decoration:none;
  font-weight:600;
  text-align:center;
  cursor:pointer;
}
.btn-primary { background:#0d6efd; color:#fff; border: none; }
.btn-outline { background: transparent; color:#0d6efd; border: 1px solid #d8e6ff; }

/* small note */
.note { font-size:13px; color:#6b7a83; margin-top:12px; }
</style>
</head>
<body>

<div class="container">
  <h1>🎉 Order Placed Successfully!</h1>
  <div class="success">Thank you — your order has been received.</div>

  <div class="order-wrapper">

    <!-- LEFT: Order summary (product list) -->
    <div class="left-side">
      <div class="summary-box">
        <h2>🧾 Order Summary</h2>

        <table class="order-table" aria-describedby="Order summary table">
          <thead>
            <tr>
              <th class="col-product">Product</th>
              <th class="col-qty">Qty</th>
              <th class="col-price">Price</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = 0;
            if ($items && $items->num_rows > 0) :
              while ($row = $items->fetch_assoc()) :
                $line_total = $row['price'] * $row['quantity'];
                $total += $line_total;
            ?>
            <tr>
              <td class="col-product">
                <span class="product-name"><?= htmlspecialchars($row['name']) ?></span>
                <span class="product-meta">Unit: Rs. <?= number_format($row['price'], 2) ?></span>
              </td>
              <td class="col-qty"><?= intval($row['quantity']) ?></td>
              <td class="col-price">Rs. <?= number_format($line_total, 2) ?></td>
            </tr>
            <?php
              endwhile;
            else:
            ?>
            <tr><td colspan="3" style="text-align:center; padding:18px; color:#6b7a83;">No items found in this order.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <div class="totals-wrap">
          <div class="totals">
            <div class="line"><span>Subtotal</span><span>Rs. <?= number_format($total, 2) ?></span></div>
            <?php
              // If you store delivery charges in DB use that; using 0 or example here:
              $delivery = floatval($order['delivery_charge'] ?? 0);
            ?>
            <div class="line"><span>Delivery</span><span><?= $delivery ? 'Rs. '.number_format($delivery,2) : 'Free' ?></span></div>
            <div class="line total"><span>Total</span><span class="total">Rs. <?= number_format($total + $delivery, 2) ?></span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Customer details -->
    <div class="right-side">
      <div class="right-card">
        <h2>👤 Customer Details</h2>
        <p><b>Name:</b> <?= htmlspecialchars($order['fullname']) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>
        <p><b>Phone:</b> <?= htmlspecialchars($order['phone']) ?></p>
        <p><b>Address:</b> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
        <p><b>Payment Method:</b> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><b>Status:</b> <span style="color:<?= (strtolower($order['status'])==='pending') ? '#d97706' : '#166534' ?>;"><?= htmlspecialchars($order['status']) ?></span></p>
        <p><b>Order Date:</b> <?= htmlspecialchars($order['order_date']) ?></p>

        <div class="actions">
          <a href="index.php" class="btn btn-primary">← Back to Shop</a>
          <a href="user_orders.php" class="btn btn-outline">View My Orders</a>
        </div>

        <div class="note">
          Keep this page for your records. If you selected bank/easypaisa please follow the payment instructions provided in your email.
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
