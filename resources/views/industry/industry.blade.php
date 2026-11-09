@extends('layouts.app')

@section('title', 'Ekosistem IT di Bali - IndoTech')

@section('content')

{{-- Hero Header Section --}}
<section class="py-12 sm:py-16 px-4 text-center max-w-4xl mx-auto">
    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
        Ekosistem IT di Bali
    </h1>
    <p class="text-slate-500 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
        Jelajahi lanskap teknologi yang sedang berkembang di Bali. Temukan perusahaan inovatif, agensi kreatif, dan startup yang membangun masa depan digital dari Pulau Dewata.
    </p>
</section>

{{-- Main Content Container --}}
<div class="max-w-6xl mx-auto px-4 pb-28 sm:pb-36">

    {{-- Filter Bar Toolbar --}}
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 mb-8 shadow-xs">
        <form id="filterForm" onsubmit="event.preventDefault(); applyFilter();" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-center">
            
            {{-- Search Bar --}}
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="searchInput" class="w-full h-11 pl-10 pr-3 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition" placeholder="Cari perusahaan..." onkeyup="applyFilter()">
            </div>

            {{-- Location Filter --}}
            <div>
                <select id="locationSelect" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-700 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer appearance-none bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_12px_center]" onchange="applyFilter()">
                    <option value="">Semua Lokasi</option>
                    <option value="Denpasar">Denpasar</option>
                    <option value="Badung">Badung</option>
                    <option value="Ubud">Ubud</option>
                    <option value="Canggu">Canggu</option>
                    <option value="Sanur">Sanur</option>
                    <option value="Kuta">Kuta</option>
                    <option value="Seminyak">Seminyak</option>
                    <option value="Jimbaran">Jimbaran</option>
                    <option value="Benoa">Benoa</option>
                    <option value="Tabanan">Tabanan</option>
                    <option value="Singaraja">Singaraja</option>
                    <option value="Uluwatu">Uluwatu</option>
                </select>
            </div>

            {{-- Category Filter --}}
            <div>
                <select id="categorySelect" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-700 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer appearance-none bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_12px_center]" onchange="applyFilter()">
                    <option value="">Kategori</option>
                    <option value="Software Development">Software Development</option>
                    <option value="Creative Agency">Creative Agency</option>
                    <option value="Startup">Startup</option>
                </select>
            </div>

            {{-- Filter Submit Button --}}
            <div>
                <button type="button" onclick="applyFilter()" class="w-full h-11 px-5 bg-blue-700 hover:bg-blue-800 active:scale-[0.99] text-white text-sm font-semibold rounded-lg flex items-center justify-center gap-2 transition cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                        <line x1="10" y1="18" x2="14" y2="18"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Company Cards Grid --}}
    <div id="companyGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($companies as $company)
            <div class="company-item bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-between hover:shadow-lg hover:border-slate-300 hover:-translate-y-0.5 transition-all duration-200"
                 data-name="{{ strtolower($company['name']) }}"
                 data-category="{{ $company['category'] }}"
                 data-location="{{ $company['location'] }}"
                 data-id="{{ $company['id'] }}">

                <div>
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                            {{-- Logo Box --}}
                            @if(!empty($company['logo_image']))
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-slate-200 shadow-xs p-0.5 bg-white">
                                    <img src="{{ $company['logo_image'] }}" alt="{{ $company['name'] }}" class="w-full h-full object-cover rounded-lg">
                                </div>
                            @else
                                @if($company['badge_class'] == 'software')
                                    <div class="w-12 h-12 rounded-lg border border-blue-100 bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                @elseif($company['badge_class'] == 'creative')
                                    <div class="w-12 h-12 rounded-lg border border-emerald-100 bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-lg border border-rose-100 bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                @endif
                                    @if($company['icon_type'] == 'code' || $company['icon_type'] == 'cpu' || $company['icon_type'] == 'cloud' || $company['icon_type'] == 'terminal')
                                        {{-- Code / Software Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="16 18 22 12 16 6"></polyline>
                                            <polyline points="8 6 2 12 8 18"></polyline>
                                        </svg>
                                    @elseif($company['icon_type'] == 'palette' || $company['icon_type'] == 'magic' || $company['icon_type'] == 'layers' || $company['icon_type'] == 'camera')
                                        {{-- Creative / Palette Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    @elseif($company['icon_type'] == 'building')
                                        {{-- Building / Office Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                            <line x1="9" y1="6" x2="9" y2="6.01"></line>
                                            <line x1="15" y1="6" x2="15" y2="6.01"></line>
                                        </svg>
                                    @elseif($company['icon_type'] == 'credit-card')
                                        {{-- Credit Card / Fintech Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                            <line x1="1" y1="10" x2="23" y2="10"></line>
                                        </svg>
                                    @elseif($company['icon_type'] == 'sparkles')
                                        {{-- Sparkles AI Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"></path>
                                        </svg>
                                    @else
                                        {{-- Rocket / Startup Icon --}}
                                        <svg class="w-6 h-6 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.71.79-1.81.79-1.81l-1.98-1.98s-1.1.08-1.81.79z"></path>
                                            <path d="M15 9l-6 6"></path>
                                            <path d="M16.5 4.5c1.5-1.26 5-2 5-2s-.5 3.74-2 5c-.71.71-1.81.79-1.81.79l-1.98-1.98s.08-1.1.79-1.81z"></path>
                                        </svg>
                                    @endif
                                </div>
                            @endif

                            {{-- Company Title & Badge --}}
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-base text-slate-900 leading-snug truncate mb-1">
                                    {{ $company['name'] }}
                                </h3>
                                @if($company['badge_class'] == 'software')
                                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                        {{ $company['category'] }}
                                    </span>
                                @elseif($company['badge_class'] == 'creative')
                                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                        {{ $company['category'] }}
                                    </span>
                                @else
                                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-600">
                                        {{ $company['category'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Views Count Indicator with Eye Icon --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold shrink-0 border border-slate-200/80 shadow-2xs" title="{{ number_format($company['views']) }} orang melihat">
                            <svg class="w-3.5 h-3.5 text-slate-400 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>{{ number_format($company['views']) }}</span>
                        </span>
                    </div>

                    {{-- Description Excerpt --}}
                    <p class="text-slate-600 text-sm leading-relaxed mb-6 grow line-clamp-3">
                        {{ strip_tags($company['description'] ?? '') }}
                    </p>
                </div>

                {{-- Action Button ("Lihat Profil") --}}
                <button type="button" data-company='@json($company)' onclick="showCompanyModal(this)" class="w-full py-2.5 text-center text-sm font-semibold text-blue-600 hover:text-white bg-white hover:bg-blue-600 border border-blue-600 rounded-lg transition-colors duration-150 cursor-pointer">
                    Lihat Profil
                </button>
            </div>
        @endforeach

        {{-- Empty state --}}
        <div id="emptyState" class="hidden text-center py-12 px-6 bg-white border border-dashed border-slate-300 rounded-xl col-span-full">
            <svg class="w-12 h-12 text-slate-400 mx-auto mb-3 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <h4 class="text-base font-bold text-slate-800 mb-1">Tidak Ada Perusahaan Ditemukan</h4>
            <p class="text-sm text-slate-500">Coba sesuaikan kata kunci pencarian atau filter lokasi dan kategori Anda.</p>
        </div>
    </div>

    {{-- Pagination Navigation --}}
    <div id="paginationBar" class="flex items-center justify-center gap-2 mt-14 mb-6">
        <button type="button" id="prevPageBtn" onclick="changePage(currentPage - 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" aria-label="Previous">
            <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <button type="button" class="page-num w-9 h-9 rounded-lg border border-blue-700 bg-blue-700 text-white text-sm font-semibold flex items-center justify-center transition cursor-pointer" onclick="changePage(1)">1</button>
        <button type="button" class="page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" onclick="changePage(2)">2</button>
        <button type="button" class="page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" onclick="changePage(3)">3</button>

        <button type="button" id="nextPageBtn" onclick="changePage(currentPage + 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" aria-label="Next">
            <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>

{{-- Ultra Premium Detail Modal --}}
<div id="companyModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-50 flex items-center justify-center p-3 sm:p-4 opacity-0 pointer-events-none transition-all duration-300 ease-out" onclick="if(event.target === this) closeCompanyModal()">
    <div id="modalDialog" class="bg-white rounded-3xl w-full max-w-xl max-h-[92vh] overflow-hidden shadow-2xl relative scale-95 translate-y-4 opacity-0 transition-all duration-300 ease-out flex flex-col">
        
        {{-- Scrollable Container for Entire Modal Content (Hidden scrollbar) --}}
        <div id="modalScrollableContent" class="overflow-y-auto flex-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
            
            {{-- Cover Banner Header with Image & Close Button --}}
            <div id="modalCoverBanner" class="relative h-36 sm:h-44 w-full bg-slate-900 overflow-hidden shrink-0">
                {{-- Cover Background Image --}}
                <img id="modalCoverImage" src="" alt="Cover" class="w-full h-full object-cover transition-opacity duration-300">
                <div id="modalCoverOverlay" class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-slate-900/30"></div>
                
                {{-- Rating & Verified Badge Top Left --}}
                <div class="absolute top-4 left-4 z-10 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-slate-950/60 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full border border-white/10 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span id="modalRatingText">4.9</span>
                    </span>
                    <span class="inline-flex items-center gap-1 bg-emerald-600/80 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-400/30 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Terverifikasi</span>
                    </span>
                </div>

                {{-- Close Button Top Right --}}
                <button onclick="closeCompanyModal()" aria-label="Tutup" class="absolute top-4 right-4 z-20 w-9 h-9 rounded-full bg-slate-950/60 hover:bg-slate-950/90 text-white backdrop-blur-md border border-white/10 flex items-center justify-center transition-transform hover:scale-110 active:scale-95 cursor-pointer">
                    <svg class="w-5 h-5 stroke-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Content Body --}}
            <div class="px-6 sm:px-8 pb-6">
                
                {{-- Floating Company Profile Logo Image --}}
                <div class="relative z-20 -mt-12 sm:-mt-14 mb-4 flex items-end justify-between">
                    <div id="modalLogoBox" class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-white p-2 shadow-xl ring-4 ring-white border border-slate-200 shrink-0 flex items-center justify-center">
                        {{-- Logo image or SVG --}}
                    </div>
                </div>

                {{-- Company Title & Category Badges --}}
                <div class="mb-5">
                    <h2 id="modalCompanyName" class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight mb-2"></h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="modalBadge" class="inline-block text-xs font-bold px-3 py-1 rounded-full"></span>
                        <span class="text-xs text-slate-600 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span id="modalLocText"></span>
                        </span>
                    </div>
                </div>

                {{-- About Section --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span>Tentang Perusahaan</span>
                    </div>
                    <div id="modalDescription" class="prose prose-sm max-w-none text-sm leading-relaxed text-slate-600 bg-slate-50/80 border border-slate-200/60 p-4 rounded-2xl overflow-hidden [&_img]:rounded-xl [&_img]:max-w-full [&_img]:h-auto [&_p]:mb-2 [&_p:last-child]:mb-0"></div>
                </div>

                {{-- Key Metrics Cards (Grid of 3) --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-blue-50/60 border border-blue-100 p-3.5 rounded-2xl text-center">
                        <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block mb-0.5">Karyawan</span>
                        <strong id="modalEmployees" class="text-xs sm:text-sm font-extrabold text-slate-900"></strong>
                    </div>
                    <div class="bg-indigo-50/60 border border-indigo-100 p-3.5 rounded-2xl text-center">
                        <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block mb-0.5">Berdiri</span>
                        <strong id="modalFounded" class="text-xs sm:text-sm font-extrabold text-slate-900"></strong>
                    </div>
                    <div class="bg-emerald-50/60 border border-emerald-100 p-3.5 rounded-2xl text-center">
                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block mb-0.5">Portofolio</span>
                        <strong id="modalProjects" class="text-xs sm:text-sm font-extrabold text-slate-900"></strong>
                    </div>
                </div>

                {{-- Keunggulan & Spesialisasi (Specialties) --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Layanan Utama & Keunggulan</span>
                    </div>
                    <ul id="modalSpecialties" class="space-y-2">
                        {{-- Dynamic list --}}
                    </ul>
                </div>

                {{-- Tech Stack --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                        </svg>
                        <span>Teknologi & Tech Stack</span>
                    </div>
                    <div id="modalTechStack" class="flex flex-wrap gap-2">
                        {{-- Dynamic pills --}}
                    </div>
                </div>

                {{-- Company Gallery --}}
                <div id="modalGallerySection" class="mb-2 hidden">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                        </svg>
                        <span>Foto Galeri Perusahaan</span>
                    </div>
                    <div id="modalGalleryGrid" class="grid grid-cols-3 gap-2">
                        {{-- Dynamic gallery images --}}
                    </div>
                </div>

            </div>

        </div>

        {{-- Sticky Action Toolbar Footer --}}
        <div class="p-4 sm:px-8 sm:py-5 bg-slate-50 border-t border-slate-100 flex gap-3 shrink-0">
            <a id="modalWebsiteLink" href="#" target="_blank" class="flex-1 py-3 px-4 text-center text-xs sm:text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] rounded-xl transition shadow-md shadow-blue-500/20 flex items-center justify-center gap-2">
                <span>Kunjungi Website</span>
                <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H18m0 0v4.5m0-4.5L8.25 15.75" />
                </svg>
            </a>
            <a id="modalEmailLink" href="#" class="flex-1 py-3 px-4 text-center text-xs sm:text-sm font-bold text-slate-700 bg-white hover:bg-slate-100 active:scale-[0.98] border border-slate-200 rounded-xl transition flex items-center justify-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-slate-500 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <span>Hubungi Email</span>
            </a>
        </div>
    </div>
</div>

<style>
    #modalDialog .overflow-y-auto::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
</style>

<script>
    let currentPage = 1;
    const itemsPerPage = 6;
    let filteredItems = [];

    document.addEventListener("DOMContentLoaded", function() {
        applyFilter();
    });

    function applyFilter() {
        const query = document.getElementById("searchInput").value.toLowerCase().trim();
        const selectedLoc = document.getElementById("locationSelect").value;
        const selectedCat = document.getElementById("categorySelect").value;

        const allCards = Array.from(document.querySelectorAll(".company-item"));

        filteredItems = allCards.filter(card => {
            const name = card.getAttribute("data-name") || "";
            const cat = card.getAttribute("data-category") || "";
            const loc = card.getAttribute("data-location") || "";

            const matchQuery = !query || name.includes(query);
            const matchLoc = !selectedLoc || loc === selectedLoc;
            const matchCat = !selectedCat || cat === selectedCat;

            return matchQuery && matchLoc && matchCat;
        });

        currentPage = 1;
        renderPage();
    }

    function renderPage() {
        const allCards = document.querySelectorAll(".company-item");
        const emptyState = document.getElementById("emptyState");
        const paginationBar = document.getElementById("paginationBar");

        allCards.forEach(card => card.classList.add("hidden"));

        if (filteredItems.length === 0) {
            emptyState.classList.remove("hidden");
            paginationBar.classList.add("hidden");
            return;
        } else {
            emptyState.classList.add("hidden");
            paginationBar.classList.remove("hidden");
            paginationBar.classList.add("flex");
        }

        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        const pageItems = filteredItems.slice(startIndex, endIndex);
        pageItems.forEach(card => card.classList.remove("hidden"));

        // Update pagination buttons
        const pageBtns = document.querySelectorAll(".page-num");
        pageBtns.forEach((btn, index) => {
            const pageNum = index + 1;
            if (pageNum <= totalPages) {
                btn.classList.remove("hidden");
                if (pageNum === currentPage) {
                    btn.className = "page-num w-9 h-9 rounded-lg border border-blue-700 bg-blue-700 text-white text-sm font-semibold flex items-center justify-center transition cursor-pointer";
                } else {
                    btn.className = "page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer";
                }
            } else {
                btn.classList.add("hidden");
            }
        });

        // Prev & Next Buttons state
        const prevBtn = document.getElementById("prevPageBtn");
        const nextBtn = document.getElementById("nextPageBtn");

        if (currentPage === 1) {
            prevBtn.classList.add("opacity-40", "cursor-not-allowed");
        } else {
            prevBtn.classList.remove("opacity-40", "cursor-not-allowed");
        }

        if (currentPage === totalPages || totalPages === 0) {
            nextBtn.classList.add("opacity-40", "cursor-not-allowed");
        } else {
            nextBtn.classList.remove("opacity-40", "cursor-not-allowed");
        }
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderPage();

        window.scrollTo({
            top: document.getElementById('filterForm').offsetTop - 80,
            behavior: 'smooth'
        });
    }

    function showCompanyModal(target) {
        let company = target;
        if (target instanceof HTMLElement) {
            company = JSON.parse(target.getAttribute('data-company'));
        }

        // Lock background body scrolling so mouse scroll strictly focuses on modal popup
        document.body.style.overflow = 'hidden';

        // Reset modal scroll container to top
        const scrollableContent = document.getElementById("modalScrollableContent");
        if (scrollableContent) {
            scrollableContent.scrollTop = 0;
        }

        document.getElementById("modalCompanyName").innerText = company.name;
        document.getElementById("modalLocText").innerText = company.location + ", Bali";

        const modalBadge = document.getElementById("modalBadge");
        modalBadge.innerText = company.category;
        
        if (company.badge_class === 'software') {
            modalBadge.className = "inline-block text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200/60";
        } else if (company.badge_class === 'creative') {
            modalBadge.className = "inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/60";
        } else {
            modalBadge.className = "inline-block text-xs font-bold px-3 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200/60";
        }

        // Cover banner: use first gallery image if available, else fallback
        const coverImg = document.getElementById("modalCoverImage");
        const gallery = company.gallery || [];
        const bannerSrc = (gallery.length > 0) ? gallery[0] : (company.cover_image || "https://images.unsplash.com/photo-1497366216548-37526070297c?w=1000&h=400&fit=crop");
        coverImg.src = bannerSrc;
        coverImg.style.opacity = '0';
        coverImg.onload = () => { coverImg.style.opacity = '1'; };
        if (coverImg.complete) coverImg.style.opacity = '1';

        document.getElementById("modalDescription").innerHTML = company.full_description || company.description || '<p class="text-slate-400">Belum ada deskripsi profil.</p>';
        document.getElementById("modalEmployees").innerText = company.employees || "20-50 Karyawan";
        document.getElementById("modalFounded").innerText = company.founded || "2020";
        document.getElementById("modalRatingText").innerText = company.rating || "4.9";
        document.getElementById("modalProjects").innerText = company.projects_completed || "100+ Proyek";

        // Logo Box (Photo Image or SVG Fallback)
        const logoBox = document.getElementById("modalLogoBox");
        if (company.logo_image) {
            logoBox.className = "w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-white p-1 shadow-xl ring-4 ring-white border border-slate-200 shrink-0 overflow-hidden";
            logoBox.innerHTML = `<img src="${company.logo_image}" alt="${company.name}" class="w-full h-full object-cover rounded-xl">`;
        } else {
            if (company.badge_class === 'software') {
                logoBox.className = "w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 p-2 shadow-xl ring-4 ring-white";
            } else if (company.badge_class === 'creative') {
                logoBox.className = "w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 p-2 shadow-xl ring-4 ring-white";
            } else {
                logoBox.className = "w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0 p-2 shadow-xl ring-4 ring-white";
            }
            logoBox.innerHTML = getIconSvg(company.icon_type);
        }

        // Specialties list
        const specialtiesContainer = document.getElementById("modalSpecialties");
        specialtiesContainer.innerHTML = "";
        const specs = (company.specialties && company.specialties.length > 0) ? company.specialties : [
            "Pengembangan Sistem Terdistribusi & Microservices",
            "Desain UI/UX & Pengalaman Pengguna Berstandar Global",
            "Integrasi Keamanan Informasi & Cloud Solutions"
        ];
        specs.forEach(spec => {
            const li = document.createElement("li");
            li.className = "flex items-start gap-2.5 text-xs text-slate-700 font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-200/50";
            li.innerHTML = `
                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>${spec}</span>
            `;
            specialtiesContainer.appendChild(li);
        });

        // Tech Stack
        const stackContainer = document.getElementById("modalTechStack");
        stackContainer.innerHTML = "";
        const stacks = (company.tech_stack && company.tech_stack.length > 0) ? company.tech_stack : [];
        if (stacks.length > 0) {
            stacks.forEach(tech => {
                const pill = document.createElement("span");
                pill.className = "text-xs font-bold bg-white text-slate-700 px-3 py-1 rounded-xl border border-slate-200 shadow-xs flex items-center gap-1.5";
                pill.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>${tech}`;
                stackContainer.appendChild(pill);
            });
        } else {
            stackContainer.innerHTML = '<span class="text-xs text-slate-400 italic">Belum ada data teknologi atau tech stack.</span>';
        }

        // Gallery section
        const gallerySection = document.getElementById("modalGallerySection");
        const galleryGrid = document.getElementById("modalGalleryGrid");
        galleryGrid.innerHTML = '';
        if (gallery.length > 0) {
            gallerySection.classList.remove('hidden');
            gallery.forEach((imgUrl, idx) => {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded-xl overflow-hidden border border-slate-200 cursor-pointer hover:opacity-90 transition';
                div.innerHTML = `<img src="${imgUrl}" alt="Galeri ${idx+1}" class="w-full h-full object-cover" onclick="setBannerFromGallery('${imgUrl}')">` ;
                galleryGrid.appendChild(div);
            });
        } else {
            gallerySection.classList.add('hidden');
        }

        // Links
        document.getElementById("modalWebsiteLink").href = company.website || "#";
        document.getElementById("modalEmailLink").href = "mailto:" + (company.email || "info@example.com");

        const modal = document.getElementById("companyModal");
        const modalDialog = document.getElementById("modalDialog");
        
        modal.classList.remove("opacity-0", "pointer-events-none");
        setTimeout(() => {
            modalDialog.classList.remove("scale-95", "translate-y-4", "opacity-0");
            modalDialog.classList.add("scale-100", "translate-y-0", "opacity-100");
        }, 10);
    }

    function closeCompanyModal() {
        const modal = document.getElementById("companyModal");
        const modalDialog = document.getElementById("modalDialog");
        
        // Restore background body scrolling
        document.body.style.overflow = '';

        modalDialog.classList.remove("scale-100", "translate-y-0", "opacity-100");
        modalDialog.classList.add("scale-95", "translate-y-4", "opacity-0");
        
        setTimeout(() => {
            modal.classList.add("opacity-0", "pointer-events-none");
        }, 200);
    }

    // Close modal on Escape key press
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeCompanyModal();
        }
    });

    function setBannerFromGallery(src) {
        const coverImg = document.getElementById('modalCoverImage');
        coverImg.style.opacity = '0';
        setTimeout(() => {
            coverImg.src = src;
            coverImg.onload = () => { coverImg.style.opacity = '1'; };
            if (coverImg.complete) coverImg.style.opacity = '1';
        }, 150);
    }

    function getIconSvg(type) {
        if (type === 'code' || type === 'cpu' || type === 'cloud' || type === 'terminal') {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>`;
        } else if (type === 'palette' || type === 'magic' || type === 'layers' || type === 'camera') {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>`;
        } else if (type === 'building') {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line></svg>`;
        } else if (type === 'credit-card') {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>`;
        } else if (type === 'sparkles') {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"></path></svg>`;
        } else {
            return `<svg class="w-8 h-8 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.71.79-1.81.79-1.81l-1.98-1.98s-1.1.08-1.81.79z"></path><path d="M15 9l-6 6"></path><path d="M16.5 4.5c1.5-1.26 5-2 5-2s-.5 3.74-2 5c-.71.71-1.81.79-1.81.79l-1.98-1.98s.08-1.1.79-1.81z"></path></svg>`;
        }
    }
</script>
@endsection
