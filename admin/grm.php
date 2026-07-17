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
    <title>Goods Receipt Management - Vehicle Square</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
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
        .blue-gradient-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px -10px rgba(37,99,235,0.3);
        }
        input, select {
            background: white !important; border: 1px solid #e2e8f0 !important;
            color: #0f172a !important; outline: none !important;
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white !important; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
            padding: 1.1rem 1.25rem !important; font-size: 10px;
        }
        td { padding: 0.9rem 1.25rem !important; border-bottom: 1px solid rgba(226,232,240,0.5); color: #0f172a; font-size:13px; }
        tr:nth-child(even) { background-color: rgba(241,245,249,0.4); }
        tr.grm-row { cursor: pointer; transition: background 0.15s; }
        tr.grm-row:hover td { background: #eff6ff; }
        option { background-color: white !important; color: #0f172a !important; }

        /* Status badges */
        .badge-completed { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-draft { background: #fef9c3; color: #a16207; border: 1px solid #fde68a; }

        /* Pagination */
        .page-btn { width: 36px; height: 36px; border-radius: 10px; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1.5px solid #e2e8f0; background: white; color: #475569; transition: all 0.12s; }
        .page-btn:hover { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
        .page-btn.active { background: #2563eb; border-color: #2563eb; color: white; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* Modal Overlay & Modal Details */
        .grm-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,0.65);
            backdrop-filter: blur(6px); z-index: 10000;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 24px 16px; overflow-y: auto;
        }
        .grm-modal {
            background: white; border-radius: 2rem;
            box-shadow: 0 30px 80px -20px rgba(0,0,0,0.35);
            width: 100%; max-width: 1150px; overflow: hidden;
            animation: modalIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
            margin: auto;
        }
        @keyframes modalIn { from{opacity:0;transform:scale(0.94) translateY(16px)} to{opacity:1;transform:scale(1) translateY(0)} }
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 1.25rem 2rem; display: flex; align-items: center; justify-content: space-between;
        }
    </style>
</head>
<body class="bg-main min-h-screen relative pb-20">
<div class="colorful-overlay"></div>

<!-- NAV -->
<nav class="glass-nav sticky top-0 z-40">
    <div class="px-4 md:px-6 py-3.5 flex flex-col sm:flex-row justify-between items-center max-w-7xl mx-auto gap-3">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="dashboard.php" class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-black text-slate-900 tracking-tight uppercase">Goods Receipt Management</h1>
                <p class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mt-0.5">Purchase Invoices &amp; Supplier Registry</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="new_grm.php" id="newGrmBtn"
                class="flex-1 sm:flex-none bg-gradient-to-r from-blue-600 to-blue-800 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 border-2 border-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                New GRM Invoice
            </a>
        </div>
    </div>
</nav>

<main class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 relative z-10">

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="blue-gradient-card rounded-[1.5rem] p-5 text-white border-2 border-white">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-75 mb-1">GRMs This Month</p>
            <h2 id="statGrms" class="text-3xl font-black tracking-tighter">—</h2>
        </div>
        <div class="blue-gradient-card rounded-[1.5rem] p-5 text-white border-2 border-white">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-75 mb-1">Purchase Value (Month)</p>
            <h2 id="statValue" class="text-3xl font-black tracking-tighter">—</h2>
        </div>
        <div class="blue-gradient-card rounded-[1.5rem] p-5 text-white border-2 border-white">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-75 mb-1">Registered Suppliers</p>
            <h2 id="statSuppliers" class="text-3xl font-black tracking-tighter">—</h2>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4 md:p-5 border-2 border-white">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:flex items-center gap-3 flex-wrap">
            <div class="relative col-span-2 md:col-span-1 lg:flex-1 lg:min-w-[220px]">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="filterSearch" placeholder="Search invoice / supplier..." autocomplete="off"
                    class="w-full pl-10 pr-4 py-3 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all shadow-sm"
                    oninput="debounceLoad()">
            </div>
            <div class="relative col-span-1" id="supplierFilterWrap">
                <select id="filterSupplier" class="w-full px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider outline-none shadow-sm" onchange="loadGRMs(1)">
                    <option value="">All Suppliers</option>
                </select>
            </div>
            <div class="flex items-center gap-2 col-span-1 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tight">From</span>
                <input type="date" id="filterFrom" class="flex-1 text-[10px] font-bold text-slate-700 outline-none bg-transparent border-none !border-none !shadow-none" onchange="loadGRMs(1)">
            </div>
            <div class="flex items-center gap-2 col-span-1 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tight">To</span>
                <input type="date" id="filterTo" class="flex-1 text-[10px] font-bold text-slate-700 outline-none bg-transparent border-none !border-none !shadow-none" onchange="loadGRMs(1)">
            </div>
            <select id="filterStatus" class="col-span-1 px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider outline-none shadow-sm" onchange="loadGRMs(1)">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="draft">Draft</option>
            </select>
            <button onclick="resetFilters()" class="col-span-1 px-5 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card overflow-hidden border-4 border-blue-500/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-center">Items</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Final</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="grmTableBody">
                    <tr><td colspan="9" class="text-center py-16 text-slate-400 font-bold text-sm uppercase tracking-widest">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-3" id="paginationWrap"></div>
    </div>
</main>

<!-- ===================== DETAILS POPUP MODAL ===================== -->
<div id="grmDetailsModal" class="grm-modal-overlay" style="display:none;" onclick="closeDetailsModalOverlay(event)">
    <div class="grm-modal" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="modal-header">
            <div>
                <h2 class="text-lg font-black text-white uppercase tracking-tight" id="detailModalTitle">GRM Invoice Details</h2>
                <p class="text-blue-200 text-[10px] font-black uppercase tracking-widest mt-1" id="detailModalSubtitle">Receipt Audit Info</p>
            </div>
            <button onclick="closeDetailsModal()" class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
            <!-- Header Grid Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Supplier Name</span>
                    <div class="font-bold text-slate-800" id="detailSupplier"></div>
                </div>
                <div>
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Invoice Number</span>
                    <div class="font-bold text-blue-700" id="detailInvoiceNo"></div>
                </div>
                <div>
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Invoice Date</span>
                    <div class="font-bold text-slate-800" id="detailDate"></div>
                </div>
                <div>
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Notes / Remarks</span>
                    <div class="font-bold text-slate-800" id="detailNotes"></div>
                </div>
            </div>

            <!-- Items Table (Single Row per Product) -->
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left min-w-[950px] border-collapse">
                    <thead>
                        <tr>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;">Product Name</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;">Barcode / Part No</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;">Type</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;text-align:right">Qty</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;text-align:right">Buying Price</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;text-align:right">Labeled Price</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;text-align:right">Est. Price</th>
                            <th style="background:linear-gradient(135deg,#475569,#334155);font-size:9px;padding:10px 12px !important;text-align:right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody id="detailItemsBody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Totals Footer -->
            <div class="flex justify-end gap-6 pt-3 border-t border-slate-200 text-xs">
                <div class="text-right">
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Subtotal</span>
                    <div class="font-bold text-slate-800" id="detailSubtotal"></div>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Discount</span>
                    <div class="font-bold text-red-500" id="detailDiscount"></div>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 font-black uppercase tracking-wider text-[9px]">Grand Total</span>
                    <div class="font-black text-blue-800 text-base" id="detailGrandTotal"></div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-2">
                <button id="deleteGrmBtn" onclick="deleteGrmInvoice()" class="px-4 py-2.5 bg-red-50 text-red-600 border border-red-200 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Invoice
                </button>
                <button onclick="closeDetailsModal()" class="px-5 py-2.5 bg-slate-100 text-slate-500 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">Close Window</button>
            </div>
        </div>
    </div>
</div>
<!-- ===================== END POPUP ===================== -->

<script>
let currentPage = 1;
let debounceTimer;
let activeGrmId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadGRMs(1);
    loadStats();
    loadSupplierFilterOptions();
});

// ─── STATS ───────────────────────────────────────────
async function loadStats() {
    try {
        const res = await fetch('grm_handler.php?action=get_stats');
        const d = await res.json();
        document.getElementById('statGrms').textContent = d.total_grms_month ?? 0;
        document.getElementById('statValue').textContent = 'Rs. ' + parseFloat(d.value_month||0).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:0});
        document.getElementById('statSuppliers').textContent = d.total_suppliers ?? 0;
    } catch(e) {}
}

async function loadSupplierFilterOptions() {
    try {
        const res = await fetch('grm_handler.php?action=search_supplier&q= ');
        const d = await res.json();
        const sel = document.getElementById('filterSupplier');
        (d.suppliers||[]).forEach(s => {
            const o = document.createElement('option');
            o.value = s.id; o.textContent = s.name;
            sel.appendChild(o);
        });
    } catch(e) {}
}

// ─── LOAD GRMs ───────────────────────────────────────
function debounceLoad() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadGRMs(1), 320);
}

