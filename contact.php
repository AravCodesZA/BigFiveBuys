<?php
include 'includes/header.php';
include 'includes/auth.php';
include 'includes/db.php';

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['to']; // passed as ?to=USER_ID

// Save new message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = htmlspecialchars($_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$sender_id', '$receiver_id', '$msg')");
}

// Mark messages as seen
mysqli_query($conn, "UPDATE messages SET seen = 1 WHERE receiver_id = '$sender_id' AND sender_id = '$receiver_id'");

// Fetch messages
$messages = mysqli_query($conn, "
    SELECT * FROM messages 
    WHERE (sender_id='$sender_id' AND receiver_id='$receiver_id') 
       OR (sender_id='$receiver_id' AND receiver_id='$sender_id')
    ORDER BY sent_at ASC
");
?>

<div class="container mt-5">
    <h3>Conversation</h3>
    <?php while ($m = mysqli_fetch_assoc($messages)): ?>
        <p><strong><?= $m['sender_id'] == $sender_id ? "You" : "Them" ?>:</strong> <?= $m['message'] ?></p>
    <?php endwhile; ?>
    <form method="post" class="mt-3">
        <textarea name="message" class="form-control mb-2" placeholder="Write a message..." required></textarea>
        <button class="btn btn-primary">Send</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>