<?php
session_start();
include "config/db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Profile Image
$profileImage = !empty($user['image']) && file_exists("uploads/" . $user['image'])
    ? "uploads/" . htmlspecialchars($user['image'])
    : "assets/user-avatar.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Account - Smart Cart</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:'Segoe UI', sans-serif;
  background:#f4f6f9;
}

/* Main Layout */
.account-wrapper{
  display:flex;
  min-height:100vh;
  width:100%;
}

/* Sidebar */
.sidebar{
  width:260px;
  background:#0b132b;
  padding:25px 20px;
  color:#fff;
  display:flex;
  flex-direction:column;
  transition:0.3s;
}

.brand{
  font-size:1.5rem;
  font-weight:600;
  text-align:center;
  margin-bottom:30px;
  color:#90ee90;
}

.sidebar ul{
  list-style:none;
  padding:0;
  margin:0;
}

.sidebar ul li{
  margin-bottom:15px;
}

.sidebar ul li a{
  text-decoration:none;
  color:#ddd;
  padding:12px 15px;
  display:flex;
  align-items:center;
  gap:10px;
  border-radius:8px;
  font-weight:500;
  transition:0.3s;
}

.sidebar ul li a:hover,
.sidebar ul li a.active{
  background:#90ee90;
  color:#000;
}

/* Buttons */
.sidebar .btn-home{
  background:#2e8b57;
  color:#fff;
  border-radius:25px;
  padding:10px;
  text-align:center;
  text-decoration:none;
  margin-top:20px;
  transition:0.3s;
}
.sidebar .btn-home:hover{
  background:#256f46;
}

.logout-btn{
  margin-top:auto;
  background:#dc3545;
  color:#fff;
  padding:12px;
  border-radius:8px;
  text-align:center;
  text-decoration:none;
  font-weight:500;
}
.logout-btn:hover{
  background:#b02a37;
  color:#fff;
}

/* Main Content */
.main-content{
  flex:1;
  padding:40px;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* Profile Card */
.profile-card{
  background:#fff;
  border-radius:20px;
  padding:35px;
  width:100%;
  max-width:600px;
  text-align:center;
  box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.profile-card img{
  width:120px;
  height:120px;
  border-radius:50%;
  object-fit:cover;
  border:4px solid #90ee90;
  margin-bottom:15px;
}

.profile-card h3{
  font-weight:600;
  color:#2e8b57;
}

.profile-info{
  text-align:left;
  margin-top:20px;
}

.profile-info p{
  margin-bottom:10px;
  font-size:15px;
  color:#555;
}

.edit-btn{
  margin-top:20px;
  background:#90ee90;
  border:none;
  padding:10px 25px;
  border-radius:25px;
  font-weight:500;
  transition:0.3s;
}
.edit-btn:hover{
  background:#74d680;
}

/* 🔥 MOBILE RESPONSIVE FIX (IMPORTANT) */
@media (max-width: 991px){
  .account-wrapper{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
    flex-direction:row;
    align-items:center;
    justify-content:space-between;
    padding:15px;
  }

  .brand{
    font-size:1.2rem;
    margin-bottom:0;
  }

  .sidebar ul{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
  }

  .sidebar ul li{
    margin-bottom:0;
  }

  .sidebar ul li a{
    padding:8px 12px;
    font-size:14px;
  }

  .logout-btn{
    padding:8px 14px;
    font-size:14px;
  }

  .main-content{
    padding:20px;
  }

  .profile-card{
    padding:25px;
  }
}
</style>
</head>
<body>

<div class="account-wrapper">

  <!-- Sidebar -->
  <div class="sidebar">
      <div class="brand">
          <i class="fas fa-user-circle"></i> My Account
      </div>

      <ul>
          <li><a href="account.php" class="active"><i class="fas fa-user"></i> Profile</a></li>
          <li><a href="user_orders.php"><i class="fas fa-box"></i> Orders</a></li>
          <li><a href="edit_profile.php"><i class="fas fa-edit"></i> Edit</a></li>
          <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
          <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
      </ul>

      <a href="logout.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Logout
      </a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
      <div class="profile-card">
          <img src="<?php echo $profileImage; ?>" alt="Profile">
          <h3><?php echo htmlspecialchars($user['name']); ?></h3>

          <div class="profile-info">
              <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
              <p><i class="fas fa-calendar"></i> Member since: <?php echo htmlspecialchars($user['created_at']); ?></p>
              <p><i class="fas fa-map-marker-alt"></i> Pakistan</p>
          </div>

          <a href="edit_profile.php" class="btn edit-btn">
              <i class="fas fa-user-edit"></i> Edit Profile
          </a>
      </div>
  </div>

</div>

</body>
</html>
