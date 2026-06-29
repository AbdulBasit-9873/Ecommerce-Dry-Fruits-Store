<?php
session_start();
include "config/db.php";

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id > 0) {
    // ✅ Check if product already in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        // ✅ Insert into cart
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();

        // 🧹 Optional: remove from wishlist
        $conn->query("DELETE FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");

        $_SESSION['msg'] = "🛒 Product added to cart successfully!";
    } else {
        $_SESSION['msg'] = "⚠️ Product is already in your cart!";
    }

    $check->close();
}

// ✅ Redirect directly to cart (not wishlist)
header("Location: cart.php");
exit;
?>
