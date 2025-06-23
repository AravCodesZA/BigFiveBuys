<?php
include '../includes/header.php';
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') exit;

$id = $_GET['id'];
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['description']);
    $price = $_POST['price'];

    mysqli_query($conn, "UPDATE products SET title='$title', description='$desc', price='$price' WHERE id='$id'");
    header("Location: manage_products.php");
}
?>

<div class="container mt-5">
    <h3>Edit Product</h3>
    <form method="post">
        <input name="title" class="form-control mb-2" value="<?= $product['title'] ?>" required>
        <textarea name="description" class="form-control mb-2"><?= $product['description'] ?></textarea>
        <input name="price" type="number" step="0.01" class="form-control mb-2" value="<?= $product['price'] ?>" required>
        <button class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>