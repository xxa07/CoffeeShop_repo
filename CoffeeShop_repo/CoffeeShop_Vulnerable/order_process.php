<?php
include 'connection.php';
session_start();

if (isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // if not logged in, redirect to login
        exit();
    }

    $user_id = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);
    $product_name = trim($_POST['product_name'] ?? '');
    $price_input = $_POST['price'] ?? '';
    $price = filter_var($price_input, FILTER_VALIDATE_FLOAT);

    if ($user_id === false || $user_id <= 0) {
        echo "<script>alert('Invalid user session.'); window.location.href='login.php';</script>";
        exit();
    }

    if ($product_name === '' || strlen($product_name) > 100) {
        echo "<script>alert('Invalid product data.'); window.location.href='index.php';</script>";
        exit();
    }

    if ($price === false || $price <= 0) {
        echo "<script>alert('Invalid product price.'); window.location.href='index.php';</script>";
        exit();
    }

    // Use prepared statement to insert order safely
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, price) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isd", $user_id, $product_name, $price);
        if ($stmt->execute()) {
            echo "<script>alert('Order placed successfully!'); window.location.href='index.php';</script>";
            exit();
        }
        $stmt->close();
    }

    // fallback
    echo "<script>alert('Failed to place order.'); window.location.href='index.php';</script>";
}
?>
