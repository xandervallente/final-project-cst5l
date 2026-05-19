<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /finalProj/index.php");
    exit();
}

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../controllers/product.php';
require_once __DIR__ . '/../public/database.config.php';

$id     = $_POST['id']     ?? "";
$amount = $_POST['amount'] ?? "";
$action = $_POST['action'] ?? "";

// Validate
$valid_actions = ["add", "subtract"];
if (empty($id) || !ctype_digit($id) ||
    empty($amount) || !ctype_digit($amount) || (int)$amount <= 0 ||
    !in_array($action, $valid_actions)) {
    header("Location: /finalProj/views/products.php");
    exit();
}

$controller = new ProductController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$controller->adjustStock((int)$id, (int)$amount, $action);

header("Location: /finalProj/views/products.php");
exit();
