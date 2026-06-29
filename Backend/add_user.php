<?php
session_start();
include(__DIR__ . "/../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Encrypt password
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = $_POST['role'] ?? 'customer';
    
    // Image upload
    $profile_image = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $profile_image = time() . "_" . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_dir . $profile_image);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, role, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $phone, $address, $role, $profile_image);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>User added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add User - Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New User</h2>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Profile Image</label>
            <input type="file" name="profile_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Add User</button>
        <a href="manage_users.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
