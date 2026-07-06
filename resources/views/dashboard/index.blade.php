<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Face React</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ═══════════════════════════════════════════════════*/
/* BASE */
/* ═══════════════════════════════════════════════════ */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f1117; --surface:#1a1d27; --surface2:#22263a;
  --border:rgba(255,255,255,.08); --border2:rgba(255,255,255,.12);
  --text:#f1f5f9; --muted:#94a3b8; --dim:#475569;
  --green:#22c55e; --blue:#3b82f6; --yellow:#eab308;
  --orange:#f97316; --red:#ef4444;
  --sidebar:240px; --radius:10px;
}
body{font-family:'Inter',sans-serif;background:radial-gradient(circle at top center, #1b1e2c 0%, var(--bg) 100%);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden}
a{text-decoration:none;color:inherit}
button{font-family:'Inter',sans-serif;cursor:pointer}
/* ═══════════════════════════════════════════════════ */
/* SIDEBAR */
/* ═══════════════════════════════════════════════════ */
.sidebar{width:var(--sidebar);min-height:100vh;background:rgba(26,29,39,0.8);backdrop-filter:blur(12px);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;padding:24px 14px 20px;box-shadow:4px 0 24px rgba(0,0,0,.1)}
.sidebar-logo{display:flex;align-items:center;gap:12px;padding:0 8px 28px;border-bottom:1px solid var(--border);margin-bottom:24px}
.sidebar-logo .ic{width:42px;height:42px;background:linear-gradient(135deg,var(--green),#16a34a);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;box-shadow:0 6px 16px rgba(34,197,94,.3)}
.sidebar-logo .txt h2{font-size:15px;font-weight:700;color:var(--text)}
.sidebar-logo .txt p{font-size:11px;color:var(--muted);margin-top:1px}
.nav-label{font-size:10px;font-weight:600;color:var(--dim);letter-spacing:1px;text-transform:uppercase;padding:0 8px;margin:0 0 8px}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:10px;font-size:13px;font-weight:500;color:var(--muted);cursor:pointer;transition:.15s;margin-bottom:2px;border:none;background:transparent;width:100%;text-align:left}
.nav-item:hover{background:var(--surface2);color:var(--text)}
.nav-item.active{background:rgba(34,197,94,.12);color:var(--green)}
.nav-item span.icon{font-size:16px;width:20px;text-align:center}
.nav-section{margin-bottom:20px}
.sidebar-footer{margin-top:auto;padding-top:20px;border-top:1px solid var(--border)}
.user-row{display:flex;align-items:center;gap:10px;padding:10px 8px;border-radius:10px}
.user-avatar{width:34px;height:34px;background:linear-gradient(135deg,var(--blue),#6366f1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0}
.user-info p{font-size:13px;font-weight:600;color:var(--text)}
.user-info span{font-size:11px;color:var(--muted)}
.logout-btn{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:8px;background:transparent;border:none;color:var(--dim);font-size:12px;width:100%;cursor:pointer;transition:.15s;margin-top:4px}
.logout-btn:hover{background:rgba(239,68,68,.1);color:#fca5a5}
/* ═══════════════════════════════════════════════════ */
/* MAIN */
/* ═══════════════════════════════════════════════════ */
.main{flex:1;display:flex;flex-direction:column;min-height:100vh;overflow:hidden}
.topbar{background:rgba(26,29,39,0.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:8px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;z-index:10;box-shadow:0 4px 20px rgba(0,0,0,.1)}
.topbar-left h1{font-size:18px;font-weight:700;color:var(--text)}
.topbar-left p{font-size:11px;color:var(--muted);margin-top:2px}
.topbar-right{display:flex;align-items:center;gap:10px}
.period-btn{padding:6px 12px;border-radius:6px;border:1px solid var(--border2);background:transparent;color:var(--muted);font-size:12px;font-weight:500;cursor:pointer;transition:.15s}
.period-btn:hover,.period-btn.active{background:rgba(255,255,255,.08);color:var(--text);border-color:rgba(255,255,255,.2)}
.period-btn.active{background:var(--surface2);color:var(--text)}
.refresh-badge{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;background:var(--surface2);border:1px solid var(--border);font-size:11px;color:var(--muted)}
.refresh-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 1.5s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
/* CONTENT */
.content{flex:1;overflow-y:auto;padding:12px 20px;padding-bottom:16px}
/* FILTER BAR */
.filter-bar{background:rgba(26,29,39,0.6);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.05);border-radius:var(--radius);padding:8px 14px;margin-bottom:10px;display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;box-shadow:0 4px 20px rgba(0,0,0,.15)}
.filter-group{display:flex;flex-direction:column;gap:5px}
.filter-group label{font-size:10px;font-weight:600;color:var(--dim);text-transform:uppercase;letter-spacing:.5px}
select,input[type=date],input[type=number]{background:var(--surface2);border:1px solid var(--border2);border-radius:6px;padding:7px 10px;color:var(--text);font-size:12px;font-family:'Inter',sans-serif;outline:none;transition:.15s;min-width:120px;height:32px}
select:focus,input[type=date]:focus,input[type=number]:focus{border-color:var(--green);box-shadow:0 0 0 2px rgba(34,197,94,.1)}
select option{background:var(--surface2)}
.filter-actions{display:flex;gap:8px;margin-left:auto;align-items:flex-end}
.btn-apply{background:var(--green);color:#fff;border:none;border-radius:6px;padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;transition:.2s ease;box-shadow:0 4px 14px rgba(34,197,94,.3);height:32px}
.btn-apply:hover{background:#16a34a;transform:translateY(-2px);box-shadow:0 6px 20px rgba(34,197,94,.4)}
.btn-export{background:rgba(59,130,246,.15);backdrop-filter:blur(4px);color:var(--blue);border:1px solid rgba(59,130,246,.25);border-radius:6px;padding:7px 14px;font-size:12px;font-weight:500;cursor:pointer;transition:.2s ease;display:flex;align-items:center;gap:6px;height:32px}
.btn-export:hover{background:rgba(59,130,246,.25);transform:translateY(-2px);box-shadow:0 6px 20px rgba(59,130,246,.2)}
.btn-reset{background:transparent;color:var(--muted);border:1px solid var(--border2);border-radius:6px;padding:7px 12px;font-size:12px;cursor:pointer;transition:.2s ease;height:32px}
.btn-reset:hover{color:var(--text);border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.03)}
/* STAT CARDS */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:10px}
.stat-card{background:linear-gradient(145deg, rgba(26,29,39,0.9), rgba(26,29,39,0.4));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.06);border-radius:var(--radius);padding:12px 14px;transition:.3s cubic-bezier(0.4, 0, 0.2, 1);box-shadow:0 8px 30px rgba(0,0,0,.15);position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg, rgba(255,255,255,.05) 0%, transparent 100%);opacity:0;transition:.3s}
.stat-card:hover::before{opacity:1}
.stat-card:hover{border-color:rgba(255,255,255,.12);transform:translateY(-4px);box-shadow:0 14px 40px rgba(0,0,0,.25)}
.stat-label{font-size:11px;color:var(--muted);font-weight:500;margin-bottom:6px}
.stat-value{font-size:26px;font-weight:800;color:var(--text);line-height:1;margin-bottom:4px}
.stat-sub{font-size:11px;color:var(--dim)}
.stat-badge{display:inline-flex;align-items:center;gap:4px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2);padding:2px 6px;border-radius:6px;font-size:10px;font-weight:600;color:var(--green);margin-top:4px}
.stat-value.green{color:var(--green)}
.stat-value.blue{color:var(--blue)}
/* GRID 2-COL */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}
.grid-3{display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:10px}
.card{background:linear-gradient(135deg, rgba(26,29,39,0.85), rgba(26,29,39,0.5));backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.05);border-radius:var(--radius);padding:14px;box-shadow:0 10px 35px rgba(0,0,0,.2);transition:.3s}
.card:hover{border-color:rgba(255,255,255,.08);box-shadow:0 14px 45px rgba(0,0,0,.25)}
.card-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;display:flex;align-items:center;justify-content:space-between}
.card-title span{font-size:10px;font-weight:400;color:var(--dim)}
/* BREAKDOWN BARS */
.breakdown-list{display:flex;flex-direction:column;gap:12px}
.breakdown-item{display:flex;flex-direction:column;gap:5px}
.breakdown-row{display:flex;align-items:center;justify-content:space-between}
.breakdown-label{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text);font-weight:500}
.breakdown-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.breakdown-nums{display:flex;align-items:center;gap:10px}
.breakdown-count{font-size:14px;font-weight:700;color:var(--text)}
.breakdown-pct{font-size:13px;font-weight:600}
.breakdown-bar-bg{height:5px;background:rgba(255,255,255,.06);border-radius:999px;overflow:hidden}
.breakdown-bar-fill{height:100%;border-radius:999px;transition:width .6s ease}
/* TOP REACTION CARD */
.top-react-card{display:flex;flex-direction:column;gap:14px}
.top-react-badge{display:flex;align-items:center;gap:12px;padding:12px 14px;background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.15);border-radius:8px}
.top-react-icon{width:44px;height:44px;background:linear-gradient(135deg,rgba(34,197,94,.2),rgba(16,163,74,.3));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.top-react-info p{font-size:10px;color:var(--muted);margin-bottom:2px}
.top-react-info h3{font-size:18px;font-weight:800;color:var(--green)}
.top-react-info span{font-size:11px;color:var(--dim)}
.participation-bar-bg{height:6px;background:rgba(255,255,255,.06);border-radius:999px;overflow:hidden;margin-top:6px}
.participation-bar-fill{height:100%;background:linear-gradient(90deg,var(--green),#16a34a);border-radius:999px;transition:width .8s ease}
/* DEPT LIST */
.dept-list{display:flex;flex-direction:column;gap:3px}
.dept-row{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;transition:.15s}
.dept-row:hover{background:var(--surface2)}
.dept-name{font-size:13px;color:var(--text);font-weight:500}
.dept-count{font-size:13px;font-weight:700;color:var(--green)}
.dept-mini-bars{display:flex;gap:2px;margin-top:4px}
.dept-mini-bar{height:3px;border-radius:999px;min-width:4px}
/* CHART CANVAS */
#barChart{max-height:130px}
#pieChart{max-height:100%;width:100%!important;height:100%!important}
/* MANAGEMENT MODALS */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:100;display:none;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border:1px solid var(--border2);border-radius:18px;padding:32px;width:100%;max-width:440px;position:relative}
.modal h3{font-size:18px;font-weight:700;margin-bottom:20px;color:var(--text)}
.modal label{font-size:12px;font-weight:500;color:var(--muted);display:block;margin-bottom:6px;margin-top:16px}
.modal input,.modal select{width:100%;background:var(--bg);border:1px solid var(--border2);border-radius:8px;padding:11px 14px;color:var(--text);font-size:14px;font-family:'Inter',sans-serif;outline:none;height:auto}
.modal input:focus,.modal select:focus{border-color:var(--green)}
.modal-actions{display:flex;gap:10px;margin-top:24px}
.btn-modal-save{flex:1;background:var(--green);color:#fff;border:none;border-radius:8px;padding:12px;font-size:14px;font-weight:600;cursor:pointer}
.btn-modal-save:hover{background:#16a34a}
.btn-modal-cancel{padding:12px 20px;background:transparent;border:1px solid var(--border2);border-radius:8px;color:var(--muted);cursor:pointer;font-size:14px}
.modal-close{position:absolute;top:16px;right:16px;background:transparent;border:none;color:var(--muted);font-size:20px;cursor:pointer}
/* TOAST */
.toast{position:fixed;bottom:24px;right:24px;background:#1e2436;border:1px solid var(--border2);border-radius:10px;padding:14px 20px;font-size:13px;color:var(--text);z-index:999;opacity:0;transform:translateY(10px);transition:.3s;display:flex;align-items:center;gap:10px}
.toast.show{opacity:1;transform:translateY(0)}
.toast.success .t-icon{color:var(--green)}
.toast.error .t-icon{color:var(--red)}
/* LOADING */
.loading-overlay{position:absolute;inset:0;background:rgba(26,29,39,.7);border-radius:var(--radius);display:none;align-items:center;justify-content:center;z-index:10}
.loading-overlay.show{display:flex}
.spinner{width:28px;height:28px;border:3px solid rgba(255,255,255,.1);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
/* TABLE & REPORTS */
.table-wrapper{background:rgba(26,29,39,0.5);border:1px solid rgba(255,255,255,.05);border-radius:var(--radius);overflow-x:auto;margin-top:0;}
table{width:100%;border-collapse:collapse;font-size:12px;text-align:left;min-width:700px}
th,td{padding:10px 14px;border-bottom:1px solid var(--border2)}
th{background:var(--surface2);font-weight:600;color:var(--muted);text-transform:uppercase;font-size:10px;letter-spacing:0.5px}
tr:last-child td{border-bottom:none}
tbody tr:hover{background:rgba(255,255,255,.02)}
.pagination{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:rgba(26,29,39,0.8);backdrop-filter:blur(8px);border-top:1px solid var(--border)}
.page-btn{background:var(--surface2);border:1px solid var(--border2);color:var(--text);padding:6px 14px;border-radius:6px;font-size:11px;cursor:pointer;transition:.15s}
.page-btn:hover:not(:disabled){background:var(--border2)}
.page-btn:disabled{opacity:0.5;cursor:not-allowed}
.react-badge{display:inline-flex;align-items:center;gap:6px;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600}

/* SCROLLBAR */
::-webkit-scrollbar{width:5px;height:5px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:999px}
/* RESPONSIVE */
@media(max-width:1280px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.grid-2,.grid-3{grid-template-columns:1fr}}
</style>
</head>
<body>

<!-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="ic">📊</div>
    <div class="txt">
      <h2>Feedback Manager</h2>
      <p>{{ Auth::user()->company->name ?? 'Company' }}</p>
    </div>
  </div>

  <div class="nav-section">
    <div class="nav-label">Analytics</div>
    <button class="nav-item active" onclick="showSection('dashboard-section')">
      <span class="icon">⊞</span> Dashboard
    </button>
    <button class="nav-item" onclick="showSection('reports-section')">
      <span class="icon">↗</span> Reports
    </button>
  </div>

  <div class="nav-section">
    <div class="nav-label">Management</div>
    <button class="nav-item" onclick="showSection('departments-tab-section')" id="nav-departments">
      <span class="icon">⌂</span> Departments
    </button>
    <button class="nav-item" onclick="showSection('sections-tab-section')" id="nav-sections">
      <span class="icon">☰</span> Sections
    </button>
    <button class="nav-item" onclick="showSection('options-section')" id="nav-options">
      <span class="icon">⚙</span> Create Options
    </button>
  </div>

  <div class="sidebar-footer">
    <div class="user-row">
      <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->username, 0, 2)) }}</div>
      <div class="user-info">
        <p>{{ Auth::user()->name ?? Auth::user()->username }}</p>
        <span>Administrator</span>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="logout-btn">⬡ Sign out</button>
    </form>
  </div>
</aside>

<!-- ═══ MAIN ══════════════════════════════════════════════════════════ -->
<main class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <h1>Dashboard</h1>
      <p id="current-date">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <div class="topbar-right">
      <button class="period-btn active" onclick="setPeriod('today',this)">Today</button>
      <button class="period-btn" onclick="setPeriod('week',this)">This week</button>
      <button class="period-btn" onclick="setPeriod('month',this)">This month</button>
      <div class="refresh-badge">
        <div class="refresh-dot"></div>
        <span id="refresh-counter">30s</span>
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">

    <!-- ── FILTER BAR ──────────────────────────────────────────── -->
    <div class="filter-bar">
      <div class="filter-group">
        <label>From Date</label>
        <input type="date" id="f-date-from" value="{{ today()->toDateString() }}">
      </div>
      <div class="filter-group">
        <label>To Date</label>
        <input type="date" id="f-date-to" value="{{ today()->toDateString() }}">
      </div>
      <div class="filter-group">
        <label>Department</label>
        <select id="f-dept">
          <option value="">All Departments</option>
          @foreach($departments as $dept)
          <option value="{{ $dept->id }}">{{ $dept->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="filter-group">
        <label>Section</label>
        <select id="f-section">
          <option value="">All Sections</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Hour From</label>
        <input type="number" id="f-hour-from" min="0" max="23" placeholder="0" style="width:90px">
      </div>
      <div class="filter-group">
        <label>Hour To</label>
        <input type="number" id="f-hour-to" min="0" max="23" placeholder="23" style="width:90px">
      </div>
      <div class="filter-actions">
        <button class="btn-reset" onclick="resetFilters()">Reset</button>
        <button class="btn-apply" onclick="loadAll()">Apply</button>
        <button class="btn-export" onclick="exportExcel()">⬇ Export Excel</button>
      </div>
    </div>

    <!-- ── TAB CONTENT ─────────────────────────────────────────── -->
    <div id="dashboard-section" class="tab-section">

      <!-- ROW 1: Stats Cards (4) -->
      <div class="stats-grid" style="margin-bottom:10px">
        <div class="stat-card" style="padding:10px 12px">
          <div class="stat-label" style="font-size:10px;margin-bottom:2px">Total reactions</div>
          <div class="stat-value" id="s-total" style="font-size:20px;font-weight:700">—</div>
          <div class="stat-badge" id="s-participation-badge" style="font-size:9px;padding:1px 4px;margin-top:2px">—% rate</div>
        </div>
        <div class="stat-card" style="padding:10px 12px">
          <div class="stat-label" style="font-size:10px;margin-bottom:2px">Satisfied</div>
          <div class="stat-value green" id="s-satisfied" style="font-size:20px;font-weight:700">—%</div>
          <div class="stat-sub" style="font-size:10px">Excellent + Good</div>
        </div>
        <div class="stat-card" style="padding:10px 12px">
          <div class="stat-label" style="font-size:10px;margin-bottom:2px">Participation rate</div>
          <div class="stat-value" id="s-participation" style="font-size:20px;font-weight:700">—%</div>
          <div class="stat-sub" id="s-employees" style="font-size:10px">— employees</div>
        </div>
        <div class="stat-card" style="padding:10px 12px">
          <div class="stat-label" style="font-size:10px;margin-bottom:2px">Top reaction</div>
          <div class="stat-value green" id="s-top-reaction" style="font-size:18px;font-weight:700">—</div>
          <div class="stat-sub" id="s-top-detail" style="font-size:10px">— reactions</div>
        </div>
      </div>

      <!-- ROW 2: Reaction breakdown & Top reaction -->
      <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:10px;margin-bottom:10px">
        <!-- Reaction breakdown -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:10px;font-size:12px">Reaction breakdown <span id="breakdown-period" style="font-size:10px">today</span></div>
          <div class="breakdown-list" id="breakdown-list" style="gap:6px">
            <div style="color:var(--dim);font-size:12px;padding:10px 0;text-align:center">Loading...</div>
          </div>
        </div>

        <!-- Top reaction (with participation details) -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:10px;font-size:12px">Top reaction</div>
          <div style="display:flex;flex-direction:column;gap:8px">
            <div class="top-react-badge" style="padding:8px 10px">
              <div class="top-react-icon" id="top-icon" style="width:32px;height:32px;font-size:16px">😊</div>
              <div class="top-react-info">
                <p style="font-size:9px;margin-bottom:1px">Top reaction</p>
                <h3 id="top-name" style="font-size:14px;font-weight:700;color:var(--green)">—</h3>
                <span id="top-count" style="font-size:10px;color:var(--dim)">— responses</span>
              </div>
              <div style="margin-left:auto;font-size:18px;font-weight:800;color:var(--green)" id="top-pct">—%</div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:4px">
                <div>
                  <div style="font-size:9px;color:var(--muted);margin-bottom:1px">Participation</div>
                  <div style="font-size:14px;font-weight:800;color:var(--text)" id="part-label">—% participated</div>
                </div>
                <div style="font-size:11px;color:var(--dim)" id="part-employees">— total</div>
              </div>
              <div class="participation-bar-bg" style="margin-top:0">
                <div class="participation-bar-fill" id="part-bar" style="width:0%"></div>
              </div>
            </div>
            <div class="breakdown-list" id="breakdown-list-2" style="margin-top:4px;gap:6px"></div>
          </div>
        </div>
      </div>

      <!-- ROW 3: Reaction distribution, Daily trend, By department -->
      <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr;gap:10px;margin-bottom:10px">
        <!-- Reaction distribution (Pie Chart) -->
        <div class="card" style="padding:12px;display:flex;flex-direction:column;align-items:center">
          <div class="card-title" style="margin-bottom:8px;font-size:12px;width:100%">Reaction distribution</div>
          <div style="width:120px;height:120px;position:relative">
            <canvas id="pieChart"></canvas>
          </div>
          <div id="pie-legend" style="display:flex;flex-wrap:wrap;gap:4px;margin-top:8px;justify-content:center"></div>
        </div>

        <!-- Daily trend (Bar Chart) -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:8px;font-size:12px">
            Daily trend
            <div id="bar-legend" style="display:flex;flex-wrap:wrap;gap:6px;font-size:10px;font-weight:400;color:var(--muted)"></div>
          </div>
          <div style="height:120px;position:relative">
            <canvas id="barChart" style="max-height:120px"></canvas>
          </div>
        </div>

        <!-- By department -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:6px;font-size:12px">By department</div>
          <div class="dept-list" id="dept-list" style="gap:2px">
            <div style="color:var(--dim);font-size:12px;padding:6px 0;text-align:center">Loading...</div>
          </div>
        </div>
      </div>

      <!-- ROW 4: Option Submissions & Option Breakdown -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <!-- Option Submissions -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:8px;font-size:12px">📋 Option Submissions <span id="or-period-label">today</span></div>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
            <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.15);border-radius:6px;padding:6px 8px">
              <div style="font-size:8px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Total Submissions</div>
              <div style="font-size:18px;font-weight:800;color:var(--green);margin-top:2px" id="or-total">—</div>
            </div>
            <div style="background:rgba(107,114,128,.08);border:1px solid rgba(107,114,128,.15);border-radius:6px;padding:6px 8px">
              <div style="font-size:8px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">No Selection</div>
              <div style="font-size:18px;font-weight:800;color:var(--muted);margin-top:2px" id="or-no-sel">—</div>
            </div>
            <div style="background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.15);border-radius:6px;padding:6px 8px;overflow:hidden">
              <div style="font-size:8px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Most Selected</div>
              <div style="font-size:12px;font-weight:700;color:var(--blue);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" id="or-top">—</div>
            </div>
            <div style="background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.15);border-radius:6px;padding:6px 8px">
              <div style="font-size:8px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Participation</div>
              <div style="font-size:18px;font-weight:800;color:var(--yellow);margin-top:2px" id="or-part">—%</div>
            </div>
          </div>
        </div>

        <!-- Option Breakdown -->
        <div class="card" style="padding:12px">
          <div class="card-title" style="margin-bottom:8px;font-size:12px">Option Breakdown</div>
          <div class="breakdown-list" id="or-breakdown" style="gap:6px">
            <div style="color:var(--dim);font-size:12px;padding:10px 0;text-align:center">Loading...</div>
          </div>
        </div>
      </div>

    </div><!-- /dashboard-section -->

    <!-- ── REPORTS SECTION ───────────────────────────────────── -->
    <div id="reports-section" class="tab-section" style="display:none">
      <div class="card" style="padding:0;overflow:hidden">
        <div class="card-title" style="padding:22px;margin:0;border-bottom:1px solid var(--border)">Detailed Reports</div>
        <div class="table-wrapper">
          <table id="logs-table">
            <thead>
              <tr>
                <th>Date &amp; Time</th>
                <th>Employee</th>
                <th>Department / Section</th>
                <th>Reaction</th>
                <th>Selected Options</th>
                <th>IP / Device</th>
                <th>Note</th>
              </tr>
            </thead>
            <tbody id="logs-tbody">
              <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>
            </tbody>
          </table>
          <div class="pagination">
            <div id="reports-info" style="font-size:12px;color:var(--muted)">Showing 0 to 0 of 0 logs</div>
            <div style="display:flex;gap:8px">
              <button class="page-btn" id="prev-page" onclick="changePage(-1)" disabled>Previous</button>
              <button class="page-btn" id="next-page" onclick="changePage(1)" disabled>Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── OPTIONS MANAGEMENT SECTION ─────────────────────── -->
    <div id="options-section" class="tab-section" style="display:none">

      <!-- Add Option Card -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-title">⚙ Options Management <span>Manage all configurable options</span></div>
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          <div class="filter-group" style="flex:1;min-width:200px">
            <label>Option Name</label>
            <input type="text" id="option-name-input" placeholder="e.g. Option Label" style="width:100%;height:auto;padding:10px 14px;font-size:14px">
          </div>
          <button class="btn-apply" id="option-save-btn" onclick="saveOption()" style="height:42px;padding:0 24px;font-size:13px">➕ Save Option</button>
        </div>
        <div id="option-form-error" style="display:none;margin-top:10px;color:var(--red);font-size:12px"></div>
      </div>

      <!-- Options Table Card -->
      <div class="card" style="padding:0;overflow:hidden">
        <div class="card-title" style="padding:18px 20px;margin:0;border-bottom:1px solid var(--border)">
          Options List
          <span id="options-count" style="font-size:11px;color:var(--dim)">Loading...</span>
        </div>
        <div class="table-wrapper">
          <table id="options-table">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Option Name</th>
                <th style="width:120px">Status</th>
                <th style="width:150px">Created Date</th>
                <th style="width:130px;text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="options-tbody">
              <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /options-section -->

    <!-- ── DEPARTMENTS MANAGEMENT SECTION ─────────────────────── -->
    <div id="departments-tab-section" class="tab-section" style="display:none">
      <!-- Add Department Card -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-title">⌂ Departments Management <span>Manage all departments of the organization</span></div>
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          <div class="filter-group" style="flex:1;min-width:200px">
            <label>Department Name</label>
            <input type="text" id="dept-name-input-tab" placeholder="e.g. Human Resources" style="width:100%;height:auto;padding:10px 14px;font-size:14px">
          </div>
          <button class="btn-apply" id="dept-save-btn-tab" onclick="saveDepartmentFromTab()" style="height:42px;padding:0 24px;font-size:13px">➕ Save Department</button>
        </div>
        <div id="dept-form-error-tab" style="display:none;margin-top:10px;color:var(--red);font-size:12px"></div>
      </div>

      <!-- Departments Table Card -->
      <div class="card" style="padding:0;overflow:hidden">
        <div class="card-title" style="padding:18px 20px;margin:0;border-bottom:1px solid var(--border)">
          Departments List
          <span id="departments-count" style="font-size:11px;color:var(--dim)">Loading...</span>
        </div>
        <div class="table-wrapper">
          <table id="departments-table">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Department Name</th>
                <th style="width:120px">Status</th>
                <th style="width:150px">Created Date</th>
                <th style="width:130px;text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="departments-tbody">
              <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /departments-tab-section -->

    <!-- ── SECTIONS MANAGEMENT SECTION ─────────────────────── -->
    <div id="sections-tab-section" class="tab-section" style="display:none">
      <!-- Add Section Card -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-title">☰ Sections Management <span>Manage sections under departments</span></div>
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          <div class="filter-group" style="width:250px">
            <label>Select Department</label>
            <select id="section-dept-select-tab" style="width:100%;height:42px;padding:0 14px;font-size:14px;border:1px solid var(--border2);border-radius:6px;background:var(--surface2);color:var(--text)">
              <option value="">-- Select Department --</option>
              @foreach($departments as $dept)
              <option value="{{ $dept->id }}">{{ $dept->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group" style="flex:1;min-width:200px">
            <label>Section Name</label>
            <input type="text" id="section-name-input-tab" placeholder="e.g. Payroll" style="width:100%;height:auto;padding:10px 14px;font-size:14px">
          </div>
          <button class="btn-apply" id="section-save-btn-tab" onclick="saveSectionFromTab()" style="height:42px;padding:0 24px;font-size:13px">➕ Save Section</button>
        </div>
        <div id="section-form-error-tab" style="display:none;margin-top:10px;color:var(--red);font-size:12px"></div>
      </div>

      <!-- Sections Table Card -->
      <div class="card" style="padding:0;overflow:hidden">
        <div class="card-title" style="padding:18px 20px;margin:0;border-bottom:1px solid var(--border)">
          Sections List
          <span id="sections-count" style="font-size:11px;color:var(--dim)">Loading...</span>
        </div>
        <div class="table-wrapper">
          <table id="sections-table">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Section Name</th>
                <th>Department</th>
                <th style="width:120px">Status</th>
                <th style="width:150px">Created Date</th>
                <th style="width:130px;text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="sections-tbody">
              <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /sections-tab-section -->

  </div><!-- /content -->
</main>

<!-- ═══ MODALS ════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="dept-modal" onclick="closeModalOutside(event,'dept-modal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('dept-modal')">✕</button>
    <h3>➕ Add Department</h3>
    <label>Department Name</label>
    <input type="text" id="dept-name-input" placeholder="e.g. Human Resources">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('dept-modal')">Cancel</button>
      <button class="btn-modal-save" onclick="saveDepartment()">Save Department</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="section-modal" onclick="closeModalOutside(event,'section-modal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('section-modal')">✕</button>
    <h3>➕ Add Section</h3>
    <label>Select Department</label>
    <select id="section-dept-select">
      <option value="">-- Select Department --</option>
      @foreach($departments as $dept)
      <option value="{{ $dept->id }}">{{ $dept->name }}</option>
      @endforeach
    </select>
    <label>Section Name</label>
    <input type="text" id="section-name-input" placeholder="e.g. Payroll">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('section-modal')">Cancel</button>
      <button class="btn-modal-save" onclick="saveSection()">Save Section</button>
    </div>
  </div>
</div>

<!-- Edit Option Modal -->
<div class="modal-overlay" id="option-edit-modal" onclick="closeModalOutside(event,'option-edit-modal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('option-edit-modal')">✕</button>
    <h3>✏️ Edit Option</h3>
    <input type="hidden" id="edit-option-id">
    <label>Option Name</label>
    <input type="text" id="edit-option-name" placeholder="e.g. Option Label">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('option-edit-modal')">Cancel</button>
      <button class="btn-modal-save" onclick="updateOption()">Update Option</button>
    </div>
  </div>
</div>

<!-- Edit Department Modal -->
<div class="modal-overlay" id="dept-edit-modal" onclick="closeModalOutside(event,'dept-edit-modal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('dept-edit-modal')">✕</button>
    <h3>✏️ Edit Department</h3>
    <input type="hidden" id="edit-dept-id">
    <label>Department Name</label>
    <input type="text" id="edit-dept-name" placeholder="e.g. Human Resources">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('dept-edit-modal')">Cancel</button>
      <button class="btn-modal-save" onclick="updateDepartment()">Update Department</button>
    </div>
  </div>
</div>

<!-- Edit Section Modal -->
<div class="modal-overlay" id="section-edit-modal" onclick="closeModalOutside(event,'section-edit-modal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('section-edit-modal')">✕</button>
    <h3>✏️ Edit Section</h3>
    <input type="hidden" id="edit-section-id">
    <label>Select Department</label>
    <select id="edit-section-dept-id" style="width:100%;height:42px;padding:0 14px;font-size:14px;border:1px solid var(--border2);border-radius:6px;background:var(--surface2);color:var(--text);margin-bottom:12px">
      <option value="">-- Select Department --</option>
      @foreach($departments as $dept)
      <option value="{{ $dept->id }}">{{ $dept->name }}</option>
      @endforeach
    </select>
    <label>Section Name</label>
    <input type="text" id="edit-section-name" placeholder="e.g. Payroll">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('section-edit-modal')">Cancel</button>
      <button class="btn-modal-save" onclick="updateSection()">Update Section</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span class="t-icon">✓</span><span id="toast-msg"></span></div>

<script>
// ═══════════════════════════════════════════════════
// GLOBALS
// ═══════════════════════════════════════════════════
const ROUTES = {
  stats:  '{{ route("dashboard.stats") }}',
  pie:    '{{ route("dashboard.pie") }}',
  bar:    '{{ route("dashboard.bar") }}',
  dept:   '{{ route("dashboard.dept") }}',
  sections: '{{ route("dashboard.sections") }}',
  deptIndex: '{{ route("departments.index") }}',
  deptStore: '{{ route("departments.store") }}',
  sectIndex: '{{ route("sections.index") }}',
  sectStore: '{{ route("sections.store") }}',
  export: '{{ route("export") }}',
  logs:   '{{ route("dashboard.logs") }}',
  optionsIndex:  '{{ route("options.index") }}',
  optionsStore:  '{{ route("options.store") }}',
  optionsToggle: '/options',
  optionDashStats: '{{ route("options.dashboard.stats") }}',
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let pieChartInst = null, barChartInst = null;
let refreshTimer = 30, refreshInterval;
let currentPage = 1;

// ═══════════════════════════════════════════════════
// REFRESH COUNTER
// ═══════════════════════════════════════════════════
function startRefresh(){
  clearInterval(refreshInterval);
  refreshTimer = 30;
  document.getElementById('refresh-counter').textContent = refreshTimer + 's';
  refreshInterval = setInterval(()=>{
    refreshTimer--;
    document.getElementById('refresh-counter').textContent = refreshTimer + 's';
    if(refreshTimer <= 0){ refreshTimer = 30; loadAll(); }
  }, 1000);
}

// ═══════════════════════════════════════════════════
// FILTERS
// ═══════════════════════════════════════════════════
function getFilters(){
  return {
    date_from:     document.getElementById('f-date-from').value,
    date_to:       document.getElementById('f-date-to').value,
    department_id: document.getElementById('f-dept').value || '',
    section_id:    document.getElementById('f-section').value || '',
    hour_from:     document.getElementById('f-hour-from').value || '',
    hour_to:       document.getElementById('f-hour-to').value || '',
  };
}
function buildQuery(extra={}){
  const f = {...getFilters(), ...extra};
  return '?' + Object.entries(f).filter(([,v])=>v!=='').map(([k,v])=>`${k}=${encodeURIComponent(v)}`).join('&');
}
function setPeriod(period, btn){
  document.querySelectorAll('.period-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  const today = new Date();
  const fmt = d => d.toISOString().split('T')[0];
  let from = fmt(today), to = fmt(today);
  if(period==='week'){ const d=new Date(today); d.setDate(d.getDate()-6); from=fmt(d); }
  if(period==='month'){ const d=new Date(today); d.setDate(1); from=fmt(d); }
  document.getElementById('f-date-from').value = from;
  document.getElementById('f-date-to').value   = to;
  loadAll();
}
function resetFilters(){
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('f-date-from').value = today;
  document.getElementById('f-date-to').value   = today;
  document.getElementById('f-dept').value      = '';
  document.getElementById('f-section').value   = '';
  document.getElementById('f-hour-from').value = '';
  document.getElementById('f-hour-to').value   = '';
  loadAll();
}

// Department → section cascade
document.getElementById('f-dept').addEventListener('change', async function(){
  const deptId = this.value;
  const sel = document.getElementById('f-section');
  sel.innerHTML = '<option value="">All Sections</option>';
  if(!deptId) return;
  const res = await fetch(ROUTES.sections + '?department_id=' + deptId);
  const sections = await res.json();
  sections.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
});

// ═══════════════════════════════════════════════════
// LOAD ALL DATA
// ═══════════════════════════════════════════════════
// ── Load All ────────────────────────────────────────────
function loadAll(){
  loadStats();
  loadPieChart();
  loadBarChart();
  loadByDept();
  loadLogs(1);
  loadOptionDashboardStats();
  startRefresh();
}

// ── Stats ────────────────────────────────────────────
async function loadStats(){
  const res = await fetch(ROUTES.stats + buildQuery());
  const d   = await res.json();
  document.getElementById('s-total').textContent         = d.total_reacts.toLocaleString();
  document.getElementById('s-satisfied').textContent     = d.satisfied_pct + '%';
  document.getElementById('s-participation').textContent = d.participation_pct + '%';
  document.getElementById('s-employees').textContent     = d.total_employees + ' employees';
  document.getElementById('s-participation-badge').textContent = '+' + d.participation_pct + '% rate';
  if(d.top_reaction){
    document.getElementById('s-top-reaction').textContent = d.top_reaction.icon + ' ' + d.top_reaction.type;
    document.getElementById('s-top-detail').textContent   = d.top_reaction.count + ' reactions · ' + d.top_reaction.percentage + '%';
    document.getElementById('top-icon').textContent        = d.top_reaction.icon;
    document.getElementById('top-name').textContent        = d.top_reaction.type;
    document.getElementById('top-count').textContent       = d.top_reaction.count + ' responses';
    document.getElementById('top-pct').textContent         = d.top_reaction.percentage + '%';
  }
  document.getElementById('part-label').textContent    = d.participation_pct + '% participated';
  document.getElementById('part-employees').textContent= d.total_employees + ' total';
  document.getElementById('part-bar').style.width      = Math.min(d.participation_pct, 100) + '%';

  // Breakdown bars
  const list = document.getElementById('breakdown-list');
  if(!d.breakdown || d.breakdown.length===0){ list.innerHTML='<div style="color:var(--dim);font-size:13px;padding:20px 0;text-align:center">No data</div>'; return; }
  list.innerHTML = d.breakdown.map(b=>`
    <div class="breakdown-item">
      <div class="breakdown-row">
        <div class="breakdown-label">
          <div class="breakdown-dot" style="background:${b.color}"></div>
          ${b.icon} ${b.type}
        </div>
        <div class="breakdown-nums">
          <span class="breakdown-count">${b.count}</span>
          <span class="breakdown-pct" style="color:${b.color}">${b.percentage}%</span>
        </div>
      </div>
      <div class="breakdown-bar-bg">
        <div class="breakdown-bar-fill" style="width:${b.percentage}%;background:${b.color}"></div>
      </div>
    </div>
  `).join('');
}

// ── Pie Chart ─────────────────────────────────────────
async function loadPieChart(){
  const res = await fetch(ROUTES.pie + buildQuery());
  const d   = await res.json();
  const labels = d.map(x=>x.icon+' '+x.label);
  const values = d.map(x=>x.value);
  const colors = d.map(x=>x.color);
  if(pieChartInst) pieChartInst.destroy();
  pieChartInst = new Chart(document.getElementById('pieChart'), {
    type:'doughnut',
    data:{labels, datasets:[{data:values, backgroundColor:colors.map(c=>c+'cc'), borderColor:colors, borderWidth:2, hoverOffset:8}]},
    options:{
      responsive:true,
      maintainAspectRatio:true,
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:ctx=>`${ctx.label}: ${ctx.raw} (${ctx.parsed ? Math.round(ctx.parsed) : 0}%)`}}
      },
      cutout:'55%'
    },
  });
  // Render custom legend
  const legendEl = document.getElementById('pie-legend');
  if(legendEl){
    legendEl.innerHTML = d.map((x,i)=>{
      const pct = values.reduce((a,b)=>a+b,0) > 0 ? Math.round(x.value / values.reduce((a,b)=>a+b,0) * 100) : 0;
      return `<span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted)">
        <span style="width:10px;height:10px;border-radius:50%;background:${x.color};display:inline-block"></span>
        ${x.icon} ${x.label} <b style="color:var(--text)">${pct}%</b>
      </span>`;
    }).join('');
  }
}

// ── Bar Chart ─────────────────────────────────────────
async function loadBarChart(){
  const f = getFilters();
  const q = buildQuery({date_from: document.getElementById('f-date-from').value || new Date(Date.now()-6*864e5).toISOString().split('T')[0]});
  const res = await fetch(ROUTES.bar + q);
  const d   = await res.json();
  // Legend
  const legend = document.getElementById('bar-legend');
  legend.innerHTML = d.datasets.map(ds=>`<span style="display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:2px;background:${ds.backgroundColor};display:inline-block"></span>${ds.label} ${totalPct(d,ds.label)}%</span>`).join('');
  if(barChartInst) barChartInst.destroy();
  barChartInst = new Chart(document.getElementById('barChart'), {
    type:'bar',
    data:{
      labels: d.labels,
      datasets: d.datasets.map(ds=>({label:ds.label, data:ds.data, backgroundColor:ds.backgroundColor+'cc', borderColor:ds.backgroundColor, borderWidth:1, borderRadius:3}))
    },
    options:{
      responsive:true, maintainAspectRatio:true,
      scales:{x:{stacked:true, grid:{color:'rgba(255,255,255,.04)'}, ticks:{color:'#64748b', font:{size:11}}}, y:{stacked:true, grid:{color:'rgba(255,255,255,.04)'}, ticks:{color:'#64748b', font:{size:11}}}},
      plugins:{legend:{display:false}, tooltip:{mode:'index', intersect:false}},
    }
  });
}
function totalPct(d, label){
  const ds = d.datasets.find(x=>x.label===label);
  if(!ds) return 0;
  const total = d.datasets.reduce((s,x)=>s+x.data.reduce((a,b)=>a+b,0),0);
  const mine  = ds.data.reduce((a,b)=>a+b,0);
  return total>0 ? Math.round(mine/total*100) : 0;
}

// ── By Department ───────────────────────────────────────
async function loadByDept(){
  const res = await fetch(ROUTES.dept + buildQuery());
  const d   = await res.json();
  const list = document.getElementById('dept-list');
  if(!d || d.length===0){ list.innerHTML='<div style="color:var(--dim);font-size:13px;padding:10px 0;text-align:center">No departments</div>'; return; }
  list.innerHTML = d.map(dept=>`
    <div class="dept-row">
      <div>
        <div class="dept-name">${dept.name}</div>
        <div class="dept-mini-bars">
          ${dept.breakdown.map(b=>`<div class="dept-mini-bar" style="background:${b.color};width:${Math.max(b.count*2,4)}px"></div>`).join('')}
        </div>
      </div>
      <div class="dept-count">${dept.total_reacts}</div>
    </div>
  `).join('');
}

// ═══════════════════════════════════════════════════
// EXPORT
// ═══════════════════════════════════════════════════
function exportExcel(){
  const url = ROUTES.export + buildQuery();
  window.location.href = url;
  toast('Downloading Excel report...', 'success');
}

// ═══════════════════════════════════════════════════
// MODALS
// ═══════════════════════════════════════════════════
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function closeModalOutside(e,id){ if(e.target.id===id) closeModal(id); }

async function saveDepartment(){
  const name = document.getElementById('dept-name-input').value.trim();
  if(!name) return toast('Please enter a department name','error');
  const res = await fetch(ROUTES.deptStore, {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({name})
  });
  const d = await res.json();
  if(d.success){
    toast('Department added!','success');
    closeModal('dept-modal');
    document.getElementById('dept-name-input').value='';
    addDeptToDropdowns(d.department);
    loadByDept();
  } else { toast('Error saving department','error'); }
}

async function saveSection(){
  const name   = document.getElementById('section-name-input').value.trim();
  const deptId = document.getElementById('section-dept-select').value;
  if(!name)   return toast('Please enter a section name','error');
  if(!deptId) return toast('Please select a department','error');
  const res = await fetch(ROUTES.sectStore, {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({name, department_id: deptId})
  });
  const d = await res.json();
  if(d.success){
    toast('Section added!','success');
    closeModal('section-modal');
    document.getElementById('section-name-input').value='';
  } else { toast('Error saving section','error'); }
}

function addDeptToDropdowns(dept){
  ['f-dept','section-dept-select'].forEach(id=>{
    const sel = document.getElementById(id);
    const opt = document.createElement('option');
    opt.value = dept.id; opt.textContent = dept.name;
    sel.appendChild(opt);
  });
}

// ═══════════════════════════════════════════════════
// TOAST
// ═══════════════════════════════════════════════════
function toast(msg, type='success'){
  const el = document.getElementById('toast');
  el.className = 'toast ' + type;
  document.getElementById('toast-msg').textContent = msg;
  el.classList.add('show');
  setTimeout(()=>el.classList.remove('show'), 3000);
}

// ═══════════════════════════════════════════════════
// LOGS TABLE
// ═══════════════════════════════════════════════════
async function loadLogs(page = 1) {
  currentPage = page;
  const tbody = document.getElementById('logs-tbody');
  tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>';
  
  const q = buildQuery({ page: currentPage });
  const res = await fetch(ROUTES.logs + q);
  const data = await res.json();
  
  const logs = data.data;
  if (!logs || logs.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">No records found.</td></tr>';
    document.getElementById('reports-info').textContent = 'Showing 0 to 0 of 0 logs';
    document.getElementById('prev-page').disabled = true;
    document.getElementById('next-page').disabled = true;
    return;
  }
  
  tbody.innerHTML = logs.map(log => {
      const empName = log.user ? log.user.name : 'Unknown';
      const empId = log.user ? log.user.employee_id : '-';
      const dept = log.department ? log.department.name : '-';
      const sect = log.section ? log.section.name : '-';
      const reactType = log.react_type ? log.react_type.type : 'N/A';
      const icon = log.react_type ? log.react_type.icon_code : '';
      
      // Determine color based on ID
      let color = '#6b7280';
      if(log.react_type){
         const id = log.react_type.id;
         if(id===1) color = '#22c55e';
         else if(id===2) color = '#3b82f6';
         else if(id===3) color = '#eab308';
         else if(id===4) color = '#f97316';
         else if(id===5) color = '#ef4444';
      }
      
      const dateStr = log.created_at ? log.created_at.split('T')[0] + ' ' + log.created_at.split('T')[1].substring(0, 5) : '-';

      // Selected options from option_submission joined on server
      const selectedOpts = log.selected_options || null;
      const isNoSel = selectedOpts === 'No Selection' || selectedOpts === null;
      const optsBadge = selectedOpts
        ? `<span style="font-size:11px;padding:2px 7px;border-radius:5px;background:${isNoSel ? 'rgba(107,114,128,.1)' : 'rgba(34,197,94,.1)'};color:${isNoSel ? '#6b7280' : 'var(--green)'}">${selectedOpts}</span>`
        : `<span style="font-size:11px;color:var(--dim)">—</span>`;
      
      return `
        <tr>
          <td style="color:var(--muted)">${dateStr}</td>
          <td>
            <div style="font-weight:600;color:var(--text)">${empName}</div>
            <div style="font-size:11px;color:var(--dim)">ID: ${empId}</div>
          </td>
          <td>
            <div style="color:var(--text)">${dept}</div>
            <div style="font-size:11px;color:var(--dim)">${sect}</div>
          </td>
          <td>
            <span class="react-badge" style="background:${color}20;color:${color};border:1px solid ${color}40">
              ${icon} ${reactType}
            </span>
          </td>
          <td>${optsBadge}</td>
          <td>
            <div style="font-size:11px;color:var(--muted)">${log.ip_address || '-'}</div>
            <div style="font-size:11px;color:var(--dim)">${log.device_info ? log.device_info.substring(0, 20) : '-'}</div>
          </td>
          <td style="font-size:12px;color:var(--dim)">${log.note || '-'}</td>
        </tr>
      `;
  }).join('');
  
  document.getElementById('reports-info').textContent = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} logs`;
  document.getElementById('prev-page').disabled = !data.prev_page_url;
  document.getElementById('next-page').disabled = !data.next_page_url;
}

function changePage(delta) {
  loadLogs(currentPage + delta);
}

// ═══════════════════════════════════════════════════
// SECTION / NAV
// ═══════════════════════════════════════════════════
function showSection(id){
  document.querySelectorAll('.tab-section').forEach(el => el.style.display = 'none');
  document.getElementById(id).style.display = 'block';
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  const btn = Array.from(document.querySelectorAll('.nav-item')).find(b => b.getAttribute('onclick') && b.getAttribute('onclick').includes(id));
  if(btn) btn.classList.add('active');
  if (id === 'reports-section') { loadLogs(currentPage); }
  if (id === 'options-section') { loadOptions(); }
  if (id === 'departments-tab-section') { loadDepartments(); }
  if (id === 'sections-tab-section') { loadSections(); }
}

// ═══════════════════════════════════════════════════
// OPTIONS MANAGEMENT
// ═══════════════════════════════════════════════════
async function loadOptions() {
  const tbody = document.getElementById('options-tbody');
  const countEl = document.getElementById('options-count');
  tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>';
  try {
    const res = await fetch(ROUTES.optionsIndex);
    const options = await res.json();
    countEl.textContent = options.length + ' total';
    if (!options || options.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--dim)">No options found. Add one above.</td></tr>';
      return;
    }
    tbody.innerHTML = options.map(opt => {
      const isActive = opt.is_active == 1 || opt.is_active === true;
      const statusColor = isActive ? 'var(--green)' : 'var(--red)';
      const statusBg   = isActive ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.12)';
      const statusBdr  = isActive ? 'rgba(34,197,94,.25)' : 'rgba(239,68,68,.25)';
      const statusLabel = isActive ? 'Active' : 'Inactive';
      const createdAt = opt.created_at ? opt.created_at.split('T')[0] : '-';
      return `
        <tr id="option-row-${opt.id}">
          <td style="color:var(--dim);font-weight:600">${opt.id}</td>
          <td style="font-weight:500;color:var(--text)">${escHtml(opt.name)}</td>
          <td>
            <span
              class="react-badge"
              style="background:${statusBg};color:${statusColor};border:1px solid ${statusBdr};cursor:pointer"
              onclick="toggleOptionStatus(${opt.id}, ${isActive ? 1 : 0})"
              title="Click to toggle status"
            >
              ${isActive ? '● Active' : '○ Inactive'}
            </span>
          </td>
          <td style="color:var(--muted)">${createdAt}</td>
          <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center">
              <button
                onclick="editOption(${opt.id}, '${escJs(opt.name)}')"
                style="background:rgba(59,130,246,.15);color:var(--blue);border:1px solid rgba(59,130,246,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(59,130,246,.3)'"
                onmouseout="this.style.background='rgba(59,130,246,.15)'"
              >✏ Edit</button>
              <button
                onclick="deleteOption(${opt.id})"
                style="background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(239,68,68,.25)'"
                onmouseout="this.style.background='rgba(239,68,68,.12)'"
              >✕ Delete</button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red)">Failed to load options.</td></tr>';
    console.error(e);
  }
}

async function saveOption() {
  const nameInput = document.getElementById('option-name-input');
  const errEl     = document.getElementById('option-form-error');
  const saveBtn   = document.getElementById('option-save-btn');
  const name = nameInput.value.trim();
  errEl.style.display = 'none';
  if (!name) { errEl.textContent = 'Option name is required.'; errEl.style.display = 'block'; return; }
  saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
  try {
    const res = await fetch(ROUTES.optionsStore, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name })
    });
    const d = await res.json();
    if (d.success) {
      toast('Option saved successfully!', 'success');
      nameInput.value = '';
      loadOptions();
    } else {
      const msg = d.errors && d.errors.name ? d.errors.name[0] : 'Failed to save option.';
      errEl.textContent = msg; errEl.style.display = 'block';
    }
  } catch(e) {
    errEl.textContent = 'Network error. Please try again.'; errEl.style.display = 'block';
  } finally {
    saveBtn.disabled = false; saveBtn.textContent = '➕ Save Option';
  }
}

