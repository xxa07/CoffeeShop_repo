<?php
require_once 'session_bootstrap.php';   // hardened session: HttpOnly, SameSite, idle timeout, fingerprint
require_once 'connection.php';

// Admin-only page
if (empty($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// --- (A) Delete section ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT);

    if ($id_to_delete !== false && $id_to_delete > 0) {
        // Use prepared statement for safe deletion
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_to_delete); // i means integer

            if ($stmt->execute()) {
                header("Location: admin.php?msg=deleted");
                exit();
            }
            $stmt->close();
        }
    }
}

// 2. Get data to display
$result = false;
$products_stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
if ($products_stmt) {
    $products_stmt->execute();
    $result = $products_stmt->get_result();
    $products_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Manage Drinks</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; color: #333; }
        form { display: grid; gap: 15px; background: #fafafa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        input, textarea { padding: 12px; border: 1px solid #ddd; border-radius: 5px; width: 100%; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .btn-delete { color: #dc3545; text-decoration: none; font-weight: bold; }
        .btn-delete:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .dashboard-actions { margin-bottom: 18px; }
        .add-product-link { display: inline-block; background: #6F4E37; color: white; text-decoration: none; padding: 10px 16px; border-radius: 6px; font-weight: bold; }
        .add-product-link:hover { background: #5a3f2d; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Admin Panel</h2>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="dashboard-actions">
        <a href="add_product.php" class="add-product-link">Add New Product</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-danger">Product deleted successfully!</div>
    <?php endif; ?>

    <h2>Current Products</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($result && ($row = $result->fetch_assoc())): ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                <td><?= htmlspecialchars($row['price']) ?> SAR</td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <a href="admin.php?delete_id=<?= (int) $row['id'] ?>" 
                       class="btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this drink?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<style>
.customer-orders-title {
    width: 90%;
    margin: 50px auto 12px;
    color: #2b2b2b;
    font-family: Arial, sans-serif;
    font-size: 28px;
    font-weight: 700;
    text-align: left;
}

.customer-orders-table {
    width: 90%;
    margin: 0 auto 30px;
    border-collapse: collapse;
    background: #ffffff;
    border: 1px solid #d6d1cc;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.customer-orders-table th,
.customer-orders-table td {
    padding: 14px 12px;
    border: 1px solid #d6d1cc;
    text-align: center;
    color: #222;
    font-family: Arial, sans-serif;
}

.customer-orders-table th {
    background-color: #4B2E2B;
    color: #ffffff;
    font-weight: 700;
}

.customer-orders-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

.customer-orders-table tbody tr:nth-child(even) {
    background-color: #f7f3ee;
}

.customer-orders-table tbody tr:hover {
    background-color: #ede3d7;
}
</style>

<h2 class="customer-orders-title">Customer Orders</h2>
<table class="customer-orders-table">
    <tr>
        <th>Order ID</th>
        <th>Customer ID</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Order Date</th>
    </tr>
    <?php
    $order_stmt = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC");
    $order_result = false;
    if ($order_stmt) {
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        $order_stmt->close();
    }
    while($order_result && ($order = mysqli_fetch_assoc($order_result))) {
        echo "<tr>
                <td>" . htmlspecialchars($order['id']) . "</td>
                <td>" . htmlspecialchars($order['user_id']) . "</td>
                <td>" . htmlspecialchars($order['product_name']) . "</td>
                <td>" . htmlspecialchars($order['price']) . " SAR</td>
                <td>" . htmlspecialchars($order['order_date']) . "</td>
              </tr>";
    }
    ?>
</table>
