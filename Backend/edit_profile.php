<?php
session_start();
include "config/db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$success = "";
$error = "";

// Update profile logic
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = trim($_POST['password']);
    $image_name = $user['image']; // current image

    // Image upload logic
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate unique filename
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Optional: delete old image if exists and not default
            if (!empty($user['image']) && file_exists("uploads/" . $user['image'])) {
                @unlink("uploads/" . $user['image']);
            }
        } else {
            $error = "❌ Error uploading image!";
        }
    }

    // Password update check
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET name='$name', email='$email', password='$hashed', image='$image_name' WHERE id='$user_id'";
    } else {
        $update = "UPDATE users SET name='$name', email='$email', image='$image_name' WHERE id='$user_id'";
    }

    if ($conn->query($update)) {
        $success = "✅ Profile updated successfully!";
        // Refresh user data after update
        $result = $conn->query("SELECT * FROM users WHERE id='$user_id'");
        $user = $result->fetch_assoc();
    } else {
        $error = "❌ Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile - Smart Cart</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
  margin:0;
  font-family:'Poppins',sans-serif;
  background:#f1f1f1;
  color:#333;
}

/* Layout */
.account-container {
  display:flex;
  min-height:100vh;
}

/* Sidebar */
.sidebar {
  width:260px;
  background:#000;
  padding:30px 20px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  box-shadow:2px 0 10px rgba(0,0,0,0.2);
}
.sidebar .brand {
  font-size:1.8rem;
  font-weight:600;
  color:#90ee90;
  text-align:center;
  margin-bottom:40px;
}
.sidebar ul {
  list-style:none;
  padding:0;
}
.sidebar ul li {
  margin-bottom:20px;
}
.sidebar ul li a {
  color:#ccc;
  text-decoration:none;
  font-weight:500;
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 15px;
  border-radius:8px;
  transition:all 0.3s;
}
.sidebar ul li a:hover, .sidebar ul li a.active {
  background:#90ee90;
  color:#000;
}
.sidebar .logout a {
  color:#fff;
  background:#dc3545;
  padding:10px 15px;
  border-radius:8px;
  text-decoration:none;
  text-align:center;
  display:block;
  transition:0.3s;
}
.sidebar .logout a:hover {
  background:#b02a37;
}

/* Main Content */
.main-content {
  flex:1;
  padding:50px;
  background:linear-gradient(120deg,#f6f9fc,#e9f5f0);
}

/* Profile Card */
.form-card {
  background:rgba(255,255,255,0.95);
  border-radius:20px;
  padding:40px;
  box-shadow:0 8px 30px rgba(0,0,0,0.1);
  max-width:750px;
  margin:auto;
  backdrop-filter:blur(10px);
}
.form-card h3 {
  color:#2e8b57;
  font-weight:600;
  text-align:center;
  margin-bottom:25px;
}

/* Profile Picture */
.profile-pic {
  text-align:center;
  margin-bottom:25px;
  position:relative;
}
.profile-pic img {
  width:130px;
  height:130px;
  border-radius:50%;
  object-fit:cover;
  border:4px solid #90ee90;
  transition:0.4s;
}
.profile-pic img:hover {
  transform:scale(1.05);
  box-shadow:0 0 10px rgba(0,0,0,0.2);
}
.profile-pic label {
  display:inline-block;
  background:#90ee90;
  color:#000;
  padding:8px 20px;
  border-radius:20px;
  cursor:pointer;
  transition:0.3s;
  font-weight:500;
}
.profile-pic label:hover {
  background:#74d680;
}
.profile-pic input { display:none; }

/* Buttons */
.btn-custom {
  background:#90ee90;
  color:#000;
  border:none;
  border-radius:25px;
  padding:10px 25px;
  font-weight:500;
  transition:0.3s;
}
.btn-custom:hover {
  background:#74d680;
}

/* Alerts */
.alert {
  max-width:700px;
  margin:20px auto;
}

/* Responsive */
@media(max-width:768px){
  .account-container{flex-direction:column;}
  .sidebar{width:100%;flex-direction:row;justify-content:space-around;}
  .sidebar ul{display:flex;gap:10px;}
  .main-content{padding:25px;}
}
</style>
</head>
<body>

<div class="account-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <div class="brand"><i class="fas fa-user-circle"></i> My Account</div>
      <ul>
        <li><a href="account.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="user_orders.php"><i class="fas fa-box"></i> My Orders</a></li>
        <li><a href="edit_profile.php" class="active"><i class="fas fa-edit"></i> Edit Profile</a></li>
        <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
      </ul>
    </div>
    <div class="logout">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <?php if($success): ?>
      <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php elseif($error): ?>
      <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-card">
      <h3>Edit Profile</h3>
      <form method="post" enctype="multipart/form-data">

        <div class="profile-pic">
          <img id="preview" 
               src="<?php echo !empty($user['image']) ? 'uploads/'.$user['image'] : 'assets/user-avatar.png'; ?>" 
               alt="Profile Picture">
          <br><br>
          <label for="image"><i class="fas fa-camera"></i> Change Photo</label>
          <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
        </div>

        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">New Password (optional)</label>
          <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
        </div>

        <div class="text-center">
          <button type="submit" name="update" class="btn btn-custom">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function previewImage(event){
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById('preview').src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
