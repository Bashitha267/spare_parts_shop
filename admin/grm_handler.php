<?php
/**
 * grm_handler.php
 * AJAX backend for the Goods Receipt Management (GRM) module.
 * All responses are JSON. Requires admin authentication.
 */

require_once '../includes/auth.php';
require_once '../includes/config.php';

// Enforce admin/cashier access
check_auth(['admin', 'cashier']);

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

// ---------------------------------------------------------------------------
// ACTION ROUTER
// ---------------------------------------------------------------------------
switch ($action) {
    case 'search_supplier': handle_search_supplier(); break;
    case 'get_supplier':    handle_get_supplier();    break;
    case 'search_product':  handle_search_product();  break;
    case 'fetch_grms':      handle_fetch_grms();      break;
    case 'get_grm_detail':  handle_get_grm_detail();  break;
    case 'save_grm':        handle_save_grm();        break;
    case 'get_stats':       handle_get_stats();       break;
    case 'delete_grm':      handle_delete_grm();      break;
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

// ---------------------------------------------------------------------------
// 1. search_supplier (GET)
//    Param: q (min 1 char)
//    Returns: {suppliers: [{id, name, contact, address}]}
// ---------------------------------------------------------------------------
function handle_search_supplier() {
    global $pdo;

    $q = trim($_GET['q'] ?? '');
    if (strlen($q) < 1) {
        echo json_encode(['suppliers' => []]);
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT id, name, contact, address
         FROM suppliers
         WHERE name LIKE ?
         ORDER BY name ASC
         LIMIT 8"
    );
    $stmt->execute(["%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['suppliers' => $rows]);
}

// ---------------------------------------------------------------------------
// 2. get_supplier (GET)
//    Param: id
//    Returns: {success:true, supplier: {id, name, contact, address, email}}
// ---------------------------------------------------------------------------
function handle_get_supplier() {
    global $pdo;

    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid supplier ID.']);
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT id, name, contact, address, email
         FROM suppliers
         WHERE id = ?
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supplier) {
        echo json_encode(['success' => false, 'message' => 'Supplier not found.']);
        return;
    }

    echo json_encode(['success' => true, 'supplier' => $supplier]);
}

// ---------------------------------------------------------------------------
// 3. search_product (GET)
//    Params: q, type (oil|spare_part)
//    Returns: {suggestions: [{id, name, barcode, type, oil_type, brand, vehicle_compatibility}]}
// ---------------------------------------------------------------------------
function handle_search_product() {
    global $pdo;

    $q    = trim($_GET['q'] ?? '');
    $type = trim($_GET['type'] ?? '');

    if (strlen($q) < 1) {
        echo json_encode(['suggestions' => []]);
        return;
    }

    $params = ["%$q%", "%$q%"];
    $typeCond = '';

    if ($type === 'oil' || $type === 'spare_part') {
        $typeCond = 'AND p.type = ?';
        $params[] = $type;
    }

    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.barcode, p.type, p.oil_type, p.brand, p.vehicle_compatibility
         FROM products p
         WHERE (p.name LIKE ? OR p.barcode LIKE ?)
           $typeCond
           AND p.is_active = 1
         ORDER BY p.name ASC
         LIMIT 8"
    );
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['suggestions' => $rows]);
}

