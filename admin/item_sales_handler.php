<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'fetch_item_sales') {
    $type = $_GET['type'] ?? 'all';
    $date = $_GET['date'] ?? date('Y-m-d'); // default today
    $sort = $_GET['sort'] ?? 'most_sold';
    $search = $_GET['search'] ?? '';

    $whereClauses = [];
    $params = [];

    // Filter by type
    if ($type !== 'all') {
        $whereClauses[] = "p.type = ?";
        $params[] = $type;
    }

    // Filter by specific date
    if (!empty($date)) {
        $whereClauses[] = "DATE(s.created_at) = ?";
        $params[] = $date;
    }

    if (!empty($search)) {
        $whereClauses[] = "(p.name LIKE ? OR p.barcode LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $orderBy = "total_qty DESC";
    if ($sort === 'least_sold') $orderBy = "total_qty ASC";
    elseif ($sort === 'highest_earning') $orderBy = "total_revenue DESC";

    $sql = "SELECT 
                p.id,
                p.name,
                p.barcode,
                p.type,
                p.oil_type,
                p.brand,
                SUM(si.qty) as total_qty,
                SUM(si.qty * si.unit_price) as total_revenue,
                COALESCE((SELECT SUM(b.current_qty) FROM batches b WHERE b.product_id = p.id), 0) as current_stock
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN products p ON si.product_id = p.id
            $whereSql
            GROUP BY p.id
            ORDER BY $orderBy";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $results]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'export_excel') {
    $type = $_GET['type'] ?? 'all';
    $date = $_GET['date'] ?? date('Y-m-d');
    
    // Repeat same logic for export
    $whereClauses = [];
    $params = [];
    if ($type !== 'all') { $whereClauses[] = "p.type = ?"; $params[] = $type; }
    if (!empty($date)) { $whereClauses[] = "DATE(s.created_at) = ?"; $params[] = $date; }
    
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $whereClauses[] = "(p.name LIKE ? OR p.barcode LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $sql = "SELECT 
                p.name, p.barcode, p.type, p.oil_type, p.brand,
                SUM(si.qty) as total_qty,
                SUM(si.qty * si.unit_price) as total_revenue,
                (SELECT SUM(b.current_qty) FROM batches b WHERE b.product_id = p.id) as current_stock
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN products p ON si.product_id = p.id
            $whereSql
            GROUP BY p.id
            ORDER BY total_qty DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Item_Sales_Report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Product Name', 'Barcode', 'Type', 'Category', 'Brand', 'Sold Qty', 'Total Earned', 'Current Stock']);
    
    foreach ($data as $row) {
        fputcsv($output, [
            $row['name'],
            $row['barcode'],
            ucfirst($row['type']),
            $row['oil_type'] !== 'none' ? ucfirst($row['oil_type']) : 'Spare Part',
            $row['brand'],
            $row['total_qty'],
            number_format($row['total_revenue'], 2, '.', ''),
            $row['current_stock'] ?: 0
        ]);
    }
    fclose($output);
    exit;
}
