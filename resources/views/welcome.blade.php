@extends('layouts.app')

@section('title', 'IndoTech — Hubungkan Diri dengan Ekosistem IT Indonesia')

@section('content')
    {{-- ===== HERO (100svh, includes category shortcuts) ===== --}}
    <section class="relative overflow-hidden bg-white text-slate-900 h-svh flex flex-col justify-center">

        {{-- === BLOB BACKGROUND === --}}
        <div class="pointer-events-none absolute left-1/2 -translate-x-1/2 -top-[280px]"
             style="width:1100px; height:1100px; border-radius:9999px;
                    background: radial-gradient(circle, rgba(147,185,255,0.75) 0%, rgba(180,200,255,0.55) 35%, rgba(210,220,255,0.25) 65%, transparent 100%);
                    filter: blur(50px);">
        </div>

        {{-- Content --}}
        <div class="relative mx-auto w-full max-w-5xl px-6 text-center">
            <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                Hubungkan Diri dengan Ekosistem IT Indonesia
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-slate-500 sm:text-lg">
                Platform terdepan untuk menjelajahi institusi pendidikan unggulan, perusahaan teknologi ternama,
                komunitas aktif, dan peluang karir digital di Indonesia.
            </p>

            {{-- Search Bar --}}
            <form action="{{ route('career.index') }}" method="GET" class="mx-auto mt-8 flex max-w-xl overflow-hidden rounded-full border border-slate-200 bg-white p-1.5 shadow-md">
                <div class="flex flex-1 items-center pl-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="q" placeholder="Cari kampus, sekolah, perusahaan, lowongan, magang, event..."
                           class="w-full border-none bg-transparent px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none">
                </div>
                <button type="submit" class="shrink-0 rounded-full bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Cari
                </button>
            </form>

            {{-- Category Shortcuts --}}
            @php
                $cats = [
                    ['Kampus',        '/campus',            'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5'],
                    ['SMK',           '/education',         'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25'],
                    ['Komunitas',     '/community',         'M5.25 10.5h4.5m-4.5 4.5h4.5m-4.5 4.5h4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['Pusat Edukasi', '/knowledge-hub',     'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
                    ['Industri',      '/industry',          'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
                    ['Lowongan',      '/career?type=jobs',  'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0'],
                    ['Siap Kerja',    '/career?type=kerja', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
                    ['Event',         '/events',            'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
                ];
            @endphp

            <div class="mt-10 grid grid-cols-2 gap-6 p-4 mx-auto max-w-fit justify-center sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-8 xl:grid-cols-8">
                @foreach ($cats as [$label, $url, $path])
                    <a href="{{ $url }}" class="group flex flex-col items-center gap-2.5 rounded-2xl border border-slate-200/80 bg-white/70 backdrop-blur-sm p-3.5 transition-all hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg hover:bg-white">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-500 transition-colors group-hover:border-blue-200 group-hover:bg-blue-50 group-hover:text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                            </svg>
                        </span>
                        <span class="text-center text-[12px] font-semibold text-slate-700 group-hover:text-blue-600 leading-snug">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ===== BANNER SLIDER ===== --}}
    @if($banners->count())
    <section class="mx-auto max-w-6xl px-6 pb-12">
        <div class="relative overflow-hidden rounded-xl border border-slate-200/80 shadow-sm" id="banner-slider" onmouseenter="pauseBannerSlider()" onmouseleave="startBannerSlider()">
            {{-- Slides --}}
            <div class="flex transition-transform duration-500 ease-in-out" id="banner-track">
                @foreach ($banners as $banner)
                    <div class="w-full flex-shrink-0">
                        <div class="relative h-[240px] sm:h-[300px] md:h-[360px] overflow-hidden">
                            <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/40 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center px-6 sm:px-12 md:px-16">
                                <div class="max-w-xl text-left">
                                    <h2 class="text-2xl font-black text-white sm:text-3xl md:text-4xl leading-tight drop-shadow-xs">{{ $banner->title }}</h2>
                                    @if($banner->subtitle)
                                        <p class="mt-2.5 text-sm text-slate-200 sm:text-base leading-relaxed max-w-lg">{{ $banner->subtitle }}</p>
                                    @endif
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-blue-700">
                                            {{ $banner->link_text ?? 'Selengkapnya' }}
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Navigation arrows --}}
            @if($banners->count() > 1)
                <button onclick="slideBanner(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition shadow-md" aria-label="Sebelumnya">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </button>
                <button onclick="slideBanner(1)" class="absolute right-4 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/30 hover:bg-black/60 text-white backdrop-blur-md transition shadow-md" aria-label="Selanjutnya">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>

                {{-- Dots --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2" id="banner-dots">
                    @foreach ($banners as $i => $b)
                        <button onclick="goToBanner({{ $i }})" class="h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/75' }}" aria-label="Banner {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <script>
        (function() {
            let currentBanner = 0;
            let bannerInterval = null;
            const track = document.getElementById('banner-track');
            const dots = document.getElementById('banner-dots');
            const total = {{ $banners->count() }};

            if (total <= 1 || !track) return;

            function updateSlider() {
                track.style.transform = 'translateX(-' + (currentBanner * 100) + '%)';
                if (dots) {
                    dots.querySelectorAll('button').forEach(function(dot, i) {
                        dot.className = i === currentBanner
                            ? 'h-2 rounded-full transition-all duration-300 w-6 bg-white'
                            : 'h-2 rounded-full transition-all duration-300 w-2 bg-white/50 hover:bg-white/75';
                    });
                }
            }

            window.slideBanner = function(dir) {
                currentBanner = (currentBanner + dir + total) % total;
                updateSlider();
            };

            window.goToBanner = function(index) {
                currentBanner = index;
                updateSlider();
            };

            window.startBannerSlider = function() {
                if (bannerInterval) clearInterval(bannerInterval);
                bannerInterval = setInterval(function() { window.slideBanner(1); }, 5000);
            };

            window.pauseBannerSlider = function() {
                if (bannerInterval) clearInterval(bannerInterval);
            };

            window.startBannerSlider();
        })();
    </script>
    @endif


    {{-- ===== LOWONGAN PEKERJAAN TERBARU ===== --}}
    @if($latestJobs->count())
    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Lowongan Pekerjaan Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Jelajahi peluang karir teknologi terverifikasi dari berbagai perusahaan mitra.</p>
            </div>
            <a href="{{ route('career.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 transition-colors hover:text-blue-700">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($latestJobs as $job)
                @php
                    $categoryLabel = match(strtolower($job->category)) {
                        'internship' => 'Magang',
                        'remote' => 'Remote',
                        'freelance' => 'Freelance',
                        default => 'Pekerjaan',
                    };
                    $categoryColor = match(strtolower($job->category)) {
                        'internship' => 'bg-amber-100 text-amber-700',
                        'remote' => 'bg-emerald-100 text-emerald-700',
                        'freelance' => 'bg-violet-100 text-violet-700',
                        default => 'bg-blue-100 text-blue-700',
                    };
                @endphp
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl text-sm font-bold text-white" style="background:{{ $job->logo_color }}">{{ $job->logo_text }}</span>
                        <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $categoryColor }}">{{ $categoryLabel }}</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">{{ $job->title }}</h3>
                    <p class="text-sm text-slate-500">{{ $job->company }}</p>
                    <div class="mt-4 flex flex-col gap-2 text-[13px] text-slate-500">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span>{{ $job->location }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span>{{ $job->type }} &middot; {{ $job->salary_range }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            <span>Batas: {{ $job->deadline }}</span>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="flex items-center gap-1.5 text-[13px] font-semibold text-emerald-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Dibuka
                        </span>
                        <a href="{{ route('career.apply', ['type' => $job->category === 'internship' ? 'internships' : 'jobs', 'id' => $job->id]) }}" class="text-sm font-semibold text-blue-600 transition-colors hover:text-blue-700">Lamar Sekarang &rarr;</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif


    {{-- ===== EVENT & ACARA MENDATANG ===== --}}
    @if($upcomingEvents->count())
    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Agenda & Event Teknologi</h2>
                <p class="mt-1 text-sm text-slate-500">Ikuti event, workshop, dan konferensi teknologi dari penyelenggara terpercaya.</p>
            </div>
            <a href="{{ route('event.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 transition-colors hover:text-blue-700">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($upcomingEvents as $event)
                <a href="{{ route('event.show', $event->id) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition-all hover:-translate-y-1 hover:shadow-xl">
                    @if($event->image)
                        <div class="relative h-44 overflow-hidden">
                            <img src="{{ $event->image }}" alt="{{ $event->title }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <span class="absolute left-3 top-3 rounded-md px-2.5 py-1 text-xs font-semibold text-white {{ $event->mode_color }}">{{ $event->mode }}</span>
                        </div>
                    @endif
                    <div class="flex flex-1 flex-col p-5">
                        <span class="mb-2 inline-flex w-fit rounded-md bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $event->category }}</span>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $event->title }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $event->organizer }}</p>
                        <div class="mt-auto flex flex-col gap-1.5 pt-4 text-[13px] text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                <span>{{ $event->short_date ?? $event->date }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span>{{ $event->location }}</span>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-xs font-medium text-slate-400">{{ $event->price }}</span>
                            <span class="text-sm font-semibold text-blue-600 group-hover:text-blue-700">Detail &rarr;</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif


    {{-- ===== JELAJAHI LEBIH LANJUT ===== --}}
    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="grid gap-6 sm:grid-cols-2">
            {{-- Card: Direktori Siap Kerja --}}
            <a href="/career?type=kerja" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-blue-50 to-white p-8 transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Direktori Siap Kerja</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Temukan talenta teknologi siap kerja dari berbagai daerah di Indonesia. Daftarkan diri Anda atau cari kandidat terbaik.</p>
                <span class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                    Jelajahi Sekarang &rarr;
                </span>
            </a>

            {{-- Card: Komunitas Teknologi --}}
            <a href="/community" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-emerald-50 to-white p-8 transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Komunitas Teknologi</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Bergabunglah dengan komunitas IT terbesar di Indonesia. Diskusi, kolaborasi, dan jaringan profesional menanti Anda.</p>
                <span class="mt-4 inline-flex items-center text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Gabung Sekarang &rarr;
                </span>
            </a>
        </div>
    </section>
@endsection
