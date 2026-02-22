<?php
require_once 'includes/config.php';
try {
    $pdo->exec("ALTER TABLE batches ADD COLUMN is_active TINYINT DEFAULT 1");
    echo "SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
