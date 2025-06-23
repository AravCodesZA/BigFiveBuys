<?php 
include '../Includes/header.php'; 
include '../Includes/auth.php'; 
include '../Includes/db.php'; 
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Get basic counts
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users"))['c'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products"))['c'];
$sellers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE is_seller = 1"))['c'];
$active_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products WHERE status = 'active'"))['c'];

// Get recent users
$recent_users = mysqli_query($conn, "SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");

// Get recent products - UPDATED to use 'title' instead of 'name'
$recent_products = mysqli_query($conn, "SELECT p.title, u.username, p.created_at 
                                      FROM products p 
                                      JOIN users u ON p.user_id = u.id 
                                      ORDER BY p.created_at DESC LIMIT 5");
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-chart-line me-2"></i>Admin Dashboard</h2>
        <div class="text-muted">Last updated: <?= date('F j, Y, g:i a') ?></div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Total Users</h6>
                            <h2 class="mb-0"><?= number_format($total_users) ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Sellers</h6>
                            <h2 class="mb-0"><?= number_format($sellers) ?></h2>
                        </div>
                        <i class="fas fa-store fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Total Products</h6>
                            <h2 class="mb-0"><?= number_format($total_products) ?></h2>
                        </div>
                        <i class="fas fa-boxes fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Active Products</h6>
                            <h2 class="mb-0"><?= number_format($active_products) ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Recent Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="users.php" class="btn btn-sm btn-outline-dark">View All Users</a>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Recent Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = mysqli_fetch_assoc($recent_products)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['title']) ?></td>
                                    <td><?= htmlspecialchars($product['username']) ?></td>
                                    <td><?= date('M j, Y', strtotime($product['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="product.php" class="btn btn-sm btn-outline-dark">View All Products</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="post_product.php" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="manage_users.php" class="btn btn-info w-100">
                        <i class="fas fa-users-cog me-2"></i>Manage Users
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="reports.php" class="btn btn-warning w-100">
                        <i class="fas fa-file-alt me-2"></i>View Reports
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="settings.php" class="btn btn-secondary w-100">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../Includes/footer.php'; ?>