function editOption(id, name) {
  document.getElementById('edit-option-id').value   = id;
  document.getElementById('edit-option-name').value = name;
  openModal('option-edit-modal');
}

async function updateOption() {
  const id   = document.getElementById('edit-option-id').value;
  const name = document.getElementById('edit-option-name').value.trim();
  if (!name) return toast('Option name is required.', 'error');
  try {
    const res = await fetch(`/options/${id}/update`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name })
    });
    const d = await res.json();
    if (d.success) {
      toast('Option updated!', 'success');
      closeModal('option-edit-modal');
      loadOptions();
    } else {
      toast('Failed to update option.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'updateOption');
  }
}

async function deleteOption(id) {
  if (!confirm('Are you sure you want to delete this option? This action cannot be undone.')) return;
  try {
    const res = await fetch(`/options/${id}/delete`, {
      method: 'POST',
      headers: { 
        'X-CSRF-TOKEN': CSRF
      }
    });
    const d = await res.json();
    if (d.success) {
      toast('Option deleted.', 'success');
      // Animate row removal
      const row = document.getElementById('option-row-' + id);
      if (row) { row.style.opacity = '0'; row.style.transition = 'opacity .3s'; setTimeout(() => loadOptions(), 300); }
      else loadOptions();
    } else {
      toast('Failed to delete option.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'deleteOption');
  }
}

async function toggleOptionStatus(id, currentActive) {
  const newStatus = currentActive === 1 ? 0 : 1;
  try {
    const res = await fetch(`/options/${id}/toggle`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ is_active: newStatus })
    });
    const d = await res.json();
    if (d.success) {
      toast(newStatus ? 'Option activated!' : 'Option deactivated.', 'success');
      loadOptions();
    } else {
      toast('Failed to update status.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'toggleOptionStatus');
  }
}

// ═══════════════════════════════════════════════════
// DEPARTMENTS MANAGEMENT
// ═══════════════════════════════════════════════════
async function loadDepartments() {
  const tbody = document.getElementById('departments-tbody');
  const countEl = document.getElementById('departments-count');
  tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>';
  try {
    const res = await fetch(ROUTES.deptIndex);
    const departments = await res.json();
    countEl.textContent = departments.length + ' total';
    if (!departments || departments.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--dim)">No departments found. Add one above.</td></tr>';
      return;
    }
    tbody.innerHTML = departments.map(dept => {
      const isActive = dept.is_active == 1 || dept.is_active === true;
      const statusColor = isActive ? 'var(--green)' : 'var(--red)';
      const statusBg   = isActive ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.12)';
      const statusBdr  = isActive ? 'rgba(34,197,94,.25)' : 'rgba(239,68,68,.25)';
      const createdAt = dept.created_at ? dept.created_at.split('T')[0] : '-';
      return `
        <tr id="dept-row-${dept.id}">
          <td style="color:var(--dim);font-weight:600">${dept.id}</td>
          <td style="font-weight:500;color:var(--text)">${escHtml(dept.name)}</td>
          <td>
            <span
              class="react-badge"
              style="background:${statusBg};color:${statusColor};border:1px solid ${statusBdr};cursor:pointer"
              onclick="toggleDepartmentStatus(${dept.id}, ${isActive ? 1 : 0})"
              title="Click to toggle status"
            >
              ${isActive ? '● Active' : '○ Inactive'}
            </span>
          </td>
          <td style="color:var(--muted)">${createdAt}</td>
          <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center">
              <button
                onclick="editDepartment(${dept.id}, '${escJs(dept.name)}')"
                style="background:rgba(59,130,246,.15);color:var(--blue);border:1px solid rgba(59,130,246,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(59,130,246,.3)'"
                onmouseout="this.style.background='rgba(59,130,246,.15)'"
              >✏ Edit</button>
              <button
                onclick="deleteDepartment(${dept.id})"
                style="background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(239,68,68,.25)'"
                onmouseout="this.style.background='rgba(239,68,68,.12)'"
              >✕ Delete</button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--red)">Failed to load departments.</td></tr>';
    console.error(e);
  }
}

async function saveDepartmentFromTab() {
  const nameInput = document.getElementById('dept-name-input-tab');
  const errEl     = document.getElementById('dept-form-error-tab');
  const saveBtn   = document.getElementById('dept-save-btn-tab');
  const name = nameInput.value.trim();
  errEl.style.display = 'none';
  if (!name) { errEl.textContent = 'Department name is required.'; errEl.style.display = 'block'; return; }
  saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
  try {
    const res = await fetch(ROUTES.deptStore, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name })
    });
    const d = await res.json();
    if (d.success) {
      toast('Department saved successfully!', 'success');
      nameInput.value = '';
      loadDepartments();
      refreshDropdowns();
      loadByDept();
    } else {
      const msg = d.errors && d.errors.name ? d.errors.name[0] : 'Failed to save department.';
      errEl.textContent = msg; errEl.style.display = 'block';
    }
  } catch(e) {
    errEl.textContent = 'Network error. Please try again.'; errEl.style.display = 'block';
  } finally {
    saveBtn.disabled = false; saveBtn.textContent = '➕ Save Department';
  }
}

function editDepartment(id, name) {
  document.getElementById('edit-dept-id').value   = id;
  document.getElementById('edit-dept-name').value = name;
  openModal('dept-edit-modal');
}

async function updateDepartment() {
  const id   = document.getElementById('edit-dept-id').value;
  const name = document.getElementById('edit-dept-name').value.trim();
  if (!name) return toast('Department name is required.', 'error');
  try {
    const res = await fetch(`/departments/${id}/update`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name })
    });
    const d = await res.json();
    if (d.success) {
      toast('Department updated!', 'success');
      closeModal('dept-edit-modal');
      loadDepartments();
      refreshDropdowns();
      loadByDept();
    } else {
      toast('Failed to update department.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'updateDepartment');
  }
}

async function deleteDepartment(id) {
  if (!confirm('Are you sure you want to delete this department? This will delete or decouple sections, employees and logs associated with it.')) return;
  try {
    const res = await fetch(`/departments/${id}/delete`, {
      method: 'POST',
      headers: { 
        'X-CSRF-TOKEN': CSRF
      }
    });
    const d = await res.json();
    if (d.success) {
      toast('Department deleted.', 'success');
      const row = document.getElementById('dept-row-' + id);
      if (row) { row.style.opacity = '0'; row.style.transition = 'opacity .3s'; setTimeout(() => { loadDepartments(); loadByDept(); }, 300); }
      else { loadDepartments(); loadByDept(); }
      refreshDropdowns();
    } else {
      toast('Failed to delete department.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'deleteDepartment');
  }
}

async function toggleDepartmentStatus(id, currentActive) {
  const newStatus = currentActive === 1 ? 0 : 1;
  try {
    const res = await fetch(`/departments/${id}/toggle`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ is_active: newStatus })
    });
    const d = await res.json();
    if (d.success) {
      toast(newStatus ? 'Department activated!' : 'Department deactivated.', 'success');
      loadDepartments();
      refreshDropdowns();
      loadByDept();
    } else {
      toast('Failed to update status.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'toggleDepartmentStatus');
  }
}

// ═══════════════════════════════════════════════════
// SECTIONS MANAGEMENT
// ═══════════════════════════════════════════════════
async function loadSections() {
  const tbody = document.getElementById('sections-tbody');
  const countEl = document.getElementById('sections-count');
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Loading...</td></tr>';
  try {
    const res = await fetch(ROUTES.sectIndex);
    const sections = await res.json();
    countEl.textContent = sections.length + ' total';
    if (!sections || sections.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--dim)">No sections found. Add one above.</td></tr>';
      return;
    }
    tbody.innerHTML = sections.map(sect => {
      const isActive = sect.is_active == 1 || sect.is_active === true;
      const statusColor = isActive ? 'var(--green)' : 'var(--red)';
      const statusBg   = isActive ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.12)';
      const statusBdr  = isActive ? 'rgba(34,197,94,.25)' : 'rgba(239,68,68,.25)';
      const createdAt = sect.created_at ? sect.created_at.split('T')[0] : '-';
      const deptName = sect.department ? sect.department.name : 'Unknown Department';
      return `
        <tr id="section-row-${sect.id}">
          <td style="color:var(--dim);font-weight:600">${sect.id}</td>
          <td style="font-weight:500;color:var(--text)">${escHtml(sect.name)}</td>
          <td style="color:var(--muted)">${escHtml(deptName)}</td>
          <td>
            <span
              class="react-badge"
              style="background:${statusBg};color:${statusColor};border:1px solid ${statusBdr};cursor:pointer"
              onclick="toggleSectionStatus(${sect.id}, ${isActive ? 1 : 0})"
              title="Click to toggle status"
            >
              ${isActive ? '● Active' : '○ Inactive'}
            </span>
          </td>
          <td style="color:var(--muted)">${createdAt}</td>
          <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center">
              <button
                onclick="editSection(${sect.id}, '${escJs(sect.name)}', ${sect.department_id})"
                style="background:rgba(59,130,246,.15);color:var(--blue);border:1px solid rgba(59,130,246,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(59,130,246,.3)'"
                onmouseout="this.style.background='rgba(59,130,246,.15)'"
              >✏ Edit</button>
              <button
                onclick="deleteSection(${sect.id})"
                style="background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.25);border-radius:6px;padding:5px 10px;font-size:11px;cursor:pointer;transition:.15s"
                onmouseover="this.style.background='rgba(239,68,68,.25)'"
                onmouseout="this.style.background='rgba(239,68,68,.12)'"
              >✕ Delete</button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--red)">Failed to load sections.</td></tr>';
    console.error(e);
  }
}

async function saveSectionFromTab() {
  const nameInput = document.getElementById('section-name-input-tab');
  const deptSelect = document.getElementById('section-dept-select-tab');
  const errEl     = document.getElementById('section-form-error-tab');
  const saveBtn   = document.getElementById('section-save-btn-tab');
  const name   = nameInput.value.trim();
  const deptId = deptSelect.value;
  errEl.style.display = 'none';
  if (!deptId) { errEl.textContent = 'Department must be selected.'; errEl.style.display = 'block'; return; }
  if (!name) { errEl.textContent = 'Section name is required.'; errEl.style.display = 'block'; return; }
  saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
  try {
    const res = await fetch(ROUTES.sectStore, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name, department_id: deptId })
    });
    const d = await res.json();
    if (d.success) {
      toast('Section saved successfully!', 'success');
      nameInput.value = '';
      deptSelect.value = '';
      loadSections();
    } else {
      const msg = d.errors && d.errors.name ? d.errors.name[0] : 'Failed to save section.';
      errEl.textContent = msg; errEl.style.display = 'block';
    }
  } catch(e) {
    errEl.textContent = 'Network error. Please try again.'; errEl.style.display = 'block';
  } finally {
    saveBtn.disabled = false; saveBtn.textContent = '➕ Save Section';
  }
}

