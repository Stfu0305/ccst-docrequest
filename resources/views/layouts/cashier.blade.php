<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="check-notifications" content="{{ session('check_notifications') ? 'true' : 'false' }}">

    {{-- Bell JS reads these URLs from meta tags instead of hardcoding paths --}}
    <meta name="notif-fetch-url"    content="{{ route('cashier.notifications.index') }}">
    <meta name="notif-read-url"     content="{{ route('cashier.notifications.markOneRead', ['id' => '__ID__']) }}">
    <meta name="notif-readall-url"  content="{{ route('cashier.notifications.markAllRead') }}">

    <title>@yield('title', 'CCST DocRequest') — Cashier</title>

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

        /* ── MAIN CONTENT with background image ── */
        .main-content {
            flex: 1;
            padding: 28px 24px;
            padding-right: 320px;
            min-width: 0;
            overflow-y: hidden;  /* Change from 'auto' to 'hidden' - removes page scroll */
            overflow-x: hidden;
            background: url('{{ asset("images/page-bg.jpeg") }}') center/cover no-repeat fixed;
            position: relative;
            height: calc(100vh - var(--header-h) - var(--footer-h)); /* Fixed height */
        }

        /* Semi-transparent overlay for readability */
        .main-content::before {
            content: '';
            position: fixed;
            top: var(--header-h);
            left: var(--sidebar-w);
            right: 300px;
            bottom: var(--footer-h);
            background: rgba(255, 255, 255, 0.45);
            pointer-events: none;
            z-index: 0;
        }

        /* Ensure content container doesn't scroll */
        .main-content > * {
            position: relative;
            z-index: 1;
            overflow-y: visible;
        }

        /* The stats row and cards should be visible without scroll */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 28px;
            overflow-y: visible;
        }

        /* Only the table body scrolls, not the page */
        .table-scroll-body {
            max-height: 300px;
            overflow-y: auto;
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
            gap: 18px;
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

        .right-panel .rp-stat-row {
            border-bottom-color: rgba(255,255,255,0.2);
            color: white;
        }

        .right-panel .rp-guide-step {
            border-bottom-color: rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.92);
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

        /* Notification Styles */
        .notif-item.unread {
            background: #f5fdf7;
            border-left: 3px solid #1B6B3A;
        }

        .notif-item:hover {
            background: #f7faf7;
        }

        .notif-item .notif-msg {
            font-size: 0.85rem;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .notif-item .notif-time {
            font-size: 0.72rem;
            color: #999;
            margin-top: 4px;
        }

        .bell-empty {
            text-align: center;
            padding: 28px 14px;
            color: var(--text-gray);
            font-size: 0.83rem;
        }

    </style>

    @stack('styles')
</head>
<body>

    {{-- HEADER --}}
    <header class="site-header">
        <span class="header-title">
            Clark College of Science and Technology's Document Request and Tracking System
        </span>
        <div class="header-right">

            <button class="bell-btn" id="bellBtn">
                <i class="bi bi-bell-fill"></i>
                <span class="bell-badge" id="bellBadge" style="display:none;">0</span>
            </button>
            <div class="bell-dropdown" id="bellDropdown">
                <div class="bell-dropdown-header">
                    <span><i class="bi bi-bell me-1"></i> Notifications</span>
                    <button class="bell-mark-all" id="bellMarkAll">Mark all as read</button>
                </div>
                <div class="bell-dropdown-body" id="bellDropdownBody">
                    <div class="bell-empty">No new notifications</div>
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
                <span class="role-badge">Cashier</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('cashier.dashboard') }}"
                   class="{{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('cashier.payments.index') }}"
                   class="{{ request()->routeIs('cashier.payments.*') ? 'active' : '' }}">
                    Payments
                </a>
                <a href="{{ route('cashier.settings.index') }}"
                   class="{{ request()->routeIs('cashier.settings.*') ? 'active' : '' }}">
                    Settings
                </a>
                <a href="{{ route('cashier.account') }}"
                   class="{{ request()->routeIs('cashier.account') ? 'active' : '' }}">
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
            {{-- No flash messages - all notifications go to bell dropdown --}}

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
    // ═══════════════════════════════════════════════════════════════════════
    // CCST NOTIFICATION SYSTEM - Cashier (Laravel Database Notifications)
    // ═══════════════════════════════════════════════════════════════════════

    (function() {
        // Configuration
        const FETCH_URL = '/cashier/notifications';
        const READ_URL_TEMPLATE = '/cashier/notifications/__ID__/read';
        const READ_ALL_URL = '/cashier/notifications/mark-all-read';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // State
        let lastNotificationCount = 0;
        let hasAutoOpenedThisSession = false;
        
        // DOM Elements
        const bellBtn = document.getElementById('bellBtn');
        const bellBadge = document.getElementById('bellBadge');
        const bellDropdown = document.getElementById('bellDropdown');
        const bellBody = document.getElementById('bellDropdownBody');
        const markAllBtn = document.getElementById('bellMarkAll');
        
        // Helper: Escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
        
        // Update badge count
        function updateBadge(count) {
            if (!bellBadge) return;
            if (count > 0) {
                bellBadge.textContent = count > 99 ? '99+' : count;
                bellBadge.style.display = 'flex';
            } else {
                bellBadge.style.display = 'none';
            }
        }
        
        // Render notifications in dropdown (regular - no highlight)
        function renderNotifications(notifications) {
            if (!bellBody) return;
            
            if (!notifications || notifications.length === 0) {
                bellBody.innerHTML = `
                    <div class="bell-empty">
                        <i class="bi bi-bell-slash"></i>
                        No notifications yet.
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(function(notif) {
                const unreadClass = notif.read ? '' : 'unread';
                
                html += `
                    <div class="notif-item ${unreadClass}" data-id="${escapeHtml(notif.id)}" data-url="${escapeHtml(notif.url)}" style="cursor:pointer; padding:12px 14px; border-bottom:1px solid #f0f0f0;">
                        <div style="flex:1;">
                            <div class="notif-msg" style="font-size:0.85rem; color:#1a1a1a; line-height:1.4;">${escapeHtml(notif.message)}</div>
                            <div class="notif-time" style="font-size:0.72rem; color:#999; margin-top:4px;">${escapeHtml(notif.time)}</div>
                        </div>
                    </div>
                `;
            });
            
            bellBody.innerHTML = html;
            
            // Add click handlers to mark as read and redirect
            document.querySelectorAll('.notif-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const url = this.dataset.url;
                    
                    if (id && READ_URL_TEMPLATE) {
                        const readUrl = READ_URL_TEMPLATE.replace('__ID__', id);
                        fetch(readUrl, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        }).finally(function() {
                            if (url && url !== '#') {
                                window.location.href = url;
                            }
                        });
                    } else if (url && url !== '#') {
                        window.location.href = url;
                    }
                });
            });
        }
        
        // Render notifications with highlight for newest notification
        function renderNotificationsWithHighlight(notifications, highlightId = null) {
            if (!bellBody) return;
            
            if (!notifications || notifications.length === 0) {
                bellBody.innerHTML = `
                    <div class="bell-empty">
                        <i class="bi bi-bell-slash"></i>
                        No notifications yet.
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(function(notif) {
                const unreadClass = notif.read ? '' : 'unread';
                // Apply yellow highlight with 50% opacity to the newest notification
                const isHighlighted = (highlightId && notif.id === highlightId);
                const highlightStyle = isHighlighted ? 'background: rgba(255, 241, 182, 1.0); border-left: 4px solid #F5C518;' : '';
                
                html += `
                    <div class="notif-item ${unreadClass}" data-id="${escapeHtml(notif.id)}" data-url="${escapeHtml(notif.url)}" style="cursor:pointer; padding:12px 14px; border-bottom:1px solid #f0f0f0; ${highlightStyle}">
                        <div style="flex:1;">
                            <div class="notif-msg" style="font-size:0.85rem; color:#1a1a1a; line-height:1.4;">${escapeHtml(notif.message)}</div>
                            <div class="notif-time" style="font-size:0.72rem; color:#999; margin-top:4px;">${escapeHtml(notif.time)}</div>
                        </div>
                        ${isHighlighted ? '<div style="margin-left:8px;"><i class="bi bi-star-fill" style="color:#F5C518; font-size:0.7rem;"></i></div>' : ''}
                    </div>
                `;
            });
            
            bellBody.innerHTML = html;
            
            // Add click handlers to mark as read and redirect
            document.querySelectorAll('.notif-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const url = this.dataset.url;
                    
                    if (id && READ_URL_TEMPLATE) {
                        const readUrl = READ_URL_TEMPLATE.replace('__ID__', id);
                        fetch(readUrl, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        }).finally(function() {
                            if (url && url !== '#') {
                                window.location.href = url;
                            }
                        });
                    } else if (url && url !== '#') {
                        window.location.href = url;
                    }
                });
            });
        }
        
        // Fetch notifications from server (regular polling)
        function fetchAndUpdateNotifications(shouldAutoOpen = false) {
            fetch(FETCH_URL, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                credentials: 'same-origin'
            })
            .then(function(res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function(data) {
                const newUnreadCount = data.unread || 0;
                const hasNewNotifications = newUnreadCount > lastNotificationCount;
                
                console.log(`🔔 Poll: Unread=${newUnreadCount}, Last=${lastNotificationCount}, New=${hasNewNotifications}, AutoOpen=${shouldAutoOpen}`);
                
                updateBadge(newUnreadCount);
                
                // Auto-open if new notifications detected (regular polling)
                if (shouldAutoOpen && hasNewNotifications && !hasAutoOpenedThisSession) {
                    console.log('🔔 Auto-opening dropdown from poll!');
                    hasAutoOpenedThisSession = true;
                    renderNotifications(data.notifications || []);
                    if (bellDropdown) {
                        bellDropdown.style.display = 'block';
                        setTimeout(function() {
                            if (bellDropdown) bellDropdown.style.display = 'none';
                        }, 8000);
                    }
                }
                
                lastNotificationCount = newUnreadCount;
            })
            .catch(function(err) {
                console.error('❌ Failed to fetch notifications:', err);
            });
        }
        
        // Load and show notifications when bell clicked
        function loadAndShowNotifications() {
            fetch(FETCH_URL, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                credentials: 'same-origin'
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                renderNotifications(data.notifications || []);
                updateBadge(data.unread || 0);
            })
            .catch(function(err) {
                console.error('Failed to load notifications:', err);
                if (bellBody) {
                    bellBody.innerHTML = '<div class="bell-empty">Could not load notifications.</div>';
                }
            });
        }
        
        // Mark all as read
        function markAllAsRead() {
            fetch(READ_ALL_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(function() {
                updateBadge(0);
                if (bellDropdown && bellDropdown.style.display === 'block') {
                    loadAndShowNotifications();
                }
            })
            .catch(function(err) {
                console.error('Failed to mark all as read:', err);
            });
        }
        
        // Event: Bell button click
        if (bellBtn) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (bellDropdown.style.display === 'block') {
                    bellDropdown.style.display = 'none';
                } else {
                    loadAndShowNotifications();
                    bellDropdown.style.display = 'block';
                }
            });
        }
        
        // Event: Mark all button
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (bellBtn && !bellBtn.contains(e.target) && bellDropdown && !bellDropdown.contains(e.target)) {
                bellDropdown.style.display = 'none';
            }
        });
        
        // Initial load - get badge count
        fetchAndUpdateNotifications(false);
        
        // Check for immediate notification from form submission
        const shouldCheck = document.querySelector('meta[name="check-notifications"]')?.content === 'true';
        console.log('🔔 Immediate notification check on page load:', shouldCheck);
        
        if (shouldCheck) {
            console.log('🔔 New notification detected! Fetching and opening dropdown immediately...');
            
            // Wait 500ms for the notification to be saved to database
            setTimeout(function() {
                // Force fetch and show notifications immediately
                fetch(FETCH_URL, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    credentials: 'same-origin'
                })
                .then(function(res) { 
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json(); 
                })
                .then(function(data) {
                    console.log('📦 Notifications fetched for auto-open:', data.notifications?.length, 'notifications');
                    console.log('📊 Unread count:', data.unread);
                    
                    if (data.notifications && data.notifications.length > 0) {
                        // Get the ID of the most recent notification (first one in the array)
                        const newestNotificationId = data.notifications[0]?.id;
                        console.log('✨ Newest notification ID:', newestNotificationId);
                        
                        // Render notifications with highlight for the newest one
                        renderNotificationsWithHighlight(data.notifications, newestNotificationId);
                        updateBadge(data.unread || 0);
                        
                        if (bellDropdown) {
                            bellDropdown.style.display = 'block';
                            console.log('🔔✅ Dropdown opened automatically with highlighted newest notification!');
                            
                            // Auto-close after 8 seconds
                            setTimeout(function() {
                                if (bellDropdown) bellDropdown.style.display = 'none';
                                console.log('🔔 Dropdown auto-closed after 8 seconds');
                            }, 8000);
                        }
                        
                        // Update state to prevent duplicate auto-open
                        lastNotificationCount = data.unread || 0;
                        hasAutoOpenedThisSession = true;
                    } else {
                        console.log('⚠️ No notifications found, but flag was set');
                    }
                })
                .catch(function(err) {
                    console.error('❌ Failed to fetch notifications for auto-open:', err);
                });
            }, 500);
            
            // Clear the flag so it doesn't trigger again on next refresh
            const meta = document.querySelector('meta[name="check-notifications"]');
            if (meta) {
                meta.content = 'false';
                console.log('🔔 Cleared check-notifications flag');
            }
        }
        
        // Poll for new notifications every 10 seconds
        setInterval(function() {
            console.log('🔍 Polling for new notifications...');
            fetchAndUpdateNotifications(true);
        }, 10000);
    })();
    </script>

    @stack('scripts')
</body>
</html>