// ---------------------------------------------------------------------------
// 4. fetch_grms (GET)
//    Params: page, search, supplier_id, date_from, date_to, status
//    Returns: {grms, pagination, stats}
// ---------------------------------------------------------------------------
function handle_fetch_grms() {
    global $pdo;

    $page        = max(1, intval($_GET['page'] ?? 1));
    $per_page    = 10;
    $offset      = ($page - 1) * $per_page;

    $search      = trim($_GET['search']      ?? '');
    $supplier_id = intval($_GET['supplier_id'] ?? 0);
    $date_from   = trim($_GET['date_from']   ?? '');
    $date_to     = trim($_GET['date_to']     ?? '');
    $status      = trim($_GET['status']      ?? '');

    // --- Build WHERE clause ---
    $where  = "WHERE i.invoice_no LIKE 'GRM-%'";
    $params = [];

    if ($search !== '') {
        $where   .= " AND (i.invoice_no LIKE ? OR COALESCE(s.name, i.supplier_name) LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($supplier_id > 0) {
        $where   .= " AND i.supplier_id = ?";
        $params[] = $supplier_id;
    }

    if ($date_from !== '') {
        $where   .= " AND DATE(i.invoice_date) >= ?";
        $params[] = $date_from;
    }

    if ($date_to !== '') {
        $where   .= " AND DATE(i.invoice_date) <= ?";
        $params[] = $date_to;
    }

    if ($status !== '') {
        $where   .= " AND i.status = ?";
        $params[] = $status;
    }

    // --- Total count ---
    $count_sql = "SELECT COUNT(*) AS total
                  FROM invoices i
                  LEFT JOIN suppliers s ON i.supplier_id = s.id
                  $where";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_items = (int) $count_stmt->fetchColumn();
    $total_pages = max(1, (int) ceil($total_items / $per_page));

    // --- Data query ---
    $data_params   = array_merge($params, [$per_page, $offset]);
    $data_sql = "SELECT
                   i.id,
                   i.invoice_no,
                   COALESCE(s.name, i.supplier_name)  AS supplier_name,
                   i.invoice_date,
                   i.total_amount,
                   i.discount,
                   i.final_amount,
                   i.status,
                   (SELECT COUNT(*) FROM grm_items gi WHERE gi.invoice_id = i.id) AS item_count
                 FROM invoices i
                 LEFT JOIN suppliers s ON i.supplier_id = s.id
                 $where
                 ORDER BY i.invoice_date DESC, i.id DESC
                 LIMIT ? OFFSET ?";
    $data_stmt = $pdo->prepare($data_sql);

    // LIMIT and OFFSET must be integers; bind them explicitly
    $bind_idx = 1;
    foreach ($params as $p) {
        $data_stmt->bindValue($bind_idx++, $p);
    }
    $data_stmt->bindValue($bind_idx++, $per_page, PDO::PARAM_INT);
    $data_stmt->bindValue($bind_idx++, $offset,   PDO::PARAM_INT);

    $data_stmt->execute();
    $grms = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Stats ---
    $stats_stmt = $pdo->prepare(
        "SELECT
           COUNT(*)          AS total_this_month,
           COALESCE(SUM(final_amount), 0) AS value_this_month
         FROM invoices
         WHERE invoice_no LIKE 'GRM-%'
           AND MONTH(invoice_date) = MONTH(CURDATE())
           AND YEAR(invoice_date)  = YEAR(CURDATE())"
    );
    $stats_stmt->execute();
    $stats_row = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    $sup_stmt = $pdo->query("SELECT COUNT(*) FROM suppliers");
    $total_suppliers = (int) $sup_stmt->fetchColumn();

    echo json_encode([
        'grms'       => $grms,
        'pagination' => [
            'current_page' => $page,
            'total_pages'  => $total_pages,
            'total_items'  => $total_items,
        ],
        'stats'      => [
            'total_this_month' => (int) $stats_row['total_this_month'],
            'value_this_month' => (float) $stats_row['value_this_month'],
            'total_suppliers'  => $total_suppliers,
        ],
    ]);
}

// ---------------------------------------------------------------------------
// 5. get_grm_detail (GET)
//    Param: id (invoice id)
//    Returns: {success, invoice, items}
// ---------------------------------------------------------------------------
function handle_get_grm_detail() {
    global $pdo;

    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid invoice ID.']);
        return;
    }

    // Fetch invoice
    $inv_stmt = $pdo->prepare(
        "SELECT i.*, COALESCE(s.name, i.supplier_name) AS resolved_supplier_name,
                s.contact AS supplier_contact, s.address AS supplier_address
         FROM invoices i
         LEFT JOIN suppliers s ON i.supplier_id = s.id
         WHERE i.id = ?
           AND i.invoice_no LIKE 'GRM-%'
         LIMIT 1"
    );
    $inv_stmt->execute([$id]);
    $invoice = $inv_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        echo json_encode(['success' => false, 'message' => 'GRM invoice not found.']);
        return;
    }

    // Fetch line items
    $items_stmt = $pdo->prepare(
        "SELECT
           gi.id,
           p.name          AS product_name,
           p.barcode,
           p.type,
           p.oil_type,
           gi.qty,
           gi.buying_price,
           gi.selling_price,
           gi.est_selling_price,
           gi.expire_date,
           gi.line_total
         FROM grm_items gi
         JOIN products p ON gi.product_id = p.id
         WHERE gi.invoice_id = ?
         ORDER BY gi.id ASC"
    );
    $items_stmt->execute([$id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'invoice' => $invoice,
        'items'   => $items,
    ]);
}

