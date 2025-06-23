<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$res = ['is_seller' => false];
$conn = null;

// Only proceed with database operations if we have a user_id
if (isset($_SESSION['user_id'])) {
    // Try to establish database connection if not already available
    if (!isset($conn)) {
        @include_once 'Includes/db.php'; // @ suppresses errors, we'll check connection manually
    }
    
    // Verify we have a valid connection
    if (isset($conn) && $conn instanceof mysqli) {
        $uid = $_SESSION['user_id'];
        
        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT is_seller FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $res = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BigFiveBuys | South Africa's Own Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF9F1C;
            --secondary: #2EC4B6;
            --dark: #011627;
            --light: #FDFFFC;
            --accent: #E71D36;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            white-space: nowrap;
        }

        .primary-text { color: var(--primary); }
        .secondary-text { color: var(--secondary); }
        .accent-text { color: var(--accent); }

        .apply-btn-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(233, 29, 54, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(233, 29, 54, 0); }
            100% { box-shadow: 0 0 0 0 rgba(233, 29, 54, 0); }
        }

        .nav-link {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .search-box {
            min-width: 200px;
            max-width: 300px;
        }

        /* Header layout fixes */
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                justify-content: space-between;
            }
            
            .navbar-nav.flex-row {
                flex-wrap: nowrap;
                gap: 0.5rem;
            }
            
            .right-nav-container {
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                gap: 1rem;
            }
        }

        /* Prevent shrinking of important elements */
        .no-shrink {
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Brand/logo -->
            <a class="navbar-brand no-shrink me-3" href="index.php">
                <span class="primary-text">Big</span><span class="secondary-text">Five</span><span class="accent-text">Buys</span>
            </a>
            
            <!-- Toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Main navbar content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Left-aligned nav items -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="browse.php">Browse</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                </ul>
                
                <!-- Right-aligned content -->
                <div class="right-nav-container">
                    <!-- Search box -->
                    <div class="input-group search-box no-shrink">
                        <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        <button id="search-btn" class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                    
                    <!-- User icons -->
                    <ul class="navbar-nav flex-row no-shrink">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Seller application button -->
                            <?php if (!$res['is_seller']): ?>
                                <li class="nav-item">
                                    <a class="nav-link btn btn-sm btn-danger apply-btn-pulse px-2" href="apply_seller.php" data-bs-toggle="modal" data-bs-target="#applyModal">
                                        <i class="fas fa-store me-1"></i> Sell
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Cart icon -->
                            <li class="nav-item">
                                <a class="nav-link position-relative px-2" href="cart.php">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-accent">0</span>
                                </a>
                            </li>
                            
                            <!-- Orders icon -->
                            <li class="nav-item">
                                <a class="nav-link position-relative px-2" href="products/order.php">
                                    <i class="fas fa-box"></i>
                                    <?php if (!empty($_SESSION['cart'])): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-accent">
                                            <?= count($_SESSION['cart']) ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            
                            <!-- User dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <!-- Login/Register -->
                            <li class="nav-item">
                                <a class="nav-link px-2" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-primary ms-2" href="register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>