<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'Includes/header.php'; 
include 'Includes/auth.php'; 
include 'Includes/db.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM orders WHERE id = p.id) AS sales_count,
          c.name as category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings | BigFiveBuys</title>
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
        
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        
        .action-buttons .btn {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">My Listings</h1>
            <a href="post_product.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Product
            </a>
        </div>

        <?php if($result->num_rows === 0): ?>
            <div class="alert alert-info">
                <h5 class="alert-heading">No Listings Found</h5>
                <p>You haven't listed any products yet. Start selling by adding your first product!</p>
                <hr>
                <a href="post_product.php" class="btn btn-outline-info">Create Your First Listing</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php while ($product = $result->fetch_assoc()): ?>
                    <?php
                        $image = !empty($product['image']) ? 'uploads/' . $product['image'] : 'uploads/default.jpg';
                        $condition_badge = [
                            'new' => 'success',
                            'used_like_new' => 'primary',
                            'used_good' => 'info',
                            'used_fair' => 'warning'
                        ][$product['condition']] ?? 'secondary';
                        $status_badge = $product['status'] === 'active' ? 'success' : 'secondary';
                    ?>
                    <div class="col">
                        <div class="product-card card h-100">
                            <div class="product-img-container position-relative">
                                <img src="<?= htmlspecialchars($image) ?>" 
                                     class="product-img" 
                                     alt="<?= htmlspecialchars($product['title']) ?>">
                                <span class="badge bg-<?= $condition_badge ?> position-absolute top-0 start-0">
                                    <?= ucfirst(str_replace('_', ' ', $product['condition'])) ?>
                                </span>
                                <span class="status-badge badge bg-<?= $status_badge ?>">
                                    <?= ucfirst($product['status']) ?>
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($product['title']) ?></h5>
                                    <p class="product-category mb-2">
                                        <i class="fas fa-tag me-1"></i> 
                                        <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="product-price">R<?= number_format($product['price'], 2) ?></span>
                                        <small class="text-muted">Qty: <?= $product['quantity'] ?></small>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">
                                            <i class="fas fa-chart-line me-1"></i>
                                            <?= $product['sales_count'] ?> sales
                                        </span>
                                        <span class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?= date('M j, Y', strtotime($product['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-auto pt-2">
                                    <div class="d-flex justify-content-between action-buttons">
                                        <a href="view_product.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="far fa-eye me-1"></i> View
                                        </a>
                                        <a href="edit_product.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-outline-secondary">
                                            <i class="far fa-edit me-1"></i> Edit
                                        </a>
                                        <form action="delete_product.php" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="far fa-trash-alt me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
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