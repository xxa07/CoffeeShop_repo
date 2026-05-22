<?php
require_once 'session_bootstrap.php';   // hardened session: HttpOnly, SameSite, idle timeout, fingerprint
require_once 'connection.php';

if (!isset($_POST['place_order'])) {
    header('Location: index.php');
    exit();
}

if (empty($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id      = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);
$product_name = trim($_POST['product_name'] ?? '');
$price        = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

if ($user_id === false || $user_id <= 0) {
    header('Location: login.php?error=session');
    exit();
}

if ($product_name === '' || strlen($product_name) > 100) {
    header('Location: index.php?error=product');
    exit();
}

if ($price === false || $price <= 0 || $price > 99999) {
    header('Location: index.php?error=price');
    exit();
}

// Prepared statement: defends against SQL injection.
$stmt = $conn->prepare('INSERT INTO orders (user_id, product_name, price) VALUES (?, ?, ?)');
if ($stmt) {
    $stmt->bind_param('isd', $user_id, $product_name, $price);
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: index.php?ordered=1');
        exit();
    }
    $stmt->close();
}

header('Location: index.php?error=order');
exit();
