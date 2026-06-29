<?php
session_start();
include 'config/db.php';

// ✅ Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Check if cart item id is provided
if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);

    // ✅ Delete only if it belongs to this user
    $sql = "DELETE FROM cart WHERE id='$cart_id' AND user_id='$user_id'";
    $conn->query($sql);
}

// ✅ Redirect back to cart page
header("Location: cart.php");
exit();
?>
