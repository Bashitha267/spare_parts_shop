<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'start_invoice') {
    $invoice_no = $_POST['invoice_no'];
    $invoice_date = $_POST['invoice_date'];
    $supplier = $_POST['supplier_name'];

    try {
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, invoice_date, supplier_name, status, user_id) VALUES (?, ?, ?, 'draft', ?)");
        if ($stmt->execute([$invoice_no, $invoice_date, $supplier, $_SESSION['id']])) {
            $_SESSION['active_invoice_id'] = $pdo->lastInsertId();
            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Duplicate Invoice Number or DB Error']);
    }
    exit;
}

if (!isset($_SESSION['active_invoice_id'])) {
    echo json_encode(['success' => false, 'message' => 'No active invoice']);
    exit;
}

if ($action === 'search') {
    $query = $_GET['query'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE (barcode LIKE ? OR name LIKE ? OR brand LIKE ?) LIMIT 10");
    $stmt->execute(["%$query%", "%$query%", "%$query%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}

if ($action === 'add_to_batch') {
    $p_id = $_POST['product_id'];
    $invoice_id = $_SESSION['active_invoice_id'];

    $pdo->beginTransaction();
    try {
        // 1. Create product if new
        if (empty($p_id)) {
            $stmt = $pdo->prepare("INSERT INTO products (barcode, name, type, oil_type, brand, vehicle_compatibility) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['p_barcode'],
                $_POST['p_name'],
                $_POST['p_type'],
                ($_POST['p_type'] === 'oil' ? $_POST['p_oil_type'] : 'none'),
                $_POST['brand'] ?? '',
                $_POST['v_types'] ?? ''
            ]);
            $p_id = $pdo->lastInsertId();
        }

        // 2. Add batch
        $stmt = $pdo->prepare("INSERT INTO batches (product_id, invoice_id, buying_price, selling_price, original_qty, current_qty, expire_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $p_id,
            $invoice_id,
            $_POST['b_price'],
            $_POST['s_price'],
            $_POST['qty'],
            $_POST['qty'],
            !empty($_POST['exp_date']) ? $_POST['exp_date'] : null
        ]);

        // 3. Update Invoice Total
        $total_item_val = $_POST['b_price'] * $_POST['qty'];
        $pdo->prepare("UPDATE invoices SET total_amount = total_amount + ? WHERE id = ?")->execute([$total_item_val, $invoice_id]);
        
        // 4. Automatically Activate Product if Qty > 0
        if ($_POST['qty'] > 0) {
            $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")->execute([$p_id]);
        }
        
        $pdo->commit();
        
        $new_total = $pdo->query("SELECT total_amount FROM invoices WHERE id = $invoice_id")->fetchColumn();
        echo json_encode(['success' => true, 'new_total' => number_format($new_total, 2)]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'load_items') {
    $invoice_id = $_SESSION['active_invoice_id'];
    $stmt = $pdo->prepare("SELECT b.*, p.name, p.barcode, p.type, p.oil_type FROM batches b JOIN products p ON b.product_id = p.id WHERE b.invoice_id = ? ORDER BY b.id DESC");
    $stmt->execute([$invoice_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['items' => $items]);
    exit;
}

if ($action === 'remove_item') {
    $batch_id = $_GET['id'];
    $invoice_id = $_SESSION['active_invoice_id'];
    
    $batch = $pdo->query("SELECT * FROM batches WHERE id = $batch_id")->fetch();
    $p_id = $batch['product_id'];
    $dec_amount = $batch['buying_price'] * $batch['original_qty'];
    
    $pdo->prepare("DELETE FROM batches WHERE id = ?")->execute([$batch_id]);
    $pdo->prepare("UPDATE invoices SET total_amount = total_amount - ? WHERE id = ?")->execute([$dec_amount, $invoice_id]);
    
    // Auto-deactivate if total stock becomes 0
    $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ? AND (SELECT SUM(current_qty) FROM batches WHERE product_id = ?) <= 0")
        ->execute([$p_id, $p_id]);
    
    $new_total = $pdo->query("SELECT total_amount FROM invoices WHERE id = $invoice_id")->fetchColumn();
    echo json_encode(['success' => true, 'new_total' => number_format($new_total, 2)]);
    exit;
}

if ($action === 'complete_invoice') {
    $invoice_id = $_SESSION['active_invoice_id'];
    $pdo->prepare("UPDATE invoices SET status = 'completed' WHERE id = ?")->execute([$invoice_id]);
    unset($_SESSION['active_invoice_id']);
    echo json_encode(['success' => true]);
    exit;
}
