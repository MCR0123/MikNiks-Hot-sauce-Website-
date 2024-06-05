<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "admin_users";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