async function loadGRMs(page = 1) {
    currentPage = page;
    const search = document.getElementById('filterSearch').value;
    const supplier_id = document.getElementById('filterSupplier').value;
    const date_from = document.getElementById('filterFrom').value;
    const date_to = document.getElementById('filterTo').value;
    const status = document.getElementById('filterStatus').value;

    let url = `grm_handler.php?action=fetch_grms&page=${page}&search=${encodeURIComponent(search)}&supplier_id=${supplier_id}&date_from=${date_from}&date_to=${date_to}&status=${status}`;
    try {
        const res = await fetch(url);
        const d = await res.json();
        renderTable(d.grms || []);
        renderPagination(d.pagination || {});
    } catch(e) {
        document.getElementById('grmTableBody').innerHTML = `<tr><td colspan="9" class="text-center py-12 text-red-400 font-bold text-sm">Failed to load data</td></tr>`;
    }
}

function renderTable(rows) {
    const tbody = document.getElementById('grmTableBody');
    tbody.innerHTML = '';
    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-16">
            <div class="flex flex-col items-center gap-4">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-slate-400 font-black text-sm uppercase tracking-widest">No GRM Invoices Found</p>
                <a href="new_grm.php" class="px-6 py-2.5 bg-blue-50 text-blue-600 border-2 border-blue-200 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">+ Create First GRM</a>
            </div>
        </td></tr>`;
        return;
    }
    rows.forEach(r => {
        const statusBadge = r.status === 'completed'
            ? `<span class="badge-completed px-3 py-1 rounded-lg text-[10px] font-black uppercase">Completed</span>`
            : `<span class="badge-draft px-3 py-1 rounded-lg text-[10px] font-black uppercase">Draft</span>`;
        const tr = document.createElement('tr');
        tr.className = 'grm-row';
        tr.innerHTML = `
            <td><span class="font-black text-blue-700 text-xs">${escHtml(r.invoice_no)}</span></td>
            <td><div class="font-bold">${escHtml(r.supplier_name||'—')}</div></td>
            <td><span class="text-xs text-slate-500 font-bold">${r.invoice_date||'—'}</span></td>
            <td class="text-center"><span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-lg text-xs font-black">${r.item_count||0}</span></td>
            <td class="text-right font-bold">Rs. ${fmtNum(r.total_amount)}</td>
            <td class="text-right text-red-500 font-bold">${parseFloat(r.discount||0) > 0 ? '- Rs. '+fmtNum(r.discount) : '<span class="text-slate-300">—</span>'}</td>
            <td class="text-right font-black text-blue-800">Rs. ${fmtNum(r.final_amount)}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-center">
                <button onclick="event.stopPropagation();showGrmDetails(${r.id})" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-600 rounded-lg text-[10px] font-black uppercase transition-all">View</button>
            </td>`;
        tr.onclick = () => { showGrmDetails(r.id); };
        tbody.appendChild(tr);
    });
}

// ─── DETAILS MODAL ───────────────────────────────────
async function showGrmDetails(id) {
    activeGrmId = id;
    document.getElementById('grmDetailsModal').style.display = 'flex';
    document.getElementById('detailSupplier').textContent = 'Loading...';
    document.getElementById('detailInvoiceNo').textContent = 'Loading...';
    document.getElementById('detailDate').textContent = 'Loading...';
    document.getElementById('detailNotes').textContent = 'Loading...';
    document.getElementById('detailSubtotal').textContent = '—';
    document.getElementById('detailDiscount').textContent = '—';
    document.getElementById('detailGrandTotal').textContent = '—';
    document.getElementById('detailItemsBody').innerHTML = '<tr><td colspan="8" class="text-center py-8 text-slate-400 font-bold animate-pulse">Loading invoice details...</td></tr>';

    try {
        const res = await fetch(`grm_handler.php?action=get_grm_detail&id=${id}`);
        const d = await res.json();
        if (d.success) {
            // Header
            document.getElementById('detailSupplier').textContent = d.invoice.supplier_name || '—';
            document.getElementById('detailInvoiceNo').textContent = d.invoice.invoice_no || '—';
            document.getElementById('detailDate').textContent = d.invoice.invoice_date || '—';
            document.getElementById('detailNotes').textContent = d.invoice.notes || '—';

            // Items (Single row per product)
            const tbody = document.getElementById('detailItemsBody');
            tbody.innerHTML = '';
            
            d.items.forEach(it => {
                const tr = document.createElement('tr');
                tr.style.backgroundColor = 'white';
                
                const typeLabel = it.type === 'oil'
                    ? `<span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-md text-[9px] font-black uppercase">🛢 Oil ${it.oil_type !== 'none' ? '('+it.oil_type+')' : ''}</span>`
                    : `<span class="bg-violet-50 text-violet-700 border border-violet-200 px-2 py-0.5 rounded-md text-[9px] font-black uppercase">🔧 Spare</span>`;

                tr.innerHTML = `
                    <td class="font-bold text-slate-800">${escHtml(it.product_name)}</td>
                    <td class="font-mono text-xs text-slate-600 font-bold">${escHtml(it.barcode)}</td>
                    <td>${typeLabel}</td>
                    <td class="text-right font-black">${it.qty}</td>
                    <td class="text-right">Rs. ${fmtNum(it.buying_price)}</td>
                    <td class="text-right">Rs. ${fmtNum(it.selling_price)}</td>
                    <td class="text-right text-blue-600 font-bold">Rs. ${fmtNum(it.est_selling_price)}</td>
                    <td class="text-right font-black text-blue-800">Rs. ${fmtNum(it.line_total)}</td>
                `;
                tbody.appendChild(tr);
            });

            // Totals
            document.getElementById('detailSubtotal').textContent = 'Rs. ' + fmtNum(d.invoice.total_amount);
            document.getElementById('detailDiscount').textContent = parseFloat(d.invoice.discount || 0) > 0 ? '- Rs. ' + fmtNum(d.invoice.discount) : 'Rs. 0.00';
            document.getElementById('detailGrandTotal').textContent = 'Rs. ' + fmtNum(d.invoice.final_amount);
        } else {
            Swal.fire('Error', d.message || 'Could not fetch details', 'error');
            closeDetailsModal();
        }
    } catch(err) {
        Swal.fire('Error', 'Network request failed', 'error');
        closeDetailsModal();
    }
}