// ---------------------------------------------------------------------------
// 6. save_grm (POST)
//    Full transaction: supplier upsert → invoice → product upsert →
//    batch merge/create → grm_items → product activation → log
// ---------------------------------------------------------------------------
function handle_save_grm() {
    global $pdo;

    // --- Collect inputs ---
    $supplier_name    = trim($_POST['supplier_name']    ?? '');
    $supplier_contact = trim($_POST['supplier_contact'] ?? '');
    $supplier_address = trim($_POST['supplier_address'] ?? '');
    $supplier_email   = trim($_POST['supplier_email']   ?? '');
    $supplier_id      = intval($_POST['supplier_id']    ?? 0);

    $invoice_date     = trim($_POST['invoice_date'] ?? date('Y-m-d'));
    $invoice_no       = trim($_POST['invoice_no']   ?? '');
    $notes            = trim($_POST['notes']        ?? '');

    $items_json       = trim($_POST['items']        ?? '[]');
    $discount_raw     = floatval($_POST['discount']      ?? 0);
    $discount_type    = trim($_POST['discount_type'] ?? 'rs'); // 'rs' | 'percent'

    // Validate
    if ($supplier_name === '') {
        echo json_encode(['success' => false, 'message' => 'Supplier name is required.']);
        return;
    }
    if ($invoice_no === '') {
        echo json_encode(['success' => false, 'message' => 'Invoice number is required.']);
        return;
    }

    $items = json_decode($items_json, true);
    if (!is_array($items) || count($items) === 0) {
        echo json_encode(['success' => false, 'message' => 'At least one item is required.']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // ----------------------------------------------------------------
        // Step 1 – Supplier upsert
        // ----------------------------------------------------------------
        if ($supplier_id <= 0) {
            // Check if supplier with same name already exists
            $chk = $pdo->prepare("SELECT id FROM suppliers WHERE name = ? LIMIT 1");
            $chk->execute([$supplier_name]);
            $existing_sup = $chk->fetch(PDO::FETCH_ASSOC);

            if ($existing_sup) {
                $supplier_id = (int) $existing_sup['id'];
            } else {
                $ins_sup = $pdo->prepare(
                    "INSERT INTO suppliers (name, contact, address, email)
                     VALUES (?, ?, ?, ?)"
                );
                $ins_sup->execute([
                    $supplier_name,
                    $supplier_contact,
                    $supplier_address,
                    $supplier_email,
                ]);
                $supplier_id = (int) $pdo->lastInsertId();
            }
        }

        // ----------------------------------------------------------------
        // Step 2 – Calculate totals
        // ----------------------------------------------------------------
        $subtotal = 0.0;
        foreach ($items as $item) {
            $qty          = floatval($item['qty']           ?? 0);
            $buying_price = floatval($item['buying_price']  ?? 0);
            $subtotal    += $qty * $buying_price;
        }

        if ($discount_type === 'percent') {
            $discount_rs = $subtotal * ($discount_raw / 100);
        } else {
            $discount_rs = $discount_raw;
        }

        $final_amount = $subtotal - $discount_rs;

        // ----------------------------------------------------------------
        // Step 3 – Insert invoice
        // ----------------------------------------------------------------
        $ins_inv = $pdo->prepare(
            "INSERT INTO invoices
               (invoice_no, user_id, supplier_name, supplier_id, invoice_date,
                total_amount, discount, final_amount, notes, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')"
        );
        $ins_inv->execute([
            $invoice_no,
            $_SESSION['id'],
            $supplier_name,
            $supplier_id > 0 ? $supplier_id : null,
            $invoice_date,
            $subtotal,
            $discount_rs,
            $final_amount,
            $notes,
        ]);
        $invoice_id = (int) $pdo->lastInsertId();

        // ----------------------------------------------------------------
        // Step 4 – Process each item
        // ----------------------------------------------------------------
        foreach ($items as $item) {
            $is_new_product       = intval($item['is_new_product']       ?? 0);
            $product_id           = intval($item['product_id']           ?? 0);
            $item_name            = trim($item['name']                   ?? '');
            $barcode              = trim($item['barcode']                ?? '');
            $product_type         = trim($item['product_type']           ?? 'spare_part');
            $oil_type             = trim($item['oil_type']               ?? 'none');
            $brand                = trim($item['brand']                  ?? '');
            $vehicle_compat       = trim($item['vehicle_compatibility']  ?? '');
            $buying_price         = floatval($item['buying_price']       ?? 0);
            $selling_price        = floatval($item['selling_price']      ?? 0);
            $est_selling_price    = floatval($item['est_selling_price']  ?? 0);
            $qty                  = floatval($item['qty']                ?? 0);
            $expire_date          = trim($item['expire_date']            ?? '') ?: null;

            // 4a – Product upsert
            if ($is_new_product === 1) {
                // oil_type only relevant for oils
                $db_oil_type = ($product_type === 'oil') ? $oil_type : 'none';

                $ins_prod = $pdo->prepare(
                    "INSERT INTO products
                       (barcode, name, type, oil_type, brand, vehicle_compatibility, is_active)
                     VALUES (?, ?, ?, ?, ?, ?, 1)"
                );
                $ins_prod->execute([
                    $barcode,
                    $item_name,
                    $product_type,
                    $db_oil_type,
                    $brand,
                    $vehicle_compat,
                ]);
                $product_id = (int) $pdo->lastInsertId();
            }

            // 4b – Batch merge or new batch
            $batch_id = null;

            $latest_batch = $pdo->prepare(
                "SELECT id, buying_price, selling_price, estimated_selling_price
                 FROM batches
                 WHERE product_id = ?
                 ORDER BY id DESC
                 LIMIT 1"
            );
            $latest_batch->execute([$product_id]);
            $existing_batch = $latest_batch->fetch(PDO::FETCH_ASSOC);

            if (
                $existing_batch &&
                (float) $existing_batch['buying_price']            === $buying_price &&
                (float) $existing_batch['selling_price']           === $selling_price &&
                (float) $existing_batch['estimated_selling_price'] === $est_selling_price
            ) {
                // Prices match → merge into existing batch
                $batch_id = (int) $existing_batch['id'];
                $upd_batch = $pdo->prepare(
                    "UPDATE batches
                     SET current_qty  = current_qty  + ?,
                         original_qty = original_qty + ?,
                         is_active    = 1
                     WHERE id = ?"
                );
                $upd_batch->execute([$qty, $qty, $batch_id]);
            } else {
                // New batch
                $ins_batch = $pdo->prepare(
                    "INSERT INTO batches
                       (product_id, invoice_id, buying_price, selling_price,
                        estimated_selling_price, original_qty, current_qty,
                        expire_date, is_active)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
                );
                $ins_batch->execute([
                    $product_id,
                    $invoice_id,
                    $buying_price,
                    $selling_price,
                    $est_selling_price,
                    $qty,
                    $qty,
                    $expire_date,
                ]);
                $batch_id = (int) $pdo->lastInsertId();
            }

            // 4c – Insert GRM line item
            $line_total = $qty * $buying_price;

            $ins_grm = $pdo->prepare(
                "INSERT INTO grm_items
                   (invoice_id, product_id, batch_id, buying_price, selling_price,
                    est_selling_price, qty, expire_date, line_total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $ins_grm->execute([
                $invoice_id,
                $product_id,
                $batch_id,
                $buying_price,
                $selling_price,
                $est_selling_price,
                $qty,
                $expire_date,
                $line_total,
            ]);

            // 4d – Ensure product is active
            $pdo->prepare("UPDATE products SET is_active = 1 WHERE id = ?")
                ->execute([$product_id]);
        }

        // ----------------------------------------------------------------
        // Step 5 – Commit
        // ----------------------------------------------------------------
        $pdo->commit();

        // ----------------------------------------------------------------
        // Step 6 – Audit log (outside transaction; non-critical)
        // ----------------------------------------------------------------
        log_action(
            'GRM Invoice',
            'GRM: ' . $invoice_no .
            ' | Supplier: ' . $supplier_name .
            ' | Items: ' . count($items) .
            ' | Total: Rs. ' . number_format($final_amount, 2)
        );

        echo json_encode([
            'success'    => true,
            'message'    => 'GRM saved successfully.',
            'invoice_id' => $invoice_id,
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('GRM save error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save GRM: ' . $e->getMessage(),
        ]);
    }
}

// ---------------------------------------------------------------------------
// 7. get_stats (GET)
//    Returns: {total_grms_month, value_month, total_suppliers}
// ---------------------------------------------------------------------------
function handle_get_stats() {
    global $pdo;

    $grm_stmt = $pdo->prepare(
        "SELECT
           COUNT(*)                          AS total_grms_month,
           COALESCE(SUM(final_amount), 0)   AS value_month
         FROM invoices
         WHERE invoice_no LIKE 'GRM-%'
           AND MONTH(invoice_date) = MONTH(CURDATE())
           AND YEAR(invoice_date)  = YEAR(CURDATE())"
    );
    $grm_stmt->execute();
    $grm_row = $grm_stmt->fetch(PDO::FETCH_ASSOC);

    $sup_stmt = $pdo->query("SELECT COUNT(*) FROM suppliers");
    $total_suppliers = (int) $sup_stmt->fetchColumn();

    echo json_encode([
        'total_grms_month' => (int)   $grm_row['total_grms_month'],
        'value_month'      => (float) $grm_row['value_month'],
        'total_suppliers'  => $total_suppliers,
    ]);
}

// ---------------------------------------------------------------------------
// 8. delete_grm (POST)
//    Param: id
//    Returns: {success: true|false, message}
// ---------------------------------------------------------------------------
function handle_delete_grm() {
    global $pdo;

    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid GRM Invoice ID.']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Fetch invoice details first to verify it exists and get invoice number
        $stmt = $pdo->prepare("SELECT invoice_no FROM invoices WHERE id = ? AND invoice_no LIKE 'GRM-%'");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$invoice) {
            echo json_encode(['success' => false, 'message' => 'GRM Invoice not found.']);
            $pdo->rollBack();
            return;
        }

        // Fetch items associated with the invoice to adjust batch quantities
        $items_stmt = $pdo->prepare("SELECT batch_id, qty FROM grm_items WHERE invoice_id = ?");
        $items_stmt->execute([$id]);
        $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            if ($item['batch_id']) {
                // Deduct the quantity added by this invoice from the batch
                // If current_qty drops to 0, mark the batch inactive
                $adjust_stmt = $pdo->prepare("
                    UPDATE batches 
                    SET 
                        current_qty = GREATEST(0, current_qty - :qty),
                        original_qty = GREATEST(0, original_qty - :qty),
                        is_active = CASE WHEN GREATEST(0, current_qty - :qty) <= 0 THEN 0 ELSE is_active END
                    WHERE id = :batch_id
                ");
                $adjust_stmt->execute([
                    ':qty' => $item['qty'],
                    ':batch_id' => $item['batch_id']
                ]);
            }
        }

        // Delete the items
        $del_items = $pdo->prepare("DELETE FROM grm_items WHERE invoice_id = ?");
        $del_items->execute([$id]);

        // Delete the invoice itself
        $del_invoice = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
        $del_invoice->execute([$id]);

        // Commit transaction
        $pdo->commit();

        // Log the deletion action
        log_action('GRM Delete', 'Deleted GRM Invoice: ' . $invoice['invoice_no'] . ' (ID: ' . $id . ')');

        echo json_encode(['success' => true, 'message' => 'GRM Invoice deleted successfully.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
