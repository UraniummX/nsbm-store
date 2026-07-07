<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("location: index.php");
    exit;
}

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['customer_name']);
    $email = trim($_POST['customer_email']);
    
    if (empty($name) || empty($email)) {
        $error = "Please fill in all details.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Calculate total
            $total = 0;
            $items_to_insert = [];
            
            foreach ($_SESSION['cart'] as $cart_key => $item) {
                $p_id = $item['product_id'];
                $v_id = $item['variant_id'];
                $qty = $item['qty'];
                
                $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$p_id]);
                $p = $stmt->fetch();
                $price = $p['price'];
                
                if ($v_id && $v_id != 0) {
                    $vStmt = $pdo->prepare("SELECT price_modifier FROM product_variants WHERE id = ?");
                    $vStmt->execute([$v_id]);
                    $v = $vStmt->fetch();
                    if ($v) $price += $v['price_modifier'];
                }
                
                $subtotal = $price * $qty;
                $total += $subtotal;
                
                $items_to_insert[] = [
                    'p_id' => $p_id,
                    'v_id' => $v_id,
                    'qty' => $qty,
                    'price' => $price
                ];
            }
            
            // Insert order
            $oStmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, total_amount) VALUES (?, ?, ?)");
            $oStmt->execute([$name, $email, $total]);
            $order_id = $pdo->lastInsertId();
            
            // Insert order items
            $iStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            foreach ($items_to_insert as $i) {
                $iStmt->execute([$order_id, $i['p_id'], $i['v_id'], $i['qty'], $i['price']]);
            }
            
            $pdo->commit();
            $_SESSION['cart'] = [];
            $success = true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to process order. Please try again.";
        }
    }
}
?>

<div class="container" style="width: 90%; max-width: 600px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <?php if ($success): ?>
        <div style="text-align: center;">
            <div style="background: #e6f0eb; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <span style="font-size: 40px; color: var(--nsbm-green);">✓</span>
            </div>
            <h1 style="color: var(--nsbm-green); font-size: 2.5rem; margin-top: 0;">Order Confirmed!</h1>
            <p style="font-size: 1.1rem; color: #555; margin-bottom: 30px;">Your purchase has been recorded successfully.</p>
            <a href="index.php" class="btn" style="text-decoration: none;">Return to Store</a>
        </div>
    <?php else: ?>
        <h1 style="font-size: 1.8rem; margin-top: 0; margin-bottom: 30px; text-transform: uppercase; border-bottom: 2px solid #eee; padding-bottom: 10px;">Checkout Details</h1>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: #d32f2f; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="checkout.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Full Name</label>
                <input type="text" name="customer_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">NSBM Student/Staff Email</label>
                <input type="email" name="customer_email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
            </div>
            <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1.1rem;">Place Order</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="cart.php" style="color: #888; text-decoration: none; font-size: 0.9rem;">Back to Cart</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