function editSection(id, name, deptId) {
  document.getElementById('edit-section-id').value   = id;
  document.getElementById('edit-section-name').value = name;
  document.getElementById('edit-section-dept-id').value = deptId;
  openModal('section-edit-modal');
}

async function updateSection() {
  const id     = document.getElementById('edit-section-id').value;
  const name   = document.getElementById('edit-section-name').value.trim();
  const deptId = document.getElementById('edit-section-dept-id').value;
  if (!deptId) return toast('Department must be selected.', 'error');
  if (!name) return toast('Section name is required.', 'error');
  try {
    const res = await fetch(`/sections/${id}/update`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ name, department_id: deptId })
    });
    const d = await res.json();
    if (d.success) {
      toast('Section updated!', 'success');
      closeModal('section-edit-modal');
      loadSections();
    } else {
      toast('Failed to update section.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'updateSection');
  }
}

async function deleteSection(id) {
  if (!confirm('Are you sure you want to delete this section? This action cannot be undone.')) return;
  try {
    const res = await fetch(`/sections/${id}/delete`, {
      method: 'POST',
      headers: { 
        'X-CSRF-TOKEN': CSRF
      }
    });
    const d = await res.json();
    if (d.success) {
      toast('Section deleted.', 'success');
      const row = document.getElementById('section-row-' + id);
      if (row) { row.style.opacity = '0'; row.style.transition = 'opacity .3s'; setTimeout(() => loadSections(), 300); }
      else loadSections();
    } else {
      toast('Failed to delete section.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'deleteSection');
  }
}

async function toggleSectionStatus(id, currentActive) {
  const newStatus = currentActive === 1 ? 0 : 1;
  try {
    const res = await fetch(`/sections/${id}/toggle`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ is_active: newStatus })
    });
    const d = await res.json();
    if (d.success) {
      toast(newStatus ? 'Section activated!' : 'Section deactivated.', 'success');
      loadSections();
    } else {
      toast('Failed to update status.', 'error');
    }
  } catch(e) {
    handleFetchError(e, 'toggleSectionStatus');
  }
}

