<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$buyer_id = $_SESSION['user_id'];
$pid = isset($_GET['id'])?(int)$_GET['id']:0;
if(!$pid){ header("Location: browse.php"); exit; }
try {
  $stmt=$pdo->prepare("SELECT p.*,u.username seller_name,u.id seller_uid FROM products p JOIN users u ON p.seller_id=u.id WHERE p.id=? AND p.stock_quantity>0");
  $stmt->execute([$pid]); $p=$stmt->fetch();
  if(!$p){ die("<p style='text-align:center;padding:5rem'>Product not found or out of stock. <a href='browse.php'>Back to Marketplace</a></p>"); }
  if($p['seller_uid']==$buyer_id){ die("<p style='text-align:center;padding:5rem'>You cannot purchase your own listing. <a href='browse.php'>Back to Marketplace</a></p>"); }
} catch(PDOException $e){ die("DB Error"); }

$msg=$mtype=''; $qty_req=isset($_POST['quantity'])?(int)$_POST['quantity']:1;

if($_SERVER['REQUEST_METHOD']==='POST'){
  $qty   = max(1,(int)$_POST['quantity']);
  $pay   = $_POST['payment_method'];
  $addr  = trim($_POST['shipping_address']);
  $notes = trim($_POST['notes']);
  if(!in_array($pay,['gcash','maya','cod','bank_transfer'])){ $msg="Invalid payment method."; $mtype="err"; }
  elseif(empty($addr)){ $msg="Shipping address is required."; $mtype="err"; }
  elseif($qty>$p['stock_quantity']){ $msg="Quantity exceeds available stock ({$p['stock_quantity']} units)."; $mtype="err"; }
  else {
    $ref='BH-'.strtoupper(bin2hex(random_bytes(5)));
    try {
      $pdo->beginTransaction();
      $pdo->prepare("INSERT INTO transactions (buyer_id,seller_id,product_id,quantity,unit_price,total_amount,payment_method,status,reference_number,shipping_address,notes) VALUES (?,?,?,?,?,?,?,'pending',?,?,?)")
          ->execute([$buyer_id,$p['seller_uid'],$pid,$qty,$p['price'],$p['price']*$qty,$pay,$ref,$addr,$notes]);
      $txn_id=$pdo->lastInsertId();
      $st=$pdo->prepare("UPDATE products SET stock_quantity=stock_quantity-? WHERE id=? AND stock_quantity>=?");
      $st->execute([$qty,$pid,$qty]);
      if($st->rowCount()===0) throw new Exception("Stock update failed.");
      $pdo->commit();
      header("Location: order-confirmation.php?txn=$txn_id&ref=$ref"); exit;
    } catch(Exception $e){ $pdo->rollBack(); $msg="Transaction failed: ".$e->getMessage(); $mtype="err"; }
  }
}
$pay_methods=[
  'gcash'=>['label'=>'GCash','sub'=>'Instant transfer','icon'=>'📱'],
  'maya'=>['label'=>'Maya','sub'=>'Digital wallet','icon'=>'💚'],
  'cod'=>['label'=>'Cash on Delivery','sub'=>'Pay upon receipt','icon'=>'💵'],
  'bank_transfer'=>['label'=>'Bank Transfer','sub'=>'BDO / BPI / UnionBank','icon'=>'🏦'],
];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Checkout — <?php echo htmlspecialchars($p['title']) ?> | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.co-wrap{padding-top:calc(var(--nav)+3rem);padding-bottom:6rem}
.co-grid{display:grid;grid-template-columns:1fr 400px;gap:2.5rem;margin-top:2rem}
.co-card{background:var(--white);border-radius:var(--r-lg);padding:2rem;border:1px solid var(--g200);box-shadow:var(--sh-sm);margin-bottom:1.25rem}
.co-sec{font-size:.68rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--g400);padding-bottom:.75rem;border-bottom:1px solid var(--g100);margin-bottom:1.5rem}
.summary-card{background:var(--white);border-radius:var(--r-lg);padding:1.75rem;border:1px solid var(--g200);box-shadow:var(--sh-md);position:sticky;top:calc(var(--nav)+1.5rem)}
.sum-row{display:flex;justify-content:space-between;padding:.55rem 0;font-size:.9rem;color:var(--g600);border-bottom:1px solid var(--g100)}
.sum-row:last-child{border:none}
.sum-total{font-size:1.3rem;font-weight:700;color:var(--espresso);margin-top:.5rem}
.prod-thumb{display:flex;gap:.9rem;align-items:center;background:var(--cream-dark);padding:1rem;border-radius:var(--r-md);margin-bottom:1.5rem}
.prod-thumb img{width:64px;height:64px;object-fit:cover;border-radius:var(--r-sm);flex-shrink:0}
.pay-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem}
.pay-opt{border:1.5px solid var(--g200);border-radius:var(--r-md);padding:.9rem;cursor:pointer;transition:all .2s;text-align:center;position:relative}
.pay-opt input[type=radio]{position:absolute;opacity:0;width:0}
.pay-opt:has(input:checked){border-color:var(--gold);background:var(--gold-soft)}
.pay-opt .pi{font-size:1.6rem;display:block;margin-bottom:.35rem}
.pay-opt .pl{font-weight:700;font-size:.82rem;color:var(--espresso)}
.pay-opt .ps{font-size:.7rem;color:var(--g400)}
.qty-ctrl{display:flex;align-items:center;border:1.5px solid var(--g200);border-radius:var(--r-md);overflow:hidden;width:fit-content}
.qty-btn{background:var(--g100);border:none;padding:.55rem .95rem;font-size:1.1rem;cursor:pointer;font-weight:700;color:var(--espresso);transition:background .2s}
.qty-btn:hover{background:var(--g200)}
.qty-num{border:none;border-left:1.5px solid var(--g200);border-right:1.5px solid var(--g200);width:55px;text-align:center;font-weight:700;font-size:.95rem;padding:.55rem 0;margin:0}
@media(max-width:860px){.co-grid{grid-template-columns:1fr}.summary-card{position:static}}
</style>
</head><body>
<?php include 'nav.php' ?>

