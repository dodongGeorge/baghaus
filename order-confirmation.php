<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$txn_id=(int)($_GET['txn']??0);
if(!$txn_id){ header("Location: browse.php"); exit; }
try {
  $stmt=$pdo->prepare("SELECT t.*,p.title bag_title,p.image_url,p.category,buyer.username buyer_name,seller.username seller_name FROM transactions t JOIN products p ON t.product_id=p.id JOIN users buyer ON t.buyer_id=buyer.id JOIN users seller ON t.seller_id=seller.id WHERE t.id=? AND t.buyer_id=?");
  $stmt->execute([$txn_id,$_SESSION['user_id']]); $txn=$stmt->fetch();
  if(!$txn){ header("Location: browse.php"); exit; }
} catch(PDOException $e){ die("Error"); }
$pay_labels=['gcash'=>'📱 GCash','maya'=>'💚 Maya','cod'=>'💵 Cash on Delivery','bank_transfer'=>'🏦 Bank Transfer'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Order Confirmed | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.conf-wrap{max-width:660px;margin:0 auto;padding:calc(var(--nav-height) + 3rem) 1.5rem 6rem;}
.success-banner{background:linear-gradient(135deg,#059669,#10b981);color:white;border-radius:var(--radius-xl);padding:3rem;text-align:center;margin-bottom:2rem;position:relative;overflow:hidden}
.success-banner::before{content:'';position:absolute;top:-40%;right:-10%;width:300px;height:300px;background:rgba(255,255,255,.08);border-radius:50%}
.check{font-size:3.5rem;margin-bottom:1rem}
.success-banner h1{font-family:var(--font-display);font-size:2.8rem;font-weight:300;margin-bottom:.5rem}
.ref-pill{display:inline-block;background:rgba(255,255,255,.15);padding:.4rem 1.2rem;border-radius:50px;font-weight:700;letter-spacing:.12em;font-size:.82rem;margin-top:.75rem}
.det-card{background:var(--white);border-radius:var(--radius-lg);border:1px solid var(--gray-200);box-shadow:var(--shadow-md);padding:2rem;margin-bottom:1.5rem}
.det-row{display:flex;justify-content:space-between;align-items:center;padding:.7rem 0;border-bottom:1px solid var(--gray-100);font-size:.9rem}
.det-row:last-child{border:none}
.det-label{color:var(--gray-400);font-weight:500}
.det-val{font-weight:600;color:var(--espresso);text-align:right;max-width:60%}
.bag-preview{display:flex;gap:1.2rem;align-items:center;background:var(--cream-dark);padding:1rem;border-radius:var(--radius-md);margin-bottom:1.75rem}
.bag-preview img{width:72px;height:72px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0}
</style>
</head><body>
<?php include 'nav.php' ?>

<div class="conf-wrap">
  <div class="success-banner">
    <div class="check">✅</div>
    <h1>Order Confirmed!</h1>
    <p style="opacity:.85;font-size:.95rem">Your order has been placed. The seller will confirm shortly.</p>
    <div class="ref-pill">REF: <?php echo $txn['reference_number'] ?></div>
  </div>

  <div class="det-card">
    <h3 style="font-family:var(--font-display);font-size:1.4rem;font-weight:500;margin-bottom:1.5rem">Order Details</h3>
    <div class="bag-preview">
      <img src="<?php echo htmlspecialchars($txn['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200') ?>" alt="Bag" onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200'">
      <div>
        <div style="font-weight:700;font-size:.95rem"><?php echo htmlspecialchars($txn['bag_title']) ?></div>
        <div style="font-size:.8rem;color:var(--gray-400);margin-top:.25rem"><?php echo $txn['category'] ?> &bull; Sold by <?php echo htmlspecialchars($txn['seller_name']) ?></div>
      </div>
    </div>
    <div class="det-row"><span class="det-label">Quantity</span><span class="det-val"><?php echo $txn['quantity'] ?> unit(s)</span></div>
    <div class="det-row"><span class="det-label">Unit Price</span><span class="det-val">$<?php echo number_format($txn['unit_price'],2) ?></span></div>
    <div class="det-row"><span class="det-label">Total Amount</span><span class="det-val" style="color:var(--brown);font-family:var(--font-display);font-size:1.3rem">$<?php echo number_format($txn['total_amount'],2) ?></span></div>
    <div class="det-row"><span class="det-label">Payment</span><span class="det-val"><?php echo $pay_labels[$txn['payment_method']]??$txn['payment_method'] ?></span></div>
    <div class="det-row"><span class="det-label">Status</span><span class="det-val"><span class="badge badge-pending">⏳ Pending</span></span></div>
    <div class="det-row"><span class="det-label">Shipping To</span><span class="det-val" style="font-size:.84rem"><?php echo nl2br(htmlspecialchars($txn['shipping_address'])) ?></span></div>
    <?php if($txn['notes']): ?><div class="det-row"><span class="det-label">Notes</span><span class="det-val" style="font-size:.84rem"><?php echo htmlspecialchars($txn['notes']) ?></span></div><?php endif ?>
    <div class="det-row"><span class="det-label">Order Date</span><span class="det-val"><?php echo date('F j, Y \a\t g:i A',strtotime($txn['created_at'])) ?></span></div>
  </div>

  <div class="flex gap-4" style="flex-wrap:wrap">
    <a href="my-orders.php" class="btn btn-buy" style="flex:1;min-width:160px">View All Orders</a>
    <a href="browse.php"    class="btn btn-ghost" style="flex:1;min-width:160px">Continue Shopping</a>
  </div>
</div>

<?php include 'footer.php' ?>
<script>window.addEventListener('scroll',()=>document.getElementById('hdr').classList.toggle('scrolled',scrollY>10));</script>
</body></html>