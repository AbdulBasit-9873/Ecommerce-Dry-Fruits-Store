<?php
session_start();
include 'config/db.php';

// ✅ Check user login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// ✅ If user not found
if(!$user){
    echo "<script>alert('User not found! Please log in again.');</script>";
    session_destroy();
    header("Location: login.php");
    exit();
}

// ✅ Update profile
if(isset($_POST['update'])){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Default old image
    $image_name = $user['profile_image'];

    // ✅ Handle image upload
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
        $target_dir = "uploads/profile/";
        if(!is_dir($target_dir)){
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];

        if(in_array($file_ext, $allowed_ext)){
            $new_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_name;

            if(move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)){
                // delete old image (except default)
                if(!empty($user['profile_image']) && file_exists($user['profile_image']) && $user['profile_image'] != 'uploads/profile/default.png'){
                    unlink($user['profile_image']);
                }
                $image_name = $target_file;
            }
        }
    }

    // ✅ Update in DB
    $update = "UPDATE users 
               SET fullname='$fullname', email='$email', phone='$phone', profile_image='$image_name' 
               WHERE id='$user_id'";
    $conn->query($update);

    header("Location: edit.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}
.container {
    max-width: 650px;
    margin: 50px auto;
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
    font-weight: 600;
}
.profile-pic {
    display: block;
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 50%;
    margin: 10px auto 20px;
    border: 3px solid #007bff;
}
.btn-save {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 10px;
    transition: 0.3s;
}
.btn-save:hover {
    background: #0056b3;
}
.alert-success {
    text-align: center;
    font-weight: 500;
}
</style>
</head>
<body>

<div class="container">
    <h2>👤 Edit Profile</h2>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Profile updated successfully!</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="text-center">
            <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'uploads/profile/default.png' ?>" 
                 alt="Profile" class="profile-pic" id="preview">
            <div class="mt-2">
                <input type="file" name="profile_image" class="form-control" accept="image/*" onchange="previewImage(event)">
            </div>
        </div>

        <div class="mt-4">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" 
                       value="<?= isset($user['fullname']) ? htmlspecialchars($user['fullname']) : '' ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" 
                       value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" 
                       value="<?= isset($user['phone']) ? htmlspecialchars($user['phone']) : '' ?>">
            </div>

            <button type="submit" name="update" class="btn btn-save w-100">💾 Save Changes</button>
        </div>
    </form>
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
