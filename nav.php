<?php if(!isset($_SESSION)) session_start(); ?>
<header id="hdr">
  <div class="container">
    <a href="index.php" class="logo">Bag<span class="text-gradient">haus</span></a>

    <nav class="nav-links hidden-sm">
      <a href="browse.php"  class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='browse.php'?'active':'') ?>">Shop</a>
      <a href="about.php"   class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='about.php'?'active':'') ?>">About</a>
      <a href="partners.php"class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='partners.php'?'active':'') ?>">Partners</a>
      <a href="terms.php"   class="nav-link <?php echo (basename($_SERVER['PHP_SELF'])=='terms.php'?'active':'') ?>">Terms</a>
    </nav>

    <div class="nav-actions">
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="my-listings.php" class="btn btn-ghost" style="padding:.5rem 1rem">Dashboard</a>
        <a href="logout.php"      class="btn btn-outline" style="padding:.5rem 1rem">Logout</a>
      <?php else: ?>
        <a href="login.php"    class="btn btn-ghost"   style="padding:.5rem 1rem">Login</a>
        <a href="register.php" class="btn btn-buy"     style="padding:.5rem 1rem">Register</a>
      <?php endif; ?>
    </div>
  </div>
</header>