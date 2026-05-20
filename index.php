<?php
require_once 'db.php'; session_start();
try {
  $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
  $total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
  $stmt = $pdo->prepare("SELECT p.*, u.username seller_name FROM products p JOIN users u ON p.seller_id=u.id ORDER BY p.created_at DESC LIMIT 8");
  $stmt->execute(); $products = $stmt->fetchAll();
} catch(Exception $e){ $total_products=0; $total_users=0; $products=[]; }
$cats = ['Tote Bags','Crossbody','Backpacks','Clutches','Shoulder Bags','Mini Bags','Luxury','Vintage'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Baghaus — Premium Bag Marketplace</title>
<link rel="stylesheet" href="style.css">
<style>
/* ── INDEX PAGE STYLES ── */
.hero {
  display:grid; grid-template-columns:1fr 1fr;
  min-height:100vh; padding-top:var(--nav); overflow:hidden;
}
.hero-left {
  display:flex; flex-direction:column; justify-content:center;
  padding:5rem 3rem 5rem max(2rem,calc((100vw - var(--container))/2 + 2rem));
}
.eyebrow {
  font-size:.68rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase;
  color:var(--gold); display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem;
}
.eyebrow::before { content:''; width:30px; height:1px; background:var(--gold); }
.hero-h1 { font-family:var(--font-display); font-size:clamp(3rem,5vw,5.5rem); font-weight:300; line-height:1.05; color:var(--espresso); margin-bottom:1.75rem; }
.hero-h1 em { font-style:italic; color:var(--gold); }
.hero-sub { font-size:.95rem; color:var(--gray-600); max-width:360px; margin-bottom:2.5rem; line-height:1.85; }
.hero-img { position:relative; overflow:hidden; }
.hero-img img { width:100%; height:100%; object-fit:cover; }
.hero-float {
  position:absolute; bottom:2rem; left:2rem;
  background:rgba(250,247,242,.95); backdrop-filter:blur(10px);
  padding:1rem 1.5rem; border-radius:var(--radius-md);
  border:1px solid var(--gray-200); box-shadow:var(--shadow-md);
}
.hero-float .f-label { font-size:.62rem; text-transform:uppercase; letter-spacing:.14em; color:var(--gold); font-weight:700; margin-bottom:.2rem; }
.hero-float .f-val   { font-family:var(--font-display); font-size:1.15rem; font-weight:600; color:var(--espresso); }

.ticker { background:var(--espresso); color:var(--gold-light); padding:.75rem 0; overflow:hidden; white-space:nowrap; }
.ticker-inner { display:inline-flex; animation:marquee 28s linear infinite; }
.ticker-inner span { padding:0 2rem; font-size:.68rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; }
.ticker-inner span::after { content:'✦'; margin-left:2rem; color:var(--gold); }

.pill-row { display:flex; gap:.55rem; overflow-x:auto; padding-bottom:.35rem; scrollbar-width:none; flex-wrap:wrap; }
.pill-row::-webkit-scrollbar { display:none; }
.pill {
  flex-shrink:0; padding:.45rem 1.2rem;
  background:var(--white); border:1.5px solid var(--gray-200);
  border-radius:50px; font-size:.78rem; font-weight:600;
  color:var(--brown); transition:all .2s; white-space:nowrap;
}
.pill:hover,.pill.on { background:var(--espresso); color:var(--cream); border-color:var(--espresso); }

.stats-band { background:var(--cream-dark); border-top:1px solid var(--gray-200); border-bottom:1px solid var(--gray-200); padding:2.5rem 0; }
.stats-band .container { display:grid; grid-template-columns:repeat(3,1fr); }
.band-stat { text-align:center; padding:0 1rem; }
.band-stat + .band-stat { border-left:1px solid var(--gray-200); }
.band-num { font-family:var(--font-display); font-size:2.25rem; font-weight:600; color:var(--espresso); line-height:1; }
.band-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.14em; color:var(--gray-400); margin-top:.3rem; font-weight:600; }

.sec-eye { font-size:.68rem; font-weight:700; letter-spacing:.2em; text-transform:uppercase; color:var(--gold); display:block; margin-bottom:.5rem; }
.sec-h   { font-family:var(--font-display); font-size:clamp(1.9rem,3vw,2.6rem); font-weight:300; color:var(--espresso); line-height:1.1; }

.pgrid { display:grid; grid-template-columns:repeat(auto-fill,minmax(255px,1fr)); gap:1.5rem; }

.trust-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }
.t-card { padding:2rem; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--gray-200); transition:all .25s; }
.t-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); }

