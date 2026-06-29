<?php
    
  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    
session_start();
include "config/db.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Add to Cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $checkProduct = $conn->prepare("SELECT id FROM products WHERE id=?");
    $checkProduct->bind_param("i", $product_id);
    $checkProduct->execute();
    $checkProduct->store_result();

    if ($checkProduct->num_rows > 0) {
        if ($user_id) {
            $checkCart = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
            $checkCart->bind_param("ii", $user_id, $product_id);
            $checkCart->execute();
            $res = $checkCart->get_result();

            if ($res->num_rows == 0) {
                $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                $insert->bind_param("ii", $user_id, $product_id);
                $insert->execute();
                $_SESSION['msg'] = "Product added to cart!";
            } else {
                $_SESSION['msg'] = "Already in cart!";
            }
        } else {
            if (!isset($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = [];
            if (!in_array($product_id, $_SESSION['guest_cart'])) {
                $_SESSION['guest_cart'][] = $product_id;
                $_SESSION['msg'] = "Product added to cart!";
            }
        }
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> Ecommerce-Dry-Fruits-Website | Online Store</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    background:#f3f4f6;
    font-family:Segoe UI, sans-serif;
}
.header{
    background:#fff;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}
.logo{
    font-size:26px;
    font-weight:700;
    text-decoration:none;
    color:#000;
}
.navbar-custom{
    background:#131921;
}
.navbar-custom .nav-link{
    color:#fff !important;
    font-weight:500;
}
.navbar-custom .nav-link:hover{
    color:#ffc107 !important;
}
.carousel img{
    width:100%;
    height:auto; /* IMPORTANT */
    max-height:420px;
    object-fit:contain; /* cover ki jagah contain */
    border-radius:12px;
}

@media(max-width:768px){
.product-card img{
    height:150px; /* mobile par space kam */
}
}



.product-card:hover{
    transform:translateY(-5px);
}

.product-card{
    background:#fff;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s;
    height:100%;
    display:flex;
    flex-direction:column;
    overflow:hidden;
}

.product-card img{
    width:100%;
    height:auto;
    aspect-ratio: 1 / 1;
    object-fit: contain;
    background:#f8f9fa;
    padding:8px;
}

.product-info{
    padding:12px;
    text-align:center;
    flex-grow:1;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}


.product-info h6{
    min-height:45px;
    font-weight:600;
}
.btn-cart{
    background:#000;
    color:#fff;
    width:100%;
    margin-bottom:6px;
}
.btn-cart:hover{
    background:#1abc9c;
}
.offer-banner{
    width:100%;
    background:linear-gradient(90deg,#ff512f,#dd2476);
    color:#fff;
    padding:15px;
    text-align:center;
    font-size:18px;
    font-weight:600;
    margin-top:40px;
}
.footer{
    background:#111;
    color:#ccc;
    padding:50px 0 20px;
}
.footer h5{color:#fff;}
.footer a{
    color:#bbb;
    text-decoration:none;
    display:block;
    margin-bottom:6px;
}
.footer a:hover{color:#1abc9c;}
    <!-- WhatsApp Floating Button -->
<a href="https://wa.me/923XXXXXXXXX" target="_blank" class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
</a>
</style>
</head>
<body>

    <!-- WhatsApp Floating Button -->
<a href="https://wa.me/923269873271" target="_blank" class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
</a>

<style>
.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #25D366;
    color: #fff;
    font-size: 28px;
    padding: 12px 16px;
    border-radius: 50%;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 999;
    transition: 0.3s;
}

.whatsapp-float:hover {
    background-color: #1ebe5d;
    transform: scale(1.1);
}
</style>
    
<!-- HEADER -->
<div class="header py-2">
<div class="container-fluid px-4">
<div class="row align-items-center g-3">
<div class="col-lg-2 col-6">
<a href="index.php" class="logo"> Ecommerce-Website</a>
</div>

    
    
    
<div class="col-lg-6 col-12">
<form class="d-flex" action="products.php" method="get">
<input class="form-control me-2" type="search" name="search" placeholder="Search products...">
<button class="btn btn-dark"><i class="fas fa-search"></i></button>
</form>
</div>

<div class="col-lg-4 col-6 text-end">
    <!-- Cart Icon -->
    <a href="cart.php" class="me-3 text-dark"><i class="fas fa-shopping-cart fa-lg"></i></a>

    <!-- Track Order Icon -->
    <a href="track_order.php" class="me-3 text-dark" title="Track Order">
        <i class="fas fa-shipping-fast fa-lg"></i>
    </a>

    <!-- Account / Login Icon -->
    <?php if(isset($_SESSION['user_id'])){ ?>
        <a href="account.php" class="text-dark me-2"><i class="fas fa-user fa-lg"></i></a>
    <?php } else { ?>
        <a href="login.php" class="text-dark"><i class="fas fa-user fa-lg"></i></a>
    <?php } ?>
</div>

</div>
</div>
</div>

<!-- UPDATED NAVBAR (RIGHT + LOGIN SYSTEM) -->
<nav class="navbar navbar-expand-lg navbar-custom">
<div class="container-fluid px-4">

<button class="navbar-toggler bg-warning" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse justify-content-end" id="menu">
<ul class="navbar-nav align-items-center gap-2">

<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="products.php">Shop</a></li>
<li class="nav-item"><a class="nav-link" href="offers.php">Offers</a></li>
<li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
<li class="nav-item"><a class="nav-link" href="user_orders.php">My Orders</a></li>

<?php if(isset($_SESSION['user_id'])){ ?>
    <!-- SHOW WHEN LOGGED IN -->
    <li class="nav-item">
        <a class="nav-link text-warning fw-bold" href="account.php">My Account</a>
    </li>
    <li class="nav-item">
        <a class="btn btn-danger ms-2" href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </li>
<?php } else { ?>
    <!-- SHOW WHEN NOT LOGGED IN -->
    <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
    </li>
    <li class="nav-item">
        <a class="btn btn-warning ms-2" href="register.php">Sign Up</a>
    </li>
<?php } ?>

</ul>
</div>
</div>
</nav>

<!-- MESSAGE -->
<div class="container-fluid px-4 mt-3">
<?php if(isset($_SESSION['msg'])){ ?>
<div class="alert alert-info text-center">
<?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
</div>
<?php } ?>
</div>

<!-- CAROUSEL -->
<div class="container-fluid px-4 mt-3">
<div id="slider" class="carousel slide" data-bs-ride="carousel">
<div class="carousel-inner">
<div class="carousel-item active">
<img src="https://www.kanzandmuhul.com/cdn/shop/files/collection_banner_mobile_1_1800x.png">
</div>
<div class="carousel-item">
<img src="https://farmorganicindia.com/cdn/shop/files/Shilajit_Gummy_Banner_111.jpg">
</div>
</div>
<button class="carousel-control-prev" type="button" data-bs-target="#slider" data-bs-slide="prev">
<span class="carousel-control-prev-icon"></span>
</button>
<button class="carousel-control-next" type="button" data-bs-target="#slider" data-bs-slide="next">
<span class="carousel-control-next-icon"></span>
</button>
</div>
</div>

<!-- PRODUCTS -->
<div class="container-fluid px-4 mt-5">
<h2 class="text-center mb-4">Featured Products</h2>
<div class="row g-4">
<?php
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
while($row = $result->fetch_assoc()){
$imagePath = "images/".$row['image'];
?>
<div class="col-6 col-md-4 col-lg-3 col-xl-3">
<div class="product-card">
<img src="<?= htmlspecialchars($imagePath); ?>">
<div class="product-info">
<h6><?= htmlspecialchars($row['name']); ?></h6>
<p class="fw-bold text-success">Rs. <?= number_format($row['price'],2); ?></p>
<a href="index.php?action=add&id=<?= $row['id']; ?>" class="btn btn-cart">Add to Cart</a>
<a href="wishlist_add.php?product_id=<?= $row['id']; ?>" class="btn btn-warning w-100">Wishlist</a>
</div>
</div>
</div>
<?php } ?>
</div>
</div>

<div class="offer-banner">
🎉 Get 10% OFF on your First Order! Use Code WELCOME10
</div>

<footer class="footer mt-5 bg-dark text-white pt-5 pb-3">
  <div class="container-fluid px-4">
    <div class="row">

      <!-- About Section -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3">About Us</h5>
        <p>
          We are a premium dry fruits store in Pakistan offering high-quality
          almonds, cashews, pistachios, and organic products at affordable prices.
          Freshness and customer satisfaction is our priority.
        </p>
      </div>

      <!-- Customer Service -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3">Customer Service</h5>
        <p><strong>Phone:</strong> +92 3269873271</p>
        <p><strong>Email:</strong> abdulbasit48308@gmail.com</p>
        <p><strong>Address:</strong> Islamabad, Pakistan</p>

        <a href="contact.php" class="d-block text-white">Contact Us</a>
        <a href="faq.php" class="d-block text-white">FAQ</a>
      </div>

      <!-- Quick Links + Social -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3">Quick Links</h5>
        <a href="about.php" class="d-block text-white">About</a>
        <a href="offers.php" class="d-block text-white">Offers</a>
        <a href="shop.php" class="d-block text-white">Shop</a>

        <!-- Social Media -->
        <div class="mt-3">
          <h6>Follow Us</h6>
          <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i> Facebook</a><br>
          <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i> Instagram</a><br>
          <a href="#" class="text-white me-3"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
      </div>

    </div>

    <!-- Bottom Bar -->
    <hr class="bg-light">
    <div class="text-center">
      <p class="mb-0">
        © 2026 Dry Fruits Store | All Rights Reserved
      </p>
    </div>

  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
