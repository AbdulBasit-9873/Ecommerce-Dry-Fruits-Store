<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$icon = "";

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Check if already in wishlist
    $check = $conn->query("SELECT * FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ('$user_id', '$product_id')");
        $message = "✅ Added to wishlist successfully!";
        $icon = "success";
    } else {
        $message = "💖 Already in your wishlist!";
        $icon = "info";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Wishlist Update</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
    background: linear-gradient(135deg, #141e30, #243b55);
    color: white;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
}
</style>
</head>
<body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: "<?= addslashes($message) ?>",
        icon: "<?= $icon ?>",
        showConfirmButton: false,
        timer: 1500,
        background: "rgba(30,30,30,0.9)",
        color: "#fff",
    }).then(() => {
        window.location.href = "wishlist.php";
    });
});
</script>

</body>
</html>
