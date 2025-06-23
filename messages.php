<?php
include_once '../Includes/config.php';
include_once '../Includes/auth.php';

include_once 'Includes/db.php';
include_once 'Includes/functions.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;

if ($product_id > 0) {
    $product = getProductById($product_id);
    if (!$product) {
        header("Location: ../index.php");
        exit();
    }
    $receiver_id = $product['seller_id'];
} elseif ($receiver_id > 0) {
    $receiver = getUserById($receiver_id);
    if (!$receiver) {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

if ($receiver_id === $_SESSION['user_id']) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    
    if (empty($message)) {
        $error = "Message cannot be empty";
    } else {
        $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, product_id, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $_SESSION['user_id'], $receiver_id, $product_id, $message);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to send message. Please try again.";
        }
    }
}

$page_title = "New Message - " . SITE_NAME;
include_once '../Includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">New Message</h3>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Your message has been sent successfully!
                        </div>
                        <a href="../index.php" class="btn btn-primary">Return to Home</a>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if ($product_id > 0): ?>
                            <div class="mb-4">
                                <h5>About Product: <?= htmlspecialchars($product['title']) ?></h5>
                                <p class="text-muted">Price: R <?= number_format($product['price'], 2) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-3">
                                <label for="message" class="form-label">Your Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                            <a href="<?= $product_id ? '../products/view.php?id='.$product_id : '../index.php' ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../Includes/footer.php'; ?>