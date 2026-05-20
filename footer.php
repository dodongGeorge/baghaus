<footer class="footer-premium">
  <div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:3rem;align-items:start">
      <div>
        <div class="logo" style="color:var(--cream);margin-bottom:1rem">Bag<span style="color:var(--gold)">haus</span></div>
        <p style="font-size:.88rem;line-height:1.8;max-width:280px">A marketplace for bags of every style — from everyday totes to luxury handbags. Buy and sell with confidence.</p>
      </div>
      <div>
        <div style="font-size:.67rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem">Marketplace</div>
        <a href="browse.php"   class="footer-link">Browse Bags</a>
        <a href="add-product.php" class="footer-link">List a Bag</a>
        <a href="my-listings.php" class="footer-link">My Listings</a>
        <a href="my-orders.php"   class="footer-link">My Orders</a>
      </div>
      <div>
        <div style="font-size:.67rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem">Company</div>
        <a href="about.php"    class="footer-link">About Us</a>
        <a href="partners.php" class="footer-link">Partners</a>
        <a href="terms.php"    class="footer-link">Terms of Service</a>
      </div>
      <div>
        <div style="font-size:.67rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem">Account</div>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="my-listings.php" class="footer-link">Dashboard</a>
          <a href="logout.php"      class="footer-link">Logout</a>
        <?php else: ?>
          <a href="login.php"    class="footer-link">Login</a>
          <a href="register.php" class="footer-link">Create Account</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?php echo date('Y') ?> Baghaus. All rights reserved.</span>
      <span style="color:var(--gold-light)">Built for bag lovers 👜</span>
    </div>
  </div>
</footer>
<script>
(function(){
  var hdr=document.getElementById('hdr');
  if(hdr){
    window.addEventListener('scroll',function(){
      hdr.style.boxShadow=window.scrollY>20?'0 4px 20px rgba(44,26,14,0.12)':'';
    });
  }
})();
</script>