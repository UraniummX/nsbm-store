<?php

// Backend Logic
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        if (password_verify($password, $row['password'])) {
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $row['id'];
                            $_SESSION["username"] = $row['username'];

                            if ($row['username'] === 'admin') {
                                header("location: dashboard.php");
                            } else {
                                header("location: ../index.php");
                            }
                            exit;
                        } else { $error = "Invalid password."; }
                    }
                } else { $error = "Invalid username."; }
            } else { $error = "System error. Try again later."; }
        }
    }
}
?>

<?php // View Output ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NSBM Store</title>
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
            <h2>Login</h2>
            <p style="text-align:center; color:#666; margin-bottom:30px; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Access your NSBM Store account</p>
            <?php if($error) echo "<p style='color:red; text-align:center; margin-bottom:15px; font-size:0.8rem; font-weight:700;'>$error</p>"; ?>
            <form action="login.php" method="post">
                <input type="text" name="username" class="auth-input" placeholder="EMAIL OR USERNAME" required>
                <input type="password" name="password" class="auth-input" placeholder="PASSWORD" required>
                <div style="text-align: right; margin-bottom: 20px;">
                    <a href="forgot_password.php" style="color: var(--nsbm-green); text-decoration: none; font-size:0.75rem; font-weight:700;">Forgot Password?</a>
                </div>
                <button type="submit" class="auth-btn">LOGIN</button>
            </form>
            <p style="text-align:center; margin-top: 25px; font-size:0.85rem; font-weight:700; color: #555;">Don't have an account? <a href="signup.php" style="color: var(--nsbm-green); text-decoration: none;">Sign Up</a></p>
            <p style="text-align:center; margin-top: 15px;"><a href="../index.php" style="color: #888; text-decoration: none; font-size:0.8rem; font-weight:700;">&larr; BACK TO MARKETPLACE</a></p>
        </div>
    </div>
</body>
</html>
