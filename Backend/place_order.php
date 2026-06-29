<?php
session_start();
include 'config/db.php';

// Step 1: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 2: Validate required fields
$fields = ['fullname', 'address', 'phone', 'payment_method'];
foreach ($fields as $field) {
    if (empty($_POST[$field])) {
        echo "<script>alert('⚠️ Please fill all required fields: $field'); window.history.back();</script>";
        exit();
    }
}

$fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
$address = mysqli_real_escape_string($conn, trim($_POST['address']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$payment_method = mysqli_real_escape_string($conn, trim($_POST['payment_method']));
$order_date = date("Y-m-d H:i:s");

// --------------------------------------------
// FIXED LOGIC HERE ↓
// --------------------------------------------

// Step 3: If user did NOT select checkboxes → select ALL their cart items automatically
if (empty($_POST['selected_cart']) || !is_array($_POST['selected_cart'])) {

    // Fetch all cart items for this user
    $get_all_cart = $conn->query("SELECT id FROM cart WHERE user_id = '$user_id'");
    
    if ($get_all_cart->num_rows == 0) {
        echo "<script>alert('❌ Your cart is empty.'); window.location='cart.php';</script>";
        exit();
    }

    $selected_carts = [];
    while ($row = $get_all_cart->fetch_assoc()) {
        $selected_carts[] = $row['id'];
    }

} else {
    // If user actually selected checkboxes → use them
    $selected_carts = array_map('intval', $_POST['selected_cart']);
}

$cart_ids_str = implode(',', $selected_carts);
$total_amount = 0;

// Step 4: Fetch selected cart items safely
$query = "
    SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price, p.stock 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.id IN ($cart_ids_str) AND c.user_id = '$user_id'
";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
    echo "<script>alert('❌ No items found in your cart!'); window.location='cart.php';</script>";
    exit();
}

// Step 5: Stock check + total amount
while ($row = $result->fetch_assoc()) {
    if ($row['stock'] < $row['quantity']) {
        echo "<script>alert('⚠️ Not enough stock for {$row['name']}. Available: {$row['stock']}'); window.location='cart.php';</script>";
        exit();
    }
    $total_amount += $row['price'] * $row['quantity'];
}

// Step 6: Create order
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, fullname, address, phone, payment_method, total_amount, order_date, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
");
$stmt->bind_param("issssds", $user_id, $fullname, $address, $phone, $payment_method, $total_amount, $order_date);
$stmt->execute();
$order_id = $stmt->insert_id;

// Step 7: Insert into order_items + update stock + delete cart
$result->data_seek(0);

while ($item = $result->fetch_assoc()) {
    $total_item_price = $item['price'] * $item['quantity'];

    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price, total)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt_item->bind_param("iiidd", $order_id, $item['product_id'], $item['quantity'], $item['price'], $total_item_price);
    $stmt_item->execute();

    // Update stock
    $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
    $update_stock->execute();

    // Remove from cart
    $del_cart = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $del_cart->bind_param("i", $item['cart_id']);
    $del_cart->execute();
}

// Step 8: Redirect
$_SESSION['order_success'] = "✅ Your order has been placed successfully!";
header("Location: checkout_success.php?order_id=" . $order_id);
exit();
?>
 