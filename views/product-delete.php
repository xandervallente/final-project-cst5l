<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../controllers/product.php';
require_once __DIR__ . '/../public/database.config.php';

$id = $_GET["id"] ?? "";

if (empty($id)) {
    header("Location: /views/products.php");
    exit();
}

$controller = new ProductController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$controller->delete($id);

header("Location: /views/products.php");
exit();
