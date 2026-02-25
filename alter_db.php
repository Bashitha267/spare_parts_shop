<?php
require_once __DIR__ . '/includes/config.php';
try {
    // Check if column exists first to be safe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM sales LIKE 'status'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE sales ADD COLUMN status ENUM('pending', 'completed') DEFAULT 'completed'");
        echo "Successfully added 'status' column to 'sales' table.\n";
    } else {
        echo "Column 'status' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
