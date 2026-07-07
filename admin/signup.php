<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if username/email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        $error = "An account with this email already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:email, :password)");
        if ($stmt->execute(['email' => $email, 'password' => $password])) {
            $new_user_id = $pdo->lastInsertId();
            
            // Auto login the newly registered user
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $new_user_id;
            $_SESSION["username"] = $email;
            
            // Redirect directly to the marketplace storefront
            header("location: ../index.php");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - NSBM Store</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Extra styles for the signup form */
        .auth-card { max-width: 500px; } /* Slightly wider for more fields */
        .form-row { display: flex; gap: 15px; }
        .form-row .auth-input { flex: 1; }
        .select-input { width: 100%; padding: 15px; margin-bottom: 20px; border: 1px solid var(--border-grey); border-radius: 8px; font-family: var(--font-main); font-size: 0.9rem; font-weight: 700; outline: none; background: #fafafa; }
    </style>
</head>
<body>
    <div class="auth-wrapper" style="padding: 40px 0;">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="../assets/images/logo.png?v=2" alt="NSBM Store Logo" style="height: 110px; width: auto;">
            </div>
            <h2>Create Account</h2>
            <p style="text-align:center; color:#666; margin-bottom:30px; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Join the NSBM marketplace</p>
            
            <?php if($error) echo "<p style='color:red; text-align:center; margin-bottom:15px; font-size:0.8rem; font-weight:700;'>$error</p>"; ?>
            <?php if($success) echo "<p style='color:var(--nsbm-green); text-align:center; margin-bottom:15px; font-size:0.8rem; font-weight:900;'>$success</p>"; ?>
            
            <form action="signup.php" method="post">
                <input type="text" name="fullname" class="auth-input" placeholder="FULL NAME" required>
                <input type="email" name="email" class="auth-input" placeholder="EMAIL ADDRESS" required>
                <input type="password" name="password" class="auth-input" placeholder="PASSWORD" required>
                <input type="text" name="phone" class="auth-input" placeholder="PHONE NUMBER" required>
                
                <h3 style="font-size: 0.9rem; margin-top: 10px; margin-bottom: 10px; text-transform:uppercase;">Delivery Details</h3>
                
                <select name="delivery_type" class="select-input" required>
                    <option value="" disabled selected>SELECT DELIVERY TYPE</option>
                    <option value="campus">On-Campus Delivery (NSBM)</option>
                    <option value="home">Home Delivery</option>
                </select>
                
                <input type="text" name="address" class="auth-input" placeholder="FULL ADDRESS (Or Batch/Batch ID if Campus)">
                
                <div class="form-row">
                    <input type="text" name="city" class="auth-input" placeholder="CITY">
                    <input type="text" name="postal" class="auth-input" placeholder="POSTAL CODE">
                </div>

                <button type="submit" class="auth-btn" style="margin-top: 10px;">CREATE ACCOUNT</button>
            </form>
            
            <p style="text-align:center; margin-top: 25px; font-size:0.85rem; font-weight:700; color: #555;">Already have an account? <a href="login.php" style="color: var(--nsbm-green); text-decoration: none;">Login</a></p>
            <p style="text-align:center; margin-top: 15px;"><a href="../index.php" style="color: #888; text-decoration: none; font-size:0.8rem; font-weight:700;">&larr; BACK TO MARKETPLACE</a></p>
        </div>
    </div>
</body>
</html>