async function deleteGrmInvoice() {
    if (!activeGrmId) return;

    Swal.fire({
        title: 'Delete GRM Receipt?',
        text: 'Are you sure you want to delete this invoice? This will remove all items and deduct the stock quantities adjusted by this receipt!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Delete it',
        customClass: { popup: 'rounded-[2rem]' }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`grm_handler.php?action=delete_grm&id=${activeGrmId}`, { method: 'POST' });
                const d = await res.json();
                if (d.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: d.message || 'Invoice has been deleted.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                    closeDetailsModal();
                    loadGRMs(currentPage); // Reload active list
                    loadStats(); // Reload stats dashboard values
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Could not delete invoice', customClass: { popup: 'rounded-[2rem]' } });
                }
            } catch(e) {
                Swal.fire({ icon: 'error', title: 'Network Error', text: 'Server is unreachable', customClass: { popup: 'rounded-[2rem]' } });
            }
        }
    });
}

function closeDetailsModal() {
    activeGrmId = null;
    document.getElementById('grmDetailsModal').style.display = 'none';
}

function closeDetailsModalOverlay(e) {
    if (e.target === document.getElementById('grmDetailsModal')) closeDetailsModal();
}

function renderPagination(p) {
    const wrap = document.getElementById('paginationWrap');
    if (!p.total_pages || p.total_pages <= 1) { wrap.innerHTML = `<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${p.total_items||0} record(s)</p>`; return; }
    let html = `<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${p.total_items} record(s)</p><div class="flex gap-1.5">`;
    html += `<button class="page-btn" onclick="loadGRMs(${p.current_page-1})" ${p.current_page<=1?'disabled':''}>&#8592;</button>`;
    for (let i=1;i<=p.total_pages;i++) {
        if (i===1||i===p.total_pages||Math.abs(i-p.current_page)<=1) {
            html += `<button class="page-btn${i===p.current_page?' active':''}" onclick="loadGRMs(${i})">${i}</button>`;
        } else if (Math.abs(i-p.current_page)===2) {
            html += `<span class="page-btn" style="cursor:default;color:#cbd5e1;">…</span>`;
        }
    }
    html += `<button class="page-btn" onclick="loadGRMs(${p.current_page+1})" ${p.current_page>=p.total_pages?'disabled':''}>&#8594;</button></div>`;
    wrap.innerHTML = html;
}

function resetFilters() {
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterSupplier').value = '';
    document.getElementById('filterFrom').value = '';
    document.getElementById('filterTo').value = '';
    document.getElementById('filterStatus').value = '';
    loadGRMs(1);
}

function fmtNum(n) { return parseFloat(n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); }
function escHtml(s) { if(!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
</body>
</html>
