<?php
session_start();
session_unset();
session_destroy();

// Redirect with absolute path from root of project
header("Location: /ecommerce_project/admin/Admin_login.php");
exit;
?>
