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
        .field-input { width: 100%; padding: 7px 11px; border-radius: 10px; border: 1.5px solid #e2e8f0 !important; font-weight: 700; font-size: 11.5px; transition: border-color 0.15s, box-shadow 0.15s; }
        .field-input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .field-input.highlight { border-color: #3b82f6 !important; background: #eff6ff !important; color: #1d4ed8 !important; font-weight: 900; }

        /* Suggest dropdown */
        .grm-dropdown {
            position: absolute; background: white;
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

        /* Type toggle buttons */
        .type-btn { flex: 1; padding: 7px 6px; border-radius: 8px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: 2px solid #e2e8f0; background: #f8fafc; color: #94a3b8; transition: all 0.15s; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .type-btn.active-oil { border-color: #2563eb; background: #2563eb; color: white; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .type-btn.active-spare { border-color: #7c3aed; background: #7c3aed; color: white; box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
        .subtype-btn { flex: 1; padding: 7px; border-radius: 8px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; border: 2px solid #e2e8f0; background: #f8fafc; color: #94a3b8; transition: all 0.12s; cursor: pointer; }
        .subtype-btn.active { border-color: #0891b2; background: #0891b2; color: white; }

        /* Item row */
        .item-row { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 12px; margin-bottom: 12px; position: relative; transition: border-color 0.15s; }
        .item-row:hover { border-color: #bfdbfe; }
        .item-row-num { position: absolute; top: -10px; left: 16px; background: linear-gradient(135deg,#2563eb,#1e40af); color: white; font-size: 8px; font-weight: 900; padding: 2px 8px; border-radius: 20px; letter-spacing: 0.1em; text-transform: uppercase; }
        .del-row-btn { position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; border-radius: 6px; background: #fee2e2; color: #ef4444; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.12s; }
        .del-row-btn:hover { background: #ef4444; color: white; }
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
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
<div class="colorful-overlay"></div>

<!-- NAV -->
<nav class="glass-nav sticky top-0 z-40">
    <div class="px-4 md:px-6 py-3.5 flex flex-col sm:flex-row justify-between items-center max-w-7xl mx-auto gap-3">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="grm.php" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-black text-slate-900 tracking-tight uppercase">New Goods Receipt Invoice</h1>
                <p class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mt-0.5">Register stock purchase receipt</p>
            </div>
        </div>
    </div>
</nav>

<main class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 relative z-10">
    <div class="glass-card p-6 md:p-8 border-2 border-white shadow-xl">
        <div class="space-y-6">

            <!-- SECTION 1: Supplier Details -->
            <div>
                <div class="modal-section-title">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Supplier Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 relative" id="supplierSearchWrap">
                        <label class="field-label">Supplier Name *</label>
                        <input type="text" id="supplierInput" placeholder="Type supplier name to search or add new..."
                            class="field-input" autocomplete="off" oninput="supplierInputChange()">
                        <input type="hidden" id="supplierId">
                        <div id="supplierDropdown" class="grm-dropdown" style="display:none; left: 0; width: 100%;"></div>
                    </div>
                    <div>
                        <label class="field-label">Contact Number</label>
                        <input type="text" id="supplierContact" placeholder="07X XXX XXXX" class="field-input">
                    </div>
                    <div>
                        <label class="field-label">Invoice Date *</label>
                        <input type="date" id="invoiceDate" class="field-input">
                    </div>
                    <div>
                        <label class="field-label">Invoice Number</label>
                        <input type="text" id="invoiceNo" class="field-input highlight" readonly>
                    </div>
                    <div>
                        <label class="field-label">Address</label>
                        <input type="text" id="supplierAddress" placeholder="Supplier address (optional)" class="field-input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label">Notes</label>
                        <textarea id="invoiceNotes" rows="2" placeholder="Any notes for this receipt..." class="field-input" style="resize:none;"></textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Items -->
            <div>
                <div class="modal-section-title">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Items
                </div>
                <div id="itemsContainer"></div>
                <button type="button" class="add-item-btn mt-2" onclick="addItemRow()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <!-- SECTION 3: Totals -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                <div class="modal-section-title">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M15 7h.01M3 5h18M3 19h18M3 12h18"/></svg>
                    Invoice Totals
                </div>
                <div class="space-y-1">
                    <div class="total-row">
                        <span class="total-label">Subtotal</span>
                        <span class="total-val" id="totalSubtotal">Rs. 0.00</span>
                    </div>
                    <div class="total-row items-start">
                        <span class="total-label pt-2">Discount</span>
                        <div class="flex items-center gap-2">
                            <input type="number" id="discountAmt" min="0" step="0.01" placeholder="0"
                                class="w-28 px-3 py-2 rounded-xl text-sm font-bold text-right" oninput="recalcTotals()">
                            <div class="flex rounded-xl overflow-hidden border border-slate-200">
                                <button id="discRsBtn" onclick="setDiscountType('rs')" class="px-3 py-2 text-[10px] font-black uppercase transition-all bg-blue-600 text-white">Rs</button>
                                <button id="discPctBtn" onclick="setDiscountType('pct')" class="px-3 py-2 text-[10px] font-black uppercase transition-all bg-white text-slate-400">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="total-row grand-total-row">
                        <span class="total-label" style="font-size:13px;color:#1e40af;">Grand Total</span>
                        <span class="total-val" id="totalGrand" style="font-size:22px;color:#1d4ed8;">Rs. 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Save / Cancel Buttons -->
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <a href="grm.php" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-slate-200 transition-all text-center flex items-center justify-center">Cancel</a>
                <button onclick="saveGRM()" id="saveGrmBtn"
                    class="flex-[2] py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl font-black uppercase text-xs tracking-widest hover:shadow-xl hover:shadow-blue-500/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Save GRM Invoice
                </button>
            </div>

        </div>
    </div>
</main>

<script>
let discountType = 'rs';
let rowCounter = 0;
let supplierDropdownActive = -1;
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
    supplierDropdownActive = -1;
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

// ─── ITEM ROWS ───────────────────────────────────────
function addItemRow() {
    rowCounter++;
    const id = rowCounter;
    const container = document.getElementById('itemsContainer');
    const div = document.createElement('div');
    div.className = 'item-row';
    div.id = `item-row-${id}`;
    div.innerHTML = `
        <span class="item-row-num">Item ${id}</span>
        <button type="button" class="del-row-btn" onclick="removeItemRow(${id})">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Type selection flow prompt -->
        <div id="type-select-container-${id}" class="py-3 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Select Item Type *</span>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm transition-all" onclick="setItemType(${id},'oil')">🛢 Oil</button>
                <button type="button" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm transition-all" onclick="setItemType(${id},'spare')">🔧 Spare Part</button>
            </div>
            <input type="hidden" id="item-type-${id}" value="">
        </div>

        <!-- Product details & search row (hidden until type selected) -->
        <div id="product-search-area-${id}" style="display:none;" class="mt-2">
            <!-- Row with Type label, Sub-category dropdown and Search field -->
            <div class="grid grid-cols-12 gap-3 items-end mb-3">
                <div class="col-span-3 md:col-span-2">
                    <label class="field-label">Selected Type</label>
                    <div id="type-badge-${id}" class="w-full text-[10px] font-black uppercase text-center py-2 bg-slate-100 border border-slate-200 rounded-xl"></div>
                </div>
                <div class="col-span-4 md:col-span-3" id="oil-subtype-col-${id}" style="display:none;">
                    <label class="field-label">Sub-category</label>
                    <select id="item-oil-type-${id}" class="field-input" onchange="setOilSubtype(${id}, this.value)">
                        <option value="can">Can (Sealed)</option>
                        <option value="loose">Loose (Litre)</option>
                    </select>
                </div>
                <div class="col-span-5 md:col-span-7 relative" id="search-product-col-${id}">
                    <label class="field-label" id="product-search-label-${id}">Search Product</label>
                    <input type="text" id="product-search-${id}" placeholder="Type name or barcode..." autocomplete="off"
                        class="field-input" oninput="productSearchInput(${id})" onfocus="productSearchInput(${id})">
                    <input type="hidden" id="item-product-id-${id}" value="">
                    <div id="product-dropdown-${id}" class="grm-dropdown" style="display:none; left: 0; width: 100%;"></div>
                </div>
            </div>

            <!-- New product toggle -->
            <div class="mb-3">
                <button type="button" onclick="toggleNewProduct(${id})" id="new-prod-toggle-${id}"
                    class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    <span id="new-prod-toggle-label-${id}">Create New Product</span>
                </button>
            </div>

            <!-- New product fields (hidden by default) -->
            <div id="new-product-fields-${id}" style="display:none;" class="bg-blue-50/60 border border-blue-200 rounded-xl p-4 mb-3">
                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-3">New Product Details</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="field-label" id="prod-name-label-${id}">Product Name *</label>
                        <input type="text" id="item-name-${id}" placeholder="Enter product name..." class="field-input" oninput="markNewProduct(${id})">
                    </div>
                    <div>
                        <label class="field-label" id="prod-barcode-label-${id}">Barcode / Part No *</label>
                        <div class="flex gap-2">
                            <input type="text" id="item-barcode-${id}" placeholder="Enter or generate..." class="field-input flex-1" oninput="markNewProduct(${id})">
                            <button type="button" onclick="genBarcode(${id})" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-xl text-[10px] font-black uppercase hover:bg-blue-200 transition-all whitespace-nowrap">Gen</button>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Brand</label>
                        <input type="text" id="item-brand-${id}" placeholder="e.g. Shell, Toyota..." class="field-input" oninput="markNewProduct(${id})">
                    </div>
                    <div class="col-span-2">
                        <label class="field-label">Vehicle Compatibility</label>
                        <input type="text" id="item-compat-${id}" placeholder="e.g. Toyota, Honda, Universal..." class="field-input" oninput="markNewProduct(${id})">
                    </div>
                </div>
            </div>
            <input type="hidden" id="item-is-new-${id}" value="0">

            <!-- Pricing row -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-3">
                <div>
                    <label class="field-label">Qty *</label>
                    <input type="number" id="item-qty-${id}" step="0.01" min="0.01" placeholder="0" class="field-input" oninput="recalcRow(${id});recalcTotals()">
                </div>
                <div>
                    <label class="field-label">Buying Price *</label>
                    <input type="number" id="item-bprice-${id}" step="0.01" min="0" placeholder="0.00" class="field-input" oninput="recalcRow(${id});recalcTotals()">
                </div>
                <div>
                    <label class="field-label">Labeled Price *</label>
                    <input type="number" id="item-sprice-${id}" step="0.01" min="0" placeholder="0.00" class="field-input">
                </div>
                <div>
                    <label class="field-label text-blue-600">Est. Selling Price</label>
                    <input type="number" id="item-estprice-${id}" step="0.01" min="0" placeholder="same as sell" class="field-input highlight">
                </div>
                <div>
                    <label class="field-label">Expire Date</label>
                    <input type="date" id="item-expire-${id}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Line Total</label>
                    <div id="item-linetotal-${id}" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 font-black text-sm text-blue-800">Rs. 0.00</div>
                </div>
            </div>
        </div>`;
    container.appendChild(div);
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

function setItemType(id, type) {
    document.getElementById(`item-type-${id}`).value = type;
    document.getElementById(`type-select-container-${id}`).style.display = 'none';

    const badge = document.getElementById(`type-badge-${id}`);
    const subtypeCol = document.getElementById(`oil-subtype-col-${id}`);
    const searchCol = document.getElementById(`search-product-col-${id}`);

    if (type === 'oil') {
        badge.textContent = '🛢 Oil';
        badge.className = 'w-full text-[10px] font-black uppercase text-center py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl';
        subtypeCol.style.display = '';
        searchCol.className = 'col-span-5 md:col-span-7 relative';
        document.getElementById(`product-search-area-${id}`).style.display = '';
        document.getElementById(`product-search-label-${id}`).textContent = 'Search Oil Product';
    } else if (type === 'spare') {
        badge.textContent = '🔧 Spare';
        badge.className = 'w-full text-[10px] font-black uppercase text-center py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-xl';
        subtypeCol.style.display = 'none';
        searchCol.className = 'col-span-9 md:col-span-10 relative';
        document.getElementById(`product-search-area-${id}`).style.display = '';
        document.getElementById(`product-search-label-${id}`).textContent = 'Search Spare Part';
    }

    document.getElementById(`product-search-${id}`).value = '';
    document.getElementById(`item-product-id-${id}`).value = '';
    document.getElementById(`item-is-new-${id}`).value = '0';
    document.getElementById(`new-product-fields-${id}`).style.display = 'none';
    document.getElementById(`new-prod-toggle-label-${id}`).textContent = 'Create New Product';
}

function setOilSubtype(id, subtype) {
    // Done automatically by the native dropdown onchange select
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
        el.innerHTML = `<div class="d-meta" style="color:#f59e0b;font-weight:900;">No match — use "Create New Product" below</div>`;
        el.style.cursor = 'default';
        dd.appendChild(el);
    }
    dd.style.display = 'block';
}

function selectProduct(id, s) {
    document.getElementById(`product-search-${id}`).value = s.name + (s.type==='oil'&&s.oil_type!=='none' ? ` (${s.oil_type})` : '');
    document.getElementById(`item-product-id-${id}`).value = s.id;
    document.getElementById(`item-is-new-${id}`).value = '0';
    if (s.type === 'oil' && s.oil_type && s.oil_type !== 'none') {
        setOilSubtype(id, s.oil_type);
    }
    document.getElementById(`new-product-fields-${id}`).style.display = 'none';
    document.getElementById(`new-prod-toggle-label-${id}`).textContent = 'Create New Product Instead';
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
    const fields = document.getElementById(`new-product-fields-${id}`);
    const lbl = document.getElementById(`new-prod-toggle-label-${id}`);
    if (fields.style.display === 'none') {
        fields.style.display = '';
        lbl.textContent = 'Cancel New Product';
        document.getElementById(`item-is-new-${id}`).value = '1';
        document.getElementById(`item-product-id-${id}`).value = '';
        document.getElementById(`product-search-${id}`).value = '';
    } else {
        fields.style.display = 'none';
        lbl.textContent = 'Create New Product';
        document.getElementById(`item-is-new-${id}`).value = '0';
    }
}

function markNewProduct(id) {
    document.getElementById(`item-is-new-${id}`).value = '1';
    document.getElementById(`item-product-id-${id}`).value = '';
}

function genBarcode(id) {
    const ts = Date.now().toString().slice(-8);
    const rnd = Math.floor(Math.random()*900+100);
    document.getElementById(`item-barcode-${id}`).value = ts + rnd;
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

        const productAreaVisible = document.getElementById(`product-search-area-${id}`).style.display !== 'none';
        if (!productAreaVisible) { Swal.fire({icon:'warning',title:'Item Type Missing',text:`Please select a type for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }

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
            if (!name || !barcode) { Swal.fire({icon:'warning',title:'Product Details Missing',text:`Name and Barcode are required for new product in Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        }
        if (qty <= 0) { Swal.fire({icon:'warning',title:'Quantity Required',text:`Please enter qty for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        if (bprice <= 0) { Swal.fire({icon:'warning',title:'Price Required',text:`Please enter buying price for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }
        if (sprice <= 0) { Swal.fire({icon:'warning',title:'Price Required',text:`Please enter selling price for Item ${id}.`,customClass:{popup:'rounded-[2rem]'}}); hasError=true; break; }

        items.push({
            product_type: type === 'oil' ? 'oil' : 'spare_part',
            is_new_product: isNew ? 1 : 0,
            product_id: productId || '',
            name: isNew ? document.getElementById(`item-name-${id}`).value.trim() : '',
            barcode: isNew ? document.getElementById(`item-barcode-${id}`).value.trim() : '',
            oil_type: type === 'oil' ? document.getElementById(`item-oil-type-${id}`).value : 'none',
            brand: isNew ? (document.getElementById(`item-brand-${id}`)?.value.trim()||'') : '',
            vehicle_compatibility: isNew ? (document.getElementById(`item-compat-${id}`)?.value.trim()||'') : '',
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

    const formData = new FormData();
    formData.append('action','save_grm');
    formData.append('supplier_name', supplierName);
    formData.append('supplier_id', document.getElementById('supplierId').value);
    formData.append('supplier_contact', document.getElementById('supplierContact').value);
    formData.append('supplier_address', document.getElementById('supplierAddress').value);
    formData.append('invoice_date', invoiceDate);
    formData.append('invoice_no', document.getElementById('invoiceNo').value);
    formData.append('notes', document.getElementById('invoiceNotes').value);
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
