<?php
include_once 'Includes/db.php';
include_once 'Includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit;
}

$product_id = intval($_GET['id']);
$sql = "SELECT p.*, c.name as category_name, u.username 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: product.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> | BigFiveBuys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .product-image {
            max-height: 400px;
            width: 100%;
            object-fit: contain;
        }
        
        .product-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .shipping-badge {
            font-size: 0.9rem;
            margin-right: 8px;
        }
        
        .seller-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="product-container p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 text-center">
                        <img src="<?= $product['image_path'] ?>" class="product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h1><?= htmlspecialchars($product['title']) ?></h1>
                    <p class="text-muted">Category: <?= htmlspecialchars($product['category_name']) ?></p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <span class="product-price me-3">R<?= number_format($product['price'], 2) ?></span>
                        <span class="badge bg-light text-dark"><?= ucfirst(str_replace('_', ' ', $product['product_condition'])) ?></span>
                    </div>
                    
                    <div class="mb-4">
                        <?php if ($product['free_shipping']): ?>
                            <span class="badge bg-success shipping-badge">
                                <i class="fas fa-shipping-fast"></i> Free Shipping
                            </span>
                        <?php endif; ?>
                        <?php if ($product['cash_on_delivery']): ?>
                            <span class="badge bg-info text-dark shipping-badge">
                                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                            </span>
                        <?php endif; ?>
                        <?php if ($product['next_day_delivery']): ?>
                            <span class="badge bg-warning text-dark shipping-badge">
                                <i class="fas fa-bolt"></i> Next Day Delivery
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Description</h4>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    
                    <div class="seller-info mb-4">
                        <h5><i class="fas fa-store me-2"></i> Sold by</h5>
                        <p class="mb-1"><?= htmlspecialchars($product['username']) ?></p>
                        <p class="text-muted small">Member since <?= date('M Y', strtotime($product['created_at'])) ?></p>
                    </div>
                    
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="row mb-3">
                            <div class="col-4">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['quantity'] ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include_once 'Includes/footer.php'; ?>