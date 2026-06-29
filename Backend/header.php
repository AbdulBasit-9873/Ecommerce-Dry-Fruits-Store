<?php
session_start();
include "config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family:'Segoe UI',sans-serif;background:#f9f9f9;margin:0; }
    .header-top {background:#f1f1f1;padding:10px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;}
    .header-top .logo {font-size:1.8rem;font-weight:bold;color:#000;text-decoration:none;}
    .header-top .search-bar {flex:1;display:flex;margin:10px 20px;}
    .header-top .search-bar input{flex:1;border:1px solid #ccc;border-radius:5px 0 0 5px;padding:8px;}
    .header-top .search-bar button{border:none;background:#000;color:white;padding:8px 15px;border-radius:0 5px 5px 0;}
    .header-top .icons a{margin-left:15px;color:#333;text-decoration:none;font-weight:500;}
    .navbar-main {background:#000;padding:10px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;}
    .navbar-main ul{list-style:none;display:flex;margin:0;padding:0;}
    .navbar-main ul li{margin-left:20px;}
    .navbar-main ul li a{color:#ffc107;text-decoration:none;font-weight:500;}
    .navbar-main ul li a:hover{color:#fff;}
    .browse-dropdown .btn{background:#ffb004;color:#fff;font-weight:500;border:none;}
  </style>
</head>
<body>
<div class="header-top">
  <a href="index.php" class="logo">Smart Cart</a>
  <form class="search-bar" action="products.php" method="get">
    <input type="text" name="search" placeholder="Search products...">
    <button type="submit"><i class="fas fa-search"></i></button>
  </form>
  <div class="icons">
    <a href="account.php"><i class="fas fa-user"></i> <?php echo isset($_SESSION['user_id']) ? "My Account" : "Login/Register"; ?></a>
    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
    <a href="track_order.php"><i class="fas fa-truck"></i> Track Order</a>
  </div>
</div>

<div class="navbar-main">
  <div class="dropdown browse-dropdown">
    <button class="btn dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-bars"></i> Browse Categories</button>
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
