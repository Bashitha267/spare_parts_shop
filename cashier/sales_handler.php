<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'search_customer') {
    $query = $_GET['query'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE name LIKE ? OR contact LIKE ? LIMIT 5");
    $stmt->execute(["%$query%", "%$query%"]);
    echo json_encode(['success' => true, 'customers' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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
    $query = $_GET['query'];
    // Fetch products that have at least one batch with stock
    $stmt = $pdo->prepare("SELECT p.* FROM products p 
                           WHERE (p.barcode LIKE ? OR p.name LIKE ?) 
                           AND p.is_active = 1 
                           AND (SELECT SUM(current_qty) FROM batches WHERE product_id = p.id) > 0
                           LIMIT 10");
    $stmt->execute(["%$query%", "%$query%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        $stmt_batch = $pdo->prepare("SELECT * FROM batches WHERE product_id = ? AND current_qty > 0 ORDER BY expire_date ASC");
        $stmt_batch->execute([$product['id']]);
        $product['batches'] = $stmt_batch->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}

if ($action === 'get_batches') {
    $product_id = $_GET['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM batches WHERE product_id = ? AND current_qty > 0 ORDER BY expire_date ASC");
    $stmt->execute([$product_id]);
    echo json_encode(['success' => true, 'batches' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'submit_sale') {
    $customer_id = $_POST['customer_id'] ?: null;
    $user_id = $_SESSION['id'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'] ?: 0;
    $final_amount = $_POST['final_amount'];
    $payment_method = $_POST['payment_method'];
    $items = json_decode($_POST['items'], true);

    try {
        $pdo->beginTransaction();

        $payment_status = ($payment_method === 'cheque' || $payment_method === 'credit') ? 'pending' : 'approved';

        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, user_id, total_amount, discount, final_amount, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customer_id, $user_id, $total_amount, $discount, $final_amount, $payment_method, $payment_status]);
        $sale_id = $pdo->lastInsertId();

        foreach ($items as $item) {
            // Update batch qty
            $stmt = $pdo->prepare("UPDATE batches SET current_qty = current_qty - ? WHERE id = ?");
            $stmt->execute([$item['qty'], $item['batch_id']]);

            // Save sale item
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, batch_id, qty, unit_price, discount, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sale_id, $item['product_id'], $item['batch_id'], $item['qty'], $item['unit_price'], $item['discount'], $item['total_price']]);

            // Auto-deactivate if total stock becomes 0
            $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ? AND (SELECT SUM(current_qty) FROM batches WHERE product_id = ?) <= 0")
                ->execute([$item['product_id'], $item['product_id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'sale_id' => $sale_id]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_today_total') {
    $user_id = $_SESSION['id'];
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE user_id = ? AND DATE(created_at) = ?");
    $stmt->execute([$user_id, $today]);
    $total = $stmt->fetchColumn() ?: 0;
    echo json_encode(['success' => true, 'total' => number_format($total, 2)]);
    exit;
}

if ($action === 'fetch_sale_details') {
    $id = $_GET['id'];
    // Header
    $stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
    $stmt->execute([$id]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);

    // Items
    $stmt = $pdo->prepare("SELECT si.*, p.name, p.brand FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?");
    $stmt->execute([$id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'sale' => $sale, 'items' => $items]);
    exit;
}
