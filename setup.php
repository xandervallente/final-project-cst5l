<?php
// ============================================================
//  CyberNeet Inventory System - Database Setup Script
//  Run this file once to create the database and tables
//  Access via: http://localhost/finalProj/setup.php
// ============================================================

require_once __DIR__ . '/public/database.config.php';

// Connect without selecting a database first
$conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, '', $PORT);

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

$steps = [];

// ── Step 1: Create Database ──────────────────────────────────
$sql = "CREATE DATABASE IF NOT EXISTS `inventory_system` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql)) {
    $steps[] = ["success", "Database <strong>inventory_system</strong> created or already exists."];
} else {
    $steps[] = ["error", "Failed to create database: " . $conn->error];
}

// Select the database
$conn->select_db($DB_NAME);

// ── Step 2: Create users table ───────────────────────────────
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(100) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    $steps[] = ["success", "Table <strong>users</strong> created or already exists."];
} else {
    $steps[] = ["error", "Failed to create users table: " . $conn->error];
}

// ── Step 3: Create products table ───────────────────────────
$sql = "CREATE TABLE IF NOT EXISTS `products` (
    `id`          INT(11)        NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150)   NOT NULL,
    `description` TEXT           NOT NULL,
    `price`       DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `quantity`    INT(11)        NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    $steps[] = ["success", "Table <strong>products</strong> created or already exists."];
} else {
    $steps[] = ["error", "Failed to create products table: " . $conn->error];
}

// ── Step 4: Insert default admin account ────────────────────
$check = $conn->query("SELECT id FROM users WHERE username = 'admin'");
if ($check->num_rows === 0) {
    $hashed = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $admin, $hashed);
    $admin = "admin";
    if ($stmt->execute()) {
        $steps[] = ["success", "Default admin account created — username: <strong>admin</strong> / password: <strong>admin123</strong>"];
    } else {
        $steps[] = ["error", "Failed to create admin account: " . $stmt->error];
    }
} else {
    $steps[] = ["info", "Admin account already exists, skipped."];
}

// ── Step 5: Insert sample products ──────────────────────────
$check = $conn->query("SELECT COUNT(*) AS total FROM products");
$row   = $check->fetch_assoc();
if ((int)$row['total'] === 0) {
    $samples = [
        ["Laptop",               "High performance laptop with 16GB RAM and 512GB SSD",   45999.00, 10],
        ["Wireless Mouse",       "Ergonomic wireless mouse with long battery life",          799.00, 50],
        ["USB-C Hub",            "7-in-1 USB-C hub with HDMI, USB 3.0, and SD card slot",  1299.00, 30],
        ["Mechanical Keyboard",  "Compact TKL mechanical keyboard with RGB lighting",       2499.00, 20],
        ["Monitor",              "24-inch Full HD IPS monitor with 75Hz refresh rate",     12999.00,  8],
    ];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $name, $desc, $price, $qty);

    foreach ($samples as $sample) {
        [$name, $desc, $price, $qty] = $sample;
        $stmt->execute();
    }
    $steps[] = ["success", "Sample products inserted successfully."];
} else {
    $steps[] = ["info", "Products table already has data, skipped sample insert."];
}

$conn->close();

// ── Check for any errors ─────────────────────────────────────
$has_error = false;
foreach ($steps as $step) {
    if ($step[0] === "error") {
        $has_error = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Setup — CyberNeet Inventory</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0a0a0f; color: #e8eaf6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: linear-gradient(135deg, #12121a, #1c1c2e); border: 1px solid #1a3a5c; border-radius: 12px; padding: 40px; width: 100%; max-width: 560px; box-shadow: 0 8px 32px rgba(0,0,0,0.4); }
        h1 { color: #64b5f6; font-size: 22px; margin-bottom: 6px; }
        .subtitle { color: #90a4ae; font-size: 13px; margin-bottom: 30px; }
        .step { display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border-radius: 8px; margin-bottom: 10px; font-size: 14px; }
        .step-success { background: rgba(46,125,50,0.15); border: 1px solid #2e7d32; color: #a5d6a7; }
        .step-error   { background: rgba(183,28,28,0.15); border: 1px solid #b71c1c; color: #ef9a9a; }
        .step-info    { background: rgba(30,96,145,0.15); border: 1px solid #1e6091; color: #90caf9; }
        .icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .result { text-align: center; margin-top: 24px; padding: 16px; border-radius: 8px; font-size: 15px; font-weight: 600; }
        .result-success { background: rgba(46,125,50,0.2); border: 1px solid #2e7d32; color: #a5d6a7; }
        .result-error   { background: rgba(183,28,28,0.2); border: 1px solid #b71c1c; color: #ef9a9a; }
        .btn { display: block; text-align: center; margin-top: 20px; padding: 12px; background: linear-gradient(135deg, #1e6091, #2196f3); color: white; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600; }
        .btn:hover { background: linear-gradient(135deg, #2196f3, #42a5f5); }
        .warning { margin-top: 20px; padding: 12px 14px; background: rgba(230,81,0,0.15); border: 1px solid #e65100; border-radius: 8px; font-size: 13px; color: #ffcc80; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Database Setup</h1>
        <p class="subtitle">CyberNeet Inventory Management System</p>

        <?php foreach ($steps as $step): ?>
            <div class="step step-<?= $step[0] ?>">
                <span class="icon">
                    <?php if ($step[0] === 'success') echo '✓';
                    elseif ($step[0] === 'error')   echo '✗';
                    else                             echo 'i'; ?>
                </span>
                <span><?= $step[1] ?></span>
            </div>
        <?php endforeach; ?>

        <?php if (!$has_error): ?>
            <div class="result result-success">Setup completed successfully!</div>
            <a href="/finalProj/index.php" class="btn">Go to Login Page</a>
            <p class="warning">Important: Delete or rename <strong>setup.php</strong> after setup to prevent unauthorized access.</p>
        <?php else: ?>
            <div class="result result-error">Setup encountered errors. Check the messages above.</div>
        <?php endif; ?>
    </div>
</body>
</html>
