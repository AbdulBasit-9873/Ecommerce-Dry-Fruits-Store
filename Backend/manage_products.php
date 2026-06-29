<?php
session_start();
include "config/db.php";

// Sirf admin ke liye
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Product add karna
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO products (name, description, price, stock) 
            VALUES ('$name', '$desc', '$price', '$stock')";
    if ($conn->query($sql) === TRUE) {
        $success = "✅ Product added successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f9; margin:0; padding:0; }
        .container { width: 80%; margin: 20px auto; }
        h2 { text-align: center; color: #333; }
        form {
            background: #fff; padding: 20px; border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        input, textarea, button {
            width: 100%; padding: 10px; margin: 8px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            background: green; color: white; font-weight: bold; cursor: pointer;
        }
        button:hover { background: darkgreen; }
        table {
            width: 100%; border-collapse: collapse; background:#fff;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
        }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background: #333; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .msg { text-align: center; font-weight: bold; margin:10px; }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Admin Panel - Manage Products</h2>

        <?php if(isset($success)) echo "<p class='msg' style='color:green;'>$success</p>"; ?>
        <?php if(isset($error)) echo "<p class='msg' style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <h3>Add New Product</h3>
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>

            <label>Stock:</label>
            <input type="number" name="stock" required>

            <button type="submit">Add Product</button>
        </form>

        <h3>All Products</h3>
        <table>
            <tr>
                <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Stock</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM products");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row['id']."</td>
                        <td>".$row['name']."</td>
                        <td>".$row['description']."</td>
                        <td>$".$row['price']."</td>
                        <td>".$row['stock']."</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
