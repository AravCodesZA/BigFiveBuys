<?php
include_once 'Includes/header.php';
include_once 'Includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Please log in first.');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE users SET is_seller = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "Your request to become a seller has been submitted.";
    } else {
        $message = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply to Become a Seller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Apply to Become a Seller</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <p>To list products on our platform, you need to register as a seller.</p>
        <button type="submit" class="btn btn-primary">Submit Application</button>
    </form>

    <p class="mt-3"><a href="index.php">Back to Home</a></p>
</div>
</body>
</html>

<?php include_once 'Includes/footer.php'; ?>
