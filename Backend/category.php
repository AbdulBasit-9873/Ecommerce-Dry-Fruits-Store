<?php
include "config/db.php";

// Check if category is in URL
if (!isset($_GET['category'])) {
    echo "<h2>No category selected.</h2>";
    exit;
}

$category = mysqli_real_escape_string($conn, $_GET['category']);

// Fetch Category Name (optional)
echo "<h2>$category</h2>";

// Fetch products by category
$sql = "SELECT * FROM products WHERE category = '$category'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='product-list'>";
    while ($row = $result->fetch_assoc()) {

        // Image setup
        $imagePath = "uploads/" . $row['image'];
        if (!file_exists($imagePath) || empty($row['image'])) {
            $imagePath = "assets/no-image.png";
        }

        echo "
        <div class='product-box'>
            <img src='$imagePath' width='150'>
            <h4>{$row['name']}</h4>
            <p>Price: {$row['price']}</p>
            <a href='product.php?id={$row['id']}'>View Details</a>
        </div>
        ";
    }
    echo "</div>";
} else {
    echo "<p>No products found in this category.</p>";
}
?>
