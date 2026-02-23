<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'search_suggest') {
    $q = trim($_GET['q'] ?? '');
    $type = $_GET['type'] ?? 'all';

    if (strlen($q) < 1) { echo json_encode(['suggestions' => []]); exit; }

    $typeCond = '';
    $params = ["%$q%", "%$q%"];
    if ($type === 'oil') { $typeCond = "AND p.type = 'oil'"; }
    elseif ($type === 'spare_part') { $typeCond = "AND p.type = 'spare_part'"; }

    $stmt = $pdo->prepare("SELECT p.name, p.barcode, p.brand, p.type, p.oil_type
                           FROM products p
                           WHERE (p.name LIKE ? OR p.barcode LIKE ?) $typeCond
                           ORDER BY p.name ASC LIMIT 8");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['suggestions' => $rows]);
    exit;
}

if ($action === 'export_inventory') {
    $type = $_GET['type'] ?? 'oil';
    $filename = ($type === 'oil' ? 'Oil_Inventory_' : 'Spare_Parts_Inventory_') . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header Row
    if ($type === 'oil') {
        fputcsv($output, ['Barcode', 'Designation', 'Brand', 'Oil Type', 'Buying Price', 'Labeled Price', 'Est. Selling Price', 'Stock Level', 'Total Valuation']);
    } else {
        fputcsv($output, ['Part Number', 'Part Name', 'Brand', 'Buying Price', 'Labeled Price', 'Est. Price', 'Stock Level', 'Total Valuation']);
    }
    
    // Fetch All Data (No Pagination)
    $sql = "SELECT p.*, 
            COALESCE(SUM(b.current_qty), 0) as total_stock,
            COALESCE(SUM(b.current_qty * b.buying_price), 0) as total_value,
            (SELECT buying_price FROM batches b2 WHERE b2.product_id = p.id ORDER BY b2.id DESC LIMIT 1) as buying_price,
            (SELECT selling_price FROM batches b2 WHERE b2.product_id = p.id ORDER BY b2.id DESC LIMIT 1) as selling_price,
            (SELECT estimated_selling_price FROM batches b2 WHERE b2.product_id = p.id ORDER BY b2.id DESC LIMIT 1) as estimated_selling_price
            FROM products p 
            LEFT JOIN batches b ON p.id = b.product_id 
            WHERE p.type = ?
            GROUP BY p.id 
            ORDER BY p.name ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$type]);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($type === 'oil') {
            fputcsv($output, [
                $row['barcode'],
                $row['name'],
                $row['brand'],
                ucfirst($row['oil_type']),
                number_format($row['buying_price'], 2, '.', ''),
                number_format($row['selling_price'], 2, '.', ''),
                number_format($row['estimated_selling_price'], 2, '.', ''),
                $row['total_stock'],
                number_format($row['total_value'], 2, '.', '')
            ]);
        } else {
            fputcsv($output, [
                $row['barcode'],
                $row['name'],
                $row['brand'],
                number_format($row['buying_price'], 2, '.', ''),
                number_format($row['selling_price'], 2, '.', ''),
                number_format($row['estimated_selling_price'], 2, '.', ''),
                $row['total_stock'],
                number_format($row['total_value'], 2, '.', '')
            ]);
        }
    }
    
    fclose($output);
    exit;
}

