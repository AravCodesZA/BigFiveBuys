<?php
include_once 'Includes/db.php';
include_once 'Includes/header.php';

// Get all products
$sql = "SELECT p.*, c.name as category_name, u.username 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
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
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 25px;
            border: 1px solid #eee;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-img-container {
            height: 200px;
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
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .shipping-badge {
            font-size: 0.75rem;
            margin-right: 5px;
        }
        
        .btn-add-to-cart {
            background-color: #28a745;
            color: white;
            border: none;
            transition: background-color 0.3s;
        }
        
        .btn-add-to-cart:hover {
            background-color: #218838;
        }
        
        .card-footer {
            display: flex;
            gap: 10px;
            padding: 15px;
        }
        
        .card-footer .btn {
            flex: 1;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Product posted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['cart']) && $_GET['cart'] == 'added'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Product added to cart!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <h2 class="mb-4">Browse Products</h2>
        
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4">
                        <div class="product-card card h-100">
                            <div class="product-img-container">
                                <img src="<?= $row['image_path'] ?>" class="product-img" alt="<?= htmlspecialchars($row['title']) ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($row['category_name']) ?></p>
                                <p class="card-text"><?= substr(htmlspecialchars($row['description']), 0, 100) ?>...</p>
                                
                                <div class="mb-2">
                                    <?php if ($row['free_shipping']): ?>
                                        <span class="badge bg-success shipping-badge">
                                            <i class="fas fa-shipping-fast"></i> Free Shipping
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($row['cash_on_delivery']): ?>
                                        <span class="badge bg-info text-dark shipping-badge">
                                            <i class="fas fa-money-bill-wave"></i> COD
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($row['next_day_delivery']): ?>
                                        <span class="badge bg-warning text-dark shipping-badge">
                                            <i class="fas fa-bolt"></i> Next Day
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">R<?= number_format($row['price'], 2) ?></span>
                                    <span class="badge bg-light text-dark"><?= ucfirst(str_replace('_', ' ', $row['product_condition'])) ?></span>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i> View
                                </a>
                                <form action="cart.php" method="post" class="d-inline">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['title']) ?>">
                                    <input type="hidden" name="product_price" value="<?= $row['price'] ?>">
                                    <input type="hidden" name="product_type" value="<?= strpos(strtolower($row['title']), 'usb') !== false ? 'usb' : 'cable' ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-add-to-cart w-100">
                                        <i class="fas fa-cart-plus me-2"></i> Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No products found. Be the first to <a href="post_product.php">post a product</a>!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include_once 'Includes/footer.php'; ?>