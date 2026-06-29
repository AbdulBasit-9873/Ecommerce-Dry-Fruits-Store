<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Track Order - Smart Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background:#f9f9f9; }
    .header-top { background:#f1f1f1; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; }
    .header-top .logo { font-size:1.6rem; font-weight:bold; color:#000; text-decoration:none; margin-right:20px; }
    .header-top .search-bar { flex:1; display:flex; margin:0 20px; }
    .header-top .search-bar input { flex:1; border:1px solid #ccc; border-radius:5px 0 0 5px; padding:8px; }
    .header-top .search-bar button { border:none; background:#000; color:white; padding:8px 15px; border-radius:0 5px 5px 0; }
    .header-top .icons a { margin-left:15px; color:#333; text-decoration:none; }
    .header-top .icons a:hover { color:#1abc9c; }

    .navbar-main { background:#000; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; }
    .navbar-main ul { list-style:none; display:flex; margin:0; padding:0; }
    .navbar-main ul li { margin-left:20px; }
    .navbar-main ul li a { color:#fcc707; text-decoration:none; font-weight:500; }
    .navbar-main ul li a:hover { color:#fcba14; }

    .browse-dropdown .btn { background:#ffb004; color:#fff; font-weight:500; border:none; }
    .browse-dropdown .btn:hover { background:#1abc9c; color:#fea826; }

    .track-container { background:#fff; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-top:40px; }
    .order-status { padding:15px; border-radius:5px; margin-top:15px; font-weight:500; text-align:center; }
    .status-pending { background:#fff3cd; color:#856404; }
    .status-shipped { background:#cce5ff; color:#004085; }
    .status-delivered { background:#d4edda; color:#155724; }
    .status-cancelled { background:#f8d7da; color:#721c24; }

    .footer { background:#363535fe; color:#fff; padding:60px 0; margin-top:60px; }
    .footer h5 { color:#fff; }
    .footer a { color:#fff; text-decoration:none; }
    .footer a:hover { color:#1abc9c; }
    .social-icons a { margin:0 8px; font-size:20px; color:#fff; }
    .social-icons a:hover { color:#1abc9c; }
  </style>
</head>
<body>

<!-- Header -->
<div class="header-top">
  <a href="index.php" class="logo">Smart Cart</a>
  <form class="search-bar" action="products.php" method="get">
    <input type="text" name="search" placeholder="Search products...">
    <button type="submit"><i class="fas fa-search"></i></button>
  </form>
  <div class="icons">
    <a href="account.php"><i class="fas fa-user"></i> <?php echo isset($_SESSION['user_id']) ? "My Account" : "Login/Register"; ?></a>
    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
    <a href="track_order.php" class="text-warning"><i class="fas fa-truck"></i> Track Order</a>
  </div>
</div>

<!-- Navbar -->
<div class="navbar-main">
  <div class="dropdown browse-dropdown">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
      <i class="fas fa-bars"></i> Browse Categories
    </button>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="category.php?cat=almonds">Almonds</a></li>
      <li><a class="dropdown-item" href="category.php?cat=cashews">Cashews</a></li>
      <li><a class="dropdown-item" href="category.php?cat=pistachios">Pistachios</a></li>
      <li><a class="dropdown-item" href="category.php?cat=raisins">Raisins</a></li>
      <li><a class="dropdown-item" href="category.php?cat=walnuts">Walnuts</a></li>
    </ul>
  </div>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Shop</a></li>
    <li><a href="new_arrivals.php">New Arrivals</a></li>
    <li><a href="offers.php">Offers</a></li>
    <li><a href="wishlist.php">Wishlist</a></li>
  </ul>
</div>

<!-- Track Order Section -->
<div class="container">
  <div class="track-container">
    <h3 class="text-center mb-4"><i class="fas fa-truck"></i> Track Your Order</h3>
    <form method="post" class="text-center">
      <div class="mb-3">
        <input type="text" name="order_id" class="form-control" placeholder="Enter Order ID" required>
      </div>
      <button type="submit" name="track" class="btn btn-dark px-4">Check Status</button>
    </form>

    <?php
    if (isset($_POST['track'])) {
        $order_id = intval($_POST['order_id']);  // secure conversion
        $query = "SELECT * FROM orders WHERE id='$order_id'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $order = $result->fetch_assoc();
            echo "<div class='mt-4'>";
            echo "<h5>Order Details:</h5>";
            echo "<p><strong>Order ID:</strong> " . htmlspecialchars($order['id']) . "</p>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($order['fullname']) . "</p>";
            echo "<p><strong>Date:</strong> " . htmlspecialchars($order['order_date']) . "</p>";
            echo "<p><strong>Total:</strong> Rs. " . htmlspecialchars($order['total_amount']) . "</p>";
            echo "<p><strong>Payment Method:</strong> " . htmlspecialchars($order['payment_method']) . "</p>";

            $status = strtolower($order['status']);
            $statusClass = match($status) {
                'pending' => 'status-pending',
                'shipped' => 'status-shipped',
                'delivered' => 'status-delivered',
                'cancelled' => 'status-cancelled',
                default => 'status-pending',
            };

            echo "<div class='order-status $statusClass'>Current Status: " . ucfirst($status) . "</div>";
            echo "</div>";

            // Fetch ordered items
            $items_query = "SELECT * FROM order_items WHERE order_id='$order_id'";
            $items_result = $conn->query($items_query);

            if ($items_result && $items_result->num_rows > 0) {
                echo "<h5 class='mt-4'>Ordered Items:</h5>";
                echo "<table class='table table-bordered'><thead><tr><th>Product ID</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>";
                while ($item = $items_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$item['product_id']}</td>
                            <td>{$item['quantity']}</td>
                            <td>Rs. {$item['price']}</td>
                            <td>Rs. {$item['total']}</td>
                          </tr>";
                }
                echo "</tbody></table>";
            }
        } else {
            echo "<div class='alert alert-danger mt-4'>❌ Order not found. Please check your Order ID and try again.</div>";
        }
    }
    ?>
  </div>
</div>

<!-- Footer -->
<div class="footer">
  <div class="container text-center">
    <p>&copy; <?php echo date('Y'); ?> Smart Cart. All Rights Reserved.</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
