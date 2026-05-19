<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberNeet Inventory</title>
    <link rel="stylesheet" href="/finalProj/public/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h2>CyberNeet - Inventory System</h2>
            </div>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/finalProj/views/dashboard.php">Dashboard</a>
                    <a href="/finalProj/views/products.php">Products</a>
                    <span class="user-info"><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="/finalProj/logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="/finalProj/index.php">Login</a>
                    <a href="/finalProj/views/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
