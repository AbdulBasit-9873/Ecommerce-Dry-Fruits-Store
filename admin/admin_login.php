<?php
 
error_reporting(E_ALL);
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/db.php";

session_start();
include(__DIR__ . "/config/db.php"); // Correct relative path


$message = "";

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin user
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=? AND role='admin' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_name'] = $name;

            header("Location: admin_dashboard.php");
            exit;
        } else {
            $message = "❌ Invalid password!";
        }
    } else {
        $message = "❌ Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - Ecommerce</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(to right, #cce7ff, #99d0ff); font-family: 'Segoe UI', sans-serif; }
.login-container { max-width: 400px; margin: 100px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); }
.login-container h2 { text-align: center; margin-bottom: 25px; color: #333; }
.btn-custom { background-color: #0066cc; color: white; border-radius: 8px; }
.btn-custom:hover { background-color: #004a99; }
.alert-message { margin-bottom: 15px; }
.register-link { text-align: center; margin-top: 15px; }
</style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($message)) { ?>
        <div class="alert alert-danger text-center alert-message"><?= $message; ?></div>
    <?php } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>

    <div class="register-link">
        Don’t have an account? <a href="admin_register.php">Register Here</a>

    </div>
</div>

</body>
</html>
