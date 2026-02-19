<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

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
        $query .= " AND (s.id LIKE ? OR c.name LIKE ?) ";
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

    $stmt = $pdo->prepare("UPDATE sales SET payment_status = ? WHERE id = ?");
    if ($stmt->execute([$status, $sale_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
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
        $query .= " AND (s.id LIKE ? OR c.name LIKE ?) ";
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
