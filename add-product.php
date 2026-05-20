<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$user_id=$_SESSION['user_id'];
$message=$status='';
$bag_categories=['Tote Bags','Crossbody','Backpacks','Clutches','Shoulder Bags','Mini Bags','Luxury','Vintage','Satchel','Bucket Bags'];

if($_SERVER['REQUEST_METHOD']==='POST'){
  $title      = trim($_POST['title']);
  $description= trim($_POST['description']);
  $price      = (float)$_POST['price'];
  $category   = $_POST['category'];
  $stock      = max(1,(int)$_POST['stock_quantity']);
  $image_url  = trim($_POST['image_url']);
  if(empty($title)||empty($description)||$price<=0||empty($image_url)||empty($category)){
    $message='Please fill in all required fields.'; $status='error';
  } else {
    try {
      $stmt=$pdo->prepare("INSERT INTO products (seller_id,title,description,price,category,stock_quantity,image_url,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
      $stmt->execute([$user_id,$title,$description,$price,$category,$stock,$image_url]);
      header("Location: my-listings.php?listed=1"); exit;
    } catch(PDOException $e){ $message='Failed to list bag. Please try again.'; $status='error'; }
  }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>List a Bag | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.form-layout { display:grid; grid-template-columns:1fr 360px; gap:2.5rem; align-items:start; padding-top:calc(var(--nav)+2.5rem); padding-bottom:5rem; }
.preview-sticky { position:sticky; top:calc(var(--nav)+1.5rem); }
.preview-card { background:var(--white); border-radius:var(--r-lg); overflow:hidden; border:1px solid var(--g200); box-shadow:var(--sh-md); }
.preview-img  { height:240px; background:var(--g100); overflow:hidden; display:flex; align-items:center; justify-content:center; }
.preview-img img { width:100%; height:100%; object-fit:cover; }
.preview-placeholder { font-size:3rem; color:var(--g200); }
.cat-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.5rem; }
.cat-option { position:relative; }
.cat-option input[type=radio] { position:absolute; opacity:0; width:0; }
.cat-label { display:block; padding:.65rem 1rem; border:1.5px solid var(--g200); border-radius:var(--r-md); font-size:.83rem; font-weight:600; cursor:pointer; transition:all .2s; text-align:center; color:var(--brown); }
.cat-option input:checked + .cat-label { border-color:var(--gold); background:var(--gold-soft); color:var(--espresso); }
.cat-label:hover { border-color:var(--brown-light); }
.tip-box { background:var(--gold-soft); border:1px solid var(--gold-light); border-radius:var(--r-md); padding:1rem 1.1rem; font-size:.83rem; color:var(--brown); line-height:1.7; margin-top:1.25rem; }
.checklist-item { font-size:.81rem; margin:.25rem 0; transition:color .2s; }
@media(max-width:900px){ .form-layout{grid-template-columns:1fr} .preview-sticky{position:static} .cat-grid{grid-template-columns:repeat(3,1fr)} }
@media(max-width:480px){ .cat-grid{grid-template-columns:repeat(2,1fr)} }
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="container">
  <div class="form-layout">
    <!-- FORM SIDE -->
    <div>
      <div style="margin-bottom:2rem">
        <a href="my-listings.php" style="font-size:.81rem;color:var(--g400)">← Back to Dashboard</a>
        <h1 style="font-family:var(--fd);font-size:2.8rem;font-weight:300;margin-top:.65rem;line-height:1.1">List a <em style="font-style:italic;color:var(--gold)">Bag</em></h1>
        <p style="color:var(--g400);margin-top:.4rem">Fill in your bag's details and go live in minutes.</p>
      </div>

      <?php if($message): ?>
      <div class="<?php echo $status==='error'?'flash-err':'flash-ok' ?>">⚠️ <?php echo $message ?></div>
      <?php endif ?>

      <form method="POST" id="listForm">
        <div class="section-card">
          <div class="section-card-title">Bag Details</div>
          <div class="form-group">
            <label>Listing Title *</label>
            <input type="text" name="title" id="titleInput" placeholder="e.g. Cream Leather Tote Bag — Coach" value="<?php echo isset($_POST['title'])?htmlspecialchars($_POST['title']):'' ?>" required>
          </div>
          <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="4" placeholder="Describe your bag — material, size, condition, brand…" required><?php echo isset($_POST['description'])?htmlspecialchars($_POST['description']):'' ?></textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label>Price (USD) *</label>
              <input type="number" step="0.01" name="price" placeholder="0.00" min="1" value="<?php echo isset($_POST['price'])?htmlspecialchars($_POST['price']):'' ?>" id="priceInput" required>
            </div>
            <div class="form-group">
              <label>Stock Quantity *</label>
              <input type="number" name="stock_quantity" placeholder="1" min="1" value="<?php echo isset($_POST['stock_quantity'])?htmlspecialchars($_POST['stock_quantity']):'1' ?>" required>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-title">Category *</div>
          <div class="cat-grid">
            <?php foreach($bag_categories as $cat): ?>
            <div class="cat-option">
              <input type="radio" name="category" id="cat_<?php echo $cat ?>" value="<?php echo $cat ?>" <?php echo (isset($_POST['category'])&&$_POST['category']===$cat)?'checked':'' ?> required>
              <label class="cat-label" for="cat_<?php echo $cat ?>"><?php echo $cat ?></label>
            </div>
            <?php endforeach ?>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-title">Product Image *</div>
          <div class="form-group">
            <label>Image URL</label>
            <input type="url" name="image_url" id="imageInput" placeholder="https://example.com/your-bag-photo.jpg" value="<?php echo isset($_POST['image_url'])?htmlspecialchars($_POST['image_url']):'' ?>" required>
          </div>
          <div class="tip-box">
            💡 <strong>Tip:</strong> Upload your photo to <a href="https://imgbb.com" target="_blank" style="color:var(--brown);font-weight:700">imgbb.com</a> or <a href="https://imgur.com" target="_blank" style="color:var(--brown);font-weight:700">imgur.com</a> for a free image link.
          </div>
        </div>

        <div style="background:var(--cream-dark);border-radius:var(--r-md);padding:1.1rem;margin-bottom:1.5rem;font-size:.81rem;color:var(--g600);line-height:1.7">
          🛡️ <strong>Seller Policy:</strong> By listing, you confirm the item is authentic, in your possession, and accurately described.
        </div>

        <button type="submit" class="btn btn-sell" style="width:100%;padding:1.1rem;font-size:.95rem">Publish Listing →</button>
      </form>
    </div>

    <!-- PREVIEW SIDE -->
    <div class="preview-sticky">
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--g400);margin-bottom:.85rem">Live Preview</p>
      <div class="preview-card">
        <div class="preview-img" id="previewImgWrap">
          <img id="previewImg" src="" alt="Preview" style="display:none">
          <div class="preview-placeholder" id="previewPlaceholder">👜</div>
        </div>
        <div style="padding:1.25rem">
          <div id="previewPrice" style="font-family:var(--fd);font-size:1.5rem;font-weight:600;color:var(--espresso);line-height:1">$0.00</div>
          <div id="previewTitle" style="font-size:.88rem;font-weight:600;color:var(--brown);margin:.35rem 0 .5rem;line-height:1.35">Your bag title will appear here</div>
          <div style="font-size:.74rem;color:var(--g400)">by <?php echo htmlspecialchars($_SESSION['username']) ?></div>
        </div>
      </div>

      <div style="margin-top:.85rem;padding:1rem;background:var(--white);border-radius:var(--r-md);border:1px solid var(--g200)">
        <div style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--g400);margin-bottom:.5rem">Checklist</div>
        <div id="check_title"  class="checklist-item" style="color:var(--g300)">○ Title added</div>
        <div id="check_price"  class="checklist-item" style="color:var(--g300)">○ Price set</div>
        <div id="check_cat"    class="checklist-item" style="color:var(--g300)">○ Category selected</div>
        <div id="check_img"    class="checklist-item" style="color:var(--g300)">○ Image URL added</div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php' ?>
