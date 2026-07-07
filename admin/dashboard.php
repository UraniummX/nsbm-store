<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../config/db.php';

// Fetch products
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$products = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NSBM Market</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container { width: 90%; max-width: 1400px; margin: 40px auto; font-family: 'Montserrat', sans-serif; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .dashboard-header h1 { margin: 0; font-weight: 900; font-size: 2rem; }
        .btn-add { background: var(--nsbm-green); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .btn-add:hover { background: var(--nsbm-green-dark); }
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 15px 20px; text-align: left; border-bottom: 1px solid #eee; }
        .admin-table th { background: #f8f8f8; font-weight: 900; text-transform: uppercase; font-size: 0.85rem; color: #555; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: #fafafa; }
        .action-btn { background: #eee; color: #333; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; margin-right: 5px; font-weight: 700; transition: 0.2s; }
        .action-btn:hover { background: #ddd; }
        .action-btn.delete { color: #d32f2f; background: #ffebee; }
        .action-btn.delete:hover { background: #ffcdd2; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="../index.php" class="logo-link">
                    <img src="../assets/images/favicon.png?v=2" alt="NSBM Store Logo" class="logo-icon" height="40">
                    <span>NSBM STORE <span style="font-weight:400; font-size:1rem; color:#888;">ADMIN</span></span>
                </a>
            </div>
            <ul class="nav-icons">
                <li><a href="dashboard.php" style="color: var(--nsbm-green); font-weight: bold;" title="Products">Products</a></li>
                <li><a href="orders.php" title="Orders">Orders</a></li>
                <li><a href="../index.php" title="View Site"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <div class="dashboard-header">
            <h1>Manage Products</h1>
            <a href="add_product.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Product</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price (LKR)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td style="font-weight:700;"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                    <td><?php echo number_format($p['price'], 2); ?></td>
                    <td><?php echo isset($p['stock_quantity']) ? $p['stock_quantity'] : 0; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="action-btn"><i class="fa-solid fa-pen"></i> Edit</a>
                        <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fa-solid fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
