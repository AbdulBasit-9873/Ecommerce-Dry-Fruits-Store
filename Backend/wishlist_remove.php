<?php
session_start();
include "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['product_id'] ?? 0;

if($product_id){
    $conn->query("DELETE FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");
}

header("Location: wishlist.php");
exit;
?>
