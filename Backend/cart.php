<?php
session_start();
include 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Quantity Update
if (isset($_GET['action']) && isset($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);
    $action = $_GET['action'];

    $q = $conn->query("SELECT quantity FROM cart WHERE id='$cart_id' AND user_id='$user_id'");
    if ($q && $q->num_rows > 0) {
        $data = $q->fetch_assoc();
        $quantity = $data['quantity'];

        if ($action == "increase") $quantity++;
        elseif ($action == "decrease" && $quantity > 1) $quantity--;

        $conn->query("UPDATE cart SET quantity='$quantity' WHERE id='$cart_id' AND user_id='$user_id'");
    }

    header("Location: cart.php");
    exit();
}

// Fetch Cart Items
$query = "
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = '$user_id'
";
$result = $conn->query($query);

$cart_items = [];
$total_amount = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['total'] = $row['price'] * $row['quantity'];
        $total_amount += $row['total'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart | Smart Cart</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
  background:#f4f6f8;
  font-family:'Segoe UI',sans-serif;
}

/* Navbar */
.navbar{
  background:#131921;
}
.navbar-brand{
  font-weight:bold;
}
.nav-link{
  color:#fff !important;
}
.nav-link:hover{
  color:#ffc107 !important;
}

/* Main Container */
.cart-wrapper{
  padding:30px 15px;
}

/* Cart Card */
.cart-card{
  background:#fff;
  border-radius:12px;
  padding:20px;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  margin-bottom:20px;
}

.cart-item{
  display:flex;
  gap:20px;
  align-items:center;
  border-bottom:1px solid #eee;
  padding:15px 0;
}
.cart-item:last-child{
  border-bottom:none;
}

.cart-img{
  width:120px;
  height:120px;
  object-fit:cover;
  border-radius:10px;
}

.qty-box{
  display:flex;
  align-items:center;
  gap:6px;
}
.qty-box a{
  text-decoration:none;
  background:#131921;
  color:#fff;
  width:32px;
  height:32px;
  line-height:32px;
  text-align:center;
  border-radius:6px;
  font-weight:bold;
}
.qty-box input{
  width:50px;
  text-align:center;
  border:1px solid #ccc;
  border-radius:6px;
}

.remove-btn{
  color:#dc3545;
  font-size:14px;
  text-decoration:none;
}
.remove-btn:hover{
  text-decoration:underline;
}

/* Summary Box */
.summary-box{
  background:#fff;
  border-radius:12px;
  padding:20px;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  position:sticky;
  top:20px;
}

.checkout-btn{
  width:100%;
  background:#ffc107;
  border:none;
  font-weight:bold;
  padding:12px;
  border-radius:8px;
  margin-top:15px;
  transition:0.3s;
}
.checkout-btn:hover{
  background:#e0a800;
}

/* 🔥 MOBILE RESPONSIVE FIX */
@media (max-width: 768px){

  .cart-item{
    flex-direction:column;
    align-items:flex-start;
    text-align:left;
  }

  .cart-img{
    width:100%;
    height:200px;
  }

  .summary-box{
    position:static;
    margin-top:20px;
  }

  .qty-box{
    margin:10px 0;
  }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🛒 Smart Cart</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link active" href="cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="user_orders.php">My Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="account.php">My Account</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container cart-wrapper">
  <div class="row g-4">

    <!-- LEFT: CART ITEMS -->
    <div class="col-lg-8">
      <div class="cart-card">
        <h4 class="mb-3">Your Shopping Cart</h4>

        <?php if (!empty($cart_items)) { ?>
        <form action="checkout.php" method="POST">

        <?php foreach ($cart_items as $item): ?>
          <div class="cart-item">
            <input type="checkbox" name="selected_cart[]" value="<?= $item['cart_id'] ?>">

            <img src="images/<?= htmlspecialchars($item['image']) ?>" class="cart-img">

            <div class="flex-grow-1">
              <h5><?= htmlspecialchars($item['name']) ?></h5>
              <p class="text-success fw-bold">Rs. <?= number_format($item['price'],2) ?></p>

              <div class="qty-box">
                <a href="cart.php?action=decrease&cart_id=<?= $item['cart_id'] ?>">−</a>
                <input type="text" value="<?= $item['quantity'] ?>" readonly>
                <a href="cart.php?action=increase&cart_id=<?= $item['cart_id'] ?>">+</a>
              </div>

              <p class="mt-2">Total: <b>Rs. <?= number_format($item['total'],2) ?></b></p>
              <a class="remove-btn" href="remove_from_cart.php?id=<?= $item['cart_id'] ?>">Remove</a>
            </div>
          </div>
        <?php endforeach; ?>

        <button type="submit" class="checkout-btn">Proceed to Checkout ➜</button>
        </form>

        <?php } else { ?>
          <p class="text-center my-4">Your cart is empty.</p>
          <div class="text-center">
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
          </div>
        <?php } ?>

      </div>
    </div>

    <!-- RIGHT: SUMMARY -->
    <?php if (!empty($cart_items)) { ?>
    <div class="col-lg-4">
      <div class="summary-box">
        <h4>Order Summary</h4>
        <div class="d-flex justify-content-between">
          <span>Subtotal:</span>
          <span>Rs. <?= number_format($total_amount,2) ?></span>
        </div>
        <div class="d-flex justify-content-between mt-2">
          <span>Shipping:</span>
          <span>Free</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
          <span>Total:</span>
          <span>Rs. <?= number_format($total_amount,2) ?></span>
        </div>
      </div>
    </div>
    <?php } ?>

  </div>
</div>

<footer class="text-center py-3 bg-dark text-light mt-4">
  © <?= date('Y') ?> Smart Cart | All Rights Reserved
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
