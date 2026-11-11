@extends('layouts.app')

@section('title', 'University (Kampus) Directory - IndoTech')

@section('content')

{{-- Hero Section --}}
<section class="py-12 sm:py-16 px-4 text-center max-w-4xl mx-auto">
    <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
        University (Kampus) Directory
    </h1>
    <p class="text-slate-500 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
        Discover and connect with top universities across Indonesia that offer Information Technology (IT) study programs — Computer Science, Information Systems, Informatics Engineering, and more.
    </p>
</section>

{{-- Main Container --}}
<div class="max-w-7xl mx-auto px-4 pb-28 sm:pb-36">

    {{-- Filter Bar Toolbar --}}
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 mb-10 shadow-xs space-y-3">

        {{-- Row 1: Search Input --}}
        <div class="relative flex items-center">
            <svg class="absolute left-3.5 w-4 h-4 text-slate-400 pointer-events-none stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="11" cy="11" r="7"/>
                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text"
                   id="campusSearchInput"
                   placeholder="Cari nama perguruan tinggi / universitas..."
                   class="w-full h-11 pl-10 pr-10 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:bg-white transition"
                   oninput="applyCampusFilter()">
            <button type="button"
                    id="campusSearchClear"
                    onclick="document.getElementById('campusSearchInput').value=''; applyCampusFilter();"
                    class="absolute right-3.5 text-slate-400 hover:text-slate-600 hidden"
                    aria-label="Hapus pencarian">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Row 2: Filter Dropdowns + Category Switcher --}}
        <form id="campusFilterForm" onsubmit="event.preventDefault(); applyCampusFilter();" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-center">
            
            {{-- Location / Region Filter --}}
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 w-4 h-4 text-slate-400 pointer-events-none stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <select id="campusLocationSelect" class="w-full h-11 pl-10 pr-8 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer appearance-none bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_12px_center]" onchange="applyCampusFilter()">
                    <option value="">All Locations</option>
                    <option value="Jakarta">DKI Jakarta</option>
                    <option value="West Java">Jawa Barat (Bandung/Depok/Bogor)</option>
                    <option value="Central Java">Jawa Tengah (Semarang/Surakarta)</option>
                    <option value="Yogyakarta">DI Yogyakarta</option>
                    <option value="East Java">Jawa Timur (Surabaya/Malang)</option>
                    <option value="North Sumatra">Sumatera Utara (Medan)</option>
                    <option value="South Sulawesi">Sulawesi Selatan (Makassar)</option>
                    <option value="South Kalimantan">Kalimantan Selatan (Banjarmasin)</option>
                    <option value="Papua">Papua (Jayapura/Abepkayat)</option>
                </select>
            </div>

            {{-- IT Program Filter --}}
            <div>
                <select id="campusSkillSelect" class="w-full h-11 px-3.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer appearance-none bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_12px_center]" onchange="applyCampusFilter()">
                    <option value="">IT Study Programs</option>
                    <option value="Computer Science">Computer Science / Ilmu Komputer</option>
                    <option value="Information Systems">Information Systems / Sistem Informasi</option>
                    <option value="Informatics">Informatics Engineering / Teknik Informatika</option>
                    <option value="Information Engineering">Information Engineering / Teknik Informasi</option>
                </select>
            </div>

            {{-- Featured Programs Filter --}}
            <div>
                <select id="campusProgramSelect" class="w-full h-11 px-3.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-hidden focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition cursor-pointer appearance-none bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_12px_center]" onchange="applyCampusFilter()">
                    <option value="">Featured Programs</option>
                    <option value="Center of Excellence">Center of Excellence</option>
                    <option value="Verified">Verified</option>
                    <option value="Enrollment Open">Enrollment Open</option>
                </select>
            </div>

            {{-- Category Switcher: SMK & Perguruan Tinggi (Direct, no 'More') --}}
            <div class="w-full h-11 p-1 bg-slate-100 border border-slate-200 rounded-lg flex items-center gap-1">
                <a href="{{ route('education.index') }}" 
                   class="flex-1 h-full flex items-center justify-center text-xs lg:text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-md transition text-center whitespace-nowrap px-2">
                    SMK
                </a>
                <a href="{{ route('campus.index') }}" 
                   class="flex-1 h-full flex items-center justify-center text-xs lg:text-sm font-bold rounded-md bg-white text-blue-600 shadow-xs border border-slate-200/60 transition text-center whitespace-nowrap px-2">
                    Perguruan Tinggi
                </a>
            </div>
        </form>
    </div>

    {{-- Campus Cards Grid (3 Columns) --}}
    <div id="campusGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($campuses as $campus)
            <div class="campus-card bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-200 group"
                 data-name="{{ $campus['name'] }}"
                 data-faculty="{{ $campus['faculty'] ?? '' }}"
                 data-location="{{ $campus['location'] }}"
                 data-province="{{ $campus['province'] ?? '' }}"
                 data-tags="{{ implode(',', $campus['tags']) }}"
                 data-status="{{ $campus['status_badge'] }}">

                <div>
                    {{-- Header Banner & Floating Logo --}}
                    <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                        <img src="{{ $campus['image'] }}" alt="{{ $campus['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        {{-- Views Badge (Top Right) --}}
                        <div class="absolute top-3 right-3 bg-slate-900/60 backdrop-blur-md px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-sm text-white z-10" title="{{ number_format($campus['views']) }} orang melihat">
                            <svg class="w-3.5 h-3.5 text-slate-200 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-[11px] font-bold tracking-tight">{{ number_format($campus['views']) }}</span>
                        </div>

                        {{-- Logo Box --}}
                        <div class="absolute bottom-3 left-4 w-12 h-12 rounded-xl bg-white border border-slate-200/80 shadow-md p-0.5 flex items-center justify-center overflow-hidden">
                            @if(!empty($campus['logo_url']))
                                <img src="{{ $campus['logo_url'] }}" alt="{{ $campus['name'] }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <div class="w-full h-full rounded-lg flex items-center justify-center text-white text-xs font-bold text-center leading-none {{ $campus['logo_bg_class'] ?? 'bg-blue-600' }}">
                                    {{ $campus['logo_text'] }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card Body Content --}}
                    <div class="p-5">
                        {{-- Title Row with Verified Badge --}}
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <h3 class="font-bold text-lg text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                                <a href="{{ route('campus.show', $campus['id']) }}">
                                    {{ $campus['name'] }}
                                </a>
                            </h3>
                            @if($campus['verified'])
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 shrink-0">
                                    <svg class="w-3 h-3 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terverifikasi
                                </span>
                            @endif
                        </div>

                        {{-- Location --}}
                        <div class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                            <svg class="w-3.5 h-3.5 text-slate-400 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $campus['location'] }}</span>
                        </div>

                        {{-- Faculty / Tags --}}
                        <p class="text-xs text-slate-600 mb-3 font-medium">{{ $campus['faculty'] }}</p>
                        <div class="flex flex-wrap gap-1.5 mb-5">
                            @foreach($campus['tags'] as $tag)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Bottom Row --}}
                <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($campus['status_badge_type'] == 'green')
                            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $campus['status_badge'] }}
                            </span>
                        @elseif($campus['status_badge_type'] == 'purple')
                            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                {{ $campus['status_badge'] }}
                            </span>
                        @else
                            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">
                                {{ $campus['status_badge'] }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('campus.show', $campus['id']) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">
                        Lihat Profile
                    </a>
                </div>
            </div>
        @endforeach
    </div>

        {{-- Empty state --}}
        <div id="campusEmptyState" class="hidden text-center py-12 px-6 bg-white border border-dashed border-slate-300 rounded-xl col-span-full">
            <svg class="w-12 h-12 text-slate-400 mx-auto mb-3 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <h4 class="text-base font-bold text-slate-800 mb-1">Tidak Ada Kampus Ditemukan</h4>
            <p class="text-sm text-slate-500">Coba sesuaikan kata kunci pencarian atau filter lokasi dan jurusan IT Anda.</p>
        </div>
    </div>

    {{-- Pagination Navigation Bar --}}
    <div id="campusPaginationBar" class="flex items-center justify-center gap-2 mt-14 mb-6">
        <button type="button" id="campusPrevPageBtn" onclick="changeCampusPage(currentCampusPage - 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" aria-label="Previous">
            <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <button type="button" class="campus-page-num w-9 h-9 rounded-lg border border-blue-700 bg-blue-700 text-white text-sm font-semibold flex items-center justify-center transition cursor-pointer" onclick="changeCampusPage(1)">1</button>
        <button type="button" class="campus-page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" onclick="changeCampusPage(2)">2</button>
        <button type="button" class="campus-page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" onclick="changeCampusPage(3)">3</button>

        <button type="button" id="campusNextPageBtn" onclick="changeCampusPage(currentCampusPage + 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer" aria-label="Next">
            <svg class="w-4 h-4 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>

<script>
    let currentCampusPage = 1;
    const itemsPerPage = 6;
    let filteredCampusItems = [];

    document.addEventListener("DOMContentLoaded", function() {
        applyCampusFilter();
    });

    function applyCampusFilter() {
        const searchInput = document.getElementById('campusSearchInput');
        const searchClear = document.getElementById('campusSearchClear');
        const search = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const loc = document.getElementById('campusLocationSelect').value.toLowerCase();
        const skill = document.getElementById('campusSkillSelect').value.toLowerCase();
        const program = document.getElementById('campusProgramSelect').value.toLowerCase();

        // Toggle clear (×) button
        if (searchClear) {
            searchClear.classList.toggle('hidden', search.length === 0);
        }

        const allCards = Array.from(document.querySelectorAll('.campus-card'));

        filteredCampusItems = allCards.filter(card => {
            const cardName = (card.getAttribute('data-name') || '').toLowerCase();
            const cardFaculty = (card.getAttribute('data-faculty') || '').toLowerCase();
            const cardLoc = (card.getAttribute('data-location') || '').toLowerCase();
            const cardProv = (card.getAttribute('data-province') || '').toLowerCase();
            const cardTags = (card.getAttribute('data-tags') || '').toLowerCase();
            const cardStatus = (card.getAttribute('data-status') || '').toLowerCase();

            const matchSearch = !search || cardName.includes(search) || cardFaculty.includes(search) || cardLoc.includes(search) || cardProv.includes(search) || cardTags.includes(search);
            const matchLoc = !loc || cardLoc.includes(loc) || cardProv.includes(loc);
            const matchSkill = !skill || cardTags.includes(skill) || cardFaculty.includes(skill);
            const matchProg = !program || cardStatus.includes(program);

            return matchSearch && matchLoc && matchSkill && matchProg;
        });

        currentCampusPage = 1;
        renderCampusPage();
    }

    function renderCampusPage() {
        const allCards = document.querySelectorAll('.campus-card');
        const emptyState = document.getElementById('campusEmptyState');
        const paginationBar = document.getElementById('campusPaginationBar');

        allCards.forEach(card => card.classList.add('hidden'));

        if (filteredCampusItems.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (paginationBar) paginationBar.classList.add('hidden');
            return;
        } else {
            if (emptyState) emptyState.classList.add('hidden');
            if (paginationBar) {
                paginationBar.classList.remove('hidden');
                paginationBar.classList.add('flex');
            }
        }

        const totalPages = Math.ceil(filteredCampusItems.length / itemsPerPage);
        if (currentCampusPage > totalPages) currentCampusPage = totalPages;
        if (currentCampusPage < 1) currentCampusPage = 1;

        const startIndex = (currentCampusPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        const pageItems = filteredCampusItems.slice(startIndex, endIndex);
        pageItems.forEach(card => card.classList.remove('hidden'));

        // Update pagination buttons
        const pageBtns = document.querySelectorAll('.campus-page-num');
        pageBtns.forEach((btn, index) => {
            const pageNum = index + 1;
            if (pageNum <= totalPages) {
                btn.classList.remove('hidden');
                if (pageNum === currentCampusPage) {
                    btn.className = 'campus-page-num w-9 h-9 rounded-lg border border-blue-700 bg-blue-700 text-white text-sm font-semibold flex items-center justify-center transition cursor-pointer';
                } else {
                    btn.className = 'campus-page-num w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-semibold flex items-center justify-center hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer';
                }
            } else {
                btn.classList.add('hidden');
            }
        });

        // Prev & Next Buttons state
        const prevBtn = document.getElementById('campusPrevPageBtn');
        const nextBtn = document.getElementById('campusNextPageBtn');

        if (currentCampusPage === 1) {
            prevBtn.classList.add('opacity-40', 'cursor-not-allowed');
        } else {
            prevBtn.classList.remove('opacity-40', 'cursor-not-allowed');
        }

        if (currentCampusPage === totalPages || totalPages === 0) {
            nextBtn.classList.add('opacity-40', 'cursor-not-allowed');
        } else {
            nextBtn.classList.remove('opacity-40', 'cursor-not-allowed');
        }
    }

    function changeCampusPage(page) {
        const totalPages = Math.ceil(filteredCampusItems.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentCampusPage = page;
        renderCampusPage();

        const scrollTarget = document.getElementById('campusSearchInput') || document.getElementById('campusFilterForm');
        window.scrollTo({
            top: scrollTarget.offsetTop - 80,
            behavior: 'smooth'
        });
    }
</script>

@endsection
