<?php
// Show errors if something goes wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session and include database
session_start();
include_once '../Includes/db.php';

date_default_timezone_set('Africa/Johannesburg');

// Restrict access: only admin can view this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=AdminAccessOnly");
    exit;
}

// Get dashboard statistics
$stats = [
    'total_users' => 0,
    'new_users_today' => 0,
    'total_products' => 0,
    'pending_products' => 0,
    'total_orders' => 0,
    'pending_orders' => 0,
    'revenue' => 0
];

// Get user statistics
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($result) $stats['total_users'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
if ($result) $stats['new_users_today'] = $result->fetch_assoc()['total'];

// Get product statistics
$result = $conn->query("SELECT COUNT(*) as total FROM products");
if ($result) $stats['total_products'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE status = 'pending'");
if ($result) $stats['pending_products'] = $result->fetch_assoc()['total'];

// Get order statistics
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
if ($result) $stats['total_orders'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
if ($result) $stats['pending_orders'] = $result->fetch_assoc()['total'];

// Get revenue
$result = $conn->query("SELECT SUM(final_amount) as total FROM orders WHERE status = 'completed'");
if ($result) $stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | BigFiveBuys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap and other styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4e73df;
      --success: #1cc88a;
      --info: #36b9cc;
      --warning: #f6c23e;
      --danger: #e74a3b;
      --dark: #5a5c69;
    }
    
    .admin-header {
      background: linear-gradient(135deg, #2c3e50 0%, #4e73df 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      border-left: 0.25rem solid;
      border-radius: 0.35rem;
      transition: all 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stat-card.primary { border-color: var(--primary); }
    .stat-card.success { border-color: var(--success); }
    .stat-card.info { border-color: var(--info); }
    .stat-card.warning { border-color: var(--warning); }
    .stat-card.danger { border-color: var(--danger); }
    
    .stat-icon {
      font-size: 2rem;
      opacity: 0.3;
    }
    
    .quick-action-card {
      transition: all 0.3s;
      height: 100%;
    }
    
    .quick-action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .recent-activity-item {
      border-left: 3px solid var(--primary);
      position: relative;
      padding-left: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .recent-activity-item::before {
      content: '';
      position: absolute;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: var(--primary);
      left: -6px;
      top: 0;
    }
  </style>
</head>
<body class="bg-light">

<?php include_once '../Includes/header.php'; ?>

<!-- Admin Header -->
<div class="admin-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1><i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard</h1>
        <p class="mb-0">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
      </div>
      <div class="text-end">
        <p class="mb-0"><small>Last login: <?= date('M j, Y H:i') ?></small></p>
        <a href="../logout.php" class="btn btn-sm btn-outline-light">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container mb-5">
  <!-- Stats Cards -->
  <div class="row mb-4 g-4">
    <div class="col-xl-3 col-md-6">
      <div class="stat-card primary card bg-white shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Total Users</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_users']) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-users stat-icon text-primary"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
      <div class="stat-card success card bg-white shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                New Users Today</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['new_users_today']) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user-plus stat-icon text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
      <div class="stat-card info card bg-white shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                Total Products</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_products']) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-boxes stat-icon text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
      <div class="stat-card warning card bg-white shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                Pending Approvals</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['pending_products']) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clock stat-icon text-warning"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Row -->
  <div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="quick-action-card card border-left-primary h-100">
                <div class="card-body text-center">
                  <i class="fas fa-users fa-2x text-primary mb-3"></i>
                  <h5 class="card-title">User Management</h5>
                  <p class="card-text">View, edit, or suspend user accounts</p>
                  <a href="manage_users.php" class="btn btn-primary btn-sm">Manage Users</a>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="quick-action-card card border-left-success h-100">
                <div class="card-body text-center">
                  <i class="fas fa-box-open fa-2x text-success mb-3"></i>
                  <h5 class="card-title">Product Listings</h5>
                  <p class="card-text">Manage all marketplace listings</p>
                  <a href="view_product.php" class="btn btn-success btn-sm">View Listings</a>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="quick-action-card card border-left-info h-100">
                <div class="card-body text-center">
                  <i class="fas fa-chart-line fa-2x text-info mb-3"></i>
                  <h5 class="card-title">Reports & Analytics</h5>
                  <p class="card-text">Generate sales and user reports</p>
                  <a href="reports.php" class="btn btn-info btn-sm">View Reports</a>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 mb-3">
              <div class="quick-action-card card border-left-warning h-100">
                <div class="card-body text-center">
                  <i class="fas fa-store fa-2x text-warning mb-3"></i>
                  <h5 class="card-title">Seller Approvals</h5>
                  <p class="card-text">Review new seller applications</p>
                  <a href="sellers.php" class="btn btn-warning btn-sm">Review Sellers</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
          <a href="activity_log.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
          <?php
          // Get recent activity (example data - replace with actual query)
          $activities = [
            ['time' => '1 min ago', 'action' => 'Product "Bicycle" was approved', 'icon' => 'check-circle'],
            ['time' => '3 mins ago', 'action' => 'New user registered: Mr Tlou', 'icon' => 'user-plus'],
            ['time' => '1 hour ago', 'action' => 'Order #1234 was completed', 'icon' => 'shopping-bag'],
            ['time' => '2 hours ago', 'action' => 'Seller application from Seller', 'icon' => 'store'],
            ['time' => '3 hours ago', 'action' => 'System maintenance performed', 'icon' => 'tools']
          ];
          
          foreach ($activities as $activity): ?>
            <div class="recent-activity-item">
              <div class="small text-gray-500"><?= $activity['time'] ?></div>
              <div class="mb-1">
                <i class="fas fa-<?= $activity['icon'] ?> me-1"></i>
                <?= $activity['action'] ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue and Orders -->
  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Revenue Summary</h6>
        </div>
        <div class="card-body">
          <div class="text-center">
            <h4>R<?= number_format($stats['revenue'], 2) ?></h4>
            <p class="mb-4">Total platform revenue</p>
            <div class="progress mb-4">
              <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="text-success">
                  <i class="fas fa-arrow-up"></i> 12%
                </div>
                <div class="small">This month</div>
              </div>
              <div class="col-6">
                <div class="text-danger">
                  <i class="fas fa-arrow-down"></i> 3%
                </div>
                <div class="small">Last month</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Customer</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#1234</td>
                  <td>John Doe</td>
                  <td>R1,299.00</td>
                  <td><span class="badge bg-success">Completed</span></td>
                </tr>
                <tr>
                  <td>#1233</td>
                  <td>Jane Smith</td>
                  <td>R2,499.00</td>
                  <td><span class="badge bg-warning">Pending</span></td>
                </tr>
                <tr>
                  <td>#1232</td>
                  <td>Mike Johnson</td>
                  <td>R799.00</td>
                  <td><span class="badge bg-info">Shipped</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <a href="orders.php" class="btn btn-sm btn-outline-primary mt-2">View All Orders</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once '../Includes/footer.php'; ?>

<!-- Bootstrap core JavaScript-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>