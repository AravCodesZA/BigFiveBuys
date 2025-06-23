<?php
include '../includes/config.php';
include '../includes/auth.php';
include '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: ../login.php?redirect=products/my_orders.php");
    exit();
}

$orders = [];

$page_title = "My Orders - " . SITE_NAME;
include '../includes/header.php';
?>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-dark mb-8">My Orders</h2>
        
        <?php if (empty($orders)): ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet.</p>
            <a href="products.php" class="bg-primary hover:bg-opacity-90 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                Start Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="grid grid-cols-12 bg-gray-100 p-4 font-semibold">
                <div class="col-span-2">Order #</div>
                <div class="col-span-4">Date</div>
                <div class="col-span-2">Status</div>
                <div class="col-span-2">Total</div>
                <div class="col-span-2">Actions</div>
            </div>
            
            <?php foreach ($orders as $order): ?>
            <div class="grid grid-cols-12 p-4 border-b items-center">
                <div class="col-span-2">#<?= $order['id'] ?></div>
                <div class="col-span-4"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                <div class="col-span-2">
                    <span class="px-2 py-1 rounded-full text-xs <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="col-span-2 font-semibold">R <?= number_format($order['total'], 2) ?></div>
                <div class="col-span-2">
                    <a href="order_details.php?id=<?= $order['id'] ?>" class="text-primary hover:underline">View</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>