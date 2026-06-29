<?php
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $gender = $_GET['gender'];
    $password = $_GET['Password'];
    $email = $_GET['email'];

    // Password ko hash karte hain (security ke liye)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL Query
    $sql = "INSERT INTO users (fname, lname, gender, password, email)
            VALUES ('$fname', '$lname', '$gender', '$hashed_password', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2 style='color:green;text-align:center;'>Sign Up Successful!</h2>";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