<div class="container co-wrap">
  <a href="product-details.php?id=<?php echo $pid ?>" style="font-size:.8rem;color:var(--g400);text-decoration:none">← Back to Product</a>
  <h1 style="font-family:var(--fd);font-size:2.8rem;font-weight:300;margin-top:.75rem">Checkout</h1>
  <p style="color:var(--g400);font-size:.9rem">Complete your purchase securely.</p>

  <?php if($msg): ?>
  <div class="<?php echo $mtype==='err'?'flash-err':'flash-ok' ?>" style="margin-top:1.5rem">⚠️ <?php echo $msg ?></div>
  <?php endif ?>

  <form action="checkout.php?id=<?php echo $pid ?>" method="POST">
  <div class="co-grid">
    <div>
      <!-- SHIPPING -->
      <div class="co-card">
        <div class="co-sec">🚚 Shipping Details</div>
        <div class="form-group">
          <label>Full Shipping Address *</label>
          <textarea name="shipping_address" rows="3" required placeholder="House No., Street, Barangay, City, Province, ZIP"><?php echo isset($_POST['shipping_address'])?htmlspecialchars($_POST['shipping_address']):'' ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Order Notes (optional)</label>
          <textarea name="notes" rows="2" placeholder="Any special instructions for the seller…"><?php echo isset($_POST['notes'])?htmlspecialchars($_POST['notes']):'' ?></textarea>
        </div>
      </div>

      <!-- PAYMENT -->
      <div class="co-card">
        <div class="co-sec">💳 Payment Method</div>
        <div class="pay-grid">
          <?php foreach($pay_methods as $val=>$m): ?>
          <label class="pay-opt">
            <input type="radio" name="payment_method" value="<?php echo $val ?>" <?php echo (!isset($_POST['payment_method'])&&$val==='gcash')||($val===($_POST['payment_method']??''))?'checked':'' ?>>
            <span class="pi"><?php echo $m['icon'] ?></span>
            <span class="pl"><?php echo $m['label'] ?></span>
            <span class="ps"><?php echo $m['sub'] ?></span>
          </label>
          <?php endforeach ?>
        </div>
      </div>

      <!-- QUANTITY -->
      <div class="co-card">
        <div class="co-sec">📦 Quantity</div>
        <div class="flex items-center gap-4">
          <div class="qty-ctrl">
            <button type="button" class="qty-btn" onclick="adjQty(-1)">−</button>
            <input type="number" name="quantity" id="qtyN" class="qty-num" value="<?php echo $qty_req ?>" min="1" max="<?php echo $p['stock_quantity'] ?>" readonly>
            <button type="button" class="qty-btn" onclick="adjQty(1)">+</button>
          </div>
          <span style="font-size:.85rem;color:var(--g400)"><?php echo $p['stock_quantity'] ?> available</span>
        </div>
      </div>
    </div>

    <!-- ORDER SUMMARY -->
    <div>
      <div class="summary-card">
        <div style="font-size:.68rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--g400);margin-bottom:1.25rem">Order Summary</div>
        <div class="prod-thumb">
          <img src="<?php echo htmlspecialchars($p['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200') ?>" alt="Bag" onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200'">
          <div>
            <div style="font-weight:700;font-size:.88rem;line-height:1.35"><?php echo htmlspecialchars($p['title']) ?></div>
            <div style="font-size:.74rem;color:var(--g400);margin-top:.2rem">by <?php echo htmlspecialchars($p['seller_name']) ?></div>
            <div style="font-weight:700;color:var(--brown);margin-top:.3rem;font-family:var(--fd);font-size:1.1rem">$<?php echo number_format($p['price'],2) ?></div>
          </div>
        </div>
        <div class="sum-row"><span>Unit Price</span><span>$<?php echo number_format($p['price'],2) ?></span></div>
        <div class="sum-row"><span>Quantity</span><span id="sumQty"><?php echo $qty_req ?></span></div>
        <div class="sum-row"><span>Shipping</span><span style="color:var(--green);font-weight:600">Free</span></div>
        <div class="sum-row sum-total"><span>Total</span><span id="sumTotal">$<?php echo number_format($p['price']*$qty_req,2) ?></span></div>
        <button type="submit" class="btn btn-buy" style="width:100%;padding:1.15rem;font-size:.92rem;margin-top:1.75rem">Confirm Purchase →</button>
        <p style="text-align:center;font-size:.72rem;color:var(--g400);margin-top:.9rem">🛡️ Protected by Baghaus Buyer Guarantee</p>
      </div>
    </div>
  </div>
  </form>
</div>

<?php include 'footer.php' ?>
<script>
window.addEventListener('scroll',()=>document.getElementById('hdr').classList.toggle('scrolled',scrollY>10));
const price=<?php echo $p['price'] ?>, maxStock=<?php echo $p['stock_quantity'] ?>;
function adjQty(d){
  const n=document.getElementById('qtyN');
  n.value=Math.max(1,Math.min(maxStock,parseInt(n.value)+d));
  document.getElementById('sumQty').textContent=n.value;
  document.getElementById('sumTotal').textContent='$'+(price*n.value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',');
}
</script>
</body></html>