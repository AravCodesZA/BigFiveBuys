<?php
include 'includes/db.php';

function shortenDescription($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isSeller() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller';
}
function getProductImage($product_id) {
    global $db;
    $stmt = $db->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['image_path'];
    }
    return 'assets/images/products/default.jpg';
}

function getProductById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getProductImages($product_id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserById($id) {
    global $db;
    $stmt = $db->prepare("SELECT id, username, email, first_name, last_name, is_seller FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getProductReviews($seller_id) {
    global $db;
    $stmt = $db->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAverageRating($seller_id) {
    global $db;
    $stmt = $db->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE reviewee_id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return round($row['avg_rating'], 1);
}

function generateStarRating($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="bi bi-star-fill text-warning"></i>';
    }
    
    if ($halfStar) {
        $stars .= '<i class="bi bi-star-half text-warning"></i>';
    }
    
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="bi bi-star text-warning"></i>';
    }
    
    return $stars;
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function getCount($table) {
    global $db;
    $result = $db->query("SELECT COUNT(*) as count FROM $table");
    return $result->fetch_assoc()['count'];
}

function getPendingSellerApplicationsCount() {
    global $db;
    $result = $db->query("SELECT COUNT(*) as count FROM users WHERE seller_application IS NOT NULL AND is_seller = 0");
    return $result->fetch_assoc()['count'];
}

function getRecentOrders($limit = 5) {
    global $db;
    $sql = "SELECT o.id, p.title as product_title, u.username as buyer_username, o.status 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            JOIN users u ON o.buyer_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT $limit";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRecentSellerApplications($limit = 5) {
    global $db;
    $sql = "SELECT id, username, updated_at 
            FROM users 
            WHERE seller_application IS NOT NULL AND is_seller = 0 
            ORDER BY updated_at DESC 
            LIMIT $limit";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending': return 'secondary';
        case 'processing': return 'info';
        case 'shipped': return 'primary';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function shortenString($string, $length) {
    if (strlen($string) <= $length) return $string;
    return substr($string, 0, $length) . '...';
}

function addToCart($product_id, $name, $price, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity
        ];
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQuantity($product_id, $quantity) {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = max(1, (int)$quantity);
    }
}

function getCartTotal() {
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
?>