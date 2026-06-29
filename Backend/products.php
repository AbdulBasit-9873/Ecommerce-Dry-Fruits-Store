<?php
session_start();
include "config/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $checkProduct = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $checkProduct->bind_param("i", $product_id);
    $checkProduct->execute();
    $checkProduct->store_result();

    if ($checkProduct->num_rows > 0) {
        $checkCart = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $checkCart->bind_param("ii", $user_id, $product_id);
        $checkCart->execute();
        $result = $checkCart->get_result();

        if ($result->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $insert->bind_param("ii", $user_id, $product_id);
            $insert->execute();
            $_SESSION['msg'] = "✅ Product added to cart successfully!";
        } else {
            $_SESSION['msg'] = "⚠️ Product is already in your cart!";
        }
        $checkCart->close();
    } else {
        $_SESSION['msg'] = "❌ Invalid product!";
    }

    $checkProduct->close();
    header("Location: products.php");
    exit;
}

// Filters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$size = isset($_GET['size']) ? $conn->real_escape_string($_GET['size']) : '';
$color = isset($_GET['color']) ? $conn->real_escape_string($_GET['color']) : '';

$sql = "SELECT * FROM products WHERE 1=1";
if ($search != '') { $sql .= " AND name LIKE '%$search%'"; }
if ($category != '') { $sql .= " AND category='$category'"; }
if ($size != '') { $sql .= " AND size='$size'"; }
if ($color != '') { $sql .= " AND color='$color'"; }

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products - Ecommerce</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
.product-card img { height: 180px; object-fit: cover; border-radius:10px; box-shadow:0 0 8px rgba(0,0,0,0.2); }
.price-tag { font-size: 1.2rem; font-weight: bold; color: #28a745; }
.stock-badge { background: #6c757d; font-size: 0.9rem; }
#searchInput { max-width: 400px; margin: 0 auto 30px auto; border-radius: 20px; padding: 10px 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🛒 Ecommerce</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="user_orders.php">My Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">My Cart</a></li>
        <?php if(isset($_SESSION['user_id'])) { ?>
          <li class="nav-item"><a class="btn btn-danger btn-sm ms-2" href="logout.php">Logout</a></li>
        <?php } else { ?>
          <li class="nav-item"><a class="btn btn-success btn-sm ms-2" href="login.php">Login</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Session Message -->
<div class="container mt-3">
  <?php if(isset($_SESSION['msg'])) { ?>
    <div class="alert alert-info text-center"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php } ?>
</div>

<!-- Filters -->
<div class="container mb-3">
  <form method="GET" action="products.php" class="row g-2">
    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <option value="Clothes" <?= ($category=="Clothes")?"selected":"" ?>>Clothes</option>
        <option value="Electronics" <?= ($category=="Electronics")?"selected":"" ?>>Electronics</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="size" class="form-select">
        <option value="">All Sizes</option>
        <option value="S" <?= ($size=="S")?"selected":"" ?>>S</option>
        <option value="M" <?= ($size=="M")?"selected":"" ?>>M</option>
        <option value="L" <?= ($size=="L")?"selected":"" ?>>L</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="color" class="form-select">
        <option value="">All Colors</option>
        <option value="Red" <?= ($color=="Red")?"selected":"" ?>>Red</option>
        <option value="Blue" <?= ($color=="Blue")?"selected":"" ?>>Blue</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
    </div>
  </form>
</div>

<!-- Search Bar -->
<div class="container mb-4">
  <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search products...">
</div>

<!-- Products -->
<div class="container">
  <h2 class="mb-4 text-center">✨ Available Products</h2>
  <div class="row" id="productList">
    <?php
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imgPath = "images/" . $row['image'];

        // Offer logic
        $originalPrice = $row['price'];
        $offerPrice = $originalPrice;
        $offerBadge = "";

        if (isset($row['offer']) && $row['offer'] > 0) {
            $offerPrice = $originalPrice - ($originalPrice * $row['offer'] / 100);
            $offerBadge = "<span style='position:absolute;top:10px;left:10px;background:#e74c3c;color:#fff;padding:5px 10px;border-radius:5px;font-weight:bold;font-size:0.9rem;'>{$row['offer']}% OFF</span>";
        }

        echo "
        <div class='col-md-4 col-lg-3 mb-4 product-item'>
          <div class='card product-card h-100 position-relative'>
            $offerBadge
            <img src='".htmlspecialchars($imgPath)."' alt='".htmlspecialchars($row['name'])."'>
            <div class='card-body text-center'>
              <h5 class='card-title'>".htmlspecialchars($row['name'])."</h5>
              <p class='card-text text-muted'>".htmlspecialchars($row['description'])."</p>
              <p>
                <span class='badge bg-info'>".htmlspecialchars($row['category'])."</span> 
                <span class='badge bg-secondary'>".htmlspecialchars($row['size'])."</span> 
                <span class='badge bg-warning text-dark'>".htmlspecialchars($row['color'])."</span>
              </p>
              <p><span class='badge stock-badge'>Stock: ".htmlspecialchars($row['stock'])."</span></p>
              <p>";
        if ($offerPrice < $originalPrice) {
            echo "<span style='text-decoration:line-through;color:#999;'>Rs. ".number_format($originalPrice,2)."</span> ";
            echo "<span style='color:#e74c3c;font-weight:bold;'>Rs. ".number_format($offerPrice,2)."</span>";
        } else {
            echo "Rs. ".number_format($originalPrice,2);
        }
        echo "</p>
              <a href='products.php?action=add&id=".$row['id']."' class='btn btn-success w-100 mb-1'>🛒 Add to Cart</a>
             <a href='wishlist_add.php?product_id=".$row['id']."' 
              class='btn mb-1 w-100' 
              style='background: #fff8dc; /* cream/light yellow */
              color: #8b4513; /* brown text */
              border: 1px solid #deb887; /* light brown border */
              font-weight: 500;'>
              💛 Add to Wishlist
                </a>

              <form action='buy_now.php' method='POST'>
                <input type='hidden' name='product_id' value='".$row['id']."'>
                <input type='hidden' name='quantity' value='1'>
                <button type='submit' class='btn btn-primary w-100'>⚡ Buy Now</button>
              </form>
            </div>
          </div>
        </div>";
    }
} else {
    echo "<p class='text-center'>No products found.</p>";
}

    ?>
  </div>
</div>

<footer>
  <div class="container text-center">
    <p>&copy; <?= date("Y"); ?> Ecommerce Website. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// 🔍 Live search
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let products = document.querySelectorAll(".product-item");
    products.forEach(function(product) {
        let text = product.innerText.toLowerCase();
        product.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
