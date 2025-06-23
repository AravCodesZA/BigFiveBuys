<?php
include 'includes/auth.php';
include 'includes/db.php';

$id = $_GET['id'];
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $id"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .invoice { border: 1px solid #ccc; padding: 30px; width: 500px; }
    </style>
</head>
<body>
<div class="invoice">
    <h2>Invoice</h2>
    <p><strong>Product:</strong> <?= $product['title'] ?></p>
    <p><strong>Price:</strong> R<?= $product['price'] ?></p>
    <p><strong>Description:</strong> <?= $product['description'] ?></p>
    <p><strong>Buyer:</strong> <?= $_SESSION['username'] ?></p>
    <p><strong>Date:</strong> <?= $product['created_at'] ?></p>
</div>
<script>window.print();</script>
</body>
</html>