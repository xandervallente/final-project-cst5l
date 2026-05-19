<?php
require_once __DIR__ . '/public/database.config.php';

echo "<h2>Raw ENV vars</h2><pre>";
foreach (['MYSQLHOST','MYSQLUSER','MYSQLPASSWORD','MYSQLDATABASE','MYSQLPORT',
          'DB_HOST','DB_USER','DB_PASS','DB_NAME','DB_PORT','PORT'] as $k) {
    $v = getenv($k);
    echo "$k = " . ($v === false ? '(not set)' : ($k === 'MYSQLPASSWORD' || $k === 'DB_PASS' ? '(set)' : htmlspecialchars($v))) . "\n";
}
echo "</pre>";

echo "<h2>Resolved PHP vars</h2><pre>";
echo "SERVER_NAME = $SERVER_NAME\n";
echo "USERNAME    = $USERNAME\n";
echo "PASSWORD    = (hidden)\n";
echo "DB_NAME     = $DB_NAME\n";
echo "PORT        = $PORT\n";
echo "</pre>";

echo "<h2>Connection test</h2>";
try {
    $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $PORT);
    echo "<p style='color:green'>Connected! Tables: ";
    $r = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $r->fetch_row()) $tables[] = $row[0];
    echo implode(', ', $tables) ?: 'none';
    echo "</p>";
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red'>Failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
