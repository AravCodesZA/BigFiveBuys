<?php
$host = "sql110.infinityfree.com";
$user = "if0_39224338";
$pass = "GeQJEvaWiKBGO"; // Default for XAMPP
$dbname = "if0_39224338_bigfivebuys";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>