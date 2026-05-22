<?php
// NOTE (Vulnerable build): plain session_start() with PHP defaults.
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'connection.php';

if (isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $price_input = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';

    $allowed_categories = ['drink', 'food'];
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    $price = filter_var($price_input, FILTER_VALIDATE_FLOAT);
    $valid_upload = isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK;

    if ($name === '' || strlen($name) > 100) {
        echo "<script>alert('Invalid product name.');</script>";
    } elseif ($price === false || $price <= 0) {
        echo "<script>alert('Invalid product price.');</script>";
    } elseif (strlen($description) > 500) {
        echo "<script>alert('Description is too long.');</script>";
    } elseif (!in_array($category, $allowed_categories, true)) {
        echo "<script>alert('Invalid category selected.');</script>";
    } elseif (!$valid_upload) {
        echo "<script>alert('Product image upload failed.');</script>";
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $finfo ? finfo_file($finfo, $_FILES['product_image']['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        $image_name = basename($_FILES['product_image']['name']);
        $target = "images/" . $image_name;

        if ($image_name === '' || !in_array($mime_type, $allowed_mime_types, true)) {
            echo "<script>alert('Invalid image type.');</script>";
        } elseif (move_uploaded_file($_FILES['product_image']['tmp_name'], $target)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sdsss", $name, $price, $description, $image_name, $category);
                if ($stmt->execute()) {
                    echo "<script>alert('Product added successfully as " . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . "!'); window.location.href='add_product.php?msg=added';</script>";
                    exit();
                } else {
                    echo "<script>alert('Failed to add product.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Failed to add product.');</script>";
            }
        } else {
            echo "<script>alert('Failed to save uploaded image.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; color: #333; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #6F4E37; font-weight: bold; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <a class="back-link" href="admin.php">Back to Admin Panel</a>
    <h2>Add New Product</h2>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success">Product added successfully!</div>
    <?php endif; ?>

    <form action="add_product.php" method="POST" enctype="multipart/form-data" style="max-width: 500px; margin: auto;">
        <input type="text" name="name" placeholder="Product Name" required maxlength="100" style="width:100%; padding:10px; margin-bottom:10px;">
        <input type="number" step="0.01" min="0.01" name="price" placeholder="Price" required style="width:100%; padding:10px; margin-bottom:10px;">

        <select name="category" required style="width:100%; padding:10px; margin-bottom:10px;">
            <option value="drink"> Drink</option>
            <option value="food"> Food </option>
        </select>

        <input type="file" name="product_image" required accept="image/*" style="width:100%; margin-bottom:10px;">
        <textarea name="description" placeholder="Description" maxlength="500" style="width:100%; padding:10px; margin-bottom:10px;"></textarea>

        <button type="submit" name="add_product" style="width:100%; padding:10px; background:#6F4E37; color:white; border:none;">Save Product</button>
    </form>
</div>

</body>
</html>
