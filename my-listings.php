<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid=$_SESSION['user_id']; $uname=$_SESSION['username'];

/* ── SELLER ACTIONS ── */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'],$_POST['txn_id'])){
  $tid = (int)$_POST['txn_id'];
  try {
    $chk = $pdo->prepare("SELECT t.id,t.status FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.id=? AND p.seller_id=?");
    $chk->execute([$tid,$uid]);
    $row = $chk->fetch();
    if($row){
      $allowed = [
        'confirm'  => ['from'=>'pending',    'to'=>'confirmed'],
        'ship'     => ['from'=>'confirmed',  'to'=>'shipped'],
        'complete' => ['from'=>'shipped',    'to'=>'completed'],
      ];
      $act = $_POST['action'];
      if(isset($allowed[$act]) && $row['status']===$allowed[$act]['from']){
        $pdo->prepare("UPDATE transactions SET status=? WHERE id=?")->execute([$allowed[$act]['to'],$tid]);
      }
    }
  } catch(PDOException $e){ /* silent */ }
  header("Location: my-listings.php?action_done=".$_POST['action']);
  exit;
}

try {
  $stmt=$pdo->prepare("SELECT * FROM products WHERE seller_id=? ORDER BY created_at DESC");
  $stmt->execute([$uid]); $listings=$stmt->fetchAll();
  $n_total  = count($listings);
  $n_instock= count(array_filter($listings,fn($l)=>$l['stock_quantity']>0));
  $s=$pdo->prepare("SELECT COUNT(*),COALESCE(SUM(total_amount),0) FROM transactions WHERE seller_id=? AND status!='cancelled'");
  $s->execute([$uid]); [$n_sales,$revenue]=$s->fetch(PDO::FETCH_NUM);

  // Active orders the seller needs to act on: pending, confirmed, shipped
  $oStmt=$pdo->prepare("
    SELECT t.*, p.title bag_title, p.image_url, u.username buyer_name
    FROM transactions t
    JOIN products p ON t.product_id=p.id
    JOIN users u    ON t.buyer_id=u.id
    WHERE p.seller_id=? AND t.status IN ('pending','confirmed','shipped')
    ORDER BY FIELD(t.status,'pending','confirmed','shipped'), t.created_at ASC
  ");
  $oStmt->execute([$uid]); $active_orders=$oStmt->fetchAll();
} catch(Exception $e){ $listings=[];$n_total=0;$n_instock=0;$n_sales=0;$revenue=0;$active_orders=[]; }

$pay_labels=['gcash'=>'📱 GCash','maya'=>'💚 Maya','cod'=>'💵 COD','bank_transfer'=>'🏦 Bank Transfer'];

$order_stages = [
  'pending'   => ['label'=>'⏳ Pending',   'bg'=>'#fef9c3','c'=>'#854d0e'],
  'confirmed' => ['label'=>'✅ Confirmed', 'bg'=>'#dbeafe','c'=>'#1e40af'],
  'shipped'   => ['label'=>'🚚 Shipped',   'bg'=>'#f3e8ff','c'=>'#6b21a8'],
];

// Count per status for the section badges
$cnt = ['pending'=>0,'confirmed'=>0,'shipped'=>0];
foreach($active_orders as $o) $cnt[$o['status']]++;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>My Listings | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.orders-section { margin-bottom: 2.5rem; }
.section-heading {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 1rem;
  padding-bottom: .6rem;
  border-bottom: 1px solid var(--gray-200);
}
.section-heading h2 {
  font-family: var(--font-display);
  font-size: 1.3rem; font-weight: 400; color: var(--espresso);
}
.stage-badge {
  font-size: .68rem; font-weight: 700; padding: .2rem .65rem;
  border-radius: 50px; letter-spacing: .05em;
}

.order-row {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  padding: 1rem 1.25rem;
  display: flex; align-items: center; gap: 1.1rem;
  margin-bottom: .6rem; flex-wrap: wrap;
  transition: box-shadow .2s;
}
.order-row:hover { box-shadow: var(--shadow-md); }
.order-row img { width:54px;height:54px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0; }
.or-info { flex:1; min-width:0; }
.or-title { font-weight:700; font-size:.88rem; color:var(--espresso); }
.or-meta  { font-size:.75rem; color:var(--gray-400); margin-top:.2rem; }
.or-amount { font-family:var(--font-display);font-size:1.2rem;font-weight:600;color:var(--brown);white-space:nowrap; }

/* action buttons per stage */
.btn-confirm-order { background:#dcfce7;color:#166534;border:1.5px solid #bbf7d0;font-size:.75rem;padding:.4rem 1rem;white-space:nowrap; }
.btn-confirm-order:hover { background:#166534;color:var(--white);transform:translateY(-1px); }

.btn-ship-order { background:#dbeafe;color:#1e40af;border:1.5px solid #bfdbfe;font-size:.75rem;padding:.4rem 1rem;white-space:nowrap; }
.btn-ship-order:hover { background:#1e40af;color:var(--white);transform:translateY(-1px); }

.btn-complete-order { background:#f3e8ff;color:#6b21a8;border:1.5px solid #e9d5ff;font-size:.75rem;padding:.4rem 1rem;white-space:nowrap; }
.btn-complete-order:hover { background:#6b21a8;color:var(--white);transform:translateY(-1px); }

.empty-stage { font-size:.84rem;color:var(--gray-400);padding:.75rem 0; }
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="dash-layout">
  <aside class="sidebar-nav">
    <span class="sb-label">Selling</span>
    <a href="my-listings.php" class="sb-link active"><span>👜</span> My Listings</a>
    <a href="add-product.php" class="sb-link"><span>➕</span> List a Bag</a>
    <span class="sb-label">Buying</span>
    <a href="my-orders.php"   class="sb-link"><span>🛒</span> My Orders</a>
    <span class="sb-label">Explore</span>
    <a href="browse.php"      class="sb-link"><span>🌐</span> Marketplace</a>
    <a href="index.php"       class="sb-link"><span>🏠</span> Home</a>
  </aside>

  <main class="dash-main">
    <div class="flex justify-between items-center" style="margin-bottom:2rem;flex-wrap:wrap;gap:1rem">
      <div>
        <h1 style="font-family:var(--font-display);font-size:2.2rem;font-weight:300">My Listings</h1>
        <p style="color:var(--gray-400);font-size:.87rem;margin-top:.25rem">Manage your bag inventory and track performance.</p>
      </div>
      <a href="add-product.php" class="btn btn-sell">+ List a Bag</a>
    </div>

    <?php if(isset($_GET['deleted'])): ?><div class="flash-ok">✅ Listing deleted.</div><?php endif ?>
    <?php if(isset($_GET['listed'])): ?><div class="flash-ok">🎉 Your bag is now live!</div><?php endif ?>
    <?php if(isset($_GET['action_done'])): ?>
      <?php $done=$_GET['action_done']; ?>
      <?php if($done==='confirm'): ?><div class="flash-ok">✅ Order confirmed — buyer notified.</div>
      <?php elseif($done==='ship'): ?><div class="flash-ok">🚚 Order marked as shipped.</div>
      <?php elseif($done==='complete'): ?><div class="flash-ok">🎉 Order marked as completed.</div>
      <?php endif ?>
    <?php endif ?>
    <?php if(isset($_GET['error'])&&$_GET['error']==='unauthorized'): ?><div class="flash-err">❌ Not authorized.</div><?php endif ?>

    <!-- STATS -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:2rem">
      <div class="stat-card"><div class="stat-label">Listings</div><div class="stat-value"><?php echo $n_total ?></div></div>
      <div class="stat-card"><div class="stat-label">In Stock</div><div class="stat-value"><?php echo $n_instock ?></div></div>
      <div class="stat-card"><div class="stat-label">Total Sales</div><div class="stat-value"><?php echo number_format($n_sales) ?></div></div>
      <div class="stat-card"><div class="stat-label">Revenue</div><div class="stat-value" style="font-size:1.5rem">$<?php echo number_format($revenue,2) ?></div></div>
    </div>

    <!-- ── ORDER MANAGEMENT ── -->
    <?php if(count($active_orders)): ?>
    <div class="orders-section">

      <?php foreach(['pending','confirmed','shipped'] as $stage):
        $stage_orders = array_filter($active_orders, fn($o)=>$o['status']===$stage);
        $si = $order_stages[$stage];
      ?>
      <div style="margin-bottom:2rem">
        <div class="section-heading">
          <h2>
            <?php if($stage==='pending')   echo '📥 Incoming Orders'; ?>
            <?php if($stage==='confirmed') echo '📦 To Ship'; ?>
            <?php if($stage==='shipped')   echo '🚚 In Transit'; ?>
          </h2>
          <span class="stage-badge" style="background:<?php echo $si['bg'] ?>;color:<?php echo $si['c'] ?>">
            <?php echo $cnt[$stage] ?> <?php echo $stage ?>
          </span>
        </div>

        <?php if(count($stage_orders)): foreach($stage_orders as $o): ?>
        <div class="order-row">
          <img src="<?php echo htmlspecialchars($o['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200') ?>"
               alt="Bag" onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200'">
          <div class="or-info">
            <div class="or-title"><?php echo htmlspecialchars($o['bag_title']) ?></div>
            <div class="or-meta">
              Buyer: <strong><?php echo htmlspecialchars($o['buyer_name']) ?></strong>
              &nbsp;·&nbsp; Qty: <strong><?php echo $o['quantity'] ?></strong>
              &nbsp;·&nbsp; <?php echo $pay_labels[$o['payment_method']]??$o['payment_method'] ?>
              &nbsp;·&nbsp; <?php echo date('M j, Y g:i A', strtotime($o['created_at'])) ?>
            </div>
            <div class="or-meta" style="margin-top:.15rem">
              Ref: <strong><?php echo $o['reference_number'] ?></strong>
              &nbsp;·&nbsp; Ship to: <?php echo htmlspecialchars($o['shipping_address']) ?>
            </div>
          </div>
          <div class="or-amount">$<?php echo number_format($o['total_amount'],2) ?></div>

          <?php if($stage==='pending'): ?>
          <form method="POST" onsubmit="return confirm('Confirm order from <?php echo htmlspecialchars($o['buyer_name']) ?>?')">
            <input type="hidden" name="action"  value="confirm">
            <input type="hidden" name="txn_id"  value="<?php echo $o['id'] ?>">
            <button type="submit" class="btn btn-confirm-order">✅ Confirm</button>
          </form>

          <?php elseif($stage==='confirmed'): ?>
          <form method="POST" onsubmit="return confirm('Mark this order as shipped?')">
            <input type="hidden" name="action"  value="ship">
            <input type="hidden" name="txn_id"  value="<?php echo $o['id'] ?>">
            <button type="submit" class="btn btn-ship-order">🚚 Mark as Shipped</button>
          </form>

          <?php elseif($stage==='shipped'): ?>
          <form method="POST" onsubmit="return confirm('Mark this order as completed?')">
            <input type="hidden" name="action"  value="complete">
            <input type="hidden" name="txn_id"  value="<?php echo $o['id'] ?>">
            <button type="submit" class="btn btn-complete-order">🎉 Mark as Completed</button>
          </form>
          <?php endif ?>
        </div>
        <?php endforeach;
        else: ?>
          <p class="empty-stage">No <?php echo $stage ?> orders right now.</p>
        <?php endif ?>
      </div>
      <?php endforeach ?>

    </div>
    <?php endif ?>

    <!-- ── LISTINGS TABLE ── -->
    <?php if($n_total): ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Bag</th><th>Category</th><th>Price</th><th>Stock</th><th>Listed</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($listings as $it): ?>
          <tr>
            <td>
              <div class="flex items-center gap-4">
                <img style="width:52px;height:52px;border-radius:var(--radius-sm);object-fit:cover;flex-shrink:0"
                     src="<?php echo htmlspecialchars($it['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200') ?>"
                     alt="<?php echo htmlspecialchars($it['title']) ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200'">
                <div>
                  <div style="font-weight:600;font-size:.87rem;color:var(--espresso)"><?php echo htmlspecialchars($it['title']) ?></div>
                  <div style="font-size:.72rem;color:var(--gray-400);margin-top:.1rem"><?php echo $it['category'] ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:.83rem;color:var(--brown)"><?php echo $it['category'] ?></td>
            <td style="font-family:var(--font-display);font-size:1.1rem;font-weight:600">$<?php echo number_format($it['price'],2) ?></td>
            <td>
              <?php if($it['stock_quantity']<=0): ?><span style="color:var(--red);font-weight:700;font-size:.8rem">Out of stock</span>
              <?php elseif($it['stock_quantity']<=3): ?><span style="color:var(--warning);font-weight:700;font-size:.8rem"><?php echo $it['stock_quantity'] ?> left</span>
              <?php else: ?><span style="color:var(--green);font-weight:700;font-size:.8rem"><?php echo $it['stock_quantity'] ?> units</span>
              <?php endif ?>
            </td>
            <td style="font-size:.79rem;color:var(--gray-400)"><?php echo date('M j, Y',strtotime($it['created_at'])) ?></td>
            <td>
              <div class="action-btns">
                <a href="product-details.php?id=<?php echo $it['id'] ?>" class="btn btn-ghost"   style="padding:.3rem .75rem;font-size:.72rem">View</a>
                <a href="edit-product.php?id=<?php echo $it['id'] ?>"    class="btn btn-outline" style="padding:.3rem .75rem;font-size:.72rem">Edit</a>
                <a href="delete-product.php?id=<?php echo $it['id'] ?>"  class="btn btn-danger"  style="padding:.3rem .75rem;font-size:.72rem" onclick="return confirm('Permanently delete this listing?')">Delete</a>
              </div>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:5rem 2rem;background:var(--white);border-radius:var(--radius-lg);border:1px solid var(--gray-200)">
      <div style="font-size:2.5rem;margin-bottom:1.25rem">👜</div>
      <h3 style="font-family:var(--font-display);font-size:1.6rem;font-weight:400;color:var(--espresso);margin-bottom:.6rem">No listings yet</h3>
      <p style="color:var(--gray-400);margin-bottom:1.75rem">Start selling by listing your first bag.</p>
      <a href="add-product.php" class="btn btn-sell" style="padding:.9rem 2.5rem">List Your First Bag</a>
    </div>
    <?php endif ?>
  </main>
</div>

<?php include 'footer.php' ?>
</body></html>