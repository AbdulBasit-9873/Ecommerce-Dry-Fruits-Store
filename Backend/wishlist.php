<?php
session_start();
include "config/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items with product info
$sql = "SELECT p.id, p.name, p.price, p.image 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wishlist - Smart Cart</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body {
    background: #f5f7fa;
    font-family: 'Poppins', sans-serif;
    color: #333;
}

/* Header */
header {
    background: #000;
    color: #fff;
    padding: 20px;
    text-align: center;
    font-size: 1.8rem;
    font-weight: 700;
    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
}

/* Wishlist Section */
.container {
    max-width: 1150px;
    margin: 60px auto;
}

h2 {
    font-weight: 700;
    color: #0078ff;
    text-align: center;
    margin-bottom: 35px;
    text-shadow: 0 0 8px rgba(0,120,255,0.2);
}

/* Product Card */
.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    transition: 0.3s;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}
.card img {
    height: 220px;
    object-fit: cover;
    width: 100%;
    border-bottom: 1px solid #eee;
}
.card-body {
    text-align: center;
    padding: 18px;
}
.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #004aad;
}
.price {
    color: #00b894;
    font-size: 1rem;
    font-weight: 500;
    margin: 6px 0 15px 0;
}

/* Buttons */
.btn {
    border-radius: 50px;
    padding: 7px 18px;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-cart {
    background: linear-gradient(90deg, #00b894, #00cec9);
    color: #fff;
}
.btn-cart:hover {
    background: linear-gradient(90deg, #00cec9, #00b894);
}
.btn-remove {
    background: linear-gradient(90deg, #ff6b6b, #d63031);
    color: #fff;
}
.btn-remove:hover {
    background: linear-gradient(90deg, #d63031, #ff6b6b);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 100px 20px;
}
.empty-state h4 {
    color: #004aad;
    font-weight: 600;
}
.empty-state .btn-primary {
    border-radius: 50px;
    background: linear-gradient(90deg, #0078ff, #00b894);
    border: none;
    font-weight: 500;
}

/* Toast Notification */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 2000;
}
.toast {
    background: rgba(0,0,0,0.85);
    color: white;
    border-radius: 10px;
    padding: 12px 18px;
    animation: fadeInOut 3s ease forwards;
}
@keyframes fadeInOut {
    0% {opacity:0; transform:translateY(-20px);}
    10% {opacity:1; transform:translateY(0);}
    90% {opacity:1;}
    100% {opacity:0; transform:translateY(-20px);}
}
</style>
</head>
<body>

<header> My Wishlist</header>

<!-- Back to Home Button -->
<div class="back-home-container">
    <a href="index.php" class="back-home-btn">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>

<style>
/* Container to center the button */
.back-home-container {
    text-align: center;
    margin-top: 40px;
}

/* Button Styling */
.back-home-btn {
    display: inline-block;
    background: linear-gradient(90deg, #0078ff, #00b894);
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.back-home-btn i {
    margin-right: 8px; /* Space between icon and text */
}

/* Hover Effect */
.back-home-btn:hover {
    background: linear-gradient(90deg, #00b894, #0078ff);
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
}
</style>


<div class="container">
<?php if($result->num_rows > 0): ?>
    <div class="row g-4">
        <?php while($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card">
                <!-- Corrected Image Path -->
                <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">

                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="price">Rs <?= number_format($row['price'], 2) ?></p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="add_to_cart.php?product_id=<?= $row['id'] ?>" class="btn btn-cart btn-sm">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </a>
                        <a href="wishlist_remove.php?product_id=<?= $row['id'] ?>" class="btn btn-remove btn-sm">
                            <i class="fas fa-trash-alt"></i> Remove
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <h4>Your wishlist is empty </h4>
        <a href="products.php" class="btn btn-primary mt-3"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
    </div>
<?php endif; ?>
</div>

<!-- Toast -->
<?php if(isset($_SESSION['msg'])): ?>
<div class="toast-container">
    <div class="toast"><?= $_SESSION['msg']; ?></div>
</div>
<?php unset($_SESSION['msg']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
