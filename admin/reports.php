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

    // Fetch Summary Stats for the selected month
    $stmt_stats = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN LOWER(payment_method) = 'cash' AND LOWER(payment_status) = 'approved' THEN final_amount ELSE 0 END) as total_cash,
            SUM(CASE WHEN LOWER(payment_method) = 'cheque' AND LOWER(payment_status) = 'approved' THEN final_amount ELSE 0 END) as total_cheque,
            SUM(CASE WHEN LOWER(payment_method) = 'credit' AND LOWER(payment_status) = 'approved' THEN final_amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN LOWER(payment_status) = 'pending' THEN final_amount ELSE 0 END) as total_pending,
            SUM(CASE WHEN LOWER(payment_status) = 'rejected' THEN final_amount ELSE 0 END) as total_cancelled,
            SUM(CASE WHEN LOWER(payment_status) = 'approved' THEN final_amount ELSE 0 END) as total_sales
        FROM sales 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ?
    ");
    $stmt_stats->execute([$month]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    // Calculate monthly cost for profit
    $stmt_profit = $pdo->prepare("
        SELECT SUM(si.qty * b.buying_price) as cost 
        FROM sale_items si 
        JOIN sales s ON si.sale_id = s.id 
        JOIN batches b ON si.batch_id = b.id 
        WHERE DATE_FORMAT(s.created_at, '%Y-%m') = ? AND LOWER(s.payment_status) = 'approved'
    ");
    $stmt_profit->execute([$month]);
    $total_monthly_cost = $stmt_profit->fetchColumn() ?: 0;
    $total_monthly_profit = ($stats['total_sales'] ?? 0) - $total_monthly_cost;

    // Write Summary Section
    fputcsv($output, ["MONTHLY SALES REPORT - " . date('F Y', strtotime($month.'-01'))]);
    fputcsv($output, []);
    fputcsv($output, ["SUMMARY STATISTICS"]);
    fputcsv($output, ["Total Sales (Approved)", number_format($stats['total_sales'] ?? 0, 2)]);
    fputcsv($output, ["Total Estimated Profit", number_format($total_monthly_profit, 2)]);
    fputcsv($output, ["Total Cash Collected", number_format($stats['total_cash'] ?? 0, 2)]);
    fputcsv($output, ["Total Cheque (Approved)", number_format($stats['total_cheque'] ?? 0, 2)]);
    fputcsv($output, ["Total Credit (Approved)", number_format($stats['total_credit'] ?? 0, 2)]);
    fputcsv($output, ["Total Pending Amount", number_format($stats['total_pending'] ?? 0, 2)]);
    fputcsv($output, ["Total Cancelled/Rejected", number_format($stats['total_cancelled'] ?? 0, 2)]);
    
    fclose($output);
    exit;
}

// 1. Initializations
$time_filter = $_GET['time'] ?? 'daily';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// 2. Summary Calculations (Based on Filters)
$where_clause = " WHERE DATE(created_at) BETWEEN ? AND ? ";
$params = [$start_date, $end_date];

// Approved Sales
$sales_stmt = $pdo->prepare("SELECT SUM(final_amount) as total, COUNT(*) as count FROM sales $where_clause AND LOWER(payment_status) = 'approved'");
$sales_stmt->execute($params);
$sales_raw = $sales_stmt->fetch();
$total_approved_sales = $sales_raw['total'] ?: 0;
$total_approved_count = $sales_raw['count'] ?: 0;

// Pending Cheques
$cheque_p_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'cheque' AND LOWER(payment_status) = 'pending'");
$cheque_p_stmt->execute($params);
$total_pending_cheque = $cheque_p_stmt->fetchColumn() ?: 0;

// Pending Credits
$credit_p_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'credit' AND LOWER(payment_status) = 'pending'");
$credit_p_stmt->execute($params);
$total_pending_credit = $credit_p_stmt->fetchColumn() ?: 0;

// Cancelled Payments
$cancelled_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_status) = 'rejected'");
$cancelled_stmt->execute($params);
$total_cancelled = $cancelled_stmt->fetchColumn() ?: 0;

// Cash Payments (Approved)
$cash_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'cash' AND LOWER(payment_status) = 'approved'");
$cash_stmt->execute($params);
$total_cash = $cash_stmt->fetchColumn() ?: 0;

