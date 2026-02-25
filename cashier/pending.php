<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('cashier');

// Fetch pending drafts
$stmt = $pdo->prepare("
    SELECT s.*, 
           c.name as cust_name, 
           c.contact as cust_contact
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.status = 'pending' 
      AND s.user_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$_SESSION['id']]);
$drafts = $stmt->fetchAll(PDO::FETCH_ASSOC);

function numberFormat($val) {
    return number_format((float)$val, 2, '.', ',');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Drafts - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        .bg-main {
            background: url('../admin/public/admin_background.jpg');
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
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.08);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>

    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex flex-col sm:flex-row justify-between items-center max-w-7xl mx-auto gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="dashboard.php" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-base font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Pending Drafts
                    </h1>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Orders waiting for completion</p>
                </div>
            </div>
            <a href="pos.php" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-600/20 transition-all flex items-center justify-center gap-2 ring-4 ring-blue-600/10">
                + New Sale Terminal
            </a>
        </div>
    </nav>

    <main class="pt-10 pb-20 px-4 md:px-6 max-w-7xl mx-auto relative z-10 animate-fade">
        <?php if (empty($drafts)): ?>
            <div class="glass-card p-12 text-center shadow-lg border-2 border-white max-w-2xl mx-auto mt-20">
                <div class="w-16 h-16 bg-slate-100/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-700 uppercase tracking-tight mb-2">No Drafts Ready</h3>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Your drafting board is currently empty.</p>
                <a href="pos.php" class="inline-flex mt-8 bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-600/20 transition-all items-center gap-2">
                    Open Point of Sale
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($drafts as $d): ?>
                    <a href="pos.php?draft_id=<?php echo $d['id']; ?>" class="glass-card p-6 border-2 border-white shadow-[0_8px_30px_-12px_rgba(0,0,0,0.1)] hover:shadow-[0_15px_40px_-10px_rgba(37,99,235,0.15)] transition-all duration-300 hover:-translate-y-1.5 group block relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/5 to-indigo-500/5 rounded-bl-[4rem] -z-10 group-hover:from-blue-500/10 group-hover:scale-110 transition-all duration-500"></div>
                        
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="text-[8px] font-black uppercase tracking-[0.2em] text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1.5 rounded-lg mb-3 inline-block shadow-sm">Draft ID: <?php echo $d['id']; ?></span>
                                <h4 class="text-base font-black text-slate-800 uppercase tracking-tighter leading-tight group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($d['cust_name'] ?: 'Walk-in Customer'); ?></h4>
                                <?php if ($d['cust_name']): ?>
                                    <p class="text-[10px] text-slate-500 font-bold mt-1.5 tracking-wider uppercase"><?php echo htmlspecialchars($d['cust_contact']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-end border-t border-slate-100 pt-5 mt-auto">
                            <div>
                                <span class="text-[8px] font-black uppercase text-slate-400 tracking-widest block mb-1">Total Payable</span>
                                <p class="text-xl font-black text-slate-900 leading-none tracking-tighter group-hover:text-blue-600 transition-colors">Rs. <?php echo numberFormat($d['final_amount']); ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-blue-600/30">
                                <svg class="w-5 h-5 group-hover:rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-50 flex justify-between items-center text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <span><?php echo date('M d, Y', strtotime($d['created_at'])); ?></span>
                            <span><?php echo date('h:i A', strtotime($d['created_at'])); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
