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
        header("location: addItems.php");
        exit;
    }
}

if ($action === 'delete') {
    $pdo->beginTransaction();
    try {
        // 1. Delete all batches associated with this invoice
        // This effectively removes them from inventory
        $stmt = $pdo->prepare("DELETE FROM batches WHERE invoice_id = ?");
        $stmt->execute([$id]);

        // 2. Delete the invoice itself
        $stmt = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        header("location: batchHistroy.php?msg=Batch Deleted Successfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error deleting batch: " . $e->getMessage());
    }
}
?>
