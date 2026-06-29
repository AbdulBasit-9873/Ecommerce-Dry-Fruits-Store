<?php
session_start();
include "config/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch Latest 8 Products ---
$sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 8";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Arrivals - Smart Cart</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }

/* Navbar */
.navbar-main {
  background-color: #000;
  padding: 12px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.navbar-nav .nav-link {
  color: #d4af37;
  font-weight: 500;
  transition: all 0.3s ease;
}
.navbar-nav .nav-link:hover { color: #90ee90; }
.navbar-nav .nav-link.active {
  color: #90ee90;
  border-bottom: 2px solid #d4af37;
}
.btn-success { background:#90ee90; color:#000; border:none; }
.btn-success:hover { background:#d4af37; color:#000; }

/* Product Cards */
.product-card {
  border: none;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.product-card img {
  height: 200px;
  object-fit: cover;
  width: 100%;
  border-radius: 8px;
}
.price-tag { font-size: 1.2rem; font-weight: bold; color: #28a745; }
footer {
  background: #343a40;
  color: #fff;
  padding: 30px 0;
  margin-top: 50px;
}
footer a { color: #ffc107; text-decoration: none; }
footer a:hover { text-decoration: underline; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-main">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🛒 Smart Cart</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="new_arrivals.php">New Arrivals</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
        <li class="nav-item"><a class="nav-link" href="user_orders.php">Orders</a></li>
        <li class="nav-item"><a class="btn btn-danger btn-sm ms-2" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- New Arrivals Section -->
<div class="container mt-5">
  <h2 class="text-center mb-4 text-success">🌟 New Arrivals</h2>
  <div class="row">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {

            // AUTO-DETECT IMAGE LOCATION (100% working)
            $imageFile = htmlspecialchars($row['image']);

            if (file_exists("uploads/$imageFile")) {
                $imagePath = "uploads/$imageFile";
            } elseif (file_exists("admin/uploads/$imageFile")) {
                $imagePath = "admin/uploads/$imageFile";
            } elseif (file_exists("images/$imageFile")) {
                $imagePath = "images/$imageFile";
            } else {
                $imagePath = "assets/no-image.png"; 
            }

            echo "
            <div class='col-md-4 col-lg-3 mb-4'>
              <div class='card product-card h-100'>
                <img src='$imagePath' alt='" . htmlspecialchars($row['name']) . "'>
                <div class='card-body text-center'>
                  <h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>
                  <p class='text-muted'>" . htmlspecialchars(substr($row['description'],0,60)) . "...</p>
                  <p>
                    <span class='badge bg-info'>" . htmlspecialchars($row['category']) . "</span> 
                    <span class='badge bg-secondary'>" . htmlspecialchars($row['size']) . "</span> 
                    <span class='badge bg-warning text-dark'>" . htmlspecialchars($row['color']) . "</span>
                  </p>
                  <p class='price-tag'>$" . htmlspecialchars($row['price']) . "</p>
                  <a href='products.php?action=add&id=" . $row['id'] . "' class='btn btn-success w-100 mb-2'>🛒 Add to Cart</a>
                  <a href='wishlist_add.php?product_id=" . $row['id'] . "' class='btn btn-outline-warning w-100 mb-2'>💛 Add to Wishlist</a>
                  <form action='buy_now.php' method='POST'>
                    <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                    <input type='hidden' name='quantity' value='1'>
                    <button type='submit' class='btn btn-primary w-100'>⚡ Buy Now</button>
                  </form>
                </div>
              </div>
            </div>";
        }
    } else {
        echo "<p class='text-center text-muted mt-5'>No new products available.</p>";
    }
    ?>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="container text-center">
    <p>&copy; <?= date("Y"); ?> Smart Cart. All rights reserved.</p>
    <p><a href='#'>About Us</a> | <a href='#'>Contact</a> | <a href='#'>Privacy Policy</a></p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
