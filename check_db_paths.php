<?php
define('BASEPATH', 1);
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';

$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$tables = ['download', 'order_circular', 'forms', 'flash_news', 'quick_link'];

foreach ($tables as $t) {
    $res = $conn->query("SHOW COLUMNS FROM `$t`");
    if (!$res) continue;
    $cols = [];
    while ($r = $res->fetch_assoc()) {
        $cols[] = $r['Field'];
    }
    
    $pathCol = in_array('path', $cols) ? 'path' : (in_array('url', $cols) ? 'url' : null);
    if ($pathCol) {
        $q = $conn->query("SELECT id, `$pathCol` FROM `$t` WHERE `$pathCol` LIKE '%public/downloads%' OR `$pathCol` LIKE 'public/%'");
        if ($q && $q->num_rows > 0) {
            echo "Table $t has " . $q->num_rows . " rows with 'public/' paths:\n";
            while ($row = $q->fetch_assoc()) {
                echo "  ID " . $row['id'] . ": " . $row[$pathCol] . "\n";
            }
        }
    }
}
$conn->close();
