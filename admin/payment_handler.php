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

    // Match TRX IDs
    $idQuery = preg_replace('/^TRX-/i', '', $q);
    if (is_numeric($idQuery)) {
        $stmt = $pdo->prepare("SELECT s.id FROM sales s WHERE s.payment_method IN ('cheque','credit') AND s.id LIKE ? LIMIT 5");
        $stmt->execute(["%$idQuery%"]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $suggestions[] = ['label' => 'TRX-' . $row['id'], 'value' => $row['id'], 'type' => 'trx'];
        }
    }

    // Match customer names or contact
    $stmt = $pdo->prepare("SELECT DISTINCT c.name, c.contact FROM customers c
                           INNER JOIN sales s ON s.customer_id = c.id
                           WHERE s.payment_method IN ('cheque','credit') AND (c.name LIKE ? OR c.contact LIKE ?) LIMIT 6");
    $stmt->execute(["%$q%", "%$q%"]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $suggestions[] = ['label' => $row['name'], 'sub' => $row['contact'], 'value' => $row['name'], 'type' => 'customer'];
    }

    echo json_encode(['suggestions' => array_slice($suggestions, 0, 8)]);
    exit;
}

if ($action === 'fetch_pending_payments') {
    $method = $_GET['method'] ?? 'all';
    $search = $_GET['search'] ?? '';
    $date = $_GET['date'] ?? '';

    $query = "SELECT s.*, c.name as cust_name, u.full_name as cashier_name 
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              JOIN users u ON s.user_id = u.id 
              WHERE s.payment_status = 'pending' ";
    $params = [];

    if ($method !== 'all') {
        $query .= " AND s.payment_method = ? ";
        $params[] = $method;
    }

    if ($search) {
        $query .= " AND (s.id LIKE ? OR c.name LIKE ? OR c.contact LIKE ?) ";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($date) {
        $query .= " AND DATE(s.created_at) = ? ";
        $params[] = $date;
    }

    $query .= " ORDER BY s.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'sales' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'update_payment_status') {
    $sale_id = $_POST['sale_id'];
    $status = $_POST['status']; // approved or rejected

    // Get sale data for logging
    $stmt = $pdo->prepare("SELECT payment_status, payment_method, final_amount FROM sales WHERE id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch();

    if ($sale) {
        $old_status = $sale['payment_status'];
        $stmt = $pdo->prepare("UPDATE sales SET payment_status = ? WHERE id = ?");
        if ($stmt->execute([$status, $sale_id])) {
            log_action("Payment Update", "TRX-$sale_id | Method: {$sale['payment_method']} | Amount: Rs. " . number_format($sale['final_amount'], 2) . " | Status: ~~{$old_status}~~ " . ucfirst($status));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Sale not found']);
    }
    exit;
}

if ($action === 'fetch_payment_history') {
    $search = $_GET['search'] ?? '';
    $method = $_GET['method'] ?? 'all';
    $status = $_GET['status'] ?? 'all';
    $date = $_GET['date'] ?? '';
    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';

    $query = "SELECT s.*, c.name as cust_name, u.full_name as cashier_name 
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              JOIN users u ON s.user_id = u.id 
              WHERE s.payment_method IN ('cheque', 'credit') ";
    $params = [];

    if ($search) {
        $query .= " AND (s.id LIKE ? OR c.name LIKE ? OR c.contact LIKE ?) ";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($method !== 'all') {
        $query .= " AND s.payment_method = ? ";
        $params[] = $method;
    }

    if ($status !== 'all') {
        $query .= " AND s.payment_status = ? ";
        $params[] = $status;
    }

    if ($date) {
        $query .= " AND DATE(s.created_at) = ? ";
        $params[] = $date;
    } elseif ($month) {
        $query .= " AND DATE_FORMAT(s.created_at, '%Y-%m') = ? ";
        $params[] = $month;
    } elseif ($year) {
        $query .= " AND YEAR(s.created_at) = ? ";
        $params[] = $year;
    }

    $query .= " ORDER BY s.created_at DESC LIMIT 100";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'sales' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}
