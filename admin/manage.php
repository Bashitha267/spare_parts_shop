<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth(['admin', 'cashier']);
$is_admin = ($_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Oil Inventory - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        .suggest-dropdown {
            position: fixed;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 16px 40px -8px rgba(0,0,0,0.18);
            z-index: 999999;
            overflow: hidden;
            animation: dropIn 0.15s ease-out;
        }
        @keyframes dropIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
        .suggest-item {
            padding: 10px 18px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.12s;
        }
        .suggest-item:last-child { border-bottom: none; }
        .suggest-item:hover, .suggest-item.active { background: #eff6ff; }
        .suggest-item .s-name { font-weight: 800; font-size: 13px; color: #0f172a; }
        .suggest-item .s-meta { font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 1px; }
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
            border-radius: 1.5rem;
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
            cursor: pointer;
        }
        select option {
            background: white;
            color: #0f172a;
            padding: 10px;
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.25rem 1.5rem !important;
        }
        tr:nth-child(even) {
            background-color: rgba(241, 245, 249, 0.5);
        }
        td {
            padding: 1rem 1.5rem !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            color: #0f172a;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body class="bg-main min-h-screen relative">
    <div class="colorful-overlay"></div>
    
    <nav class="glass-nav sticky top-0 z-30">
        <div class="px-4 md:px-6 py-4 flex justify-between items-center mx-auto">
            <div class="flex items-center gap-4">
                <a href="<?php echo $is_admin ? 'dashboard.php' : '../cashier/dashboard.php'; ?>" class="p-2 hover:bg-blue-50 rounded-xl transition-all text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">Oil Registry</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="manage_handler.php?action=export_inventory&type=oil" class="bg-white text-emerald-600 px-6 py-2.5 rounded-xl text-xs font-black hover:bg-emerald-50 transition-all uppercase tracking-widest border-2 border-emerald-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
                <button onclick="openNewProductModal()" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2.5 rounded-xl text-xs font-black hover:shadow-lg hover:shadow-blue-500/30 transition-all uppercase tracking-widest border-2 border-white">+ Add Oil Product</button>
            </div>
        </div>
    </nav>

    <main class="p-8 mx-auto space-y-8 relative z-10">
        
        <!-- Inventory Valuation & Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-2">
            <div class="blue-gradient-card p-6 border-2 border-white rounded-[2rem] flex flex-col justify-center text-white">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-80">Oil Inventory Total</p>
                <h2 id="grand_inventory_value" class="text-2xl font-black tracking-tighter">Rs. 0.00</h2>
            </div>
            <div class="md:col-span-3 glass-card p-6 rounded-[2rem] flex flex-col lg:flex-row gap-6 items-center border-2 border-white">
                <div class="relative flex-grow w-full" id="searchOilWrapper">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInventory" autocomplete="off" class="block w-full pl-14 pr-6 py-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-slate-400 text-sm font-bold" placeholder="Find product by name or barcode...">
                </div>

                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <select id="oilTypeFilter" class="flex-grow lg:flex-none px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] outline-none transition-all hover:bg-slate-50 border-slate-200">
                        <option value="all">All Types</option>
                        <option value="can">Can</option>
                        <option value="loose">Loose</option>
                    </select>
                    <select id="statusFilter" class="flex-grow lg:flex-none px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] outline-none transition-all hover:bg-slate-50 border-slate-200">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                    <select id="sortFilter" class="flex-grow lg:flex-none px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] outline-none transition-all hover:bg-slate-50 border-slate-200">
                        <option value="high_value">Value: High to Low</option>
                        <option value="low_value">Value: Low to High</option>
                        <option value="name_asc">Alphabetical (A-Z)</option>
                    </select>
                    <button onclick="resetFilters()" title="Reset Filters" class="shrink-0 flex items-center gap-2 px-5 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="glass-card overflow-hidden border-4 border-blue-500/20">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em]">Product Details</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em]">Type</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right">Buying Price</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right">Labeled Price</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right text-blue-400">Estimated Selling Price</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right">Stock</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-right">Total Value</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] text-center"></th>
                    </tr>
                </thead>
                <tbody id="inventoryBody" class="divide-y divide-white/5 cursor-pointer">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
            
            <!-- Pagination Controls -->
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center" id="paginationControls">
                <!-- Loaded via JS -->
            </div>
        </div>
    </main>

    <!-- Quick Add Batch Modal (For existing items) -->
    <div id="quickBatchModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md shadow-2xl p-8 border border-white/50 animate-in fade-in zoom-in duration-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter" id="qb_title">Add New Oil Product</h3>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-1" id="qb_subtitle"></p>
                </div>
                <button onclick="closeModal('quickBatchModal')" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="quickBatchForm" class="space-y-4">
                <input type="hidden" name="product_id" id="qb_product_id">
                <input type="hidden" name="target_batch_id" id="qb_target_batch_id">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Buying Price</label>
                        <input type="number" step="0.01" name="b_price" id="qb_b_price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Labeled Price</label>
                        <input type="number" step="0.01" name="s_price" id="qb_s_price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1.5 ml-1">Estimated Selling Price</label>
                        <input type="number" step="0.01" name="est_price" id="qb_est_price" required class="w-full px-5 py-3 rounded-xl border-2 border-blue-100 font-black text-base text-blue-700 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Quantity to Add</label>
                        <input type="number" step="0.01" name="qty" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-black text-lg text-center focus:ring-2 focus:ring-blue-500 outline-none transition-all" value="1">
                    </div>
                </div>
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl font-black shadow-lg shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all mt-4 uppercase text-xs tracking-[0.2em]">Record Batch</button>
            </form>
        </div>
    </div>

    <!-- New Product Modal -->
    <div id="newProductModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-lg shadow-2xl p-8 border border-white/50">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Register New Oil Product</h3>
                <button onclick="closeModal('newProductModal')" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="newProductForm" class="space-y-4">
                <input type="hidden" name="type" value="oil">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Barcode / Scan Point</label>
                        <div class="flex gap-2">
                            <input type="text" name="barcode" id="np_barcode" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="flex-grow px-5 py-3 rounded-xl border border-slate-200 font-black text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Enter numbers only...">
                            <button type="button" onclick="generateBarcode('np_barcode')" class="px-4 py-3 bg-blue-50 text-blue-600 border-2 border-blue-200 rounded-xl text-[10px] font-black uppercase hover:bg-blue-100 transition-all">Generate</button>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Product Name</label>
                        <input type="text" name="name" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Brand, Grade, Volume...">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Oil Type</label>
                        <div class="flex gap-2">
                            <input type="hidden" name="oil_type" id="np_oil_type" value="can">
                            <button type="button" onclick="setOilType('can')" id="btn_type_can" class="flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-blue-600 bg-blue-600 text-white shadow-lg shadow-blue-200">Can (Sealed)</button>
                            <button type="button" onclick="setOilType('loose')" id="btn_type_loose" class="flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-slate-100 bg-slate-50 text-slate-400 hover:border-slate-200">Loose (Liter)</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Brand</label>
                        <input type="text" name="brand" class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Shell, Castrol...">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Vehicle Compatibility</label>
                        <input type="text" name="v_types" class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Toyota, Honda, Universal...">
                    </div>
                    <div class="pt-2 col-span-2 border-t border-slate-100 mt-2">
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-4">Initial Batch Entry</p>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Buying Price</label>
                        <input type="number" step="0.01" name="b_price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Labeled Price</label>
                        <input type="number" step="0.01" name="s_price" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1.5 ml-1">Est. Selling Price</label>
                        <input type="number" step="0.01" name="est_price" required class="w-full px-5 py-3 rounded-xl border-2 border-blue-100 font-black text-sm text-blue-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Initial Qty</label>
                        <input type="number" step="0.01" name="qty" required class="w-full px-5 py-3 rounded-xl border border-slate-200 font-black text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" value="1">
                    </div>
                </div>
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl font-black shadow-lg shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all mt-6 uppercase text-xs tracking-[0.2em]">Register & Add Stock</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-lg shadow-2xl p-10 border border-white/50">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Edit Registry</h3>
                <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="editForm" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="batch_id" id="edit_batch_id">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Barcode / Part Number</label>
                        <input type="text" name="barcode" id="edit_barcode" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all placeholder:text-slate-300 font-black bg-blue-50 border-2 border-blue-100 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Product Designation</label>
                        <input type="text" name="name" id="edit_name" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all placeholder:text-slate-300 font-bold border border-slate-200 text-sm" placeholder="Product Name">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Brand Identity</label>
                        <input type="text" name="brand" id="edit_brand" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all placeholder:text-slate-300 font-bold border border-slate-200 text-sm" placeholder="Brand Name">
                    </div>
                    <div>
                       <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Compatibility Profile</label>
                       <input type="text" name="v_types" id="edit_v_types" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all placeholder:text-slate-300 font-bold border border-slate-200 text-sm" placeholder="Universal / Specific">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Buying Price</label>
                        <input type="number" step="0.01" name="b_price" id="edit_b_price" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all font-bold border border-slate-100 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Labeled Price</label>
                        <input type="number" step="0.01" name="s_price" id="edit_s_price" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all font-bold border border-slate-100 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2 ml-1">Est. Selling Price</label>
                        <input type="number" step="0.01" name="est_price" id="edit_est_price" class="w-full px-6 py-3.5 rounded-xl border-2 border-blue-50 font-black text-blue-700 outline-none transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Current Stock (Qty)</label>
                        <input type="number" step="0.01" name="qty" id="edit_qty" class="w-full px-6 py-3.5 rounded-xl outline-none transition-all font-black bg-slate-50 border border-slate-100 focus:bg-white focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="flex flex-col gap-3 mt-10">
                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl font-black hover:shadow-lg transition-all uppercase text-sm tracking-widest">Commit Changes</button>
                    <button type="button" onclick="closeModal('editModal')" class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-200 transition-all uppercase text-xs tracking-widest">Dismiss</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let debounceTimer;
        let currentStatus = 'all';
        let currentOilType = 'all';
        let currentSort = 'high_value';
        const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            loadInventory(1);

            const oilSearchInput = document.getElementById('searchInventory');
            let oilSuggestTimer;
            let oilActiveIdx = -1;

            // Create dropdown appended to body to escape stacking context
            const oilDropdown = document.createElement('div');
            oilDropdown.className = 'suggest-dropdown';
            oilDropdown.style.display = 'none';
            document.body.appendChild(oilDropdown);

            function positionOilDropdown() {
                const rect = oilSearchInput.getBoundingClientRect();
                oilDropdown.style.left = rect.left + 'px';
                oilDropdown.style.top  = (rect.bottom + 6) + 'px';
                oilDropdown.style.width = rect.width + 'px';
            }

            oilSearchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                clearTimeout(oilSuggestTimer);
                const q = this.value.trim();
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    loadInventory(1, q, currentStatus);
                }, 300);
                if (q.length < 1) { oilDropdown.style.display = 'none'; return; }
                oilSuggestTimer = setTimeout(() => fetchOilSuggestions(q), 180);
            });

            oilSearchInput.addEventListener('keydown', function(e) {
                const items = oilDropdown.querySelectorAll('.suggest-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    oilActiveIdx = Math.min(oilActiveIdx + 1, items.length - 1);
                    items.forEach((el, i) => el.classList.toggle('active', i === oilActiveIdx));
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    oilActiveIdx = Math.max(oilActiveIdx - 1, 0);
                    items.forEach((el, i) => el.classList.toggle('active', i === oilActiveIdx));
                } else if (e.key === 'Enter' && oilActiveIdx >= 0) {
                    e.preventDefault();
                    items[oilActiveIdx].click();
                } else if (e.key === 'Escape') {
                    oilDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!document.getElementById('searchOilWrapper').contains(e.target) && !oilDropdown.contains(e.target)) {
                    oilDropdown.style.display = 'none';
                }
            });

            window.addEventListener('scroll', () => { if (oilDropdown.style.display !== 'none') positionOilDropdown(); }, true);
            window.addEventListener('resize', () => { if (oilDropdown.style.display !== 'none') positionOilDropdown(); });

            async function fetchOilSuggestions(q) {
                const res = await fetch(`manage_handler.php?action=search_suggest&q=${encodeURIComponent(q)}&type=oil`);
                const data = await res.json();
                oilDropdown.innerHTML = '';
                oilActiveIdx = -1;
                if (!data.suggestions.length) { oilDropdown.style.display = 'none'; return; }
                data.suggestions.forEach(s => {
                    const el = document.createElement('div');
                    el.className = 'suggest-item';
                    el.innerHTML = `<div class="s-name">${s.name}</div><div class="s-meta">${s.barcode}${s.brand ? ' &nbsp;·&nbsp; ' + s.brand : ''}</div>`;
                    el.onclick = () => {
                        oilSearchInput.value = s.name;
                        oilDropdown.style.display = 'none';
                        loadInventory(1, s.name, currentStatus);
                    };
                    oilDropdown.appendChild(el);
                });
                positionOilDropdown();
                oilDropdown.style.display = 'block';
            }

            document.getElementById('statusFilter').addEventListener('change', function() {
                currentStatus = this.value;
                loadInventory(1, document.getElementById('searchInventory').value, currentStatus);
            });

            document.getElementById('oilTypeFilter').addEventListener('change', function() {
                currentOilType = this.value;
                loadInventory(1, document.getElementById('searchInventory').value, currentStatus);
            });

            document.getElementById('sortFilter').addEventListener('change', function() {
                currentSort = this.value;
                loadInventory(1, document.getElementById('searchInventory').value, currentStatus);
            });

            window.resetFilters = function() {
                document.getElementById('searchInventory').value = '';
                document.getElementById('oilTypeFilter').value = 'all';
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('sortFilter').value = 'high_value';
                currentStatus = 'all';
                currentOilType = 'all';
                currentSort = 'high_value';
                oilDropdown.style.display = 'none';
                loadInventory(1, '', 'all');
            };

            document.getElementById('editForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_product');
                
                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.success) {
                    closeModal('editModal');
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Product updated!', showConfirmButton: false, timer: 1500 });
                }
            };

            document.getElementById('quickBatchForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'quick_add_stock');
                
                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.success) {
                    closeModal('quickBatchModal');
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire({ icon: 'success', title: 'Batch Added', text: 'Stock updated successfully!', timer: 1500 });
                    e.target.reset();
                }
            };

            document.getElementById('newProductForm').onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'quick_add_stock');
                
                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.success) {
                    closeModal('newProductModal');
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire({ icon: 'success', title: 'Registered', text: data.message, timer: 1500 });
                    e.target.reset();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            };
        });

        async function loadInventory(page, search = '', status = 'all') {
            currentPage = page;
            let url = `manage_handler.php?action=fetch_inventory&page=${page}&search=${search}&type=oil&status=${status}&sort=${currentSort}`;
            if(currentOilType !== 'all') url += `&oil_type=${currentOilType}`;
            const res = await fetch(url);
            const data = await res.json();
            
            const tbody = document.getElementById('inventoryBody');
            tbody.innerHTML = '';
            
            // Update Grand Total Value
            document.getElementById('grand_inventory_value').innerText = 'Rs. ' + parseFloat(data.grand_total_value || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if(data.products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-slate-400">No products found.</td></tr>';
                return;
            }

            data.products.forEach(p => {
                let qtyDisplay = '';
                if(p.type === 'oil') {
                    if(p.oil_type === 'can') qtyDisplay = `<span class="font-black text-blue-800">${Math.round(p.current_qty)}</span> <span class="text-blue-900 font-bold">Cans</span>`;
                    else qtyDisplay = `<span class="font-black text-amber-800">${p.current_qty}</span> <span class="text-amber-900 font-bold">Liters</span>`;
                } else {
                    qtyDisplay = `<span class="font-black text-blue-900">${Math.round(p.current_qty)}</span> <span class="text-blue-950 font-bold">Units</span>`;
                }

                let statusBadge = p.current_qty > 0 
                    ? '<span class="px-3 py-1 bg-emerald-500/15 text-emerald-800 border border-emerald-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest">In Stock</span>'
                    : '<span class=" py-1  text-red-600  rounded-lg text-[10px] font-black uppercase ">Finished</span>';

                let statusBtn = p.is_active == 1
                    ? `<button onclick="toggleStatus(${p.id}, 0); event.stopPropagation();" class="text-xs font-black text-emerald-800 hover:text-emerald-950 transition-colors uppercase tracking-tighter" title="Mark Out of Stock">Active</button>`
                    : `<button onclick="toggleStatus(${p.id}, 1); event.stopPropagation();" class="text-[10px] font-black transition-colors uppercase tracking-tight bg-red-400 text-white hover:bg-red-400 px-2 py-1 rounded-lg" title="Activate">Out of Stock</button>`;

                const rowClass = (p.current_qty <= 0 || p.is_active == 0) ? 'bg-red-300 hover:bg-red-400' : 'hover:bg-slate-50';

                const row = `
                    <tr class="${rowClass} transition-all group cursor-pointer" onclick="openQuickBatchModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        <td class="px-8 py-5">
                            <p class="font-black  text-sm tracking-tight text-red-950">${p.name}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[11px] font-mono text-blue-800 font-bold uppercase tracking-tight">${p.p_barcode || p.barcode}</span>
                              
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            ${p.type === 'oil' && p.oil_type === 'can' 
                                ? '<span class="px-3 py-1 bg-amber-500/15 text-amber-800 border border-amber-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest">Can</span>' 
                                : p.type === 'oil' && p.oil_type === 'loose'
                                    ? '<span class="px-3 py-1 bg-cyan-500/15 text-cyan-800 border border-cyan-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest">Loose</span>'
                                    : '<span class="text-slate-300">-</span>'}
                        </td>
                        <td class="px-2 py-5 text-right font-mono font-black text-slate-800">
                            ${parseFloat(p.buying_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-2 py-5 text-right font-mono font-black text-slate-800">
                             ${parseFloat(p.selling_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-2 py-5 text-right font-mono font-black text-blue-600">
                             ${parseFloat(p.estimated_selling_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-2 py-5 text-right font-mono text-blue-800">
                            ${qtyDisplay}
                        </td>
                        <td class="px-2 py-5 text-right font-mono font-black text-emerald-800">
                             ${parseFloat(p.total_value || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="px-3 py-5 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-8 py-5 text-center flex justify-center items-center gap-2" onclick="event.stopPropagation()">
                            <button onclick='showCompatibility(${JSON.stringify(p)})' class="p-2 text-blue-800 border border-transparent hover:border-blue-200 hover:bg-white rounded-lg transition-all" title="View Compatibility">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick="printBarcode('${p.p_barcode || p.barcode}', '${p.name.replace(/'/g, "\\'")}', '${p.brand ? p.brand.replace(/'/g, "\\'") : ''}')" class="p-2 text-slate-400 hover:text-blue-600 rounded-lg transition-all" title="Print Barcode">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                            </button>
                            ${isAdmin ? `
                            <button onclick='editProduct(${JSON.stringify(p)})' class="p-2 text-slate-400 hover:text-indigo-600 rounded-lg transition-all" title="Edit Info">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick='deleteBatch(${p.id})' class="p-2 text-slate-400 hover:text-rose-600 rounded-lg transition-all" title="Delete Batch">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            ` : ''}
                            ${statusBtn}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });


            renderPagination(data.pagination);
            currentPage = page;
        }

        function renderPagination(pg) {
            const container = document.getElementById('paginationControls');
            let html = '<div class="flex items-center gap-2">';
            
            // Previous Button (Icon only)
            if(pg.current_page > 1) {
                html += `<button onclick="loadInventory(${pg.current_page - 1}, document.getElementById('searchInventory').value, currentStatus)" class="p-2 bg-slate-100 border border-slate-200 rounded-lg hover:bg-slate-200 text-blue-800 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>`;
            }

            // Page Numbers
            for(let i = 1; i <= pg.total_pages; i++) {
                const activeClass = i === pg.current_page ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200';
                html += `<button onclick="loadInventory(${i}, document.getElementById('searchInventory').value, currentStatus)" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-bold transition-all ${activeClass}">${i}</button>`;
            }

            // Next Button (Icon only)
            if(pg.current_page < pg.total_pages) {
                html += `<button onclick="loadInventory(${pg.current_page + 1}, document.getElementById('searchInventory').value, currentStatus)" class="p-2 bg-slate-100 border border-slate-200 rounded-lg hover:bg-slate-200 text-blue-800 transition-all font-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`;
            }
            
            html += '</div>';
            container.innerHTML = html;
        }

        async function toggleStatus(id, newStatus) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', id);
            formData.append('status', newStatus);

            const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if(data.success) {
                loadInventory(currentPage, document.getElementById('searchInventory').value);
                const msg = newStatus === 1 ? 'Product Activated' : 'Product Deactivated';
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 1500 });
            }
        }

        function showCompatibility(p) {
            const types = p.vehicle_compatibility || 'Universal Application';
            Swal.fire({
                title: p.name,
                html: `
                    <div class="text-left mt-4 space-y-4">
                        <div class="p-5 bg-slate-50 border border-slate-100 rounded-3xl">
                            <div class="flex justify-between items-center mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recommended Vehicles</p>
                                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-500 shadow-sm">${p.type}</span>
                            </div>
                            <p class="text-base font-black text-slate-800 leading-tight mb-4">${types}</p>
                            <div class="flex items-center gap-2 py-2 px-3 bg-blue-50/50 rounded-xl w-fit">
                                <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Brand</span>
                                <span class="text-xs font-black text-blue-700">${p.brand || 'General'}</span>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    container: 'backdrop-blur-sm'
                }
            });
        }

        function openQuickBatchModal(p) {
            document.getElementById('qb_product_id').value = p.product_id;
            document.getElementById('qb_target_batch_id').value = p.id;
            document.getElementById('qb_title').innerText = p.name;
            document.getElementById('qb_subtitle').innerText = `Batch #${p.id} | Barcode: ${p.p_barcode || p.barcode}`;
            document.getElementById('qb_b_price').value = p.buying_price || '';
            document.getElementById('qb_s_price').value = p.selling_price || '';
            document.getElementById('qb_est_price').value = p.estimated_selling_price || '';
            document.getElementById('quickBatchModal').classList.remove('hidden');
        }

        function openNewProductModal() {
            document.getElementById('newProductModal').classList.remove('hidden');
            setOilType('can'); // Reset to default
            setTimeout(() => document.getElementById('np_barcode').focus(), 100);
        }

        function setOilType(type) {
            document.getElementById('np_oil_type').value = type;
            const canBtn = document.getElementById('btn_type_can');
            const looseBtn = document.getElementById('btn_type_loose');
            
            if (type === 'can') {
                canBtn.className = 'flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-blue-600 bg-blue-600 text-white shadow-lg shadow-blue-500/20';
                looseBtn.className = 'flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-slate-100 bg-slate-50 text-slate-400 hover:border-slate-200';
            } else {
                looseBtn.className = 'flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-emerald-600 bg-emerald-600 text-white shadow-lg shadow-emerald-500/20';
                canBtn.className = 'flex-1 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 border-slate-100 bg-slate-50 text-slate-400 hover:border-slate-200';
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function editProduct(p) {
            document.getElementById('edit_id').value = p.product_id;
            document.getElementById('edit_batch_id').value = p.id;
            document.getElementById('edit_barcode').value = p.p_barcode || p.barcode;
            document.getElementById('edit_name').value = p.name;
            document.getElementById('edit_brand').value = p.brand;
            document.getElementById('edit_v_types').value = p.vehicle_compatibility;
            
            // New fields
            document.getElementById('edit_b_price').value = p.buying_price || 0;
            document.getElementById('edit_s_price').value = p.selling_price || 0;
            document.getElementById('edit_est_price').value = p.estimated_selling_price || 0;
            const isLoose = p.type === 'oil' && p.oil_type === 'loose';
            document.getElementById('edit_qty').value = isLoose ? (p.current_qty || 0) : (Math.round(p.current_qty) || 0);
            
            document.getElementById('editModal').classList.remove('hidden');
        }

        function generateBarcode(inputId) {
            const timestamp = Date.now().toString().slice(-8);
            const random = Math.floor(Math.random() * 900 + 100).toString();
            const barcode = timestamp + random;
            document.getElementById(inputId).value = barcode;
        }

        async function deleteProduct(id) {
            const { isConfirmed } = await Swal.fire({
                title: 'Delete Product?',
                text: "This will remove the product registry. Only possible if stock is 0.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Yes, Delete',
                customClass: { popup: 'rounded-3xl' }
            });

            if (isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_product');
                formData.append('id', id);

                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if (data.success) {
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        }

        async function deleteBatch(batchId) {
            const { isConfirmed } = await Swal.fire({
                title: 'Delete this Specific Batch?',
                text: "This removes the stock/value from this exact batch. If there are past sales, it will be zeroed out instead of permanently deleted to protect history.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Yes, Remove Batch',
                customClass: { popup: 'rounded-3xl' }
            });

            if (isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_batch');
                formData.append('id', batchId);

                const res = await fetch('manage_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if (data.success) {
                    loadInventory(currentPage, document.getElementById('searchInventory').value, currentStatus);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        }



        function printBarcode(barcode, name, brand) {
            const printWindow = window.open('', '_blank', 'width=600,height=400');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Barcode - ${barcode}</title>
                        <style>
                            @page { size: auto; margin: 0; }
                            body { 
                                font-family: 'Inter', sans-serif; 
                                display: flex; 
                                flex-direction: column; 
                                align-items: center; 
                                justify-content: center; 
                                height: 100vh; 
                                margin: 0;
                                text-align: center;
                            }
                            .label-container {
                                border: 1px dashed #ccc;
                                padding: 20px;
                                border-radius: 8px;
                                display: inline-block;
                            }
                            .product-name { font-weight: bold; font-size: 14px; margin-bottom: 2px; }
                            .brand-name { font-size: 10px; color: #666; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
                            #barcode { max-width: 100%; }
                        </style>
                    </head>
                    <body>
                        <div class="label-container">
                            <div class="product-name">${name}</div>
                            <div class="brand-name">${brand || 'Generic'}</div>
                            <svg id="barcode"></svg>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                        <script>
                            JsBarcode("#barcode", "${barcode}", {
                                format: "CODE128",
                                width: 2,
                                height: 60,
                                displayValue: true,
                                fontSize: 14,
                                margin: 0
                            });
                            // Wait a bit for barcode to render
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 500);
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</body>
</html>
