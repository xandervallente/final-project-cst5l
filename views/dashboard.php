<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /finalProj/index.php");
    exit();
}

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../controllers/product.php';
require_once __DIR__ . '/../public/database.config.php';

$controller = new ProductController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$stats      = $controller->getStats();
$low_stock  = $controller->getLowStock(5);
$recent     = $controller->getRecent(5);
?>

<?php require __DIR__ . '/partial/header.php'; ?>

<div style="display:flex; flex-direction:column; gap:20px;">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:24px 30px; box-shadow:0 4px 20px rgba(0,0,0,0.3); flex-wrap:wrap; gap:16px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#64b5f6; margin-bottom:5px; letter-spacing:0.3px;">Inventory Dashboard</h1>
            <p style="font-size:14px; color:#90a4ae;">Welcome back, <strong style="color:#90caf9;"><?= htmlspecialchars($_SESSION['username']) ?></strong> &mdash; here is your inventory at a glance.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="/finalProj/views/products.php" class="btn btn-primary">View Products</a>
            <a href="/finalProj/views/product-add.php" class="btn btn-success">Add Product</a>
            <a href="/finalProj/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px;">

        <!-- Total Products -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:22px 24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div>
                <p style="font-size:11px; color:#90a4ae; text-transform:uppercase; letter-spacing:0.8px; font-weight:500; margin-bottom:8px;">Total Products</p>
                <p style="font-size:28px; font-weight:700; color:#e8eaf6; line-height:1;"><?= number_format($stats['total_products']) ?></p>
            </div>
            <div style="width:50px; height:50px; border-radius:12px; background:rgba(33,150,243,0.12); border:1px solid rgba(33,150,243,0.25); color:#64b5f6; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z"/></svg>
            </div>
        </div>

        <!-- In Stock -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:22px 24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div>
                <p style="font-size:11px; color:#90a4ae; text-transform:uppercase; letter-spacing:0.8px; font-weight:500; margin-bottom:8px;">In Stock</p>
                <p style="font-size:28px; font-weight:700; color:#a5d6a7; line-height:1;"><?= number_format($stats['in_stock']) ?></p>
            </div>
            <div style="width:50px; height:50px; border-radius:12px; background:rgba(46,125,50,0.12); border:1px solid rgba(46,125,50,0.25); color:#a5d6a7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Out of Stock -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:22px 24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div>
                <p style="font-size:11px; color:#90a4ae; text-transform:uppercase; letter-spacing:0.8px; font-weight:500; margin-bottom:8px;">Out of Stock</p>
                <p style="font-size:28px; font-weight:700; color:#ef9a9a; line-height:1;"><?= number_format($stats['out_of_stock']) ?></p>
            </div>
            <div style="width:50px; height:50px; border-radius:12px; background:rgba(183,28,28,0.12); border:1px solid rgba(183,28,28,0.25); color:#ef9a9a; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Total Value -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:22px 24px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div>
                <p style="font-size:11px; color:#90a4ae; text-transform:uppercase; letter-spacing:0.8px; font-weight:500; margin-bottom:8px;">Total Inventory Value</p>
                <p style="font-size:22px; font-weight:700; color:#ffcc80; line-height:1;">&#8369;<?= number_format((float)$stats['total_value'], 2) ?></p>
            </div>
            <div style="width:50px; height:50px; border-radius:12px; background:rgba(245,124,0,0.12); border:1px solid rgba(245,124,0,0.25); color:#ffcc80; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

    </div>

    <!-- Bottom Grid -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

        <!-- Low Stock Alert -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid #1a3a5c;">
                <div>
                    <h2 style="font-size:15px; font-weight:600; color:#e8eaf6; margin-bottom:3px;">Low Stock Alert</h2>
                    <p style="font-size:12px; color:#90a4ae;">Products with quantity of 5 or below</p>
                </div>
                <span style="font-size:12px; font-weight:600; padding:4px 10px; border-radius:20px; flex-shrink:0; <?= count($low_stock) > 0 ? 'background:rgba(183,28,28,0.2); color:#ef9a9a; border:1px solid #b71c1c;' : 'background:rgba(46,125,50,0.2); color:#a5d6a7; border:1px solid #2e7d32;' ?>">
                    <?= count($low_stock) ?> item<?= count($low_stock) !== 1 ? 's' : '' ?>
                </span>
            </div>
            <?php if (empty($low_stock)): ?>
                <div style="padding:30px 20px; text-align:center; color:#90a4ae; font-size:14px;">
                    <p style="color:#a5d6a7;">All products are sufficiently stocked.</p>
                </div>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Product Name</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Price</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Qty</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock as $item): ?>
                            <tr>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);"><?= htmlspecialchars($item['name']) ?></td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);">&#8369;<?= number_format((float)$item['price'], 2) ?></td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);"><span class="badge badge-warning"><?= $item['quantity'] ?></span></td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);"><a href="/finalProj/views/product-edit.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-small">Restock</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Recently Added -->
        <div style="background:linear-gradient(135deg,#12121a,#1c1c2e); border:1px solid #1a3a5c; border-radius:12px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid #1a3a5c;">
                <div>
                    <h2 style="font-size:15px; font-weight:600; color:#e8eaf6; margin-bottom:3px;">Recently Added</h2>
                    <p style="font-size:12px; color:#90a4ae;">Last 5 products added to inventory</p>
                </div>

            </div>
            <?php if (empty($recent)): ?>
                <div style="padding:30px 20px; text-align:center; color:#90a4ae; font-size:14px;">
                    <p>No products yet. <a href="/finalProj/views/product-add.php" style="color:#64b5f6;">Add one now</a>.</p>
                </div>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Product Name</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Price</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Qty</th>
                            <th style="padding:9px 12px; text-align:left; font-size:11px; font-weight:600; color:#64b5f6; text-transform:uppercase; letter-spacing:0.7px; border-bottom:1px solid #1a3a5c;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $item): ?>
                            <tr>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);"><?= htmlspecialchars($item['name']) ?></td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);">&#8369;<?= number_format((float)$item['price'], 2) ?></td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);">
                                    <?php if ((int)$item['quantity'] > 0): ?>
                                        <span class="badge badge-success"><?= $item['quantity'] ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">0</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px; font-size:13px; color:#e8eaf6; border-bottom:1px solid rgba(26,58,92,0.4);"><a href="/finalProj/views/product-edit.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-small">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php require __DIR__ . '/partial/footer.php'; ?>
