<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$page_title = "Order Confirmation - " . SITE_NAME;
require_once '../includes/header.php';

// Get shipping info from session
$shipping_info = json_decode($_SESSION['shipping_info'] ?? '{}', true);

// Clear the cart after successful purchase
unset($_SESSION['cart']);
unset($_SESSION['shipping_info']);
?>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-dark mb-4">Order Confirmed!</h2>
            <p class="text-lg text-gray-600 mb-6">Thank you for your purchase. Your order has been received and is being processed.</p>
            
            <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold mb-4">Order Details</h3>
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div>
                        <p class="text-gray-600">Order Number:</p>
                        <p class="font-medium">#<?= rand(100000, 999999) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Date:</p>
                        <p class="font-medium"><?= date('F j, Y') ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total:</p>
                        <p class="font-medium">R <?= number_format($_SESSION['order_total'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Payment Method:</p>
                        <p class="font-medium">Credit Card</p>
                    </div>
                </div>
            </div>
            
            <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-xl font-semibold mb-4">Shipping Information</h3>
                <p class="font-medium"><?= htmlspecialchars($shipping_info['first_name'] ?? '') ?> <?= htmlspecialchars($shipping_info['last_name'] ?? '') ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($shipping_info['address'] ?? '') ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($shipping_info['city'] ?? '') ?>, <?= htmlspecialchars($shipping_info['province'] ?? '') ?> <?= htmlspecialchars($shipping_info['postal_code'] ?? '') ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($shipping_info['phone'] ?? '') ?></p>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="products.php" class="bg-primary hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    Continue Shopping
                </a>
                <a href="my_orders.php" class="bg-white hover:bg-gray-100 text-dark font-bold py-3 px-6 rounded-lg border border-gray-300 transition duration-200">
                    View My Orders
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>