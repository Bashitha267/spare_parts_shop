<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if (!$id) {
    header("location: batchHistroy.php");
    exit;
}

if ($action === 'reopen') {
    // Reopen invoice: Set status to draft and put in session
    $stmt = $pdo->prepare("UPDATE invoices SET status = 'draft' WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['active_invoice_id'] = $id;
        header("location: batchHistroy.php?msg=Invoice Reopened as Draft");
        exit;
    }
}

if ($action === 'delete') {
    $pdo->beginTransaction();
    try {
        // 0. Get affected product IDs before deletion
        $p_stmt = $pdo->prepare("SELECT DISTINCT product_id FROM batches WHERE invoice_id = ?");
        $p_stmt->execute([$id]);
        $affected_products = $p_stmt->fetchAll(PDO::FETCH_COLUMN);

        // 1. Delete all batches associated with this invoice
        $stmt = $pdo->prepare("DELETE FROM batches WHERE invoice_id = ?");
        $stmt->execute([$id]);

        // 2. Delete the invoice itself
        $stmt = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);

        // 3. Auto-deactivate products with 0 stock
        foreach($affected_products as $p_id) {
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ? AND (SELECT SUM(current_qty) FROM batches WHERE product_id = ?) <= 0")
                ->execute([$p_id, $p_id]);
        }

        $pdo->commit();
        header("location: batchHistroy.php?msg=Batch Deleted Successfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error deleting batch: " . $e->getMessage());
    }
}
?>
