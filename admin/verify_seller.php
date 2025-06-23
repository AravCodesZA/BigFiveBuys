<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') exit;

$id = $_GET['id'];
mysqli_query($conn, "UPDATE users SET seller_verified = 1 WHERE id = '$id'");
header("Location: manage_users.php");