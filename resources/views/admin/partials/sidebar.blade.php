<aside class="w-64 shrink-0 bg-white border-r border-slate-200/80 flex flex-col h-screen sticky top-0 z-40 select-none">
    @php 
        $userRole = auth()->user()?->role ?? 'user'; 
        if ($userRole === 'school_admin') {
            $userRole = 'school';
        }
        $dashboardRoute = match($userRole) {
            'school' => route('dashboard.school'),
            'university' => route('dashboard.university'),
            'company' => route('dashboard.company'),
            'user' => route('dashboard.user'),
            default => route('admin.dashboard'),
        };
        $isDashboardActive = request()->routeIs('admin.dashboard') 
            || request()->routeIs('dashboard.school') 
            || request()->routeIs('dashboard.university') 
            || request()->routeIs('dashboard.company') 
            || request()->routeIs('dashboard.user')
            || (request()->routeIs('dashboard') && !request()->routeIs('dashboard.*'));
        $pendingVerificationsCount = in_array($userRole, ['super_admin', 'admin'], true) 
            ? \App\Models\User::where('status', 'pending')->count() 
            : 0;
    @endphp

    {{-- Header / Brand --}}
    <div class="h-16 px-6 flex items-center border-b border-slate-200/80 shrink-0">
        <a href="{{ $dashboardRoute }}" class="group flex items-center gap-2">
            <span class="text-[21px] font-black text-slate-900 tracking-tight leading-none group-hover:text-brand-600 transition-colors">
                Indo<span class="text-brand-600">Tech</span>
            </span>
        </a>
    </div>

    {{-- Nav with Smooth Invisible/Ultra-thin Scrollbar --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5 custom-sidebar-nav min-h-0">
        @php
            $isUserAndVerification = request()->routeIs('admin.users.*') || request()->routeIs('admin.verification.*');
            $menu = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'grid',
                    'active' => $isDashboardActive,
                    'href' => $dashboardRoute,
                    'roles' => ['super_admin', 'admin', 'school', 'university', 'company'],
                ],
                [
                    'label' => 'Applications',
                    'icon' => 'jobs',
                    'active' => request()->routeIs('admin.applications.*'),
                    'href' => route('admin.applications.index'),
                    'roles' => ['super_admin', 'admin'],
                ],
                [
                    'label' => 'Users and Verification',
                    'icon' => 'users',
                    'active' => $isUserAndVerification,
                    'href' => route('admin.users.index'),
                    'arrow' => true,
                    'is_group' => true,
                    'roles' => ['super_admin', 'admin'],
                ],
                [
                    'label' => $userRole === 'school' ? 'Informasi Sekolah' : 'School',
                    'icon' => 'school',
                    'active' => request()->routeIs('admin.schools.*') && !request()->routeIs('admin.school*.report-bug*'),
                    'href' => route('admin.schools.index'),
                    'roles' => ['super_admin', 'admin', 'school'],
                ],
                [
                    'label' => $userRole === 'university' ? 'Informasi Kampus' : 'Universities',
                    'icon' => 'university',
                    'active' => request()->routeIs('admin.univ.*') && !request()->routeIs('admin.univ*.report-bug*'),
                    'href' => route('admin.univ.index'),
                    'roles' => ['super_admin', 'admin', 'university'],
                ],
                [
                    'label' => $userRole === 'company' ? 'Informasi Perusahaan' : 'Company',
                    'icon' => 'company',
                    'active' => request()->routeIs('admin.company.*') && !request()->routeIs('admin.company*.report-bug*'),
                    'href' => route('admin.company.index'),
                    'roles' => ['super_admin', 'admin', 'company'],
                ],
                [
                    'label' => in_array($userRole, ['company'], true) ? 'Lowongan Kerja & Magang' : 'Jobs',
                    'icon' => 'jobs',
                    'active' => request()->routeIs('admin.jobs.*'),
                    'href' => route('admin.jobs.index'),
                    'roles' => ['super_admin', 'admin', 'company'],
                ],
                [
                    'label' => 'Internships',
                    'icon' => 'internship',
                    'active' => request()->routeIs('admin.internships.*'),
                    'href' => route('admin.internships.index'),
                    'roles' => ['super_admin', 'admin'],
                ],
                [
                    'label' => 'Event',
                    'icon' => 'events',
                    'active' => request()->routeIs('admin.events.*'),
                    'href' => route('admin.events.index'),
                    'roles' => ['super_admin', 'admin', 'school', 'university', 'company'],
                ],
                [
                    'label' => in_array($userRole, ['school', 'university', 'company'], true) ? 'Artikel' : 'Articles',
                    'icon' => 'articles',
                    'active' => request()->routeIs('admin.articles.*'),
                    'href' => route('admin.articles.index'),
                    'roles' => ['super_admin', 'admin', 'school', 'university', 'company'],
                ],
                [
                    'label' => 'Laporkan Masalah',
                    'icon' => 'bug',
                    'active' => request()->routeIs('admin.school*.report-bug*') || request()->routeIs('admin.univ*.report-bug*') || request()->routeIs('admin.company*.report-bug*'),
                    'href' => $userRole === 'university' ? route('admin.univ.report-bug') : ($userRole === 'company' ? route('admin.company.report-bug') : route('admin.school.report-bug')),
                    'roles' => ['school', 'university', 'company'],
                ],
                [
                    'label' => 'Lihat Website',
                    'icon' => 'globe',
                    'active' => false,
                    'href' => '/',
                    'target' => '_blank',
                    'roles' => ['super_admin', 'admin', 'school', 'university', 'company'],
                ],
                [
                    'label' => 'Community',
                    'icon' => 'community',
                    'active' => request()->routeIs('admin.community.*'),
                    'href' => route('admin.community.index'),
                    'roles' => ['super_admin', 'admin'],
                ],
                [
                    'label' => 'Reports',
                    'icon' => 'reports',
                    'active' => request()->routeIs('admin.reports.*'),
                    'href' => route('admin.reports.index'),
                    'roles' => ['super_admin', 'admin'],
                ],
            ];

            // Filter: hanya menu yang role boleh lihat
            $menu = array_values(array_filter($menu, fn ($m) => in_array($userRole, $m['roles'], true)));
        @endphp

        @foreach ($menu as $item)
            @if (!empty($item['is_group']))
                <div class="space-y-0.5">
                    <button type="button" 
                            onclick="toggleSubmenu('user-verification-submenu', 'user-verification-arrow')"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 cursor-pointer select-none relative group
                                   {{ $item['active'] ? 'bg-blue-50/80 text-brand-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        @if ($item['active'])
                            <span class="absolute left-0 top-2 bottom-2 w-1 bg-brand-600 rounded-r-full"></span>
                        @endif
                        <span class="{{ $item['active'] ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20c1.2-3.2 3.7-4.8 6.5-4.8s5.3 1.6 6.5 4.8"/><path stroke-linecap="round" d="M16 5.5a3 3 0 0 1 0 5.8M18.5 15.6c1.2 1.1 2 2.4 2.6 4.4"/></svg>
                        </span>
                        <span class="flex-1 text-left tracking-tight">{{ $item['label'] }}</span>
                        
                        @if($pendingVerificationsCount > 0)
                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 leading-none mr-1">
                                {{ $pendingVerificationsCount }}
                            </span>
                        @endif

                        <svg id="user-verification-arrow" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                             class="transition-transform duration-200 {{ $item['active'] ? 'rotate-180 text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }}">
                            <path d="m8 10 4 4 4-4"/>
                        </svg>
                    </button>

                    {{-- Submenu Items with Elegant Connected Tree Line --}}
                    <div id="user-verification-submenu" class="relative pl-7 pr-1 space-y-0.5 py-0.5 {{ $item['active'] ? '' : 'hidden' }} transition-all duration-200">
                        <div class="absolute left-5 top-1 bottom-1 w-px bg-slate-200"></div>

                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center justify-between px-3 py-1.5 rounded-lg text-[12.5px] font-medium transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-brand-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' }}">
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.verification.index') }}" 
                           class="flex items-center justify-between px-3 py-1.5 rounded-lg text-[12.5px] font-medium transition-all duration-150 {{ request()->routeIs('admin.verification.*') ? 'bg-brand-600 text-white shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900' }}">
                            <span>Verification</span>
                            @if($pendingVerificationsCount > 0)
                                <span class="px-1.5 py-0.2 text-[10px] font-bold rounded-full {{ request()->routeIs('admin.verification.*') ? 'bg-white text-brand-600' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $pendingVerificationsCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            @else
                <a href="{{ $item['href'] }}"
                   @if(!empty($item['target'])) target="{{ $item['target'] }}" rel="noopener" @endif
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 relative group
                          {{ $item['active']
                              ? 'bg-blue-50/80 text-brand-600 font-semibold'
                              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    
                    {{-- Active blue left edge pill indicator --}}
                    @if ($item['active'])
                        <span class="absolute left-0 top-2 bottom-2 w-1 bg-brand-600 rounded-r-full"></span>
                    @endif

                    <span class="{{ $item['active'] ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors">
                        @if ($item['icon'] === 'grid')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        @elseif ($item['icon'] === 'users')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20c1.2-3.2 3.7-4.8 6.5-4.8s5.3 1.6 6.5 4.8"/><path stroke-linecap="round" d="M16 5.5a3 3 0 0 1 0 5.8M18.5 15.6c1.2 1.1 2 2.4 2.6 4.4"/></svg>
                        @elseif ($item['icon'] === 'school')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5 2 9.5l10 5 10-5-10-5Zm0 7.5v6.5m6-4.5v3.5c0 1.5-2.69 3-6 3s-6-1.5-6-3V12"/></svg>
                        @elseif ($item['icon'] === 'university')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 9h16M4 9l8-5 8 5M5 12v6m4.5-6v6m5-6v6M19 12v6M3 21h18"/></svg>
                        @elseif ($item['icon'] === 'company')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path stroke-linecap="round" d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/></svg>
                        @elseif ($item['icon'] === 'jobs')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 12h18"/></svg>
                        @elseif ($item['icon'] === 'internship')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/><path stroke-linecap="round" d="M19 8v6m3-3h-6"/></svg>
                        @elseif ($item['icon'] === 'events')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 8h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg>
                        @elseif ($item['icon'] === 'articles')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
                        @elseif ($item['icon'] === 'community')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                        @elseif ($item['icon'] === 'reports')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                        @elseif ($item['icon'] === 'bug')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        @elseif ($item['icon'] === 'globe')
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a14.5 14.5 0 0 0 0 20M12 2a14.5 14.5 0 0 1 0 20M2 12h20"/></svg>
                        @endif
                    </span>

                    <span class="flex-1 tracking-tight">{{ $item['label'] }}</span>

                    @if (!empty($item['target']))
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 group-hover:text-slate-600 transition-colors shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    @endif

                    @if (! empty($item['arrow']))
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="text-slate-400 group-hover:text-slate-600"><path d="m8 10 4 4 4-4"/></svg>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Sidebar footer / Actions --}}
    <div class="p-3 border-t border-slate-100 shrink-0 bg-white space-y-1">
        <form method="POST" data-async data-async-follow action="{{ route('logout') }}" class="w-full m-0">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-red-600 hover:bg-red-50/80 transition-all duration-150 cursor-pointer text-left group">
                <span class="text-slate-400 group-hover:text-red-500 transition-colors">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </span>
                <span class="font-medium">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<style>
    /* Custom Sleek Scrollbar (invisible by default, subtle rounded bar on hover) */
    .custom-sidebar-nav {
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
        transition: scrollbar-color 0.2s ease-in-out;
    }
    .custom-sidebar-nav:hover {
        scrollbar-color: #cbd5e1 transparent;
    }
    .custom-sidebar-nav::-webkit-scrollbar {
        width: 5px;
    }
    .custom-sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-sidebar-nav::-webkit-scrollbar-thumb {
        background: transparent;
        border-radius: 9999px;
        transition: background-color 0.2s;
    }
    .custom-sidebar-nav:hover::-webkit-scrollbar-thumb {
        background: #cbd5e1;
    }
    .custom-sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    if (typeof window.toggleSubmenu === 'undefined') {
        window.toggleSubmenu = function(submenuId, arrowId) {
            const submenu = document.getElementById(submenuId);
            const arrow = document.getElementById(arrowId);
            if (submenu) {
                submenu.classList.toggle('hidden');
            }
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        };
    }
</script>
