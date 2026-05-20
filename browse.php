<?php
require_once 'db.php'; session_start();
$cats = ['Tote Bags','Crossbody','Backpacks','Clutches','Shoulder Bags','Mini Bags','Luxury','Vintage','Satchel','Bucket Bags'];
$active_cat = isset($_GET['category']) ? trim($_GET['category']) : '';
$search     = isset($_GET['q'])        ? trim($_GET['q'])        : '';
$sort       = isset($_GET['sort'])     ? $_GET['sort']           : 'newest';
$min_price  = isset($_GET['min'])      ? (float)$_GET['min']     : 0;
$max_price  = isset($_GET['max'])      ? (float)$_GET['max']     : 0;
$page       = max(1,(int)($_GET['page']??1));
$per_page   = 12; $offset = ($page-1)*$per_page;

$where=[]; $params=[];
$where[]="p.stock_quantity>0";
if($active_cat){ $where[]="p.category=?"; $params[]=$active_cat; }
if($search)    { $where[]="(p.title LIKE ? OR p.description LIKE ?)"; $params[]= "%$search%"; $params[]= "%$search%"; }
if($min_price) { $where[]="p.price>=?"; $params[]=$min_price; }
if($max_price) { $where[]="p.price<=?"; $params[]=$max_price; }
$wsql = implode(' AND ',$where);
$osql = ['newest'=>'p.created_at DESC','price_asc'=>'p.price ASC','price_desc'=>'p.price DESC'][$sort] ?? 'p.created_at DESC';

try {
  $cnt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $wsql");
  $cnt->execute($params); $total=(int)$cnt->fetchColumn();
  $total_pages = max(1,ceil($total/$per_page));
  $stmt = $pdo->prepare("SELECT p.*,u.username seller_name FROM products p JOIN users u ON p.seller_id=u.id WHERE $wsql ORDER BY $osql LIMIT $per_page OFFSET $offset");
  $stmt->execute($params); $products=$stmt->fetchAll();
} catch(PDOException $e){ $products=[];$total=0;$total_pages=1; }

