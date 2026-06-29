<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Special Offers - Smart Cart</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
  font-family:'Segoe UI',sans-serif;
  background:#f5f7fa;
}

/* Top Header */
.top-header{
  background:#131921;
  padding:12px 0;
}
.logo{
  font-size:26px;
  font-weight:bold;
  color:#ffc107 !important;
  text-decoration:none;
}
.search-box input{
  border-radius:6px 0 0 6px;
  border:none;
}
.search-box button{
  border-radius:0 6px 6px 0;
  background:#febd69;
  border:none;
}
.header-icons a{
  color:#fff;
  margin-left:15px;
  text-decoration:none;
  font-weight:500;
}
.header-icons a:hover{
  color:#ffc107;
}

/* Navbar */
.main-navbar{
  background:#232f3e;
}
.main-navbar .nav-link{
  color:#fff !important;
  font-weight:500;
}
.main-navbar .nav-link:hover{
  color:#ffc107 !important;
}

/* Offer Card */
.offer-card{
  background:#fff;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  transition:0.3s;
  height:100%;
}
.offer-card:hover{
  transform:translateY(-5px);
}
.offer-img{
  width:100%;
  height:220px;
  object-fit:cover;
}
.offer-badge{
  position:absolute;
  top:10px;
  left:10px;
  background:#dc3545;
  color:#fff;
  padding:5px 10px;
  border-radius:6px;
  font-size:14px;
  font-weight:bold;
}

/* Footer */
.footer{
  background:#111;
  color:#ccc;
  padding:50px 0 20px;
}
.footer h5{
  color:#fff;
  margin-bottom:15px;
}
.footer a{
  color:#bbb;
  text-decoration:none;
  display:block;
  margin-bottom:8px;
}
.footer a:hover{
  color:#ffc107;
}

/* 🔥 MOBILE FIX */
@media (max-width: 768px){
  .search-box{
    margin-top:10px;
  }
  .header-icons{
    margin-top:10px;
    text-align:center;
    width:100%;
  }
  .offer-img{
    height:180px;
  }
}
</style>
</head>
<body>

<!-- ===== TOP HEADER (RESPONSIVE) ===== -->
<div class="top-header">
  <div class="container">
    <div class="row align-items-center g-2">
      
      <!-- Logo -->
      <div class="col-lg-2 col-md-3 col-6">
        <a href="index.php" class="logo">Ecommerce Website</a>
      </div>

      <!-- Search Bar -->
      <div class="col-lg-6 col-md-5 col-12">
        <form class="d-flex search-box" action="products.php" method="get">
          <input class="form-control" type="text" name="search" placeholder="Search products...">
          <button class="btn" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>

      <!-- Icons -->
      <div class="col-lg-4 col-md-4 col-6 text-end header-icons">
        <a href="account.php">
          <i class="fas fa-user"></i>
          <?= isset($_SESSION['user_id']) ? 'My Account' : 'Login'; ?>
        </a>
        <a href="cart.php">
          <i class="fas fa-shopping-cart"></i> Cart
        </a>
        <a href="track_order.php">
          <i class="fas fa-truck"></i> Track
        </a>
      </div>

    </div>
  </div>
</div>

<!-- ===== NAVBAR (MOBILE COLLAPSE FIXED) ===== -->
<nav class="navbar navbar-expand-lg main-navbar">
  <div class="container">
    
    <button class="navbar-toggler bg-warning" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="navMenu">
      
      <!-- Categories Dropdown -->
      <div class="dropdown my-2 my-lg-0">
        <button class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
          <i class="fas fa-bars"></i> Browse Categories
        </button>
        <ul class="dropdown-menu">
          <?php
          $categories = $conn->query("SELECT DISTINCT category FROM products");
          while($cat = $categories->fetch_assoc()){
              echo '<li><a class="dropdown-item" href="category.php?cat='.urlencode($cat['category']).'">'.htmlspecialchars($cat['category']).'</a></li>';
          }
          ?>
        </ul>
      </div>

      <!-- Menu Links (RIGHT SIDE) -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="new_arrivals.php">New Arrivals</a></li>
        <li class="nav-item"><a class="nav-link active text-warning" href="offers.php">Offers</a></li>
        <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
      </ul>

    </div>
  </div>
</nav>

<!-- ===== OFFERS SECTION ===== -->
<div class="container my-5">
  <h3 class="text-center mb-4 fw-bold">🎉 Exclusive Offers & Discounts</h3>
  <div class="row g-4">

  <?php
  $offers = $conn->query("SELECT * FROM products WHERE offer > 0 ORDER BY id DESC");
  if($offers->num_rows > 0){
    while($row = $offers->fetch_assoc()){
        $imagePath = 'images/' . $row['image'];
        if(!file_exists($imagePath)) $imagePath = 'images/default.jpg';
        $discountPrice = $row['price'] * (100 - $row['offer']) / 100;
  ?>
    <div class="col-xl-3 col-lg-4 col-md-6 col-12">
      <div class="offer-card position-relative">
        <span class="offer-badge"><?= $row['offer']; ?>% OFF</span>
        <img src="<?= htmlspecialchars($imagePath); ?>" class="offer-img" alt="<?= htmlspecialchars($row['name']); ?>">
        <div class="p-3">
          <h6 class="fw-bold"><?= htmlspecialchars($row['name']); ?></h6>
          <p class="text-muted mb-1">
            <del>Rs. <?= number_format($row['price'],2); ?></del>
          </p>
          <h5 class="text-success fw-bold">
            Rs. <?= number_format($discountPrice,2); ?>
          </h5>
          <a href="add_to_cart.php?product_id=<?= $row['id']; ?>&page=offers" class="btn btn-dark w-100 mt-2">
            <i class="fas fa-cart-plus"></i> Add to Cart
          </a>
        </div>
      </div>
    </div>
  <?php
    }
  } else {
    echo '<p class="text-center">No special offers available right now.</p>';
  }
  ?>

  </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <div class="container">
    <div class="row text-center text-md-start">
      <div class="col-md-3 mb-4">
        <h5>About Us</h5>
        <p>Premium products with fast delivery across Pakistan.</p>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Quick Links</h5>
        <a href="products.php">All Products</a>
        <a href="new_arrivals.php">New Arrivals</a>
        <a href="offers.php">Offers</a>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Customer Service</h5>
        <a href="account.php">My Account</a>
        <a href="cart.php">Cart</a>
        <a href="track_order.php">Track Order</a>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Contact</h5>
        <p><i class="fas fa-envelope"></i> support@smartcart.pk</p>
        <p><i class="fas fa-phone"></i> +92 300 1234567</p>
        <p><i class="fas fa-map-marker-alt"></i> Pakistan</p>
      </div>
    </div>
    <div class="text-center mt-3">
      © <?= date("Y"); ?> Smart Cart. All Rights Reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
