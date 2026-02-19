<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);

// Logic for active invoice
$active_invoice = null;
if (isset($_SESSION['active_invoice_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND status = 'draft'");
    $stmt->execute([$_SESSION['active_invoice_id']]);
    $active_invoice = $stmt->fetch();
}

// Auto-create invoice if not exists
if (!$active_invoice) {
    $invoice_no = "STOCK-" . date('Ymd-His');
    $invoice_date = date('Y-m-d');
    $supplier = "Local Supply";

    try {
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, invoice_date, supplier_name, status, user_id) VALUES (?, ?, ?, 'draft', ?)");
        if ($stmt->execute([$invoice_no, $invoice_date, $supplier, $_SESSION['id']])) {
            $_SESSION['active_invoice_id'] = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND status = 'draft'");
            $stmt->execute([$_SESSION['active_invoice_id']]);
            $active_invoice = $stmt->fetch();
        }
    } catch (PDOException $e) {
        // Error handling
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Entry - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #334155;
        }
        .bg-main {
            background: url('public/admin_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            pointer-events: none; z-index: 0;
        }
        .glass-panel {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .blue-gradient-header {
            background: linear-gradient(to right, #2563eb, #1e40af);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .search-input {
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            color: #1f2937 !important;
        }
        .btn-blue {
            background: #2563eb;
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        .btn-blue:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 0.65rem 0.5rem !important;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        th:first-child { border-top-left-radius: 0.5rem; }
        th:last-child { border-top-right-radius: 0.5rem; border-right: none; }
        

        
        td {
            padding: 0.5rem 0.5rem !important;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
            font-size: 0.8rem;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.8);
        }

        .light-panel {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1rem;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-main py-2 lg:py-4 px-2 sm:px-4 lg:mx-10 h-screen lg:overflow-hidden flex flex-col relative text-slate-800">
    <!-- <div class="colorful-overlay"></div> -->

    <!-- Header -->
    <header class="glass-nav relative z-50">
        <div class="max-w-full px-3 sm:px-6 py-2 flex flex-wrap justify-between items-center mx-auto gap-2">
            <div class="flex items-center gap-2 sm:gap-4">
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : '../cashier/dashboard.php'; ?>" class="hidden lg:block p-2 hover:bg-slate-100 rounded-xl transition-all">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-xs sm:text-sm font-black text-blue-700 uppercase tracking-tighter">Add Items to Stock</h1>
                    <p class="text-[8px] sm:text-[9px] text-slate-700 font-bold uppercase tracking-widest mt-0.5"><?php echo $active_invoice['invoice_no']; ?> • <?php echo date('d M, Y', strtotime($active_invoice['invoice_date'])); ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-4">
                <button onclick="openMobileForm(); resetEntry('new');" class="lg:hidden btn-blue shadow-sm !rounded-lg !py-1.5 !px-3 !text-[9px] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Add New Item to Sto
                </button>
                <div class="h-8 w-px bg-slate-200 hidden sm:block mx-2"></div>
                <div class="text-right hidden sm:block">
                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest">Operator</p>
                    <p class="text-xs font-bold text-slate-900"><?php echo $_SESSION['full_name']; ?></p>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto lg:overflow-hidden grid grid-cols-1 lg:grid-cols-12 relative z-10 w-full bg-opacity-50">
        <!-- Mobile Form Modal Backdrop -->
        <div id="mobileFormBackdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[80] hidden lg:hidden" onclick="closeMobileForm()"></div>

        <!-- Left Section: Search & Add Form -->
        <div id="leftFormPanel" class="hidden lg:block lg:col-span-4 fixed lg:relative inset-0 z-[85] lg:z-auto bg-white lg:bg-white/60 backdrop-blur-xl border-r border-white/30 px-4 sm:px-5 py-4 overflow-y-auto space-y-3">
            <!-- Mobile Close Button (shown via JS when form opens as modal) -->
            <div id="mobileCloseBar" class="hidden items-center justify-between mb-3 sticky top-0 bg-white/90 backdrop-blur-sm py-2 -mx-4 px-4 z-10 border-b border-slate-100">
                <h3 class="text-sm font-black text-blue-700 uppercase tracking-tight">New Product</h3>
                <button onclick="closeMobileForm()" class="p-2.5 bg-slate-100 hover:bg-red-100 hover:text-red-600 rounded-xl transition-all text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Search Bar -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors">
                        <svg class="h-4 w-4 text-blue-600 group-focus-within:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="mainSearch" class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700" placeholder="Search product or scan barcode...">
                
                <!-- Results Dropdown -->
                <div id="searchResults" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 z-[60] hidden max-h-64 overflow-y-auto anim-pop"></div>
            </div>

            <!-- Form Area (Dynamic: Add Batch or Create Product) -->
            <div id="entryContainer" class="animate-fade">
                <div class="light-panel px-5 py-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div id="entryIcon" class="w-7 h-7 rounded-lg bg-blue-500 flex items-center justify-center text-white shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 id="entryTitle" class="text-sm font-black text-slate-900 uppercase tracking-tight">Add New Product</h3>
                    </div>

                    <form id="productEntryForm" class="space-y-3">
                        <input type="hidden" name="action" value="add_to_batch">
                        <input type="hidden" name="product_id" id="modal_product_id">
                        
                        <!-- Base Info Section -->
                        <div id="baseInfoArea" class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2">Product Category</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="p_type" value="oil" class="peer hidden" checked>
                                            <div class="py-4 px-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-md peer-checked:shadow-blue-500/10 transition-all font-extrabold text-xs text-slate-400 peer-checked:text-blue-600 uppercase tracking-wider flex flex-col items-center gap-2">
                                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C12 2 6 9 6 14a6 6 0 0 0 12 0c0-5-6-12-6-12zm0 18a4 4 0 0 1-4-4c0-.5.1-1 .3-1.5.1-.3.4-.5.7-.5s.5.3.4.6c-.1.4-.2.9-.2 1.4a2.8 2.8 0 0 0 2.8 2.8c.3 0 .6.3.6.6s-.3.6-.6.6z"/></svg>
                                                Oil & Lubricant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="p_type" value="spare_part" class="peer hidden">
                                            <div class="py-4 px-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-md peer-checked:shadow-emerald-500/10 transition-all font-extrabold text-xs text-slate-400 peer-checked:text-emerald-600 uppercase tracking-wider flex flex-col items-center gap-2">
                                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                                                Spare Part
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div id="oilOptions" class="col-span-2 grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="p_oil_type" value="can" class="peer hidden" checked>
                                        <div class="py-3.5 px-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md peer-checked:shadow-amber-500/10 transition-all font-extrabold text-xs text-slate-400 peer-checked:text-amber-600 uppercase tracking-wider flex flex-col items-center gap-1.5">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            Can / Pack
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="p_oil_type" value="loose" class="peer hidden">
                                        <div class="py-3.5 px-3 text-center border-2 border-slate-200 rounded-xl peer-checked:border-cyan-500 peer-checked:bg-cyan-50 peer-checked:shadow-md peer-checked:shadow-cyan-500/10 transition-all font-extrabold text-xs text-slate-400 peer-checked:text-cyan-600 uppercase tracking-wider flex flex-col items-center gap-1.5">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16.92C21 19.07 19.21 21 17 21H7c-2.21 0-4-1.93-4-4.08 0-1.62.67-2.54 1.95-3.77l.29-.27C6.82 11.42 9.5 8.5 12 5c2.5 3.5 5.18 6.42 6.76 7.88l.29.27C20.33 14.38 21 15.3 21 16.92z"/></svg>
                                            Loose Oil
                                        </div>
                                    </label>
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5">Product Name</label>
                                    <input type="text" name="p_name" id="modal_p_name" class="w-full px-4 py-3 bg-white/80 border border-slate-300 rounded-xl text-base font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="Enter full name...">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5">Barcode</label>
                                <div class="relative">
                                    <input type="text" name="p_barcode" id="modal_p_barcode" class="w-full pl-4 pr-11 py-3 bg-white/80 border border-slate-300 rounded-xl font-mono text-base text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="Scan or type barcode...">
                                    <button type="button" onclick="generateBarcode()" class="absolute right-2 top-2 p-2 text-blue-500 hover:bg-blue-500/10 rounded-lg transition-all" title="Generate QR Code">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zm0 11h7v7h-7v-7zM3 14h7v7H3v-7z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Batch Info -->
                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-200">
                            <div>
                                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5">Buying Price (Rs)</label>
                                <input type="number" step="0.01" name="b_price" required class="w-full px-4 py-3 bg-white/80 border border-slate-300 rounded-xl font-bold text-base text-blue-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5">Labeled Price (Rs)</label>
                                <input type="number" step="0.01" name="s_price" required class="w-full px-4 py-3 bg-white/80 border border-slate-300 rounded-xl font-bold text-base text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="0.00">
                            </div>
                            <div>
                                <label id="qtyLabel" class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5">Quantity</label>
                                <input type="number" step="0.01" name="qty" required class="w-full px-4 py-3 bg-white/80 border border-slate-300 rounded-xl font-bold text-base text-slate-900 text-center outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" value="1">
                            </div>
                            <div class="flex flex-col justify-end">
                                <button type="submit" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-extrabold shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all uppercase text-xs tracking-widest active:scale-95">Add to Inventory</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Section: Cart / Invoice Summary -->
        <!-- Mobile Floating Add Button -->
        <button onclick="openMobileForm()" class="fixed bottom-24 right-5 z-[70] lg:hidden w-14 h-14 bg-blue-600 text-white rounded-full shadow-xl shadow-blue-600/30 flex items-center justify-center active:scale-90 transition-all hover:bg-blue-700">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
        </button>

        <div class="col-span-1 lg:col-span-8 flex flex-col">
            <!-- Mobile Search Bar -->
            <div class="lg:hidden px-3 pt-3 pb-2 bg-white/60 backdrop-blur-xl border-b border-slate-100 relative z-20">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="mobileSearch" class="block w-full pl-9 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-700" placeholder="Search or scan barcode..." autocomplete="off">
                    <div id="mobileSearchResults" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 z-[999] hidden max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="flex-1 overflow-y-auto p-2 sm:p-4 bg-white/50 backdrop-blur-xl">
              <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[600px]">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-white">
                            <th class="pb-5 pl-4">Product Details</th>
                            <th class="pb-5">Category</th>
                            <th class="pb-5 text-right">Buying Price</th>
                            <th class="pb-5 text-right">Label Price</th>
                            <th class="pb-5 text-center">Qty</th>
                            <th class="pb-5 text-right pr-4">Subtotal</th>
                            <th class="pb-5"></th>
                        </tr>
                    </thead>
                    <tbody id="batchItemsBody" class="divide-y divide-slate-100">
                        <!-- Items populated via AJAX -->
                    </tbody>
                </table>
              </div>
                
                <div id="emptyState" class="flex flex-col items-center justify-center py-10 sm:py-16 opacity-30 animate-pulse hidden">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 mb-4 rounded-full bg-blue-600/10 flex items-center justify-center text-blue-600 border border-blue-600/20">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-center text-slate-100">Search & Add Items<br><span class="text-[9px] sm:text-[10px] tracking-[0.3em] text-blue-800">To start stock entry</span></p>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="px-3 sm:px-5 py-3 sm:py-4 bg-white/60 backdrop-blur-xl border-t border-white/30 mt-auto">
                <div class="flex flex-row justify-between items-center gap-3 sm:gap-4">
                    <div class="flex-1 flex flex-col items-start">
                        <p class="text-[8px] sm:text-[9px] font-bold text-slate-700 uppercase tracking-[0.15em] mb-0.5">Total Invoice Value</p>
                        <p id="header_total" class="text-xl sm:text-3xl font-black text-blue-700 tracking-tighter">Rs. 0.00</p>
                    </div>

                    <div>
                        <button onclick="saveAndComplete()" class="px-4 sm:px-8 py-2.5 sm:py-3 bg-blue-600 text-white rounded-xl font-bold text-[10px] sm:text-[11px] shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all uppercase tracking-widest active:scale-95">Complete Entry</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Quick Add Popup Modal -->
    <div id="quickAddModal" class="fixed top-0 left-0 w-screen h-screen z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeQuickModal()"></div>
        <div class="relative bg-white/70 backdrop-blur-xl rounded-2xl shadow-2xl w-full max-w-md mx-3 sm:mx-auto p-4 sm:p-6 animate-fade">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 flex items-center justify-center text-white shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <h3 id="quickModalTitle" class="text-sm font-black text-slate-900 uppercase tracking-tight">Add Stock</h3>
                        <p id="quickModalSub" class="text-[9px] text-slate-600 font-bold uppercase tracking-widest"></p>
                    </div>
                </div>
                <button onclick="closeQuickModal()" class="p-1.5 hover:bg-slate-100 rounded-lg transition-all text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="quickAddForm" class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-700 uppercase tracking-widest mb-1">Buying Price (Rs)</label>
                        <input type="number" step="0.01" id="quick_b_price" class="w-full px-3 py-2.5 bg-white/80 border border-slate-300 rounded-lg font-bold text-sm text-blue-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="0.00" data-quick-index="0">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-700 uppercase tracking-widest mb-1">Label Price (Rs)</label>
                        <input type="number" step="0.01" id="quick_s_price" class="w-full px-3 py-2.5 bg-white/80 border border-slate-300 rounded-lg font-bold text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" placeholder="0.00" data-quick-index="1">
                    </div>
                </div>
                <div>
                    <label id="quickQtyLabel" class="block text-[9px] font-bold text-slate-700 uppercase tracking-widest mb-1">Quantity</label>
                    <input type="number" step="0.01" id="quick_qty" class="w-full px-3 py-2.5 bg-white/80 border border-slate-300 rounded-lg font-bold text-sm text-slate-900 text-center outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all" value="1" data-quick-index="2">
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all uppercase text-[11px] tracking-widest active:scale-95 mt-2">Commit to Batch</button>
            </form>
        </div>
    </div>

    <script>
        // Mobile form modal functions
        function openMobileForm() {
            document.getElementById('leftFormPanel').classList.remove('hidden');
            document.getElementById('leftFormPanel').classList.add('flex', 'flex-col');
            document.getElementById('mobileFormBackdrop').classList.remove('hidden');
            document.getElementById('mobileCloseBar').classList.remove('hidden');
            document.getElementById('mobileCloseBar').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeMobileForm() {
            document.getElementById('leftFormPanel').classList.add('hidden');
            document.getElementById('leftFormPanel').classList.remove('flex', 'flex-col');
            document.getElementById('mobileFormBackdrop').classList.add('hidden');
            document.getElementById('mobileCloseBar').classList.add('hidden');
            document.getElementById('mobileCloseBar').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Reusable search setup for both desktop and mobile search bars
            function setupSearch(inputEl, resultsEl) {
                if (!inputEl) return;
                inputEl.setAttribute('autocomplete', 'off');

                inputEl.addEventListener('input', async function() {
                    const val = this.value.trim();
                    if (val.length < 2) { resultsEl.classList.add('hidden'); return; }
                    
                    const res = await fetch(`addItems_handler.php?action=search&query=${val}`);
                    const data = await res.json();
                    
                    if (data.products && data.products.length > 0) {
                        resultsEl.innerHTML = '';
                        data.products.forEach(p => {
                            const row = document.createElement('div');
                            row.className = 'px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 flex justify-between items-center group transition-all';
                            row.innerHTML = `
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">${p.name}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[8px] font-bold text-blue-600 uppercase tracking-widest">${p.barcode}</span>
                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">${p.brand || 'No Brand'}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[8px] font-bold px-2 py-0.5 bg-blue-50 border border-blue-100 rounded text-blue-600 uppercase tracking-widest">${p.type === 'oil' ? p.oil_type : 'Spare'}</span>
                                    <div class="w-6 h-6 rounded bg-blue-600/10 flex items-center justify-center text-blue-600 opacity-0 group-hover:opacity-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                            `;
                            row.onclick = () => { selectProduct(p); inputEl.value = ''; resultsEl.classList.add('hidden'); };
                            resultsEl.appendChild(row);
                        });
                        resultsEl.classList.remove('hidden');
                    } else {
                        resultsEl.innerHTML = `<div class="p-4 text-center text-slate-400 font-semibold text-sm italic">No matching products found.<br>Press Enter to setup as new.</div>`;
                        resultsEl.classList.remove('hidden');
                    }
                });

                inputEl.addEventListener('keypress', async function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const val = this.value.trim();
                        if (!val) return;

                        // Check dropdown first for already loaded results
                        const first = resultsEl.querySelector('.cursor-pointer');
                        if (first) {
                            first.click();
                            return;
                        }

                        // Otherwise do a fresh lookup for the barcode/name
                        try {
                            const res = await fetch(`addItems_handler.php?action=search&query=${val}`);
                            const data = await res.json();
                            if (data.products && data.products.length > 0) {
                                // Found existing product — open quick add modal
                                selectProduct(data.products[0]);
                            } else {
                                // No match — open create new product form
                                openMobileForm();
                                resetEntry('new', { barcode: val });
                            }
                        } catch(err) {
                            openMobileForm();
                            resetEntry('new', { barcode: val });
                        }

                        resultsEl.classList.add('hidden');
                        this.value = '';
                    }
                });

                // Close results on outside click
                document.addEventListener('click', (e) => {
                    if (!inputEl.contains(e.target) && !resultsEl.contains(e.target)) resultsEl.classList.add('hidden');
                });
            }

            // Setup desktop search
            setupSearch(document.getElementById('mainSearch'), document.getElementById('searchResults'));
            // Setup mobile search
            setupSearch(document.getElementById('mobileSearch'), document.getElementById('mobileSearchResults'));

            // Type Switching
            document.querySelectorAll('input[name="p_type"]').forEach(r => {
                r.addEventListener('change', function() {
                    document.getElementById('oilOptions').style.display = (this.value === 'oil') ? 'grid' : 'none';
                    updateQtyLabel();
                });
            });
            document.querySelectorAll('input[name="p_oil_type"]').forEach(r => {
                r.addEventListener('change', updateQtyLabel);
            });

            loadBatchItems();
        });

        function updateQtyLabel() {
            const type = document.querySelector('input[name="p_type"]:checked').value;
            const oilMode = document.querySelector('input[name="p_oil_type"]:checked').value;
            const label = document.getElementById('qtyLabel');
            if(type === 'oil') label.innerText = oilMode === 'can' ? 'Quantity (Cans)' : 'Quantity (Liters)';
            else label.innerText = 'Quantity (Units)';
        }

        let quickModalProduct = null;

        function selectProduct(p) {
            document.getElementById('mainSearch').value = '';
            document.getElementById('searchResults').classList.add('hidden');
            openQuickModal(p);
        }

        function openQuickModal(product) {
            quickModalProduct = product;
            document.getElementById('quickModalTitle').innerText = 'Add Stock: ' + product.name;
            
            let subText = product.barcode || '';
            if(product.type === 'oil') subText += ' • ' + (product.oil_type === 'can' ? 'Can/Pack' : 'Loose Oil');
            else subText += ' • Spare Part';
            document.getElementById('quickModalSub').innerText = subText;

            // Set qty label
            const qLabel = document.getElementById('quickQtyLabel');
            if(product.type === 'oil') qLabel.innerText = product.oil_type === 'can' ? 'Quantity (Cans)' : 'Quantity (Liters)';
            else qLabel.innerText = 'Quantity (Units)';

            // Reset inputs
            document.getElementById('quick_b_price').value = '';
            document.getElementById('quick_s_price').value = '';
            document.getElementById('quick_qty').value = '1';

            document.getElementById('quickAddModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('quick_b_price').focus(), 100);
        }

        function closeQuickModal() {
            document.getElementById('quickAddModal').classList.add('hidden');
            quickModalProduct = null;
        }

        // Enter key navigation in quick modal
        document.querySelectorAll('#quickAddForm input[data-quick-index]').forEach(input => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const idx = parseInt(this.dataset.quickIndex);
                    const next = document.querySelector(`#quickAddForm input[data-quick-index="${idx + 1}"]`);
                    if (next) {
                        next.focus();
                        next.select();
                    } else {
                        // Last input, submit the form
                        document.getElementById('quickAddForm').requestSubmit();
                    }
                }
            });
        });

        // Quick modal form submit
        document.getElementById('quickAddForm').onsubmit = async (e) => {
            e.preventDefault();
            if (!quickModalProduct) return;

            const bPrice = document.getElementById('quick_b_price').value;
            const sPrice = document.getElementById('quick_s_price').value;
            const qty = document.getElementById('quick_qty').value;

            if (!bPrice || !sPrice || !qty) {
                Swal.fire('Missing Fields', 'Please fill in all price and quantity fields.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add_to_batch');
            formData.append('product_id', quickModalProduct.id);
            formData.append('p_type', quickModalProduct.type);
            if(quickModalProduct.type === 'oil') formData.append('p_oil_type', quickModalProduct.oil_type);
            formData.append('b_price', bPrice);
            formData.append('s_price', sPrice);
            formData.append('qty', qty);

            const res = await fetch('addItems_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, icon: 'success', title: 'Item added successfully'
                });
                closeQuickModal();
                loadBatchItems();
                document.getElementById('header_total').innerText = 'Rs. ' + data.new_total;
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        };

        function resetEntry(mode, product = null) {
            const form = document.getElementById('productEntryForm');
            form.reset();
            
            const title = document.getElementById('entryTitle');
            const icon = document.getElementById('entryIcon');
            const baseArea = document.getElementById('baseInfoArea');
            const pId = document.getElementById('modal_product_id');

            if(mode === 'existing') {
                title.innerText = "Add Stock: " + product.name;
                icon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>`;
                icon.className = "w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm";
                pId.value = product.id;
                baseArea.classList.add('hidden');
                
                // Set hidden type for correct logic
                document.querySelector(`input[name="p_type"][value="${product.type}"]`).checked = true;
                if(product.type === 'oil') document.querySelector(`input[name="p_oil_type"][value="${product.oil_type}"]`).checked = true;
            } else {
                title.innerText = "Create New Product";
                icon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>`;
                icon.className = "w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm";
                pId.value = '';
                baseArea.classList.remove('hidden');
                if(product?.barcode) document.getElementById('modal_p_barcode').value = product.barcode;
            }
            updateQtyLabel();
        }

        function generateBarcode() {
            document.getElementById('modal_p_barcode').value = Math.floor(100000000000 + Math.random() * 900000000000);
        }

        const entryForm = document.getElementById('productEntryForm');
        entryForm.onsubmit = async (e) => {
            e.preventDefault();
            // Validate p_name when creating new product
            const pName = document.getElementById('modal_p_name');
            const baseArea = document.getElementById('baseInfoArea');
            if (!baseArea.classList.contains('hidden') && !pName.value.trim()) {
                Swal.fire('Missing Fields', 'Please enter a product name.', 'warning');
                pName.focus();
                return;
            }
            const formData = new FormData(entryForm);
            const res = await fetch('addItems_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, icon: 'success', title: 'Item added successfully'
                });
                resetEntry('new');
                closeMobileForm();
                loadBatchItems();
                document.getElementById('header_total').innerText = 'Rs. ' + data.new_total;
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        };

        async function loadBatchItems() {
            const res = await fetch('addItems_handler.php?action=load_items');
            const data = await res.json();
            const body = document.getElementById('batchItemsBody');
            const empty = document.getElementById('emptyState');
            body.innerHTML = '';
            
            if(data.items.length === 0) empty.classList.remove('hidden');
            else empty.classList.add('hidden');

            let totalVal = 0;

            data.items.forEach(item => {
                const sub = item.original_qty * item.buying_price;
                totalVal += sub;

                let catBadge = '';
                if(item.type === 'oil') {
                    const color = item.oil_type === 'can' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-cyan-100 text-cyan-700 border-cyan-200';
                    catBadge = `<div class="flex flex-col items-center gap-1">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Oil</span>
                        <span class="px-3 py-1 ${color} border rounded-lg text-[10px] font-black uppercase tracking-wider">${item.oil_type === 'can' ? 'Can' : 'Loose'}</span>
                    </div>`;
                } else {
                    catBadge = `<div class="flex flex-col items-center gap-1">
                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Spare Part</span>
                        <span class="px-3 py-1 bg-emerald-100 border border-emerald-200 rounded-lg text-[10px] font-black uppercase tracking-wider text-emerald-700">Unit</span>
                    </div>`;
                }

                body.innerHTML += `
                    <tr class="group bg-white hover:bg-slate-50 transition-all">
                        <td class="px-4 py-4 text-left">
                            <p class="text-sm font-bold text-slate-900">${item.name}</p>
                            <p class="text-[9px] text-slate-500 font-mono mt-1">${item.barcode}</p>
                        </td>
                        <td class="py-4">${catBadge}</td>
                        <td class="py-4 text-right font-bold text-base text-slate-900">Rs. ${numberFormat(item.buying_price)}</td>
                        <td class="py-4 text-right font-bold text-base text-slate-900">Rs. ${numberFormat(item.selling_price)}</td>
                        <td class="py-4 text-center font-extrabold text-blue-700 text-base">${item.original_qty}</td>
                        <td class="py-4 text-right font-bold text-slate-900 text-base tracking-tight pr-4">Rs. ${numberFormat(sub)}</td>
                        <td class="py-4">
                            <button onclick="removeItem(${item.id})" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-100 rounded-lg transition-all hover:scale-110 hover:shadow-md active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        async function removeItem(batchId) {
            const res = await fetch(`addItems_handler.php?action=remove_item&id=${batchId}`);
            const data = await res.json();
            if (data.success) {
                loadBatchItems();
                document.getElementById('header_total').innerText = 'Rs. ' + data.new_total;
            }
        }

        async function saveAndComplete() {
            const res = await fetch('addItems_handler.php?action=complete_invoice');
            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    title: 'Stock Completed', text: 'Inventory has been updated successfully!', icon: 'success', background: '#fff', color: '#1f2937', confirmButtonColor: '#2563eb'
                }).then(() => { resetEntry('new'); loadBatchItems(); document.getElementById('header_total').innerText = 'Rs. 0.00'; });
            }
        }

        function numberFormat(val) { return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    </script>
</body>
</html>