function qs($extra=[]){
  $p=array_merge(['category'=>$_GET['category']??'','q'=>$_GET['q']??'','sort'=>$_GET['sort']??'newest','min'=>$_GET['min']??'','max'=>$_GET['max']??''],$extra);
  return http_build_query(array_filter($p,'strlen'));
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $active_cat?"$active_cat — ":''; ?>Shop Bags | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.browse-wrap { display:grid; grid-template-columns:240px 1fr; gap:2rem; padding-top:calc(var(--nav) + 2.5rem); padding-bottom:5rem; align-items:start; }
.sidebar { position:sticky; top:calc(var(--nav) + 1.5rem); }
.sb-card { background:var(--white); border-radius:var(--r-lg); border:1px solid var(--g200); padding:1.5rem; margin-bottom:1rem; box-shadow:var(--sh-sm); }
.sb-lbl  { font-size:.65rem; font-weight:700; letter-spacing:.18em; text-transform:uppercase; color:var(--g400); margin-bottom:.85rem; display:block; }
.cat-list { list-style:none; }
.cat-list li a { display:flex; justify-content:space-between; align-items:center; padding:.45rem .65rem; border-radius:var(--r-sm); font-size:.86rem; font-weight:500; color:var(--brown); transition:all .2s; }
.cat-list li a:hover,.cat-list li a.on { background:var(--espresso); color:var(--cream); padding-left:.9rem; }
.sort-tabs { display:flex; gap:.35rem; flex-wrap:wrap; }
.sort-tab { padding:.38rem .9rem; border-radius:50px; border:1.5px solid var(--g200); background:var(--white); font-size:.75rem; font-weight:600; cursor:pointer; color:var(--brown); transition:all .2s; text-decoration:none; }
.sort-tab:hover,.sort-tab.on { background:var(--espresso); color:var(--cream); border-color:var(--espresso); }
.pgrid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.25rem; }
.page-btn { width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:var(--r-sm); border:1.5px solid var(--g200); background:var(--white); font-size:.83rem; font-weight:600; color:var(--brown); text-decoration:none; transition:all .2s; }
.page-btn:hover,.page-btn.on { background:var(--espresso); color:var(--cream); border-color:var(--espresso); }
@media(max-width:900px){ .browse-wrap{grid-template-columns:1fr} .sidebar{position:static} }
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="container browse-wrap">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sb-card">
      <span class="sb-lbl">Categories</span>
      <ul class="cat-list">
        <li><a href="browse.php?<?php echo qs(['category'=>'','page'=>1]) ?>" class="<?php echo !$active_cat?'on':'' ?>">All Bags</a></li>
        <?php foreach($cats as $c): ?>
        <li><a href="browse.php?<?php echo qs(['category'=>$c,'page'=>1]) ?>" class="<?php echo $active_cat===$c?'on':'' ?>"><?php echo $c ?></a></li>
        <?php endforeach ?>
      </ul>
    </div>

    <div class="sb-card">
      <span class="sb-lbl">Price Range</span>
      <div style="display:flex;gap:.4rem;align-items:center;margin-bottom:.75rem">
        <input type="number" id="minP" placeholder="Min $" value="<?php echo $min_price?:'' ?>" style="padding:.55rem .7rem;font-size:.83rem">
        <span style="color:var(--g400);flex-shrink:0;font-size:.9rem">—</span>
        <input type="number" id="maxP" placeholder="Max $" value="<?php echo $max_price?:'' ?>" style="padding:.55rem .7rem;font-size:.83rem">
      </div>
      <button onclick="applyPrice()" class="btn btn-buy" style="width:100%;padding:.6rem">Apply Filter</button>
      <?php if($min_price||$max_price): ?>
        <a href="browse.php?<?php echo qs(['min'=>'','max'=>'']) ?>" class="btn btn-ghost" style="width:100%;margin-top:.5rem;padding:.55rem;text-align:center;display:flex;justify-content:center">Clear Price</a>
      <?php endif ?>
    </div>

    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="add-product.php" class="btn btn-sell" style="width:100%;padding:.85rem;justify-content:center;display:flex">+ List a Bag</a>
    <?php else: ?>
      <a href="register.php" class="btn btn-buy" style="width:100%;padding:.85rem;justify-content:center;display:flex">Start Selling Free</a>
    <?php endif ?>
  </aside>

  <!-- MAIN -->
  <main>
    <div style="margin-bottom:1.5rem">
      <h1 style="font-family:var(--fd);font-size:2.4rem;font-weight:300;line-height:1.1;margin-bottom:.25rem">
        <?php echo $active_cat?htmlspecialchars($active_cat):'All Bags' ?>
        <?php if($search): ?><em style="font-style:italic;color:var(--gold)"> "<?php echo htmlspecialchars($search) ?>"</em><?php endif ?>
      </h1>
      <p style="font-size:.83rem;color:var(--g400)"><?php echo number_format($total) ?> bag<?php echo $total!=1?'s':'' ?> found</p>
    </div>

    <!-- SEARCH -->
    <form method="GET" action="browse.php" style="display:flex;gap:.6rem;margin-bottom:1.5rem">
      <?php if($active_cat): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($active_cat) ?>"><?php endif ?>
      <input type="text" name="q" placeholder="Search bags…" value="<?php echo htmlspecialchars($search) ?>" style="flex:1">
      <button type="submit" class="btn btn-buy" style="padding:.8rem 1.5rem">Search</button>
      <?php if($search||$active_cat||$min_price||$max_price): ?>
        <a href="browse.php" class="btn btn-ghost" style="padding:.8rem 1.1rem">Clear</a>
      <?php endif ?>
    </form>

    <!-- SORT BAR -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:.65rem">
      <span style="font-size:.82rem;color:var(--g400)"><?php echo number_format($total) ?> results</span>
      <div class="sort-tabs">
        <?php foreach(['newest'=>'Newest','price_asc'=>'Price ↑','price_desc'=>'Price ↓'] as $k=>$v): ?>
          <a href="browse.php?<?php echo qs(['sort'=>$k,'page'=>1]) ?>" class="sort-tab <?php echo $sort===$k?'on':'' ?>"><?php echo $v ?></a>
        <?php endforeach ?>
      </div>
    </div>

    <?php if(count($products)): ?>
    <div class="pgrid">
      <?php foreach($products as $p): ?>
      <a href="product-details.php?id=<?php echo $p['id'] ?>" class="product-card">
        <div class="card-img" style="height:220px">
          <span class="card-tag"><?php echo htmlspecialchars($p['category']) ?></span>
          <img src="<?php echo htmlspecialchars($p['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500') ?>"
               alt="<?php echo htmlspecialchars($p['title']) ?>"
               onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500'" loading="lazy">
          <div class="card-hover-btn"><span>View Details</span></div>
        </div>
        <div class="card-body">
          <div class="card-price">$<?php echo number_format($p['price'],2) ?></div>
          <div class="card-title"><?php echo htmlspecialchars($p['title']) ?></div>
          <div class="flex justify-between items-center">
            <span class="card-seller">by <?php echo htmlspecialchars($p['seller_name']) ?></span>
            <span class="<?php echo $p['stock_quantity']<=3?'stock-low':'stock-in' ?>"><?php echo $p['stock_quantity']<=3?$p['stock_quantity'].' left':'In Stock' ?></span>
          </div>
        </div>
      </a>
      <?php endforeach ?>
    </div>

    <!-- PAGINATION -->
    <?php if($total_pages>1): ?>
    <div class="flex justify-center gap-4" style="margin-top:2.5rem;flex-wrap:wrap">
      <?php if($page>1): ?><a href="browse.php?<?php echo qs(['page'=>$page-1]) ?>" class="page-btn">‹</a><?php endif ?>
      <?php for($i=max(1,$page-2);$i<=min($total_pages,$page+2);$i++): ?>
        <a href="browse.php?<?php echo qs(['page'=>$i]) ?>" class="page-btn <?php echo $i===$page?'on':'' ?>"><?php echo $i ?></a>
      <?php endfor ?>
      <?php if($page<$total_pages): ?><a href="browse.php?<?php echo qs(['page'=>$page+1]) ?>" class="page-btn">›</a><?php endif ?>
    </div>
    <?php endif ?>

    <?php else: ?>
    <div style="text-align:center;padding:5rem 2rem;background:var(--white);border-radius:var(--r-lg);border:1px solid var(--g200)">
      <div style="font-size:2.5rem;margin-bottom:1.25rem">👜</div>
      <h3 style="font-family:var(--fd);font-size:1.6rem;font-weight:400;color:var(--espresso);margin-bottom:.6rem">No bags found</h3>
      <p style="color:var(--g400);margin-bottom:1.75rem">Try adjusting your search or filters.</p>
      <a href="browse.php" class="btn btn-buy">View All Bags</a>
    </div>
    <?php endif ?>
  </main>
</div>

<?php include 'footer.php' ?>
<script>
function applyPrice(){
  const url=new URL(window.location);
  const mn=document.getElementById('minP').value;
  const mx=document.getElementById('maxP').value;
  mn?url.searchParams.set('min',mn):url.searchParams.delete('min');
  mx?url.searchParams.set('max',mx):url.searchParams.delete('max');
  url.searchParams.set('page','1');
  window.location=url;
}
</script>
</body></html>