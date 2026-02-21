<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

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
    $date = $_GET['date'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $whereClause = " WHERE 1=1 ";
    $params = [];
    
    if ($search) {
        $whereClause .= " AND (s.id LIKE ? OR c.name LIKE ? OR s.payment_method LIKE ?) ";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($date) {
        $whereClause .= " AND DATE(s.created_at) = ? ";
        $params[] = $date;
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
        log_action("Edit Sale", "Modified TRX-$sale_id. Reason: $reason");

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

            // Re-activate product if stock becomes > 0
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
