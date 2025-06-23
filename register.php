<?php
session_start();
include 'Includes/db.php';

$error = '';
$success = '';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (username, email, password, role) VALUES ('$name', '$email', '$password', 'user')");
        if ($insert) {
            $success = "Account created. You can now log in.";
        } else {
            $error = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - BigFiveBuys</title>
    <link rel="stylesheet" href="assets/css/login-style.css">
    <script defer src="assets/js/password-validation.js"></script>
</head>
<body>
    <div class="container">
        <form method="post" onsubmit="return validatePassword();">
            <h1>Register</h1>
            <input type="text" name="name" placeholder="Name" required class="animated-input">
            <input type="email" name="email" placeholder="Email" required class="animated-input">
            <input type="password" name="password" id="password" placeholder="Password" required class="animated-input">
            <input type="password" id="confirm_password" placeholder="Confirm Password" required class="animated-input">
            <button type="submit">Register</button>
            <?php if ($error): ?>
                <p style="color:red"><?= $error ?></p>
            <?php elseif ($success): ?>
                <p style="color:green"><?= $success ?></p>
            <?php endif; ?>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>
