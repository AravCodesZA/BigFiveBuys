<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include_once 'Includes/db.php';

// Initialize user data
$res = ['is_seller' => false];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $q = mysqli_query($conn, "SELECT is_seller FROM users WHERE id = '$uid'");
    if ($q && mysqli_num_rows($q) > 0) {
        $res = mysqli_fetch_assoc($q);
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
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF9F1C;
            --secondary: #2EC4B6;
            --dark: #011627;
            --light: #FDFFFC;
            --accent: #E71D36;
            --flag-green: #007749;
            --flag-yellow: #FFB81C;
            --flag-red: #E03C31;
            --flag-blue: #001489;
        }
        
        /* Vibrant Header Styles */
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, var(--dark) 0%, #1a2a3a 100%) !important;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background: var(--dark) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            transition: all 0.3s ease;
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
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        /* Flag color animation */
        .flag-color span {
            display: inline-block;
            transition: all 0.5s ease;
        }
        .flag-color span:nth-child(1) { color: var(--flag-green); }
        .flag-color span:nth-child(2) { color: black; }
        .flag-color span:nth-child(3) { color: var(--flag-yellow); }
        .flag-color span:nth-child(4) { color: var(--flag-blue); }
        .flag-color span:nth-child(5) { color: var(--flag-red); }
        .flag-color span:nth-child(6) { color: white; }
        
        .flag-color:hover span {
            transform: translateY(-3px);
            text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Hero Section with Parallax */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/assets/images/south-africa.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to bottom, transparent, var(--light));
        }
        
        /* Scroll Animation Elements */
        [data-aos] {
            transition-property: transform, opacity;
        }
        
        /* Vibrant Card Styles */
        .category-card, .product-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            overflow: hidden;
        }
        
        .category-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        /* Floating Buttons */
        .floating-cart-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(233, 29, 54, 0.3);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .floating-cart-btn:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 25px rgba(233, 29, 54, 0.4);
        }
        
        .floating-seller-btn {
            position: fixed;
            bottom: 100px;
            right: 30px;
            z-index: 99;
            padding: 12px 20px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(255, 159, 28, 0.3);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .floating-seller-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 159, 28, 0.4);
        }
        
        /* Gradient Backgrounds */
        .gradient-bg-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #ffbf69 100%);
        }
        
        .gradient-bg-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, #5bc0be 100%);
        }
        
        /* Testimonial Cards */
        .testimonial-card {
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand me-3" href="index.php">
                <span class="primary-text">Big</span><span class="secondary-text">Five</span><span class="accent-text">Buys</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse.php">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center ms-auto">
                    <div class="input-group search-box me-3">
                        <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Search...">
                        <button id="search-btn" class="btn btn-primary btn-sm" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <ul class="navbar-nav flex-row">
                        <li class="nav-item mx-1">
                            <a class="nav-link position-relative" href="cart.php" title="Cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count">0</span>
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item mx-1">
                                <a class="nav-link" href="dashboard.php" title="Dashboard">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                            </li>
                            <li class="nav-item mx-1">
                                <a class="nav-link" href="logout.php" title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item mx-1">
                                <a class="nav-link" href="login.php" title="Login">
                                    <i class="fas fa-sign-in-alt"></i>
                                </a>
                            </li>
                            <li class="nav-item ms-2">
                                <a class="btn btn-primary btn-sm" href="register.php">
                                    Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Scroll Animation -->
    <section class="hero-section mb-5" data-aos="fade" data-aos-duration="1000">
        <div class="container text-center text-white">
            <h1 class="flag-color display-3 mb-4">
                <span>B</span><span>i</span><span>g</span><span>F</span><span>i</span><span>v</span><span>e</span>Buys
            </h1>
            <p class="lead mb-5" data-aos="fade-up" data-aos-delay="200">South Africa's Premier C2C Marketplace 🇿🇦</p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="browse.php" class="btn btn-outline-light btn-lg me-2">Browse Products</a>
                <a href="register.php" class="btn btn-light btn-lg">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Categories Section with Animation -->
    <section class="container py-5 my-5">
        <h2 class="text-center mb-5" data-aos="fade-up">Popular Categories</h2>
        <div class="row">
            <!-- Lion - Electronics -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div class="gradient-bg-primary p-4 flex justify-center">
                        <i class="fas fa-lion text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Electronics</h3>
                        <p class="text-muted text-sm text-center mt-1">128 products</p>
                    </div>
                </div>
            </div>
            
            <!-- Elephant - Furniture -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div class="gradient-bg-secondary p-4 flex justify-center">
                        <i class="fas fa-elephant text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Furniture</h3>
                        <p class="text-muted text-sm text-center mt-1">96 products</p>
                    </div>
                </div>
            </div>
            
            <!-- Buffalo - Clothing -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div style="background: linear-gradient(135deg, #001489 0%, #3a5bcd 100%)" class="p-4 flex justify-center">
                        <i class="fas fa-tshirt text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Clothing</h3>
                        <p class="text-muted text-sm text-center mt-1">245 products</p>
                    </div>
                </div>
            </div>
            
            <!-- Leopard - Vehicles -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div style="background: linear-gradient(135deg, #E03C31 0%, #f0756d 100%)" class="p-4 flex justify-center">
                        <i class="fas fa-car text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Vehicles</h3>
                        <p class="text-muted text-sm text-center mt-1">42 products</p>
                    </div>
                </div>
            </div>
            
            <!-- Rhino - Home & Garden -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div style="background: linear-gradient(135deg, #007749 0%, #4daa7d 100%)" class="p-4 flex justify-center">
                        <i class="fas fa-leaf text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Home & Garden</h3>
                        <p class="text-muted text-sm text-center mt-1">178 products</p>
                    </div>
                </div>
            </div>
            
            <!-- Additional Category -->
            <div class="col-md-2 col-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="category-card bg-white rounded-lg shadow-md overflow-hidden h-100">
                    <div style="background: linear-gradient(135deg, #FFB81C 0%, #ffd166 100%)" class="p-4 flex justify-center">
                        <i class="fas fa-mobile-alt text-white fa-3x"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-center">Phones</h3>
                        <p class="text-muted text-sm text-center mt-1">312 products</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products with Animation -->
    <section class="container mb-5 py-5">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <h2 class="mb-0">Featured Products</h2>
            <a href="browse.php" class="text-primary text-decoration-none">
                View all <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row">
            <!-- Product 1 -->
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="product-card card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="card-img-top" alt="Running Shoes" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 bg-danger text-white text-xs px-2 py-1 rounded m-2">NEW</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Premium Running Shoes</h5>
                                <p class="text-muted small mb-2">Johannesburg</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Clothing</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold">R1,299</span>
                            <div class="d-flex align-items-center text-warning">
                                <i class="fas fa-star small"></i>
                                <span class="ms-1 text-muted small">4.8</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="product-card card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="https://matrixwarehouse.co.za/wp-content/uploads/2023/05/asus-rog-strix-g17-g713-ryzen-7-gaming-laptop.jpg" class="card-img-top" alt="Laptop" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 bg-success text-white text-xs px-2 py-1 rounded m-2">-15%</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Gaming Laptop</h5>
                                <p class="text-muted small mb-2">Cape Town</p>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success">Electronics</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="fw-bold">R12,999</span>
                                <span class="text-muted small text-decoration-line-through ms-2">R15,299</span>
                            </div>
                            <div class="d-flex align-items-center text-warning">
                                <i class="fas fa-star small"></i>
                                <span class="ms-1 text-muted small">4.9</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 3 -->
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="product-card card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQzCyiBg5lSJTXtFpxiw-LC8T46zYQoAM2a1A&s" class="card-img-top" alt="Headphones" style="height: 200px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Wireless Headphones</h5>
                                <p class="text-muted small mb-2">Durban</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Electronics</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold">R799</span>
                            <div class="d-flex align-items-center text-warning">
                                <i class="fas fa-star small"></i>
                                <span class="ms-1 text-muted small">4.7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 4 -->
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="product-card card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="https://sa.smartwatch.com.co/cdn/shop/files/smart-watch-south-africa-ip68-smart-watch-professional-black-w26-professional-black-36300298748150.jpg?v=1688828594" class="card-img-top" alt="Smartwatch" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 bg-danger text-white text-xs px-2 py-1 rounded m-2">BESTSELLER</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Smart Watch 2023</h5>
                                <p class="text-muted small mb-2">Pretoria</p>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger">Electronics</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold">R2,499</span>
                            <div class="d-flex align-items-center text-warning">
                                <i class="fas fa-star small"></i>
                                <span class="ms-1 text-muted small">5.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works with Animation -->
    <section class="container mb-5 py-5">
        <h2 class="text-center mb-5" data-aos="fade-up">How BigFiveBuys Works</h2>
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4 rounded-lg h-100">
                    <div class="bg-primary bg-opacity-10 w-16 h-16 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <i class="fas fa-search text-primary fs-4"></i>
                    </div>
                    <h3 class="h5 mb-2">Find What You Need</h3>
                    <p class="text-muted">Browse through thousands of listings from trusted South African sellers.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4 rounded-lg h-100">
                    <div class="bg-success bg-opacity-10 w-16 h-16 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <i class="fas fa-lock text-success fs-4"></i>
                    </div>
                    <h3 class="h5 mb-2">Secure Transaction</h3>
                    <p class="text-muted">Our platform ensures secure payments and protected transactions.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center p-4 rounded-lg h-100">
                    <div class="bg-dark bg-opacity-10 w-16 h-16 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <i class="fas fa-truck text-dark fs-4"></i>
                    </div>
                    <h3 class="h5 mb-2">Receive Your Item</h3>
                    <p class="text-muted">Get your purchase delivered or arrange for local pickup.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials with Animation -->
    <section class="bg-light py-5 mb-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">What Our Community Says</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Thandie M." class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <h4 class="h6 mb-0">Thandie M.</h4>
                                <div class="d-flex text-warning">
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0">"I've sold over 50 items on BigFiveBuys. The platform is so easy to use and the community is trustworthy. It's changed my small business completely!"</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Pieter V." class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <h4 class="h6 mb-0">Pieter V.</h4>
                                <div class="d-flex text-warning">
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0">"As a buyer, I appreciate the local feel of BigFiveBuys. Found great deals on electronics and the sellers are responsive. Much better than international platforms."</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Nosipho K." class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <h4 class="h6 mb-0">Nosipho K.</h4>
                                <div class="d-flex text-warning">
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star small"></i>
                                    <i class="fas fa-star-half-alt small"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0">"I applied for seller privileges and within 2 days I was approved. The admin team is efficient and helpful. Now I can sell my handmade crafts safely."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section with Animation -->
    <section class="bg-dark text-white py-5 mb-5">
        <div class="container text-center py-4" data-aos="zoom-in">
            <h2 class="mb-4">Ready to Join South Africa's Marketplace?</h2>
            <p class="lead mb-5">
                Whether buying or selling, BigFiveBuys connects you with trusted members of your community.
            </p>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
                <a href="register.php" class="btn btn-primary btn-lg">
                    Register Now <i class="fas fa-user-plus ms-2"></i>
                </a>
                <a href="about.php" class="btn btn-light btn-lg">
                    Learn More <i class="fas fa-info-circle ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Floating Cart Button -->
    <a href="cart.php" class="floating-cart-btn btn btn-primary d-flex align-items-center justify-content-center" data-aos="fade-up" data-aos-delay="400">
        <i class="fas fa-shopping-cart fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-accent">
            0
        </span>
    </a>

    <!-- Floating Apply to Sell Button -->
    <?php if (!isset($_SESSION['user_id']) || !$res['is_seller']): ?>
    <button class="floating-seller-btn btn btn-danger apply-btn-pulse" data-bs-toggle="modal" data-bs-target="#applyModal" data-aos="fade-up" data-aos-delay="500">
        <i class="fas fa-store me-2"></i> Apply to Sell
    </button>
    <?php endif; ?>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS (Animate On Scroll)
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Search functionality
        document.getElementById('search-btn').addEventListener('click', function() {
            const query = document.getElementById('search-input').value.trim();
            if (query) {
                window.location.href = `browse.php?search=${encodeURIComponent(query)}`;
            }
        });

        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `browse.php?search=${encodeURIComponent(query)}`;
                }
            }
        });

        // Update cart count (example)
        function updateCartCount() {
            const count = <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>;
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
        }
        
        updateCartCount();
    </script>
    <?php include_once 'Includes/footer.php'; ?>
</body>
</html>