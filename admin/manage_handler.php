<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'fetch_inventory') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $low_stock = isset($_GET['low_stock']) && $_GET['low_stock'] === 'active';

    $whereClause = "WHERE 1=1";
    $params = [];
    
    if (!empty($type)) {
        $whereClause .= " AND p.type = ?";
        $params[] = $type;
    }

    if (!empty($search)) {
        $whereClause .= " AND (p.name LIKE ? OR p.barcode LIKE ? OR p.brand LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $havingClause = $low_stock ? " HAVING total_stock < 5 " : "";

    // If filtering by low stock, we need to wrap the count query
    if ($low_stock) {
        $countSql = "SELECT COUNT(*) FROM (
                        SELECT p.id, COALESCE(SUM(b.current_qty), 0) as total_stock 
                        FROM products p 
                        LEFT JOIN batches b ON p.id = b.product_id 
                        $whereClause 
                        GROUP BY p.id 
                        $havingClause
                     ) as subquery";
    } else {
        $countSql = "SELECT COUNT(*) FROM products p $whereClause";
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);

    // Fetch Products with Stock Levels
    $sql = "SELECT p.*, COALESCE(SUM(b.current_qty), 0) as total_stock 
            FROM products p 
            LEFT JOIN batches b ON p.id = b.product_id 
            $whereClause 
            GROUP BY p.id 
            $havingClause
            ORDER BY p.name ASC 
            LIMIT $limit OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalProducts
        ]
    ]);
    exit;
}

if ($action === 'toggle_status') {
    $id = $_POST['id'];
    $status = $_POST['status']; // 1 or 0
    
    $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    exit;
}

if ($action === 'update_product') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $v_types = $_POST['v_types'];
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, brand = ?, vehicle_compatibility = ? WHERE id = ?");
    if ($stmt->execute([$name, $brand, $v_types, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product']);
    }
    exit;
}
