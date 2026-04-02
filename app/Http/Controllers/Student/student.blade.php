<!DOCTYPE html>
<html lang="en" style="height:100vh; overflow:hidden; display:block;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CCST DocRequest') — Student</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Volkhov:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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
            --footer-h:     5px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f4;
            color: var(--text-dark);
            height: 100vh;
            overflow: hidden;
            display: grid;
            grid-template-rows: var(--header-h) 1fr var(--footer-h);
            grid-template-columns: var(--sidebar-w) 1fr 350px;
            grid-template-areas:
                "header  header  header"
                "sidebar content right"
                "footer  footer  footer";
        }

        /* ── HEADER ── */
        .site-header {
            grid-area: header;
            background: linear-gradient(to right, #0C6637 13%, #9CDBBA 81%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 200;
            overflow: visible;
        }

        .site-header .header-title {
            font-family: 'Volkhov', serif;
            font-size: 0.96rem;
            color: var(--white);
            letter-spacing: 0.3px;
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .header-right {
            display: flex;
            align-items: center;
            padding-right: 4px;
            flex-shrink: 0;
            position: relative;
        }

        /* ── BELL BUTTON ── */
        .bell-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            padding: 4px 6px;
            line-height: 1;
            opacity: 0.9;
            z-index: 1;
        }
        .bell-btn:hover { opacity: 1; }

        .bell-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #DC3545;
            color: white;
            font-size: 0.55rem;
            font-weight: 700;
            min-width: 15px;
            height: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            pointer-events: none;
            line-height: 1;
        }

        /* ── NOTIFICATION DROPDOWN ──
           IMPORTANT: position:fixed so it always drops from the top-right
           corner of the viewport, never gets clipped by overflow:hidden on
           any parent, and always sits on top of all page content.
        ── */
        .bell-dropdown {
            position: fixed;
            top: calc(var(--header-h) + 8px);   /* just below the header */
            right: 18px;                          /* aligns with bell button */
            width: 310px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease;
        }
        .bell-dropdown.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .bell-dropdown::before {
            content: '';
            position: absolute;
            top: -7px;
            right: 14px;
            width: 14px;
            height: 14px;
            background: #1B6B3A;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }

        .bell-dropdown-header {
            background: #1B6B3A;
            color: white;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 9px 14px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bell-mark-all-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.8);
            font-size: 0.68rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            text-decoration: underline;
            transition: color 0.15s;
        }
        .bell-mark-all-btn:hover { color: white; }

        .bell-dropdown-body {
            max-height: 320px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
            border-radius: 0 0 12px 12px;
        }
        .bell-dropdown-body::-webkit-scrollbar { width: 4px; }
        .bell-dropdown-body::-webkit-scrollbar-thumb { background: #ccc; border-radius: 2px; }

        .bell-dropdown-empty {
            padding: 24px 16px;
            font-size: 0.82rem;
            color: #888;
            text-align: center;
        }

        .bell-notif-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            margin: 8px 10px;
            background: #F5C518;
            border-radius: 8px;
            cursor: default;
            transition: background 0.15s;
        }
        .bell-notif-item:last-child { margin-bottom: 10px; }
        .bell-notif-item.is-unread { background: #F5C518; }
        .bell-notif-item.is-read   { background: #FFF8D6; }
        .bell-notif-item:hover     { background: #f0bd10; }
        .bell-notif-item.is-read:hover { background: #fff0a0; }

        .bell-notif-icon {
            font-size: 1rem;
            margin-top: 1px;
            flex-shrink: 0;
            color: #1B6B3A;
        }

        .bell-notif-content { flex: 1; min-width: 0; }
        .bell-notif-title {
            font-size: 0.80rem;
            font-weight: 700;
            color: #1A1A1A;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        .bell-notif-detail {
            font-size: 0.73rem;
            color: #444;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bell-notif-time {
            font-size: 0.67rem;
            color: #666;
            margin-top: 3px;
        }

        .bell-notif-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #DC3545;
            flex-shrink: 0;
            margin-top: 5px;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 18px 0 14px;
            overflow-y: auto;
            overflow-x: hidden;
            height: calc(100vh - var(--header-h) - var(--footer-h));
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
            white-space: nowrap;
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
            grid-area: content;
            padding: 24px 20px 0 20px;
            min-width: 0;
            overflow: hidden;
            height: calc(100vh - var(--header-h) - var(--footer-h));
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            grid-area: right;
            overflow: hidden;
            height: calc(100vh - var(--header-h) - var(--footer-h));
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
        .ccst-card-header.outline {
            background: white;
            color: var(--green-dark);
            border-bottom: 2px solid var(--green-dark);
        }
        .ccst-card-body {
            padding: 12px 14px;
            font-size: 0.90rem;
            line-height: 1.6;
            color: var(--text-dark);
        }

        .status-pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.81rem;
            font-weight: 600;
        }

        /* ── PAGE BACKGROUND ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ asset("images/page-bg.jpeg") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.5;
            z-index: -1;
            pointer-events: none;
        }

        /* ── FOOTER ── */
        .site-footer {
            grid-area: footer;
            background: linear-gradient(to right, #9CDBBA 13%, #0C6637 81%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-top: -1px;
        }
        .site-footer span {
            font-size: 0.72rem;
            color: var(--white);
            font-weight: 500;
            white-space: nowrap;
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ══════════════════════════════════════════════════════════
         READ FLASH INTO PHP VARIABLES FIRST, THEN WIPE THE SESSION.
         ─────────────────────────────────────────────────────────
         WHY THIS ORDER MATTERS:
           1. We save the flash text into $flashSuccess / $flashError.
           2. We immediately call session()->forget() to wipe the session.
           3. The bell dropdown below uses the PHP variables — not session().
           4. Because the session is already cleared, auth-session-status
              .blade.php finds nothing and renders no banner. Ever.
           5. SweetAlert2 is completely unaffected — we never touch it.
    ══════════════════════════════════════════════════════════ --}}
    @php
        $flashSuccess = session('success');
        $flashError   = session('error');
        session()->forget(['success', 'error']);

        $dbUnread    = auth()->user()->unreadNotifications->count();
        $flashCount  = ($flashSuccess ? 1 : 0) + ($flashError ? 1 : 0);
        $totalUnread = $dbUnread + $flashCount;
    @endphp

    <header class="site-header">
        <span class="header-title">
            Clark College of Science and Technology's &nbsp; Document Request and Tracking System
        </span>

        <div class="header-right" id="bell-anchor">

            <button class="bell-btn"
                    id="bell-toggle-btn"
                    onclick="toggleBellDropdown()"
                    title="Notifications"
                    aria-label="Toggle notifications">
                <i class="bi bi-bell-fill"></i>
                <span class="bell-badge"
                      id="bell-badge"
                      style="{{ $totalUnread > 0 ? 'display:flex;' : 'display:none;' }}">
                    {{ $totalUnread > 9 ? '9+' : ($totalUnread ?: '') }}
                </span>
            </button>

        </div>{{-- end header-right --}}
    </header>

    {{-- ══════════════════════════════════════════════════════════
         BELL DROPDOWN — rendered directly on <body> (not inside
         the header) so position:fixed works correctly and the
         dropdown is never clipped by any parent overflow setting.
    ══════════════════════════════════════════════════════════ --}}
    <div class="bell-dropdown" id="bell-dropdown" role="dialog" aria-label="Notifications">

        <div class="bell-dropdown-header">
            <span>🔔 Notifications</span>
            @if($totalUnread > 0)
                <button class="bell-mark-all-btn"
                        onclick="markAllRead(event)"
                        id="mark-all-btn">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="bell-dropdown-body" id="bell-dropdown-body">

            {{-- ── SESSION FLASH ITEMS ──
                 Uses $flashSuccess / $flashError (PHP variables, not session).
                 Session is already wiped above so nothing else can grab it.
            ── --}}
            @if($flashSuccess)
                <div class="bell-notif-item is-unread" id="flash-notif-success">
                    <i class="bi bi-check-circle-fill bell-notif-icon" style="color:#1B6B3A;"></i>
                    <div class="bell-notif-content">
                        <div class="bell-notif-title">Success</div>
                        <div class="bell-notif-detail">{{ $flashSuccess }}</div>
                        <div class="bell-notif-time">Just now</div>
                    </div>
                    <span class="bell-notif-dot"></span>
                </div>
            @endif

            @if($flashError)
                <div class="bell-notif-item is-unread" id="flash-notif-error">
                    <i class="bi bi-exclamation-circle-fill bell-notif-icon" style="color:#DC3545;"></i>
                    <div class="bell-notif-content">
                        <div class="bell-notif-title">Error</div>
                        <div class="bell-notif-detail">{{ $flashError }}</div>
                        <div class="bell-notif-time">Just now</div>
                    </div>
                    <span class="bell-notif-dot"></span>
                </div>
            @endif

            {{-- ── DB NOTIFICATIONS ── --}}
            @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notif)
                @php
                    $nData   = $notif->data;
                    $nTitle  = $nData['title']  ?? 'Notification';
                    $nDetail = $nData['detail'] ?? ($nData['message'] ?? '');
                    $nType   = $nData['type']   ?? 'request';
                    $isRead  = !is_null($notif->read_at);

                    $iconMap = [
                        'payment'     => ['icon' => 'bi-cash-coin',          'color' => '#1B6B3A'],
                        'request'     => ['icon' => 'bi-file-earmark-text',  'color' => '#1A9FE0'],
                        'appointment' => ['icon' => 'bi-calendar-check',     'color' => '#6f42c1'],
                    ];
                    $iconCfg = $iconMap[$nType] ?? $iconMap['request'];
                @endphp
                <div class="bell-notif-item {{ $isRead ? 'is-read' : 'is-unread' }}"
                     data-notif-id="{{ $notif->id }}"
                     onclick="markOneRead('{{ $notif->id }}', this)">
                    <i class="bi {{ $iconCfg['icon'] }} bell-notif-icon"
                       style="color:{{ $iconCfg['color'] }};"></i>
                    <div class="bell-notif-content">
                        <div class="bell-notif-title">{{ $nTitle }}</div>
                        @if($nDetail)
                            <div class="bell-notif-detail">{{ $nDetail }}</div>
                        @endif
                        <div class="bell-notif-time">
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if(!$isRead)
                        <span class="bell-notif-dot"></span>
                    @endif
                </div>
            @empty
                @if(!$flashSuccess && !$flashError)
                    <div class="bell-dropdown-empty">
                        <i class="bi bi-bell-slash" style="font-size:1.4rem; color:#ccc; display:block; margin-bottom:6px;"></i>
                        No notifications yet.
                    </div>
                @endif
            @endforelse

        </div>{{-- end bell-dropdown-body --}}
    </div>{{-- end bell-dropdown — lives on <body>, not inside header --}}

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
            <span class="role-badge">Student</span>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('student.dashboard') }}"
               class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('student.documents') }}"
               class="{{ request()->routeIs('student.documents') ? 'active' : '' }}">
                Documents
            </a>
            <a href="{{ route('student.history') }}"
               class="{{ request()->routeIs('student.history') ? 'active' : '' }}">
                Request History
            </a>
            <a href="{{ route('student.account') }}"
               class="{{ request()->routeIs('student.account') ? 'active' : '' }}">
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

    <main class="main-content">
        @yield('content')
    </main>

    <aside class="right-panel">
        <img src="{{ asset('images/4-easy-steps.png') }}"
             alt="4 Easy Steps"
             style="width:100%; height:100%; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
    </aside>

    <footer class="site-footer">
        <span>© Copyright 2026 Clark College of Science and Technology | Document Request and Tracking System</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // ═══════════════════════════════════════════════════════════════════
    // ALERT KILLER
    // ─────────────────────────────────────────────────────────────────
    // Targets Bootstrap alert banners ONLY — elements that have BOTH
    // .alert AND a Bootstrap colour variant (.alert-success, etc.).
    // This never touches .swal2-* so CcstAlert popups are unaffected.
    // ═══════════════════════════════════════════════════════════════════
    (function killBootstrapAlerts() {
        const variants = [
            'alert-success','alert-danger','alert-warning','alert-info',
            'alert-primary','alert-secondary','alert-light','alert-dark'
        ];

        function kill() {
            document.querySelectorAll('.alert').forEach(function (el) {
                if (variants.some(function(v){ return el.classList.contains(v); })) {
                    el.style.setProperty('display', 'none', 'important');
                }
            });
        }

        kill(); // kill anything already in the DOM right now

        // Watch for alerts injected dynamically after page load
        var obs = new MutationObserver(kill);
        obs.observe(document.body, { childList: true, subtree: true });
        setTimeout(function(){ obs.disconnect(); }, 3000);
    })();


    // ═══════════════════════════════════════════════════════════════════
    // BELL NOTIFICATION DROPDOWN
    // ═══════════════════════════════════════════════════════════════════

    const bellDropdown = document.getElementById('bell-dropdown');
    const bellBadge    = document.getElementById('bell-badge');

    function toggleBellDropdown() {
        bellDropdown.classList.toggle('open');
    }

    // Close when clicking anywhere outside the bell button or dropdown
    document.addEventListener('click', function (e) {
        const bellBtn    = document.getElementById('bell-toggle-btn');
        const bellAnchor = document.getElementById('bell-anchor');
        if (
            bellDropdown &&
            !bellDropdown.contains(e.target) &&
            bellAnchor && !bellAnchor.contains(e.target)
        ) {
            bellDropdown.classList.remove('open');
        }
    });

    // Clicks inside dropdown don't bubble up and close it
    bellDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    function markOneRead(notifId, el) {
        if (el.classList.contains('is-read')) return;

        fetch('/student/notifications/' + notifId + '/read', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(() => {
            el.classList.remove('is-unread');
            el.classList.add('is-read');
            const dot = el.querySelector('.bell-notif-dot');
            if (dot) dot.remove();
            decrementBadge();
        })
        .catch(() => {});
    }

    function markAllRead(e) {
        e.stopPropagation();
        fetch('/student/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(() => {
            document.querySelectorAll('.bell-notif-item.is-unread').forEach(item => {
                item.classList.remove('is-unread');
                item.classList.add('is-read');
                const dot = item.querySelector('.bell-notif-dot');
                if (dot) dot.remove();
            });
            if (bellBadge) bellBadge.style.display = 'none';
            const markAllBtn = document.getElementById('mark-all-btn');
            if (markAllBtn) markAllBtn.style.display = 'none';
        })
        .catch(() => {});
    }

    function decrementBadge() {
        if (!bellBadge) return;
        const current = parseInt(bellBadge.textContent) || 0;
        const next = current - 1;
        if (next <= 0) {
            bellBadge.style.display = 'none';
            const markAllBtn = document.getElementById('mark-all-btn');
            if (markAllBtn) markAllBtn.style.display = 'none';
        } else {
            bellBadge.textContent = next > 9 ? '9+' : String(next);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Auto-open bell when a flash message exists on page load.
    // Opens immediately, scrolls the flash item into view inside the
    // dropdown body, auto-closes after 6 seconds.
    // Student can click bell to reopen at any time.
    // ─────────────────────────────────────────────────────────────────
    (function autoOpenForFlash() {
        const flashSuccess = document.getElementById('flash-notif-success');
        const flashError   = document.getElementById('flash-notif-error');
        if (!flashSuccess && !flashError) return;

        // Open the dropdown immediately
        bellDropdown.classList.add('open');

        // Scroll the flash item into view inside the dropdown body
        // Small delay lets the dropdown finish its open animation first
        const flashEl = flashSuccess || flashError;
        setTimeout(function () {
            const body = document.getElementById('bell-dropdown-body');
            if (body && flashEl) {
                // Scroll the dropdown body so the flash item is visible at the top
                body.scrollTop = flashEl.offsetTop - body.offsetTop;
            }
        }, 80);

        // Auto-close after 6 seconds
        const autoCloseTimer = setTimeout(function () {
            bellDropdown.classList.remove('open');
        }, 6000);

        // If student clicks bell manually before 6s, cancel the auto-close
        document.getElementById('bell-toggle-btn')
            .addEventListener('click', function () {
                clearTimeout(autoCloseTimer);
            }, { once: true });

        // Dim the flash dots to "read" after auto-close
        setTimeout(function() {
            [flashSuccess, flashError].forEach(function (el) {
                if (!el) return;
                el.classList.remove('is-unread');
                el.classList.add('is-read');
                const dot = el.querySelector('.bell-notif-dot');
                if (dot) dot.remove();
            });
        }, 6200);
    })();


    // ═══════════════════════════════════════════════════════════════════
    // CCST SWEETALERT2 ALERT SYSTEM
    // ═══════════════════════════════════════════════════════════════════

    window.CcstAlert = {

        confirm({ title = 'Confirm', text = 'Are you sure?', confirmText = 'Yes, Proceed', cancelText = 'Cancel', onConfirm = null } = {}) {
            Swal.fire({
                title: `<span style="color:#1B6B3A; font-size:1.05rem; font-weight:700;">${title}</span>`,
                html:  `<span style="font-size:0.88rem; color:#444;">${text}</span>`,
                icon: null,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: '#1A9FE0',
                cancelButtonColor: '#F5C518',
                customClass: { cancelButton: 'swal-cancel-dark', popup: 'ccst-swal-popup' },
                reverseButtons: false,
                focusCancel: false,
            }).then(result => {
                if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
            });
        },

        cancel({ title = 'Cancel Request?', text = null, refNumber = null, confirmText = 'Yes, Cancel It', cancelText = 'No, Keep It', onConfirm = null } = {}) {
            const bodyText = text || (refNumber
                ? `You are about to cancel <strong>${refNumber}</strong>.<br>This action <strong>cannot be undone</strong>.`
                : 'This action <strong>cannot be undone</strong>. Are you sure?');
            Swal.fire({
                title: `<span style="color:#DC3545; font-size:1.05rem; font-weight:700;">${title}</span>`,
                html:  `<span style="font-size:0.88rem; color:#444;">${bodyText}</span>`,
                iconHtml: `<i class="bi bi-trash3-fill" style="color:#DC3545; font-size:1.6rem;"></i>`,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#F5C518',
                customClass: { cancelButton: 'swal-cancel-dark', popup: 'ccst-swal-popup', icon: 'ccst-swal-icon-border-red' },
                reverseButtons: true,
                focusCancel: true,
            }).then(result => {
                if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
            });
        },

        error(message = 'Something went wrong. Please try again.', title = 'Error') {
            Swal.fire({
                title: `<span style="color:#DC3545; font-size:1.05rem; font-weight:700;">${title}</span>`,
                html:  `<span style="font-size:0.88rem; color:#444;">${message}</span>`,
                iconHtml: `<i class="bi bi-x-circle-fill" style="color:#DC3545; font-size:1.6rem;"></i>`,
                confirmButtonText: 'OK, Got It',
                confirmButtonColor: '#DC3545',
                customClass: { popup: 'ccst-swal-popup', icon: 'ccst-swal-icon-border-red' },
            });
        },

        warning(message = 'Please check the highlighted fields.', title = 'Heads Up') {
            Swal.fire({
                title: `<span style="color:#856404; font-size:1.05rem; font-weight:700;">${title}</span>`,
                html:  `<span style="font-size:0.88rem; color:#444;">${message}</span>`,
                iconHtml: `<i class="bi bi-exclamation-triangle-fill" style="color:#F5C518; font-size:1.6rem;"></i>`,
                confirmButtonText: "OK, I'll Fix It",
                confirmButtonColor: '#1A9FE0',
                customClass: { popup: 'ccst-swal-popup', icon: 'ccst-swal-icon-border-yellow' },
            });
        },

        incomplete(message = 'Please complete all required fields before continuing.') {
            this.warning(message, 'Incomplete Selection');
        },

        success(message = 'Action completed successfully.', title = 'Done!') {
            Swal.fire({
                title: `<span style="color:#1B6B3A; font-size:1.05rem; font-weight:700;">${title}</span>`,
                html:  `<span style="font-size:0.88rem; color:#444;">${message}</span>`,
                iconHtml: `<i class="bi bi-check-circle-fill" style="color:#1B6B3A; font-size:1.6rem;"></i>`,
                confirmButtonText: 'Great!',
                confirmButtonColor: '#1B6B3A',
                customClass: { popup: 'ccst-swal-popup', icon: 'ccst-swal-icon-border-green' },
                timer: 3000,
                timerProgressBar: true,
            });
        },

    };

    const swalStyleEl = document.createElement('style');
    swalStyleEl.textContent = `
        .ccst-swal-popup { font-family:'Poppins',sans-serif !important; border-radius:12px !important; padding-bottom:20px !important; }
        .ccst-swal-icon-border-red    { border-color:transparent !important; box-shadow:none !important; }
        .ccst-swal-icon-border-yellow { border-color:transparent !important; box-shadow:none !important; }
        .ccst-swal-icon-border-green  { border-color:transparent !important; box-shadow:none !important; }
        .swal2-icon.swal2-warning     { border-color:transparent !important; }
        .swal-cancel-dark { color:#1A1A1A !important; font-weight:700 !important; }
        .swal-cancel-dark:hover { background:#e6b800 !important; color:#1A1A1A !important; }
        .swal2-confirm, .swal2-cancel { font-family:'Poppins',sans-serif !important; font-weight:700 !important; font-size:0.85rem !important; padding:9px 24px !important; border-radius:6px !important; }
        .swal2-actions { gap:10px !important; }
    `;
    document.head.appendChild(swalStyleEl);

    </script>

    @stack('scripts')
</body>
</html>
