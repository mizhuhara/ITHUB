@extends('layouts.app')

@section('title', ($tool['name'] ?? 'Detail AI') . ' — IndoTech Knowledge Hub')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Navigation Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6 text-xs font-bold text-slate-500">
            <a href="/" class="hover:text-blue-600 transition">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ route('ai.index') }}" class="hover:text-blue-600 transition">{{ \App\Models\AppSetting::get('nav_ai_info', 'AI Info') }}</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-extrabold">{{ $tool['name'] }}</span>
        </div>

        {{-- Header Tool Banner --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                
                {{-- Left: Icon & Names --}}
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-center shrink-0 overflow-hidden p-2.5 shadow-xs">
                        @if(!empty($tool['logo_url']))
                            <img src="{{ $tool['logo_url'] }}" alt="{{ $tool['name'] }}" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base uppercase">
                                {{ substr($tool['name'], 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ $tool['name'] }}
                            </h1>
                            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full border border-blue-100">
                                {{ $tool['category'] }}
                            </span>
                            @if(!empty($tool['pricing']))
                                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">
                                    {{ $tool['pricing'] }}
                                </span>
                            @endif
                        </div>
                        <p class="text-slate-600 text-xs sm:text-sm font-medium">
                            {{ $tool['tagline'] ?? $tool['description'] }}
                        </p>
                    </div>
                </div>

                {{-- Right Action Button --}}
                <div class="w-full sm:w-auto shrink-0 pt-2 sm:pt-0">
                    <a href="{{ $tool['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl text-xs sm:text-sm shadow-md shadow-blue-500/20 transition-all duration-200">
                        <span>Kunjungi Situs Resmi</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>

            </div>
        </div>

        {{-- Detailed Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            
            {{-- Left 2 Columns: Main Overview & Features --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Section: Overview --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-6 bg-blue-600 rounded-full inline-block"></span>
                        <span>Apa itu {{ $tool['name'] }} dan Apa Fungsinya?</span>
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        {{ $tool['overview'] ?? $tool['description'] }}
                    </p>
                    @if(!empty($tool['what_it_does']))
                        <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-4 text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                            <span class="font-bold text-slate-900">Peran Utama:</span> {{ $tool['what_it_does'] }}
                        </div>
                    @endif
                </div>

                {{-- Section: Capabilities / Features --}}
                @if(!empty($tool['capabilities']))
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-6 bg-blue-600 rounded-full inline-block"></span>
                            <span>Fitur & Kemampuan Utama</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($tool['capabilities'] as $cap)
                                <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-4 hover:border-blue-300 transition">
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $cap['title'] }}</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        {{ $cap['desc'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Sidebar: Use Cases & Target Audience --}}
            <div class="space-y-6">
                
                {{-- Use Cases Card --}}
                @if(!empty($tool['use_cases']))
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            <span>Contoh Penggunaan</span>
                        </h3>
                        <ul class="space-y-2.5">
                            @foreach($tool['use_cases'] as $uc)
                                <li class="flex items-start gap-2 text-xs text-slate-600 leading-relaxed">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                                    <span>{{ $uc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Target Audience Card --}}
                @if(!empty($tool['target_audience']))
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 shadow-sm space-y-2">
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.009a6.002 6.002 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span>Target Pengguna</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-800 font-semibold leading-relaxed">
                            {{ $tool['target_audience'] }}
                        </p>
                    </div>
                @endif

                {{-- External Link Box --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm text-center space-y-3">
                    <p class="text-xs text-slate-500">
                        Tertarik mencoba {{ $tool['name'] }} untuk kebutuhan pekerjaan Anda?
                    </p>
                    <a href="{{ $tool['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="block w-full py-2.5 px-4 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition">
                        Buka {{ $tool['name'] }} &rsaquo;
                    </a>
                </div>

            </div>
        </div>

        {{-- Related AI Tools Recommendations --}}
        @if(!empty($relatedTools))
            <div class="pt-8 border-t border-slate-200">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-6">
                    Rekomendasi AI Lainnya
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedTools as $rel)
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <h3 class="text-base font-bold text-slate-900">
                                        {{ $rel['name'] }}
                                    </h3>
                                    <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-md">
                                        {{ $rel['category'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-4">
                                    {{ $rel['description'] }}
                                </p>
                            </div>

                            <a href="{{ route('ai.show', $rel['id']) }}" 
                               class="block w-full text-center py-2 px-4 border border-blue-600 rounded-full text-xs font-bold text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                Lihat Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
