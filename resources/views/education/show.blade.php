@extends('layouts.app')

@section('title', $school['name'] . ' - IndoTech')

@section('content')

{{-- Top Campus Aerial Banner --}}
<div class="relative h-64 sm:h-80 w-full bg-slate-900 overflow-hidden">
    <img src="{{ $school['image'] }}" alt="{{ $school['name'] }}" class="w-full h-full object-cover opacity-90">
    
    {{-- Back Button --}}
    <div class="absolute top-6 left-6 z-20">
        <a href="{{ route('education.index') }}" class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center shadow-md transition cursor-pointer" aria-label="Back to Directory">
            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>
</div>

{{-- Main Container --}}
<div class="max-w-6xl mx-auto px-4">

    {{-- Floating Profile Header Card --}}
    <div class="-mt-20 sm:-mt-24 relative z-10 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row items-center md:items-start gap-6 mb-12">
        
        {{-- Circular Avatar Logo --}}
        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-white shadow-lg bg-white shrink-0 overflow-hidden ring-2 ring-slate-200/80 flex items-center justify-center">
            @if(!empty($school['logo_url']))
                <img src="{{ $school['logo_url'] }}" alt="{{ $school['name'] }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-white text-base sm:text-lg font-black text-center {{ $school['logo_bg_class'] ?? 'bg-blue-600' }}">
                    {{ $school['logo_text'] }}
                </div>
            @endif
        </div>

        {{-- School Main Info --}}
        <div class="flex-1 text-center md:text-left">
            {{-- Badges Row --}}
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                @if($school['verified'])
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                        <svg class="w-3.5 h-3.5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Terverifikasi
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-200/60">
                    🎓 {{ $school['category_badge'] }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">
                {{ $school['name'] }}
            </h1>

            {{-- Location --}}
            <p class="text-sm font-medium text-blue-600 flex items-center justify-center md:justify-start gap-1.5 mb-5">
                <svg class="w-4 h-4 stroke-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $school['location_full'] }}</span>
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                <a href="{{ $school['website'] }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-xs">
                    <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    Website
                </a>

                <a href="mailto:{{ $school['email'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 stroke-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email
                </a>

                <a href="{{ $school['map_link'] }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 stroke-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <polygon points="1 6 1 22 8 18 15 22 22 18 22 2 15 6 8 2 1 6"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="15" y1="6" x2="15" y2="22"/>
                    </svg>
                    Peta
                </a>
            </div>
        </div>
    </div>

    {{-- About & Key Statistics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        
        {{-- Left Column: About --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-4">Tentang Sekolah</h2>
            <div class="prose max-w-none text-slate-700 text-sm sm:text-base leading-relaxed mb-4 [&_a]:text-blue-600 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-blue-800 hover:[&_a]:underline [&_a]:cursor-pointer [&_a]:transition-colors [&_a]:break-all">
                {!! $school['about'] !!}
            </div>
        </div>

        {{-- Right Column: Key Statistics Box --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 sm:p-7 shadow-xs self-start">
            <h3 class="text-base font-bold text-slate-900 mb-6">Statistik Utama</h3>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <span class="text-3xl font-extrabold text-blue-600 block mb-1">
                        {{ $school['stats']['placement_rate'] }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Tingkat Penyerapan Kerja</span>
                </div>

                <div>
                    <span class="text-3xl font-extrabold text-blue-600 block mb-1">
                        {{ $school['stats']['industry_partners'] }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Mitra Industri</span>
                </div>

                <div>
                    <span class="text-3xl font-extrabold text-blue-600 block mb-1">
                        {{ $school['stats']['active_students'] }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Siswa Aktif</span>
                </div>

                <div>
                    <span class="text-3xl font-extrabold text-blue-600 block mb-1">
                        {{ $school['stats']['modern_labs'] }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Lab IT Modern</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Skill Competencies Section --}}
    <div class="pb-16">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Kompetensi Keahlian</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($school['competencies'] as $comp)
                <div class="bg-slate-50/60 border border-slate-200/70 rounded-2xl p-6 hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-4 shadow-xs">
                        @if($comp['icon'] == 'code')
                            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <polyline points="16 18 22 12 16 6"/>
                                <polyline points="8 6 2 12 8 18"/>
                            </svg>
                        @elseif($comp['icon'] == 'server')
                            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                                <line x1="6" y1="6" x2="6.01" y2="6"/>
                                <line x1="6" y1="18" x2="6.01" y2="18"/>
                            </svg>
                        @elseif($comp['icon'] == 'cube')
                            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-2">
                        {{ $comp['name'] }}
                    </h3>

                    <p class="text-sm text-blue-500 leading-relaxed">
                        {{ $comp['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Galeri Sekolah Section (jika ada foto galeri) --}}
    @if(!empty($school['gallery']) && count($school['gallery']) > 0)
        <div class="pb-16 border-t border-slate-100 pt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Galeri Sekolah</h2>
                    <p class="text-sm text-slate-500 mt-1">Dokumentasi suasana pembelajaran, fasilitas IT, dan aktivitas di {{ $school['name'] }}</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-600 rounded-full border border-blue-200/60">
                    {{ count($school['gallery']) }} Foto
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($school['gallery'] as $photo)
                    <div class="aspect-4/3 rounded-2xl overflow-hidden border border-slate-200/80 bg-slate-100 shadow-xs group">
                        <img src="{{ $photo }}" alt="Foto Galeri {{ $school['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection
