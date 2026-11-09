<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, interactive-widget=resizes-content">
        <title>@yield('title', config('app.name', 'ITHub'))</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts

            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; }
            </style>
        @endif

        {{-- ===== NAVBAR & FOOTER STYLES ===== --}}
        <style>
        /* ---- UTILITIES ---- */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ---- NAVBAR ---- */
        #it-navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        #it-navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 62px;
        }
        #it-logo {
            font-size: 21px;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.025em;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            line-height: 1;
            transition: color 0.15s ease;
        }
        #it-logo span {
            color: #2563eb;
            transition: color 0.15s ease;
        }
        #it-logo:hover {
            color: #2563eb;
        }
        #it-nav {
            display: flex;
            align-items: stretch;
            height: 100%;
        }
        #it-nav a {
            display: flex;
            align-items: center;
            padding: 0 13px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            border-bottom: 2.5px solid transparent;
            transition: color 0.15s, border-color 0.15s;
            white-space: nowrap;
        }
        #it-nav a:hover { color: #2563eb; }
        #it-nav a.it-active {
            color: #2563eb;
            font-weight: 600;
            border-bottom-color: #2563eb;
        }
        #it-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .it-btn-signin {
            display: inline-flex !important;
            align-items: center;
            padding: 7px 16px;
            font-size: 13.5px;
            font-weight: 600;
            color: #2563eb !important;
            border: 1.5px solid #2563eb;
            border-radius: 8px;
            text-decoration: none;
            background: transparent;
            white-space: nowrap;
            transition: background 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .it-btn-signin:hover { background: #eff6ff; }
        .it-btn-signup {
            display: inline-flex !important;
            align-items: center;
            padding: 7px 16px;
            font-size: 13.5px;
            font-weight: 600;
            color: #ffffff !important;
            background: #2563eb;
            border: 1.5px solid #2563eb;
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s;
            box-shadow: 0 1px 4px rgba(37,99,235,0.25);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .it-btn-signup:hover { background: #1d4ed8; border-color: #1d4ed8; }

        /* ---- NAVBAR ACTION ICON BUTTONS (CHAT & NOTIFICATION) ---- */
        .it-nav-icon-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #eff6ff;
            border: 1.5px solid #dbeafe;
            color: #2563eb;
            cursor: pointer;
            padding: 0;
            transition: all 0.15s ease;
            text-decoration: none;
            flex-shrink: 0;
        }
        .it-nav-icon-btn:hover,
        .it-nav-icon-btn[aria-expanded="true"] {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
            transform: scale(1.04);
        }
        .it-notif-btn {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
        }
        .it-notif-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #2563eb;
            transform: scale(1.04);
        }
        .it-nav-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);
            pointer-events: none;
            z-index: 2;
        }

        /* ---- MESSENGER STYLE CHAT DROPDOWN ---- */
        .it-chat-nav { position: relative; }
        .it-notif-nav { position: relative; }
        .it-chat-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 375px;
            max-width: calc(100vw - 24px);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 16px 38px -6px rgba(15, 23, 42, 0.14), 0 4px 12px -2px rgba(15, 23, 42, 0.05);
            z-index: 70;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: itChatDropFade 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes itChatDropFade {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .it-chat-dropdown[hidden] {
            display: none !important;
        }

        .it-chat-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px 12px;
            border-bottom: 1px solid #f1f5f9;
            background: #ffffff;
        }
        .it-chat-dropdown-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .it-chat-dropdown-see-all {
            font-size: 12.5px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
            transition: color 0.15s;
        }
        .it-chat-dropdown-see-all:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .it-chat-dropdown-list {
            max-height: 380px;
            overflow-y: auto;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .it-chat-dropdown-list::-webkit-scrollbar {
            width: 5px;
        }
        .it-chat-dropdown-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .it-chat-dropdown-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        .it-chat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 10px;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
            position: relative;
        }
        .it-chat-item:hover {
            background: #f1f5f9;
        }
        .it-chat-item.it-chat-unread {
            background: #f0f7ff;
        }
        .it-chat-item.it-chat-unread:hover {
            background: #e0f0fe;
        }

        .it-chat-avatar-wrap {
            position: relative;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
        }
        .it-chat-avatar-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .it-chat-avatar-init {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .it-chat-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .it-chat-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .it-chat-unread .it-chat-name {
            font-weight: 700;
            color: #0f172a;
        }
        .it-chat-snippet-row {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12.5px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .it-chat-unread .it-chat-snippet-row {
            color: #1e293b;
            font-weight: 600;
        }
        .it-chat-sender {
            font-weight: 600;
            color: #475569;
            flex-shrink: 0;
        }
        .it-chat-unread .it-chat-sender {
            color: #2563eb;
        }
        .it-chat-snippet {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .it-chat-empty-snippet {
            color: #94a3b8;
            font-style: italic;
        }

        .it-chat-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0;
            margin-left: 6px;
        }
        .it-chat-time {
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            white-space: nowrap;
        }
        .it-chat-unread .it-chat-time {
            color: #2563eb;
            font-weight: 700;
        }
        .it-chat-unread-badge {
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .it-chat-empty-state {
            padding: 30px 18px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .it-chat-empty-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px;
        }
        .it-chat-empty-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .it-chat-empty-desc {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
            max-width: 240px;
        }
        .it-chat-empty-btn {
            margin-top: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: #2563eb;
            color: #ffffff !important;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }
        .it-chat-empty-btn:hover {
            background: #1d4ed8;
        }

        .it-chat-dropdown-footer {
            padding: 10px 14px;
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            display: flex;
            justify-content: center;
        }
        .it-chat-btn-all {
            display: block;
            width: 100%;
            text-align: center;
            padding: 8px 12px;
            border-radius: 8px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
        }
        .it-chat-btn-all:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        /* Mobile drawer link & badge */
        .it-mobile-chat-link {
            display: flex !important;
            align-items: center;
            justify-content: space-between !important;
        }
        .it-mobile-unread-badge {
            background: #ef4444;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .it-chat-dropdown {
                position: fixed;
                top: 64px;
                left: 12px;
                right: 12px;
                width: auto;
                max-width: none;
                max-height: calc(88dvh - 64px);
                border-radius: 16px;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.22);
                z-index: 100;
            }
            .it-chat-dropdown-list {
                max-height: calc(88dvh - 180px);
                -webkit-overflow-scrolling: touch;
            }
        }

        /* ---- USER AVATAR + DROPDOWN ---- */
        .it-user { position: relative; }
        #it-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1.5px solid #e2e8f0;
            border-radius: 50px;
            padding: 4px 6px 4px 4px;
            cursor: pointer;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s, background 0.15s;
            line-height: 1;
        }
        #it-user-btn:hover { border-color: #2563eb; background: #f8fafc; }
        .it-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
            line-height: 1;
        }
        .it-user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        #it-user-btn svg { color: #64748b; flex-shrink: 0; }
        .it-user-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 200px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.10);
            padding: 6px;
            z-index: 60;
        }
        .it-user-head {
            padding: 8px 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 6px;
        }
        .it-user-role {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            border-radius: 50px;
            padding: 3px 10px;
            text-transform: capitalize;
        }
        .it-user-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 8px 12px;
            border: none;
            background: none;
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-align: left;
            transition: background 0.12s, color 0.12s;
        }
        .it-user-item:hover { background: #f1f5f9; color: #0f172a; }
        .it-user-item svg { color: #64748b; flex-shrink: 0; }
        .it-user-item:hover svg { color: #2563eb; }
        .it-user-logout { color: #dc2626; }
        .it-user-logout svg { color: #dc2626; }
        .it-user-logout:hover { background: #fef2f2; color: #b91c1c; }
        .it-user-logout:hover svg { color: #b91c1c; }

        /* Mobile menu toggle button */
        #it-mobile-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            width: 38px;
            height: 38px;
            color: #475569;
            border-radius: 10px;
            padding: 0;
            line-height: 0;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }
        #it-mobile-toggle:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        #it-mobile-toggle.it-open,
        #it-mobile-toggle[aria-expanded="true"] {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
        }
        #it-mobile-toggle .it-icon-close { display: none; }
        #it-mobile-toggle.it-open .it-icon-bars,
        #it-mobile-toggle[aria-expanded="true"] .it-icon-bars { display: none; }
        #it-mobile-toggle.it-open .it-icon-close,
        #it-mobile-toggle[aria-expanded="true"] .it-icon-close { display: block; }

        /* Mobile drawer */
        #it-mobile-menu {
            display: none;
            border-top: 1px solid #f1f5f9;
            background: #ffffff;
            padding: 12px 18px 20px;
            max-height: calc(100dvh - 58px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        }
        #it-mobile-menu.it-open {
            display: block;
            animation: itMobileMenuSlide 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes itMobileMenuSlide {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .it-mobile-user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 6px 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .it-mobile-user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        .it-mobile-user-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .it-mobile-links {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        #it-mobile-menu a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.12s, color 0.12s;
        }
        #it-mobile-menu a svg {
            color: #94a3b8;
            flex-shrink: 0;
            transition: color 0.12s;
        }
        #it-mobile-menu a:hover,
        #it-mobile-menu a:active,
        #it-mobile-menu a.it-active {
            color: #2563eb;
            background: #eff6ff;
            font-weight: 600;
        }
        #it-mobile-menu a:hover svg,
        #it-mobile-menu a.it-active svg {
            color: #2563eb;
        }

        .it-mobile-btns {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }
        .it-mobile-btns a,
        .it-mobile-btns button {
            flex: 1;
            text-align: center;
            padding: 9px 12px !important;
            border-radius: 8px !important;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
            transition: background 0.15s, border-color 0.15s;
        }
        .it-mbtn-profile {
            color: #2563eb !important;
            background: #eff6ff !important;
            border: 1.5px solid #bfdbfe !important;
        }
        .it-mbtn-profile:hover {
            background: #dbeafe !important;
        }
        .it-mbtn-dash {
            color: #ffffff !important;
            background: #2563eb !important;
            border: 1.5px solid #2563eb !important;
        }
        .it-mbtn-dash:hover {
            background: #1d4ed8 !important;
        }
        .it-mbtn-logout {
            color: #dc2626 !important;
            background: #fef2f2 !important;
            border: 1.5px solid #fecaca !important;
        }
        .it-mbtn-logout:hover {
            background: #fee2e2 !important;
        }
        .it-mbtn-signin {
            color: #2563eb !important;
            background: transparent !important;
            border: 1.5px solid #2563eb !important;
        }
        .it-mbtn-signin:hover {
            background: #eff6ff !important;
        }
        .it-mbtn-signup {
            color: #ffffff !important;
            background: #2563eb !important;
            border: 1.5px solid #2563eb !important;
            box-shadow: 0 1px 4px rgba(37,99,235,0.25);
        }
        .it-mbtn-signup:hover {
            background: #1d4ed8 !important;
        }

        /* Laptops fit (1025px - 1200px) */
        @media (min-width: 1025px) and (max-width: 1200px) {
            #it-nav a {
                padding: 0 8px;
                font-size: 13.5px;
            }
            #it-navbar-inner {
                padding: 0 16px;
            }
        }

        /* Tablets & Mobile breakpoint */
        @media (max-width: 1024px) {
            #it-nav { display: none; }
            .it-btn-signin, .it-btn-signup { display: none !important; }
            #it-mobile-toggle { display: inline-flex; }
            #it-mobile-menu.it-open { display: block; }
        }

        /* Mobile phones (<= 768px) */
        @media (max-width: 768px) {
            #it-navbar-inner {
                padding: 0 16px;
                height: 58px;
            }
            #it-logo {
                font-size: 19px;
            }
            #it-actions {
                gap: 6px;
            }
            .it-nav-icon-btn,
            #it-mobile-toggle {
                width: 36px;
                height: 36px;
            }
            #it-user-btn {
                padding: 2px;
                border-radius: 50%;
                gap: 0;
            }
            .it-user-name,
            #it-user-btn svg {
                display: none !important;
            }
            .it-user-menu {
                position: fixed;
                top: 64px;
                right: 12px;
                left: auto;
                width: min(260px, calc(100vw - 24px));
                border-radius: 16px;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
                z-index: 90;
            }
        }

        /* Extra small phones (<= 380px) */
        @media (max-width: 380px) {
            #it-navbar-inner {
                padding: 0 10px;
                height: 56px;
            }
            #it-logo {
                font-size: 18px;
            }
            #it-actions {
                gap: 4px;
            }
            .it-nav-icon-btn,
            #it-mobile-toggle {
                width: 34px;
                height: 34px;
            }
            .it-avatar {
                width: 28px;
                height: 28px;
                font-size: 13px;
            }
        }

        /* ---- FOOTER ---- */
        #it-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding-top: 52px;
            padding-bottom: max(16px, env(safe-area-inset-bottom));
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
        }
        #it-footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .it-footer-grid {
            display: grid;
            grid-template-columns: 1.45fr 1fr 1.05fr 1.15fr;
            gap: 40px;
        }
        .it-f-brand {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.025em;
            display: inline-flex;
            align-items: center;
            line-height: 1;
            transition: color 0.15s ease;
        }
        .it-f-brand span {
            color: #2563eb;
            transition: color 0.15s ease;
        }
        .it-f-brand:hover {
            color: #2563eb;
        }
        .it-f-tagline {
            font-size: 13.5px;
            font-weight: 600;
            color: #2563eb;
            margin-top: 4px;
            line-height: 1.45;
        }
        .it-f-desc {
            font-size: 13.5px;
            color: #64748b;
            margin-top: 10px;
            line-height: 1.65;
            max-width: 340px;
        }
        .it-social-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .it-si {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            line-height: 0;
        }
        .it-si:hover { background: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .it-si.li:hover { border-color: #0077b5; color: #0077b5; }
        .it-si.gh:hover { border-color: #24292f; color: #24292f; }
        .it-si.tw:hover { border-color: #0f172a; color: #0f172a; }
        .it-si.ig:hover { border-color: #e1306c; color: #e1306c; }
        .it-si.yt:hover { border-color: #ff0000; color: #ff0000; }

        .it-f-col-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0 0 16px;
        }
        .it-f-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .it-f-links a { 
            font-size: 14px; 
            color: #475569; 
            text-decoration: none; 
            transition: color 0.15s, transform 0.15s; 
            display: inline-block;
            padding: 3px 0;
        }
        .it-f-links a:hover { color: #2563eb; transform: translateX(2px); }

        .it-contact-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
        .it-ci {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: #475569;
            min-width: 0;
        }
        .it-ci a { 
            color: #475569; 
            text-decoration: none; 
            transition: color 0.15s; 
            word-break: break-word;
            min-width: 0;
        }
        .it-ci a:hover { color: #2563eb; }
        .it-ci-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            line-height: 0;
        }
        .it-ci-icon.email    { background: #f1f5f9; color: #64748b; }
        .it-ci-icon.phone    { background: #f1f5f9; color: #64748b; }
        .it-ci-icon.location { background: #f1f5f9; color: #64748b; }

        .it-footer-bottom {
            margin-top: 48px;
            border-top: 1px solid #e2e8f0;
            padding: 22px 0 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }
        .it-footer-copy { font-size: 12.5px; color: #94a3b8; margin: 0; }
        .it-footer-legal { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .it-footer-legal a { font-size: 12.5px; color: #94a3b8; text-decoration: none; transition: color 0.15s; }
        .it-footer-legal a:hover { color: #2563eb; }

        /* ---- CUSTOM LANGUAGE SELECTOR DROPDOWN (Matches user design) ---- */
        .it-lang-menu-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .it-lang-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 12px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.15s ease;
            color: #0f172a;
            font-family: inherit;
        }
        .it-lang-btn:hover,
        .it-lang-btn[aria-expanded="true"] {
            border-color: #cbd5e1;
            background: #f8fafc;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .it-lang-btn-flag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 15px;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }
        .it-lang-btn-flag .it-flag-svg {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .it-lang-btn-code {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.2px;
            line-height: 1;
        }

        .it-lang-chevron {
            color: #64748b;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }
        .it-lang-btn[aria-expanded="true"] .it-lang-chevron {
            transform: rotate(180deg);
        }

        .it-lang-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 285px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 40px -8px rgba(15, 23, 42, 0.14), 0 4px 14px -2px rgba(15, 23, 42, 0.05);
            padding: 14px 10px 10px;
            z-index: 80;
            animation: itChatDropFade 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .it-lang-dropdown[hidden] {
            display: none !important;
        }

        .it-lang-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 6px;
        }
        .it-lang-header-title {
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .it-lang-badge {
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
            background: #eff6ff;
            padding: 3px 9px;
            border-radius: 9999px;
            line-height: 1;
        }

        .it-lang-list {
            max-height: 330px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding-right: 2px;
        }
        .it-lang-list::-webkit-scrollbar {
            width: 5px;
        }
        .it-lang-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        .it-lang-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 12px;
            background: transparent;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
            font-family: inherit;
        }
        .it-lang-item:hover {
            background: #f8fafc;
        }
        .it-lang-item.it-lang-active {
            background: #eff6ff !important;
        }

        .it-lang-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .it-flag-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 16px;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }
        .it-flag-icon .it-flag-svg {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .it-lang-item-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }
        .it-lang-item-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.25;
            white-space: nowrap;
        }
        .it-lang-active .it-lang-item-name {
            color: #1d4ed8;
        }
        .it-lang-item-sub {
            font-size: 11.5px;
            font-weight: 500;
            color: #94a3b8;
            line-height: 1.2;
        }

        .it-lang-check {
            display: none;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #2563eb;
        }
        .it-lang-active .it-lang-check {
            display: inline-flex;
        }

        @media (max-width: 768px) {
            .it-lang-btn {
                height: 36px;
                padding: 0 9px;
                gap: 5px;
            }
            .it-lang-btn-code {
                font-size: 12px;
            }
            .it-lang-dropdown {
                position: fixed;
                top: 64px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: 320px;
                margin-left: auto;
            }
        }

        /* ---- HIDE TOP GOOGLE TRANSLATE BANNER FRAME, TOOLTIPS & OVERLAYS ---- */
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.goog-te-banner-frame,
        iframe.skiptranslate,
        iframe[id*=":1.container"],
        iframe[id*=":2.container"],
        .VIpgJd-ZVi9od-ORHb-OEVmcd,
        .VIpgJd-ZVi9od-ORHb-OEVmcd-ti6hGc,
        .VIpgJd-ZVi9od-aZ2wEe-wOHMyf,
        #goog-gt-tt,
        .goog-te-balloon-frame {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
            z-index: -9999 !important;
        }
        body {
            top: 0px !important;
            position: static !important;
            margin-top: 0px !important;
        }
        .goog-text-highlight {
            background: none !important;
            box-shadow: none !important;
        }

        /* Tablet Responsive (561px - 960px) */
        @media (min-width: 561px) and (max-width: 960px) {
            .it-footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 36px 32px;
            }
            .it-f-brand-col {
                grid-column: 1 / -1;
            }
            .it-f-desc {
                max-width: 540px;
            }
        }

        /* Mobile Responsive (≤ 560px) - Semua Smartphone */
        @media (max-width: 560px) {
            #it-footer {
                padding-top: 36px;
                padding-bottom: max(20px, env(safe-area-inset-bottom));
            }
            #it-footer-inner {
                padding: 0 18px;
            }
            .it-footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .it-f-brand {
                font-size: 21px;
            }
            .it-f-tagline {
                font-size: 13px;
            }
            .it-f-desc {
                font-size: 13px;
                max-width: 100%;
                line-height: 1.6;
            }
            .it-social-row {
                gap: 9px;
                margin-top: 16px;
            }
            .it-si {
                width: 36px;
                height: 36px;
            }
            .it-f-col-title {
                margin-bottom: 12px;
                font-size: 12px;
            }
            .it-f-links {
                gap: 6px;
            }
            .it-f-links a {
                padding: 4px 0;
                font-size: 13.5px;
            }
            .it-contact-list {
                gap: 11px;
            }
            .it-ci {
                font-size: 13px;
                gap: 10px;
            }
            .it-ci-icon {
                width: 30px;
                height: 30px;
            }
            .it-footer-bottom {
                margin-top: 32px;
                padding: 20px 0 14px;
                flex-direction: column-reverse;
                align-items: center;
                text-align: center;
                gap: 16px;
            }
            .it-footer-legal {
                justify-content: center;
                gap: 14px 18px;
            }
            .it-footer-legal a {
                font-size: 12px;
                padding: 4px 2px;
            }
            .it-footer-copy {
                font-size: 11.5px;
                line-height: 1.6;
                text-align: center;
            }
        }

        /* ---- HERO SECTION ---- */
        .it-hero {
            position: relative;
            background: linear-gradient(180deg, #eef4ff 0%, #f8fafc 100%);
            padding: 64px 24px 48px;
            text-align: center;
            overflow: hidden;
        }
        .it-hero::before {
            content: '';
            position: absolute;
            top: -120px;
            left: -120px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(59,130,246,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }
        .it-hero::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(147,197,253,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .it-hero-content {
            position: relative;
            z-index: 1;
            max-width: 750px;
            margin: 0 auto;
        }
        .it-hero h1 {
            font-size: 40px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.5px;
            margin: 0;
        }
        @media (min-width: 768px) {
            .it-hero h1 { font-size: 48px; }
        }
        .it-hero-sub {
            font-size: 15px;
            color: #64748b;
            max-width: 560px;
            margin: 16px auto 0;
            line-height: 1.65;
        }

        /* Hero Search */
        .it-hero-search {
            max-width: 560px;
            margin: 32px auto 0;
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 5px 5px 5px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .it-hero-search svg {
            color: #94a3b8;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .it-hero-search input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 14px;
            color: #334155;
            background: transparent;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-width: 0;
        }
        .it-hero-search input::placeholder { color: #94a3b8; }
        .it-hero-search button {
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .it-hero-search button:hover { background: #1d4ed8; }

        /* Category Icons */
        .it-categories {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 44px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .it-cat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #475569;
            font-size: 12px;
            font-weight: 500;
            width: 80px;
            transition: color 0.15s;
            cursor: pointer;
        }
        .it-cat-item:hover { color: #2563eb; }
        .it-cat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s, box-shadow 0.15s;
            line-height: 0;
        }
        .it-cat-item:hover .it-cat-icon {
            border-color: #93c5fd;
            box-shadow: 0 2px 8px rgba(59,130,246,0.1);
        }

        /* ---- FEATURED OPPORTUNITIES ---- */
        .it-featured {
            max-width: 1200px;
            margin: 0 auto;
            padding: 56px 24px;
        }
        .it-featured-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 28px;
        }
        .it-featured-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .it-featured-header p {
            font-size: 14px;
            color: #64748b;
            margin: 4px 0 0;
        }
        .it-featured-header a {
            font-size: 14px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
            white-space: nowrap;
            transition: color 0.15s;
        }
        .it-featured-header a:hover { color: #1d4ed8; }

        .it-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        @media (max-width: 860px) {
            .it-cards-grid { grid-template-columns: 1fr; }
        }

        /* Opportunity Card */
        .it-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            transition: box-shadow 0.2s, transform 0.2s;
            display: flex;
            flex-direction: column;
        }
        .it-card:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .it-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .it-card-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #ffffff;
            flex-shrink: 0;
        }
        .it-card-tag {
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 5px;
        }
        .it-card-tag.jobs        { background: #dbeafe; color: #2563eb; }
        .it-card-tag.internship  { background: #fef3c7; color: #d97706; }
        .it-card-tag.scholarship { background: #ede9fe; color: #7c3aed; }

        .it-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 2px;
        }
        .it-card-company {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .it-card-details {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .it-card-detail {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            color: #64748b;
        }
        .it-card-detail svg { color: #94a3b8; flex-shrink: 0; }

        .it-card-bottom {
            margin-top: auto;
            padding-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .it-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #15803d;
            background: #dcfce7;
            padding: 3px 10px;
            border-radius: 999px;
        }
        .it-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #16a34a;
        }
        .it-apply-btn {
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
            transition: color 0.15s;
        }
        .it-apply-btn:hover { color: #1d4ed8; }

        /* ---- CTA SECTION ---- */
        .it-cta-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 56px;
        }
        .it-cta {
            background: #1e293b;
            border-radius: 20px;
            padding: 52px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .it-cta { flex-direction: column; text-align: center; }
        }
        .it-cta-text h2 {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 8px;
        }
        .it-cta-text p {
            font-size: 14px;
            color: #94a3b8;
            margin: 0;
            max-width: 400px;
            line-height: 1.6;
        }
        .it-cta-btns {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }
        .it-cta-btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 11px 24px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: #dc2626;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .it-cta-btn-primary:hover { background: #b91c1c; }
        .it-cta-btn-secondary {
            display: inline-flex;
            align-items: center;
            padding: 11px 24px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: transparent;
            border: 1.5px solid #475569;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .it-cta-btn-secondary:hover { background: rgba(255,255,255,0.05); border-color: #94a3b8; }
        </style>
    </head>

    <body class="bg-slate-50 text-slate-800 antialiased">
        @unless(View::hasSection('hide_navbar'))
            @include('partials.navbar')
        @endunless

        <main>
            @yield('content')
        </main>

        @unless(View::hasSection('hide_footer'))
            @include('partials.footer')
        @endunless

        @stack('scripts')

        {{-- Google Translate Widget with Protected Filtering & Adaptive Auth Buttons --}}
        <script>
            // Translation Dictionary for Navbar Auth Buttons (Per Language)
            var authTranslations = {
                'id': { signin: 'Masuk', signup: 'Daftar' },
                'en': { signin: 'Login', signup: 'Register' },
                'ja': { signin: 'ログイン', signup: '新規登録' },
                'ko': { signin: '로그인', signup: '회원가입' },
                'zh-CN': { signin: '登录', signup: '注册' },
                'ar': { signin: 'تسجيل الدخول', signup: 'إنشاء حساب' },
                'de': { signin: 'Anmelden', signup: 'Registrieren' },
                'fr': { signin: 'Connexion', signup: "S'inscrire" },
                'es': { signin: 'Iniciar sesión', signup: 'Registrarse' },
                'ru': { signin: 'Войти', signup: 'Регистрация' }
            };

            function updateAuthNavButtons(langCode) {
                var t = authTranslations[langCode] || authTranslations['id'];
                var signinBtn = document.getElementById('it-nav-signin');
                var signupBtn = document.getElementById('it-nav-signup');
                var mSigninBtn = document.getElementById('it-mnav-signin');
                var mSignupBtn = document.getElementById('it-mnav-signup');

                if (signinBtn) signinBtn.textContent = t.signin;
                if (signupBtn) signupBtn.textContent = t.signup;
                if (mSigninBtn) mSigninBtn.textContent = t.signin;
                if (mSignupBtn) mSignupBtn.textContent = t.signup;
            }

            // Filter & Protect Sensitive Elements from Google Translate (Brand Logo, Tech Tags, Code, Auth)
            function applyTranslateProtection() {
                var protectSelectors = [
                    '#it-logo',
                    '.it-f-brand',
                    '#it-lang-dropdown-wrap',
                    '#it-lang-dropdown',
                    '#it-lang-btn',
                    '#it-user-btn',
                    '#it-user-menu',
                    '.it-mobile-user-card',
                    '#it-nav-signin',
                    '#it-nav-signup',
                    '#it-mnav-signin',
                    '#it-mnav-signup',
                    'code',
                    'pre',
                    'kbd',
                    'samp',
                    '.cr-chip',
                    '.sks-chip',
                    '.cr-logo',
                    '.sks-avatar',
                    '.it-status-dot',
                    'a[href^="mailto:"]',
                    'a[href^="tel:"]',
                    'a[href*="wa.me"]',
                    '.notranslate'
                ];

                protectSelectors.forEach(function(sel) {
                    try {
                        document.querySelectorAll(sel).forEach(function(el) {
                            if (!el.classList.contains('notranslate')) {
                                el.classList.add('notranslate');
                            }
                            if (el.getAttribute('translate') !== 'no') {
                                el.setAttribute('translate', 'no');
                            }
                        });
                    } catch (e) {}
                });
            }

            function googleTranslateElementInit() {
                applyTranslateProtection();
                if (!document.getElementById('google_translate_element')) {
                    return;
                }
                new google.translate.TranslateElement({
                    pageLanguage: 'id',
                    autoDisplay: false
                }, 'google_translate_element');
                applyTranslateProtection();
            }

            // Custom Language Switcher Dropdown Controller
            document.addEventListener('DOMContentLoaded', function() {
                applyTranslateProtection();

                var langWrap = document.getElementById('it-lang-dropdown-wrap');
                if (!langWrap) return;

                var langBtn = document.getElementById('it-lang-btn');
                var langDropdown = document.getElementById('it-lang-dropdown');
                var btnFlag = document.getElementById('it-lang-btn-flag');
                var btnCode = document.getElementById('it-lang-btn-code');
                var langItems = langDropdown.querySelectorAll('.it-lang-item');

                function toggleDropdown(open) {
                    var isExpanded = open !== undefined ? open : langDropdown.hasAttribute('hidden');
                    if (isExpanded) {
                        langDropdown.removeAttribute('hidden');
                        langBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        langDropdown.setAttribute('hidden', '');
                        langBtn.setAttribute('aria-expanded', 'false');
                    }
                }

                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var userMenu = document.getElementById('it-user-menu');
                    if (userMenu) userMenu.setAttribute('hidden', '');
                    var chatDrop = document.getElementById('it-chat-dropdown');
                    if (chatDrop) chatDrop.setAttribute('hidden', '');
                    toggleDropdown();
                });

                document.addEventListener('click', function(e) {
                    if (!langWrap.contains(e.target)) {
                        toggleDropdown(false);
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !langDropdown.hasAttribute('hidden')) {
                        toggleDropdown(false);
                    }
                });

                function getSavedLang() {
                    var match = document.cookie.match(/googtrans=\/id\/([a-zA-Z\-]+)/);
                    if (match && match[1]) {
                        return match[1];
                    }
                    return localStorage.getItem('it_selected_lang') || 'id';
                }

                function updateLanguageUI(langCode) {
                    var matchedItem = null;
                    langItems.forEach(function(item) {
                        if (item.getAttribute('data-lang') === langCode) {
                            item.classList.add('it-lang-active');
                            matchedItem = item;
                        } else {
                            item.classList.remove('it-lang-active');
                        }
                    });

                    if (matchedItem) {
                        var code = matchedItem.getAttribute('data-code');
                        var flagSvg = matchedItem.querySelector('.it-flag-icon').innerHTML;
                        if (btnCode) btnCode.textContent = code;
                        if (btnFlag) btnFlag.innerHTML = flagSvg;
                    }

                    // Dynamically adapt Login & Register buttons in Navbar
                    updateAuthNavButtons(langCode);
                    applyTranslateProtection();
                }

                function applyTranslation(langCode) {
                    if (langCode === 'id') {
                        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
                        document.cookie = "googtrans=/id/id; path=/;";
                        document.cookie = "googtrans=/id/id; path=/; domain=" + window.location.hostname + ";";
                    } else {
                        document.cookie = "googtrans=/id/" + langCode + "; path=/;";
                        document.cookie = "googtrans=/id/" + langCode + "; path=/; domain=" + window.location.hostname + ";";
                    }

                    localStorage.setItem('it_selected_lang', langCode);
                    updateLanguageUI(langCode);
                    applyTranslateProtection();

                    var combo = document.querySelector('.goog-te-combo');
                    if (combo) {
                        combo.value = langCode;
                        combo.dispatchEvent(new Event('change'));
                    } else {
                        window.location.reload();
                    }
                }

                langItems.forEach(function(item) {
                    item.addEventListener('click', function() {
                        var lang = this.getAttribute('data-lang');
                        toggleDropdown(false);
                        applyTranslation(lang);
                    });
                });

                var currentLang = getSavedLang();
                updateLanguageUI(currentLang);

                window.addEventListener('load', function() {
                    var current = getSavedLang();
                    updateLanguageUI(current);
                    applyTranslateProtection();
                    if (current && current !== 'id') {
                        var combo = document.querySelector('.goog-te-combo');
                        if (combo && combo.value !== current) {
                            combo.value = current;
                            combo.dispatchEvent(new Event('change'));
                        }
                    }
                });

                // Observe DOM changes to re-protect newly rendered nodes (AJAX/Modals)
                if (window.MutationObserver) {
                    var observer = new MutationObserver(function() {
                        applyTranslateProtection();
                    });
                    observer.observe(document.body, { childList: true, subtree: true });
                }
            });

            // Hapus banner bawaan Google Translate di bagian atas dan pulihkan posisi body
            (function() {
                function purgeGoogleTranslateBanner() {
                    var selectors = [
                        '.goog-te-banner-frame',
                        'iframe.goog-te-banner-frame',
                        '.VIpgJd-ZVi9od-ORHb-OEVmcd',
                        '.VIpgJd-ZVi9od-ORHb-OEVmcd-ti6hGc',
                        '.VIpgJd-ZVi9od-aZ2wEe-wOHMyf',
                        '#goog-gt-tt',
                        '.goog-te-balloon-frame'
                    ];
                    selectors.forEach(function(sel) {
                        document.querySelectorAll(sel).forEach(function(el) {
                            el.style.setProperty('display', 'none', 'important');
                            el.style.setProperty('visibility', 'hidden', 'important');
                            el.style.setProperty('height', '0px', 'important');
                            el.style.setProperty('opacity', '0', 'important');
                            el.style.setProperty('pointer-events', 'none', 'important');
                        });
                    });

                    if (document.body) {
                        if (document.body.style.top && document.body.style.top !== '0px') {
                            document.body.style.setProperty('top', '0px', 'important');
                        }
                        if (document.body.style.position && document.body.style.position !== 'static') {
                            document.body.style.setProperty('position', 'static', 'important');
                        }
                    }
                }

                if (window.MutationObserver) {
                    var observer = new MutationObserver(function() {
                        purgeGoogleTranslateBanner();
                    });
                    observer.observe(document.documentElement, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                }

                window.addEventListener('load', purgeGoogleTranslateBanner);
                document.addEventListener('DOMContentLoaded', purgeGoogleTranslateBanner);
            })();
        </script>
        <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
    </body>
</html>