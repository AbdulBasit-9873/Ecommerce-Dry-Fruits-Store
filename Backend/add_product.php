<?php
session_start();
include(__DIR__ . "/../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: Admin_login.php");
    exit;
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = trim($_POST['category']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $size = trim($_POST['size']);
    $color = trim($_POST['color']);
    $unit = trim($_POST['unit']);
    $offer = intval($_POST['offer']); // ✅ Offer %

    // Handle image upload
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $stmt = $conn->prepare("INSERT INTO products (category, name, description, price, stock, size, color, unit, image, offer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdissssi", $category, $name, $description, $price, $stock, $size, $color, $unit, $image_name, $offer);

    if ($stmt->execute()) {
        $message = "✅ Product added successfully!";
    } else {
        $message = "❌ Error adding product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product - Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Product</h2>
    <?php if($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control">
        </div>
        <div class="mb-3">
            <label>Size</label>
            <input type="text" name="size" class="form-control">
        </div>
        <div class="mb-3">
            <label>Color</label>
            <input type="text" name="color" class="form-control">
        </div>
        <div class="mb-3">
            <label>Unit</label>
            <input type="text" name="unit" class="form-control">
        </div>
        <div class="mb-3">
            <label>Offer %</label>
            <input type="number" name="offer" class="form-control" min="0" max="100" value="0">
            <small class="text-muted">Set 0 if no offer.</small>
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
