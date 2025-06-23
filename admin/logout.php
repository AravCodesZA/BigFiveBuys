<?php
session_start();
session_destroy();
header("Location: index.php");
?>
<?php
if (session_status() == PHP_SESSION_NONE) session_start();

$relative_path = (strpos($_SERVER['PHP_SELF'], '/Admin/') !== false) ? '../' : '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="<?php echo $relative_path; ?>index.php">BigFiveBuys</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item"><a class="nav-link" href="<?php echo $relative_path; ?>browse.php">Browse</a></li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $relative_path; ?>my_listings.php">My Listings</a></li>
        <?php
          include_once $relative_path . 'Includes/db.php';
          $uid = $_SESSION['user_id'];
          $q = mysqli_query($conn, "SELECT can_sell FROM users WHERE id = $uid");
          $res = mysqli_fetch_assoc($q);
          if (!$res['can_sell']) {
            echo '<li class="nav-item"><a class="nav-link" href="mailto:admin@bigfivebuys.com?subject=Request to Sell on BigFiveBuys">Apply to Sell</a></li>';
          }
        ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $relative_path; ?>logout.php">Logout</a></li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $relative_path; ?>login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $relative_path; ?>register.php">Register</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