.cta-box {
  background:var(--espresso); border-radius:var(--radius-xl);
  padding:4rem; display:grid; grid-template-columns:1fr auto;
  gap:2rem; align-items:center;
}
.cta-box h2 { font-family:var(--font-display); font-size:clamp(1.7rem,2.8vw,2.8rem); font-weight:300; color:var(--cream); line-height:1.1; }
.cta-box h2 em { color:var(--gold-light); font-style:italic; }
.cta-box p { color:rgba(255,255,255,.55); margin-top:.75rem; font-size:.9rem; }

@media(max-width:900px){
  .hero { grid-template-columns:1fr; min-height:auto; }
  .hero-img { height:55vw; min-height:240px; }
  .hero-left { padding:4rem 1.5rem 3rem; }
  .trust-grid { grid-template-columns:1fr; }
  .cta-box { grid-template-columns:1fr; }
}
</style>
</head><body>

<?php include 'nav.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-left">
    <div class="eyebrow">The Bag Marketplace</div>
    <h1 class="hero-h1">Every Bag<br>Tells a <em>Story.</em></h1>
    <p class="hero-sub">Discover handpicked totes, crossbodies, luxury handbags and more — from sellers who care about craft.</p>
    <div class="flex gap-4" style="flex-wrap:wrap">
      <a href="browse.php" class="btn btn-buy" style="padding:.9rem 2rem">Shop the Collection</a>
      <a href="<?php echo isset($_SESSION['user_id'])?'add-product.php':'register.php' ?>" class="btn btn-outline" style="padding:.9rem 2rem">
        <?php echo isset($_SESSION['user_id'])?'List a Bag':'Start Selling' ?>
      </a>
    </div>
  </div>
  <div class="hero-img">
    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=960&q=80" alt="Luxury bag collection" loading="eager">
    <div class="hero-float">
      <div class="f-label">Listings</div>
      <div class="f-val"><?php echo number_format($total_products+120) ?> Bags Listed</div>
    </div>
  </div>
</section>

<!-- TICKER -->
<div class="ticker"><div class="ticker-inner">
  <?php for($i=0;$i<4;$i++) foreach($cats as $c) echo "<span>$c</span>"; ?>
</div></div>

<!-- CATEGORIES -->
<section class="py-20">
  <div class="container">
    <div class="flex justify-between items-end" style="margin-bottom:1.75rem">
      <div><span class="sec-eye">Browse by Style</span><h2 class="sec-h">Find Your <em style="font-style:italic;color:var(--gold)">Perfect Bag</em></h2></div>
      <a href="browse.php" class="btn btn-ghost hidden-sm">View All →</a>
    </div>
    <div class="pill-row">
      <a href="browse.php" class="pill on">All Bags</a>
      <?php foreach($cats as $c): ?><a href="browse.php?category=<?php echo urlencode($c) ?>" class="pill"><?php echo $c ?></a><?php endforeach ?>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-band">
  <div class="container">
    <div class="band-stat"><div class="band-num"><?php echo number_format($total_users+800) ?>+</div><div class="band-lbl">Verified Sellers</div></div>
    <div class="band-stat"><div class="band-num"><?php echo number_format($total_products+3200) ?></div><div class="band-lbl">Bags Available</div></div>
    <div class="band-stat"><div class="band-num">100%</div><div class="band-lbl">Buyer Protected</div></div>
  </div>
</div>

