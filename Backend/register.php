<?php
session_start();
include "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check agar email already exist hai
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "❌ Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "✅ Registration successful! Please <a href='login.php'>Login</a>";
        } else {
            $message = "❌ Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Ecommerce Website</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f6d365, #fda085);
      font-family: 'Segoe UI', sans-serif;
    }
    .register-container {
      max-width: 450px;
      margin: 60px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.2);
      animation: fadeIn 1s ease-in-out;
    }
    .register-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .btn-custom {
      background-color: #28a745;
      color: white;
      border-radius: 8px;
    }
    .btn-custom:hover {
      background-color: #1e7e34;
    }
    header {
      text-align: center;
      padding: 20px;
      background: #333;
      color: white;
      font-size: 26px;
      font-weight: bold;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<header>Ecommerce Website 🛒</header>

<div class="register-container">
  <h2>Register</h2>

  <?php if (!empty($message)) { ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php } ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-custom w-100">Register</button>
  </form>

  <div class="text-center mt-3">
    Already have an account? <a href="login.php">Login Here</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
