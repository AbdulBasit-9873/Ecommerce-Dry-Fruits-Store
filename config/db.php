<?php
$host = "sql312.infinityfree.com";   // InfinityFree ka host check karo (control panel me SQL details)
$user = "if0_41154718";
$password = "smartcartt123";
$db = "if0_41154718_ecommerce_db";

// Correct mysqli connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
