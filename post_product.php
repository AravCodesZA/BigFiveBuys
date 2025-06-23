<?php
include 'includes/header.php';
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = substr(mysqli_real_escape_string($conn, $_POST['description']), 0, 8000);
    $price = floatval($_POST['price']);
    $user_id = $_SESSION['user_id'];

    $imageNames = [];
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $name = basename($_FILES['images']['name'][$key]);
        $targetPath = "uploads/" . time() . "_" . $name;
        if (move_uploaded_file($tmp_name, $targetPath)) {
            $imageNames[] = basename($targetPath);
        }
    }

    $images_json = json_encode($imageNames);
    $query = "INSERT INTO products (user_id, title, description, price, images) VALUES ('$user_id', '$title', '$description', '$price', '$images_json')";
    mysqli_query($conn, $query);
    header("Location: my_listings.php");
    exit;
}
?>

<div class="container mt-4">
    <h2>Post a Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description (max 8000 chars)</label>
            <textarea name="description" class="form-control" maxlength="8000" required></textarea>
        </div>
        <div class="form-group">
            <label>Price (ZAR)</label>
            <input name="price" type="number" step="0.01" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Product Images (up to 15)</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
        </div>
        <button type="submit" class="btn btn-success">Submit Product</button>
    </form>
</div>

<?php include 'Includes/footer.php'; ?>
