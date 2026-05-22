<?php
require_once 'session_bootstrap.php';   // hardened session: HttpOnly, SameSite, idle timeout, fingerprint
require_once 'connection.php';

// Admin-only page
if (empty($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

$form_error = '';

if (isset($_POST['add_product'])) {

    // Server-side input handling (client-side rules are repeated here).
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = $_POST['category'] ?? '';
    $price       = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

    $allowed_categories = ['drink', 'food'];
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $valid_upload = isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK;

    // Sanitize the description so any HTML/JS submitted is rendered as text.
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

    if (!preg_match('/^[\p{L}\p{N} \-_.()]{2,100}$/u', $name)) {
        $form_error = 'Invalid product name.';
    } elseif ($price === false || $price <= 0 || $price > 99999) {
        $form_error = 'Invalid product price.';
    } elseif (strlen($description) > 500) {
        $form_error = 'Description is too long.';
    } elseif (!in_array($category, $allowed_categories, true)) {
        $form_error = 'Invalid category selected.';
    } elseif (!$valid_upload) {
        $form_error = 'Product image upload failed.';
    } else {
        // Validate the uploaded image by real MIME type (not just extension).
        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $finfo ? finfo_file($finfo, $_FILES['product_image']['tmp_name']) : '';
        if ($finfo) { finfo_close($finfo); }

        $image_name = basename($_FILES['product_image']['name']);
        $target     = 'images/' . $image_name;

        if ($image_name === '' || !in_array($mime_type, $allowed_mime_types, true)) {
            $form_error = 'Invalid image type.';
        } elseif (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target)) {
            $form_error = 'Failed to save uploaded image.';
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param('sdsss', $name, $price, $description, $image_name, $category);
                if ($stmt->execute()) {
                    header('Location: add_product.php?msg=added');
                    exit();
                }
                $form_error = 'Failed to add product.';
                $stmt->close();
            } else {
                $form_error = 'Failed to add product.';
            }
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

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
        <div class="alert alert-success">Product added successfully!</div>
    <?php endif; ?>

    <?php if (!empty($form_error)): ?>
        <div class="alert" style="background:#f8d7da; color:#721c24;">
            <?php echo htmlspecialchars($form_error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form id="productForm" action="add_product.php" method="POST"
          enctype="multipart/form-data"
          onsubmit="return validateProduct();"
          style="max-width: 500px; margin: auto;">

        <input type="text" name="name" placeholder="Product Name" required
               minlength="2" maxlength="100"
               style="width:100%; padding:10px; margin-bottom:10px;">

        <input type="number" step="0.01" min="0.01" max="99999" name="price"
               placeholder="Price" required
               style="width:100%; padding:10px; margin-bottom:10px;">

        <select name="category" required
                style="width:100%; padding:10px; margin-bottom:10px;">
            <option value="drink">Drink</option>
            <option value="food">Food</option>
        </select>

        <input type="file" name="product_image" required
               accept="image/png,image/jpeg,image/gif,image/webp"
               style="width:100%; margin-bottom:10px;">

        <textarea name="description" placeholder="Description"
                  maxlength="500"
                  style="width:100%; padding:10px; margin-bottom:10px;"></textarea>

        <button type="submit" name="add_product"
                style="width:100%; padding:10px; background:#6F4E37; color:white; border:none;">
            Save Product
        </button>
    </form>
</div>

<script>
// Client-side validation: blocks obvious mistakes before the request leaves
// the browser. The same checks are repeated on the server.
function validateProduct() {
    const f = document.getElementById('productForm');
    const name = f.name.value.trim();
    const price = parseFloat(f.price.value);
    if (name.length < 2 || name.length > 100) {
        alert('Product name must be between 2 and 100 characters.');
        return false;
    }
    if (isNaN(price) || price <= 0 || price > 99999) {
        alert('Price must be a positive number.');
        return false;
    }
    return true;
}
</script>

</body>
</html>
