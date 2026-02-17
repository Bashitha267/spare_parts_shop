<?php
ob_start();
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');

// 0. Handle CSV Export FIRST (Before any HTML output)
if(isset($_GET['export']) && $_GET['export'] === 'csv') {
    $month = $_GET['month'];
    $filename = "sales_report_$month.csv";
    
    // Clear buffer to ensure no HTML leaks into CSV
    ob_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Sale ID', 'Customer', 'Date/Time', 'Method', 'Total', 'Status']);
    
    $stmt = $pdo->prepare("SELECT s.*, c.name as cust_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE DATE_FORMAT(s.created_at, '%Y-%m') = ? ORDER BY s.created_at ASC");
    $stmt->execute([$month]);
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'], 
            $row['cust_name'] ?: 'Guest', 
            $row['created_at'], 
            strtoupper($row['payment_method']), 
            $row['final_amount'],
            strtoupper($row['payment_status'])
        ]);
    }
    fclose($output);
    exit;
}

// 1. Initializations
$type = $_GET['type'] ?? 'sales';
$time_filter = $_GET['time'] ?? 'daily';
$filter_date = $_GET['date'] ?? date('Y-m-d');
$filter_month = $_GET['month'] ?? date('Y-m');
$year = $_GET['year'] ?? date('Y');
$filter_method = $_GET['method'] ?? 'all';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// 2. Summary Calculations
$today = date('Y-m-d');

// Today's Approved Sales
$today_sales_stmt = $pdo->prepare("SELECT SUM(final_amount) as total, COUNT(*) as count FROM sales WHERE DATE(created_at) = ? AND payment_status = 'approved'");
$today_sales_stmt->execute([$today]);
$today_sales_raw = $today_sales_stmt->fetch();
$today_sales_approved = $today_sales_raw['total'] ?: 0;
$today_sales_count = $today_sales_raw['count'] ?: 0;

// Today's Pending Cheques
$today_cheque_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales WHERE DATE(created_at) = ? AND payment_status = 'pending' AND payment_method = 'cheque'");
$today_cheque_stmt->execute([$today]);
$today_pending_cheque = $today_cheque_stmt->fetchColumn() ?: 0;

// Today's Pending Credits
$today_credit_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales WHERE DATE(created_at) = ? AND payment_status = 'pending' AND payment_method = 'credit'");
$today_credit_stmt->execute([$today]);
$today_pending_credit = $today_credit_stmt->fetchColumn() ?: 0;

// Total Sales (All Verified)
$total_sales_raw = $pdo->query("SELECT SUM(final_amount) as total FROM sales WHERE payment_status = 'approved'")->fetch();
$total_sales = $total_sales_raw['total'] ?: 0;

// Expenses (All completed GRNs)
$total_expenses_stmt = $pdo->query("SELECT SUM(total_amount) FROM invoices WHERE status = 'completed'");
$total_expenses = $total_expenses_stmt->fetchColumn() ?: 0;

// Profit (Approved - Expenses)
$total_profit = $total_sales - $total_expenses;

// Monthly Sales for Download List
$monthly_sales_stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(final_amount) as total 
                                   FROM sales WHERE payment_status IN ('approved', 'pending')
                                   GROUP BY month ORDER BY month DESC");
