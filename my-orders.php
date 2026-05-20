<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'];

/* ── ACTIONS ── */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'],$_POST['txn_id'])){
  $tid = (int)$_POST['txn_id'];
  try {
    // Verify this order belongs to the buyer
    $chk = $pdo->prepare("SELECT id,status FROM transactions WHERE id=? AND buyer_id=?");
    $chk->execute([$tid,$uid]);
    $row = $chk->fetch();

    if($row){
      if($_POST['action']==='cancel' && in_array($row['status'],['pending','confirmed','shipped'])){
        $pdo->prepare("UPDATE transactions SET status='cancelled' WHERE id=?")->execute([$tid]);
      }
      if($_POST['action']==='delivered' && $row['status']==='shipped'){
        $pdo->prepare("UPDATE transactions SET status='completed' WHERE id=?")->execute([$tid]);
      }
    }
  } catch(PDOException $e){ /* silent */ }
  header("Location: my-orders.php?status=".($_GET['status']??'all'));
  exit;
}

$filter = $_GET['status']??'all';
try {
  $stmt=$pdo->prepare("SELECT t.*,p.title bag_title,p.image_url,p.category,seller.username seller_name FROM transactions t JOIN products p ON t.product_id=p.id JOIN users seller ON t.seller_id=seller.id WHERE t.buyer_id=? ORDER BY t.created_at DESC");
  $stmt->execute([$uid]); $orders=$stmt->fetchAll();
  $total_spent   = array_sum(array_column($orders,'total_amount'));
  $pending_count = count(array_filter($orders,fn($o)=>$o['status']==='pending'));
} catch(PDOException $e){ $orders=[];$total_spent=0;$pending_count=0; }

