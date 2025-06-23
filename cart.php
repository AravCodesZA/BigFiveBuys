<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        [
            'name' => 'Type-C To Micro Usb Charger',
            'price' => 32.00,
            'quantity' => 1,
            'type' => 'cable',
            'image' => 'https://www.firstshop.co.za/cdn/shop/products/y-c473bk-cables-30753523761316.jpg?v=1630074765&width=675'
        ],
        [
            'name' => 'USB 32GB Kioxia USB2 White',
            'price' => 77.00,
            'quantity' => 1,
            'type' => 'usb',
            'image' => 'https://www.huge.co.za/wp-content/uploads/2025/03/LU202W032GG4.jpg'
        ]
    ];
}

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 30.00;
$total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --text-color: #333;
            --text-light: #666;
            --bg-color: #f8f9fa;
            --border-color: #e9ecef;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Main Content */
        .main-wrapper {
            flex: 1;
            padding: 30px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }

        .main-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            height: fit-content;
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-title {
            font-size: 18px;
            font-weight: 600;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-image {
            width: 80px;
            height: 80px;
            background: var(--bg-color);
            border-radius: 8px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 5px;
        }

        .product-details {
            flex: 1;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .rating-section {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .rating-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: var(--text-light);
        }

        .rating-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-price {
            text-align: right;
        }

        .quantity {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .price {
            font-size: 16px;
            font-weight: 600;
        }

        .shipping-section, .payment-section {
            padding: 20px 0;
            border-top: 1px solid #f0f0f0;
            margin-top: 20px;
        }

        .shipping-row, .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .total-row {
            padding-top: 15px;
            border-top: 2px solid var(--border-color);
            margin-top: 10px;
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checkout-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.2s;
        }

        .checkout-btn:hover {
            background-color: var(--primary-hover);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php 

    include 'Includes/header.php'; 
    ?>
    
    <div class="main-wrapper">
        <div class="container">
            <div class="main-content">
                <div class="summary-header">
                    <span class="summary-title">Your Order</span>
                </div>

                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <div class="product-item">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNjY2IiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxMCI+SW1hZ2UgTm90IEZvdW5kPC90ZXh0Pjwvc3ZnPg=='">
                    </div>
                    <div class="product-details">
                        <div class="product-title"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="rating-section">
                            <div class="rating-item">
                                <div class="rating-icon"></div>
                                <span>Rating given</span>
                            </div>
                            <div class="rating-item">
                                <div class="rating-icon"></div>
                                <span>Rating received</span>
                            </div>
                        </div>
                    </div>
                    <div class="product-price">
                        <div class="quantity"><?= $item['quantity'] ?> x</div>
                        <div class="price">R<?= number_format($item['price'], 2) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="shipping-section">
                    <div class="shipping-row">
                        <span>Standard shipping</span>
                        <span>R<?= number_format($shipping, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Total:</span>
                        <span>R<?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <div class="payment-section">
                    <form action="payment.php" method="POST">
                        <input type="hidden" name="amount" value="<?= htmlspecialchars($total) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['user_email'] ?? 'guest@example.com') ?>">
                        <button type="submit" class="checkout-btn">PROCEED TO PAYMENT</button>
                    </form>
                </div>
            </div>

            <div class="sidebar">
                <div class="sidebar-section">
                    <div class="sidebar-title">SHIPPING</div>
                    <div>Standard shipping</div>
                    <div style="color: var(--text-light); line-height: 1.6; margin-top: 10px;">
                        123 Eduvos Road<br>
                        Bedfordview<br>
                        Johannesburg<br>
                        Gauteng<br>
                        1610<br>
                        South Africa
                    </div>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-title">Contact details</div>
                    <div style="font-weight: 600; margin-bottom: 5px;">Arav Baboolal</div>
                    <div style="color: var(--text-light);">aravbaboolal22@gmail.com</div>
                    <div style="color: var(--text-light);">+27670498415</div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    include 'Includes/footer.php'; 
    ?>
</body>
</html>