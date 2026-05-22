<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GymFlow') — Propriétaire</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Bebas+Neue&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0f1117; --bg2:#161b27; --bg3:#1e2535;
            --border:#2a3347; --text:#e8ecf0; --muted:#6b7a99;
            --orange:#f97316; --green:#22c55e; --red:#ef4444; --blue:#3b82f6;
            --radius:10px; --sidebar-w:240px;
            --font:'DM Sans',sans-serif; --font-num:'Bebas Neue',sans-serif; --font-mono:'DM Mono',monospace;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;}

        /* ── TOPBAR MOBILE ── */
        .topbar{height:56px;background:var(--bg2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 16px;position:sticky;top:0;z-index:200;}
        .topbar-left{display:flex;align-items:center;gap:12px;}
        .hamburger{background:none;border:none;color:var(--text);cursor:pointer;padding:6px;border-radius:8px;display:flex;align-items:center;justify-content:center;}
        .hamburger:hover{background:var(--bg3);}
        .hamburger svg{width:22px;height:22px;}
        .topbar-logo{font-family:var(--font-num);font-size:20px;letter-spacing:2px;color:var(--text);}
        .topbar-logo span{color:var(--orange);}
        .topbar-right{display:flex;align-items:center;gap:8px;}
        .topbar-date{font-size:11px;color:var(--muted);font-family:var(--font-mono);}
        .role-pill{font-size:10px;font-weight:600;padding:3px 8px;border-radius:20px;background:rgba(249,115,22,0.15);color:var(--orange);border:1px solid rgba(249,115,22,0.25);letter-spacing:0.5px;}

        /* ── OVERLAY ── */
        .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:299;backdrop-filter:blur(2px);}
        .overlay.open{display:block;}

        /* ── SIDEBAR ── */
        .sidebar{width:var(--sidebar-w);background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:300;transform:translateX(-100%);transition:transform 0.25s ease;}
        .sidebar.open{transform:translateX(0);}
        .sidebar-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
        .sidebar-logo-wrap{display:flex;align-items:center;gap:10px;}
        .logo-icon{width:32px;height:32px;background:var(--orange);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;}
        .logo-text{font-family:var(--font-num);font-size:20px;letter-spacing:2px;}
        .logo-sub{font-size:9px;color:var(--muted);letter-spacing:1px;text-transform:uppercase;}
        .sidebar-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:20px;padding:4px;}
        .sidebar-close:hover{color:var(--text);}
        .role-badge{margin:10px 14px;padding:5px 10px;background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.25);border-radius:6px;font-size:10px;font-weight:600;color:var(--orange);letter-spacing:1px;text-align:center;text-transform:uppercase;}
        .sidebar-nav{flex:1;padding:6px 0;overflow-y:auto;}
        .nav-section{padding:8px 16px 3px;font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 20px;color:var(--muted);text-decoration:none;border-left:3px solid transparent;transition:all 0.15s;font-size:14px;}
        .nav-link:hover{color:var(--text);background:var(--bg3);}
        .nav-link.active{color:var(--orange);border-left-color:var(--orange);background:rgba(249,115,22,0.08);font-weight:500;}
        .nav-link svg{width:18px;height:18px;flex-shrink:0;opacity:0.7;}
        .nav-link.active svg{opacity:1;}
        .sidebar-footer{padding:14px 18px;border-top:1px solid var(--border);}
        .sidebar-user{font-size:13px;font-weight:500;margin-bottom:2px;}
        .sidebar-role{font-size:11px;color:var(--muted);margin-bottom:10px;}

        /* ── CONTENU PRINCIPAL ── */
        .page-content{padding:16px;}

        /* ── COMPOSANTS ── */
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;}
        .page-title{font-family:var(--font-num);font-size:24px;letter-spacing:1.5px;}
        .page-subtitle{font-size:12px;color:var(--muted);margin-top:2px;}

        .stats-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;}
        .stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;position:relative;overflow:hidden;}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--accent,var(--orange));}
        .stat-label{font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
        .stat-value{font-family:var(--font-num);font-size:30px;letter-spacing:1px;line-height:1;}
        .stat-note{font-size:10px;color:var(--muted);margin-top:5px;}

        .card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin-bottom:16px;}
        .card-title{font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all 0.15s;font-family:var(--font);white-space:nowrap;min-height:40px;}
        .btn-primary{background:var(--orange);color:white;}
        .btn-primary:hover{background:#ea6a0a;}
        .btn-secondary{background:var(--bg3);color:var(--text);border:1px solid var(--border);}
        .btn-secondary:hover{background:var(--border);}
        .btn-sm{padding:6px 12px;font-size:12px;min-height:32px;}
        .btn-danger{background:rgba(239,68,68,0.15);color:var(--red);border:1px solid rgba(239,68,68,0.3);}
        .btn-full{width:100%;}

        .table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border);-webkit-overflow-scrolling:touch;}
        table{width:100%;border-collapse:collapse;background:var(--bg2);min-width:500px;}
        thead th{padding:10px 14px;text-align:left;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);background:var(--bg3);border-bottom:1px solid var(--border);white-space:nowrap;}
        tbody td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover{background:var(--bg3);}

        .form-group{margin-bottom:14px;}
        label{display:block;font-size:11px;font-weight:600;letter-spacing:0.5px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;}
        input,select,textarea{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:var(--text);font-family:var(--font);font-size:15px;transition:border-color 0.15s;-webkit-appearance:none;}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--orange);}
        input::placeholder{color:var(--muted);}
        select option{background:var(--bg3);}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
        .form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}

        .badge{display:inline-flex;align-items:center;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:600;}
        .badge-active,.badge-paid,.badge-confirmed{background:rgba(34,197,94,0.15);color:var(--green);}
        .badge-inactive,.badge-cancelled{background:rgba(107,122,153,0.15);color:var(--muted);}
        .badge-suspended,.badge-failed{background:rgba(239,68,68,0.15);color:var(--red);}
        .badge-pending{background:rgba(249,115,22,0.15);color:var(--orange);}
        .badge-expired,.badge-warning{background:rgba(239,68,68,0.12);color:#f87171;}

        .avatar{width:34px;height:34px;border-radius:50%;background:var(--bg3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--orange);flex-shrink:0;}
        .member-info{display:flex;align-items:center;gap:9px;}
        .member-name{font-weight:500;font-size:13px;}
        .member-sub{font-size:11px;color:var(--muted);}

        .alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px;}
        .alert-success{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#4ade80;}
        .alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;}

        .modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:400;align-items:flex-end;justify-content:center;backdrop-filter:blur(2px);}
        .modal-backdrop.open{display:flex;}
        .modal{background:var(--bg2);border:1px solid var(--border);border-radius:14px 14px 0 0;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;}
        .modal-header{padding:16px 20px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--bg2);z-index:1;}
        .modal-title{font-family:var(--font-num);font-size:18px;letter-spacing:1px;}
        .modal-body{padding:20px;}
        .modal-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:22px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;}
        .modal-close:hover{background:var(--bg3);color:var(--text);}

        .progress{height:4px;background:var(--bg3);border-radius:2px;overflow:hidden;}
        .progress-bar{height:100%;border-radius:2px;background:var(--orange);}
        .divider{border:none;border-top:1px solid var(--border);margin:16px 0;}
        .text-muted{color:var(--muted);} .text-orange{color:var(--orange);} .text-green{color:var(--green);} .text-red{color:var(--red);}
        .text-sm{font-size:12px;} .fw-500{font-weight:500;} .fw-600{font-weight:600;} .mono{font-family:var(--font-mono);}
        .mt-4{margin-top:4px;} .mb-16{margin-bottom:16px;}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
        .empty-state{text-align:center;padding:36px 16px;color:var(--muted);font-size:13px;}
        .tag{display:inline-block;padding:2px 7px;border-radius:4px;font-size:11px;background:var(--bg3);color:var(--muted);border:1px solid var(--border);}
        .filter-bar{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
        .filter-bar input,.filter-bar select{flex:1;min-width:140px;}

        ::-webkit-scrollbar{width:4px;height:4px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}

        /* ── DESKTOP (≥ 768px) ── */
        @media(min-width:768px){
            body{display:flex;}
            .topbar{display:none;}
            .overlay{display:none !important;}
            .sidebar{transform:translateX(0);position:fixed;}
            .main-wrapper{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
            .page-content{padding:24px;}
            .stats-grid{grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;}
            .stat-value{font-size:36px;}
            .page-title{font-size:28px;}
            .modal-backdrop{align-items:center;}
            .modal{border-radius:14px;max-width:520px;}
            .grid-2{gap:20px;}
            .card{padding:20px;}
        }

        @media(max-width:400px){
            .stats-grid{grid-template-columns:1fr 1fr;}
            .stat-value{font-size:26px;}
            .form-grid{grid-template-columns:1fr;}
            .form-grid-3{grid-template-columns:1fr;}
            .grid-2{grid-template-columns:1fr;}
            .grid-3{grid-template-columns:1fr;}
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- TOPBAR MOBILE -->
<header class="topbar">
    <div class="topbar-left">
        <button class="hamburger" onclick="openSidebar()" aria-label="Menu">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="topbar-logo">GYM<span>FLOW</span></div>
    </div>
    <div class="topbar-right">
        <span class="role-pill">👑 Proprio</span>
        <span class="topbar-date mono">{{ now()->format('H:i') }}</span>
    </div>
</header>

<!-- OVERLAY -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-wrap">
            <div class="logo-icon">G</div>
            <div>
                <div class="logo-text">GYMFLOW</div>
                <div class="logo-sub">Espace propriétaire</div>
            </div>
        </div>
        <button class="sidebar-close" onclick="closeSidebar()">✕</button>
    </div>

    <div class="role-badge">👑 Propriétaire</div>

    <nav class="sidebar-nav">
        <div class="nav-section">Finances</div>

        <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('owner.revenue.detail', ['period'=>'today']) }}" class="nav-link {{ request()->routeIs('owner.revenue.detail') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Revenus détaillés
        </a>
        <a href="{{ route('owner.subscriptions') }}" class="nav-link {{ request()->routeIs('owner.subscriptions') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            Abonnements
        </a>
        <a href="{{ route('payments.report') }}" class="nav-link {{ request()->routeIs('payments.report') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Rapport annuel
        </a>

        <div class="nav-section">Gestion</div>

        <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
            Membres
        </a>
        <a href="{{ route('subscriptions.plans') }}" class="nav-link {{ request()->routeIs('subscriptions.plans') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            Plans tarifaires
        </a>
        <a href="{{ route('coaches.index') }}" class="nav-link {{ request()->routeIs('coaches.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
            Coachs
        </a>
        <a href="{{ route('owner.staff.index') }}" class="nav-link {{ request()->routeIs('owner.staff.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/><line x1="19" y1="8" x2="23" y2="8"/><line x1="21" y1="6" x2="21" y2="10"/></svg>
            Gérants & Staff
        </a>
    </nav>

    <div class="sidebar-footer">
        @auth
        <div class="sidebar-user">{{ auth()->user()->name }}</div>
        <div class="sidebar-role">Propriétaire</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm btn-full">Se déconnecter</button>
        </form>
        @endauth
    </div>
</aside>

<!-- WRAPPER DESKTOP -->
<div class="main-wrapper">
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">✗ {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
function openSidebar(){
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
    document.body.style.overflow='';
}
function openModal(id){document.getElementById(id).classList.add('open');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.remove('open');document.body.style.overflow='';}
document.querySelectorAll('.modal-backdrop').forEach(el=>el.addEventListener('click',function(e){if(e.target===this)closeModal(this.id);}));
</script>
@stack('scripts')
</body>
</html>
