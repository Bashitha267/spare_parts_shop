<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'suggest') {
    $q = trim($_GET['q'] ?? '');
    if (strlen($q) < 1) { echo json_encode(['suggestions' => []]); exit; }

    $suggestions = [];

    // Match TRX IDs (numeric or prefixed)
    $idQuery = preg_replace('/^TRX-/i', '', $q);
    if (is_numeric($idQuery)) {
        $stmt = $pdo->prepare("SELECT s.id FROM sales s WHERE s.id LIKE ? LIMIT 5");
        $stmt->execute(["%$idQuery%"]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $suggestions[] = ['label' => 'TRX-' . $row['id'], 'value' => $row['id'], 'type' => 'trx'];
        }
    }

    // Match customer names or contact
    $stmt = $pdo->prepare("SELECT DISTINCT c.name, c.contact FROM customers c
                           INNER JOIN sales s ON s.customer_id = c.id
                           WHERE (c.name LIKE ? OR c.contact LIKE ?) LIMIT 6");
    $stmt->execute(["%$q%", "%$q%"]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $suggestions[] = ['label' => $row['name'], 'sub' => $row['contact'], 'value' => $row['name'], 'type' => 'customer'];
    }

    echo json_encode(['suggestions' => array_slice($suggestions, 0, 8)]);
    exit;
}

if ($action === 'fetch_items') {
    $sale_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT si.*, p.name, p.barcode 
                          FROM sale_items si 
                          JOIN products p ON si.product_id = p.id 
                          WHERE si.sale_id = ?");
    $stmt->execute([$sale_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

if ($action === 'fetch') {
    $search = $_GET['search'] ?? '';
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $whereClause = " WHERE 1=1 ";
    $params = [];
    
    if ($search) {
        $whereClause .= " AND (s.id LIKE ? OR c.name LIKE ? OR c.contact LIKE ? OR s.payment_method LIKE ?) ";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($from && $to) {
        $whereClause .= " AND DATE(s.created_at) BETWEEN ? AND ? ";
        $params[] = $from;
        $params[] = $to;
    } elseif ($from) {
        $whereClause .= " AND DATE(s.created_at) >= ? ";
        $params[] = $from;
    } elseif ($to) {
        $whereClause .= " AND DATE(s.created_at) <= ? ";
        $params[] = $to;
    }

    $method = $_GET['method'] ?? 'all';
    if ($method !== 'all') {
        $whereClause .= " AND s.payment_method = ? ";
        $params[] = $method;
    }

    $status = $_GET['status'] ?? 'all';
    if ($status !== 'all') {
        $whereClause .= " AND s.payment_status = ? ";
        $params[] = $status;
    }

    $order_status = $_GET['order_status'] ?? 'all';
    if ($order_status !== 'all') {
        $whereClause .= " AND s.status = ? ";
        $params[] = $order_status;
    }

    // Count total items
    $countSql = "SELECT COUNT(*) FROM sales s 
                 LEFT JOIN customers c ON s.customer_id = c.id 
                 $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $limit);
    
    $query = "SELECT s.*, c.name as cust_name, c.contact as cust_contact, u.full_name as officer_name
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              LEFT JOIN users u ON s.user_id = u.id
              $whereClause
              ORDER BY s.created_at DESC 
              LIMIT $limit OFFSET $offset";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true, 
        'sales' => $sales,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems
        ]
    ]);
    exit;
}

if ($action === 'fetch_summaries') {
    $from = $_GET['from'] ?? date('Y-m-d');
    $to = $_GET['to'] ?? date('Y-m-d');
    $date_today = date('Y-m-d');
    
    // --- Range Summaries (for cards) ---
    $summaries = [
        'cash' => 0, 'card' => 0,
        'approved_cheque' => 0,
        'total_credit' => 0,
        'pending_credit' => 0, 'pending_cheque' => 0
    ];

    $query = "SELECT payment_method, payment_status, SUM(final_amount) as total 
              FROM sales 
              WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
              GROUP BY payment_method, payment_status";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$from, $to]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $method = $row['payment_method'];
        $status = $row['payment_status'];
        $total = (float)$row['total'];

        if ($method === 'cash') $summaries['cash'] += $total;
        elseif ($method === 'card') $summaries['card'] += $total;
        elseif ($method === 'credit') {
            $summaries['total_credit'] += $total;
            if ($status !== 'approved') $summaries['pending_credit'] += $total;
        }
        elseif ($method === 'cheque') {
            if ($status === 'approved') $summaries['approved_cheque'] += $total;
            else $summaries['pending_cheque'] += $total;
        }
    }

    // --- Strictly Today's Stats (for navbar) ---
    $stmt_tot = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = ? AND status = 'completed'");
    $stmt_tot->execute([$date_today]);
    $grand_total = $stmt_tot->fetchColumn() ?: 0;

    $stmt_app = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = ? AND payment_status = 'approved' AND status = 'completed'");
    $stmt_app->execute([$date_today]);
    $total_approved = $stmt_app->fetchColumn() ?: 0;

    $stmt_pend = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = ? AND payment_status = 'pending' AND status = 'completed'");
    $stmt_pend->execute([$date_today]);
    $total_pending = $stmt_pend->fetchColumn() ?: 0;

    echo json_encode([
        'success' => true, 
        'summaries' => $summaries,
        'today' => [
            'total' => (float)$grand_total,
            'approved' => (float)$total_approved,
            'pending' => (float)$total_pending
        ]
    ]);
    exit;
}

if ($action === 'return_item') {
    $sale_item_id = $_POST['sale_item_id'] ?? null;
    $qty = floatval($_POST['qty'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $admin_id = $_SESSION['id'];

    if (!$sale_item_id || $qty <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Fetch sale item detail
        $stmt = $pdo->prepare("SELECT si.*, s.final_amount, s.payment_status FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE si.id = ?");
        $stmt->execute([$sale_item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            throw new Exception("Sale item not found.");
        }

        $remaining_qty = $item['qty'] - $item['returned_qty'];
        if ($qty > $remaining_qty) {
            throw new Exception("Return quantity ($qty) exceeds remaining quantity ($remaining_qty).");
        }

        // Calculate refund amount for this returned quantity
        // unit price after discount is total_price / qty
        $unit_price_after_discount = floatval($item['total_price']) / floatval($item['qty']);
        $refund_amount = $qty * $unit_price_after_discount;

        // Calculate original unit price and discount proportion
        $orig_unit_price = floatval($item['unit_price']);
        $disc_per_unit = floatval($item['discount']) / floatval($item['qty']);
        $returned_total_amount = $qty * $orig_unit_price;
        $returned_discount_amount = $qty * $disc_per_unit;

        // 2. Insert into returns table
        $stmt = $pdo->prepare("INSERT INTO `returns` (sale_id, sale_item_id, product_id, batch_id, qty, refund_amount, reason) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$item['sale_id'], $sale_item_id, $item['product_id'], $item['batch_id'], $qty, $refund_amount, $reason]);
        $return_id = $pdo->lastInsertId();

        // 3. Update sale_items: set returned_qty
        $stmt = $pdo->prepare("UPDATE sale_items SET returned_qty = returned_qty + ? WHERE id = ?");
        $stmt->execute([$qty, $sale_item_id]);

        // 4. Update sales: adjust total_amount, discount, final_amount
        $stmt = $pdo->prepare("UPDATE sales SET total_amount = total_amount - ?, discount = discount - ?, final_amount = final_amount - ? WHERE id = ?");
        $stmt->execute([$returned_total_amount, $returned_discount_amount, $refund_amount, $item['sale_id']]);

        // 5. Update batches: add returned quantity back to stock
        $stmt = $pdo->prepare("UPDATE batches SET current_qty = current_qty + ?, is_active = 1 WHERE id = ?");
        $stmt->execute([$qty, $item['batch_id']]);

        // 6. Ensure product is active
        $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?");
        $stmt->execute([$item['product_id']]);

        // 7. Audit log & System log
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, table_name, record_id, reason, old_data, new_data) 
                                VALUES (?, 'edit', 'returns', ?, ?, ?, ?)");
        $stmt->execute([
            $admin_id, 
            $return_id, 
            "Item Return: " . $reason, 
            json_encode($item), 
            json_encode(['returned_qty_added' => $qty, 'refund_amount' => $refund_amount])
        ]);

        log_action("Item Return", "Returned $qty of item ID {$item['product_id']} from Sale TRX-{$item['sale_id']}. Refund: Rs. " . number_format($refund_amount, 2));

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Return processed successfully.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'edit') {
    $sale_id = $_POST['sale_id'];
    $amount = $_POST['total_amount'];
    $method = $_POST['payment_method'];
    $status = $_POST['payment_status'];
    $reason = $_POST['reason'];
    $admin_id = $_SESSION['id']; // Fixed session key

    if (empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Reason is required']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Get Old Data
        $old_stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
        $old_stmt->execute([$sale_id]);
        $old_data = $old_stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Update Sale
        $update_stmt = $pdo->prepare("UPDATE sales SET final_amount = ?, payment_method = ?, payment_status = ? WHERE id = ?");
        $update_stmt->execute([$amount, $method, $status, $sale_id]);

        // 3. Log into Audit
        $audit_stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, table_name, record_id, reason, old_data, new_data) 
                                    VALUES (?, 'edit', 'sales', ?, ?, ?, ?)");
        $audit_stmt->execute([
            $admin_id, 
            $sale_id, 
            $reason, 
            json_encode($old_data), 
            json_encode(['final_amount' => $amount, 'payment_method' => $method, 'payment_status' => $status])
        ]);

        // 4. Also add to System Logs for dashboard visibility
        // 4. Also add to System Logs for dashboard visibility
        $changes = [];
        if($old_data['final_amount'] != $amount) $changes[] = "Amount: ~~" . number_format($old_data['final_amount'], 0) . "~~ " . number_format($amount, 0);
        if($old_data['payment_method'] != $method) $changes[] = "Method: ~~{$old_data['payment_method']}~~ $method";
        if($old_data['payment_status'] != $status) $changes[] = "Status: ~~{$old_data['payment_status']}~~ $status";
        log_action("Edit Sale", "TRX-$sale_id | " . implode(", ", $changes) . " | $reason");

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    $sale_id = $_POST['sale_id'];
    $reason = $_POST['reason'];
    $admin_id = $_SESSION['id']; // Fixed session key

    try {
        $pdo->beginTransaction();

        // 1. Get Old Data
        $old_stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
        $old_stmt->execute([$sale_id]);
        $old_data = $old_stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Reverse Inventory
        $items_stmt = $pdo->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
        $items_stmt->execute([$sale_id]);
        $items = $items_stmt->fetchAll();

        foreach ($items as $item) {
            $update_inventory = $pdo->prepare("UPDATE batches SET current_qty = current_qty + ? WHERE id = ?");
            $update_inventory->execute([$item['qty'], $item['batch_id']]);

            // Re-activate batch and product if stock becomes > 0
            $pdo->prepare("UPDATE batches SET is_active = 1 WHERE id = ?")->execute([$item['batch_id']]);
            $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")->execute([$item['product_id']]);
        }

        // 3. Delete from tables
        $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?")->execute([$sale_id]);
        $pdo->prepare("DELETE FROM sales WHERE id = ?")->execute([$sale_id]);

        // 4. Log into Audit
        $audit_stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, table_name, record_id, reason, old_data, new_data) 
                                    VALUES (?, 'delete', 'sales', ?, ?, ?, NULL)");
        $audit_stmt->execute([$admin_id, $sale_id, $reason, json_encode($old_data)]);

        // 5. Also add to System Logs
        log_action("Purge Sale", "Deleted TRX-$sale_id. Reason: $reason");

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