if ($action === 'fetch_inventory') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $low_stock = isset($_GET['low_stock']) && $_GET['low_stock'] === 'active';
    $status_filter = $_GET['status'] ?? 'all';
    $sort = $_GET['sort'] ?? 'high_value';
    
    $whereClause = "WHERE 1=1";
    $params = [];
    
    if ($status_filter === 'active') {
        $whereClause .= " AND b.is_active = 1";
    } else if ($status_filter === 'out_of_stock') {
        $whereClause .= " AND b.is_active = 0";
    }

    if (!empty($type)) {
        $whereClause .= " AND p.type = ?";
        $params[] = $type;
    }

    $oil_type = $_GET['oil_type'] ?? '';
    if (!empty($oil_type)) {
        $whereClause .= " AND p.oil_type = ?";
        $params[] = $oil_type;
    }

    if (!empty($search)) {
        $whereClause .= " AND (p.name LIKE ? OR p.barcode LIKE ? OR p.brand LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $orderBy = "total_value DESC"; // Default
    if ($sort === 'low_value') $orderBy = "total_value ASC";
    else if ($sort === 'name_asc') $orderBy = "p.name ASC";

    $havingClause = $low_stock ? " HAVING total_stock < 5 " : "";

    // If filtering by low stock, we need to wrap the count query
    if ($low_stock) {
        $countSql = "SELECT COUNT(*) FROM batches b INNER JOIN products p ON b.product_id = p.id $whereClause AND b.current_qty < 5";
    } else {
        $countSql = "SELECT COUNT(*) FROM batches b INNER JOIN products p ON b.product_id = p.id $whereClause";
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalBatches = $countStmt->fetchColumn();
    $totalPages = ceil($totalBatches / $limit);

    // Fetch Grand Total Inventory Value (matching filters, ignoring pagination)
    $valSql = "SELECT SUM(b.current_qty * b.buying_price) as grand_total 
               FROM batches b
               INNER JOIN products p ON b.product_id = p.id 
               $whereClause";
    $valStmt = $pdo->prepare($valSql);
    $valStmt->execute($params);
    $grandTotalValue = $valStmt->fetchColumn() ?: 0;

    // Fetch Batches with Product Details
    $orderBy = "b.id DESC"; // Default for batch view
    if ($sort === 'low_value') $orderBy = "(b.current_qty * b.buying_price) ASC";
    else if ($sort === 'high_value') $orderBy = "(b.current_qty * b.buying_price) DESC";
    else if ($sort === 'name_asc') $orderBy = "p.name ASC";

    $sql = "SELECT b.*, p.name, p.barcode as p_barcode, p.type, p.oil_type, p.brand, p.vehicle_compatibility,
            (b.current_qty * b.buying_price) as total_value
            FROM batches b 
            INNER JOIN products p ON b.product_id = p.id
            $whereClause 
            ORDER BY $orderBy 
            LIMIT $limit OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'products' => $batches, // Keep key name for frontend compatibility
        'grand_total_value' => $grandTotalValue,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalBatches
        ]
    ]);
    exit;
}

if ($action === 'toggle_status') {
    $id = $_POST['id'];
    $status = $_POST['status']; // 1 or 0
    
    // Update batch status instead of product status
    $stmt = $pdo->prepare("UPDATE batches SET is_active = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        // Log the action
        $info_stmt = $pdo->prepare("SELECT p.name, b.id FROM batches b JOIN products p ON b.product_id = p.id WHERE b.id = ?");
        $info_stmt->execute([$id]);
        $info = $info_stmt->fetch(PDO::FETCH_ASSOC);
        $action_name = $status == 1 ? "Activate Batch" : "Deactivate Batch";
        log_action($action_name, "Batch #{$info['id']} for {$info['name']} set to " . ($status == 1 ? "Active" : "Inactive"));
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update batch status']);
    }
    exit;
}

