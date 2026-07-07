<?php
session_start();
require_once 'config/db.php';
require_once 'includes/image_helper.php';

$category_id   = isset($_GET['category'])  ? $_GET['category']  : '';
$search_query  = isset($_GET['search'])    ? trim($_GET['search']) : '';
$min_price     = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : 0;
$max_price     = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : 999999;
$status_filter = isset($_GET['status'])    ? $_GET['status'] : 'all';

if ($min_price > $max_price) { [$min_price, $max_price] = [$max_price, $min_price]; }

$query = "SELECT p.*, c.name as category_name,
          COALESCE((SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id), p.stock_quantity) as total_stock
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.price BETWEEN :min_p AND :max_p";

$params = ['min_p' => $min_price, 'max_p' => $max_price];

if ($category_id) {
    $query .= " AND p.category_id = :cat_id";
    $params['cat_id'] = $category_id;
}
if ($search_query) {
    $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params['search'] = "%$search_query%";
}

if ($status_filter === 'in_stock') {
    $query .= " AND p.status = 'active' HAVING total_stock > 0";
} elseif ($status_filter === 'pre_order') {
    $query .= " AND p.status = 'pre_order'";
} elseif ($status_filter === 'out_of_stock') {
    $query .= " AND p.status = 'out_of_stock'";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

if (count($products) === 0) {
    echo "<div style='grid-column: 1 / -1; text-align: center; padding: 50px;'><h2 style='color:#ccc'>No products found</h2></div>";
    exit;
}

foreach ($products as $row) {
    $name   = htmlspecialchars($row['name']);
    $price  = number_format($row['price'], 2);
    $id     = $row['id'];
    $stock  = (int)$row['total_stock'];
    $img    = getProductImage($row['name'], $row['category_name'], $row['image_path']);
    $cat    = htmlspecialchars($row['category_name']);
    $status = $row['status'];

    if ($status === 'pre_order') {
        $badge_html = "<span class='stock-badge pre-order'>PRE-ORDER</span>";
    } elseif ($stock > 0) {
        $badge_html = "<span class='stock-badge in-stock'>IN STOCK</span>";
    } else {
        $badge_html = "<span class='stock-badge out-of-stock'>OUT OF STOCK</span>";
    }

    $star_count = rand(4, 5);
    $stars_html = '';
    for ($i = 0; $i < 5; $i++) {
        $stars_html .= $i < $star_count
            ? '<i class="fa-solid fa-star" style="color:#FFD700;font-size:0.8rem;"></i>'
            : '<i class="fa-regular fa-star" style="color:#FFD700;font-size:0.8rem;"></i>';
    }

    echo "<div class='product-card'>";
    echo "<div class='img-container' style='position:relative;'>";
    echo "<a href='details.php?id=$id'><img src='$img' alt='$name' loading='lazy' onerror=\"this.src='https://picsum.photos/seed/product/600/800'\"></a>";
    echo $badge_html;
    echo "<span class='category-badge'>$cat</span>";
    echo "</div>";

    echo "<div class='card-info'>";
    echo "<p class='product-name'>$name</p>";
    echo "<div style='margin-bottom:8px;'>$stars_html <span style='font-size:0.75rem;color:#888;'>(" . rand(10, 150) . ")</span></div>";
    echo "<p class='product-price'>LKR $price</p>";

    echo "<div class='product-actions'>";
    echo "<button class='action-btn' onclick='quickView($id)' title='Quick View'><i class='fa-regular fa-eye'></i></button>";
    echo "<button class='action-btn' onclick='toggleWishlist(this)' title='Wishlist'><i class='fa-regular fa-heart'></i></button>";
    echo "<a href='details.php?id=$id' class='action-btn' title='View Details'><i class='fa-solid fa-circle-info'></i></a>";

    if ($status === 'pre_order') {
        echo "<a href='details.php?id=$id' class='action-btn' title='Pre-Order' style='color:var(--nsbm-green);'><i class='fa-solid fa-clock'></i></a>";
    } elseif ($stock > 0) {
        echo "<form class='ajax-cart-form' method='POST' style='display:inline;'>";
        echo "<input type='hidden' name='product_id' value='$id'><button type='submit' class='action-btn' title='Add to Cart'><i class='fa-solid fa-cart-plus'></i></button>";
        echo "</form>";
    } else {
        echo "<button class='action-btn' style='opacity:0.4;cursor:not-allowed;' disabled><i class='fa-solid fa-cart-plus'></i></button>";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
?>
