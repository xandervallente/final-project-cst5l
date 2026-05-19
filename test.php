<?php
require_once __DIR__ . '/public/database.config.php';

$vars = [
    'MYSQLHOST'     => getenv('MYSQLHOST'),
    'MYSQLUSER'     => getenv('MYSQLUSER'),
    'MYSQLPASSWORD' => getenv('MYSQLPASSWORD') ? '(set)' : '(empty)',
    'MYSQLDATABASE' => getenv('MYSQLDATABASE'),
    'MYSQLPORT'     => getenv('MYSQLPORT'),
    'DB_HOST'       => getenv('DB_HOST'),
    'DB_USER'       => getenv('DB_USER'),
    'DB_NAME'       => getenv('DB_NAME'),
    'DB_PORT'       => getenv('DB_PORT'),
];

$resolved = [
    'SERVER_NAME' => $SERVER_NAME,
    'USERNAME'    => $USERNAME,
    'PASSWORD'    => $PASSWORD ? '(set)' : '(empty)',
    'DB_NAME'     => $DB_NAME,
    'PORT'        => $PORT,
];

$conn = @new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $PORT);
$connected = !$conn->connect_error;
$error     = $conn->connect_error;

$tables = [];
if ($connected) {
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>DB Diagnostics</title>
<style>
  body{font-family:monospace;background:#0d0d0d;color:#e0e0e0;padding:30px;max-width:700px;margin:auto}
  h2{color:#64b5f6;margin-top:30px}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  td,th{padding:8px 12px;border:1px solid #333;text-align:left}
  th{background:#1a1a2e;color:#90caf9}
  .ok{color:#81c784} .fail{color:#e57373} .warn{color:#ffb74d}
</style>
</head>
<body>
<h1>Railway DB Diagnostics</h1>

<h2>Raw Environment Variables</h2>
<table><tr><th>Variable</th><th>Value</th></tr>
<?php foreach ($vars as $k => $v): ?>
  <tr><td><?= $k ?></td><td><?= htmlspecialchars($v ?: '(not set)') ?></td></tr>
<?php endforeach ?>
</table>

<h2>Resolved Config (what mysqli will use)</h2>
<table><tr><th>Key</th><th>Value</th></tr>
<?php foreach ($resolved as $k => $v): ?>
  <tr><td><?= $k ?></td><td><?= htmlspecialchars($v) ?></td></tr>
<?php endforeach ?>
</table>

<h2>Connection Test</h2>
<?php if ($connected): ?>
  <p class="ok">Connected successfully to <strong><?= htmlspecialchars($DB_NAME) ?></strong> on <?= htmlspecialchars($SERVER_NAME) ?>:<?= $PORT ?></p>
  <h2>Tables in "<?= htmlspecialchars($DB_NAME) ?>"</h2>
  <?php if (empty($tables)): ?>
    <p class="warn">No tables found — run <a href="/setup.php" style="color:#64b5f6">/setup.php</a> to create them.</p>
  <?php else: ?>
    <ul><?php foreach ($tables as $t): ?><li><?= htmlspecialchars($t) ?></li><?php endforeach ?></ul>
  <?php endif ?>
<?php else: ?>
  <p class="fail">Connection FAILED: <?= htmlspecialchars($error) ?></p>
<?php endif ?>
</body>
</html>
