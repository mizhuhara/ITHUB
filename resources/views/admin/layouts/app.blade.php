<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — ITHub')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f8fafd] min-h-screen text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

{{-- ===== MOBILE SIDEBAR BACKDROP ===== --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<div class="flex min-h-screen">
    {{-- ===== SIDEBAR ===== --}}
    @include('admin.partials.sidebar')

    {{-- ===== MAIN WRAPPER ===== --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Header --}}
        @include('admin.partials.header')

        {{-- Main Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{-- Flash Alert Messages --}}
            @if(session('success') && !request()->routeIs('*report-bug*'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-emerald-600"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- ===== ADMIN ASSETS: POPUPS & MODALS ===== --}}
@include('admin.assets.splash-toast')
@include('admin.assets.delete-modal')
@include('admin.assets.show-modal')
@include('admin.assets.update-modal')
@include('admin.assets.verification-pending-modal')
@include('partials.async-form')

{{-- Mobile Sidebar Drawer Toggle Script --}}
<script>
    (function() {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('admin-sidebar-backdrop');
        const toggleBtn = document.getElementById('admin-sidebar-toggle');
        const closeBtn = document.getElementById('admin-sidebar-close');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }

        toggleBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        backdrop?.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    })();
</script>

@stack('scripts')
</body>
</html>
