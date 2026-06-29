<?php
session_start();

function getDB()
{
    $host = '127.0.0.1';
    $db   = 'ecommerce';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // In production, don't echo errors. Log them instead.
        die('Database connection failed: ' . $e->getMessage());
    }
}

// Helper to escape output in templates
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "portfolio_db";

$conn = new mysqli($host, $user, $pass, $db);

// Set charset to avoid encoding issues
$conn->set_charset("utf8");

if ($conn->connect_error) {
  die("Database Connection Failed: " . $conn->connect_error);
}
?>
