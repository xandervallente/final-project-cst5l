<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /finalProj/index.php");
    exit();
}

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../controllers/product.php';
require_once __DIR__ . '/../public/database.config.php';

$errors  = [];
$success = "";

$id = $_GET["id"] ?? "";

// Validate the ID from URL
if (empty($id) || !ctype_digit($id)) {
    header("Location: /finalProj/views/products.php");
    exit();
}

$controller = new ProductController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$product    = $controller->getOne((int)$id);

// Redirect if product doesn't exist
if (!$product) {
    header("Location: /finalProj/views/products.php");
    exit();
}

// Pre-fill form with existing product data
$form = [
    "name"        => $product["name"],
    "description" => $product["description"],
    "price"       => $product["price"],
    "quantity"    => $product["quantity"]
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_product"])) {

    // Sanitize inputs
    $form["name"]        = trim($_POST["name"] ?? "");
    $form["description"] = trim($_POST["description"] ?? "");
    $form["price"]       = trim($_POST["price"] ?? "");
    $form["quantity"]    = trim($_POST["quantity"] ?? "");

    // Validate: required fields
    if (empty($form["name"])) {
        $errors[] = "Product name is required.";
    } elseif (strlen($form["name"]) > 150) {
        $errors[] = "Product name must not exceed 150 characters.";
    }

    if (empty($form["description"])) {
        $errors[] = "Description is required.";
    }

    if ($form["price"] === "") {
        $errors[] = "Price is required.";
    } elseif (!is_numeric($form["price"])) {
        $errors[] = "Price must be a valid number.";
    } elseif ((float)$form["price"] < 0) {
        $errors[] = "Price cannot be negative.";
    }

    if ($form["quantity"] === "") {
        $errors[] = "Quantity is required.";
    } elseif (!ctype_digit($form["quantity"])) {
        $errors[] = "Quantity must be a whole number.";
    } elseif ((int)$form["quantity"] < 0) {
        $errors[] = "Quantity cannot be negative.";
    }

    // Only save if no errors
    if (empty($errors)) {
        if ($controller->edit((int)$id, $form["name"], $form["description"], (float)$form["price"], (int)$form["quantity"])) {
            $success = "Product updated successfully!";
            $product = $controller->getOne((int)$id);
        } else {
            $errors[] = "Failed to update product. Please try again.";
        }
    }
}
?>

<?php require __DIR__ . '/partial/header.php'; ?>

<div class="form-container">
    <h1>Edit Product</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
        <a href="/finalProj/views/products.php" class="btn btn-primary">View All Products</a>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label>Product Name: <span class="required">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($form["name"]) ?>" maxlength="150" placeholder="e.g. Wireless Mouse">

        <label>Description: <span class="required">*</span></label>
        <textarea name="description" placeholder="Brief description of the product"><?= htmlspecialchars($form["description"]) ?></textarea>

        <label>Price (₱): <span class="required">*</span></label>
        <input type="number" name="price" value="<?= htmlspecialchars($form["price"]) ?>" step="0.01" min="0" placeholder="e.g. 999.00">

        <label>Quantity: <span class="required">*</span></label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($form["quantity"]) ?>" min="0" placeholder="e.g. 50">

        <p class="required-note"><span class="required">*</span> All fields are required.</p>

        <div style="margin-top: 25px; display: flex; gap: 10px; align-items: center;">
            <button type="submit" name="edit_product" class="btn btn-warning" style="margin:0; height:42px;">Update Product</button>
            <a href="/finalProj/views/products.php" class="btn btn-secondary" style="margin:0; height:42px; display:inline-flex; align-items:center;">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/partial/footer.php'; ?>
