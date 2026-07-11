<?php

// Backend Logic
session_start();
require_once 'config/db.php';
require_once 'includes/image_helper.php';
include 'includes/header.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LIMIT 5");
$top_sellers = $stmt->fetchAll();

$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 20000;
?>

<div class="mobile-action-bar">
    <button class="mobile-action-btn" id="mobile-filter-toggle">
        <i class="fa-solid fa-filter"></i> Filters
    </button>
    <button class="mobile-action-btn" id="mobile-sellers-toggle">
        <i class="fa-solid fa-fire"></i> Top Sellers
    </button>
</div>

<div class="store-layout">

    <div class="sidebar-left-trigger"></div>
    <aside class="sidebar-left">
        <div class="sidebar-close-btn">
            <button type="button" class="drawer-close" id="sidebar-left-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <h2>Filters</h2>
        <form id="filter-form">
            <div class="filter-group">
                <span class="filter-label">Search</span>
                <input type="text" id="search-input" class="search-input" placeholder="Search..." onkeyup="debounceFetch()" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Categories</span>
                <div class="radio-list">
                    <label class="radio-item">
                        <input type="radio" name="category" value="" <?php echo (!isset($_GET['category']) || $_GET['category'] == '') ? 'checked' : ''; ?> onchange="fetchProducts()">
                        <span>All Items</span>
                    </label>
                    <?php foreach ($categories as $cat): ?>
                        <label class="radio-item">
                            <input type="radio" name="category" value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'checked' : ''; ?> onchange="fetchProducts()">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-group">
                <span class="filter-label">Availability</span>
                <div class="radio-list">
                    <label class="radio-item">
                        <input type="radio" name="status" value="all" checked onchange="fetchProducts()">
                        <span>All Items</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="status" value="in_stock" onchange="fetchProducts()">
                        <span>In Stock</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="status" value="pre_order" onchange="fetchProducts()">
                        <span>Pre-Order</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="status" value="out_of_stock" onchange="fetchProducts()">
                        <span>Out of Stock</span>
                    </label>
                </div>
            </div>

            <div class="filter-group">
                <span class="filter-label">Price Range</span>
                <div class="price-slider-container">
                    <div class="slider-track" id="slider-track"></div>
                    <input type="range" id="min_price_slider" min="0" max="20000" value="<?php echo htmlspecialchars($min_price); ?>" step="500">
                    <input type="range" id="max_price_slider" min="0" max="20000" value="<?php echo htmlspecialchars($max_price); ?>" step="500">
                </div>
                <div class="price-display">
                    <span id="min-val">LKR <?php echo number_format($min_price); ?></span>
                    <span id="max-val">LKR <?php echo number_format($max_price); ?></span>
                </div>
                
                <input type="hidden" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                <input type="hidden" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">

                <button type="button" class="btn-update-price" onclick="fetchProducts()">UPDATE PRICE FILTER</button>
            </div>

            <div class="filter-group">
                <button type="button" class="btn-reset" onclick="resetFilters()">Reset Filters</button>
            </div>
        </form>
    </aside>

    <main class="products-section">
        <h1 class="section-title">All Products</h1>
        <div class="product-grid" id="product-grid">
            
            <div class="loader"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>
    </main>

    <div class="sidebar-right-trigger"></div>
    <aside class="sidebar-right">
        <div class="sidebar-close-btn">
            <button type="button" class="drawer-close" id="sidebar-right-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <h2>Top Sellers</h2>
        <div class="top-seller-list">
            <?php 
            $rank = 1;
            foreach($top_sellers as $ts): 
                $ts_img = getProductImage($ts['name'], $ts['category_name'], $ts['image_path']);
            ?>
            <a href="details.php?id=<?php echo $ts['id']; ?>" class="top-seller-card">
                <div class="ts-rank">#<?php echo $rank++; ?></div>
                <img src="<?php echo $ts_img; ?>" alt="<?php echo htmlspecialchars($ts['name']); ?>" class="ts-img">
                <div class="ts-info">
                    <div class="ts-name"><?php echo htmlspecialchars($ts['name']); ?></div>
                    <div class="ts-price">LKR <?php echo number_format($ts['price']); ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </aside>

</div>

<div id="toast" class="toast">Added to cart!</div>

<div class="modal-overlay" id="quick-view-modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeQuickView()"><i class="fa-solid fa-xmark"></i></button>
        <div id="quick-view-body">Loading...</div>
    </div>
</div>

<script>
let debounceTimer;

const minSlider = document.getElementById('min_price_slider');
const maxSlider = document.getElementById('max_price_slider');
const minVal = document.getElementById('min-val');
const maxVal = document.getElementById('max-val');
const minHidden = document.getElementById('min_price');
const maxHidden = document.getElementById('max_price');
const track = document.getElementById('slider-track');

function setSlider() {
    let min = parseInt(minSlider.value);
    let max = parseInt(maxSlider.value);
    if(min > max) { [min, max] = [max, min]; }
    minVal.textContent = `LKR ${min.toLocaleString()}`;
    maxVal.textContent = `LKR ${max.toLocaleString()}`;
    minHidden.value = min;
    maxHidden.value = max;
    const percent1 = (min / 20000) * 100;
    const percent2 = (max / 20000) * 100;
    track.style.background = `linear-gradient(to right, #eee ${percent1}%, var(--nsbm-green) ${percent1}%, var(--nsbm-green) ${percent2}%, #eee ${percent2}%)`;
}
if(minSlider) minSlider.oninput = setSlider;
if(maxSlider) maxSlider.oninput = setSlider;
if(minSlider && maxSlider) setSlider();

function debounceFetch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchProducts, 300);
}

function fetchProducts() {
    const form = document.getElementById('filter-form');
    const search = document.getElementById('search-input').value;
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.append('search', search);

    document.getElementById('product-grid').innerHTML = '<div class="loader"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';

    fetch('fetch_products.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-grid').innerHTML = html;
            attachCartListeners();
        });
}

function resetFilters() {
    document.getElementById('filter-form').reset();
    document.getElementById('search-input').value = '';
    fetchProducts();
}

function attachCartListeners() {
    document.querySelectorAll('.ajax-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'add');
            fetch('cart_action.php', { method: 'POST', body: formData })
            .then(r => r.json()).then(data => {
                if (data.success) {
                    document.getElementById('cart-badge').textContent = data.cart_count;
                    const toast = document.getElementById('toast');
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.display = 'none'; }, 2000);
                }
            });
        });
    });
}

function toggleWishlist(btn) {
    const icon = btn.querySelector('i');
    if(icon.classList.contains('fa-regular')) {
        icon.classList.replace('fa-regular', 'fa-solid');
        icon.style.color = '#ff4757';
    } else {
        icon.classList.replace('fa-solid', 'fa-regular');
        icon.style.color = 'inherit';
    }
}

function quickView(id) {
    const modal = document.getElementById('quick-view-modal');
    const body = document.getElementById('quick-view-body');
    body.innerHTML = '<div style="text-align: center; padding: 50px;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: var(--nsbm-green);"></i></div>';
    modal.classList.add('active');
    window.location.href = `details.php?id=${id}`;
}

function closeQuickView() {
    document.getElementById('quick-view-modal').classList.remove('active');
}

// Initial fetch
document.addEventListener('DOMContentLoaded', fetchProducts);
</script>

<?php include 'includes/footer.php'; ?>
