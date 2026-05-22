<?php
session_start();
include 'connection.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $plain_password = $_POST['password'] ?? '';
    $role = 'user'; 

    if ($username === '' || strlen($username) < 3 || strlen($username) > 50 || !preg_match('/^[A-Za-z0-9_ ]+$/', $username)) {
        $error = "Username must be 3-50 characters and only letters, numbers, spaces, or underscore.";
    } elseif ($plain_password === '' || strlen($plain_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $password = password_hash($plain_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $username, $password, $role);
            if ($stmt->execute()) {
                $success = "Account created successfully! <a href='login.php'>Login here</a>";
            } else {
                $error = "Failed to create account.";
            }
            $stmt->close();
        } else {
            $error = "Failed to create account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - CoffeeShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background: #fff; padding: 40px 30px; border-radius: 15px; width: 100%; max-width: 420px; box-shadow: 0 15px 50px rgba(0,0,0,0.3); text-align: center; }
        h2 { margin-bottom: 10px; color: #333; font-size: 28px; }
        input { width: 100%; padding: 14px; margin: 12px 0; border-radius: 8px; border: 2px solid #e0e0e0; font-size: 15px; background: #f8f9fa; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #c9a227 0%, #d4af37 100%); color: #1a1a2e; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .error { background: #fee; color: #c33; border-right: 4px solid #c33; text-align: right; }
        .success { background: #e6ffed; color: #155724; border-right: 4px solid #2e7d32; text-align: right; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .footer a { color: #c9a227; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <?php if (!empty($error)) : ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_ ]{3,50}">
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <button type="submit" name="register">Create Account</button>
    </form>

    <div class="footer">
        <a href="login.php">Already have an account? Login</a>
    </div>
</div>

</body>
</html>
