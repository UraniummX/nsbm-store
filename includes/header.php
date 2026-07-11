<?php

// Backend Logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_count = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $cart_count += isset($item['qty']) ? $item['qty'] : 0;
    }
}

$header_categories = [];
if (isset($pdo)) {
    try {
        $header_categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        
    }
}
?>

<?php // View Output ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSBM Campus Market</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        // Apply saved theme before render to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php" class="logo-link">
                    <img src="assets/images/favicon.png?v=2" alt="NSBM Store Logo" class="logo-icon" height="40">
                    <span>NSBM STORE</span>
                </a>
            </div>
            <ul class="nav-icons">
                <li><a href="index.php" title="Home"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="admin/login.php" title="Sign Up"><i class="fa-solid fa-user-plus"></i></a></li>
                <li class="cart-icon">
                    <a href="cart.php" title="View Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-badge" class="badge"><?php echo $cart_count; ?></span>
                    </a>
                </li>
                
                <li>
                    <button id="theme-toggle-btn" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                        <i class="fa-regular fa-moon" id="theme-icon"></i>
                    </button>
                </li>
                
                <li>
                    <a href="javascript:void(0)" id="settings-toggle" title="Settings">
                        <i class="fa-solid fa-bars"></i>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sub-nav">
            <ul>
                <li><a href="index.php?category=" class="<?php echo (!isset($_GET['category']) || $_GET['category'] === '') ? 'active' : ''; ?>">All</a></li>
                <?php foreach ($header_categories as $cat): ?>
                    <li>
                        <a href="index.php?category=<?php echo $cat['id']; ?>" class="<?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active' : ''; ?>">
                            <?php 
                            $display_name = $cat['name'];
                            if ($display_name === 'Food & Snacks') $display_name = 'Food';
                            if ($display_name === 'Tech & Gadgets') $display_name = 'Tech';
                            echo htmlspecialchars($display_name); 
                            ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form action="index.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
    </header>

    <div class="drawer-overlay" id="drawer-overlay"></div>

    <div class="settings-drawer" id="settings-drawer">
        <div class="settings-header">
            <h2>Settings</h2>
            <button class="drawer-close" id="settings-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="settings-section">
            <h3>Account Profile</h3>
            <div class="account-profile">
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <div class="avatar-circle">
                        <?php echo strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                        <?php if ($_SESSION["username"] === 'admin'): ?>
                            <span class="profile-role">Admin</span>
                            <a href="admin/dashboard.php" class="profile-link"><i class="fa-solid fa-gauge" style="margin-right: 5px;"></i>Dashboard</a>
                        <?php else: ?>
                            <span class="profile-role">Student</span>
                        <?php endif; ?>
                        <a href="admin/logout.php" class="profile-link" style="color: #d32f2f; margin-top: 2px;"><i class="fa-solid fa-right-from-bracket" style="margin-right: 5px;"></i>Log Out</a>
                    </div>
                <?php else: ?>
                    <div class="avatar-circle">G</div>
                    <div class="profile-info">
                        <span class="profile-name">Guest Student</span>
                        <span class="profile-role">Visitor</span>
                        <a href="admin/login.php" class="profile-link"><i class="fa-solid fa-right-to-bracket" style="margin-right: 5px;"></i>Admin Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="settings-section">
            <h3>Appearance</h3>
            <div class="theme-options">
                <button class="theme-btn" id="theme-light-btn">
                    <i class="fa-regular fa-sun"></i> Light
                </button>
                <button class="theme-btn" id="theme-dark-btn">
                    <i class="fa-regular fa-moon"></i> Dark
                </button>
            </div>
        </div>
    </div>

