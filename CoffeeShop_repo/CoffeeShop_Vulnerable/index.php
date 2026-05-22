<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Include database connection
require_once 'connection.php';

$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// 2. Get data from products table (show only drinks)
$result = false;
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
if ($stmt) {
    $category = 'drink';
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeeShop - Drinks Menu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; color: #5d4037; }
        .menu-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1100px; margin: auto; }
        .product-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 5px solid #6f4e37; transition: 0.3s; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .product-name { font-size: 1.5rem; margin: 0 0 10px 0; color: #333; }
        .product-price { display: inline-block; background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 1.1rem; }
        .product-desc { color: #666; font-size: 0.95rem; margin-top: 15px; line-height: 1.6; }
        .no-data { text-align: center; grid-column: 1 / -1; font-size: 1.2rem; color: #888; }
        .success-alert { max-width: 1100px; margin: 0 auto 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px 15px; font-weight: 600; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="success-alert"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="header">
        <h1> CoffeeShop - Available Drinks</h1>
        <p>Try our best drinks at CoffeeShop</p>
    </div>

    <div class="menu-container">
        <?php
        // 3. Check if data returned from database
        if ($result && $result->num_rows > 0) {
            // "fetch_assoc" gets each row as an array
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($row['image_url']); ?>" alt="product" style="width:100% ; height:200px; object-fit:cover; border-radius:10px; margin-bottom:10px;">
                    <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <span class="product-price"><?php echo htmlspecialchars($row['price']); ?> SAR</span>

                    <form method="POST" action="order_process.php">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($row['price']); ?>">
                        <button type="submit" name="place_order" style="background: #6F4E37; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%;">
                             Order Now
                        </button>
                    </form>

                    <p class="product-desc"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                </div>
                <?php
            }
        } else {
            echo '<p class="no-data">Sorry, no drinks available on the menu.</p>';
        }
        
        // 4. Close database connection for performance optimization
        $conn->close();
        ?>
    </div>

</body>
</html>
