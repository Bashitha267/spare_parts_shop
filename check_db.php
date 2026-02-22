<?php
require_once 'includes/config.php';
try {
    $stmt = $pdo->query("DESCRIBE batches");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
