<?php
session_start();
include "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;

            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Invalid password!";
        }
    } else {
        $message = "❌ Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Ecommerce Website</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: linear-gradient(to right, #a8edea, #fed6e3);
  font-family: 'Segoe UI', sans-serif;
  min-height: 100vh;
  margin: 0;
  display: flex;
  flex-direction: column;
}

/* Header (Same design, responsive) */
header {
  text-align: center;
  padding: 20px;
  background: #333;
  color: white;
  font-size: clamp(20px, 4vw, 26px);
  font-weight: bold;
}

/* Center container FIX (Main responsive fix) */
.main-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* Login Card (Same look, better scaling) */
.login-container {
  width: 100%;
  max-width: 420px;
  background: #fff;
  padding: 30px 25px;
  border-radius: 12px;
  box-shadow: 0px 6px 18px rgba(0,0,0,0.2);
  animation: slideDown 0.8s ease-in-out;
}

.login-container h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

/* Button (SAME color) */
.btn-custom {
  background-color: #007bff;
  color: white;
  border-radius: 8px;
  padding: 10px;
  font-weight: 500;
}

.btn-custom:hover {
  background-color: #0056b3;
  color: white;
}

/* Animation same */
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Mobile Optimization */
@media (max-width: 576px) {
  .login-container {
    padding: 25px 18px;
    border-radius: 10px;
  }

  header {
    padding: 15px;
  }
}
</style>
</head>
<body>

<header>Ecommerce Website 🛒</header>

<div class="main-wrapper">
  <div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($message)) { ?>
      <div class="alert alert-info text-center">
        <?php echo $message; ?>
      </div>
    <?php } ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>

    <div class="text-center mt-3">
      Don’t have an account? <a href="register.php">Register Here</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
