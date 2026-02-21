<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

$action = $_GET['action'] ?? '';

if ($action === 'fetch_logs') {
    try {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Count total for pagination
        $total_stmt = $pdo->query("SELECT COUNT(*) FROM system_logs");
        $total_items = $total_stmt->fetchColumn();
        $total_pages = ceil($total_items / $limit);

        $stmt = $pdo->prepare("
            SELECT sl.*, u.full_name as user_name, u.role as user_role 
            FROM system_logs sl 
            LEFT JOIN users u ON sl.user_id = u.id 
            ORDER BY sl.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
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
