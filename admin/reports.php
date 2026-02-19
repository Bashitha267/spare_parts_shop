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

// Today's Gross Intake (Approved + Pending)
$today_gross_stmt = $pdo->prepare("SELECT SUM(final_amount) FROM sales WHERE DATE(created_at) = ?");
$today_gross_stmt->execute([$today]);
$today_gross_intake = $today_gross_stmt->fetchColumn() ?: 0;

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
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #0d1117;
            color: #e6edf3;
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
        .blue-gradient-card {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(30, 64, 175, 0.8) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .glass-nav {
            background: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        input, select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            outline: none !important;
            appearance: none;
            cursor: pointer;
        }
        option {
            background-color: #0f172a !important;
            color: white !important;
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
        .emerald-gradient {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.8) 0%, rgba(5, 150, 105, 0.7) 100%);
        }
        .amber-gradient {
            background: linear-gradient(135deg, rgba(120, 53, 15, 0.8) 0%, rgba(217, 119, 6, 0.7) 100%);
        }
        .rose-gradient {
            background: linear-gradient(135deg, rgba(136, 19, 55, 0.8) 0%, rgba(225, 29, 72, 0.7) 100%);
        }
        .indigo-gradient {
            background: linear-gradient(135deg, rgba(49, 46, 129, 0.8) 0%, rgba(67, 56, 202, 0.7) 100%);
        }
        .card-glow {
            position: absolute;
            top: -50%;
            right: -50%;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-50">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Reports</h1>
            </div>
            <div class="flex items-center gap-2">
                
            </div>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-10 relative z-10">
        
        <!-- Summary Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Today's Approved Sales -->
            <div class="emerald-gradient p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] border border-emerald-500/20 backdrop-blur-xl shadow-2xl shadow-emerald-950/20 group hover:scale-[1.02] transition-all">
                <div class="card-glow"></div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-emerald-200 uppercase tracking-widest mb-2 opacity-60">Total Verified Sales Today</p>
                    <h2 class="text-xl font-black text-white leading-tight">Rs. <?php echo number_format($today_sales_approved, 2); ?></h2>
                </div>
                <div class="relative z-10 flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-white/10 rounded-lg text-[9px] font-black text-emerald-100 uppercase tracking-widest border border-white/10"><?php echo $today_sales_count; ?> Sales</span>
                </div>
            </div>

            <!-- Pending Cheques Today -->
            <div class="amber-gradient p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] border border-amber-500/20 backdrop-blur-xl shadow-2xl shadow-amber-950/20 group hover:scale-[1.02] transition-all">
                <div class="card-glow"></div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-amber-200 uppercase tracking-widest mb-2 opacity-60">Pending Cheques</p>
                    <h2 class="text-xl font-black text-white leading-tight">Rs. <?php echo number_format($today_pending_cheque, 2); ?></h2>
                </div>
               
            </div>

            <!-- Pending Credit Today -->
            <div class="rose-gradient p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] border border-rose-500/20 backdrop-blur-xl shadow-2xl shadow-rose-950/20 group hover:scale-[1.02] transition-all">
                <div class="card-glow"></div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-rose-200 uppercase tracking-widest mb-2 opacity-60">Pending Credits</p>
                    <h2 class="text-xl font-black text-white leading-tight">Rs. <?php echo number_format($today_pending_credit, 2); ?></h2>
                </div>
             
            </div>

            <!-- Gross Intake Today -->
            <div class="indigo-gradient p-6 rounded-[2rem] relative overflow-hidden flex flex-col justify-between min-h-[160px] border border-indigo-500/20 backdrop-blur-xl shadow-2xl shadow-indigo-950/20 group hover:scale-[1.02] transition-all">
                <div class="card-glow"></div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-indigo-200 uppercase tracking-widest mb-2 opacity-60">Gross Profit From Today's Sales</p>
                    <h2 class="text-xl font-black text-white leading-tight">Rs. <?php echo number_format($today_gross_intake, 2); ?></h2>
                </div>
             
            </div>
        </div>

        <!-- Fiscal Performance Graph -->
        <div class="blue-gradient-card p-10 rounded-[3rem] border-white/10 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tighter uppercase mb-1">Total Sales Monthly</h3>
                        <p class="text-[10px] text-blue-300/40 font-black uppercase tracking-[0.2em]"> <?php echo date('Y'); ?></p>
                    </div>
                    
                    <div class="flex bg-white/5 p-1.5 rounded-2xl border border-white/5">
                        <button onclick="updateChart('sales')" id="btn-sales" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-blue-500 shadow-lg shadow-blue-500/20 text-white">Total Sales</button>
                        <button onclick="updateChart('profit')" id="btn-profit" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-white/30 hover:text-white">Net Profit</button>
                    </div>
                </div>

                <div class="h-[350px] w-full">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-blue-500/5 rounded-full blur-[100px]"></div>
        </div>

        <!-- Sales Reporting View -->
        <div class="space-y-10">
            <div class="blue-gradient-card p-6 rounded-[2.5rem] flex flex-wrap gap-8 items-center border-white/10">
                <form class="flex flex-wrap gap-8 items-center w-full">
                    
                    <div class="flex bg-white/5 p-1.5 rounded-2xl border border-white/5">
                        <button type="submit" name="type" value="sales" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $type === 'sales' ? 'bg-blue-500 shadow-lg shadow-blue-500/20 text-white font-black' : 'text-white/30 hover:text-white'; ?>">Sales Ledger</button>
                        <button type="submit" name="type" value="logs" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $type === 'logs' ? 'bg-indigo-500 shadow-lg shadow-indigo-500/20 text-white font-black' : 'text-white/30 hover:text-white'; ?>">System Audits</button>
                    </div>

                    <div class="flex bg-white/5 p-1.5 rounded-2xl border border-white/5">
                        <button type="submit" name="time" value="daily" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $time_filter === 'daily' ? 'bg-blue-500 shadow-lg shadow-blue-500/20 text-white font-black' : 'text-white/30 hover:text-white'; ?>">Daily</button>
                        <button type="submit" name="time" value="monthly" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $time_filter === 'monthly' ? 'bg-blue-500 shadow-lg shadow-blue-500/20 text-white font-black' : 'text-white/30 hover:text-white'; ?>">Monthly</button>
                        <button type="submit" name="time" value="yearly" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $time_filter === 'yearly' ? 'bg-blue-500 shadow-lg shadow-blue-500/20 text-white font-black' : 'text-white/30 hover:text-white'; ?>">Yearly</button>
                    </div>

                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 text-blue-300/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?php if($time_filter === 'daily'): ?>
                            <input type="date" name="date" value="<?php echo $filter_date; ?>" onchange="this.form.submit()" class="px-6 py-2.5 rounded-2xl text-[11px] font-black uppercase tracking-widest">
                        <?php elseif($time_filter === 'monthly'): ?>
                            <input type="month" name="month" value="<?php echo $filter_month; ?>" onchange="this.form.submit()" class="px-6 py-2.5 rounded-2xl text-[11px] font-black uppercase tracking-widest">
                        <?php else: ?>
                            <input type="number" name="year" value="<?php echo $_GET['year'] ?? date('Y'); ?>" min="2020" max="2030" onchange="this.form.submit()" class="px-6 py-2.5 rounded-2xl text-[11px] font-black uppercase tracking-widest">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 text-blue-300/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <select name="method" onchange="this.form.submit()" class="px-6 py-2.5 rounded-2xl text-[11px] font-black uppercase tracking-widest min-w-[160px]">
                            <option value="all">All Methods</option>
                            <option value="cash" <?php echo $filter_method === 'cash' ? 'selected' : ''; ?>>Cash Entry</option>
                            <option value="card" <?php echo $filter_method === 'card' ? 'selected' : ''; ?>>Card Terminal</option>
                            <option value="cheque" <?php echo $filter_method === 'cheque' ? 'selected' : ''; ?>>Cheque Pool</option>
                            <option value="credit" <?php echo $filter_method === 'credit' ? 'selected' : ''; ?>>Credit Facility</option>
                        </select>
                    </div>

                </form>
            </div>

            <?php
            // Calculate Method Breakdown for the selected period
            $methods = [
                'cash' => ['label' => 'Cash Payments', 'color' => 'bg-blue-600', 'shadow' => 'shadow-blue-500/20', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'card' => ['label' => 'Cards Payments', 'color' => 'bg-indigo-600', 'shadow' => 'shadow-indigo-500/20', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                'cheque' => ['label' => 'Cheque Payments', 'color' => 'bg-amber-500', 'shadow' => 'shadow-amber-500/20', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'credit' => ['label' => 'Credit Payments', 'color' => 'bg-rose-500', 'shadow' => 'shadow-rose-500/20', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z']
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($methods as $m_key => $meta): 
                    $data = $breakdown_data[$m_key];
                    $opacity = ($filter_method !== 'all' && $filter_method !== $m_key) ? 'opacity-20 grayscale' : '';
                ?>
                <div class="<?php echo $meta['color']; ?> p-6 rounded-[2rem] shadow-xl <?php echo $meta['shadow']; ?> text-white transition-all hover:-translate-y-2 <?php echo $opacity; ?> border border-white/10">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $meta['icon']; ?>"></path></svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-black/20 px-3 py-1 rounded-full border border-white/5"><?php echo $data['count']; ?> TRX</span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2"><?php echo $meta['label']; ?></p>
                    <h3 class="text-xl font-black leading-tight tracking-widest">Rs. <?php echo number_format($data['total'] ?: 0, 2); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="blue-gradient-card rounded-[2.5rem] border border-white/10 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px] border-collapse">
                        <thead class="bg-white/5 border-b border-white/10">
                        <?php if($type === 'sales'): ?>
                        <tr>
                            <th class="px-8 py-6">Sale ID</th>
                            <th class="px-8 py-6">Customer Profile</th>
                            <th class="px-8 py-6">Digital Timeline</th>
                            <th class="px-8 py-6 text-center">Protocol</th>
                            <th class="px-8 py-6 text-right">Settlement</th>
                            <th class="px-8 py-6 text-center">Status</th>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th class="px-8 py-6">Log ID</th>
                            <th class="px-8 py-6">Officer Profile</th>
                            <th class="px-8 py-6">Digital Timeline</th>
                            <th class="px-8 py-6 text-center">Action</th>
                            <th class="px-8 py-6 text-right">Record Ref</th>
                            <th class="px-8 py-6 text-center">Reason</th>
                        </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php
                        // Base conditions for counting and fetching
                        $where_clause = " WHERE 1=1 ";
                        $params = [];
                        
                        $date_col = ($type === 'sales') ? 's.created_at' : 'al.created_at';

                        if($time_filter === 'daily') { $where_clause .= " AND DATE($date_col) = ? "; $params[] = $filter_date; }
                        else if($time_filter === 'monthly') { $where_clause .= " AND DATE_FORMAT($date_col, '%Y-%m') = ? "; $params[] = $filter_month; }
                        else { $where_clause .= " AND YEAR($date_col) = ? "; $params[] = $year; }
                        
                        if($type === 'sales') {
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
                            $rows = $stmt->fetchAll();

                            foreach($rows as $row):
                            ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="px-8 py-6">
                                    <span class="font-mono text-[10px] font-black text-blue-300/40 tracking-tighter uppercase px-3 py-1 bg-white/5 rounded-lg border border-white/5">ID-<?php echo $row['id']; ?></span>
                                </td>
                                <td class="px-8 py-6 font-black text-white text-sm tracking-tight"><?php echo htmlspecialchars($row['cust_name'] ?: 'Public Guest'); ?></td>
                                <td class="px-8 py-6">
                                    <p class="text-[10px] font-black text-white/30 uppercase tracking-widest"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
                                    <p class="text-[9px] font-bold text-blue-400 mt-1 uppercase"><?php echo date('h:i A', strtotime($row['created_at'])); ?></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 bg-white/5 text-blue-300/60 border border-white/10 rounded-lg text-[9px] font-black uppercase tracking-widest"><?php echo $row['payment_method']; ?></span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-white tracking-widest text-sm">Rs. <?php echo number_format($row['final_amount'], 2); ?></td>
                                <td class="px-8 py-6 text-center">
                                    <?php if($row['payment_status'] === 'approved'): ?>
                                        <span class="text-[9px] font-black text-white uppercase tracking-widest px-4 py-1.5 bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20">Approved</span>
                                    <?php elseif($row['payment_status'] === 'pending'): ?>
                                        <span class="text-[9px] font-black text-white uppercase tracking-widest px-4 py-1.5 bg-amber-400 rounded-xl shadow-lg shadow-amber-400/20">Pending</span>
                                    <?php else: ?>
                                        <span class="text-[9px] font-black text-white uppercase tracking-widest px-4 py-1.5 bg-rose-500 rounded-xl shadow-lg shadow-rose-500/20">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; 
                        } else {
                            // Logic for Audit Logs
                            $count_query = "SELECT COUNT(*) FROM audit_logs al $where_clause";
                            $count_stmt = $pdo->prepare($count_query);
                            $count_stmt->execute($params);
                            $total_records = $count_stmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            $query = "SELECT al.*, u.full_name as officer_name FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id $where_clause ORDER BY al.created_at DESC LIMIT $limit OFFSET $offset";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute($params);
                            $rows = $stmt->fetchAll();

                            foreach($rows as $row):
                            ?>
                            <tr class="hover:bg-white/5 transition-all group border-l-2 <?php echo $row['action_type'] === 'delete' ? 'border-rose-500' : 'border-amber-500'; ?>">
                                <td class="px-8 py-6">
                                    <span class="font-mono text-[10px] font-black text-blue-300/40 tracking-tighter uppercase px-3 py-1 bg-white/5 rounded-lg border border-white/5">LOG-<?php echo $row['id']; ?></span>
                                </td>
                                <td class="px-8 py-6 font-black text-white text-sm tracking-tight"><?php echo htmlspecialchars($row['officer_name'] ?: 'System Process'); ?></td>
                                <td class="px-8 py-6">
                                    <p class="text-[10px] font-black text-white/30 uppercase tracking-widest"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
                                    <p class="text-[9px] font-bold text-blue-400 mt-1 uppercase"><?php echo date('h:i A', strtotime($row['created_at'])); ?></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 <?php echo $row['action_type'] === 'delete' ? 'bg-rose-500/10 text-rose-400' : 'bg-amber-500/10 text-amber-400'; ?> border border-white/10 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                        <?php echo strtoupper($row['action_type']); ?> <?php echo strtoupper($row['table_name']); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-blue-300 tracking-widest text-sm">#<?php echo $row['record_id']; ?></td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-[9px] font-black text-white/60 uppercase tracking-widest px-4 py-1.5 bg-white/5 rounded-xl border border-white/5">
                                        <?php echo htmlspecialchars($row['reason'] ?: 'No reason provided'); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach;
                        }
                        
                        if(empty($rows)): ?>
                        <tr><td colspan="6" class="px-8 py-20 text-center text-blue-300/20 font-black uppercase tracking-[0.2em] italic">Zero forensic records found in this segment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <?php if($total_pages > 1): ?>
            <div class="flex justify-center items-center gap-3 py-10 border-t border-white/5 bg-white/5">
                <?php if($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 text-blue-300 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                <?php endif; ?>

                <div class="flex gap-2">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="w-10 h-10 flex items-center justify-center border rounded-xl text-[11px] font-black transition-all <?php echo $i === $page ? 'bg-blue-500 text-white border-blue-500 shadow-xl shadow-blue-500/20' : 'bg-white/5 text-white/40 border-white/10 hover:bg-white/10 hover:text-white'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif($i == $page - 2 || $i == $page + 2): ?>
                            <span class="text-white/10 flex items-center">•••</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <?php if($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 text-blue-300 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

            <!-- Download Section -->
            <div class="mt-16 pt-10 border-t border-white/5">
                <h3 class="text-[11px] font-black text-blue-300 uppercase tracking-[0.3em] mb-8 flex items-center gap-3 opacity-60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Monthly Sales Reports Downloads
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <?php foreach($monthly_summary as $m): ?>
                    <div class="blue-gradient-card p-6 rounded-[2rem] flex justify-between items-center group hover:scale-[1.02] transition-all border-white/5">
                        <div>
                            <p class="text-[10px] font-black text-white uppercase tracking-widest"><?php echo date('F Y', strtotime($m['month'].'-01')); ?></p>
                            <p class="text-[9px] text-emerald-400 font-bold mt-1 tracking-widest">VOL: Rs. <?php echo number_format($m['total'], 2); ?></p>
                        </div>
                        <a href="?export=csv&month=<?php echo $m['month']; ?>" class="p-3 bg-white/5 rounded-xl text-blue-300 hover:bg-blue-500 hover:text-white transition-all shadow-inner border border-white/10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


    </main>

    <div class="fixed bottom-0 left-0 right-0 glass-nav py-4 z-40 print:hidden">
        <div class="max-w-7xl mx-auto px-8 flex justify-between items-center text-[9px] font-black text-white/20 uppercase tracking-[0.3em]">
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
                        grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)', font: { size: 10, weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)', font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        });

        function updateChart(type) {
            const isSales = type === 'sales';
            
            // Toggle Buttons
            document.getElementById('btn-sales').className = isSales ? 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-blue-500 shadow-lg shadow-blue-500/20 text-white' : 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-white/30 hover:text-white';
            document.getElementById('btn-profit').className = !isSales ? 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all bg-emerald-500 shadow-lg shadow-emerald-500/20 text-white' : 'px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-white/30 hover:text-white';

            // Update Dataset
            performanceChart.data.datasets[0].label = isSales ? 'Revenue (LKR)' : 'Profit (LKR)';
            performanceChart.data.datasets[0].data = isSales ? salesData : profitData;
            performanceChart.data.datasets[0].backgroundColor = isSales ? 'rgba(59, 130, 246, 0.5)' : 'rgba(16, 185, 129, 0.5)';
            performanceChart.data.datasets[0].borderColor = isSales ? '#3b82f6' : '#10b981';
            performanceChart.data.datasets[0].hoverBackgroundColor = isSales ? '#3b82f6' : '#10b981';
            
            performanceChart.update();
        }
    </script>
</body>
</html>