// Rebuild/refresh all department dropdown select elements in UI
async function refreshDropdowns() {
  try {
    const res = await fetch(ROUTES.deptIndex);
    const departments = await res.json();
    const activeDepts = departments.filter(d => d.is_active == 1 || d.is_active === true);
    const dropdownIds = [
      'f-dept', 
      'section-dept-select', 
      'section-dept-select-tab', 
      'edit-section-dept-id'
    ];
    dropdownIds.forEach(id => {
      const selectEl = document.getElementById(id);
      if (!selectEl) return;
      const currentVal = selectEl.value;
      const firstOpt = selectEl.options[0];
      selectEl.innerHTML = '';
      if (firstOpt) {
        selectEl.appendChild(firstOpt);
      }
      activeDepts.forEach(dept => {
        const opt = document.createElement('option');
        opt.value = dept.id;
        opt.textContent = dept.name;
        selectEl.appendChild(opt);
      });
      selectEl.value = currentVal;
    });
  } catch(e) {
    console.error('Error refreshing dropdowns', e);
  }
}

// Helpers
function handleFetchError(e, context) {
  console.error(`${context} error:`, e);
  toast(`Error (${context}): ${e.message}`, 'error');
}
function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escJs(str) {
  return String(str).replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"');
}

// Allow Enter key on inputs
document.addEventListener('DOMContentLoaded', () => {
  const inp = document.getElementById('option-name-input');
  if (inp) inp.addEventListener('keydown', e => { if (e.key === 'Enter') saveOption(); });
  const editInp = document.getElementById('edit-option-name');
  if (editInp) editInp.addEventListener('keydown', e => { if (e.key === 'Enter') updateOption(); });

  const deptInp = document.getElementById('dept-name-input-tab');
  if (deptInp) deptInp.addEventListener('keydown', e => { if (e.key === 'Enter') saveDepartmentFromTab(); });
  const editDeptInp = document.getElementById('edit-dept-name');
  if (editDeptInp) editDeptInp.addEventListener('keydown', e => { if (e.key === 'Enter') updateDepartment(); });

  const sectInp = document.getElementById('section-name-input-tab');
  if (sectInp) sectInp.addEventListener('keydown', e => { if (e.key === 'Enter') saveSectionFromTab(); });
  const editSectInp = document.getElementById('edit-section-name');
  if (editSectInp) editSectInp.addEventListener('keydown', e => { if (e.key === 'Enter') updateSection(); });
});

