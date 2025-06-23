<?php 
include '../includes/header.php'; 
include '../includes/auth.php'; 
include '../includes/db.php'; 
if ($_SESSION['role'] !== 'admin') exit;

$users = mysqli_query($conn, "SELECT * FROM users");
?>

<div class="container mt-5">
    <h3>All Users</h3>
    <table class="table">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Seller</th></tr></thead>
        <tbody>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['is_seller'] ? 'Yes' : 'No' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if ($u['is_seller'] && !$u['seller_verified']): ?>
    <a href="verify_seller.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-success">Verify Seller</a>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>