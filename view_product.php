<?php
session_start();
include_once 'Includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = $product_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: products.php");
    exit;
}

$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> | BigFiveBuys</title>
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
        
        .product-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        
        .product-image-container {
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s;
        }
        
        .product-image:hover {
            transform: scale(1.05);
        }
        
        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .shipping-badge {
            font-size: 0.9rem;
            margin-right: 8px;
            padding: 8px 12px;
            border-radius: 20px;
        }
        
        .condition-badge {
            font-size: 0.9rem;
            padding: 8px 12px;
        }
        
        .add-to-cart-btn {
            background: var(--success);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .add-to-cart-btn:hover {
            background: #17a673;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(28, 200, 138, 0.3);
        }
        
        .quantity-selector {
            width: 120px;
        }
        
        .delivery-option {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .delivery-option:hover {
            border-color: var(--primary);
            background: rgba(78, 115, 223, 0.05);
        }
        
        .delivery-option.selected {
            border-color: var(--success);
            background: rgba(28, 200, 138, 0.05);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .review-star {
            color: #FFD700;
            margin-right: 3px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="product-container p-0 animate__animated animate__fadeIn">
            <div class="row g-0">
                <!-- Product Image -->
                <div class="col-lg-6">
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="col-lg-6 p-4 p-lg-5">
                    <h1 class="mb-3"><?= htmlspecialchars($product['title']) ?></h1>
                    
                    <!-- Rating -->
                    <div class="mb-3">
                        <span class="review-star"><i class="fas fa-star"></i></span>
                        <span class="review-star"><i class="fas fa-star"></i></span>
                        <span class="review-star"><i class="fas fa-star"></i></span>
                        <span class="review-star"><i class="fas fa-star"></i></span>
                        <span class="review-star"><i class="fas fa-star-half-alt"></i></span>
                        <span class="text-muted ms-2">(55 Reviews)</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="product-price me-3">R<?= number_format($product['price'], 2) ?></span>
                        <span class="badge condition-badge bg-<?= 
                            $product['condition'] == 'new' ? 'success' : 
                            ($product['condition'] == 'used_like_new' ? 'info' : 
                            ($product['condition'] == 'used_good' ? 'warning' : 'secondary')) 
                        ?>">
                            <?= ucfirst(str_replace('_', ' ', $product['condition'])) ?>
                        </span>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h4 class="mb-3">Description</h4>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    
                    <!-- Shipping Options -->
                    <div class="mb-4">
                        <h4 class="mb-3">Delivery Options</h4>
                        
                        <div class="delivery-option selected" onclick="selectDelivery(this)">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-truck me-2"></i> <strong>Standard Delivery</strong>
                                </div>
                                <span class="badge bg-success">R30.00</span>
                            </div>
                            <p class="mb-0 text-muted small">Eligible for collection or next-day delivery</p>
                        </div>
                        
                        <?php if ($product['cash_on_delivery']): ?>
                        <div class="delivery-option" onclick="selectDelivery(this)">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-money-bill-wave me-2"></i> <strong>Cash on Delivery</strong>
                                </div>
                            </div>
                            <p class="mb-0 text-muted small">Pay when you receive your order</p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($product['next_day_delivery']): ?>
                        <div class="delivery-option" onclick="selectDelivery(this)">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-bolt me-2"></i> <strong>Next Day Delivery</strong>
                                </div>
                                <span class="text-success">+R99.00</span>
                            </div>
                            <p class="mb-0 text-muted small">Get it by tomorrow</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Add to Cart -->
            <form action="cart.php?id=<?= $product_id ?>" method="post" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="add_to_cart" value="1">
                
                <div class="row align-items-center mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Quantity</label>
                        <div class="input-group quantity-selector">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQuantity(-1)">-</button>
                            <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['quantity'] ?>">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQuantity(1)">+</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <button type="submit" class="btn add-to-cart-btn w-100">
                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </form>

                    
                    <!-- Product Policies -->
                    <div class="border-top pt-3 mt-4">
                        <div class="row">
                            <div class="col-6 col-md-4 text-center mb-3">
                                <div class="text-primary mb-1"><i class="fas fa-exchange-alt fa-2x"></i></div>
                                <small class="d-block">Free returns & exchanges for 60 days</small>
                            </div>
                            <div class="col-6 col-md-4 text-center mb-3">
                                <div class="text-primary mb-1"><i class="fas fa-shield-alt fa-2x"></i></div>
                                <small class="d-block">Limited Warranty for 12 Months</small>
                            </div>
                            <div class="col-6 col-md-4 text-center">
                                <div class="text-primary mb-1"><i class="fas fa-credit-card fa-2x"></i></div>
                                <small class="d-block">Secure payment options</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Technical Specifications Section -->
        <div class="product-container mt-4 p-4 animate__animated animate__fadeIn animate__delay-1s">
            <h4 class="mb-4">Technical Specifications</h4>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%">Brand</th>
                                <td>DJI</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>Mini 2 SE</td>
                            </tr>
                            <tr>
                                <th>Camera Resolution</th>
                                <td>4K Ultra HD</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%">Flight Time</th>
                                <td>30 minutes</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>249g</td>
                            </tr>
                            <tr>
                                <th>GPS</th>
                                <td>GPS + GLONASS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delivery option selection
        function selectDelivery(element) {
            document.querySelectorAll('.delivery-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            element.classList.add('selected');
        }
        
        // Quantity adjustment
        function adjustQuantity(change) {
            const input = document.querySelector('input[name="quantity"]');
            let newValue = parseInt(input.value) + change;
            if (newValue < 1) newValue = 1;
            if (newValue > <?= $product['quantity'] ?>) newValue = <?= $product['quantity'] ?>;
            input.value = newValue;
        }
        
        // Animation for elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.animate__animated');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add(entry.target.dataset.animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            animateElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>
</body>
</html>

<?php include_once 'Includes/footer.php'; ?>