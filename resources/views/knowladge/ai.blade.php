@extends('layouts.app')

@section('title', 'Rekomendasi AI Terbaik — IndoTech')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back Navigation --}}
        <div class="mb-6">
            <a href="/" 
               class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-8">
            {{ \App\Models\AppSetting::get('nav_ai_info', 'AI Info') }} — Rekomendasi AI Terbaik
        </h1>

        {{-- Category Pills --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 mb-8 w-full sm:w-auto scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
            @foreach($categories as $cat)
                @php
                    $isActive = strtolower($activeCategory) === strtolower($cat);
                @endphp
                <a href="{{ route('ai.index', ['category' => $cat]) }}" 
                   class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap shrink-0 transition-all duration-150 {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-200/70 hover:bg-slate-200 text-slate-600' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- AI Tools Grid --}}
        @if(count($aiTools) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 my-8">
                @foreach($aiTools as $tool)
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between">
                        <div>
                            {{-- Icon Header & Category Badge --}}
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-center shrink-0 overflow-hidden p-2 shadow-2xs">
                                    @if(!empty($tool['logo_url']))
                                        <img src="{{ $tool['logo_url'] }}" alt="{{ $tool['name'] }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($tool['name'], 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Title & Badge --}}
                            <div class="space-y-1 mb-3">
                                <h3 class="text-base font-bold text-slate-900 leading-tight">
                                    {{ $tool['name'] }}
                                </h3>
                                <div>
                                    <span class="inline-block bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-0.5 rounded-md">
                                        {{ $tool['category'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Description --}}
                            <p class="text-xs text-slate-500 leading-relaxed mb-6">
                                {{ $tool['description'] }}
                            </p>
                        </div>

                        {{-- Action Button --}}
                        <div>
                            <a href="{{ route('ai.show', $tool['id']) }}" 
                               class="block w-full text-center py-2.5 px-4 border border-blue-600 rounded-full text-xs font-bold text-blue-600 hover:bg-blue-600 hover:text-white transition duration-200">
                                {{ $tool['action_type'] === 'visit' ? 'Lihat Detail & Situs' : 'Lihat Detail' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center my-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="text-base font-bold text-slate-800">Tidak ada alat AI ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan filter bidang atau kategori.</p>
                <a href="{{ route('ai.index') }}" class="inline-block mt-4 text-xs font-bold text-blue-600 hover:underline">
                    Reset Filter AI
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
