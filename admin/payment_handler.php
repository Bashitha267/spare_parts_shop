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