<script>
const titleInput=document.getElementById('titleInput');
const priceInput=document.getElementById('priceInput');
const imageInput=document.getElementById('imageInput');
function setCheck(id,ok){
  const el=document.getElementById(id);
  const txt=el.textContent.slice(2);
  el.textContent=(ok?'✅ ':'○ ')+txt;
  el.style.color=ok?'#059669':'var(--g400)';
}
function updatePreview(){
  const title=titleInput.value||'Your bag title will appear here';
  const price=parseFloat(priceInput.value)||0;
  const imgUrl=imageInput.value;
  document.getElementById('previewTitle').textContent=title;
  document.getElementById('previewPrice').textContent='$'+price.toFixed(2);
  const img=document.getElementById('previewImg');
  const ph=document.getElementById('previewPlaceholder');
  if(imgUrl){ img.src=imgUrl; img.style.display='block'; ph.style.display='none'; img.onerror=()=>{img.style.display='none';ph.style.display='flex';} }
  else { img.style.display='none'; ph.style.display='flex'; }
  setCheck('check_title',titleInput.value.length>2);
  setCheck('check_price',parseFloat(priceInput.value)>0);
  setCheck('check_img',imageInput.value.length>5);
  setCheck('check_cat',!!document.querySelector('input[name=category]:checked'));
}
titleInput.addEventListener('input',updatePreview);
priceInput.addEventListener('input',updatePreview);
imageInput.addEventListener('input',updatePreview);
document.querySelectorAll('input[name=category]').forEach(r=>r.addEventListener('change',updatePreview));
updatePreview();
</script>
</body></html>