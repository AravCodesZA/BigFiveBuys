<?php 
include '../Includes/header.php'; 
include '../Includes/auth.php'; 
include '../Includes/db.php'; 
if ($_SESSION['role'] !== 'admin') exit;

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = '$id'");
    header("Location: manage_products.php");
}

$products = mysqli_query($conn, "
    SELECT products.*, users.username 
    FROM products JOIN users ON products.user_id = users.id
");
?>

<div class="container mt-5">
    <h3>All Products</h3>
    <table class="table">
        <thead><tr><th>ID</th><th>Title</th><th>Seller</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['title'] ?></td>
                <td><?= $p['username'] ?></td>
                <td>R<?= $p['price'] ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete product?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../Includes/footer.php'; ?>