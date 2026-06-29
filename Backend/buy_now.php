<?php
session_start();
include 'config/db.php';

// -----------------------------
// 1. User Login Check
// -----------------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// -----------------------------
// 2. Get Product ID & Quantity
// -----------------------------
$product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? $_SESSION['buy_now_product']['product_id'] ?? null;
$quantity   = $_POST['quantity']   ?? $_GET['quantity']   ?? $_SESSION['buy_now_product']['quantity'] ?? 1;

if (!$product_id) {
    echo "<script>alert('No product selected'); window.location='products.php';</script>";
    exit();
}

// Save product in session for POST/refresh protection
$_SESSION['buy_now_product'] = ['product_id' => $product_id, 'quantity' => $quantity];

// -----------------------------
// 3. Fetch Product Info
// -----------------------------
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Product not found'); window.location='products.php';</script>";
    exit();
}

$product = $result->fetch_assoc();

// Prices
$total_price = $product['price'] * $quantity;
$shipping = 200;
$grand_total = $total_price + $shipping;

// CSRF Token
if (empty($_SESSION["order_token"])) {
    $_SESSION["order_token"] = bin2hex(random_bytes(20));
}
$token = $_SESSION["order_token"];

// -----------------------------
// 4. Place Order
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION["order_token"]) {
        echo "<script>alert('Order already submitted!'); window.location='user_orders.php';</script>";
        exit();
    }
    unset($_SESSION["order_token"]);

    $fullname       = $_POST['fullname'];
    $address        = $_POST['address'];
    $phone          = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $order_date     = date("Y-m-d H:i:s");

    // Insert Order
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id, fullname, address, phone, payment_method, total_amount, order_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("issssds", $user_id, $fullname, $address, $phone, $payment_method, $grand_total, $order_date);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert Order Item
        $item_total = $product['price'] * $quantity;
        $stmt_item = $conn->prepare("INSERT INTO order_items 
            (order_id, product_id, quantity, price, total)
            VALUES (?, ?, ?, ?, ?)");
        $stmt_item->bind_param("iiidd", $order_id, $product_id, $quantity, $product['price'], $item_total);
        $stmt_item->execute();

        // Update Stock
        $update = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update->bind_param("ii", $quantity, $product_id);
        $update->execute();

        unset($_SESSION['buy_now_product']);
        header("Location: checkout_success.php?order_id={$order_id}");
        exit();
    } else {
        echo "<script>alert('Order Failed');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Buy Now - <?= htmlspecialchars($product['name']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f8f9fa;
}
.navbar-custom {
    background: #343a40;
}
.navbar-custom .navbar-brand {
    color: #fff;
    font-weight: 600;
    font-size: 1.5rem;
}
.navbar-custom .btn-home {
    padding: 10px 25px;
    font-size: 16px;
    font-weight: 600;
}
.card-product {
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 20px;
    margin-top: 20px;
}
.card-product img {
    border-radius: 15px;
    object-fit: cover;
}
h4.total-price {
    color: #2e8b57;
    font-weight: 600;
}
.btn-place {
    background: #2e8b57;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 12px 30px;
}
.btn-place:hover {
    background: #27694b;
}
@media(max-width:768px){
    .row-flex {flex-direction:column;}
    .btn-place {width:100%;}
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom py-3">
  <div class="container d-flex justify-content-between align-items-center">
    <span class="navbar-brand">⚡ Buy Now</span>
    <a href="products.php" class="btn btn-success btn-home">🏠 Back to products</a>
  </div>
</nav>

<div class="container">
    <div class="card card-product">
        <div class="row row-flex align-items-center">
            <div class="col-md-5 text-center">
                <?php
                $imagePath = 'images/' . $product['image'];
                if (empty($product['image']) || !file_exists($imagePath)) {
                    $imagePath = 'assets/no-image.png';
                }
                ?>
                <img src="<?= htmlspecialchars($imagePath); ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-7">
                <h3><?= htmlspecialchars($product['name']); ?></h3>
                <p><strong>Price:</strong> Rs <?= number_format($product['price']); ?></p>
                <p><strong>Quantity:</strong> <?= $quantity; ?></p>
                <p><strong>Total:</strong> Rs <?= number_format($total_price); ?></p>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="token" value="<?= $token; ?>">

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="COD">Cash on Delivery</option>
                            <option value="Easypaisa">Easypaisa</option>
                        </select>
                    </div>

                    <h4 class="total-price">Grand Total: Rs <?= number_format($grand_total); ?></h4>

                    <button type="submit" name="place_order" class="btn btn-place mt-3 w-100">
                        Confirm & Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