<!-- FEATURED LISTINGS -->
<section class="py-20">
  <div class="container">
    <div class="flex justify-between items-end" style="margin-bottom:2.5rem">
      <div><span class="sec-eye">Hand-picked Selections</span><h2 class="sec-h">Featured <em style="font-style:italic;color:var(--gold)">Listings</em></h2></div>
      <a href="browse.php" class="btn btn-buy hidden-sm">Browse All →</a>
    </div>
    <?php if(count($products)): ?>
    <div class="pgrid">
      <?php foreach($products as $p): ?>
      <a href="product-details.php?id=<?php echo $p['id'] ?>" class="product-card">
        <div class="card-img" style="height:240px">
          <span class="card-tag"><?php echo htmlspecialchars($p['category']) ?></span>
          <img src="<?php echo htmlspecialchars($p['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600') ?>"
               alt="<?php echo htmlspecialchars($p['title']) ?>"
               onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600'">
          <div class="card-hover-btn"><span>View Details</span></div>
        </div>
        <div class="card-body">
          <div class="card-price">$<?php echo number_format($p['price'],2) ?></div>
          <div class="card-title"><?php echo htmlspecialchars($p['title']) ?></div>
          <div class="card-seller">by <?php echo htmlspecialchars($p['seller_name']) ?></div>
        </div>
      </a>
      <?php endforeach ?>
    </div>
    <?php else: ?>
    <!-- Placeholder cards shown when no listings exist yet -->
    <div class="pgrid">
      <?php foreach([
        ['img'=>'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600','cat'=>'Luxury',    'title'=>'Classic Leather Handbag', 'price'=>'12,500.00'],
        ['img'=>'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600','cat'=>'Crossbody','title'=>'Everyday Crossbody Bag',  'price'=>'3,200.00'],
        ['img'=>'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=600','cat'=>'Tote Bags','title'=>'Canvas Tote — Cream',       'price'=>'1,800.00'],
        ['img'=>'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?w=600','cat'=>'Clutches', 'title'=>'Evening Clutch Bag',        'price'=>'2,400.00'],
      ] as $ph): ?>
      <a href="register.php" class="product-card">
        <div class="card-img" style="height:240px">
          <span class="card-tag"><?php echo $ph['cat'] ?></span>
          <img src="<?php echo $ph['img'] ?>" alt="<?php echo $ph['title'] ?>">
          <div class="card-hover-btn"><span>List Yours</span></div>
        </div>
        <div class="card-body">
          <div class="card-price">$<?php echo $ph['price'] ?></div>
          <div class="card-title"><?php echo $ph['title'] ?></div>
          <div class="card-seller" style="color:var(--gold);font-weight:600;font-size:.72rem">✦ List yours today</div>
        </div>
      </a>
      <?php endforeach ?>
    </div>
    <p style="text-align:center;margin-top:1.75rem;font-size:.85rem;color:var(--gray-400)">Be the first seller — <a href="register.php" style="color:var(--brown);font-weight:600">list your bag now →</a></p>
    <?php endif ?>
  </div>
</section>

<!-- WHY BAGHAUS -->
<section class="py-20" style="background:var(--cream-dark)">
  <div class="container">
    <div class="text-center" style="margin-bottom:3rem">
      <span class="sec-eye">Why Baghaus</span>
      <h2 class="sec-h">Shop with <em style="font-style:italic;color:var(--gold)">Confidence</em></h2>
    </div>
    <div class="trust-grid">
      <div class="t-card">
        <div style="font-size:1.75rem;margin-bottom:1rem">🔒</div>
        <div style="font-family:var(--font-display);font-size:1.25rem;font-weight:600;margin-bottom:.6rem">Secure Transactions</div>
        <p style="color:var(--gray-600);font-size:.88rem;line-height:1.8">Every purchase is protected. Your money is safe until you confirm receipt.</p>
      </div>
      <div class="t-card">
        <div style="font-size:1.75rem;margin-bottom:1rem">✅</div>
        <div style="font-family:var(--font-display);font-size:1.25rem;font-weight:600;margin-bottom:.6rem">Verified Sellers</div>
        <p style="color:var(--gray-600);font-size:.88rem;line-height:1.8">All merchants are reviewed. Ratings and history visible on every listing.</p>
      </div>
      <div class="t-card">
        <div style="font-size:1.75rem;margin-bottom:1rem">📱</div>
        <div style="font-family:var(--font-display);font-size:1.25rem;font-weight:600;margin-bottom:.6rem">Local Payment Options</div>
        <p style="color:var(--gray-600);font-size:.88rem;line-height:1.8">Pay via GCash, Maya, Bank Transfer, or Cash on Delivery.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-20">
  <div class="container">
    <div class="cta-box">
      <div>
        <h2>Turn your collection<br>into <em>income.</em></h2>
        <p>List your bags for free. Reach thousands of buyers across the Philippines and beyond.</p>
      </div>
      <div class="flex gap-4" style="flex-direction:column;align-items:flex-end;flex-shrink:0;flex-wrap:wrap">
        <?php if(!isset($_SESSION['user_id'])): ?>
          <a href="register.php" class="btn btn-sell" style="padding:.9rem 2rem">Create Free Account</a>
          <a href="browse.php" class="btn" style="padding:.9rem 2rem;border:1.5px solid rgba(255,255,255,.25);color:var(--cream);background:transparent">Browse Bags</a>
        <?php else: ?>
          <a href="add-product.php" class="btn btn-sell" style="padding:.9rem 2rem">List a Bag Now</a>
          <a href="browse.php" class="btn" style="padding:.9rem 2rem;border:1.5px solid rgba(255,255,255,.25);color:var(--cream);background:transparent">Browse Bags</a>
        <?php endif ?>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php' ?>
<script>
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('active'); observer.unobserve(e.target); } });
}, { threshold: 0.08 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body></html>