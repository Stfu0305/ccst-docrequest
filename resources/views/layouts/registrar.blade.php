<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bell JS reads these URLs from meta tags instead of hardcoding paths --}}
    <meta name="notif-fetch-url"    content="{{ route('registrar.notifications.index') }}">
    <meta name="notif-read-url"     content="{{ route('registrar.notifications.markOneRead', ['id' => '__ID__']) }}">
    <meta name="notif-readall-url"  content="{{ route('registrar.notifications.markAllRead') }}">

    <title>@yield('title', 'CCST DocRequest') — Registrar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Volkhov:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --green-dark:   #1B6B3A;
            --blue-main:    #1A9FE0;
            --blue-dark:    #0D7FBF;
            --yellow:       #F5C518;
            --text-dark:    #1A1A1A;
            --text-gray:    #666666;
            --border:       #D0DDD0;
            --white:        #FFFFFF;
            --sidebar-w:    240px;
            --header-h:     50px;
            --footer-h:     35px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f4;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── HEADER ── */
        .site-header {
            height: var(--header-h);
            background: linear-gradient(to right, #0C6637 13%, #9CDBBA 81%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 200;
            overflow: visible; /* must be visible so bell dropdown is not clipped */
        }

        .site-header .header-title {
            font-family: 'Volkhov', serif;
            font-size: 0.96rem;
            color: var(--white);
            letter-spacing: 0.3px;
            flex: 1;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
            padding-right: 4px;
            position: relative; /* anchor for the dropdown */
        }

        /* ── BELL BUTTON ── */
        .bell-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            padding: 4px;
            line-height: 1;
            opacity: 0.9;
            transition: opacity 0.15s;
        }

        .bell-btn:hover { opacity: 1; }

        .bell-badge {
            position: absolute;
            top: -2px;
            right: -4px;
            background: #DC3545;
            color: white;
            font-size: 0.55rem;
            font-weight: 700;
            min-width: 14px;
            height: 14px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2px;
        }

        /* ── BELL DROPDOWN ── */
        .bell-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 9999;
            overflow: hidden;
        }

        .bell-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: #F0F7F0;
            border-bottom: 1px solid var(--border);
        }

        .bell-dropdown-header span {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        .bell-mark-all {
            background: none;
            border: none;
            font-size: 0.76rem;
            color: var(--blue-main);
            cursor: pointer;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        .bell-mark-all:hover { text-decoration: underline; }

        .bell-dropdown-body {
            max-height: 360px;
            overflow-y: auto;
        }

        .notif-item {
            display: block;
            padding: 10px 14px;
            border-bottom: 1px solid #f2f2f2;
            text-decoration: none;
            color: var(--text-dark);
            transition: background 0.12s;
        }

        .notif-item:hover { background: #f7faf7; }
        .notif-item:last-child { border-bottom: none; }

        .notif-item .notif-msg {
            font-size: 0.83rem;
            line-height: 1.4;
        }

        .notif-item .notif-time {
            font-size: 0.73rem;
            color: var(--text-gray);
            margin-top: 3px;
        }

        .bell-empty {
            text-align: center;
            padding: 28px 14px;
            color: var(--text-gray);
            font-size: 0.83rem;
        }

        .bell-empty i {
            display: block;
            font-size: 1.6rem;
            margin-bottom: 6px;
            opacity: 0.5;
        }

        /* ── PAGE BODY ── */
        .page-body {
            display: flex;
            flex: 1;
            min-height: 0;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 18px 0 14px;
            position: sticky;
            top: var(--header-h);
            height: calc(100vh - var(--header-h) - var(--footer-h));
            overflow-y: auto;
        }

        .sidebar-logo {
            text-align: center;
            padding: 20px 12px 25px;
            margin-bottom: 16px;
        }

        .sidebar-logo img {
            width: 125px;
            height: 125px;
            object-fit: contain;
        }

        .sidebar-avatar {
            text-align: center;
            padding: 0 12px 38px;
            border-bottom: 1px solid var(--border);
            margin-bottom: -8px;
        }

        .avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: var(--green-dark);
            color: white;
            font-size: 1.79rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            overflow: hidden;
            border: 2px solid var(--border);
        }

        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

        .user-name {
            font-weight: 700;
            font-size: 0.99rem;
            color: var(--text-dark);
            line-height: 1.3;
        }

        .role-badge {
            display: inline-block;
            background: var(--yellow);
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .sidebar-nav { flex: 1; padding: 8px 0; }

        .sidebar-nav a {
            display: block;
            padding: 12px 16px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.98rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            text-align: center;
            transition: all 0.15s;
        }

        .sidebar-nav a:hover {
            color: var(--green-dark);
            background: rgba(12,102,55,0.05);
        }

        .sidebar-nav a.active {
            color: var(--text-dark);
            font-weight: 700;
            border-left-color: var(--green-dark);
            background: rgba(12,102,55,0.06);
        }

        .sidebar-footer {
            padding: 12px 14px 0;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-gray);
            font-family: 'Poppins', sans-serif;
            font-size: 0.90rem;
            font-weight: 500;
            padding: 4px 0;
            text-decoration: none;
            transition: color 0.15s;
        }

        .logout-btn:hover { color: var(--text-dark); }

        .logout-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--blue-main);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.92rem;
            flex-shrink: 0;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            flex: 1;
            padding: 28px 24px;
            padding-right: 320px;
            min-width: 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 300px;
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: calc(var(--header-h) + 14px) 14px calc(var(--footer-h) + 14px);
            background: url('{{ asset("images/right-panel-building.png") }}') center/cover no-repeat;
            z-index: 50;
        }

        /* ── SHARED CARD STYLES ── */
        .ccst-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .ccst-card-header {
            background: var(--green-dark);
            color: white;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 9px 16px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .ccst-card-header.yellow {
            background: var(--yellow);
            color: var(--text-dark);
        }

        .ccst-card-header.blue {
            background: var(--blue-main);
            color: white;
        }

        .ccst-card-body {
            padding: 12px 14px;
            font-size: 0.90rem;
            line-height: 1.6;
            color: var(--text-dark);
        }

        .right-panel .ccst-card {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            overflow: hidden;
        }

        .right-panel .ccst-card-body {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: white;
        }

        /* ── FLASH MESSAGES ── */
        .flash-container { margin-bottom: 16px; }

        /* ── FOOTER ── */
        .site-footer {
            height: var(--footer-h);
            background: linear-gradient(to right, #9CDBBA 13%, #0C6637 81%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            z-index: 200;
            width: 100%;
        }

        .site-footer span {
            font-size: 0.72rem;
            color: var(--white);
            font-weight: 500;
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- HEADER --}}
    <header class="site-header">
        <span class="header-title">
            Clark College of Science and Technology's &nbsp; Document Request and Tracking System
        </span>
        <div class="header-right">

            {{-- BELL BUTTON --}}
            <button class="bell-btn" id="bellBtn" title="Notifications" type="button">
                <i class="bi bi-bell-fill"></i>
                <span class="bell-badge" id="bellBadge" style="display:none;">0</span>
            </button>

            {{-- BELL DROPDOWN PANEL --}}
            <div class="bell-dropdown" id="bellDropdown">
                <div class="bell-dropdown-header">
                    <span><i class="bi bi-bell me-1"></i> Notifications</span>
                    <button class="bell-mark-all" id="bellMarkAll" type="button">Mark all as read</button>
                </div>
                <div class="bell-dropdown-body" id="bellDropdownBody">
                    <div class="bell-empty">
                        <i class="bi bi-bell-slash"></i>
                        No new notifications
                    </div>
                </div>
            </div>

        </div>
    </header>

    {{-- PAGE BODY --}}
    <div class="page-body">

        {{-- SIDEBAR --}}
        <aside class="sidebar">

            <div class="sidebar-logo">
                <img src="{{ asset('images/ccst-logo.png') }}" alt="CCST"
                     onerror="this.style.display='none'">
            </div>

            <div class="sidebar-avatar">
                <div class="avatar-circle">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <span class="role-badge">Registrar</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('registrar.dashboard') }}"
                   class="{{ request()->routeIs('registrar.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('registrar.requests.index') }}"
                   class="{{ request()->routeIs('registrar.requests.*') ? 'active' : '' }}">
                    Document Requests
                </a>
                <a href="{{ route('registrar.appointments.index') }}"
                   class="{{ request()->routeIs('registrar.appointments.*') ? 'active' : '' }}">
                    Appointments
                </a>
                <a href="{{ route('registrar.reports.index') }}"
                   class="{{ request()->routeIs('registrar.reports.*') ? 'active' : '' }}">
                    Reports
                </a>
                <a href="{{ route('registrar.account') }}"
                   class="{{ request()->routeIs('registrar.account') ? 'active' : '' }}">
                    Account
                </a>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="logout-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </span>
                        Log Out
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">
            <div class="flash-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>

        {{-- RIGHT PANEL --}}
        <aside class="right-panel">
            @yield('right-panel')
        </aside>

    </div>

    {{-- FOOTER --}}
    <footer class="site-footer">
        <span>© Copyright 2026 Clark College of Science and Technology | Document Request and Tracking System</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    (function () {
        const FETCH_URL    = document.querySelector('meta[name="notif-fetch-url"]').content;
        const READ_URL     = document.querySelector('meta[name="notif-read-url"]').content;
        const READ_ALL_URL = document.querySelector('meta[name="notif-readall-url"]').content;
        const CSRF         = document.querySelector('meta[name="csrf-token"]').content;

        const bellBtn      = document.getElementById('bellBtn');
        const bellBadge    = document.getElementById('bellBadge');
        const bellDropdown = document.getElementById('bellDropdown');
        const bellBody     = document.getElementById('bellDropdownBody');
        const markAllBtn   = document.getElementById('bellMarkAll');

        function esc(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function setBadge(count) {
            if (count > 0) {
                bellBadge.textContent = count > 9 ? '9+' : count;
                bellBadge.style.display = 'flex';
            } else {
                bellBadge.style.display = 'none';
            }
        }

        function renderList(notifications) {
            if (!notifications || notifications.length === 0) {
                bellBody.innerHTML = '<div class="bell-empty"><i class="bi bi-bell-slash"></i>No new notifications</div>';
                return;
            }
            let html = '';
            notifications.forEach(function (n) {
                html += '<a href="' + esc(n.url) + '" class="notif-item" data-id="' + esc(n.id) + '">' +
                        '<div class="notif-msg">' + esc(n.message) + '</div>' +
                        '<div class="notif-time">' + esc(n.time) + '</div></a>';
            });
            bellBody.innerHTML = html;
            bellBody.querySelectorAll('.notif-item').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const url = this.getAttribute('href');
                    fetch(READ_URL.replace('__ID__', id), {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    }).finally(function () { window.location.href = url; });
                });
            });
        }

        function loadNotifications() {
            bellBody.innerHTML = '<div class="bell-empty"><i class="bi bi-arrow-repeat"></i>Loading...</div>';
            fetch(FETCH_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                credentials: 'same-origin',
            })
            .then(function (res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
            .then(function (data) { setBadge(data.count); renderList(data.notifications); })
            .catch(function (err) {
                bellBody.innerHTML = '<div class="bell-empty" style="color:#DC3545;"><i class="bi bi-exclamation-circle"></i>Could not load notifications.</div>';
                console.error('Bell error:', err);
            });
        }

        bellBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (bellDropdown.style.display === 'block') {
                bellDropdown.style.display = 'none';
            } else {
                bellDropdown.style.display = 'block';
                loadNotifications();
            }
        });

        document.addEventListener('click', function (e) {
            if (!bellBtn.contains(e.target) && !bellDropdown.contains(e.target)) {
                bellDropdown.style.display = 'none';
            }
        });

        markAllBtn.addEventListener('click', function () {
            fetch(READ_ALL_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            }).then(function () {
                setBadge(0);
                bellBody.innerHTML = '<div class="bell-empty"><i class="bi bi-bell-slash"></i>No new notifications</div>';
            });
        });

        // Auto-load badge count on page load
        fetch(FETCH_URL, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
            credentials: 'same-origin',
        }).then(function (res) { return res.json(); }).then(function (data) { setBadge(data.count); }).catch(function () {});

    })();
    </script>

    @stack('scripts')
</body>
</html>