<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("location: index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("location: index.php");
    exit;
}

// Fetch variants
$vStmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = :id");
$vStmt->execute(['id' => $id]);
$variants = $vStmt->fetchAll();

$hasVariants = count($variants) > 0;
$total_stock = $hasVariants ? array_sum(array_column($variants, 'stock_quantity')) : (int)$product['stock_quantity'];

$name = htmlspecialchars($product['name']);
$base_price = (float)$product['price'];
$desc = htmlspecialchars($product['description']);
$cat = htmlspecialchars($product['category_name']);

require_once 'includes/image_helper.php';
$images = getProductImages($name, $cat, $product['image_path'], $product['gallery_paths']);
$main_img = $images[0];
$product_status = $product['status'] ?? 'active';
?>

<div class="product-page-container">
    
    <nav class="breadcrumb">
        <a href="index.php">Home</a> / <?php echo $name; ?>
    </nav>

    <div class="product-main-grid">
        
        <?php if (count($images) > 1): ?>
        <div class="thumbnail-column">
            <?php foreach ($images as $index => $img_src): ?>
                <img src="<?php echo $img_src; ?>" class="thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="updateMainImg(this)">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        
        <div class="main-image-column" id="img-zoom-container">
            <img src="<?php echo $main_img; ?>" alt="<?php echo $name; ?>" id="main-display-img">
            
            <div class="trust-badges">
                <div class="badge-item"><i class="fa-solid fa-circle-check"></i> 100% Authentic Product</div>
                <div class="badge-item"><i class="fa-solid fa-truck-fast"></i> Campus-wide Delivery</div>
                <div class="badge-item"><i class="fa-solid fa-shield-halved"></i> 6 Months Student Warranty</div>
            </div>
        </div>

        
        <div class="info-column">
            <p class="cat-label"><?php echo $cat; ?></p>
            <h1 class="prod-title"><?php echo $name; ?></h1>
            
            <div class="price-section">
                <span class="old-price" id="display-old-price">LKR <?php echo number_format($base_price * 1.15, 2); ?></span>
                <span class="new-price" id="display-price">LKR <?php echo number_format($base_price, 2); ?></span>
                <span class="save-tag">SAVE 15%</span>
            </div>

            <div class="stock-status-detail <?php echo ($product_status === 'pre_order') ? 'in' : (($total_stock > 0) ? 'in' : 'out'); ?>" id="display-stock">
                <?php
                if ($product_status === 'pre_order') {
                    echo '<i class="fa-solid fa-clock"></i> <span id="stock-text">PRE-ORDER — Ships soon</span>';
                } elseif ($total_stock > 0) {
                    echo '<i class="fa-solid fa-check"></i> <span id="stock-text">IN STOCK</span>';
                } else {
                    echo '<i class="fa-solid fa-xmark"></i> <span id="stock-text">OUT OF STOCK</span>';
                }
                ?>
            </div>

            <div class="short-desc">
                <h3>Short Description</h3>
                <p><?php echo nl2br($desc); ?></p>
                <ul>
                    <li>Official NSBM Verified Seller</li>
                    <li>Eco-friendly packaging</li>
                    <li>Cash on delivery available</li>
                </ul>
            </div>

            <form action="cart_action.php" method="POST" class="action-form" style="flex-direction: column;">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                <input type="hidden" name="action" value="add">
                
                <?php if ($hasVariants): ?>
                    <?php 
                                    $groupedVariants = [];
                    foreach($variants as $v) {
                        $groupedVariants[$v['variant_type']][] = $v;
                    }
                    foreach ($groupedVariants as $type => $vars):
                    ?>
                    <div class="variant-selector">
                        <span class="variant-label"><?php echo htmlspecialchars($type); ?></span>
                        <div class="variant-chips">
                            <?php foreach ($vars as $index => $v): ?>
                                <label class="variant-chip <?php echo $index === 0 ? 'active' : ''; ?>" 
                                       onclick="selectVariant(this, <?php echo $v['id']; ?>, <?php echo $v['price_modifier']; ?>, <?php echo $v['stock_quantity']; ?>)">
                                    <input type="radio" name="variant_id" value="<?php echo $v['id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> style="display:none;">
                                    <?php echo htmlspecialchars($v['variant_name']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($product_status === 'pre_order'): ?>
                <div class="purchase-actions" style="margin-top: 20px;">
                    <a href="#" class="btn-buy-now" style="text-align:center;background:#E65100;text-decoration:none;display:block;padding:18px;">
                        <i class="fa-solid fa-clock"></i> PRE-ORDER NOW
                    </a>
                    <p style="font-size:0.8rem;color:#888;margin-top:10px;text-align:center;">This item is not yet in stock. Pre-ordering reserves your place.</p>
                </div>
                <?php elseif ($total_stock > 0): ?>
                <div class="purchase-actions" style="margin-top: 20px;">
                    <div style="display: flex; gap: 15px;">
                        <div class="qty-control">
                            <button type="button" onclick="changeQty(-1)">-</button>
                            <input type="number" name="quantity" id="qty-input" value="1" min="1">
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                        <button type="submit" class="btn-add-cart" id="add-to-cart-btn">ADD TO CART</button>
                    </div>
                    <button type="submit" name="buy_now" value="1" class="btn-buy-now" id="buy-now-btn">BUY IT NOW</button>
                </div>
                <?php else: ?>
                <button class="btn-disabled" disabled style="margin-top: 20px;">OUT OF STOCK</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<style>
.product-page-container { width: 90%; max-width: 1400px; margin: 40px auto; }
.breadcrumb { font-size: 0.8rem; color: #888; margin-bottom: 30px; font-weight: 700; text-transform: uppercase; }
.breadcrumb a { color: inherit; text-decoration: none; }

.product-main-grid { 
    display: grid; 
    grid-template-columns: <?php echo count($images) > 1 ? '80px 1fr 1fr' : '1fr 1fr'; ?>; 
    gap: 40px; 
    align-items: flex-start; 
}

.thumbnail-column { display: flex; flex-direction: column; gap: 15px; }
.thumb { width: 100%; aspect-ratio: 1; object-fit: cover; border: 1px solid #eee; cursor: pointer; border-radius: 4px; transition: 0.2s; }
.thumb.active { border: 2px solid var(--nsbm-green); }
.thumb:hover { border-color: var(--nsbm-green); }

.main-image-column { position: relative; overflow: hidden; border-radius: 8px; border: 1px solid #f0f0f0; cursor: crosshair; }
.main-image-column img { width: 100%; display: block; transition: transform 0.1s; transform-origin: center center; }

.trust-badges { margin-top: 30px; display: flex; flex-direction: column; gap: 10px; }
.badge-item { background: #f4f7f5; padding: 12px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; color: #444; display: flex; align-items: center; gap: 10px; }
.badge-item i { color: var(--nsbm-green); }

.cat-label { color: #888; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 10px; }
.prod-title { font-size: 2.2rem; font-weight: 900; margin: 0 0 20px 0; line-height: 1.2; text-transform: uppercase; }

.price-section { margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
.old-price { text-decoration: line-through; color: #888; font-size: 1.1rem; }
.new-price { color: #d32f2f; font-size: 1.8rem; font-weight: 900; transition: 0.3s; }
.save-tag { background: #d32f2f; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: 900; }

.stock-status-detail { font-weight: 900; font-size: 0.9rem; margin-bottom: 30px; transition: 0.3s; }
.stock-status-detail.in { color: var(--nsbm-green); }
.stock-status-detail.out { color: #d32f2f; }

.short-desc { border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 25px 0; margin-bottom: 30px; }
.short-desc h3 { font-size: 0.9rem; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; }
.short-desc p { font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 15px; }
.short-desc ul { font-size: 0.9rem; color: #666; padding-left: 20px; }

.purchase-actions { display: flex; flex-direction: column; gap: 15px; }
.action-form { display: flex; gap: 15px; width: 100%; }
.qty-control { display: flex; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
.qty-control button { background: white; border: none; padding: 10px 15px; cursor: pointer; font-weight: bold; }
.qty-control input { width: 50px; border: none; text-align: center; font-weight: bold; -moz-appearance: textfield; }

.btn-add-cart { flex: 1; background: white; color: var(--nsbm-green); border: 2px solid var(--nsbm-green); padding: 15px; font-weight: 900; border-radius: 8px; cursor: pointer; transition: 0.3s; }
.btn-add-cart:hover { background: var(--nsbm-green); color: white; }

.btn-buy-now { background: var(--nsbm-green); color: white; border: none; padding: 18px; font-weight: 900; border-radius: 8px; text-align: center; text-decoration: none; transition: 0.3s; cursor: pointer; width: 100%; box-sizing: border-box; }
.btn-buy-now:hover { background: var(--nsbm-green-dark); transform: translateY(-2px); }

.btn-disabled { background: #eee; color: #888; border: none; padding: 18px; font-weight: 900; border-radius: 8px; width: 100%; cursor: not-allowed; }

@media (max-width: 900px) {
    .product-main-grid { grid-template-columns: 1fr; }
    .thumbnail-column { order: 2; flex-direction: row; }
}
</style>

<script>
const basePrice = <?php echo $base_price; ?>;

function changeQty(amt) {
    const input = document.getElementById('qty-input');
    let val = parseInt(input.value) + amt;
    if (val < 1) val = 1;
    input.value = val;
}

function updateMainImg(thumb) {
    document.getElementById('main-display-img').src = thumb.src;
    document.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
    thumb.classList.add('active');
}

function selectVariant(labelElement, variantId, modifier, stock) {
    // Remove active class from siblings
    const parent = labelElement.closest('.variant-chips');
    parent.querySelectorAll('.variant-chip').forEach(el => el.classList.remove('active'));
    
    // Add active to current
    labelElement.classList.add('active');

    let newPrice = basePrice + parseFloat(modifier);
    
    document.getElementById('display-price').textContent = 'LKR ' + newPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('display-old-price').textContent = 'LKR ' + (newPrice * 1.15).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Update stock UI
    const stockContainer = document.getElementById('display-stock');
    const addCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');

    if (stock > 0) {
        stockContainer.className = 'stock-status-detail in';
        stockContainer.innerHTML = '<i class="fa-solid fa-check"></i> IN STOCK (' + stock + ' available)';
        if(addCartBtn) addCartBtn.disabled = false;
        if(buyNowBtn) buyNowBtn.disabled = false;
    } else {
        stockContainer.className = 'stock-status-detail out';
        stockContainer.innerHTML = '<i class="fa-solid fa-xmark"></i> OUT OF STOCK';
        if(addCartBtn) addCartBtn.disabled = true;
        if(buyNowBtn) buyNowBtn.disabled = true;
    }
}

window.onload = function() {
    const firstVariant = document.querySelector('.variant-chip.active');
    if (firstVariant) {
        firstVariant.click();
    }
};

// Image zoom
const container = document.getElementById('img-zoom-container');
const img = document.getElementById('main-display-img');

container.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const xPercent = x / rect.width * 100;
    const yPercent = y / rect.height * 100;
    
    img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
    img.style.transform = "scale(2)";
});

container.addEventListener('mouseleave', () => {
    img.style.transformOrigin = "center center";
    img.style.transform = "scale(1)";
});

</script>

<?php include 'includes/footer.php'; ?>
