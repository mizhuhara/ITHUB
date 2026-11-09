@extends('layouts.app')

@section('title', $keyword ? 'Pencarian: "' . $keyword . '" — IndoTech' : 'Pencarian Global — IndoTech')

@section('content')
<div class="min-h-screen bg-slate-50/70 pb-20 pt-8 sm:pt-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Top Header & Search Bar --}}
        <div class="mb-10 text-center">
            <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl md:text-4xl">
                Eksplorasi Ekosistem IT Indonesia
            </h1>
            <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">
                Temukan kampus, sekolah vokasi, mitra industri, karir, event, dan komunitas dalam satu pencarian terpadu.
            </p>

            {{-- Main Search Box --}}
            <form action="{{ route('search') }}" method="GET" class="mx-auto mt-6 flex max-w-2xl overflow-hidden rounded-full border border-slate-200 bg-white p-1.5 shadow-md shadow-slate-100 transition-all focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-100">
                <input type="hidden" name="type" value="{{ $activeType !== 'all' ? $activeType : 'all' }}">
                <div class="flex flex-1 items-center pl-4">
                    <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Ketik kata kunci pencarian (nama kampus, teknologi, kota, dll)..."
                           class="w-full border-none bg-transparent px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none"
                           autofocus>
                </div>
                <button type="submit" class="shrink-0 rounded-full bg-blue-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700">
                    Cari
                </button>
            </form>

            @if($keyword !== '')
                <p class="mt-4 text-xs font-semibold text-slate-500">
                    Menampilkan hasil untuk: <span class="font-bold text-slate-800">"{{ $keyword }}"</span>
                    &middot; Ditemukan <span class="text-blue-600 font-extrabold">{{ number_format($counts['all']) }}</span> hasil
                </p>
            @endif
        </div>

        {{-- Filter Category Tabs --}}
        @php
            $tabItems = [
                ['key' => 'all',       'label' => 'Semua',              'count' => $counts['all']],
                ['key' => 'campus',    'label' => 'Kampus',             'count' => $counts['campus']],
                ['key' => 'school',    'label' => 'SMK / Vokasi',       'count' => $counts['school']],
                ['key' => 'company',   'label' => 'Industri',           'count' => $counts['company']],
                ['key' => 'career',    'label' => 'Karir & Lowongan',   'count' => $counts['career']],
                ['key' => 'event',     'label' => 'Event IT',           'count' => $counts['event']],
                ['key' => 'community', 'label' => 'Komunitas',          'count' => $counts['community']],
                ['key' => 'article',   'label' => 'Pusat Pengetahuan',  'count' => $counts['article']],
            ];
        @endphp

        <div class="mb-8 flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar border-b border-slate-200">
            @foreach($tabItems as $tab)
                @php $isActive = ($activeType === $tab['key']); @endphp
                <a href="{{ route('search', array_merge(request()->query(), ['type' => $tab['key'], 'page' => 1])) }}"
                   class="inline-flex shrink-0 items-center gap-2 rounded-full px-4 py-2 text-xs sm:text-sm font-bold transition {{ $isActive ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-extrabold {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ number_format($tab['count']) }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- ============================================================ --}}
        {{-- EMPTY STATE: NO KEYWORD OR ZERO RESULTS                      --}}
        {{-- ============================================================ --}}
        @if($keyword === '')
            <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Mulai Pencarian Anda</h3>
                <p class="mx-auto mt-1 max-w-md text-xs sm:text-sm text-slate-500">
                    Ketik nama kampus, sekolah vokasi, mitra industri, lowongan pekerjaan, komunitas, atau agenda acara yang ingin Anda jelajahi.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 self-center">Rekomendasi kata kunci:</span>
                    @foreach(['Teknologi', 'Laravel', 'Jakarta', 'SMK', 'Universitas', 'Startup', 'Magang', 'Webinar'] as $suggest)
                        <a href="{{ route('search', ['q' => $suggest]) }}"
                           class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 transition">
                            {{ $suggest }}
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif($counts['all'] === 0)
            <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Tidak ada hasil ditemukan</h3>
                <p class="mx-auto mt-1 max-w-md text-xs sm:text-sm text-slate-500">
                    Kami tidak menemukan hasil untuk kata kunci <strong class="text-slate-800">"{{ $keyword }}"</strong> di seluruh ekosistem. Coba periksa ejaan atau gunakan istilah yang lebih umum.
                </p>
            </div>
        @else

            {{-- ============================================================ --}}
            {{-- VIEW MODE 1: ALL RESULTS (SUMMARY PER CATEGORY)              --}}
            {{-- ============================================================ --}}
            @if($activeType === 'all')
                <div class="space-y-12">

                    {{-- 1. KAMPUS / PERGURUAN TINGGI --}}
                    @if($results['campuses']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Kampus &amp; Perguruan Tinggi</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['campus'] }}
                                    </span>
                                </div>
                                @if($counts['campus'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'campus']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['campus'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['campuses'] as $c)
                                    @php
                                        $banner = (!empty($c->gallery) && is_array($c->gallery) && !empty($c->gallery[0]))
                                            ? $c->gallery[0]
                                            : 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($c->tags) && is_array($c->tags)
                                            ? $c->tags
                                            : array_values(array_filter([$c->type ?: 'Perguruan Tinggi', $c->accreditation ? 'Akreditasi ' . $c->accreditation : null]));
                                        $loc = $c->city ? $c->city . ($c->province ? ', ' . $c->province : '') : ($c->location ?: 'Indonesia');
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Logo --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $c->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($c->logo_url))
                                                        <img src="{{ $c->logo_url }}" alt="{{ $c->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $c->logo_bg ?: 'bg-blue-700' }}">
                                                            {{ $c->logo_text ?: mb_substr($c->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('campus.show', $c->npsn ?: $c->id) }}">
                                                            {{ $c->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $loc }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $c->accreditation ? 'Akreditasi ' . $c->accreditation : 'Terverifikasi' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('campus.show', $c->npsn ?: $c->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 2. SEKOLAH VOKASI / SMK --}}
                    @if($results['schools']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                            <line x1="8" y1="21" x2="16" y2="21"/>
                                            <line x1="12" y1="17" x2="12" y2="21"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Sekolah Vokasi &amp; SMK</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['school'] }}
                                    </span>
                                </div>
                                @if($counts['school'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'school']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['school'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['schools'] as $s)
                                    @php
                                        $banner = (!empty($s->gallery) && is_array($s->gallery) && !empty($s->gallery[0]))
                                            ? $s->gallery[0]
                                            : 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1000&q=80';
                                        $tags = collect($s->competencies ?? [])->merge($s->tags ?? [])->filter()->unique()->values()->all();
                                        if (empty($tags)) {
                                            $tags = array_values(array_filter([$s->institution_type ?: 'SMK IT', $s->accreditation ? 'Akreditasi ' . $s->accreditation : null]));
                                        }
                                        $loc = $s->city ? $s->city . ($s->province ? ', ' . $s->province : '') : ($s->location ?: 'Indonesia');
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Logo --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $s->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($s->logo_url))
                                                        <img src="{{ $s->logo_url }}" alt="{{ $s->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $s->logo_bg ?: 'bg-emerald-600' }}">
                                                            {{ $s->logo_text ?: mb_substr($s->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('education.show', $s->npsn ?: $s->id) }}">
                                                            {{ $s->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $loc }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $s->accreditation ? 'Akreditasi ' . $s->accreditation : 'Terverifikasi' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('education.show', $s->npsn ?: $s->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 3. MITRA INDUSTRI / PERUSAHAAN --}}
                    @if($results['companies']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Mitra Industri &amp; Perusahaan</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['company'] }}
                                    </span>
                                </div>
                                @if($counts['company'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'company']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['company'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['companies'] as $comp)
                                    @php
                                        $banner = (!empty($comp->gallery) && is_array($comp->gallery) && !empty($comp->gallery[0]))
                                            ? $comp->gallery[0]
                                            : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1000&q=80';
                                        $tags = collect($comp->specialties ?? [])->merge($comp->tags ?? [])->merge([$comp->industry])->filter()->unique()->values()->all();
                                        if (empty($tags)) {
                                            $tags = [$comp->industry ?: 'Teknologi & Solusi'];
                                        }
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Logo --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $comp->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($comp->logo_url))
                                                        <img src="{{ $comp->logo_url }}" alt="{{ $comp->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $comp->logo_bg ?: 'bg-indigo-600' }}">
                                                            {{ $comp->logo_text ?: mb_substr($comp->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('industry') }}">
                                                            {{ $comp->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $comp->location ?: 'Indonesia' }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">
                                                    {{ $comp->total_employees ? $comp->total_employees . ' Karyawan' : 'Mitra Industri' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('industry') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Direktori
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 4. KARIR & LOWONGAN --}}
                    @if($results['jobs']->isNotEmpty() || $results['internships']->isNotEmpty() || $results['seekers']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600 border border-sky-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Peluang Karir &amp; Magang</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['career'] }}
                                    </span>
                                </div>
                                @if($counts['career'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'career']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['career'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {{-- Jobs --}}
                                @foreach($results['jobs'] as $j)
                                    @php
                                        $tags = array_values(array_filter([$j->type ?? 'Full Time', $j->category ?? null]));
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=1000&q=80" alt="{{ $j->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($j->logo_url))
                                                        <img src="{{ $j->logo_url }}" alt="{{ $j->company }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-blue-600">
                                                            {{ strtoupper(substr($j->company ?: 'IT', 0, 4)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('career.index', ['type' => 'jobs', 'q' => $j->title]) }}">
                                                            {{ $j->title }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/60 shrink-0">
                                                        Lowongan
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $j->company }} &middot; {{ $j->location ?: 'Indonesia' }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $j->type ?? 'Full Time' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('career.index', ['type' => 'jobs', 'q' => $j->title]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Lowongan
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Internships --}}
                                @foreach($results['internships'] as $intern)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80" alt="{{ $intern->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($intern->profile_picture))
                                                        <img src="{{ asset('storage/'.$intern->profile_picture) }}" alt="{{ $intern->company }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-purple-600">
                                                            {{ strtoupper(substr($intern->company ?: 'MG', 0, 4)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('career.index', ['type' => 'internship', 'q' => $intern->title]) }}">
                                                            {{ $intern->title }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200/60 shrink-0">
                                                        Magang
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $intern->company }} &middot; {{ $intern->location ?: 'Indonesia' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                        Magang IT
                                                    </span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                        Kerja Praktik
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                                    Program Magang
                                                </span>
                                            </div>
                                            <a href="{{ route('career.index', ['type' => 'internship', 'q' => $intern->title]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Magang
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Job Seekers --}}
                                @foreach($results['seekers'] as $sk)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1000&q=80" alt="{{ $sk->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($sk->photo_url))
                                                        <img src="{{ $sk->photo_url }}" alt="{{ $sk->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-amber-600">
                                                            {{ strtoupper(substr($sk->name ?: 'SK', 0, 4)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('career.seeker.show', $sk->id) }}">
                                                            {{ $sk->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        Job Seeker
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $sk->job_title }} &middot; {{ $sk->location ?: 'Indonesia' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                        {{ $sk->availability ?: 'Tersedia' }}
                                                    </span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                        {{ $sk->job_title }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $sk->availability ?: 'Job Seeker' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('career.seeker.show', $sk->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 5. EVENT IT --}}
                    @if($results['events']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Agenda &amp; Event IT</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['event'] }}
                                    </span>
                                </div>
                                @if($counts['event'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'event']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['event'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['events'] as $ev)
                                    @php
                                        $banner = $ev->image
                                            ? (str_starts_with($ev->image, 'http') ? $ev->image : asset('storage/' . $ev->image))
                                            : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1000&q=80';
                                        $tags = array_values(array_filter([$ev->category ?: 'Event IT', $ev->mode ?: null]));
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Icon --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $ev->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-rose-600">
                                                        EVT
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('event.show', $ev->id) }}">
                                                            {{ $ev->title }}
                                                        </a>
                                                    </h3>
                                                    @if($ev->is_verified)
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                            <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Terverifikasi
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $ev->organizer }} &middot; {{ $ev->location ?: 'Online' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach($tags as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">
                                                        {{ $ev->short_date ?: $ev->date }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $ev->price === '0' || strtolower($ev->price) === 'gratis' ? 'Gratis' : $ev->price }}
                                                </span>
                                            </div>
                                            <a href="{{ route('event.show', $ev->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Detail Event
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 6. KOMUNITAS --}}
                    @if($results['communities']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Komunitas Teknologi</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['community'] }}
                                    </span>
                                </div>
                                @if($counts['community'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'community']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['community'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['communities'] as $cm)
                                    @php
                                        $banner = $cm->cover_url
                                            ? (str_starts_with($cm->cover_url, 'http') ? $cm->cover_url : asset('storage/' . $cm->cover_url))
                                            : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($cm->tags) && is_array($cm->tags)
                                            ? $cm->tags
                                            : [$cm->category ?: 'Komunitas IT'];
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Logo --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $cm->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($cm->logo_url))
                                                        <img src="{{ str_starts_with($cm->logo_url, 'http') ? $cm->logo_url : asset('storage/' . $cm->logo_url) }}" alt="{{ $cm->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-teal-600">
                                                            {{ mb_substr($cm->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('community.show', $cm->id) }}">
                                                            {{ $cm->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200/60 shrink-0">
                                                        Aktif
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    <span>{{ number_format($cm->members ?? 0) }} Anggota Terdaftar</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach(array_slice($tags, 0, 4) as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-teal-50 text-teal-700 border border-teal-100">
                                                    Komunitas Aktif
                                                </span>
                                            </div>
                                            <a href="{{ route('community.show', $cm->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Buka Komunitas
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 7. PUSAT PENGETAHUAN / ARTIKEL --}}
                    @if($results['articles']->isNotEmpty())
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-50 text-orange-600 border border-orange-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </span>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Pusat Pengetahuan &amp; Artikel</h2>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        {{ $counts['article'] }}
                                    </span>
                                </div>
                                @if($counts['article'] > 3)
                                    <a href="{{ route('search', ['q' => $keyword, 'type' => 'article']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                        Lihat semua ({{ $counts['article'] }}) &rarr;
                                    </a>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['articles'] as $art)
                                    @php
                                        $banner = $art->image
                                            ? (str_starts_with($art->image, 'http') ? $art->image : asset('storage/' . $art->image))
                                            : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($art->tags) && is_array($art->tags)
                                            ? $art->tags
                                            : [$art->category ?: 'Artikel Edukasi'];
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            {{-- Header Banner & Floating Logo --}}
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $art->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($art->author_avatar))
                                                        <img src="{{ $art->author_avatar }}" alt="{{ $art->author_name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-orange-600">
                                                            {{ mb_substr($art->author_name ?: 'ID', 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Card Body --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('knowledge.show', $art->id) }}">
                                                            {{ $art->title }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-200/60 shrink-0">
                                                        {{ $art->category ?: 'Artikel' }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $art->author_name ? 'Penulis: ' . $art->author_name : 'IndoTech Editorial' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach(array_slice($tags, 0, 4) as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-100">
                                                    {{ $art->read_time ?: '5 min baca' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('knowledge.show', $art->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Baca Artikel
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            {{-- ============================================================ --}}
            {{-- VIEW MODE 2: SPECIFIC CATEGORY TAB (PAGINATED)               --}}
            {{-- ============================================================ --}}
            @else
                <div>
                    @if($paginatedResults && $paginatedResults->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                            {{-- CAMPUS --}}
                            @if($activeType === 'campus')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = (!empty($item->gallery) && is_array($item->gallery) && !empty($item->gallery[0]))
                                            ? $item->gallery[0]
                                            : 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($item->tags) && is_array($item->tags)
                                            ? $item->tags
                                            : array_values(array_filter([$item->type ?: 'Perguruan Tinggi', $item->accreditation ? 'Akreditasi ' . $item->accreditation : null]));
                                        $loc = $item->city ? $item->city . ($item->province ? ', ' . $item->province : '') : ($item->location ?: 'Indonesia');
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item->logo_url))
                                                        <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $item->logo_bg ?: 'bg-blue-700' }}">
                                                            {{ $item->logo_text ?: mb_substr($item->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('campus.show', $item->npsn ?: $item->id) }}">
                                                            {{ $item->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $loc }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $item->accreditation ? 'Akreditasi ' . $item->accreditation : 'Terverifikasi' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('campus.show', $item->npsn ?: $item->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- SCHOOL --}}
                            @elseif($activeType === 'school')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = (!empty($item->gallery) && is_array($item->gallery) && !empty($item->gallery[0]))
                                            ? $item->gallery[0]
                                            : 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1000&q=80';
                                        $tags = collect($item->competencies ?? [])->merge($item->tags ?? [])->filter()->unique()->values()->all();
                                        if (empty($tags)) {
                                            $tags = array_values(array_filter([$item->institution_type ?: 'SMK IT', $item->accreditation ? 'Akreditasi ' . $item->accreditation : null]));
                                        }
                                        $loc = $item->city ? $item->city . ($item->province ? ', ' . $item->province : '') : ($item->location ?: 'Indonesia');
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item->logo_url))
                                                        <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $item->logo_bg ?: 'bg-emerald-600' }}">
                                                            {{ $item->logo_text ?: mb_substr($item->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('education.show', $item->npsn ?: $item->id) }}">
                                                            {{ $item->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $loc }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $item->accreditation ? 'Akreditasi ' . $item->accreditation : 'Terverifikasi' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('education.show', $item->npsn ?: $item->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- COMPANY --}}
                            @elseif($activeType === 'company')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = (!empty($item->gallery) && is_array($item->gallery) && !empty($item->gallery[0]))
                                            ? $item->gallery[0]
                                            : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1000&q=80';
                                        $tags = collect($item->specialties ?? [])->merge($item->tags ?? [])->merge([$item->industry])->filter()->unique()->values()->all();
                                        if (empty($tags)) {
                                            $tags = [$item->industry ?: 'Teknologi & Solusi'];
                                        }
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item->logo_url))
                                                        <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $item->logo_bg ?: 'bg-indigo-600' }}">
                                                            {{ $item->logo_text ?: mb_substr($item->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('industry') }}">
                                                            {{ $item->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                        <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $item->location ?: 'Indonesia' }}</span>
                                                </div>

                                                @if(!empty($tags))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach(array_slice($tags, 0, 4) as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($tags) > 4)
                                                            <span class="text-xs font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-500">
                                                                +{{ count($tags) - 4 }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">
                                                    {{ $item->total_employees ? $item->total_employees . ' Karyawan' : 'Mitra Industri' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('industry') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Lihat Direktori
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- CAREER --}}
                            @elseif($activeType === 'career')
                                @foreach($paginatedResults as $item)
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $item['banner'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item['image']))
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['subtitle'] }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $item['logo_bg'] }}">
                                                            {{ $item['initials'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ $item['url'] }}">
                                                            {{ $item['title'] }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $item['type'] === 'job' ? 'bg-blue-50 text-blue-700 border-blue-200/60' : ($item['type'] === 'internship' ? 'bg-purple-50 text-purple-700 border-purple-200/60' : 'bg-amber-50 text-amber-700 border-amber-200/60') }} border shrink-0">
                                                        {{ $item['badge'] }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $item['subtitle'] }} &middot; {{ $item['location'] ?: 'Indonesia' }}</span>
                                                </div>

                                                @if(!empty($item['tags']))
                                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                                        @foreach($item['tags'] as $tag)
                                                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full {{ $item['type'] === 'job' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ($item['type'] === 'internship' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">
                                                    {{ $item['status_badge'] }}
                                                </span>
                                            </div>
                                            <a href="{{ $item['url'] }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                {{ $item['link_text'] }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- EVENT --}}
                            @elseif($activeType === 'event')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = $item->image
                                            ? (str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image))
                                            : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1000&q=80';
                                        $tags = array_values(array_filter([$item->category ?: 'Event IT', $item->mode ?: null]));
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-rose-600">
                                                        EVT
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('event.show', $item->id) }}">
                                                            {{ $item->title }}
                                                        </a>
                                                    </h3>
                                                    @if($item->is_verified)
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                                            <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Terverifikasi
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $item->organizer }} &middot; {{ $item->location ?: 'Online' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach($tags as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">
                                                        {{ $item->short_date ?: $item->date }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $item->price === '0' || strtolower($item->price) === 'gratis' ? 'Gratis' : $item->price }}
                                                </span>
                                            </div>
                                            <a href="{{ route('event.show', $item->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Detail Event
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- COMMUNITY --}}
                            @elseif($activeType === 'community')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = $item->cover_url
                                            ? (str_starts_with($item->cover_url, 'http') ? $item->cover_url : asset('storage/' . $item->cover_url))
                                            : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($item->tags) && is_array($item->tags)
                                            ? $item->tags
                                            : [$item->category ?: 'Komunitas IT'];
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item->logo_url))
                                                        <img src="{{ str_starts_with($item->logo_url, 'http') ? $item->logo_url : asset('storage/' . $item->logo_url) }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-teal-600">
                                                            {{ mb_substr($item->name, 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('community.show', $item->id) }}">
                                                            {{ $item->name }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200/60 shrink-0">
                                                        Aktif
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    <span>{{ number_format($item->members ?? 0) }} Anggota Terdaftar</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach(array_slice($tags, 0, 4) as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-teal-50 text-teal-700 border border-teal-100">
                                                    Komunitas Aktif
                                                </span>
                                            </div>
                                            <a href="{{ route('community.show', $item->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Buka Komunitas
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- ARTICLE --}}
                            @elseif($activeType === 'article')
                                @foreach($paginatedResults as $item)
                                    @php
                                        $banner = $item->image
                                            ? (str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image))
                                            : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1000&q=80';
                                        $tags = !empty($item->tags) && is_array($item->tags)
                                            ? $item->tags
                                            : [$item->category ?: 'Artikel Edukasi'];
                                    @endphp
                                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group">
                                        <div>
                                            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                                                <img src="{{ $banner }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                                                    @if(!empty($item->author_avatar))
                                                        <img src="{{ $item->author_avatar }}" alt="{{ $item->author_name }}" class="w-full h-full object-cover rounded-lg">
                                                    @else
                                                        <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none bg-orange-600">
                                                            {{ mb_substr($item->author_name ?: 'ID', 0, 4) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <h3 class="font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors line-clamp-1">
                                                        <a href="{{ route('knowledge.show', $item->id) }}">
                                                            {{ $item->title }}
                                                        </a>
                                                    </h3>
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-200/60 shrink-0">
                                                        {{ $item->category ?: 'Artikel' }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $item->author_name ? 'Penulis: ' . $item->author_name : 'IndoTech Editorial' }}</span>
                                                </div>

                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                    @foreach(array_slice($tags, 0, 4) as $tag)
                                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100/80">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-100">
                                                    {{ $item->read_time ?: '5 min baca' }}
                                                </span>
                                            </div>
                                            <a href="{{ route('knowledge.show', $item->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                                                Baca Artikel
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            @endif
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8 flex justify-center">
                            {{ $paginatedResults->links() }}
                        </div>
                    @else
                        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                            <p class="text-sm font-semibold text-slate-500">Tidak ada hasil pada kategori ini untuk "{{ $keyword }}".</p>
                            <a href="{{ route('search', ['q' => $keyword, 'type' => 'all']) }}" class="mt-3 inline-block text-xs font-bold text-blue-600 hover:underline">
                                &larr; Lihat hasil di kategori lainnya
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        @endif

    </div>
</div>
@endsection
