<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include_once 'Includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to post a product.");
}

$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$category_id = (int) $_POST['category_id'];
$description = mysqli_real_escape_string($conn, $_POST['description']);
$price = (float) $_POST['price'];
$quantity = (int) $_POST['quantity'];
$product_condition = mysqli_real_escape_string($conn, $_POST['condition']);

$free_shipping = isset($_POST['free_shipping']) ? 1 : 0;
$cash_on_delivery = isset($_POST['cash_on_delivery']) ? 1 : 0;
$next_day_delivery = isset($_POST['next_day_delivery']) ? 1 : 0;

$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_name = basename($_FILES['image']['name']);
    $target_file = $upload_dir . time() . "_" . $image_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
    } else {
        die("Image upload failed.");
    }
} else {
    die("No image uploaded.");
}

$sql = "INSERT INTO products (
    user_id, category_id, title, description, price, quantity,
    `condition`, `image`, free_shipping, cash_on_delivery,
    next_day_delivery, status, created_at
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW()
)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iissdissiii",
    $user_id, $category_id, $title, $description, $price, $quantity,
    $product_condition, $image_path, $free_shipping, $cash_on_delivery,
    $next_day_delivery
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: my_listings.php?success=Product posted successfully");
    exit;
} else {
    echo "Database error: " . mysqli_error($conn);
}
?>
