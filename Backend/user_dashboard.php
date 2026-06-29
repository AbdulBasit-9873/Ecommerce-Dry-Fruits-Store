<?php
session_start();

// Agar user login nahi hai to wapas login page bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?> 👋</h2>
    <p>You are logged in as a <b><?php echo $_SESSION['role']; ?></b>.</p>

    <h3>Features:</h3>
    <ul>
        <li><a href="products.php">Browse Products</a></li>
        <li><a href="cart.php">My Cart</a></li>
        <li><a href="orders.php">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