// Card Payments (Approved)
$card_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'card' AND LOWER(payment_status) = 'approved'");
$card_stmt->execute($params);
$total_card = $card_stmt->fetchColumn() ?: 0;

// Cheque Payments (Approved)
$cheque_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'cheque' AND LOWER(payment_status) = 'approved'");
$cheque_stmt->execute($params);
$total_cheque = $cheque_stmt->fetchColumn() ?: 0;

// Credit Payments (Approved)
$credit_stmt = $pdo->prepare("SELECT SUM(final_amount) as total FROM sales $where_clause AND LOWER(payment_method) = 'credit' AND LOWER(payment_status) = 'approved'");
$credit_stmt->execute($params);
$total_credit = $credit_stmt->fetchColumn() ?: 0;

// Net Profit Calculation for Range
$profit_stmt = $pdo->prepare("
    SELECT SUM(si.qty * b.buying_price) as cost 
    FROM sale_items si 
    JOIN sales s ON si.sale_id = s.id 
    JOIN batches b ON si.batch_id = b.id 
    WHERE DATE(s.created_at) BETWEEN ? AND ? AND (LOWER(s.payment_status) = 'approved' OR LOWER(s.payment_status) = 'rejected')
");
$profit_stmt->execute($params);
$total_cost = $profit_stmt->fetchColumn() ?: 0;
$net_profit = $total_approved_sales - $total_cost;


// Monthly Sales for Download List
$monthly_sales_stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(final_amount) as total 
                                   FROM sales WHERE payment_status IN ('approved', 'pending')
                                   GROUP BY month ORDER BY month DESC");
$monthly_summary = $monthly_sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Chart Data: Monthly Sales & Profit for Current Year
$chart_year = date('Y');
$chart_sales = array_fill(1, 12, 0);
$chart_profit = array_fill(1, 12, 0);

// Fetch Sales
$sales_q = $pdo->prepare("SELECT MONTH(created_at) as month, SUM(final_amount) as total FROM sales WHERE YEAR(created_at) = ? AND payment_status = 'approved' GROUP BY MONTH(created_at)");
$sales_q->execute([$chart_year]);
while($row = $sales_q->fetch()) {
    $chart_sales[(int)$row['month']] = (float)$row['total'];
}

// Fetch Profit (Revenue - Cost)
$profit_q = $pdo->prepare("SELECT MONTH(s.created_at) as month, SUM(si.qty * b.buying_price) as cost 
                           FROM sale_items si 
                           JOIN sales s ON si.sale_id = s.id 
                           JOIN batches b ON si.batch_id = b.id 
                           WHERE YEAR(s.created_at) = ? AND s.payment_status = 'approved' 
                           GROUP BY MONTH(s.created_at)");
$profit_q->execute([$chart_year]);
while($row = $profit_q->fetch()) {
    $month = (int)$row['month'];
    $chart_profit[$month] = $chart_sales[$month] - (float)$row['cost'];
}

$chart_labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$chart_sales_data = array_values($chart_sales);
$chart_profit_data = array_values($chart_profit);

// --- NEW PIE CHART DATA ---
// 1. Most Sold Items (Top 10)
$top_items_stmt = $pdo->prepare("
    SELECT p.name, SUM(si.qty) as total_qty 
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    JOIN sales s ON si.sale_id = s.id
    WHERE LOWER(s.payment_status) = 'approved' AND DATE(s.created_at) BETWEEN ? AND ?
    GROUP BY p.name
    ORDER BY total_qty DESC
    LIMIT 10
");
$top_items_stmt->execute($params);
$top_items_data = $top_items_stmt->fetchAll(PDO::FETCH_ASSOC);

$item_names = array_column($top_items_data, 'name');
$item_qtys = array_column($top_items_data, 'total_qty');

// 2. Payment Methods Breakdown (Approved Only)
$payment_breakdown_stmt = $pdo->prepare("
    SELECT payment_method, SUM(final_amount) as total_amount 
    FROM sales 
    WHERE LOWER(payment_status) = 'approved' AND DATE(created_at) BETWEEN ? AND ?
    GROUP BY payment_method
");
$payment_breakdown_stmt->execute($params);
$payment_summary_data = $payment_breakdown_stmt->fetchAll(PDO::FETCH_ASSOC);

$payment_labels = [];
$payment_amounts = [];
$payment_colors = [
    'cash' => '#3b82f6',   // Blue
    'card' => '#4f46e5',   // Indigo
    'cheque' => '#f59e0b', // Amber
    'credit' => '#ef4444'  // Rose
];
$actual_colors = [];

foreach($payment_summary_data as $row) {
    $payment_labels[] = strtoupper($row['payment_method']);
    $payment_amounts[] = (float)$row['total_amount'];
    $actual_colors[] = $payment_colors[strtolower($row['payment_method'])] ?? '#94a3b8';
}
// --- END PIE CHART DATA ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Engine - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        .bg-main {
            background:  url('public/admin_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 2rem;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.08);
        }
        .blue-gradient-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px -10px rgba(37, 99, 235, 0.3);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        input, select {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            outline: none !important;
            appearance: none;
            cursor: pointer;
        }
        option {
            background-color: white !important;
            color: #0f172a !important;
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem 1.5rem !important;
            font-size: 10px;
        }
        .emerald-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        .amber-gradient {
            background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%);
        }
        .rose-gradient {
            background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%);
        }
        .indigo-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        }
        .card-glow {
            position: absolute;
            top: -50%;
            right: -50%;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        tr:nth-child(even) {
            background-color: rgba(241, 245, 249, 0.5);
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #1e293b;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Reports</h1>
            </div>
            <div class="flex items-center gap-2">
                
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- TOP SECTION: PRIMARY FINANCIAL KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Sales -->
            <div class="blue-gradient-card p-8 rounded-[2.5rem] relative overflow-hidden flex flex-col justify-between min-h-[200px] text-white shadow-2xl group hover:scale-[1.02] transition-all border border-white/10">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-3 opacity-80">Total Revenue</p>
                    <h2 class="text-2xl font-black leading-tight tracking-tighter">Rs. <?php echo number_format($total_approved_sales, 2); ?></h2>
                </div>
                <div class="relative z-10 flex items-center">
                     <span class="text-[9px] font-black bg-white/15 px-4 py-2 rounded-xl border border-white/20 uppercase tracking-widest backdrop-blur-md"><?php echo $total_approved_count; ?> Sales Approved</span>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Pending Cheques -->
            <div class="bg-amber-600/90 p-8 rounded-[2.5rem] relative overflow-hidden flex flex-col justify-between min-h-[200px] text-white shadow-2xl group hover:scale-[1.02] transition-all border border-white/10 backdrop-blur-xl">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-3 opacity-80">Pending Cheques</p>
                    <h2 class="text-2xl font-black leading-tight tracking-tighter">Rs. <?php echo number_format($total_pending_cheque, 2); ?></h2>
                </div>
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center absolute bottom-6 right-6 border border-white/10 shadow-inner backdrop-blur-md">
                    <svg class="w-7 h-7 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <!-- Pending Credits -->
            <div class="bg-rose-700/90 p-8 rounded-[2.5rem] relative overflow-hidden flex flex-col justify-between min-h-[200px] text-white shadow-2xl group hover:scale-[1.02] transition-all border border-white/10 backdrop-blur-xl">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-3 opacity-80">Pending Credits</p>
                    <h2 class="text-2xl font-black leading-tight tracking-tighter">Rs. <?php echo number_format($total_pending_credit, 2); ?></h2>
                </div>
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center absolute bottom-6 right-6 border border-white/10 shadow-inner backdrop-blur-md">
                    <svg class="w-7 h-7 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- MIDDLE SECTION: FILTERING CONTROLS -->
        <div class="glass-card p-6 md:p-8 flex flex-wrap gap-8 items-center border-b-4 border-blue-500/20 rounded-[2.5rem]">
            <form class="flex flex-wrap gap-6 md:gap-8 items-end w-full" id="filterForm">
                <div class="space-y-3 flex-grow sm:flex-none w-full sm:w-auto">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Analysis Period Start</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" onchange="this.form.submit()" class="w-full px-6 md:px-8 py-4 rounded-2xl text-[12px] font-black uppercase tracking-widest border border-slate-200 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="space-y-3 flex-grow sm:flex-none w-full sm:w-auto">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Analysis Period End</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" onchange="this.form.submit()" class="w-full px-6 md:px-8 py-4 rounded-2xl text-[12px] font-black uppercase tracking-widest border border-slate-200 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="flex-shrink-0 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.25em] transition-all shadow-lg shadow-blue-500/20 active:scale-95">Apply</button>
                </div>
                <div class="h-12 w-px bg-slate-200 mx-2 hidden lg:block"></div>
                <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                    <button type="button" onclick="setRange('today')" class="flex-grow sm:flex-none px-6 py-3 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all">Today</button>
                    <button type="button" onclick="setRange('week')" class="flex-grow sm:flex-none px-6 py-3 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all">This Week</button>
                    <button type="button" onclick="setRange('month')" class="flex-grow sm:flex-none px-6 py-3 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 transition-all">This Month</button>
                    <button type="button" onclick="setRange('month'); document.getElementById('filterForm').submit();" class="flex-grow sm:flex-none px-6 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- LOWER MIDDLE SECTION: DETAILED BREAKDOWN CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Net Profit -->
            <div class="emerald-gradient p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] text-white shadow-xl shadow-emerald-500/20 border border-white/20 group hover:scale-[1.02] transition-all">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80">Net Profit (Est.)</p>
                    <h2 class="text-2xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($net_profit, 2); ?></h2>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center absolute bottom-5 right-5 backdrop-blur-sm"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
            </div>

            <!-- Cash Color -->
            <div class="bg-sky-500 p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] text-white border border-white/10 group hover:scale-[1.02] transition-all shadow-xl shadow-sky-500/10">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80">Cash Liquidity</p>
                    <h2 class="text-2xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($total_cash, 2); ?></h2>
                </div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Direct Settlement</p>
            </div>

            <!-- Card Color -->
            <div class="bg-indigo-600 p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] text-white border border-white/10 group hover:scale-[1.02] transition-all shadow-xl shadow-indigo-600/10">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80">Card Terminals</p>
                    <h2 class="text-2xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($total_card, 2); ?></h2>
                </div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Digital Processing</p>
            </div>

            <!-- Cheque Color -->
            <div class="bg-amber-500 p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] text-white border border-white/10 group hover:scale-[1.02] transition-all shadow-xl shadow-amber-500/10">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80">Approved Cheques</p>
                    <h2 class="text-2xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($total_cheque, 2); ?></h2>
                </div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Verified & Cleared</p>
            </div>

            <!-- Credit Color -->
            <div class="bg-rose-600 p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] text-white border border-white/10 group hover:scale-[1.02] transition-all shadow-xl shadow-rose-600/10">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80">Recovered Credit</p>
                    <h2 class="text-2xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($total_credit, 2); ?></h2>
                </div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Settled Facility</p>
            </div>
        </div>

        <!-- Fiscal Performance Graph -->
        <div class="glass-card p-10 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tighter uppercase mb-1">Total Sales Monthly</h3>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]"> <?php echo date('Y'); ?></p>
                    </div>
                    
                    <div class="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                        <button onclick="updateChart('sales')" id="btn-sales" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-blue-600 shadow-lg shadow-blue-500/20 text-white">Total Sales</button>
                        <button onclick="updateChart('profit')" id="btn-profit" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-slate-600">Net Profit</button>
                    </div>
                </div>

                <div class="h-[350px] w-full">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribution Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Top Selling Items Pie Chart -->
            <div class="glass-card p-10 shadow-2xl">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase mb-8">Top 10 Selling Items</h3>
                <div class="h-[400px] flex items-center justify-center">
                    <?php if(empty($item_names)): ?>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">No sales data available</p>
                    <?php else: ?>
                        <canvas id="itemsPieChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Methods Pie Chart -->
            <div class="glass-card p-10 shadow-2xl">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase mb-8">Revenue by Payment Method</h3>
                <div class="h-[400px] flex items-center justify-center">
                    <?php if(empty($payment_labels)): ?>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">No approved payments found</p>
                    <?php else: ?>
                        <canvas id="paymentsPieChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>


            <!-- Download Section -->
            <div class="mt-16 pt-10 border-t border-slate-200">
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Monthly Sales Reports Downloads
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <?php foreach($monthly_summary as $m): ?>
                    <div class="glass-card p-6 flex justify-between items-center group hover:scale-[1.02] transition-all border shadow-sm">
                        <div>
                            <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest"><?php echo date('F Y', strtotime($m['month'].'-01')); ?></p>
                            <p class="text-[9px] text-emerald-600 font-bold mt-1 tracking-widest">VOL: Rs. <?php echo number_format($m['total'], 2); ?></p>
                        </div>
                        <a href="?export=csv&month=<?php echo $m['month']; ?>" class="p-3 bg-blue-50 rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


    </main>

    <div class="fixed bottom-0 left-0 right-0 glass-nav py-4 z-40 print:hidden">
        <div class="max-w-7xl mx-auto px-8 flex justify-between items-center text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">
            <span>Vehicle Parts Inventory Management</span>
            <span>Last Sync: <?php echo date('Y-m-d H:i:s'); ?></span>
        </div>
    </div>

    <?php
    ob_end_flush();
    ?>
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const salesData = <?php echo json_encode($chart_sales_data); ?>;
        const profitData = <?php echo json_encode($chart_profit_data); ?>;
        const labels = <?php echo json_encode($chart_labels); ?>;
        Chart.register(ChartDataLabels);

        let performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (LKR)',
                    data: salesData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#3b82f6'
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
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { color: 'rgba(0, 0, 0, 0.4)', font: { size: 10, weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(0, 0, 0, 0.4)', font: { size: 10, weight: 'bold' } }
                    }
                },
                plugins: {
                    datalabels: { display: false }
                }
            }
        });

        // 1. Most Sold Items Pie Chart
        const itemsCtx = document.getElementById('itemsPieChart');
        if(itemsCtx) {
            new Chart(itemsCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($item_names); ?>,
                    datasets: [{
                        data: <?php echo json_encode($item_qtys); ?>,
                        backgroundColor: [
                            '#3b82f6', '#4f46e5', '#8b5cf6', '#a855f7', '#d946ef',
                            '#ec4899', '#f43f5e', '#ef4444', '#f97316', '#f59e0b'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 10, weight: 'bold' },
                                padding: 20
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            textShadowColor: 'rgba(0,0,0,0.5)',
                            textShadowBlur: 4,
                            formatter: (value, ctx) => {
                                return ctx.chart.data.labels[ctx.dataIndex];
                            },
                            font: { weight: '900', size: 9 },
                            align: 'center',
                            anchor: 'center',
                            clip: true
                        }
                    }
                }
            });
        }

        // 2. Payment Methods Pie Chart
        const paymentsCtx = document.getElementById('paymentsPieChart');
        if(paymentsCtx) {
            new Chart(paymentsCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($payment_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($payment_amounts); ?>,
                        backgroundColor: <?php echo json_encode($actual_colors); ?>,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 10, weight: 'bold' },
                                padding: 20
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            textShadowColor: 'rgba(0,0,0,0.5)',
                            textShadowBlur: 4,
                            formatter: (value, ctx) => {
                                return ctx.chart.data.labels[ctx.dataIndex];
                            },
                            font: { weight: '900', size: 10 },
                            align: 'center',
                            anchor: 'center',
                            clip: true
                        }
                    }
                }
            });
        }

        function setRange(range) {
            const today = new Date();
            let start, end;
            
            if(range === 'today') {
                start = end = today.toISOString().split('T')[0];
            } else if(range === 'week') {
                const day = today.getDay();
                const diff = today.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday
                start = new Date(today.setDate(diff)).toISOString().split('T')[0];
                end = new Date().toISOString().split('T')[0];
            } else if(range === 'month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            }
            
            document.getElementsByName('start_date')[0].value = start;
            document.getElementsByName('end_date')[0].value = end;
            document.getElementById('filterForm').submit();
        }

        function updateChart(type) {
            const isSales = type === 'sales';
            
            // Toggle Buttons
            document.getElementById('btn-sales').className = isSales ? 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-blue-600 shadow-lg shadow-blue-500/20 text-white' : 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-slate-600';
            document.getElementById('btn-profit').className = !isSales ? 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-emerald-600 shadow-lg shadow-emerald-500/20 text-white' : 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-slate-600';

            // Update Dataset
            performanceChart.data.datasets[0].label = isSales ? 'Revenue (LKR)' : 'Profit (LKR)';
            performanceChart.data.datasets[0].data = isSales ? salesData : profitData;
            performanceChart.data.datasets[0].backgroundColor = isSales ? 'rgba(59, 130, 246, 0.5)' : 'rgba(16, 185, 129, 0.5)';
            performanceChart.data.datasets[0].borderColor = isSales ? '#3b82f6' : '#10b981';
            performanceChart.data.datasets[0].hoverBackgroundColor = isSales ? '#3b82f6' : '#10b981';
            
            performanceChart.update();
        }</script>
</body>
</html>
