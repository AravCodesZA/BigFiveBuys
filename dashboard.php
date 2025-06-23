<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and check login
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to DB
include_once("Includes/db.php");

date_default_timezone_set('Africa/Johannesburg');

// Initialize variables
$unread_messages = 0;
$active_products = 0;
$pending_orders = 0;
$completed_orders = 0;
$recent_orders = [];
$recent_messages = [];

// Get user data
$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$is_seller = $_SESSION['is_seller'] ?? true;
$seller_verified = $_SESSION['seller_verified'] ?? true;
$role = $_SESSION['role'] ?? 'user';

// Get dashboard stats
$unread_result = mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM messages WHERE receiver_id = '$user_id' AND is_read = 0");
if ($unread_result) {
    $unread_data = mysqli_fetch_assoc($unread_result);
    $unread_messages = (int)$unread_data['count'];
}

if ($is_seller) {
    $products_result = mysqli_query($conn, 
        "SELECT COUNT(*) as count FROM products WHERE user_id = '$user_id' AND status = 'active'");
    if ($products_result) {
        $products_data = mysqli_fetch_assoc($products_result);
        $active_products = (int)$products_data['count'];
    }
    
    $pending_result = mysqli_query($conn, 
        "SELECT COUNT(*) as count FROM orders o 
         JOIN order_items oi ON o.id = oi.order_id 
         JOIN products p ON oi.product_id = p.id 
         WHERE p.user_id = '$user_id' AND o.status = 'pending'");
    if ($pending_result) {
        $pending_data = mysqli_fetch_assoc($pending_result);
        $pending_orders = (int)$pending_data['count'];
    }
    
    $completed_result = mysqli_query($conn, 
        "SELECT COUNT(*) as count FROM orders o 
         JOIN order_items oi ON o.id = oi.order_id 
         JOIN products p ON oi.product_id = p.id 
         WHERE p.user_id = '$user_id' AND o.status = 'delivered'");
    if ($completed_result) {
        $completed_data = mysqli_fetch_assoc($completed_result);
        $completed_orders = (int)$completed_data['count'];
    }

    // Get recent orders (for sellers)
    $orders_result = mysqli_query($conn, 
        "SELECT o.id, o.order_number, o.created_at, o.status, o.final_amount 
         FROM orders o 
         JOIN order_items oi ON o.id = oi.order_id 
         JOIN products p ON oi.product_id = p.id 
         WHERE p.user_id = '$user_id' 
         ORDER BY o.created_at DESC LIMIT 5");
    if ($orders_result) {
        while ($row = mysqli_fetch_assoc($orders_result)) {
            $recent_orders[] = $row;
        }
    }
}

// Get recent messages
$stmt = $conn->prepare("
    SELECT m.*, u.username 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.receiver_id = ? 
    ORDER BY m.created_at DESC
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $recent_messages[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BigFiveBuys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --dark: #5a5c69;
        }
        
        .user-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2c3e50 100%);
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
        
        .quick-action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary);
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
        
        .unread-message {
            background-color: rgba(78, 115, 223, 0.05);
            font-weight: 600;
        }
        
        .admin-alert {
            background: linear-gradient(135deg, #2c3e50 0%, #4e73df 100%);
            color: white;
            border: none;
        }
    </style>
</head>
<body class="bg-light">

<?php include_once 'Includes/header.php'; ?>

<!-- User Header Section -->
<div class="user-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <?php if ($is_seller): ?>
                        <i class="fas fa-store me-2"></i> Seller Dashboard
                    <?php else: ?>
                        <i class="fas fa-user me-2"></i> User Dashboard
                    <?php endif; ?>
                </h1>
                <p class="mb-0">Welcome back, <?= $username ?></p>
            </div>
            <div class="text-end">
                <p class="mb-0"><small>Last login: <?= date('M j, Y H:i') ?></small></p>
                <div class="mt-2">
                    <?php if ($is_seller): ?>
                        <span class="badge bg-success me-2">
                            <i class="fas fa-check-circle"></i> Verified Seller
                        </span>
                    <?php elseif ($seller_verified): ?>
                        <span class="badge bg-warning text-dark me-2">
                            <i class="fas fa-clock"></i> Pending Approval
                        </span>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <!-- Notification Alert -->
    <?php if ($unread_messages > 0): ?>
        <div class="alert alert-info d-flex align-items-center animate__animated animate__fadeInDown">
            <i class="fas fa-envelope me-2"></i>
            You have <?= $unread_messages ?> unread message<?= $unread_messages > 1 ? 's' : '' ?>.
            <a href="messages.php" class="ms-auto btn btn-sm btn-outline-info">View Messages</a>
        </div>
    <?php endif; ?>

    <!-- Admin Control Panel Button (only visible for admins) -->
    <?php if ($role === 'admin'): ?>
        <div class="alert admin-alert d-flex align-items-center animate__animated animate__fadeInDown mb-4">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Administrator Access:</strong> You have full system privileges
            <a href="admin/dashboard.php" class="ms-auto btn btn-sm btn-light">Go to Admin Panel</a>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4 g-4">
        <?php if ($is_seller): ?>
            <div class="col-xl-3 col-md-6 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
                <div class="stat-card primary card bg-white shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Active Listings</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($active_products) ?></div>
                                <a href="my_listings.php" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box-open stat-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                <div class="stat-card warning card bg-white shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($pending_orders) ?></div>
                                <a href="seller_orders.php?status=pending" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock stat-icon text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                <div class="stat-card success card bg-white shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($completed_orders) ?></div>
                                <a href="seller_orders.php?status=delivered" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle stat-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeIn" style="animation-delay: <?= $is_seller ? '0.4s' : '0.1s' ?>">
            <div class="stat-card info card bg-white shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Unread Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($unread_messages) ?></div>
                            <a href="messages.php" class="stretched-link"></a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope stat-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($is_seller): ?>
                            <a href="post_product.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Post New Product
                            </a>
                            <a href="my_listings.php" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Manage Listings
                            </a>
                            <a href="seller_orders.php" class="btn btn-outline-warning">
                                <i class="fas fa-shopping-bag me-2"></i>Manage Orders
                            </a>
                        <?php endif; ?>
                        <a href="browse.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Browse Marketplace
                        </a>
                        <a href="messages.php" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i>View Messages
                        </a>
                        <?php if ($role === 'admin'): ?>
                            <a href="admin_panel.php" class="btn btn-danger">
                                <i class="fas fa-cog me-2"></i>Admin Panel
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders (for sellers) -->
    <?php if ($is_seller && !empty($recent_orders)): ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="seller_orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    <td>R<?= number_format($order['final_amount'], 2) ?></td>
                                    <td>
                                        <?php 
                                        $badge_class = [
                                            'pending' => 'bg-warning',
                                            'processing' => 'bg-info',
                                            'shipped' => 'bg-primary',
                                            'delivered' => 'bg-success',
                                            'cancelled' => 'bg-danger'
                                        ][$order['status']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Messages</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_messages)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_messages as $message): ?>
                            <a href="message_view.php?id=<?= $message['id'] ?>" 
                               class="list-group-item list-group-item-action <?= !$message['is_read'] ? 'unread-message' : '' ?> recent-activity-item">
                                <div class="d-flex justify-content-between">
                                    <span><?= htmlspecialchars($message['username']) ?></span>
                                    <small class="text-muted"><?= date('M j', strtotime($message['created_at'])) ?></small>
                                </div>
                                <p class="mb-0 text-truncate"><?= htmlspecialchars($message['subject'] ?? 'No subject') ?></p>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-end mt-3">
                            <a href="messages.php" class="btn btn-sm btn-outline-primary">View All Messages</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent messages</p>
                        <a href="browse.php" class="btn btn-sm btn-primary">Browse Products</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Profile Information</h6>
                        <p class="text-muted"><?= htmlspecialchars($_SESSION['email'] ?? 'No email provided') ?></p>
                        <a href="profile.php" class="btn btn-sm btn-outline-secondary">Edit Profile</a>
                    </div>
                    
                    <?php if ($is_seller): ?>
                        <div class="mb-4">
                            <h6>Seller Dashboard</h6>
                            <p class="text-muted">Manage your seller account and listings</p>
                            <div class="d-flex gap-2">
                                <a href="seller_dashboard.php" class="btn btn-sm btn-outline-success">Seller Dashboard</a>
                                <a href="sales_report.php" class="btn btn-sm btn-outline-info">Sales Reports</a>
                            </div>
                        </div>
                    <?php elseif ($role === 'admin'): ?>
                        <div class="mb-4">
                            <h6>Administrator Tools</h6>
                            <p class="text-muted">Access administrative functions</p>
                            <div class="d-flex gap-2">
                                <a href="admin_panel.php" class="btn btn-sm btn-outline-danger">Admin Panel</a>
                                <a href="admin_reports.php" class="btn btn-sm btn-outline-warning">System Reports</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h6>Security</h6>
                        <a href="change_password.php" class="btn btn-sm btn-outline-warning me-2">Change Password</a>
                        <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply to be Seller Modal -->
<div class="modal fade" id="applySellerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Become a Seller</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="apply_seller.php" method="POST">
                <div class="modal-body">
                    <p>Fill out this form to apply for seller privileges on BigFiveBuys. Our team will review your application within 2 business days.</p>
                    
                    <div class="mb-3">
                        <label for="businessName" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="businessName" name="business_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="businessDesc" class="form-label">Business Description</label>
                        <textarea class="form-control" id="businessDesc" name="business_description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the <a href="#" target="_blank">Seller Terms and Conditions</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'Includes/footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Add animation to elements when they come into view
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate__animated');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add(entry.target.dataset.animation);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
});
</script>
</body>
</html>