@extends('layouts.app')

@section('title', 'Knowledge Hub — IndoTech')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mb-3">
                Knowledge Hub
            </h1>
            <p class="text-slate-600 text-base sm:text-lg max-w-3xl leading-relaxed">
                Stay at the forefront of the digital world. Curated insights, industry trends, and in-depth technical analysis bridging Indonesian talent with global IT excellence.
            </p>
        </div>

        {{-- Category Pills Row with hidden scrollbar and scroll controls --}}
        <div class="relative mb-8 group">
            {{-- Left Scroll Button --}}
            <button id="category-scroll-left" type="button" aria-label="Scroll Left"
                    class="absolute -left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/95 shadow-md border border-slate-200 text-slate-600 hover:text-blue-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 backdrop-blur-sm hidden sm:flex">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Category Pills Container (Hidden scrollbar) --}}
            <div id="category-scroll-container" 
                 class="flex items-center gap-2.5 overflow-x-auto py-1 scroll-smooth select-none cursor-grab active:cursor-grabbing"
                 style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                @foreach($categories as $cat)
                    @php
                        $isActive = strtolower($activeCategory) === strtolower($cat);
                        // Special link for AI pill to showcase AI Recommendations page directly or filter
                        $linkUrl = (strtolower($cat) === 'ai') 
                            ? route('knowledge.ai') 
                            : route('knowledge.index', ['category' => strtolower($cat) === 'all' ? 'All' : $cat]);
                    @endphp
                    <a href="{{ $linkUrl }}" 
                       class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap shrink-0 transition-all duration-150 {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-200/70 hover:bg-slate-200 text-slate-600' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            {{-- Right Scroll Button --}}
            <button id="category-scroll-right" type="button" aria-label="Scroll Right"
                    class="absolute -right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/95 shadow-md border border-slate-200 text-slate-600 hover:text-blue-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 backdrop-blur-sm hidden sm:flex">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <style>
            #category-scroll-container::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('category-scroll-container');
                const btnLeft = document.getElementById('category-scroll-left');
                const btnRight = document.getElementById('category-scroll-right');

                if (container) {
                    if (btnLeft) {
                        btnLeft.addEventListener('click', () => {
                            container.scrollBy({ left: -220, behavior: 'smooth' });
                        });
                    }
                    if (btnRight) {
                        btnRight.addEventListener('click', () => {
                            container.scrollBy({ left: 220, behavior: 'smooth' });
                        });
                    }

                    // Mouse wheel horizontal scroll support
                    container.addEventListener('wheel', (e) => {
                        if (e.deltaY !== 0) {
                            e.preventDefault();
                            container.scrollLeft += e.deltaY;
                        }
                    }, { passive: false });

                    // Drag to scroll functionality
                    let isDown = false;
                    let startX, scrollLeft;

                    container.addEventListener('mousedown', (e) => {
                        isDown = true;
                        startX = e.pageX - container.offsetLeft;
                        scrollLeft = container.scrollLeft;
                    });
                    container.addEventListener('mouseleave', () => { isDown = false; });
                    container.addEventListener('mouseup', () => { isDown = false; });
                    container.addEventListener('mousemove', (e) => {
                        if (!isDown) return;
                        e.preventDefault();
                        const x = e.pageX - container.offsetLeft;
                        const walk = (x - startX) * 1.5;
                        container.scrollLeft = scrollLeft - walk;
                    });
                }
            });
        </script>

        {{-- Banner for AI Tools Directory if AI filter is active --}}
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 rounded-2xl p-6 mb-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg shadow-blue-500/10">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white mb-1">
                    <span></span>
                    <span>AI Hub Spotlight</span>
                </div>
                <h3 class="text-xl font-extrabold text-white">Eksplorasi Rekomendasi AI Terbaik</h3>
                <p class="text-blue-100 text-xs sm:text-sm max-w-xl">
                    Temukan perkakas AI terkemuka untuk Pemrograman, Kesehatan, Pertanian, dan berbagai industri terkini.
                </p>
            </div>
            <a href="{{ route('knowledge.ai') }}" 
               class="shrink-0 bg-white text-blue-600 hover:bg-blue-50 font-bold px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md transition-all duration-200 flex items-center gap-2">
                <span>Lihat Rekomendasi AI</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        {{-- Articles Grid --}}
        @if(count($articles) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12">
                @foreach($articles as $article)
                    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between group">
                        <div>
                            {{-- Image Container & Category Tag --}}
                            <div class="relative h-52 w-full bg-slate-100 overflow-hidden">
                                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                
                                {{-- Category Tag Pill --}}
                                <div class="absolute top-3 left-3 bg-blue-600 text-white text-[11px] font-bold px-3 py-1 rounded-md shadow-sm">
                                    {{ $article['category'] }}
                                </div>
                            </div>

                            {{-- Card Content --}}
                            <div class="p-6 space-y-3">
                                {{-- Title --}}
                                <h2 class="text-lg font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-blue-600 transition">
                                    <a href="{{ route('knowledge.show', $article['id']) }}">
                                        {{ $article['title'] }}
                                    </a>
                                </h2>

                                {{-- Excerpt if present --}}
                                @if(!empty($article['excerpt']))
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $article['excerpt'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Author Metadata Footer --}}
                        <div class="p-6 pt-0">
                            <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                                @if(!empty($article['author_avatar']))
                                    <img src="{{ $article['author_avatar'] }}" alt="{{ $article['author_name'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-slate-200">
                                @else
                                    <span class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center text-xs font-bold text-white shadow-xs" style="background-color: {{ $article['author_bg'] }}">
                                        {{ $article['author_initials'] }}
                                    </span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if(!empty($article['author_url']))
                                            <a href="{{ $article['author_url'] }}" class="text-xs font-bold text-slate-800 leading-tight truncate hover:text-blue-600 transition" title="Lihat profil {{ $article['author_name'] }}">
                                                {{ $article['author_name'] }}
                                            </a>
                                        @else
                                            <h4 class="text-xs font-bold text-slate-800 leading-tight truncate">
                                                {{ $article['author_name'] }}
                                            </h4>
                                        @endif
                                        @if(!empty($article['author_badge']))
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $article['author_badge_class'] }}">
                                                {{ $article['author_badge'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        <span class="text-slate-500 font-medium">{{ $article['author_role'] }}</span>
                                        <span class="mx-1">•</span> {{ $article['date'] }} <span class="mx-1">•</span> {{ $article['read_time'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center my-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-base font-bold text-slate-800">Tidak ada artikel ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau kategori Anda.</p>
                <a href="{{ route('knowledge.index') }}" class="inline-block mt-4 text-xs font-bold text-blue-600 hover:underline">
                    Lihat Semua Artikel
                </a>
            </div>
        @endif

        {{-- Pagination Component (Matching Design Image 1) --}}
        @php
            $curr = $currentPage ?? 1;
            $maxP = $totalPages ?? 12;
        @endphp
        <div class="flex items-center justify-center gap-2 pt-4">
            {{-- Prev --}}
            @if($curr > 1)
                <a href="{{ route('knowledge.index', ['page' => $curr - 1, 'category' => $activeCategory]) }}" 
                   class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center text-sm transition">
                    &lsaquo;
                </a>
            @else
                <span class="w-9 h-9 rounded-xl border border-slate-200 text-slate-300 flex items-center justify-center opacity-50 text-sm cursor-not-allowed">
                    &lsaquo;
                </span>
            @endif

            {{-- Page 1 --}}
            <a href="{{ route('knowledge.index', ['page' => 1, 'category' => $activeCategory]) }}" 
               class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition {{ $curr === 1 ? 'bg-blue-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                1
            </a>

            {{-- Page 2 --}}
            <a href="{{ route('knowledge.index', ['page' => 2, 'category' => $activeCategory]) }}" 
               class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition {{ $curr === 2 ? 'bg-blue-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                2
            </a>

            {{-- Page 3 --}}
            <a href="{{ route('knowledge.index', ['page' => 3, 'category' => $activeCategory]) }}" 
               class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition {{ $curr === 3 ? 'bg-blue-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                3
            </a>

            <span class="px-1 text-slate-400 text-xs">...</span>

            {{-- Max Page (12) --}}
            <a href="{{ route('knowledge.index', ['page' => $maxP, 'category' => $activeCategory]) }}" 
               class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs transition {{ $curr === $maxP ? 'bg-blue-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                {{ $maxP }}
            </a>

            {{-- Next --}}
            @if($curr < $maxP)
                <a href="{{ route('knowledge.index', ['page' => $curr + 1, 'category' => $activeCategory]) }}" 
                   class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center text-sm transition">
                    &rsaquo;
                </a>
            @else
                <span class="w-9 h-9 rounded-xl border border-slate-200 text-slate-300 flex items-center justify-center opacity-50 text-sm cursor-not-allowed">
                    &rsaquo;
                </span>
            @endif
        </div>

    </div>
</div>
@endsection
