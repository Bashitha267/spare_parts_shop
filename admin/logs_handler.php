<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);

$action = $_GET['action'] ?? '';

if ($action === 'fetch_logs') {
    try {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $whereClause = " WHERE 1=1 ";
        $params = [];

        if (!empty($search)) {
            $whereClause .= " AND (sl.action LIKE ? OR sl.details LIKE ? OR u.full_name LIKE ?) ";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        if (!empty($from_date)) {
            $whereClause .= " AND DATE(sl.created_at) >= ? ";
            $params[] = $from_date;
        }

        if (!empty($to_date)) {
            $whereClause .= " AND DATE(sl.created_at) <= ? ";
            $params[] = $to_date;
        }

        $type = $_GET['type'] ?? '';
        if ($type === 'inventory') {
            $whereClause .= " AND sl.action IN ('Activate Batch', 'Deactivate Batch', 'Update Registry', 'Delete Item', 'New Item Added', 'New Batch Added')";
        } else {
            $whereClause .= " AND sl.action NOT IN ('Activate Batch', 'Deactivate Batch', 'Update Registry', 'Delete Item', 'New Item Added', 'New Batch Added')";
        }

        // Count total for pagination
        $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM system_logs sl LEFT JOIN users u ON sl.user_id = u.id $whereClause");
        $total_stmt->execute($params);
        $total_items = $total_stmt->fetchColumn();
        $total_pages = ceil($total_items / $limit);

        $stmt = $pdo->prepare("
            SELECT sl.*, u.full_name as user_name, u.role as user_role 
            FROM system_logs sl 
            LEFT JOIN users u ON sl.user_id = u.id 
            $whereClause
            ORDER BY sl.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'logs' => $logs,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_items' => $total_items
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>
