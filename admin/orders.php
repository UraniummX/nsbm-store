<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../config/db.php';

// Fetch orders
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$orders = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - NSBM Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; margin: 0; background: #f4f7f6; color: #333; }
        header { background: #fff; padding: 20px; border-bottom: 1px solid #ddd; }
        nav { width: 90%; max-width: 1400px; margin: auto; display: flex; justify-content: space-between; align-items: center; }
        .logo-link { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #388E3C; font-size: 1.5rem; font-weight: 900; }
        .logo-icon { height: 40px; width: auto; }
        @media (max-width: 1024px) {
            .logo-link { font-size: 1.3rem !important; gap: 6px !important; }
            .logo-icon { height: 28px !important; }
        }
        @media (max-width: 480px) {
            .logo-link { font-size: 1.1rem !important; gap: 4px !important; }
            .logo-icon { height: 24px !important; }
        }
        .nav-icons { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; }
        .nav-icons a { color: #333; text-decoration: none; font-size: 1.2rem; }
        .nav-icons a:hover { color: #388E3C; }
        
        .container { width: 90%; max-width: 1400px; margin: 40px auto; }
        .header-section { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .header-section h1 { margin: 0; font-weight: 900; }
        .btn-link { text-decoration: none; color: #388E3C; font-weight: bold; border: 1px solid #388E3C; padding: 8px 15px; border-radius: 5px; }
        
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f8f8; font-weight: 900; text-transform: uppercase; font-size: 0.85rem; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .badge.pending { background: #fff3cd; color: #856404; }
        .badge.completed { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="dashboard.php" class="logo-link">
                    <img src="../assets/images/favicon.png?v=2" alt="NSBM Store Logo" class="logo-icon" height="40">
                    <span>NSBM STORE <span style="font-weight:400; font-size:1rem; color:#888;">ADMIN</span></span>
                </a>
            </div>
            <ul class="nav-icons">
                <li><a href="dashboard.php" title="Products">Products</a></li>
                <li><a href="orders.php" style="color: #388E3C; font-weight: bold;" title="Orders">Orders</a></li>
                <li><a href="../index.php" title="View Site"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container">
        <div class="header-section">
            <h1>Customer Orders</h1>
            <a href="dashboard.php" class="btn-link">Manage Products</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Total (LKR)</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($orders)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #888;">No orders found.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($o['customer_email']); ?></td>
                        <td style="font-weight: bold; color: #388E3C;"><?php echo number_format($o['total_amount'], 2); ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($o['created_at'])); ?></td>
                        <td><span class="badge <?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
