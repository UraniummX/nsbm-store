<?php

// Backend Logic
session_start();

$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $success = "If an account exists with this email, a reset code has been sent.";
}
?>

<?php // View Output ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - NSBM Store</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="../assets/images/logo.png?v=2" alt="NSBM Store Logo" style="height: 110px; width: auto;">
            </div>
            <h2>Reset Password</h2>
            <p style="text-align:center; color:#666; margin-bottom:30px; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Enter your email to receive a code</p>
            
            <?php if($success) echo "<p style='color:var(--nsbm-green); text-align:center; margin-bottom:15px; font-size:0.8rem; font-weight:900; background: #e8f5e9; padding: 10px; border-radius: 5px;'>$success</p>"; ?>
            
            <form action="forgot_password.php" method="post">
                <input type="email" name="email" class="auth-input" placeholder="EMAIL ADDRESS" required>
                <button type="submit" class="auth-btn">SEND RESET CODE</button>
            </form>
            
            <p style="text-align:center; margin-top: 25px;"><a href="login.php" style="color: var(--nsbm-green); text-decoration: none; font-size:0.85rem; font-weight:700;">&larr; BACK TO LOGIN</a></p>
        </div>
    </div>
</body>
</html>