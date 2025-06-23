<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'Includes/header.php'; 
include 'Includes/db.php';

$keyword = $_GET['q'] ?? '';
$min = $_GET['min'] ?? 0;
$max = $_GET['max'] ?? 999999;

$result = mysqli_query($conn, "
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.title LIKE '%$keyword%' AND p.price BETWEEN $min AND $max
    AND p.status = 'active'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | BigFiveBuys</title>
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
        
        .search-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .product-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .product-img-container {
            height: 220px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        
        .product-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .product-category {
            font-size: 0.85rem;
            color: var(--dark);
        }
        
        .btn-view {
            background-color: var(--primary);
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            background-color: #3a5bc7;
            transform: translateY(-2px);
        }
        
        .page-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .page-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="page-title">Browse Products</h1>
        
        <!-- Search Form -->
        <div class="search-container">
            <form method="get">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input name="q" type="text" class="form-control form-control-lg" 
                                   placeholder="Search products..." value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">R</span>
                            <input name="min" type="number" class="form-control form-control-lg" 
                                   placeholder="Min price" value="<?= htmlspecialchars($min) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">R</span>
                            <input name="max" type="number" class="form-control form-control-lg" 
                                   placeholder="Max price" value="<?= htmlspecialchars($max) ?>">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Product Grid -->
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $image = !empty($row['image']) ? $row['image'] : 'uploads/default.jpg';
                        $condition_badge = [
                            'new' => 'success',
                            'used_like_new' => 'primary',
                            'used_good' => 'info',
                            'used_fair' => 'warning'
                        ][$row['condition']] ?? 'secondary';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card card h-100">
                            <div class="product-img-container">
                                <img src="<?= htmlspecialchars($image) ?>" class="product-img" alt="<?= htmlspecialchars($row['title']) ?>">
                            </div>
                            <div class="card-body">
                                <span class="badge bg-<?= $condition_badge ?> mb-2">
                                    <?= ucfirst(str_replace('_', ' ', $row['condition'])) ?>
                                </span>
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="product-category mb-2">
                                    <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="product-price">R<?= number_format($row['price'], 2) ?></span>
                                    <a href="view_product.php?id=<?= $row['id'] ?>" class="btn btn-view">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <h4>No products found</h4>
                        <p class="mb-0">Try adjusting your search filters</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to cards when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.product-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>

<?php include 'Includes/footer.php'; ?>