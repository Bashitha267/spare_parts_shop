<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'search_product_names') {
    $query = $_GET['query'] ?? '';
    $stmt = $pdo->prepare("SELECT id, name, barcode, type, oil_type FROM products
                           WHERE (name LIKE ? OR barcode LIKE ?) AND is_active = 1
                           AND (SELECT COUNT(*) FROM batches WHERE product_id = products.id AND current_qty > 0 AND is_active = 1) > 0
                           ORDER BY name ASC LIMIT 12");
    $stmt->execute(["%$query%", "%$query%"]);
    echo json_encode(['names' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'search_customer') {
    $query  = $_GET['query'] ?? '';
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 6;
    $offset = ($page - 1) * $limit;
    $fetch  = $limit + 1;
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE name LIKE ? OR contact LIKE ? LIMIT $fetch OFFSET $offset");
    $stmt->execute(["%$query%", "%$query%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $has_more = count($rows) > $limit;
    if ($has_more) array_pop($rows);
    echo json_encode(['success' => true, 'customers' => $rows, 'has_more' => $has_more, 'page' => $page]);
    exit;
}

if ($action === 'add_customer') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, contact, address) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $contact, $address])) {
            echo json_encode(['success' => true, 'customer_id' => $pdo->lastInsertId(), 'name' => $name]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Contact already exists or DB Error']);
    }
    exit;
}

if ($action === 'search_product') {
    $query  = $_GET['query'] ?? '';
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 8;
    $offset = ($page - 1) * $limit;
    $fetch  = $limit + 1;

    $stmt = $pdo->prepare("SELECT p.* FROM products p 
                           WHERE (p.barcode LIKE ? OR p.name LIKE ?) 
                           AND p.is_active = 1 
                           AND (SELECT COUNT(*) FROM batches WHERE product_id = p.id AND current_qty > 0 AND is_active = 1) > 0
                           ORDER BY p.name ASC
                           LIMIT $fetch OFFSET $offset");
    $stmt->execute(["%$query%", "%$query%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $has_more = count($products) > $limit;
    if ($has_more) array_pop($products);

    foreach ($products as &$product) {
        $stmt_batch = $pdo->prepare("SELECT * FROM batches WHERE product_id = ? AND current_qty > 0 AND is_active = 1 ORDER BY id ASC");
        $stmt_batch->execute([$product['id']]);
        $product['batches'] = $stmt_batch->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'products' => $products, 'has_more' => $has_more, 'page' => $page]);
    exit;
}

if ($action === 'get_batches') {
    $product_id = $_GET['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM batches WHERE product_id = ? AND current_qty > 0 AND is_active = 1 ORDER BY id ASC");
    $stmt->execute([$product_id]);
    echo json_encode(['success' => true, 'batches' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'submit_sale' || $action === 'save_draft') {
    $sale_id = $_POST['draft_id'] ?? null;
    $customer_id = $_POST['customer_id'] ?: null;
    $user_id = $_SESSION['id'];
    $total_amount = $_POST['total_amount'] ?? 0;
    $discount = $_POST['discount'] ?? 0;
    $final_amount = $_POST['final_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $items = json_decode($_POST['items'], true) ?? [];
    
    $is_draft = ($action === 'save_draft');
    $status = $is_draft ? 'pending' : 'completed';
    $payment_status = ($payment_method === 'cheque' || $payment_method === 'credit') ? 'pending' : 'approved';

    try {
        $pdo->beginTransaction();

        if ($sale_id) {
            // Restore old quantities before deleting old items
            $stmt = $pdo->prepare("SELECT batch_id, qty FROM sale_items WHERE sale_id = ?");
            $stmt->execute([$sale_id]);
            $old_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($old_items as $old) {
                $pdo->prepare("UPDATE batches SET current_qty = current_qty + ?, is_active = 1 WHERE id = ?")->execute([$old['qty'], $old['batch_id']]);
                // Ensure product is active
                $pdo->prepare("UPDATE products p JOIN batches b ON p.id = b.product_id SET p.is_active = 1 WHERE b.id = ?")->execute([$old['batch_id']]);
            }
            // Delete old items
            $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?")->execute([$sale_id]);
            
            // Update Header
            $stmt = $pdo->prepare("UPDATE sales SET customer_id=?, user_id=?, total_amount=?, discount=?, final_amount=?, payment_method=?, payment_status=?, status=? WHERE id=?");
            $stmt->execute([$customer_id, $user_id, $total_amount, $discount, $final_amount, $payment_method, $payment_status, $status, $sale_id]);
        } else {
            // Insert new Header
            $stmt = $pdo->prepare("INSERT INTO sales (customer_id, user_id, total_amount, discount, final_amount, payment_method, payment_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_id, $user_id, $total_amount, $discount, $final_amount, $payment_method, $payment_status, $status]);
            $sale_id = $pdo->lastInsertId();
        }

        foreach ($items as $item) {
            // Update batch qty
            $stmt = $pdo->prepare("UPDATE batches SET current_qty = current_qty - ? WHERE id = ?");
            $stmt->execute([$item['qty'], $item['batch_id']]);

            // Deactivate batch if qty becomes 0
            $pdo->prepare("UPDATE batches SET is_active = 0 WHERE id = ? AND current_qty <= 0")
                ->execute([$item['batch_id']]);

            // Save sale item (handle 'per_item_discount' or 'discount' interchangeably)
            $itemDisc = $item['per_item_discount'] ?? ($item['discount'] ?? 0);
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, batch_id, qty, unit_price, discount, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sale_id, $item['product_id'], $item['batch_id'], $item['qty'], $item['unit_price'], $itemDisc, $item['total_price']]);

            // Auto-deactivate product if total stock across all active batches becomes 0
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ? AND (SELECT SUM(current_qty) FROM batches WHERE product_id = ? AND is_active = 1) <= 0")
                ->execute([$item['product_id'], $item['product_id']]);
        }

        if (!$is_draft) {
             log_action("New Sale", "Recorded TRX-$sale_id. Total: Rs. " . number_format($final_amount, 2));
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'sale_id' => $sale_id]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'load_drafts') {
    $user_id = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT s.*, c.name as cust_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s.status = 'pending' AND s.user_id = ? ORDER BY s.created_at DESC");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'drafts' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'discard_draft') {
    $sale_id = $_POST['draft_id'];
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT batch_id, qty FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $old_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($old_items as $old) {
            $pdo->prepare("UPDATE batches SET current_qty = current_qty + ?, is_active = 1 WHERE id = ?")->execute([$old['qty'], $old['batch_id']]);
            $pdo->prepare("UPDATE products p JOIN batches b ON p.id = b.product_id SET p.is_active = 1 WHERE b.id = ?")->execute([$old['batch_id']]);
        }
        $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?")->execute([$sale_id]);
        $pdo->prepare("DELETE FROM sales WHERE id = ?")->execute([$sale_id]);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_today_total') {
    $user_id = $_SESSION['id'];
    $date = $_GET['date'] ?? date('Y-m-d');
    
    $summaries = [
        'cash' => 0,
        'card' => 0,
        'approved_credit' => 0,
        'approved_cheque' => 0,
        'pending_credit' => 0,
        'pending_cheque' => 0
    ];

    $query = "SELECT payment_method, payment_status, SUM(final_amount) as total 
              FROM sales 
              WHERE user_id = ? AND DATE(created_at) = ? AND status = 'completed'
              GROUP BY payment_method, payment_status";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $date]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $method = $row['payment_method'];
        $status = $row['payment_status'];
        $total = (float)$row['total'];

        if ($method === 'cash') $summaries['cash'] += $total;
        elseif ($method === 'card') $summaries['card'] += $total;
        elseif ($method === 'credit') {
            if ($status === 'approved') $summaries['approved_credit'] += $total;
            elseif ($status === 'pending') $summaries['pending_credit'] += $total;
        }
        elseif ($method === 'cheque') {
            if ($status === 'approved') $summaries['approved_cheque'] += $total;
            elseif ($status === 'pending') $summaries['pending_cheque'] += $total;
        }
    }

    $stmt_tot = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE user_id = ? AND DATE(created_at) = ? AND status = 'completed'");
    $stmt_tot->execute([$user_id, $date]);
    $grand_total = $stmt_tot->fetchColumn() ?: 0;

    $stmt_app = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE user_id = ? AND DATE(created_at) = ? AND payment_status = 'approved' AND status = 'completed'");
    $stmt_app->execute([$user_id, $date]);
    $total_approved = $stmt_app->fetchColumn() ?: 0;

    $stmt_pend = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE user_id = ? AND DATE(created_at) = ? AND payment_status = 'pending' AND status = 'completed'");
    $stmt_pend->execute([$user_id, $date]);
    $total_pending = $stmt_pend->fetchColumn() ?: 0;

    echo json_encode([
        'success' => true, 
        'summaries' => $summaries,
        'total' => number_format($grand_total, 2),
        'approved' => number_format($total_approved, 2),
        'pending' => number_format($total_pending, 2)
    ]);
    exit;
}

if ($action === 'fetch_sale_details') {
    $id = $_GET['id'];
    // Header
    $stmt = $pdo->prepare("SELECT s.*, c.id as c_id, c.name as cust_name, c.contact as cust_contact, c.address as cust_address FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s.id = ?");
    $stmt->execute([$id]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);

    // Items
    $stmt = $pdo->prepare("SELECT si.*, p.name, p.brand, p.type, p.oil_type, b.buying_price, b.selling_price as labeled_price, b.estimated_selling_price as est_selling_price, b.current_qty as current_stock_qty FROM sale_items si JOIN products p ON si.product_id = p.id JOIN batches b ON si.batch_id = b.id WHERE si.sale_id = ?");
    $stmt->execute([$id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'sale' => $sale, 'items' => $items]);
    exit;
}

if ($action === 'fetch_sales') {
    $user_id = $_SESSION['id'];
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $search = $_GET['search'] ?? '';
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $method = $_GET['method'] ?? '';

    $where = ["s.user_id = ?", "s.status = 'completed'"];
    $params = [$user_id];

    if (!empty($search)) {
        $where[] = "(c.name LIKE ? OR c.contact LIKE ? OR s.id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if (!empty($from)) {
        $where[] = "DATE(s.created_at) >= ?";
        $params[] = $from;
    }
    if (!empty($to)) {
        $where[] = "DATE(s.created_at) <= ?";
        $params[] = $to;
    }
    if (!empty($method)) {
        $where[] = "s.payment_method = ?";
        $params[] = $method;
    }

    $where_sql = implode(" AND ", $where);

    // Count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE $where_sql");
    $count_stmt->execute($params);
    $total = $count_stmt->fetchColumn();

    // Data
    $stmt = $pdo->prepare("SELECT s.*, c.name as cust_name, c.contact as cust_phone 
                           FROM sales s 
                           LEFT JOIN customers c ON s.customer_id = c.id 
                           WHERE $where_sql
                           ORDER BY s.created_at DESC 
                           LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'sales' => $sales,
        'pagination' => [
            'total_records' => $total,
            'total_pages' => ceil($total / $limit),
            'current_page' => $page
        ]
    ]);
    exit;
}
