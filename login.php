<?php
session_start();
include 'Includes/db.php';

if (!isset($conn)) {
    die("Connection not established. Check db.php path or connection variables.");
}

$error = "";
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - BigFiveBuys</title>
    <link rel="stylesheet" href="assets/css/login-style.css">
</head>
<body>
    <div class="container">
        <form method="post">
            <h1>Login</h1>
            <input type="email" name="email" placeholder="Email" required class="animated-input">
            <input type="password" name="password" placeholder="Password" required class="animated-input">
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p style="color:red"><?= $error ?></p>
            <?php endif; ?>
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>
</body>
</html>
