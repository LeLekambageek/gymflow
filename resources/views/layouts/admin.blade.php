<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GymFlow') — Administrateur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Bebas+Neue&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0f1117; --bg2:#161b27; --bg3:#1e2535;
            --border:#2a3347; --text:#e8ecf0; --muted:#6b7a99;
            --red:#ef4444; --green:#22c55e; --orange:#f97316; --blue:#3b82f6;
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
        .topbar-logo span{color:var(--red);}
        .topbar-right{display:flex;align-items:center;gap:8px;}
        .topbar-date{font-size:11px;color:var(--muted);font-family:var(--font-mono);}
        .role-pill{font-size:10px;font-weight:600;padding:3px 8px;border-radius:20px;background:rgba(239,68,68,0.15);color:var(--red);border:1px solid rgba(239,68,68,0.25);letter-spacing:0.5px;}

        /* ── OVERLAY ── */
        .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:299;backdrop-filter:blur(2px);}
        .overlay.open{display:block;}

        /* ── SIDEBAR ── */
        .sidebar{width:var(--sidebar-w);background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:300;transform:translateX(-100%);transition:transform 0.25s ease;}
        .sidebar.open{transform:translateX(0);}
        .sidebar-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
        .sidebar-logo-wrap{display:flex;align-items:center;gap:10px;}
        .logo-icon{width:32px;height:32px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;}
        .logo-text{font-family:var(--font-num);font-size:20px;letter-spacing:2px;}
        .logo-sub{font-size:9px;color:var(--muted);letter-spacing:1px;text-transform:uppercase;}
        .sidebar-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:20px;padding:4px;}
        .sidebar-close:hover{color:var(--text);}
        .role-badge{margin:10px 14px;padding:5px 10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:6px;font-size:10px;font-weight:600;color:var(--red);letter-spacing:1px;text-align:center;text-transform:uppercase;}
        .sidebar-nav{flex:1;padding:6px 0;overflow-y:auto;}
        .nav-section{padding:8px 16px 3px;font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 20px;color:var(--muted);text-decoration:none;border-left:3px solid transparent;transition:all 0.15s;font-size:14px;}
        .nav-link:hover{color:var(--text);background:var(--bg3);}
        .nav-link.active{color:var(--red);border-left-color:var(--red);background:rgba(239,68,68,0.08);font-weight:500;}
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

        .card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin-bottom:16px;}
        .card-title{font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all 0.15s;font-family:var(--font);white-space:nowrap;min-height:40px;}
        .btn-primary{background:var(--red);color:white;}
        .btn-primary:hover{background:#dc2626;}
        .btn-secondary{background:var(--bg3);color:var(--text);border:1px solid var(--border);}
        .btn-secondary:hover{background:var(--border);}
        .btn-danger{background:rgba(239,68,68,0.15);color:var(--red);border:1px solid rgba(239,68,68,0.3);}
        .btn-sm{padding:6px 12px;font-size:12px;min-height:32px;}

        .table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border);-webkit-overflow-scrolling:touch;}
        table{width:100%;border-collapse:collapse;background:var(--bg2);min-width:500px;}
        thead th{padding:10px 14px;text-align:left;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);background:var(--bg3);border-bottom:1px solid var(--border);white-space:nowrap;}
        tbody td{padding:11px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover{background:var(--bg3);}

        .form-group{margin-bottom:14px;}
        label{display:block;font-size:11px;font-weight:600;letter-spacing:0.5px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;}
        input,select,textarea{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:var(--text);font-family:var(--font);font-size:15px;transition:border-color 0.15s;-webkit-appearance:none;}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--red);}
        input::placeholder{color:var(--muted);}
        select option{background:var(--bg3);}

        .badge{display:inline-flex;align-items:center;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:600;}
        .badge-admin{background:rgba(239,68,68,0.15);color:var(--red);}
        .badge-owner{background:rgba(168,85,247,0.15);color:#d8b4fe;}
        .badge-manager{background:rgba(34,197,94,0.15);color:var(--green);}

        .avatar{width:34px;height:34px;border-radius:50%;background:var(--bg3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--red);flex-shrink:0;}

        .alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px;}
        .alert-success{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#4ade80;}
        .alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;}

        .modal{position:fixed;inset:0;background:rgba(0,0,0,0.65);display:none;align-items:center;justify-content:center;z-index:400;backdrop-filter:blur(2px);}
        .modal.open{display:flex;}
        .modal-content{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);width:100%;max-width:500px;max-height:90vh;overflow-y:auto;}
        .modal-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--bg2);z-index:1;}
        .modal-title{font-family:var(--font-num);font-size:18px;letter-spacing:1px;}
        .modal-body{padding:20px;}
        .modal-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:22px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;}
        .modal-close:hover{background:var(--bg3);color:var(--text);}

        .text-muted{color:var(--muted);} .text-red{color:var(--red);} .text-green{color:var(--green);}
        .text-sm{font-size:12px;} .fw-600{font-weight:600;}
        .hidden{display:none;}

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
        }
    </style>
</head>
<body>
    <!-- Overlay mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-wrap">
                <div class="logo-icon">⚙️</div>
                <div>
                    <div class="logo-text">GYMFLOW</div>
                    <div class="logo-sub">ADMIN</div>
                </div>
            </div>
            <button class="sidebar-close" id="sidebarClose">×</button>
        </div>

        <div class="role-badge">ADMINISTRATEUR</div>

        <div class="sidebar-nav">
            <div class="nav-section">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
                📊 Gestion des utilisateurs
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                🚪 Déconnexion
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user">{{ auth()->user()->name }}</div>
            <div class="sidebar-role">{{ auth()->user()->role }}</div>
        </div>
    </div>

    <!-- Topbar Mobile -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <span class="topbar-logo">GYM<span>FLOW</span></span>
        </div>
        <div class="topbar-right">
            <span class="role-pill">ADMIN</span>
            <span class="topbar-date" id="topbarDate"></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('hamburger')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('open');
        });

        document.getElementById('sidebarClose')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        });

        document.getElementById('overlay')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        });

        // Date display
        document.getElementById('topbarDate').textContent = new Date().toLocaleDateString('fr-FR', {
            month: 'short', day: 'numeric'
        });
    </script>
</body>
</html>
