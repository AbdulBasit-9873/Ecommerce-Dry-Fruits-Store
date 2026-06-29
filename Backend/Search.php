<?php
session_start();
include "config/db.php"; // <-- make sure this file connects to your database
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results - Smart Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color:#f9f9f9; font-family: 'Segoe UI', sans-serif; }
    .search-title {
      background: #000;
      color: #d4af37;
      padding: 15px;
      text-align: center;
      margin-bottom: 20px;
    }
    .card {
      transition: 0.3s;
      border: none;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .not-found {
      text-align: center;
      font-size: 20px;
      color: #c0392b;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<div class="search-title">
  <h3>Search Results</h3>
</div>

<div class="container my-5">
  <div class="row g-4">

<?php
if(isset($_GET['search']) && $_GET['search'] != '') {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "SELECT * FROM products WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="col-md-3">
              <div class="card">
                <img src="images/'.$row['image'].'" class="card-img-top" alt="'.$row['name'].'">
                <div class="card-body text-center">
                  <h6 class="card-title">'.$row['name'].'</h6>
                  <p>Rs. '.$row['price'].'</p>
                  <a href="cart_add.php?id='.$row['id'].'" class="btn btn-warning w-100">Add to Cart</a>
                </div>
              </div>
            </div>';
        }
    } else {
        echo '<div class="not-found">No products found for "<b>'.htmlspecialchars($search).'</b>"</div>';
    }
} else {
    echo '<div class="not-found">Please enter a search term.</div>';
}
?>

  </div>
</div>

</body>
</html>
