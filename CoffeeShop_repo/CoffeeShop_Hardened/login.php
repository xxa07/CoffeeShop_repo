<?php
require_once 'session_bootstrap.php';
require_once 'connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Server-side sanitization: trim + length limit
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation: pattern + length
    $valid_user = preg_match('/^[A-Za-z0-9_ ]{3,50}$/', $username);
    $valid_pass = strlen($password) >= 6 && strlen($password) <= 100;

    if ($valid_user && $valid_pass) {

        // Prepared statement (defends against SQL injection)
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // bcrypt verification
            if (password_verify($password, $user['password'])) {

                // Rotate the session id on privilege change (defeats fixation)
                session_regenerate_id(true);

                $_SESSION['user_id']      = (int) $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['role']         = $user['role'];
                $_SESSION['logged_in']    = true;
                $_SESSION['last_activity']= time();

                // Bind the new session to this browser + IP
                $_SESSION['fp'] = hash('sha256',
                    ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' .
                    ($_SERVER['REMOTE_ADDR']     ?? ''));

                $_SESSION['success_message'] = "Login successful!";

                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Username not found";
        }

        $stmt->close();
    } else {
        $error = "Please enter a valid username and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CoffeeShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            text-align: center;
        }

        .logo {
            font-size: 60px;
            margin-bottom: 10px;
        }

        .login-container h2 {
            margin-bottom: 10px;
            color: #333;
            font-size: 28px;
        }

        .login-container p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input:focus {
            outline: none;
            border-color: #c9a227;
            background: #fff;
        }

        input::placeholder {
            color: #999;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c9a227 0%, #d4af37 100%);
            color: #1a1a2e;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(201, 162, 39, 0.4);
        }

        .error {
            color: #c33;
            background: #fee;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-right: 4px solid #c33;
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .footer a {
            color: #c9a227;
            text-decoration: none;
            font-size: 14px;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo">☕</div>
    <h2>CoffeeShop</h2>
    <p>Admin Login</p>

    <?php
    $notice = '';
    if (isset($_GET['timeout'])) { $notice = 'Your session expired due to inactivity. Please log in again.'; }
    if (isset($_GET['hijack']))  { $notice = 'Your session was terminated for security reasons. Please log in again.'; }
    ?>
    <?php if (!empty($notice)) : ?>
        <div class="error"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required maxlength="50">
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        <a href="index.php">Back to Home</a>
    </div>
</div>
<script>
document.querySelector('form').onsubmit = function(e) {
let user = document.querySelector('input[name="username"]').value;
let pass = document.querySelector('input[name="password"]').value;

};
</script>
</body>
</html>
