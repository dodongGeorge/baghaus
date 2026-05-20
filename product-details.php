<?php
require_once 'db.php'; session_start();
$pid = isset($_GET['id'])?(int)$_GET['id']:0;
if(!$pid){ header("Location: browse.php"); exit; }
try {
  $stmt=$pdo->prepare("SELECT p.*,u.username seller_name,u.id seller_uid,u.created_at seller_since FROM products p JOIN users u ON p.seller_id=u.id WHERE p.id=?");
  $stmt->execute([$pid]); $p=$stmt->fetch();
  if(!$p){ header("Location: browse.php"); exit; }
  $rel=$pdo->prepare("SELECT p.*,u.username seller_name FROM products p JOIN users u ON p.seller_id=u.id WHERE p.category=? AND p.id!=? AND p.stock_quantity>0 ORDER BY RAND() LIMIT 4");
  $rel->execute([$p['category'],$pid]); $related=$rel->fetchAll();
  $sc=$pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id=?");
  $sc->execute([$p['seller_uid']]); $seller_count=$sc->fetchColumn();
} catch(Exception $e){ header("Location: browse.php"); exit; }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo htmlspecialchars($p['title']) ?> | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.detail-wrap { padding-top:calc(var(--nav)+2.5rem); padding-bottom:5rem; }
.breadcrumb  { display:flex; gap:.35rem; align-items:center; font-size:.77rem; color:var(--g400); margin-bottom:2rem; flex-wrap:wrap; }
.breadcrumb a { color:var(--g400); transition:color .2s; }
.breadcrumb a:hover { color:var(--espresso); }
.breadcrumb .sep { color:var(--g300); }
.detail-layout { display:grid; grid-template-columns:1fr 440px; gap:3.5rem; align-items:start; }
.main-img { border-radius:var(--r-xl); overflow:hidden; aspect-ratio:4/3; background:var(--g100); box-shadow:var(--sh-lg); }
.main-img img { width:100%; height:100%; object-fit:cover; }
.cat-pill { display:inline-block; background:var(--gold-soft); border:1px solid var(--gold-lt); color:var(--brown); padding:.28rem .9rem; border-radius:50px; font-size:.66rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; margin-bottom:1rem; }
.prod-title { font-family:var(--fd); font-size:2.4rem; font-weight:300; line-height:1.08; color:var(--espresso); margin-bottom:.9rem; }
.prod-price { font-family:var(--fd); font-size:2.8rem; font-weight:600; color:var(--espresso); line-height:1; margin-bottom:1.5rem; }
.prod-price small { font-size:1rem; font-weight:300; color:var(--g400); margin-left:.25rem; }
.stock-row  { display:flex; align-items:center; gap:.55rem; margin-bottom:1.5rem; font-size:.86rem; font-weight:600; }
.dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.divider { height:1px; background:var(--g200); margin:1.5rem 0; }
.meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.5rem; }
.meta-box { background:var(--cream-dark); padding:.85rem 1rem; border-radius:var(--r-md); }
.meta-box .ml { font-size:.6rem; text-transform:uppercase; letter-spacing:.1em; color:var(--g400); margin-bottom:.2rem; font-weight:600; }
.meta-box .mv { font-weight:700; font-size:.86rem; color:var(--espresso); }
.action-box { background:var(--white); border-radius:var(--r-lg); border:1px solid var(--g200); padding:1.5rem; box-shadow:var(--sh-md); margin-top:1.5rem; }
.seller-box { background:var(--cream-dark); border-radius:var(--r-lg); border:1px solid var(--g200); padding:1.25rem; margin-top:1.25rem; display:flex; align-items:center; gap:.9rem; }
.seller-avatar { width:44px; height:44px; border-radius:50%; background:var(--espresso); display:flex; align-items:center; justify-content:center; color:var(--cream); font-weight:700; font-size:.95rem; font-family:var(--fd); flex-shrink:0; }
.trust-pills { display:flex; gap:.85rem; flex-wrap:wrap; margin-top:1.1rem; }
.trust-pill  { font-size:.73rem; color:var(--g400); display:flex; align-items:center; gap:.3rem; }
.rel-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(205px,1fr)); gap:1.25rem; margin-top:2.5rem; }
@media(max-width:960px){ .detail-layout{grid-template-columns:1fr} }
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="container detail-wrap">
  <div class="breadcrumb">
    <a href="index.php">Home</a><span class="sep">/</span>
    <a href="browse.php">Bags</a><span class="sep">/</span>
    <a href="browse.php?category=<?php echo urlencode($p['category']) ?>"><?php echo htmlspecialchars($p['category']) ?></a><span class="sep">/</span>
    <span style="color:var(--espresso)"><?php echo htmlspecialchars($p['title']) ?></span>
  </div>

  <div class="detail-layout">
    <!-- IMAGE SIDE -->
    <div>
      <div class="main-img">
        <img src="<?php echo htmlspecialchars($p['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=900') ?>"
             alt="<?php echo htmlspecialchars($p['title']) ?>"
             onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=900'">
      </div>
      <div class="seller-box">
        <div class="seller-avatar"><?php echo strtoupper(substr($p['seller_name'],0,1)) ?></div>
        <div>
          <div style="font-weight:700;font-size:.9rem"><?php echo htmlspecialchars($p['seller_name']) ?></div>
          <div style="font-size:.75rem;color:var(--g400);margin-top:.15rem">
            <?php echo $seller_count ?> listing<?php echo $seller_count!=1?'s':'' ?> &bull; Member since <?php echo date('M Y',strtotime($p['seller_since'])) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- INFO SIDE -->
    <div>
      <span class="cat-pill"><?php echo htmlspecialchars($p['category']) ?></span>
      <h1 class="prod-title"><?php echo htmlspecialchars($p['title']) ?></h1>
      <div class="prod-price">$<?php echo number_format($p['price'],2) ?> <small>USD</small></div>

      <?php
      $stock=$p['stock_quantity'];
      if($stock<=0)     { $dotcol='#ef4444'; $txt='Out of Stock'; }
      elseif($stock<=3) { $dotcol='#f59e0b'; $txt="Only $stock left"; }
      else              { $dotcol='#22c55e'; $txt="In Stock ($stock available)"; }
      ?>
      <div class="stock-row">
        <div class="dot" style="background:<?php echo $dotcol ?>"></div>
        <span><?php echo $txt ?></span>
      </div>

      <div class="divider"></div>
      <p style="font-size:.92rem;color:var(--g600);line-height:1.85"><?php echo nl2br(htmlspecialchars($p['description'])) ?></p>
      <div class="divider"></div>

      <div class="meta-grid">
        <div class="meta-box"><div class="ml">Category</div><div class="mv"><?php echo htmlspecialchars($p['category']) ?></div></div>
        <div class="meta-box"><div class="ml">Listed</div><div class="mv"><?php echo date('M j, Y',strtotime($p['created_at'])) ?></div></div>
      </div>

      <div class="action-box">
        <?php if($stock<=0): ?>
          <button class="btn w-full" style="padding:1rem;background:var(--g200);color:var(--g400);cursor:not-allowed" disabled>Out of Stock</button>
        <?php elseif(!isset($_SESSION['user_id'])): ?>
          <a href="login.php" class="btn btn-buy" style="width:100%;padding:1rem;display:flex">Login to Purchase</a>
          <p style="text-align:center;font-size:.75rem;color:var(--g400);margin-top:.6rem">Don't have an account? <a href="register.php" style="color:var(--gold);font-weight:600">Register free</a></p>
        <?php elseif($_SESSION['user_id']==$p['seller_uid']): ?>
          <a href="edit-product.php?id=<?php echo $pid ?>" class="btn btn-outline" style="width:100%;padding:1rem;display:flex;margin-bottom:.6rem">Edit This Listing</a>
          <a href="delete-product.php?id=<?php echo $pid ?>" class="btn btn-danger" style="width:100%;padding:.85rem;display:flex" onclick="return confirm('Delete this listing? This cannot be undone.')">Delete Listing</a>
        <?php else: ?>
          <a href="checkout.php?id=<?php echo $pid ?>" class="btn btn-buy" style="width:100%;padding:1rem;font-size:.92rem;display:flex">Purchase This Bag →</a>
          <p style="text-align:center;font-size:.73rem;color:var(--g400);margin-top:.65rem">🛡️ Protected by Baghaus Buyer Guarantee</p>
        <?php endif ?>
      </div>

      <div class="trust-pills">
        <div class="trust-pill">🔒 Secure Checkout</div>
        <div class="trust-pill">📱 GCash / Maya</div>
        <div class="trust-pill">💵 COD Available</div>
        <div class="trust-pill">🏦 Bank Transfer</div>
      </div>
    </div>
  </div>

  <!-- RELATED -->
  <?php if(count($related)): ?>
  <div style="margin-top:5rem">
    <h2 style="font-family:var(--fd);font-size:2rem;font-weight:300;margin-bottom:.35rem">More <em style="font-style:italic"><?php echo htmlspecialchars($p['category']) ?></em></h2>
    <p style="color:var(--g400);font-size:.86rem;margin-bottom:2rem">You might also like these.</p>
    <div class="rel-grid">
      <?php foreach($related as $r): ?>
      <a href="product-details.php?id=<?php echo $r['id'] ?>" class="product-card">
        <div class="card-img" style="height:200px">
          <img src="<?php echo htmlspecialchars($r['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500') ?>"
               alt="<?php echo htmlspecialchars($r['title']) ?>"
               onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500'" loading="lazy">
        </div>
        <div class="card-body">
          <div class="card-price">$<?php echo number_format($r['price'],2) ?></div>
          <div class="card-title"><?php echo htmlspecialchars($r['title']) ?></div>
          <div class="card-seller">by <?php echo htmlspecialchars($r['seller_name']) ?></div>
        </div>
      </a>
      <?php endforeach ?>
    </div>
  </div>
  <?php endif ?>
</div>

<?php include 'footer.php' ?>
</body></html>