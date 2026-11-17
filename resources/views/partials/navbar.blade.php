<header id="it-navbar">
    <div id="it-navbar-inner">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="inline-flex items-center leading-none no-underline notranslate" translate="no">
            <span class="text-2xl sm:text-3xl font-black tracking-tight font-sans">
                <span class="text-black">IT</span><span class="text-blue-600">HUB</span>
            </span>
        </a>

        {{-- Desktop Nav --}}
        <nav id="it-nav">
            <a href="/" class="{{ request()->is('/') ? 'it-active' : '' }}">Beranda</a>
            <a href="/education" class="{{ request()->is('education*') || request()->is('campus*') ? 'it-active' : '' }}">Pendidikan</a>
            <a href="/industry" class="{{ request()->is('industry*') ? 'it-active' : '' }}">Industri</a>
            <a href="/career" class="{{ request()->is('career*') ? 'it-active' : '' }}">Karir</a>
            <a href="/events" class="{{ request()->is('events*') ? 'it-active' : '' }}">Acara</a>
            <a href="/community" class="{{ request()->is('community*') ? 'it-active' : '' }}">Komunitas</a>
            <a href="/knowledge-hub" class="{{ request()->is('knowledge-hub*') ? 'it-active' : '' }}">Pusat Pengetahuan</a>
        </nav>

        {{-- Actions --}}
        <div id="it-actions">
            {{-- Custom Language Selector (Dropdown with Flag & 10 Languages) --}}
            <div class="it-lang-menu-wrap notranslate" id="it-lang-dropdown-wrap" translate="no">
                <button type="button" id="it-lang-btn" class="it-lang-btn" aria-haspopup="true" aria-expanded="false" title="Pilih Bahasa">
                    <span id="it-lang-btn-flag" class="it-lang-btn-flag">
                        <svg class="it-flag-svg" viewBox="0 0 24 16">
                            <rect width="24" height="8" fill="#e11d48"/>
                            <rect y="8" width="24" height="8" fill="#ffffff"/>
                        </svg>
                    </span>
                    <span id="it-lang-btn-code" class="it-lang-btn-code">ID</span>
                    <svg class="it-lang-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div id="it-lang-dropdown" class="it-lang-dropdown" hidden>
                    <div class="it-lang-header">
                        <span class="it-lang-header-title">PILIH BAHASA</span>
                        <span class="it-lang-badge">10 Bahasa</span>
                    </div>

                    <div class="it-lang-list">
                        {{-- 1. Bahasa Indonesia --}}
                        <button type="button" class="it-lang-item it-lang-active" data-lang="id" data-code="ID" data-name="Bahasa Indonesia">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="8" fill="#e11d48"/>
                                        <rect y="8" width="24" height="8" fill="#ffffff"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">Bahasa Indonesia</span>
                                    <span class="it-lang-item-sub">Indonesia</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 2. English --}}
                        <button type="button" class="it-lang-item" data-lang="en" data-code="EN" data-name="English">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 60 30">
                                        <clipPath id="uk-clip-nav"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                                        <clipPath id="uk-diag-nav"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
                                        <g clip-path="url(#uk-clip-nav)">
                                            <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                                            <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#uk-diag-nav)" stroke="#C8102E" stroke-width="4"/>
                                            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                                            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
                                        </g>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">English</span>
                                    <span class="it-lang-item-sub">Global</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 3. 日本語 (Japanese) --}}
                        <button type="button" class="it-lang-item" data-lang="ja" data-code="JA" data-name="日本語">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="16" fill="#ffffff"/>
                                        <circle cx="12" cy="8" r="4.8" fill="#bc002d"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">日本語</span>
                                    <span class="it-lang-item-sub">Japan</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 4. 한국어 (Korean) --}}
                        <button type="button" class="it-lang-item" data-lang="ko" data-code="KO" data-name="한국어">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="16" fill="#ffffff"/>
                                        <circle cx="12" cy="8" r="4" fill="#c60c30"/>
                                        <path d="M12,4 a4,4 0 0,0 0,8 a2,2 0 0,1 0,-4 a2,2 0 0,0 0,-4" fill="#003478"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">한국어</span>
                                    <span class="it-lang-item-sub">Korea</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 5. 中文 (简体) (Chinese) --}}
                        <button type="button" class="it-lang-item" data-lang="zh-CN" data-code="ZH" data-name="中文 (简体)">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="16" fill="#de2910"/>
                                        <polygon points="4,2 4.8,4.5 2.6,3 5.4,3 3.2,4.5" fill="#ffde00"/>
                                        <circle cx="7" cy="2" r="0.6" fill="#ffde00"/>
                                        <circle cx="8.5" cy="3.5" r="0.6" fill="#ffde00"/>
                                        <circle cx="8.5" cy="5.5" r="0.6" fill="#ffde00"/>
                                        <circle cx="7" cy="7" r="0.6" fill="#ffde00"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">中文 (简体)</span>
                                    <span class="it-lang-item-sub">China</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 6. العربية (Arabic) --}}
                        <button type="button" class="it-lang-item" data-lang="ar" data-code="AR" data-name="العربية">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="5.33" fill="#00732f"/>
                                        <rect y="5.33" width="24" height="5.33" fill="#ffffff"/>
                                        <rect y="10.66" width="24" height="5.34" fill="#000000"/>
                                        <rect width="6" height="16" fill="#ff0000"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">العربية</span>
                                    <span class="it-lang-item-sub">Arab</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 7. Deutsch (German) --}}
                        <button type="button" class="it-lang-item" data-lang="de" data-code="DE" data-name="Deutsch">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="5.33" fill="#000000"/>
                                        <rect y="5.33" width="24" height="5.33" fill="#dd0000"/>
                                        <rect y="10.66" width="24" height="5.34" fill="#ffce00"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">Deutsch</span>
                                    <span class="it-lang-item-sub">Germany</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 8. Français (French) --}}
                        <button type="button" class="it-lang-item" data-lang="fr" data-code="FR" data-name="Français">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="8" height="16" fill="#002395"/>
                                        <rect x="8" width="8" height="16" fill="#ffffff"/>
                                        <rect x="16" width="8" height="16" fill="#ed2939"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">Français</span>
                                    <span class="it-lang-item-sub">France</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 9. Русский (Russian) --}}
                        <button type="button" class="it-lang-item" data-lang="ru" data-code="RU" data-name="Русский">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="5.33" fill="#ffffff"/>
                                        <rect y="5.33" width="24" height="5.33" fill="#0039a6"/>
                                        <rect y="10.66" width="24" height="5.34" fill="#d52b1e"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">Русский</span>
                                    <span class="it-lang-item-sub">Russia</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>

                        {{-- 10. Español (Spanish) --}}
                        <button type="button" class="it-lang-item" data-lang="es" data-code="ES" data-name="Español">
                            <div class="it-lang-item-left">
                                <span class="it-flag-icon">
                                    <svg class="it-flag-svg" viewBox="0 0 24 16">
                                        <rect width="24" height="4" fill="#aa151b"/>
                                        <rect y="4" width="24" height="8" fill="#f1bf00"/>
                                        <rect y="12" width="24" height="4" fill="#aa151b"/>
                                    </svg>
                                </span>
                                <div class="it-lang-item-info">
                                    <span class="it-lang-item-name">Español</span>
                                    <span class="it-lang-item-sub">Spain</span>
                                </div>
                            </div>
                            <span class="it-lang-check">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Hidden Google Translate Element Target --}}
                <div id="google_translate_element" style="display:none !important; visibility:hidden !important; position:absolute !important; left:-9999px !important;"></div>
            </div>

            @auth
                @php
                    $unreadTotal = $navbarChatSummary['total_unread_count'] ?? 0;
                    $chatCommunities = $navbarChatSummary['communities'] ?? [];
                    $allChatUrl = $navbarChatSummary['all_chat_url'] ?? route('chat.index');
                @endphp

                {{-- Chat Komunitas (Messenger Style Dropdown) --}}
                <div id="it-chat-nav" class="it-chat-nav">
                    <button type="button" id="it-chat-btn" class="it-nav-icon-btn" aria-label="Chat Komunitas" aria-haspopup="true" aria-expanded="false" title="Chat Komunitas">
                        {{-- Modern Messenger Icon --}}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.512 3.736 7.202V22l3.385-1.859c.91.252 1.879.387 2.879.387 5.523 0 10-4.145 10-9.27S17.523 2 12 2zm1.045 12.443l-2.67-2.85-5.21 2.85 5.73-6.082 2.74 2.85 5.14-2.85-5.73 6.082z"/>
                        </svg>
                        <span id="it-chat-badge" class="it-nav-badge" style="{{ $unreadTotal > 0 ? '' : 'display:none;' }}">
                            {{ $unreadTotal > 99 ? '99+' : $unreadTotal }}
                        </span>
                    </button>

                    {{-- Dropdown Container --}}
                    <div id="it-chat-dropdown" class="it-chat-dropdown" hidden>
                        <div class="it-chat-dropdown-header">
                            <h3 class="it-chat-dropdown-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#2563eb">
                                    <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.512 3.736 7.202V22l3.385-1.859c.91.252 1.879.387 2.879.387 5.523 0 10-4.145 10-9.27S17.523 2 12 2zm1.045 12.443l-2.67-2.85-5.21 2.85 5.73-6.082 2.74 2.85 5.14-2.85-5.73 6.082z"/>
                                </svg>
                                Chat Komunitas
                            </h3>
                            <a href="{{ route('chat.index') }}" class="it-chat-dropdown-see-all">Lihat Semua</a>
                        </div>

                        <div id="it-chat-dropdown-list" class="it-chat-dropdown-list">
                            @forelse($chatCommunities as $c)
                                <a href="{{ $c['chat_url'] }}" class="it-chat-item {{ $c['is_unread'] ? 'it-chat-unread' : '' }}" data-community-id="{{ $c['id'] }}">
                                    <div class="it-chat-avatar-wrap">
                                        @if(!empty($c['logo_url']))
                                            <img src="{{ $c['logo_url'] }}" alt="{{ $c['name'] }}" class="it-chat-avatar-img">
                                        @else
                                            <div class="it-chat-avatar-init" style="background: linear-gradient(135deg, {{ $c['gradient'][0] }}, {{ $c['gradient'][1] }});">
                                                {{ $c['initials'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="it-chat-info">
                                        <div class="it-chat-name">{{ $c['name'] }}</div>
                                        <div class="it-chat-snippet-row">
                                            @if($c['sender_name'])
                                                <span class="it-chat-sender">{{ $c['sender_name'] }}:</span>
                                            @endif
                                            <span class="it-chat-snippet {{ !$c['sender_name'] ? 'it-chat-empty-snippet' : '' }}">{{ $c['snippet'] }}</span>
                                        </div>
                                    </div>
                                    <div class="it-chat-meta">
                                        <span class="it-chat-time">{{ $c['time_formatted'] }}</span>
                                        @if($c['unread_count'] > 0)
                                            <span class="it-chat-unread-badge">{{ $c['unread_count'] > 99 ? '99+' : $c['unread_count'] }}</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="it-chat-empty-state">
                                    <div class="it-chat-empty-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.512 3.736 7.202V22l3.385-1.859c.91.252 1.879.387 2.879.387 5.523 0 10-4.145 10-9.27S17.523 2 12 2zm1.045 12.443l-2.67-2.85-5.21 2.85 5.73-6.082 2.74 2.85 5.14-2.85-5.73 6.082z"/>
                                        </svg>
                                    </div>
                                    <p class="it-chat-empty-title">Belum ada obrolan komunitas</p>
                                    <p class="it-chat-empty-desc">Bergabunglah dengan komunitas favorit Anda untuk mulai berdiskusi dan berbagi berkas.</p>
                                    <a href="{{ route('community.index') }}" class="it-chat-empty-btn">
                                        Jelajahi Komunitas
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        <div class="it-chat-dropdown-footer">
                            <a href="{{ route('chat.index') }}" class="it-chat-btn-all">Buka Semua Chat</a>
                        </div>
                    </div>
                </div>

                {{-- User Profile & Dropdown --}}
                <div id="it-user" class="it-user">
                    <button type="button" id="it-user-btn" aria-haspopup="true" aria-expanded="false" title="{{ Auth::user()->name }}" aria-label="Profil {{ Auth::user()->name }}">
                        @if(!empty($navbarLogoUrl))
                            <img src="{{ $navbarLogoUrl }}" alt="{{ Auth::user()->name }}" class="it-user-avatar-img">
                        @else
                            <span class="it-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        @endif
                    </button>
                    <div id="it-user-menu" class="it-user-menu" hidden>
                        <div class="it-user-head">
                            <div class="it-user-head-name notranslate" translate="no">{{ Auth::user()->name }}</div>
                            <span class="it-user-role">
                                @php
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin' => 'Admin',
                                        'school' => 'Sekolah',
                                        'school_admin' => 'Admin Sekolah',
                                        'university' => 'Universitas',
                                        'company' => 'Perusahaan',
                                        'user' => 'Pengguna',
                                    ];
                                    $roleKey = Auth::user()->role;
                                @endphp
                                {{ $roleLabels[$roleKey] ?? ucfirst(str_replace(['-', '_'], ' ', $roleKey)) }}
                            </span>
                        </div>
                        @if(!in_array(Auth::user()->role, ['school', 'university', 'company', 'super_admin'], true))
                            <a href="{{ route('profile.edit') }}" class="it-user-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0 1 16 0"/></svg> Profil Saya</a>
                        @endif
                        <a href="{{ route('community.my') }}" class="it-user-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Komunitas Saya</a>
                        <a href="{{ route('chat.index') }}" class="it-user-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg> Chat Komunitas</a>
                        @if (in_array(Auth::user()->role, ['super_admin', 'school', 'university', 'company']))
                            <a href="{{ route('dashboard') }}" class="it-user-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg> Dashboard</a>
                        @endif
                        <form method="POST" data-async data-async-follow action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="it-user-item it-user-logout"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3m7 14 5-5-5-5m5 5H9"/></svg> Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="it-btn-signin notranslate" id="it-nav-signin" translate="no">Masuk</a>
                <a href="/register" class="it-btn-signup notranslate" id="it-nav-signup" translate="no">Daftar</a>
            @endauth

            <button id="it-mobile-toggle" aria-label="Menu" aria-controls="it-mobile-menu" aria-expanded="false">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path class="it-icon-bars" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    <path class="it-icon-close" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" style="display:none;"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="it-mobile-menu">
        @auth
            <div class="it-mobile-user-card">
                @if(!empty($navbarLogoUrl))
                    <img src="{{ $navbarLogoUrl }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                @else
                    <span class="it-avatar" style="width:36px;height:36px;font-size:14px;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
                <div class="it-mobile-user-info">
                    <div class="it-mobile-user-name notranslate" translate="no">{{ Auth::user()->name }}</div>
                    <span class="it-user-role" style="font-size:11px;padding:2px 8px;">
                        {{ $roleLabels[$roleKey] ?? ucfirst(str_replace(['-', '_'], ' ', $roleKey)) }}
                    </span>
                </div>
            </div>
        @endauth

        <nav class="it-mobile-links">
            <a href="/" class="{{ request()->is('/') ? 'it-active' : '' }}">
                <span>Beranda</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="/education" class="{{ request()->is('education*') || request()->is('campus*') ? 'it-active' : '' }}">
                <span>Pendidikan</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="/industry" class="{{ request()->is('industry*') ? 'it-active' : '' }}">
                <span>Industri</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="/career" class="{{ request()->is('career*') ? 'it-active' : '' }}">
                <span>Karir</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="/events" class="{{ request()->is('events*') ? 'it-active' : '' }}">
                <span>Acara</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="/community" class="{{ (request()->is('community') || request()->is('community/create') || (request()->is('community/*') && !request()->routeIs('community.my*') && !request()->is('community/*/manage*'))) ? 'it-active' : '' }}">
                <span>Komunitas</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            @auth
                <a href="{{ route('community.my') }}" class="{{ request()->routeIs('community.my*') || request()->is('community/*/manage*') ? 'it-active' : '' }}">
                    <span>Komunitas Saya</span>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="{{ route('chat.index') }}" class="it-mobile-chat-link {{ request()->is('community/*/chat*') || request()->is('chat*') ? 'it-active' : '' }}">
                    <span style="display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#2563eb"><path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.512 3.736 7.202V22l3.385-1.859c.91.252 1.879.387 2.879.387 5.523 0 10-4.145 10-9.27S17.523 2 12 2zm1.045 12.443l-2.67-2.85-5.21 2.85 5.73-6.082 2.74 2.85 5.14-2.85-5.73 6.082z"/></svg>
                        Chat Komunitas
                    </span>
                    @if(($navbarChatSummary['total_unread_count'] ?? 0) > 0)
                        <span class="it-mobile-unread-badge">{{ $navbarChatSummary['total_unread_count'] > 99 ? '99+' : $navbarChatSummary['total_unread_count'] }}</span>
                    @else
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    @endif
                </a>
            @endauth
            <a href="/knowledge-hub" class="{{ request()->is('knowledge-hub*') ? 'it-active' : '' }}">
                <span>Pusat Pengetahuan</span>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </nav>

        <div class="it-mobile-btns">
            @auth
                @if(!in_array(Auth::user()->role, ['school', 'university', 'company', 'super_admin'], true))
                    <a href="{{ route('profile.edit') }}" class="it-mbtn-profile">Profil Saya</a>
                @endif
                @if (in_array(Auth::user()->role, ['super_admin', 'school', 'university', 'company']))
                    <a href="/admin" class="it-mbtn-dash">Dashboard</a>
                @endif
                <form method="POST" data-async data-async-follow action="{{ route('logout') }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="it-mbtn-logout">Keluar</button>
                </form>
            @else
                <a href="/login" class="it-mbtn-signin notranslate" id="it-mnav-signin" translate="no">Masuk</a>
                <a href="/register" class="it-mbtn-signup notranslate" id="it-mnav-signup" translate="no">Daftar</a>
            @endauth
        </div>
    </div>
</header>

<script>
    (function () {
        var mobileBtn = document.getElementById('it-mobile-toggle');
        var mobileMenu = document.getElementById('it-mobile-menu');
        var chatBtn = document.getElementById('it-chat-btn');
        var chatDropdown = document.getElementById('it-chat-dropdown');
        var chatBadge = document.getElementById('it-chat-badge');
        var userBtn = document.getElementById('it-user-btn');
        var userMenu = document.getElementById('it-user-menu');

        function closeAllDropdowns() {
            if (chatDropdown) {
                chatDropdown.hidden = true;
                if (chatBtn) chatBtn.setAttribute('aria-expanded', 'false');
            }
            if (userMenu) {
                userMenu.hidden = true;
                if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
            }
        }

        function closeMobileMenu() {
            if (mobileMenu && mobileMenu.classList.contains('it-open')) {
                mobileMenu.classList.remove('it-open');
                if (mobileBtn) {
                    mobileBtn.setAttribute('aria-expanded', 'false');
                    mobileBtn.classList.remove('it-open');
                }
            }
        }

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = mobileMenu.classList.toggle('it-open');
                mobileBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                mobileBtn.classList.toggle('it-open', open);
                if (open) {
                    closeAllDropdowns();
                }
            });
        }

        // Chat dropdown toggle
        if (chatBtn && chatDropdown) {
            chatBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var wasOpen = !chatDropdown.hidden;
                closeAllDropdowns();
                closeMobileMenu();
                if (!wasOpen) {
                    chatDropdown.hidden = false;
                    chatBtn.setAttribute('aria-expanded', 'true');
                    refreshNavbarChat();
                }
            });
        }

        // User menu dropdown toggle
        if (userBtn && userMenu) {
            userBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var wasOpen = !userMenu.hidden;
                closeAllDropdowns();
                closeMobileMenu();
                if (!wasOpen) {
                    userMenu.hidden = false;
                    userBtn.setAttribute('aria-expanded', 'true');
                }
            });
        }

        // Click outside closes dropdowns and mobile menu
        document.addEventListener('click', function (e) {
            var inChat = chatBtn && (chatBtn.contains(e.target) || (chatDropdown && chatDropdown.contains(e.target)));
            var inUser = userBtn && (userBtn.contains(e.target) || (userMenu && userMenu.contains(e.target)));
            var inMobile = mobileBtn && (mobileBtn.contains(e.target) || (mobileMenu && mobileMenu.contains(e.target)));

            if (!inChat && !inUser) {
                closeAllDropdowns();
            }
            if (!inMobile) {
                closeMobileMenu();
            }
        });

        // Escape closes dropdowns and mobile menu
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
                closeMobileMenu();
            }
        });

        // Auto close mobile menu if resized to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024) {
                closeMobileMenu();
            }
        });

        // Background polling for unread chat updates
        @auth
        function refreshNavbarChat() {
            fetch("{{ route('community.chat.navbar_summary') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (payload) {
                if (!payload || !payload.success || !payload.data) return;
                var data = payload.data;
                var unread = data.total_unread_count || 0;

                // Update badge
                if (chatBadge) {
                    if (unread > 0) {
                        chatBadge.textContent = unread > 99 ? '99+' : unread;
                        chatBadge.style.display = 'inline-flex';
                    } else {
                        chatBadge.style.display = 'none';
                    }
                }

                // If dropdown is open and we have communities data, update list HTML
                if (chatDropdown && !chatDropdown.hidden && data.communities) {
                    renderChatList(data.communities);
                }
            })
            .catch(function () {
                // Ignore transient network errors
            });
        }

        function renderChatList(communities) {
            var listEl = document.getElementById('it-chat-dropdown-list');
            if (!listEl) return;

            if (!communities || communities.length === 0) {
                listEl.innerHTML = '<div class="it-chat-empty-state">' +
                    '<div class="it-chat-empty-icon">' +
                        '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">' +
                            '<path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.512 3.736 7.202V22l3.385-1.859c.91.252 1.879.387 2.879.387 5.523 0 10-4.145 10-9.27S17.523 2 12 2zm1.045 12.443l-2.67-2.85-5.21 2.85 5.73-6.082 2.74 2.85 5.14-2.85-5.73 6.082z"/>' +
                        '</svg>' +
                    '</div>' +
                    '<p class="it-chat-empty-title">Belum ada obrolan komunitas</p>' +
                    '<p class="it-chat-empty-desc">Bergabunglah dengan komunitas favorit Anda untuk mulai berdiskusi dan berbagi berkas.</p>' +
                    '<a href="{{ route('community.index') }}" class="it-chat-empty-btn">Jelajahi Komunitas</a>' +
                '</div>';
                return;
            }

            var html = '';
            for (var i = 0; i < communities.length; i++) {
                var c = communities[i];
                var unreadCls = c.is_unread ? 'it-chat-unread' : '';
                var avatarHtml = c.logo_url
                    ? '<img src="' + escapeHtml(c.logo_url) + '" alt="' + escapeHtml(c.name) + '" class="it-chat-avatar-img">'
                    : '<div class="it-chat-avatar-init" style="background:linear-gradient(135deg, ' + (c.gradient[0] || '#2563eb') + ', ' + (c.gradient[1] || '#1d4ed8') + ');">' + escapeHtml(c.initials) + '</div>';

                var senderHtml = c.sender_name
                    ? '<span class="it-chat-sender">' + escapeHtml(c.sender_name) + ':</span> '
                    : '';

                var badgeHtml = c.unread_count > 0
                    ? '<span class="it-chat-unread-badge">' + (c.unread_count > 99 ? '99+' : c.unread_count) + '</span>'
                    : '';

                html += '<a href="' + escapeHtml(c.chat_url) + '" class="it-chat-item ' + unreadCls + '" data-community-id="' + c.id + '">' +
                    '<div class="it-chat-avatar-wrap">' + avatarHtml + '</div>' +
                    '<div class="it-chat-info">' +
                        '<div class="it-chat-name">' + escapeHtml(c.name) + '</div>' +
                        '<div class="it-chat-snippet-row">' +
                            senderHtml +
                            '<span class="it-chat-snippet ' + (!c.sender_name ? 'it-chat-empty-snippet' : '') + '">' + escapeHtml(c.snippet) + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="it-chat-meta">' +
                        '<span class="it-chat-time">' + escapeHtml(c.time_formatted) + '</span>' +
                        badgeHtml +
                    '</div>' +
                '</a>';
            }

            listEl.innerHTML = html;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Poll every 20 seconds
        setInterval(refreshNavbarChat, 20000);
        @endauth
    })();
</script>
