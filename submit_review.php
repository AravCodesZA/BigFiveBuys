<?php
require_once 'Includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || empty($_POST['product_id']) || empty($_POST['rating']) || empty($_POST['comment'])) {
    header("Location: product.php?id=" . intval($_POST['product_id']));
    exit;
}

$product_id = intval($_POST['product_id']);
$user_id = $_SESSION['user_id'];
$rating = intval($_POST['rating']);
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

mysqli_query($conn, "INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES ($product_id, $user_id, $rating, '$comment', NOW())");

header("Location: product.php?id=$product_id");
exit;
