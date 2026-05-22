<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<nav style="background: #333; padding: 15px; text-align: center; margin-bottom: 30px; font-family: Arial, sans-serif;">
    <a href="index.php" style="color: white; margin: 0 20px; text-decoration: none; font-weight: bold;"> Drinks</a>
    <a href="food.php" style="color: white; margin: 0 20px; text-decoration: none; font-weight: bold;"> Food</a>

    <?php if (!$isLoggedIn): ?>
        <a href="login.php" style="color: white; margin: 0 20px; text-decoration: none; font-weight: bold;"> Login</a>
        <a href="register.php" style="color: white; margin: 0 15px; text-decoration: none;"> Register</a>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
        <a href="admin.php" style="background: #6F4E37; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; margin-left: 20px;">Admin</a>
        <a href="add_product.php" style="background: #6F4E37; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; margin-left: 10px;">Add Product</a>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <a href="logout.php" style="color: white; margin: 0 15px; text-decoration: none;">Logout</a>
    <?php endif; ?>
</nav>