if ($action === 'update_product') {
    $id = $_POST['id'];
    $batch_id = $_POST['batch_id'] ?? null;
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $v_types = $_POST['v_types'];
    $qty = $_POST['qty'] ?? null;
    $b_price = $_POST['b_price'] ?? null;
    $s_price = $_POST['s_price'] ?? null;
    $est_price = $_POST['est_price'] ?? null;
    
    $pdo->beginTransaction();
    try {
        // 0. Get Old Data for Logging
        $stmt = $pdo->prepare("SELECT p.*, b.buying_price, b.selling_price, b.estimated_selling_price, b.current_qty 
                              FROM products p 
                              LEFT JOIN batches b ON p.id = b.product_id 
                              WHERE p.id = ? " . ($batch_id ? " AND b.id = ?" : " ORDER BY b.id DESC LIMIT 1"));
        if ($batch_id) {
            $stmt->execute([$id, $batch_id]);
        } else {
            $stmt->execute([$id]);
        }
        $old_data = $stmt->fetch(PDO::FETCH_ASSOC);

        $barcode = $_POST['barcode'] ?? $old_data['barcode'];

        // 1. Update Product Details
        $stmt = $pdo->prepare("UPDATE products SET barcode = ?, name = ?, brand = ?, vehicle_compatibility = ? WHERE id = ?");
        $stmt->execute([$barcode, $name, $brand, $v_types, $id]);

        // 2. Update Batch Details (if prices/qty provided)
        $batch_details_updated = false;
        if ($qty !== null || $b_price !== null || $s_price !== null || $est_price !== null) {
            // Find the batch to update
            if (!$batch_id) {
                // Fallback to latest batch if ID not provided
                $stmt = $pdo->prepare("SELECT id FROM batches WHERE product_id = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$id]);
                $batch_id = $stmt->fetchColumn();
            }

            if ($batch_id) {
                // Update specific batch fields and ensure active if qty > 0
                $status_clause = ((float)$qty > 0) ? ", is_active = 1" : "";
                $stmt = $pdo->prepare("UPDATE batches SET buying_price = ?, selling_price = ?, estimated_selling_price = ?, current_qty = ? $status_clause WHERE id = ?");
                $stmt->execute([$b_price, $s_price, $est_price, $qty, $batch_id]);
                
                // Also ensure product is active if qty > 0
                if ((float)$qty > 0) {
                    $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")->execute([$id]);
                }
                $batch_details_updated = true;
            }
        }

        // 3. Log Action with Details
        $changes = [];
        if ($old_data['barcode'] != $barcode) $changes[] = "Barcode: ~~{$old_data['barcode']}~~ $barcode";
        if ($old_data['name'] != $name) $changes[] = "Name: ~~{$old_data['name']}~~ $name";
        if ($old_data['brand'] != $brand) $changes[] = "Brand: ~~{$old_data['brand']}~~ $brand";
        if ($old_data['vehicle_compatibility'] != $v_types) $changes[] = "Compatibility: ~~{$old_data['vehicle_compatibility']}~~ $v_types";
        
        if ($batch_details_updated) {
            if ($old_data['buying_price'] != $b_price) $changes[] = "Buying Price: ~~" . number_format($old_data['buying_price'], 0) . "~~ " . number_format($b_price, 0);
            if ($old_data['selling_price'] != $s_price) $changes[] = "Selling Price: ~~" . number_format($old_data['selling_price'], 0) . "~~ " . number_format($s_price, 0);
            if ($old_data['estimated_selling_price'] != $est_price) $changes[] = "Estimated Price: ~~" . number_format($old_data['estimated_selling_price'], 0) . "~~ " . number_format($est_price, 0);
            if ($old_data['current_qty'] != $qty) $changes[] = "Quantity: ~~{$old_data['current_qty']}~~ $qty";
        }
        
        if (!empty($changes)) {
            log_action("Update Registry", "$name | " . implode(", ", $changes));
        }
        
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_product') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'No ID provided']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // 1. Check if the product has EVER been sold (Safety)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            // If historical sales exist, we cannot delete without breaking records. 
            // We just deactivate it.
            $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Product has sales history. Registry deactivated instead of deleted.']);
            exit;
        }

        // 2. If no sales, delete associated batches first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM batches WHERE product_id = ?");
        $stmt->execute([$id]);

        // 3. Delete the product
        $stmt = $pdo->prepare("SELECT name, barcode FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p_info = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        log_action("Delete Item", "Permanently deleted product: {$p_info['name']} ({$p_info['barcode']}) and its associated batches.");

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Product and all associated batches deleted successfully.']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'quick_add_stock') {
    $p_id = $_POST['product_id'] ?? null;
    $target_batch_id = $_POST['target_batch_id'] ?? null;
    $barcode = $_POST['barcode'] ?? '';
    
    // Auto-generate barcode if empty (Numeric only)
    if (empty($barcode)) {
        $barcode = date('His') . rand(100, 999);
    }
    
    $pdo->beginTransaction();
    try {
        // 1. If we have a barcode but no p_id, check if barcode exists
        if (empty($p_id) && !empty($barcode)) {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $p_id = $stmt->fetchColumn();
        }

        // 2. Create product if still no p_id (New Product Path)
        $is_new_product = false;
        if (empty($p_id)) {
            $is_new_product = true;
            $stmt = $pdo->prepare("INSERT INTO products (barcode, name, type, oil_type, brand, vehicle_compatibility) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $barcode,
                $_POST['name'],
                $_POST['type'],
                ($_POST['type'] === 'oil' ? ($_POST['oil_type'] ?? 'can') : 'none'),
                $_POST['brand'] ?? 'Generic',
                $_POST['v_types'] ?? 'Universal'
            ]);
            $p_id = $pdo->lastInsertId();
        }

        $new_b_price = $_POST['b_price'];
        $new_qty = $_POST['qty'];
        $new_s_price = $_POST['s_price'];
        $new_est_price = $_POST['est_price'];

        // 3. Check for batch to merge (ALL prices must match)
        // Check target batch first if provided, else check latest
        if ($target_batch_id) {
            $stmt = $pdo->prepare("SELECT id, invoice_id, buying_price, selling_price, estimated_selling_price FROM batches WHERE id = ?");
            $stmt->execute([$target_batch_id]);
        } else {
            $stmt = $pdo->prepare("SELECT id, invoice_id, buying_price, selling_price, estimated_selling_price FROM batches WHERE product_id = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$p_id]);
        }
        $latest_batch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($latest_batch && 
            (float)$latest_batch['buying_price'] == (float)$new_b_price && 
            (float)$latest_batch['selling_price'] == (float)$new_s_price && 
            (float)$latest_batch['estimated_selling_price'] == (float)$new_est_price) {
            // MERGE PATH: Update existing batch
            $batch_id = $latest_batch['id'];
            $invoice_id = $latest_batch['invoice_id'];

            // Update Batch Qty & Prices & Reactivate
            $stmt = $pdo->prepare("UPDATE batches SET current_qty = current_qty + ?, original_qty = original_qty + ?, selling_price = ?, estimated_selling_price = ?, is_active = 1 WHERE id = ?");
            $stmt->execute([$new_qty, $new_qty, $new_s_price, $new_est_price, $batch_id]);

            // Update associated silent invoice total
            $stmt = $pdo->prepare("UPDATE invoices SET total_amount = total_amount + ? WHERE id = ?");
            $stmt->execute([($new_qty * $new_b_price), $invoice_id]);

        } else {
            // NEW BATCH PATH: Create silent invoice and new batch
            $invoice_no = "DIRECT-" . date('Ymd-His') . "-" . rand(100, 999);
            $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, invoice_date, supplier_name, total_amount, status, user_id) VALUES (?, ?, ?, ?, 'completed', ?)");
            $total_buying = $new_b_price * $new_qty;
            $stmt->execute([$invoice_no, date('Y-m-d'), 'Direct Entry', $total_buying, $_SESSION['id']]);
            $invoice_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO batches (product_id, invoice_id, buying_price, selling_price, estimated_selling_price, original_qty, current_qty, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                $p_id,
                $invoice_id,
                $new_b_price,
                $new_s_price,
                $new_est_price,
                $new_qty,
                $new_qty
            ]);
        }

        // 4. Ensure Product is Active
        $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?");
        $stmt->execute([$p_id]);

        // 5. Log Action
        if ($is_new_product) {
            log_action("New Item Added", "Added new item: {$_POST['name']} ({$barcode}) with initial stock $new_qty at $new_b_price each.");
        } else {
            $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
            $stmt->execute([$p_id]);
            $p_name = $stmt->fetchColumn();
            $total_val = $new_qty * $new_b_price;
            log_action("New Batch Added", "Batch added for $p_name: Qty $new_qty, Total Value Rs. " . number_format($total_val, 2));
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'search_suggest') {
    $q = trim($_GET['q'] ?? '');
    $type = $_GET['type'] ?? '';
    if (strlen($q) < 1) { echo json_encode(['suggestions' => []]); exit; }

    $params = ["%$q%", "%$q%", "%$q%"];
    $typeClause = '';
    if (!empty($type)) {
        $typeClause = ' AND p.type = ?';
        $params[] = $type;
    }

    $stmt = $pdo->prepare("
        SELECT DISTINCT p.name, p.barcode, p.brand
        FROM products p
        WHERE (p.name LIKE ? OR p.barcode LIKE ? OR p.brand LIKE ?)
        $typeClause
        AND p.is_active = 1
        ORDER BY p.name ASC
        LIMIT 8
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['suggestions' => $rows]);
    exit;
}

if ($action === 'fetch_batch_details') {
    $invoice_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT b.*, p.name, p.barcode 
                          FROM batches b 
                          JOIN products p ON b.product_id = p.id 
                          WHERE b.invoice_id = ?");
    $stmt->execute([$invoice_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}
