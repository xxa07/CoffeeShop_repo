<?php
require_once 'session_bootstrap.php';
$_SESSION = [];
session_unset();
session_destroy();

// Expire the PHPSESSID cookie on the client side as well.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

header('Location: login.php');
exit();