$monthly_summary = $monthly_sales_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="px-6 py-3 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-slate-100 rounded-lg transition-colors text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Reports Central</h1>
            </div>
            <div></div>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto space-y-8 animate-fade-in">
        
        <!-- Summary Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Today's Approved Sales -->
            <div class="bg-blue-600 p-5 rounded-2xl shadow-lg shadow-blue-100 relative overflow-hidden flex flex-col justify-between min-h-[140px]">
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-blue-100 uppercase tracking-widest mb-1 opacity-80">Today's Sales</p>
                    <h2 class="text-xl font-black text-white leading-tight">Rs. <?php echo number_format($today_sales_approved, 2); ?></h2>
                </div>
                <div class="relative z-10 flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-white/10 rounded-lg text-[9px] font-bold text-white uppercase"><?php echo $today_sales_count; ?> Approved</span>
                </div>
                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            <!-- Pending Cheques Today -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[140px]">
                <div>
                    <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">Pending Cheques</p>
                    <h2 class="text-xl font-black text-slate-800 leading-tight">Rs. <?php echo number_format($today_pending_cheque, 2); ?></h2>
                </div>
                <p class="text-[9px] font-bold text-slate-400 uppercase">Today's Pool</p>
            </div>

            <!-- Pending Credit Today -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[140px]">
                <div>
                    <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-1">Pending Credits</p>
                    <h2 class="text-xl font-black text-slate-800 leading-tight">Rs. <?php echo number_format($today_pending_credit, 2); ?></h2>
                </div>
                <p class="text-[9px] font-bold text-slate-400 uppercase">Awaiting Settlement</p>
            </div>

            <!-- Total Sales (All Verified) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[140px]">
                <div>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Sales</p>
                    <h2 class="text-xl font-black text-slate-800 leading-tight">Rs. <?php echo number_format($total_sales, 2); ?></h2>
                </div>
                <p class="text-[9px] font-bold text-emerald-500 uppercase font-bold">All-time Verified</p>
            </div>

            <!-- Total Profit -->
            <div class="bg-slate-900 p-5 rounded-2xl shadow-xl flex flex-col justify-between min-h-[140px]">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Net Profit</p>
                    <h2 class="text-xl font-black text-emerald-400 leading-tight">Rs. <?php echo number_format($total_profit, 2); ?></h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Live Ledger</p>
                </div>
            </div>
        </div>

        <!-- Section Toggles -->
        <div class="flex gap-4 border-b border-slate-200">
            <a href="?type=sales&time=daily" class="pb-4 px-2 text-sm font-bold transition-all relative <?php echo $type === 'sales' ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600'; ?>">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Sales Reports
                </div>
                <?php if($type === 'sales'): ?><div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600 rounded-t-full"></div><?php endif; ?>
            </a>
            <a href="?type=profit&time=daily" class="pb-4 px-2 text-sm font-bold transition-all relative <?php echo $type === 'profit' ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600'; ?>">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Payment & Profit Reports
                </div>
                <?php if($type === 'profit'): ?><div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600 rounded-t-full"></div><?php endif; ?>
            </a>
        </div>

        <?php if($type === 'sales'): ?>
        <!-- Sales Reporting View -->
        <div class="space-y-6">
            <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm flex flex-wrap gap-4 items-center">
                <form class="flex flex-wrap gap-4 items-center w-full">
                    <input type="hidden" name="type" value="sales">
                    
                    <div class="flex bg-slate-100 p-1 rounded-xl">
                        <button type="submit" name="time" value="daily" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $time_filter === 'daily' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-700'; ?>">Daily</button>
                        <button type="submit" name="time" value="monthly" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $time_filter === 'monthly' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-700'; ?>">Monthly</button>
                        <button type="submit" name="time" value="yearly" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $time_filter === 'yearly' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-700'; ?>">Yearly</button>
                    </div>

                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?php if($time_filter === 'daily'): ?>
                            <input type="date" name="date" value="<?php echo $filter_date; ?>" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                        <?php elseif($time_filter === 'monthly'): ?>
                            <input type="month" name="month" value="<?php echo $filter_month; ?>" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                        <?php else: ?>
                            <input type="number" name="year" value="<?php echo $_GET['year'] ?? date('Y'); ?>" min="2020" max="2030" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <select name="method" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all" <?php echo $filter_method === 'all' ? 'selected' : ''; ?>>All Methods</option>
                            <option value="cash" <?php echo $filter_method === 'cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="card" <?php echo $filter_method === 'card' ? 'selected' : ''; ?>>Card</option>
                            <option value="cheque" <?php echo $filter_method === 'cheque' ? 'selected' : ''; ?>>Cheque</option>
                            <option value="credit" <?php echo $filter_method === 'credit' ? 'selected' : ''; ?>>Credit</option>
                        </select>
                    </div>

                </form>
            </div>

            <?php
            // Calculate Method Breakdown for the selected period
            $methods = [
                'cash' => ['label' => 'Cash Sales', 'color' => 'bg-blue-600', 'shadow' => 'shadow-blue-100', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'card' => ['label' => 'Card Sales', 'color' => 'bg-indigo-600', 'shadow' => 'shadow-indigo-100', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                'cheque' => ['label' => 'Cheque Pool', 'color' => 'bg-amber-500', 'shadow' => 'shadow-amber-100', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'credit' => ['label' => 'Credit Sales', 'color' => 'bg-rose-500', 'shadow' => 'shadow-rose-100', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z']
            ];
            
            $breakdown_data = [];
            foreach($methods as $m_key => $m_meta) {
                if($filter_method !== 'all' && $filter_method !== $m_key) {
                    $breakdown_data[$m_key] = ['total' => 0, 'count' => 0];
                    continue;
                }
                
                $q = "SELECT SUM(final_amount) as total, COUNT(*) as count FROM sales WHERE payment_method = ? ";
                $p = [$m_key];
                if($time_filter === 'daily') { $q .= " AND DATE(created_at) = ? "; $p[] = $filter_date; }
                else if($time_filter === 'monthly') { $q .= " AND DATE_FORMAT(created_at, '%Y-%m') = ? "; $p[] = $filter_month; }
                else { $q .= " AND YEAR(created_at) = ? "; $p[] = $year; }
                
                $stmt = $pdo->prepare($q);
                $stmt->execute($p);
                $breakdown_data[$m_key] = $stmt->fetch();
            }
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach($methods as $m_key => $meta): 
                    $data = $breakdown_data[$m_key];
                    $opacity = ($filter_method !== 'all' && $filter_method !== $m_key) ? 'opacity-30 grayscale' : '';
                ?>
                <div class="<?php echo $meta['color']; ?> p-5 rounded-2xl shadow-lg <?php echo $meta['shadow']; ?> text-white transition-all hover:-translate-y-1 <?php echo $opacity; ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $meta['icon']; ?>"></path></svg>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-widest bg-black/10 px-2 py-0.5 rounded-full"><?php echo $data['count']; ?> Sales</span>
                    </div>
                    <p class="text-[9px] font-bold uppercase tracking-widest opacity-80 mb-1"><?php echo $meta['label']; ?></p>
                    <h3 class="text-lg font-black leading-tight">Rs. <?php echo number_format($data['total'] ?: 0, 2); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px]">
                        <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sale ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date/Time</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Method</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        // Base conditions for counting and fetching
                        $where_clause = " WHERE 1=1 ";
                        $params = [];
                        
                        if($time_filter === 'daily') { $where_clause .= " AND DATE(s.created_at) = ? "; $params[] = $filter_date; }
                        else { $where_clause .= " AND DATE_FORMAT(s.created_at, '%Y-%m') = ? "; $params[] = $filter_month; }
                        
                        if($filter_method !== 'all') { $where_clause .= " AND s.payment_method = ? "; $params[] = $filter_method; }

                        // 1. Get Total Count for Pagination
                        $count_query = "SELECT COUNT(*) FROM sales s $where_clause";
                        $count_stmt = $pdo->prepare($count_query);
                        $count_stmt->execute($params);
                        $total_records = $count_stmt->fetchColumn();
                        $total_pages = ceil($total_records / $limit);

                        // 2. Fetch Paginated Records
                        $query = "SELECT s.*, c.name as cust_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id $where_clause ORDER BY s.created_at DESC LIMIT $limit OFFSET $offset";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($params);
                        $sales_rows = $stmt->fetchAll();

                        foreach($sales_rows as $row):
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">ID-<?php echo $row['id']; ?></td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?php echo htmlspecialchars($row['cust_name'] ?: 'Guest'); ?></td>
                            <td class="px-6 py-4 text-[11px] text-slate-500"><?php echo date('d M, Y h:i A', strtotime($row['created_at'])); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] font-black uppercase"><?php echo $row['payment_method']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">Rs. <?php echo number_format($row['final_amount'], 2); ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if($row['payment_status'] === 'approved'): ?>
                                    <span class="text-[9px] font-black text-white uppercase px-3 py-1 bg-emerald-500 rounded-full shadow-sm">Approved</span>
                                <?php elseif($row['payment_status'] === 'pending'): ?>
                                    <span class="text-[9px] font-black text-white uppercase px-3 py-1 bg-amber-400 rounded-full shadow-sm">Pending</span>
                                <?php else: ?>
                                    <span class="text-[9px] font-black text-white uppercase px-3 py-1 bg-rose-500 rounded-full shadow-sm">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; if(empty($sales_rows)): ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">No sales found for the selected filter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <?php if($total_pages > 1): ?>
            <div class="flex justify-center items-center gap-2 pt-10 pb-6">
                <?php if($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-400 hover:text-blue-600 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-bold transition-all <?php echo $i === $page ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php elseif($i == $page - 3 || $i == $page + 3): ?>
                        <span class="text-slate-400">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-400 hover:text-blue-600 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Download Section -->
            <div class="mt-12 pt-8 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Monthly Sales CSV
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <?php foreach($monthly_summary as $m): ?>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 flex justify-between items-center group hover:border-blue-500 transition-all">
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase"><?php echo date('F Y', strtotime($m['month'].'-01')); ?></p>
                            <p class="text-[10px] text-slate-400 font-bold">Total: Rs. <?php echo number_format($m['total'], 2); ?></p>
                        </div>
                        <a href="?export=csv&month=<?php echo $m['month']; ?>" class="p-2 bg-slate-50 rounded-lg text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Profit Analysis View -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Period Analysis</h3>
                <form class="space-y-4">
                    <input type="hidden" name="type" value="profit">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Time Scale</label>
                            <select name="time" onchange="this.form.submit()" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                                <option value="daily" <?php echo $time_filter==='daily'?'selected':''; ?>>Daily</option>
                                <option value="monthly" <?php echo $time_filter==='monthly'?'selected':''; ?>>Monthly</option>
                                <option value="yearly" <?php echo $time_filter==='yearly'?'selected':''; ?>>Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Select Period</label>
                            <?php if($time_filter === 'daily'): ?>
                                <input type="date" name="date" value="<?php echo $filter_date; ?>" onchange="this.form.submit()" class="w-full bg-slate-50 border-none px-4 py-2 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                            <?php elseif($time_filter === 'monthly'): ?>
                                <input type="month" name="month" value="<?php echo $filter_month; ?>" onchange="this.form.submit()" class="w-full bg-slate-50 border-none px-4 py-2 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                            <?php else: ?>
                                <input type="number" name="year" value="<?php echo $_GET['year'] ?? date('Y'); ?>" min="2020" max="2030" onchange="this.form.submit()" class="w-full bg-slate-50 border-none px-4 py-2 rounded-xl text-sm font-bold text-slate-700 outline-none ring-1 ring-slate-100">
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <?php
                // Fetch periodic stats including Pending
                if($time_filter === 'daily') {
                    $p_sales = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = ? AND payment_status IN ('approved', 'pending')");
                    $p_sales->execute([$filter_date]);
                    $p_exp = $pdo->prepare("SELECT SUM(total_amount) FROM invoices WHERE DATE(created_at) = ? AND status = 'completed'");
                    $p_exp->execute([$filter_date]);
                } else if($time_filter === 'monthly') {
                    $p_sales = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND payment_status IN ('approved', 'pending')");
                    $p_sales->execute([$filter_month]);
                    $p_exp = $pdo->prepare("SELECT SUM(total_amount) FROM invoices WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'completed'");
                    $p_exp->execute([$filter_month]);
                } else {
                    $p_sales = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE YEAR(created_at) = ? AND payment_status IN ('approved', 'pending')");
                    $p_sales->execute([$year]);
                    $p_exp = $pdo->prepare("SELECT SUM(total_amount) FROM invoices WHERE YEAR(created_at) = ? AND status = 'completed'");
                    $p_exp->execute([$year]);
                }
                $ps = $p_sales->fetchColumn() ?: 0;
                $pe = $p_exp->fetchColumn() ?: 0;
                $pp = $ps - $pe;
                ?>

                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-500">Period Revenue</span>
                        <span class="text-sm font-black text-slate-900">Rs. <?php echo number_format($ps, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-rose-500">
                        <span class="text-sm font-bold">Period Expenses</span>
                        <span class="text-sm font-black">- Rs. <?php echo number_format($pe, 2); ?></span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-900 text-white flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider">Estimated Profit</span>
                        </div>
                        <span class="text-xl font-black <?php echo $pp >= 0 ? 'text-blue-400' : 'text-rose-400'; ?>">Rs. <?php echo number_format($pp, 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Monthly Revenue Trend</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Comparison for <?php echo $year; ?></p>
                    </div>
                </div>
                
                <?php
                // Fetch Monthly Data for the Chart
                $chart_data = array_fill(1, 12, 0); // Jan to Dec
                $chart_stmt = $pdo->prepare("SELECT MONTH(created_at) as m, SUM(final_amount) as total 
                                             FROM sales 
                                             WHERE YEAR(created_at) = ? AND payment_status IN ('approved', 'pending')
                                             GROUP BY m ORDER BY m ASC");
                $chart_stmt->execute([$year]);
                while($c_row = $chart_stmt->fetch()) {
                    $chart_data[$c_row['m']] = (float)$c_row['total'];
                }
                $chart_json = json_encode(array_values($chart_data));
                ?>
                
                <div class="flex-grow min-h-[300px] relative">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue (Rs.)',
                        data: <?php echo $chart_json; ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: {
                                font: { size: 10, weight: 'bold' },
                                color: '#94a3b8',
                                callback: value => 'Rs. ' + value.toLocaleString()
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10, weight: 'bold' },
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        </script>
        <?php endif; ?>
    </main>

    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-slate-200 py-3 hidden print:hidden md:block">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            <span>Vehicle Square Analytics Engine V1.0</span>
            <span>Generated on <?php echo date('Y-m-d H:i:s'); ?></span>
        </div>
    </div>

    <?php
    ob_end_flush();
    ?>
</body>
</html>