// ═══════════════════════════════════════════════════
// OPTIONS DASHBOARD ANALYTICS
// ═══════════════════════════════════════════════════
async function loadOptionDashboardStats() {
  try {
    const res = await fetch(ROUTES.optionDashStats + buildQuery());
    const d   = await res.json();
    document.getElementById('or-total').textContent  = d.total_submissions.toLocaleString();
    document.getElementById('or-no-sel').textContent = d.no_selection_count.toLocaleString();
    document.getElementById('or-top').textContent    = d.top_option ? d.top_option.name + ' (' + d.top_option.count + ')' : '—';
    document.getElementById('or-part').textContent   = d.participation_pct + '%';

    const list = document.getElementById('or-breakdown');
    const optColors = ['#22c55e','#3b82f6','#eab308','#f97316','#ef4444','#a855f7','#06b6d4','#f43f5e','#10b981','#6366f1'];
    if (!d.option_counts || d.option_counts.length === 0) {
      list.innerHTML = '<div style="color:var(--dim);font-size:13px;padding:12px 0;text-align:center">No option submissions yet.</div>';
      return;
    }
    let html = d.option_counts.map((opt, i) => `
      <div class="breakdown-item">
        <div class="breakdown-row">
          <div class="breakdown-label">
            <div class="breakdown-dot" style="background:${optColors[i % optColors.length]}"></div>
            ${escHtml(opt.name)}
          </div>
          <div class="breakdown-nums">
            <span class="breakdown-count">${opt.count}</span>
            <span class="breakdown-pct" style="color:${optColors[i % optColors.length]}">${opt.percentage}%</span>
          </div>
        </div>
        <div class="breakdown-bar-bg">
          <div class="breakdown-bar-fill" style="width:${opt.percentage}%;background:${optColors[i % optColors.length]}"></div>
        </div>
      </div>
    `).join('');
    if (d.no_selection_count > 0) {
      const noSelPct = d.total_submissions > 0 ? Math.round((d.no_selection_count / d.total_submissions) * 100) : 0;
      html += `<div class="breakdown-item">
        <div class="breakdown-row">
          <div class="breakdown-label"><div class="breakdown-dot" style="background:#6b7280"></div>No Selection</div>
          <div class="breakdown-nums">
            <span class="breakdown-count">${d.no_selection_count}</span>
            <span class="breakdown-pct" style="color:#6b7280">${noSelPct}%</span>
          </div>
        </div>
        <div class="breakdown-bar-bg"><div class="breakdown-bar-fill" style="width:${noSelPct}%;background:#6b7280"></div></div>
      </div>`;
    }
    list.innerHTML = html;
  } catch(e) { console.error('loadOptionDashboardStats', e); }
}

// ═══════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', ()=>{
  loadAll();
});
</script>
</body>
</html>
