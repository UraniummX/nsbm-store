<?php

// Backend Logic
session_start();
require_once 'config/db.php';
require_once 'includes/image_helper.php';
include 'includes/header.php';

$cart_items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cart_key => $item) {
        $p_id = $item['product_id'];
        $v_id = $item['variant_id'];
        $qty = $item['qty'];

        $stmt = $pdo->prepare("SELECT p.name, p.price, p.stock_quantity, p.image_path, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$p_id]);
        $product = $stmt->fetch();

        if ($product) {
            $price = (float)$product['price'];
            $variant_text = '';

            if ($v_id && $v_id !== '0') {
                $vStmt = $pdo->prepare("SELECT * FROM product_variants WHERE id = ?");
                $vStmt->execute([$v_id]);
                $variant = $vStmt->fetch();
                if ($variant) {
                    $price += (float)$variant['price_modifier'];
                    $variant_text = $variant['variant_type'] . ': ' . $variant['variant_name'];
                }
            }

            $subtotal = $price * $qty;
            $total += $subtotal;
            $img = getProductImage($product['name'], $product['category_name'], $product['image_path']);

            $cart_items[] = [
                'cart_key' => $cart_key,
                'name' => $product['name'],
                'variant_text' => $variant_text,
                'price' => $price,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'image' => $img
            ];
        }
    }
}
?>

<div class="container" style="width: 90%; max-width: 1200px; margin: 60px auto;">
    <h1 style="font-family: 'Arial Black', sans-serif; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 40px; text-align: center;">Shopping Bag</h1>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 60px 0;">
            <p style="color: var(--text-grey); margin-bottom: 20px;">Your bag is empty.</p>
            <a href="index.php" class="btn" style="background: var(--bg-track); color: var(--text-black);">CONTINUE SHOPPING</a>
        </div>
    <?php else: ?>
        <div style="display: flex; gap: 60px; align-items: flex-start; flex-wrap: wrap;">
            <div style="flex: 1.5; min-width: 300px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #eee;">
                            <th style="padding: 15px 0; text-align: left; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: #888;">Product</th>
                            <th style="padding: 15px 0; text-align: center; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: #888;">Quantity</th>
                            <th style="padding: 15px 0; text-align: right; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: #888;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 30px 0; display: flex; gap: 20px; align-items: center;">
                                <img src="<?php echo $item['image']; ?>" style="width: 80px; height: 100px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <div style="font-weight: bold; text-transform: uppercase; font-size: 1rem;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <?php if ($item['variant_text']): ?>
                                        <div style="color: #666; font-size: 0.8rem; margin-top: 5px;"><?php echo htmlspecialchars($item['variant_text']); ?></div>
                                    <?php endif; ?>
                                    <div style="color: var(--nsbm-green); font-weight: 900; font-size: 0.9rem; margin-top: 5px;">LKR <?php echo number_format($item['price'], 2); ?></div>
                                    
                                    <form action="cart_action.php" method="POST" style="margin-top: 15px;">
                                        <?php 
                                            $parts = explode('-', $item['cart_key']); 
                                            $pid = $parts[0];
                                            $vid = isset($parts[1]) ? $parts[1] : '0';
                                        ?>
                                        <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                        <input type="hidden" name="variant_id" value="<?php echo $vid; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" style="background:none; border:none; border-bottom: 1px solid #000; padding:0; cursor:pointer; font-size: 0.7rem; font-weight: bold; color: #ff4757; border-color: #ff4757;">REMOVE</button>
                                    </form>
                                </div>
                            </td>
                            <td style="padding: 30px 0; text-align: center; font-size: 1rem; font-weight: bold;"><?php echo $item['qty']; ?></td>
                            <td style="padding: 30px 0; text-align: right; font-weight: bold; font-size: 1.1rem;">LKR <?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="flex: 1; background: var(--bg-white); padding: 40px; border-radius: 16px; border: 1px solid var(--border-grey); box-shadow: 0 10px 30px rgba(0,0,0,0.05); min-width: 300px;">
                <h2 style="margin-top: 0; text-transform: uppercase; font-size: 1.2rem; letter-spacing: 1px; border-bottom: 2px solid var(--border-grey); padding-bottom: 15px;">Summary</h2>
                <div style="display: flex; justify-content: space-between; margin: 30px 0; font-weight: 900; font-size: 1.3rem; color: var(--nsbm-green);">
                    <span style="text-transform: uppercase; color: var(--text-black);">Total</span>
                    <span>LKR <?php echo number_format($total, 2); ?></span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-grey); margin-bottom: 30px;">Shipping and taxes calculated at checkout.</p>
                <a href="checkout.php" style="display: block; background: var(--nsbm-green); color: #fff; text-align: center; padding: 18px; border-radius: 10px; text-decoration: none; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; transition: 0.2s;">Checkout</a>
                <a href="index.php" class="btn" style="display: block; text-align: center; margin-top: 20px; background: var(--bg-track); color: var(--text-black);">CONTINUE SHOPPING</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
