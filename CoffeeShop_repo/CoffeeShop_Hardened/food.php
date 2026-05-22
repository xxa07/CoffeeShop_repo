<?php
// Hardened bootstrap so the navbar inherits HttpOnly/SameSite headers.
if (session_status() === PHP_SESSION_NONE) {
    require_once 'session_bootstrap.php';
}
require_once 'connection.php';

$result = false;
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
if ($stmt) {
	$category = 'food';
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
	<title>CoffeeShop - Food Menu</title>
	<style>
		body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
		.top-nav { max-width: 1100px; margin: 0 auto 20px; display:flex; justify-content:space-between; align-items:center; }
		.top-nav .links a { color: #6f4e37; text-decoration: none; margin-right: 12px; font-weight: bold; }
		.top-nav .links a.active { text-decoration: underline; }
		.header { text-align: left; margin-bottom: 20px; color: #5d4037; }
		.menu-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1100px; margin: auto; }
		.product-card { background: white; border-radius: 15px; padding: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 5px solid #6f4e37; transition: 0.3s; overflow: hidden; }
		.product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
		.product-name { font-size: 1.25rem; margin: 8px 0 6px 0; color: #333; }
		.product-price { display: inline-block; background: #e8f5e9; color: #2e7d32; padding: 6px 10px; border-radius: 16px; font-weight: bold; font-size: 1rem; }
		.product-desc { color: #666; font-size: 0.95rem; margin-top: 10px; line-height: 1.6; }
		.no-data { text-align: center; grid-column: 1 / -1; font-size: 1.2rem; color: #888; }
		img.product-img { width:100%; height:200px; object-fit:cover; border-radius:10px; margin-bottom:12px; display:block; }
	</style>
</head>
<body>
<?php include 'navbar.php'; ?>

	<div class="top-nav">
		<div class="header">
			<h1> CoffeeShop - Food Menu</h1>
			<p>Delicious food items available</p>
		</div>
	</div>

	<div class="menu-container">
		<?php
		if ($result && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				?>
				<div class="product-card">
					<img class="product-img" src="images/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
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
			echo '<p class="no-data">No food items found.</p>';
		}

		$conn->close();
		?>
	</div>

</body>
</html>

