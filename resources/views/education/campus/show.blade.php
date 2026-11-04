@extends('layouts.app')

@section('title', $campus['name'] . ' - Kampus IT di IndoTech')

@section('content')

{{-- Top Campus Aerial Banner --}}
<div class="relative h-64 sm:h-80 w-full bg-slate-900 overflow-hidden">
    <img src="{{ $campus['image'] }}" alt="{{ $campus['name'] }}" class="w-full h-full object-cover opacity-90">

    {{-- Back Button --}}
    <div class="absolute top-6 left-6 z-20">
        <a href="{{ route('campus.index') }}" class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center shadow-md transition cursor-pointer" aria-label="Kembali ke Direktori Kampus">
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
            @if(!empty($campus['logo_url']))
                <img src="{{ $campus['logo_url'] }}" alt="{{ $campus['name'] }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-white text-base sm:text-lg font-black text-center {{ $campus['logo_bg_class'] ?? 'bg-blue-600' }}">
                    {{ $campus['logo_text'] }}
                </div>
            @endif
        </div>

        {{-- Campus Main Info --}}
        <div class="flex-1 text-center md:text-left">
            {{-- Badges Row --}}
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                @if($campus['verified'])
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                        <svg class="w-3.5 h-3.5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Terverifikasi
                    </span>
                @endif
                @php
                    $badgeColors = [
                        'green' => 'bg-emerald-50 text-emerald-600 border-emerald-200/60',
                        'purple' => 'bg-purple-50 text-purple-600 border-purple-200/60',
                        'gray' => 'bg-slate-100 text-slate-600 border-slate-200/60',
                    ];
                    $badgeClass = $badgeColors[$campus['status_badge_type']] ?? $badgeColors['gray'];
                @endphp
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full {{ $badgeClass }}">
                    🎓 {{ $campus['status_badge'] }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">
                {{ $campus['name'] }}
            </h1>

            {{-- Location --}}
            <p class="text-sm font-medium text-blue-600 flex items-center justify-center md:justify-start gap-1.5 mb-5">
                <svg class="w-4 h-4 stroke-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $campus['location_full'] }}</span>
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                @if(!empty($campus['website']))
                <a href="{{ $campus['website'] }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-xs">
                    <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    Website
                </a>
                @endif

                @if(!empty($campus['email']))
                <a href="mailto:{{ $campus['email'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 stroke-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email
                </a>
                @endif

                @if(!empty($campus['map_link']))
                <a href="{{ $campus['map_link'] }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 stroke-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <polygon points="1 6 1 22 8 18 15 22 22 18 22 2 15 6 8 2 1 6"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="15" y1="6" x2="15" y2="22"/>
                    </svg>
                    Peta Lokasi
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- About & Key Statistics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">

        {{-- Left Column: About --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-4">Tentang Kampus</h2>
            <div class="text-slate-700 text-sm sm:text-base leading-relaxed prose max-w-none [&_a]:text-blue-600 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-blue-800 hover:[&_a]:underline [&_a]:cursor-pointer [&_a]:transition-colors [&_a]:break-all">
                {!! $campus['about'] !!}
            </div>
        </div>

        {{-- Right Column: Key Statistics Box --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 sm:p-7 shadow-xs self-start">
            <h3 class="text-base font-bold text-slate-900 mb-6">Statistik Utama</h3>

            <div class="grid grid-cols-2 gap-6">
                @foreach($campus['stats'] as $label => $value)
                    <div>
                        <span class="text-3xl font-extrabold text-blue-600 block mb-1">
                            {{ $value }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Campus Gallery Section --}}
    @if(!empty($campus['gallery']) && count($campus['gallery']) > 0)
    <div class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-extrabold text-slate-900">Galeri Kampus</h2>
            <span class="text-[13px] font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ count($campus['gallery']) }} Foto</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($campus['gallery'] as $photo)
                <div class="aspect-square rounded-2xl overflow-hidden border border-slate-200 shadow-xs group cursor-pointer"
                     onclick="openLightbox('{{ $photo }}')">
                    <img src="{{ $photo }}" alt="Galeri {{ $campus['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4"
         onclick="closeLightbox()">
        <div class="relative max-w-4xl max-h-[90vh]">
            <img id="lightboxImg" src="" alt="Preview" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
            <button onclick="closeLightbox()" class="absolute -top-4 -right-4 w-9 h-9 rounded-full bg-white text-slate-800 flex items-center justify-center shadow-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Program Studi IT Section --}}
    <div class="pb-16">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Program Studi IT</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($campus['competencies'] as $comp)
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
            @empty
                <div class="col-span-full bg-slate-50 border border-slate-200/80 rounded-2xl p-6 text-center text-slate-400 text-sm">
                    Belum ada program studi IT yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }
    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endpush