$filtered = $filter==='all' ? $orders : array_filter($orders,fn($o)=>$o['status']===$filter);
$statuses = [
  'pending'  =>['bg'=>'#fef9c3','c'=>'#854d0e','l'=>'⏳ Pending'],
  'confirmed'=>['bg'=>'#dbeafe','c'=>'#1e40af','l'=>'✅ Confirmed'],
  'shipped'  =>['bg'=>'#f3e8ff','c'=>'#6b21a8','l'=>'🚚 Shipped'],
  'completed'=>['bg'=>'#dcfce7','c'=>'#166534','l'=>'🎉 Completed'],
  'cancelled'=>['bg'=>'#fee2e2','c'=>'#991b1b','l'=>'❌ Cancelled'],
];
$pay_labels=['gcash'=>'📱 GCash','maya'=>'💚 Maya','cod'=>'💵 COD','bank_transfer'=>'🏦 Bank Transfer'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>My Orders | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.filter-tabs{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1.75rem}
.ftab{padding:.38rem .9rem;border-radius:50px;border:1.5px solid var(--gray-200);background:var(--white);font-size:.76rem;font-weight:600;cursor:pointer;color:var(--brown);transition:all .2s;text-decoration:none}
.ftab:hover,.ftab.on{background:var(--espresso);color:var(--cream);border-color:var(--espresso)}

.order-card{background:var(--white);border-radius:var(--radius-lg);border:1px solid var(--gray-200);box-shadow:var(--shadow-sm);margin-bottom:1rem;overflow:hidden;transition:box-shadow .2s}
.order-card:hover{box-shadow:var(--shadow-md)}
.oc-head{display:flex;justify-content:space-between;align-items:center;padding:.8rem 1.35rem;background:var(--cream-dark);border-bottom:1px solid var(--gray-200);font-size:.79rem;flex-wrap:wrap;gap:.5rem}
.oc-body{display:flex;gap:1.25rem;align-items:flex-start;padding:1.1rem 1.35rem;flex-wrap:wrap}
.oc-body img{width:66px;height:66px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0}
.oc-actions{display:flex;gap:.45rem;flex-wrap:wrap;margin-top:.6rem;align-items:center}

.btn-delivered{background:#dcfce7;color:#166534;border:1.5px solid #bbf7d0;font-size:.72rem;padding:.32rem .8rem}
.btn-delivered:hover{background:#166534;color:var(--white);transform:translateY(-1px)}
.btn-cancel-order{background:#fee2e2;color:#991b1b;border:1.5px solid #fecaca;font-size:.72rem;padding:.32rem .8rem}
.btn-cancel-order:hover{background:#991b1b;color:var(--white);transform:translateY(-1px)}
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="dash-layout">
  <aside class="sidebar-nav">
    <span class="sb-label">Selling</span>
    <a href="my-listings.php" class="sb-link"><span>👜</span> My Listings</a>
    <a href="add-product.php" class="sb-link"><span>➕</span> List a Bag</a>
    <span class="sb-label">Buying</span>
    <a href="my-orders.php"   class="sb-link active"><span>🛒</span> My Orders</a>
    <span class="sb-label">Explore</span>
    <a href="browse.php"      class="sb-link"><span>🌐</span> Marketplace</a>
    <a href="index.php"       class="sb-link"><span>🏠</span> Home</a>
  </aside>

  <main class="dash-main">
    <div class="flex justify-between items-center" style="margin-bottom:2rem;flex-wrap:wrap;gap:1rem">
      <div>
        <h1 style="font-family:var(--font-display);font-size:2.2rem;font-weight:300">My Orders</h1>
        <p style="color:var(--gray-400);font-size:.87rem;margin-top:.25rem">Track and manage your purchases.</p>
      </div>
      <a href="browse.php" class="btn btn-sell">+ Shop More</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem">
      <div class="stat-card"><div class="stat-label">Total Orders</div><div class="stat-value"><?php echo count($orders) ?></div></div>
      <div class="stat-card"><div class="stat-label">Total Spent</div><div class="stat-value" style="font-size:1.5rem">$<?php echo number_format($total_spent,2) ?></div></div>
      <div class="stat-card"><div class="stat-label">Pending</div><div class="stat-value" style="color:var(--warning)"><?php echo $pending_count ?></div></div>
    </div>

    <div class="filter-tabs">
      <?php foreach(['all'=>'All Orders','pending'=>'⏳ Pending','confirmed'=>'✅ Confirmed','shipped'=>'🚚 Shipped','completed'=>'🎉 Completed','cancelled'=>'❌ Cancelled'] as $k=>$v): ?>
        <a href="my-orders.php?status=<?php echo $k ?>" class="ftab <?php echo $filter===$k?'on':'' ?>"><?php echo $v ?></a>
      <?php endforeach ?>
    </div>

    <?php if(count($filtered)): foreach($filtered as $o):
      $s = $statuses[$o['status']] ?? $statuses['pending'];
      $can_cancel    = in_array($o['status'], ['pending','confirmed','shipped']);
      $can_delivered = $o['status'] === 'shipped';
    ?>
    <div class="order-card">
      <div class="oc-head">
        <div>
          <strong style="color:var(--espresso)">Order #<?php echo $o['id'] ?></strong>
          <span style="color:var(--gray-400);margin-left:.65rem">Ref: <?php echo $o['reference_number'] ?></span>
        </div>
        <div class="flex items-center gap-4">
          <span style="color:var(--gray-400)"><?php echo date('M j, Y',strtotime($o['created_at'])) ?></span>
          <span class="badge" style="background:<?php echo $s['bg'] ?>;color:<?php echo $s['c'] ?>"><?php echo $s['l'] ?></span>
        </div>
      </div>

      <div class="oc-body">
        <img src="<?php echo htmlspecialchars($o['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200') ?>"
             alt="Bag" onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200'">

        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:.92rem"><?php echo htmlspecialchars($o['bag_title']) ?></div>
          <div style="font-size:.77rem;color:var(--gray-400);margin:.15rem 0">by <strong><?php echo htmlspecialchars($o['seller_name']) ?></strong></div>
          <div style="display:flex;gap:1.1rem;flex-wrap:wrap;margin-top:.35rem;font-size:.77rem;color:var(--gray-400)">
            <span>Qty: <strong style="color:var(--espresso)"><?php echo $o['quantity'] ?></strong></span>
            <span>Unit: <strong style="color:var(--espresso)">$<?php echo number_format($o['unit_price'],2) ?></strong></span>
            <span><?php echo $pay_labels[$o['payment_method']]??$o['payment_method'] ?></span>
          </div>

          <!-- ACTION BUTTONS -->
          <?php if($can_cancel || $can_delivered): ?>
          <div class="oc-actions">
            <?php if($can_delivered): ?>
            <form method="POST" onsubmit="return confirm('Confirm that you have received this order?')">
              <input type="hidden" name="action"  value="delivered">
              <input type="hidden" name="txn_id"  value="<?php echo $o['id'] ?>">
              <button type="submit" class="btn btn-delivered">✅ Mark as Delivered</button>
            </form>
            <?php endif ?>
            <?php if($can_cancel): ?>
            <form method="POST" onsubmit="return confirm('Cancel this order? This cannot be undone.')">
              <input type="hidden" name="action"  value="cancel">
              <input type="hidden" name="txn_id"  value="<?php echo $o['id'] ?>">
              <button type="submit" class="btn btn-cancel-order">✕ Cancel Order</button>
            </form>
            <?php endif ?>
          </div>
          <?php endif ?>
        </div>

        <div style="text-align:right;flex-shrink:0">
          <div style="font-family:var(--font-display);font-size:1.35rem;font-weight:600;color:var(--brown)">$<?php echo number_format($o['total_amount'],2) ?></div>
          <a href="product-details.php?id=<?php echo $o['product_id'] ?>" class="btn btn-ghost" style="padding:.3rem .75rem;font-size:.73rem;margin-top:.4rem">View Item</a>
        </div>
      </div>
    </div>
    <?php endforeach; else: ?>
    <div style="text-align:center;padding:5rem 2rem;background:var(--white);border-radius:var(--radius-lg);border:1px solid var(--gray-200)">
      <div style="font-size:2.5rem;margin-bottom:1.25rem">🛍️</div>
      <h3 style="font-family:var(--font-display);font-size:1.6rem;font-weight:400;color:var(--espresso);margin-bottom:.6rem">No orders yet</h3>
      <p style="color:var(--gray-400);margin-bottom:1.75rem">You haven't placed any <?php echo $filter!=='all'?$filter:'' ?> orders.</p>
      <a href="browse.php" class="btn btn-buy">Start Shopping</a>
    </div>
    <?php endif ?>
  </main>
</div>

<?php include 'footer.php' ?>
</body></html>