<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch History - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }
        th {
            font-weight: 900;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 10px;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : '../cashier/dashboard.php'; ?>" class="p-2 hover:bg-white/10 rounded-xl transition-all text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 tracking-tight uppercase">Item Added History</h1>
            </div>
            <a href="addItems.php" class="bg-blue-500 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:bg-blue-400 transition-all shadow-lg shadow-blue-500/20 uppercase tracking-widest">+ Add New Items</a>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto space-y-8 relative z-10">
        
        <!-- Filter Bar -->
        <div class="blue-gradient-card p-6 rounded-[2rem] flex flex-wrap items-center gap-6">
            <form method="GET" class="flex flex-wrap items-end gap-6 w-full">
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-black text-blue-300 uppercase tracking-widest ml-1">Arrival Date</label>
                    <input type="date" name="date" onchange="this.form.submit()" value="<?php echo $_GET['date'] ?? ''; ?>" class="px-6 py-3 rounded-2xl text-sm font-bold w-full md:w-auto">
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-black text-blue-300 uppercase tracking-widest ml-1">Operator Profile</label>
                    <select name="role" onchange="this.form.submit()" class="px-6 py-3 rounded-2xl text-sm font-bold min-w-[180px]">
                        <option value="">All Clearances</option>
                        <option value="admin" <?php echo ($_GET['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin Entry</option>
                        <option value="cashier" <?php echo ($_GET['role'] ?? '') === 'cashier' ? 'selected' : ''; ?>>Cashier Entry</option>
                    </select>
                </div>

                <div class="flex items-end gap-3 pb-0.5">
                    <?php if(isset($_GET['date']) || isset($_GET['role'])): ?>
                    <a href="batchHistroy.php" class="px-6 py-3 bg-white/5 text-white/40 border border-white/10 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all">Reset Filters</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="blue-gradient-card rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-8 py-6">Arrival Date</th>
                            <th class="px-8 py-6">Authorized Officer</th>
                            <th class="px-8 py-6">Invoice Identifier</th>
                            <th class="px-8 py-6 text-right">Total Amount</th>
                            <th class="px-8 py-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php
                        $where = "WHERE 1=1";
                        $params = [];

                        if(!empty($_GET['date'])) {
                            $where .= " AND DATE(i.created_at) = :date";
                            $params['date'] = $_GET['date'];
                        }

                        if(!empty($_GET['role'])) {
                            $where .= " AND u.role = :role";
                            $params['role'] = $_GET['role'];
                        }

                        $sql = "SELECT i.*, u.full_name, u.role 
                                FROM invoices i 
                                JOIN users u ON i.user_id = u.id 
                                $where 
                                ORDER BY i.created_at DESC";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $found = false;
                        while($row = $stmt->fetch()): $found = true;
                        ?>
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-white tracking-tight"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
                                <p class="text-[10px] font-bold text-white/30 uppercase tracking-tighter mt-1"><?php echo date('h:i A', strtotime($row['created_at'])); ?></p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest <?php echo $row['role'] === 'admin' ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20' : 'bg-blue-400/10 text-blue-400 border border-blue-400/20'; ?>">
                                        <?php echo $row['role']; ?>
                                    </span>
                                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-tight"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-black text-blue-300/40 tracking-tighter uppercase px-3 py-1 bg-white/5 rounded-lg border border-white/5"><?php echo htmlspecialchars($row['invoice_no']); ?></span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-base font-black text-emerald-400 tracking-widest"><?php echo number_format($row['total_amount'], 2); ?></p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center items-center gap-3">
                                    <button onclick="viewBatch(<?php echo $row['id']; ?>)" class="p-3 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-lg active:scale-95" title="View Manifest">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <?php if($_SESSION['role'] === 'admin'): ?>
                                    <button onclick="editBatch(<?php echo $row['id']; ?>)" class="p-3 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-lg active:scale-95" title="Modify Record">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button onclick="deleteBatch(<?php echo $row['id']; ?>)" class="p-3 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-lg active:scale-95" title="Purge Record">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; if(!$found): ?>
                        <tr><td colspan="5" class="px-8 py-12 text-center text-blue-300/20 font-black uppercase tracking-[0.2em] italic">No transaction logs captured for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        async function viewBatch(id) {
            const res = await fetch(`manage_handler.php?action=fetch_batch_details&id=${id}`);
            const data = await res.json();
            
            if(!data.success) return;

            let html = `
                <div class="overflow-x-auto mt-4 px-2">
                    <table class="w-full text-left text-[11px] border-collapse">
                        <thead>
                            <tr class="text-blue-300/50 uppercase tracking-widest border-b border-white/10 italic">
                                <th class="py-2">Item</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Unit Price</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
            `;

            data.items.forEach(item => {
                html += `
                    <tr>
                        <td class="py-3">
                            <p class="font-black text-white">${item.name}</p>
                            <p class="text-[9px] opacity-40">${item.barcode}</p>
                        </td>
                        <td class="py-3 text-center font-bold">${item.original_qty}</td>
                        <td class="py-3 text-right font-mono text-blue-300">${parseFloat(item.buying_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="py-3 text-right font-mono text-emerald-400">${(item.original_qty * item.buying_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;

            Swal.fire({
                title: '<span class="text-xl font-black uppercase tracking-tighter">Itemized Manifest</span>',
                html: html,
                width: '600px',
                confirmButtonText: 'Done',
                confirmButtonColor: '#3b82f6',
                customClass: { 
                    popup: 'rounded-[2rem] bg-slate-900 text-white border border-white/10 px-4',
                    confirmButton: 'rounded-xl font-black uppercase text-[10px] px-8 py-3 tracking-widest w-full'
                }
            });
        }

        function editBatch(id) {
            Swal.fire({
                title: 'Reopen Batch?',
                text: "This will unlock the invoice and return items to active entry mode.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Yes, Authorize Edit',
                customClass: { 
                    popup: 'rounded-[1.5rem] bg-slate-900 text-white',
                    confirmButton: 'rounded-xl font-black uppercase text-[10px] px-8 py-3 tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `batch_actions.php?action=reopen&id=${id}`;
                }
            })
        }

        function deleteBatch(id) {
            Swal.fire({
                title: 'Confirm Purge?',
                text: "CRITICAL: This will irreversibly subtract items from inventory levels.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Yes, Delete & Reverse',
                customClass: { 
                    popup: 'rounded-[1.5rem] bg-slate-900 border border-white/10 text-white',
                    confirmButton: 'rounded-xl font-black uppercase text-[10px] px-8 py-3 tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `batch_actions.php?action=delete&id=${id}`;
                }
            })
        }
    </script>
</body>
</html>
