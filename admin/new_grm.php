<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
check_auth('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Goods Receipt - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; font-size: 12px; }
        .bg-main {
            background: url('public/admin_background.jpg');
            background-size: cover; background-position: center;
            background-attachment: fixed; min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background:
                radial-gradient(circle at 0% 0%, rgba(37,99,235,0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139,92,246,0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(236,72,153,0.05) 0%, transparent 50%);
            pointer-events: none; z-index: 0;
        }
        .glass-card {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px -4px rgba(0,0,0,0.08);
        }
        .glass-nav {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        input, select, textarea {
            background: white !important; border: 1px solid #e2e8f0 !important;
            color: #0f172a !important; outline: none !important;
        }
        .field-label { display: block; font-size: 8.5px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 4px; margin-left: 2px; }
        .field-input { width: 100%; padding: 6px 10px; border-radius: 8px; border: 1.5px solid #e2e8f0 !important; font-weight: 700; font-size: 11px; transition: border-color 0.15s, box-shadow 0.15s; }
        .field-input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .field-input.highlight { border-color: #3b82f6 !important; background: #eff6ff !important; color: #1d4ed8 !important; font-weight: 900; }

        /* Suggest dropdown */
        .grm-dropdown {
            position: absolute; background: white;
            top: 100%; margin-top: 4px;
            border: 1.5px solid #e2e8f0; border-radius: 1rem;
            box-shadow: 0 16px 40px -8px rgba(0,0,0,0.18);
            z-index: 999999; overflow: hidden;
            animation: dropIn 0.13s ease-out;
        }
        @keyframes dropIn { from{opacity:0;transform:translateY(-5px)} to{opacity:1;transform:translateY(0)} }
        .grm-drop-item {
            padding: 7px 13px; cursor: pointer;
            border-bottom: 1px solid #f1f5f9; transition: background 0.1s;
        }
        .grm-drop-item:last-child { border-bottom: none; }
        .grm-drop-item:hover, .grm-drop-item.active { background: #eff6ff; }
        .grm-drop-item .d-name { font-weight: 800; font-size: 11px; color: #0f172a; }
        .grm-drop-item .d-meta { font-size: 9px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 1px; }

        /* Table styles */
        th {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white !important; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 13px 14px !important; font-size: 9px;
        }
        td { padding: 13px 14px !important; border-bottom: 1px solid rgba(226,232,240,0.5); }
        .add-item-btn { width: 100%; padding: 10px; border: 2px dashed #bfdbfe; border-radius: 12px; background: #f0f9ff; color: #2563eb; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .add-item-btn:hover { background: #2563eb; color: white; border-color: #2563eb; }

        /* Total section */
        .total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; }
        .total-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; }
        .total-val { font-size: 13px; font-weight: 900; color: #0f172a; }
        .grand-total-row { border-top: 2px solid #e2e8f0; margin-top: 4px; padding-top: 8px; }
        .grand-total-row .total-val { font-size: 18px; color: #1d4ed8; }
        .modal-section-title {
            font-size: 8.5px; font-weight: 900; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.18em; margin-bottom: 0.75rem;
            display: flex; align-items: center; gap: 8px;
        }
        .modal-section-title::after { content:''; flex:1; height:1px; background:#e2e8f0; }

        /* Modal Overlay & Modal Details */
        .grm-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px); z-index: 10000;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 40px 16px; overflow-y: auto;
        }
        .grm-modal {
            background: white; border-radius: 1.5rem;
            box-shadow: 0 20px 60px -15px rgba(0,0,0,0.3);
            width: 100%; max-width: 440px; overflow: hidden;
            animation: modalIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
            margin: auto;
        }
        @keyframes modalIn { from{opacity:0;transform:scale(0.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
<div class="colorful-overlay"></div>

<!-- NAV -->
<nav class="glass-nav sticky top-0 z-40">
    <div class="px-4 md:px-6 py-3.5 flex flex-col sm:flex-row justify-between items-center max-w-[1600px] mx-auto gap-3">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="grm.php" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-black text-slate-900 tracking-tight uppercase">New Goods Receipt Invoice</h1>
                <p class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mt-0.5">Register stock purchase receipt</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <button onclick="confirmDiscard()" class="px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 rounded-xl font-black uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Discard / Delete
            </button>
            <button onclick="confirmSave()" id="saveGrmBtn" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black uppercase text-[10px] tracking-wider transition-all flex items-center gap-2 shadow-md shadow-emerald-500/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Save Receipt
            </button>
        </div>
    </div>
</nav>

<main class="p-4 md:p-8 max-w-[1600px] mx-auto space-y-6 relative z-10">
    <div class="glass-card p-6 md:p-8 border-2 border-white shadow-xl">
        <div class="space-y-6">

            <!-- SECTION 1: Supplier Details -->
            <div>
                <div class="modal-section-title">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Supplier Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3 relative" id="supplierSearchWrap">
                        <label class="field-label">Supplier Name *</label>
                        <input type="text" id="supplierInput" placeholder="Search or add new..."
                            class="field-input" autocomplete="off" oninput="supplierInputChange()">
                        <input type="hidden" id="supplierId">
                        <div id="supplierDropdown" class="grm-dropdown" style="display:none; left: 0; width: 100%;"></div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label">Contact Number</label>
                        <input type="text" id="supplierContact" placeholder="07X XXX XXXX" class="field-input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label">Invoice Date *</label>
                        <input type="date" id="invoiceDate" class="field-input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label">Invoice Number</label>
                        <input type="text" id="invoiceNo" class="field-input highlight" placeholder="Auto-generate if blank">
                    </div>
                    <div class="md:col-span-3">
                        <label class="field-label">Address</label>
                        <input type="text" id="supplierAddress" placeholder="Supplier address (optional)" class="field-input">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Items (Table Layout) -->
            <div>
                <div class="modal-section-title">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Items Grid
                </div>
                <div class="overflow-x-auto lg:overflow-visible border border-slate-200 rounded-2xl bg-white shadow-sm" style="min-height: 350px;">
                    <table class="w-full text-left min-w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center" style="width: 10%;">Type</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center" style="width: 10%;">Sub-cat</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest" style="width: 30%;">Product Name / Search</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right" style="width: 7%;">Qty</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right" style="width: 9%;">Buying Price</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right" style="width: 9%;">Labeled Price</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right" style="width: 9%;">Est. Price</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest" style="width: 10%;">Expire Date</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right" style="width: 10%;">Line Total</th>
                                <th class="p-3 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center" style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>
                </div>
                
                <button type="button" class="add-item-btn mt-3" onclick="addItemRow()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Add Item Row
                </button>
            </div>

            <!-- SECTION 3: Totals (Single Row) -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="total-label !mb-0 text-slate-500">Subtotal:</span>
                        <span class="total-val text-sm text-slate-800" id="totalSubtotal">Rs. 0.00</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="total-label !mb-0 text-slate-500">Discount:</span>
                        <div class="flex items-center gap-2">
                            <input type="number" id="discountAmt" min="0" step="0.01" placeholder="0"
                                class="w-24 px-3 py-1.5 rounded-xl text-xs font-bold text-right" oninput="recalcTotals()">
                            <div class="flex rounded-xl overflow-hidden border border-slate-200">
                                <button id="discRsBtn" onclick="setDiscountType('rs')" class="px-2.5 py-1.5 text-[9px] font-black uppercase transition-all bg-blue-600 text-white">Rs</button>
                                <button id="discPctBtn" onclick="setDiscountType('pct')" class="px-2.5 py-1.5 text-[9px] font-black uppercase transition-all bg-white text-slate-400">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 border-l border-slate-200 pl-6">
                        <span class="total-label !mb-0 text-blue-800" style="font-size:11px;">Grand Total:</span>
                        <span class="total-val text-xl text-blue-700" id="totalGrand" style="font-size:22px;">Rs. 0.00</span>
                    </div>
                </div>
            </div>



        </div>
    </div>
</main>

<!-- ===================== NEW PRODUCT CONFIG POPUP MODAL ===================== -->
<div id="newProductModal" class="grm-modal-overlay" style="display:none;" onclick="closeNewProductModalOverlay(event)">
    <div class="grm-modal" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="modal-header">
            <div>
                <h2 class="text-[12px] font-black text-white uppercase tracking-tight">New Product Registration</h2>
                <p class="text-blue-200 text-[8px] font-black uppercase tracking-widest mt-0.5" id="newProdModalTitle">Configure Extra Information</p>
            </div>
            <button onclick="closeNewProductDetailsModal()" class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="modalActiveRowId">
            <div>
                <label class="field-label">Barcode / Part Number *</label>
                <div class="flex gap-2">
                    <input type="text" id="modalBarcode" placeholder="Enter barcode or generate..." class="field-input flex-1">
                    <button type="button" onclick="genModalBarcode()" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-[9px] font-black uppercase hover:bg-blue-200 transition-all whitespace-nowrap">Gen</button>
                </div>
            </div>
            <div>
                <label class="field-label">Brand Name</label>
                <input type="text" id="modalBrand" placeholder="e.g. Shell, Mobil, Toyota (optional)" class="field-input">
            </div>
            <div>
                <label class="field-label">Vehicle Compatibility</label>
                <input type="text" id="modalCompat" placeholder="e.g. Civic, Axio, Universal (optional)" class="field-input">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeNewProductDetailsModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold uppercase text-[9px]">Cancel</button>
                <button type="button" onclick="applyModalProductDetails()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black uppercase text-[9px] shadow-md shadow-blue-500/20">Apply Details</button>
            </div>
        </div>
    </div>
</div>
<!-- ========================================================================= -->

<script>
let discountType = 'rs';
let rowCounter = 0;
let supplierSuggestTimer;

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('invoiceDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('invoiceNo').value = generateInvoiceNo();
    recalcTotals();
    addItemRow();
    setTimeout(() => document.getElementById('supplierInput').focus(), 150);
});

function generateInvoiceNo() {
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    return `GRM-${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}-${Math.floor(Math.random()*900)+100}`;
}

// ─── SUPPLIER AUTOCOMPLETE ───────────────────────────
function supplierInputChange() {
    clearTimeout(supplierSuggestTimer);
    document.getElementById('supplierId').value = '';
    const q = document.getElementById('supplierInput').value.trim();
    if (q.length < 1) { hideSupplierDropdown(); return; }
    supplierSuggestTimer = setTimeout(() => fetchSupplierSuggestions(q), 200);
}

async function fetchSupplierSuggestions(q) {
    const res = await fetch(`grm_handler.php?action=search_supplier&q=${encodeURIComponent(q)}`);
    const d = await res.json();
    const dd = document.getElementById('supplierDropdown');
    dd.innerHTML = '';
    const inp = document.getElementById('supplierInput');
    const rect = inp.getBoundingClientRect();
    dd.style.width = rect.width + 'px';

    let exactMatch = null;
    if ((d.suppliers||[]).length > 0) {
        d.suppliers.forEach(s => {
            if (s.name.trim().toLowerCase() === q.trim().toLowerCase()) {
                exactMatch = s;
            }
            const el = document.createElement('div');
            el.className = 'grm-drop-item';
            el.innerHTML = `<div class="d-name">${escHtml(s.name)}</div><div class="d-meta">${escHtml(s.contact||'')}${s.address?' · '+s.address:''}</div>`;
            el.onclick = () => selectSupplier(s);
            dd.appendChild(el);
        });
    } else {
        const el = document.createElement('div');
        el.className = 'grm-drop-item';
        el.innerHTML = `<div class="d-meta" style="color:#10b981;font-weight:900;">✦ New supplier — will be auto-registered on save</div>`;
        el.style.cursor = 'pointer';
        el.onclick = () => hideSupplierDropdown();
        dd.appendChild(el);
    }
    
    if (exactMatch) {
        document.getElementById('supplierId').value = exactMatch.id;
        document.getElementById('supplierContact').value = exactMatch.contact || '';
        document.getElementById('supplierAddress').value = exactMatch.address || '';
    }
    
    dd.style.display = 'block';
}

function selectSupplier(s) {
    document.getElementById('supplierInput').value = s.name;
    document.getElementById('supplierId').value = s.id;
    document.getElementById('supplierContact').value = s.contact || '';
    document.getElementById('supplierAddress').value = s.address || '';
    hideSupplierDropdown();
}
function hideSupplierDropdown() { document.getElementById('supplierDropdown').style.display = 'none'; }

document.addEventListener('click', e => {
    const inp = document.getElementById('supplierInput');
    const dd = document.getElementById('supplierDropdown');
    if (inp && dd && e.target !== inp && !dd.contains(e.target)) {
        hideSupplierDropdown();
    }
});

// ─── ITEM ROWS (TABLE ROW INJECTION) ─────────────────
function addItemRow() {
    rowCounter++;
    const id = rowCounter;
    const container = document.getElementById('itemsContainer');
    const tr = document.createElement('tr');
    tr.className = 'item-row border-b border-slate-100 align-middle';
    tr.id = `item-row-${id}`;
    
    // Initial state: single colspan cell asking for type selection
    tr.innerHTML = `
        <td colspan="10" class="p-3 text-center bg-slate-50/50">
            <div class="flex items-center justify-center gap-3 py-1">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Select Item Type *</span>
                <button type="button" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm transition-all" onclick="buildItemRow(${id}, 'oil')">🛢 Oil</button>
                <button type="button" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm transition-all" onclick="buildItemRow(${id}, 'spare')">🔧 Spare Part</button>
            </div>
        </td>
    `;
    container.appendChild(tr);
}

function buildItemRow(id, type) {
    const tr = document.getElementById(`item-row-${id}`);
    if (!tr) return;
    
    tr.innerHTML = `
        <!-- Col 1: Type Badge -->
        <td class="p-2 text-center">
            <div id="type-badge-container-${id}">
                <div id="type-badge-${id}" class="text-[9px] font-black uppercase text-center py-1 rounded"></div>
            </div>
            <input type="hidden" id="item-type-${id}" value="${type}">
        </td>

        <!-- Col 2: Sub-category -->
        <td class="p-2">
            <div id="oil-subtype-col-${id}" style="display:none;">
                <select id="item-oil-type-${id}" class="field-input !py-1 !px-2 !text-xs" onchange="setOilSubtype(${id}, this.value)">
                    <option value="can">Can</option>
                    <option value="loose">Loose</option>
                </select>
            </div>
        </td>

        <!-- Col 3: Search / Product Name -->
        <td class="p-2 relative">
            <div id="product-search-wrapper-${id}">
                <input type="text" id="product-search-${id}" placeholder="Type name or barcode to search..." autocomplete="off"
                    class="field-input !py-1 !px-2 !text-[11px]" oninput="productSearchInput(${id})" onfocus="productSearchInput(${id})" onkeydown="searchKeydown(${id}, event)">
                <input type="hidden" id="item-product-id-${id}" value="">
                <div id="product-dropdown-${id}" class="grm-dropdown" style="display:none; left: 0; width: 100%;"></div>
                
                <button type="button" onclick="toggleNewProduct(${id})" id="new-prod-toggle-${id}" class="text-[8px] font-black text-blue-600 uppercase mt-1 hover:underline block">
                    ✦ Create New Instead
                </button>
            </div>
            
            <div id="new-product-name-wrapper-${id}" style="display:none;" class="space-y-1">
                <input type="text" id="item-name-${id}" placeholder="Enter new product name..." class="field-input !py-1 !px-2 !text-[11px]" oninput="markNewProduct(${id})">
                <div class="flex items-center gap-2">
                    <button type="button" id="config-btn-${id}" onclick="openNewProductDetailsModal(${id})" class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-[8.5px] rounded font-black uppercase transition-all">
                        ⚙ Configure Details
                    </button>
                    <button type="button" onclick="toggleNewProduct(${id})" class="text-[8.5px] font-black text-red-500 uppercase hover:underline">
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Hidden Meta Fields for newly registered products -->
            <input type="hidden" id="item-is-new-${id}" value="0">
            <input type="hidden" id="item-barcode-${id}" value="">
            <input type="hidden" id="item-brand-${id}" value="">
            <input type="hidden" id="item-compat-${id}" value="">
        </td>

        <!-- Col 4: Qty -->
        <td class="p-2">
            <input type="number" id="item-qty-${id}" step="0.01" min="0.01" placeholder="0" class="field-input !py-1 !px-2 !text-[11px] text-right" oninput="recalcRow(${id});recalcTotals()">
        </td>

        <!-- Col 5: Buying Price -->
        <td class="p-2">
            <input type="number" id="item-bprice-${id}" step="0.01" min="0" placeholder="0.00" class="field-input !py-1 !px-2 !text-[11px] text-right" oninput="recalcRow(${id});recalcTotals()">
        </td>

        <!-- Col 6: Labeled Price -->
        <td class="p-2">
            <input type="number" id="item-sprice-${id}" step="0.01" min="0" placeholder="0.00" class="field-input !py-1 !px-2 !text-[11px] text-right">
        </td>

        <!-- Col 7: Est. Price -->
        <td class="p-2">
            <input type="number" id="item-estprice-${id}" step="0.01" min="0" placeholder="same" class="field-input highlight !py-1 !px-2 !text-[11px] text-right">
        </td>

        <!-- Col 8: Expire Date -->
        <td class="p-2">
            <input type="date" id="item-expire-${id}" class="field-input !py-1 !px-1.5 !text-[11px]">
        </td>

        <!-- Col 9: Line Total -->
        <td class="p-2 text-right">
            <div id="item-linetotal-${id}" class="font-black text-xs text-blue-800">Rs. 0.00</div>
        </td>

        <!-- Col 10: Delete Button -->
        <td class="p-2 text-center">
            <button type="button" class="text-red-400 hover:text-red-600 transition-colors" onclick="removeItemRow(${id})">
                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>
    `;

    const badge = document.getElementById(`type-badge-${id}`);
    const subtypeCol = document.getElementById(`oil-subtype-col-${id}`);

    if (type === 'oil') {
        badge.textContent = 'Oil';
        badge.className = 'w-full text-[9px] font-black uppercase text-center py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded';
        subtypeCol.style.display = '';
    } else if (type === 'spare') {
        badge.textContent = 'Spare';
        badge.className = 'w-full text-[9px] font-black uppercase text-center py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded';
        subtypeCol.style.display = 'none';
    }
}

function removeItemRow(id) {
    const allRows = document.querySelectorAll('.item-row');
    if (allRows.length <= 1) {
        Swal.fire({toast:true,position:'top-end',icon:'warning',title:'At least one item required',showConfirmButton:false,timer:1800});
        return;
    }
    document.getElementById(`item-row-${id}`).remove();
    recalcTotals();
}

function setOilSubtype(id, subtype) {
    // Handled automatically by native select
}

let productSearchTimers = {};
function productSearchInput(id) {
    clearTimeout(productSearchTimers[id]);
    const q = document.getElementById(`product-search-${id}`).value.trim();
    document.getElementById(`item-product-id-${id}`).value = '';
    if (q.length < 1) { hideProductDropdown(id); return; }
    productSearchTimers[id] = setTimeout(() => fetchProductSuggestions(id, q), 200);
}

async function fetchProductSuggestions(id, q) {
    const type = document.getElementById(`item-type-${id}`).value;
    const apiType = type === 'oil' ? 'oil' : 'spare_part';
    const res = await fetch(`grm_handler.php?action=search_product&q=${encodeURIComponent(q)}&type=${apiType}`);
    const d = await res.json();
    const dd = document.getElementById(`product-dropdown-${id}`);
    dd.innerHTML = '';
    const inp = document.getElementById(`product-search-${id}`);
    const rect = inp.getBoundingClientRect();
    dd.style.width = rect.width + 'px';

    if ((d.suggestions||[]).length > 0) {
        d.suggestions.forEach(s => {
            const el = document.createElement('div');
            el.className = 'grm-drop-item';
            const extra = s.type==='oil' && s.oil_type!=='none' ? ` (${s.oil_type})` : '';
            el.innerHTML = `<div class="d-name">${escHtml(s.name)}${escHtml(extra)}</div><div class="d-meta">${escHtml(s.barcode)}${s.brand?' · '+s.brand:''}</div>`;
            el.onclick = () => selectProduct(id, s);
            dd.appendChild(el);
        });
    } else {
        const el = document.createElement('div');
        el.className = 'grm-drop-item';
        el.innerHTML = `<div class="d-meta" style="color:#2563eb;font-weight:900;text-transform:uppercase;">✦ Register "${escHtml(q)}" as new product</div>`;
        el.style.cursor = 'pointer';
        el.onclick = () => {
            toggleNewProduct(id);
            document.getElementById(`item-name-${id}`).value = q;
            
            let barcodeVal = '';
            if (/^[0-9a-zA-Z]{5,}$/.test(q)) {
                barcodeVal = q;
            }
            
            openNewProductDetailsModal(id);
            if (barcodeVal) {
                document.getElementById('modalBarcode').value = barcodeVal;
            }
            hideProductDropdown(id);
        };
        dd.appendChild(el);
    }
    dd.style.display = 'block';
}

function searchKeydown(id, e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const dd = document.getElementById(`product-dropdown-${id}`);
        if (dd && dd.style.display !== 'none') {
            const items = dd.querySelectorAll('.grm-drop-item');
            if (items.length > 0) {
                items[0].click();
            }
        }
    }
}

function selectProduct(id, s) {
    document.getElementById(`product-search-${id}`).value = s.name + (s.type==='oil'&&s.oil_type!=='none' ? ` (${s.oil_type})` : '');
    document.getElementById(`item-product-id-${id}`).value = s.id;
    document.getElementById(`item-is-new-${id}`).value = '0';
    
    // Autofill metadata directly
    document.getElementById(`item-barcode-${id}`).value = s.barcode || '';
    document.getElementById(`item-brand-${id}`).value = s.brand || '';
    document.getElementById(`item-compat-${id}`).value = s.vehicle_compatibility || '';

    if (s.type === 'oil' && s.oil_type && s.oil_type !== 'none') {
        document.getElementById(`item-oil-type-${id}`).value = s.oil_type;
    }
    
    hideProductDropdown(id);
}

function hideProductDropdown(id) {
    const dd = document.getElementById(`product-dropdown-${id}`);
    if (dd) dd.style.display = 'none';
}

document.addEventListener('click', e => {
    document.querySelectorAll('[id^="product-dropdown-"]').forEach(dd => {
        const rowId = dd.id.replace('product-dropdown-','');
        const inp = document.getElementById(`product-search-${rowId}`);
        if (inp && !inp.contains(e.target) && !dd.contains(e.target)) dd.style.display = 'none';
    });
});

function toggleNewProduct(id) {
    const isNewInput = document.getElementById(`item-is-new-${id}`);
    const searchWrapper = document.getElementById(`product-search-wrapper-${id}`);
    const nameWrapper = document.getElementById(`new-product-name-wrapper-${id}`);

    if (isNewInput.value === '1') {
        isNewInput.value = '0';
        searchWrapper.style.display = '';
        nameWrapper.style.display = 'none';
    } else {
        isNewInput.value = '1';
        searchWrapper.style.display = 'none';
        nameWrapper.style.display = '';
        
        // Reset fields
        document.getElementById(`item-product-id-${id}`).value = '';
        document.getElementById(`item-name-${id}`).value = '';
        document.getElementById(`item-barcode-${id}`).value = '';
        document.getElementById(`item-brand-${id}`).value = '';
        document.getElementById(`item-compat-${id}`).value = '';
        
        document.getElementById(`config-btn-${id}`).textContent = '⚙ Configure Details';
        document.getElementById(`config-btn-${id}`).className = 'px-2 py-0.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-[8.5px] rounded font-black uppercase transition-all';
        
        setTimeout(() => document.getElementById(`item-name-${id}`).focus(), 50);
    }
}

function markNewProduct(id) {
    document.getElementById(`item-is-new-${id}`).value = '1';
    document.getElementById(`item-product-id-${id}`).value = '';
}

// ─── NEW PRODUCT METADATA MODAL FUNCTIONS ─────────────
function openNewProductDetailsModal(rowId) {
    document.getElementById('modalActiveRowId').value = rowId;
    document.getElementById('newProdModalTitle').textContent = `Product Configuration for Row #${rowId}`;
    
    // Prefill modal values from hidden row inputs
    document.getElementById('modalBarcode').value = document.getElementById(`item-barcode-${rowId}`).value;
    document.getElementById('modalBrand').value = document.getElementById(`item-brand-${rowId}`).value;
    document.getElementById('modalCompat').value = document.getElementById(`item-compat-${rowId}`).value;
    
    document.getElementById('newProductModal').style.display = 'flex';
}

function closeNewProductDetailsModal() {
    document.getElementById('newProductModal').style.display = 'none';
}

function closeNewProductModalOverlay(e) {
    if (e.target === document.getElementById('newProductModal')) closeNewProductDetailsModal();
}

function genModalBarcode() {
    const ts = Date.now().toString().slice(-8);
    const rnd = Math.floor(Math.random()*900+100);
    document.getElementById('modalBarcode').value = ts + rnd;
}

function applyModalProductDetails() {
    const rowId = document.getElementById('modalActiveRowId').value;
    const barcode = document.getElementById('modalBarcode').value.trim();
    const brand = document.getElementById('modalBrand').value.trim();
    const compat = document.getElementById('modalCompat').value.trim();

    if (!barcode) {
        Swal.fire({icon:'warning',title:'Barcode Required',text:'Please enter or generate a barcode for this new product.',customClass:{popup:'rounded-[2rem]'}});
        return;
    }

    // Save back to row hidden fields
    document.getElementById(`item-barcode-${rowId}`).value = barcode;
    document.getElementById(`item-brand-${rowId}`).value = brand;
    document.getElementById(`item-compat-${rowId}`).value = compat;

    // Visual indicator on row configure button
    const btn = document.getElementById(`config-btn-${rowId}`);
    btn.textContent = '✓ Configured';
    btn.className = 'px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[8.5px] rounded font-black uppercase transition-all';

    closeNewProductDetailsModal();
}

// ─── TOTALS ──────────────────────────────────────────
function recalcRow(id) {
    const qty = parseFloat(document.getElementById(`item-qty-${id}`)?.value) || 0;
    const bp = parseFloat(document.getElementById(`item-bprice-${id}`)?.value) || 0;
    const total = qty * bp;
    const el = document.getElementById(`item-linetotal-${id}`);
    if (el) el.textContent = 'Rs. ' + total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
}

function recalcTotals() {
    let subtotal = 0;
    document.querySelectorAll('[id^="item-qty-"]').forEach(inp => {
        const id = inp.id.replace('item-qty-','');
        const qty = parseFloat(inp.value) || 0;
        const bp = parseFloat(document.getElementById(`item-bprice-${id}`)?.value) || 0;
        subtotal += qty * bp;
    });
    document.getElementById('totalSubtotal').textContent = 'Rs. ' + fmt(subtotal);

    let disc = parseFloat(document.getElementById('discountAmt').value) || 0;
    if (discountType === 'pct') disc = subtotal * disc / 100;
    const grand = Math.max(0, subtotal - disc);
    document.getElementById('totalGrand').textContent = 'Rs. ' + fmt(grand);
}

function setDiscountType(t) {
    discountType = t;
    document.getElementById('discRsBtn').className = 'px-3 py-2 text-[10px] font-black uppercase transition-all ' + (t==='rs' ? 'bg-blue-600 text-white' : 'bg-white text-slate-400');
    document.getElementById('discPctBtn').className = 'px-3 py-2 text-[10px] font-black uppercase transition-all ' + (t==='pct' ? 'bg-blue-600 text-white' : 'bg-white text-slate-400');
    recalcTotals();
}

function confirmDiscard() {
    Swal.fire({
        title: 'Discard Receipt?',
        text: 'Are you sure you want to discard and delete this draft receipt? Any entered data will be lost.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Discard It',
        customClass: { popup: 'rounded-[2rem]' }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'grm.php';
        }
    });
}

function confirmSave() {
    Swal.fire({
        title: 'Save GRM Invoice?',
        text: 'Are you sure you want to finalize and save this Goods Receipt?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Save It',
        customClass: { popup: 'rounded-[2rem]' }
    }).then((result) => {
        if (result.isConfirmed) {
            saveGRM();
        }
    });
}

// ─── SAVE GRM ────────────────────────────────────────
async function saveGRM() {
    const supplierName = document.getElementById('supplierInput').value.trim();
    if (!supplierName) { Swal.fire({icon:'warning',title:'Supplier Required',text:'Please enter a supplier name.',customClass:{popup:'rounded-[2rem]'}}); return; }
    const invoiceDate = document.getElementById('invoiceDate').value;
    if (!invoiceDate) { Swal.fire({icon:'warning',title:'Date Required',text:'Please select an invoice date.',customClass:{popup:'rounded-[2rem]'}}); return; }

    const itemRows = document.querySelectorAll('.item-row');
    const items = [];
    let hasError = false;

    for (const row of itemRows) {
        const id = row.id.replace('item-row-','');
        const typeEl = document.getElementById(`item-type-${id}`);
        if (!typeEl) continue;
        const type = typeEl.value;
        if (!type) { Swal.fire({icon:'warning',title:'Item Type Missing',text:`Please select Oil or Spare Part for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }

        const isNew = document.getElementById(`item-is-new-${id}`).value === '1';
        const productId = document.getElementById(`item-product-id-${id}`).value;
        const qty = parseFloat(document.getElementById(`item-qty-${id}`).value) || 0;
        const bprice = parseFloat(document.getElementById(`item-bprice-${id}`).value) || 0;
        const sprice = parseFloat(document.getElementById(`item-sprice-${id}`).value) || 0;

        if (!isNew && !productId) {
            Swal.fire({icon:'warning',title:'Product Not Selected',text:`Please select an existing product or create a new one for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break;
        }
        if (isNew) {
            const name = document.getElementById(`item-name-${id}`).value.trim();
            const barcode = document.getElementById(`item-barcode-${id}`).value.trim();
            if (!name || !barcode) { Swal.fire({icon:'warning',title:'Product Details Missing',text:`Name and Barcode are required for new product in Item ${id}. Click 'Configure Details' to enter barcode.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        }
        if (qty <= 0) { Swal.fire({icon:'warning',title:'Quantity Required',text:`Please enter qty for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        if (bprice <= 0) { Swal.fire({icon:'warning',title:'Price Required',text:`Please enter buying price for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        if (sprice <= 0) { Swal.fire({icon:'warning',title:'Price Required',text:`Please enter labeled price for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }

        items.push({
            product_type: type === 'oil' ? 'oil' : 'spare_part',
            is_new_product: isNew ? 1 : 0,
            product_id: productId || '',
            name: isNew ? document.getElementById(`item-name-${id}`).value.trim() : '',
            barcode: document.getElementById(`item-barcode-${id}`).value.trim(),
            oil_type: type === 'oil' ? document.getElementById(`item-oil-type-${id}`).value : 'none',
            brand: document.getElementById(`item-brand-${id}`).value.trim(),
            vehicle_compatibility: document.getElementById(`item-compat-${id}`).value.trim(),
            buying_price: bprice,
            selling_price: sprice,
            est_selling_price: parseFloat(document.getElementById(`item-estprice-${id}`).value)||sprice,
            qty: qty,
            expire_date: document.getElementById(`item-expire-${id}`).value || ''
        });
    }

    if (hasError) return;
    if (items.length === 0) { Swal.fire({icon:'warning',title:'No Items',text:'Please add at least one item.',customClass:{popup:'rounded-[2rem]'}}); return; }

    const btn = document.getElementById('saveGrmBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving...';

    let invoiceNo = document.getElementById('invoiceNo').value.trim();
    if (!invoiceNo) {
        invoiceNo = generateInvoiceNo();
        document.getElementById('invoiceNo').value = invoiceNo;
    }

    const formData = new FormData();
    formData.append('action','save_grm');
    formData.append('supplier_name', supplierName);
    formData.append('supplier_id', document.getElementById('supplierId').value);
    formData.append('supplier_contact', document.getElementById('supplierContact').value);
    formData.append('supplier_address', document.getElementById('supplierAddress').value);
    formData.append('invoice_date', invoiceDate);
    formData.append('invoice_no', invoiceNo);
    formData.append('notes', '');
    formData.append('discount', document.getElementById('discountAmt').value || 0);
    formData.append('discount_type', discountType);
    formData.append('items', JSON.stringify(items));

    try {
        const res = await fetch('grm_handler.php', {method:'POST',body:formData});
        const d = await res.json();
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save GRM Invoice';
        if (d.success) {
            Swal.fire({
                icon: 'success',
                title: 'GRM Saved!',
                text: d.message||'Goods receipt recorded.',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'rounded-[2rem]' }
            }).then(() => {
                window.location.href = 'grm.php';
            });
        } else {
            Swal.fire({icon:'error',title:'Save Failed',text:d.message||'Something went wrong.',customClass:{popup:'rounded-[2rem]'}});
        }
    } catch(err) {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save GRM Invoice';
        Swal.fire({icon:'error',title:'Network Error',text:'Could not reach server.',customClass:{popup:'rounded-[2rem]'}});
    }
}

function fmt(n) { return parseFloat(n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); }
function escHtml(s) { if(!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
</body>
</html>
