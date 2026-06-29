<?php
session_start();
include 'config/db.php';

// ✅ Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// ✅ Get selected cart items from previous page
$selected_cart_ids = isset($_POST['selected_cart']) ? $_POST['selected_cart'] : [];

if (empty($selected_cart_ids)) {
    echo "<div style='text-align:center; margin-top:100px;'>
            <h2>No items selected for checkout 🛒</h2>
            <a href='cart.php'>Go back to cart</a>
          </div>";
    exit();
}

// ✅ Sanitize and prepare cart IDs
$ids_str = implode(",", array_map('intval', $selected_cart_ids));

// ✅ Fetch user info
$user_query = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// ✅ Fetch selected Cart Items
$query = "
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? AND c.id IN ($ids_str)
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "<div style='text-align:center; margin-top:100px;'>
            <h2>No valid products found for checkout 🚫</h2>
            <a href='cart.php'>Go back to cart</a>
          </div>";
    exit();
}

// ✅ Calculate totals
$cart_items = [];
$subtotal = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $subtotal += $row['subtotal'];
    $cart_items[] = $row;
}

$delivery_charges = 200;
$grand_total = $subtotal + $delivery_charges;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - MyShop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background-color: #f7f9fb;
  font-family: 'Segoe UI', sans-serif;
}
.checkout-wrapper {
  max-width: 1100px;
  margin: 50px auto;
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.1);
  overflow: hidden;
}
.header {
  background: #232f3e;
  color: #fff;
  padding: 20px;
  text-align: center;
}
.header h2 {
  margin: 0;
}
.order-summary {
  border-right: 1px solid #eee;
  padding: 25px;
  background: #fcfcfc;
}
.order-summary img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 10px;
}
.order-summary h5 {
  font-weight: 600;
}
.total-table th {
  width: 60%;
}
.checkout-form {
  padding: 25px;
}
label {
  font-weight: 500;
}
.payment-methods label {
  display: block;
  background: #f1f3f5;
  border-radius: 8px;
  padding: 10px 15px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: 0.3s;
}
.payment-methods input[type="radio"] {
  margin-right: 10px;
}
.payment-methods label:hover {
  background: #e9ecef;
}
.btn-confirm {
  width: 100%;
  padding: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  background: #febd69;
  border: none;
  color: #111;
  transition: 0.3s;
}
.btn-confirm:hover {
  background: #f3a847;
}
footer {
  text-align: center;
  margin-top: 40px;
  color: #888;
}
</style>
</head>
<body>

<div class="checkout-wrapper">
  <div class="header">
    <h2>🛍 Checkout</h2>
    <p>Review your selected products and provide delivery details</p>
  </div>

  <div class="row g-0">
    <!-- LEFT SIDE: Order Summary -->
    <div class="col-md-5 order-summary">
      <h5 class="mb-3">Order Summary</h5>
      <div class="cart-items">
        <?php foreach ($cart_items as $item): ?>
          <div class="d-flex align-items-center mb-3 border-bottom pb-2">
            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="">
            <div class="ms-3">
              <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
              <small>Qty: <?php echo $item['quantity']; ?></small><br>
              <span>Rs. <?php echo number_format($item['subtotal']); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <table class="table total-table mt-4">
        <tr>
          <th>Subtotal:</th>
          <td>Rs. <?php echo number_format($subtotal); ?></td>
        </tr>
        <tr>
          <th>Delivery Charges:</th>
          <td>Rs. <?php echo number_format($delivery_charges); ?></td>
        </tr>
        <tr>
          <th>Total Payable:</th>
          <td><b>Rs. <?php echo number_format($grand_total); ?></b></td>
        </tr>
      </table>
    </div>

    <!-- RIGHT SIDE: Checkout Form -->
    <div class="col-md-7 checkout-form">
      <form action="place_order.php" method="POST">
        <input type="hidden" name="total_amount" value="<?php echo $grand_total; ?>">

        <?php foreach ($cart_items as $item): ?>
          <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
          <input type="hidden" name="quantity[]" value="<?php echo $item['quantity']; ?>">
          <input type="hidden" name="cart_id[]" value="<?php echo $item['cart_id']; ?>">
        <?php endforeach; ?>
        <?php foreach ($cart_items as $item): ?>
        <input type="hidden" name="selected_cart[]" value="<?= $item['cart_id'] ?>">
        <?php endforeach; ?>


        <div class="mb-3">
          <label>Full Name</label>
          <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label>Phone</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label>City</label>
          <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Province</label>
          <input type="text" name="province" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Full Address</label>
          <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="mb-2">Select Payment Method</label>
          <div class="payment-methods">
            <label><input type="radio" name="payment_method" value="Easypaisa" required> Easypaisa</label>
            <label><input type="radio" name="payment_method" value="JazzCash"> JazzCash</label>
            <label><input type="radio" name="payment_method" value="Cash on Delivery"> Cash on Delivery</label>
          </div>
        </div>

        <button type="submit" class="btn-confirm">
          ✅ Confirm Order (Rs. <?php echo number_format($grand_total); ?>)
        </button>
      </form>
    </div>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> MyShop. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
   