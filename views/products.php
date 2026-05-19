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

$keyword = trim($_GET["search"] ?? "");
$filter  = $_GET["filter"] ?? "all";

$allowed_filters = ["all", "in_stock", "out_of_stock"];
if (!in_array($filter, $allowed_filters)) {
    $filter = "all";
}

if ($keyword !== "" || $filter !== "all") {
    $products = $controller->search($keyword, $filter);
} else {
    $products = $controller->getAll();
}
?>

<?php require __DIR__ . '/partial/header.php'; ?>

<div class="products-container">
    <h1>All Products</h1>

    <div class="products-toolbar">
        <div class="toolbar-left">
            <a href="/finalProj/views/product-add.php" class="btn btn-success">Add New Product</a>
            <a href="/finalProj/views/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="/finalProj/views/export-csv.php" class="btn btn-export" style="background: linear-gradient(135deg, #1a237e, #283593); color: #90caf9; border: 2px solid #5c6bc0;">Export Excel</a>
        </div>

        <form method="GET" class="search-form" novalidate>
            <input type="text" name="search" placeholder="Search by name or description..." value="<?= htmlspecialchars($keyword) ?>">
            <select name="filter">
                <option value="all"          <?= $filter === "all"          ? "selected" : "" ?>>All Stock</option>
                <option value="in_stock"     <?= $filter === "in_stock"     ? "selected" : "" ?>>In Stock</option>
                <option value="out_of_stock" <?= $filter === "out_of_stock" ? "selected" : "" ?>>Out of Stock</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($keyword !== "" || $filter !== "all"): ?>
                <a href="/finalProj/views/products.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($keyword !== "" || $filter !== "all"): ?>
        <p class="search-info">
            Showing <strong><?= count($products) ?></strong> result(s)
            <?= $keyword !== "" ? " for <strong>\"" . htmlspecialchars($keyword) . "\"</strong>" : "" ?>
            <?= $filter !== "all" ? " — Filter: <strong>" . htmlspecialchars(str_replace("_", " ", $filter)) . "</strong>" : "" ?>
        </p>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="alert alert-error" style="margin-top: 20px;">
            <p>No products found.
                <?php if ($keyword !== "" || $filter !== "all"): ?>
                    <a href="/finalProj/views/products.php">Clear search</a> or
                <?php endif; ?>
                <a href="/finalProj/views/product-add.php">Add a new product</a>.
            </p>
        </div>
    <?php else: ?>
        <table class="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (₱)</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td>₱<?= number_format((float)$product['price'], 2) ?></td>
                        <td id="qty-<?= $product['id'] ?>"><?= htmlspecialchars($product['quantity']) ?></td>
                        <td>
                            <?php if ((int)$product['quantity'] > 0): ?>
                                <span class="badge badge-success">In Stock</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button
                                class="btn btn-primary btn-small"
                                onclick="openStockModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>', <?= $product['quantity'] ?>)"
                            >Adjust Stock</button>
                            <a href="/finalProj/views/product-edit.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-small">Edit</a>
                            <a href="/finalProj/views/product-delete.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Stock Adjustment Modal -->
<div id="stockModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title">Adjust Stock</h2>
            <button class="modal-close" onclick="closeStockModal()">&times;</button>
        </div>

        <div class="modal-body">
            <p class="modal-product-name" id="modalProductName"></p>
            <p class="modal-current-qty">Current Quantity: <strong id="modalCurrentQty"></strong></p>

            <form method="POST" action="/finalProj/views/adjust-stock.php" id="stockForm">
                <input type="hidden" name="id" id="modalProductId">

                <div class="modal-action-tabs">
                    <button type="button" class="tab-btn tab-active" id="tabAdd"      onclick="setAction('add')">Add Stock</button>
                    <button type="button" class="tab-btn"            id="tabSubtract" onclick="setAction('subtract')">Reduce Stock</button>
                </div>

                <input type="hidden" name="action" id="modalAction" value="add">

                <label class="modal-label" id="amountLabel">Amount to Add:</label>
                <input type="number" name="amount" id="modalAmount" class="modal-input" min="1" placeholder="Enter amount" required>

                <p class="modal-preview" id="modalPreview"></p>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">Confirm</button>
                    <button type="button" class="btn btn-secondary" style="flex:1;" onclick="closeStockModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentQty = 0;

    function openStockModal(id, name, qty) {
        currentQty = qty;
        document.getElementById('modalProductId').value  = id;
        document.getElementById('modalProductName').textContent = name;
        document.getElementById('modalCurrentQty').innerHTML = 'Current Quantity: <strong>' + qty + '</strong>';
        document.getElementById('modalAmount').value = '';
        document.getElementById('modalPreview').textContent = '';
        setAction('add');
        document.getElementById('stockModal').style.display = 'flex';
        document.getElementById('modalAmount').focus();
    }

    function closeStockModal() {
        document.getElementById('stockModal').style.display = 'none';
    }

    function setAction(action) {
        document.getElementById('modalAction').value = action;

        // Toggle tab styles
        document.getElementById('tabAdd').classList.toggle('tab-active', action === 'add');
        document.getElementById('tabSubtract').classList.toggle('tab-active', action === 'subtract');

        // Update label
        document.getElementById('amountLabel').textContent = action === 'add' ? 'Amount to Add:' : 'Amount to Reduce:';

        // Update preview
        updatePreview();
    }

    function updatePreview() {
        const action  = document.getElementById('modalAction').value;
        const amount  = parseInt(document.getElementById('modalAmount').value) || 0;
        const preview = document.getElementById('modalPreview');

        if (amount <= 0) {
            preview.textContent = '';
            return;
        }

        let newQty = action === 'add' ? currentQty + amount : Math.max(0, currentQty - amount);
        preview.textContent = 'New quantity will be: ' + newQty;
        preview.style.color = newQty > 0 ? '#a5d6a7' : '#ef9a9a';
    }

    // Close modal when clicking outside
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) closeStockModal();
    });

    // Live preview update
    document.getElementById('modalAmount').addEventListener('input', updatePreview);
</script>

<?php require __DIR__ . '/partial/footer.php'; ?>
