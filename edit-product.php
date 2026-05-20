<?php
require_once 'db.php'; session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$product_id=(int)($_GET['id']??0);
$user_id=$_SESSION['user_id'];
$message=$status='';
try {
  $stmt=$pdo->prepare("SELECT * FROM products WHERE id=? AND seller_id=?");
  $stmt->execute([$product_id,$user_id]); $product=$stmt->fetch();
  if(!$product){ header("Location: my-listings.php?error=access_denied"); exit; }
} catch(PDOException $e){ header("Location: my-listings.php"); exit; }
$bag_categories=['Tote Bags','Crossbody','Backpacks','Clutches','Shoulder Bags','Mini Bags','Luxury','Vintage','Satchel','Bucket Bags'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title      = trim($_POST['title']);
  $description= trim($_POST['description']);
  $price      = (float)$_POST['price'];
  $stock      = max(0,(int)$_POST['stock']);
  $category   = $_POST['category'];
  $image_url  = trim($_POST['image_url']);
  if(empty($title)||empty($description)||$price<=0||empty($image_url)){
    $message='Please fill in all required fields.'; $status='error';
  } else {
    try {
      $pdo->prepare("UPDATE products SET title=?,description=?,price=?,stock_quantity=?,category=?,image_url=? WHERE id=? AND seller_id=?")
          ->execute([$title,$description,$price,$stock,$category,$image_url,$product_id,$user_id]);
      $product=array_merge($product,['title'=>$title,'description'=>$description,'price'=>$price,'stock_quantity'=>$stock,'category'=>$category,'image_url'=>$image_url]);
      $message='Listing updated successfully!'; $status='success';
    } catch(PDOException $e){ $message='Update failed. Please try again.'; $status='error'; }
  }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit — <?php echo htmlspecialchars($product['title']) ?> | Baghaus</title>
<link rel="stylesheet" href="style.css">
<style>
.form-layout { display:grid; grid-template-columns:1fr 340px; gap:2.5rem; align-items:start; padding-top:calc(var(--nav)+2.5rem); padding-bottom:5rem; }
.preview-sticky { position:sticky; top:calc(var(--nav)+1.5rem); }
.preview-card { background:var(--white); border-radius:var(--r-lg); overflow:hidden; border:1px solid var(--g200); box-shadow:var(--sh-md); }
.preview-img  { height:230px; overflow:hidden; background:var(--g100); }
.preview-img img { width:100%; height:100%; object-fit:cover; }
.cat-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.5rem; }
.cat-option { position:relative; }
.cat-option input[type=radio] { position:absolute; opacity:0; width:0; }
.cat-label { display:block; padding:.65rem 1rem; border:1.5px solid var(--g200); border-radius:var(--r-md); font-size:.83rem; font-weight:600; cursor:pointer; transition:all .2s; text-align:center; color:var(--brown); }
.cat-option input:checked + .cat-label { border-color:var(--gold); background:var(--gold-soft); color:var(--espresso); }
.danger-zone { background:#fef2f2; border:1px solid #fecaca; border-radius:var(--r-lg); padding:1.4rem; margin-top:1.5rem; }
@media(max-width:900px){ .form-layout{grid-template-columns:1fr} .preview-sticky{position:static} }
</style>
</head><body>
<?php include 'nav.php'; ?>

<div class="container">
  <div class="form-layout">
    <div>
      <div style="margin-bottom:2rem">
        <a href="my-listings.php" style="font-size:.81rem;color:var(--g400)">← Back to Dashboard</a>
        <h1 style="font-family:var(--fd);font-size:2.8rem;font-weight:300;margin-top:.65rem;line-height:1.1">Edit <em style="font-style:italic;color:var(--gold)">Listing</em></h1>
      </div>

      <?php if($message): ?>
      <div class="<?php echo $status==='success'?'flash-ok':'flash-err' ?>"><?php echo $status==='success'?'✅':'⚠️' ?> <?php echo $message ?></div>
      <?php endif ?>

      <form method="POST">
        <div class="section-card">
          <div class="section-card-title">Bag Details</div>
          <div class="form-group">
            <label>Listing Title *</label>
            <input type="text" name="title" id="titleInput" value="<?php echo htmlspecialchars($product['title']) ?>" required>
          </div>
          <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($product['description']) ?></textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label>Price (USD) *</label>
              <input type="number" step="0.01" name="price" id="priceInput" value="<?php echo $product['price'] ?>" min="0.01" required>
            </div>
            <div class="form-group">
              <label>Stock Quantity</label>
              <input type="number" name="stock" value="<?php echo $product['stock_quantity'] ?>" min="0" required>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-title">Category</div>
          <div class="cat-grid">
            <?php foreach($bag_categories as $cat): ?>
            <div class="cat-option">
              <input type="radio" name="category" id="cat_<?php echo $cat ?>" value="<?php echo $cat ?>" <?php echo $product['category']===$cat?'checked':'' ?>>
              <label class="cat-label" for="cat_<?php echo $cat ?>"><?php echo $cat ?></label>
            </div>
            <?php endforeach ?>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-title">Product Image</div>
          <div class="form-group" style="margin-bottom:0">
            <label>Image URL</label>
            <input type="url" name="image_url" id="imageInput" value="<?php echo htmlspecialchars($product['image_url']) ?>">
          </div>
        </div>

        <div class="flex gap-4" style="margin-top:.5rem">
          <button type="submit" class="btn btn-sell" style="flex:2;padding:1rem;font-size:.9rem">Save Changes</button>
          <a href="my-listings.php" class="btn btn-ghost" style="flex:1;padding:1rem;text-align:center">Cancel</a>
        </div>
      </form>

      <div class="danger-zone">
        <h4 style="color:var(--red);font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.65rem">⚠️ Danger Zone</h4>
        <p style="font-size:.84rem;color:#7f1d1d;margin-bottom:.85rem">Deleting this listing is permanent and cannot be undone.</p>
        <a href="delete-product.php?id=<?php echo $product_id ?>" class="btn btn-danger" style="font-size:.8rem" onclick="return confirm('Permanently delete this listing?')">Delete This Listing</a>
      </div>
    </div>

    <!-- PREVIEW -->
    <div class="preview-sticky">
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--g400);margin-bottom:.85rem">Live Preview</p>
      <div class="preview-card">
        <div class="preview-img">
          <img id="previewImg" src="<?php echo htmlspecialchars($product['image_url']?:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600') ?>"
               alt="Preview" onerror="this.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600'">
        </div>
        <div style="padding:1.25rem">
          <div id="previewPrice" style="font-family:var(--fd);font-size:1.5rem;font-weight:600;color:var(--espresso)">$<?php echo number_format($product['price'],2) ?></div>
          <div id="previewTitle" style="font-size:.88rem;font-weight:600;color:var(--brown);margin:.35rem 0 .4rem;line-height:1.35"><?php echo htmlspecialchars($product['title']) ?></div>
          <a href="product-details.php?id=<?php echo $product_id ?>" style="font-size:.74rem;color:var(--gold);font-weight:600">View public listing →</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php' ?>
<script>
const titleInput=document.getElementById('titleInput');
const priceInput=document.getElementById('priceInput');
const imageInput=document.getElementById('imageInput');
function updatePreview(){
  document.getElementById('previewTitle').textContent=titleInput.value||'—';
  document.getElementById('previewPrice').textContent='$'+(parseFloat(priceInput.value)||0).toFixed(2);
  const img=document.getElementById('previewImg');
  if(imageInput.value){ img.src=imageInput.value; img.onerror=()=>{img.src='https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600';} }
}
titleInput.addEventListener('input',updatePreview);
priceInput.addEventListener('input',updatePreview);
imageInput.addEventListener('input',updatePreview);
</script>
</body></html>