@extends('admin.layouts.app')

@section('title', 'Company Management — IndoTech Admin')

@section('content')
<div class="space-y-6">

    {{-- Success Toast Banner --}}
    

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm flex items-start gap-3 animate-fade-in">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-red-500 shrink-0 mt-0.5">
                <circle cx="12" cy="12" r="9"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="flex-1">
                <div class="font-bold">Terdapat beberapa kesalahan:</div>
                <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex items-center justify-end gap-3">
            {{-- Filter Button --}}
            <button type="button" 
                    onclick="toggleFilter()" 
                    id="filterBtn"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-[13.5px] font-semibold shadow-xs transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
                @if((request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all'))
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                @endif
            </button>

            {{-- Add New Company Button --}}
            @if (!empty($canManage))
            <a href="{{ route('admin.company.create') }}" 
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-[13.5px] font-semibold shadow-md shadow-blue-500/20 transition transform active:scale-95">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>Tambah Perusahaan</span>
            </a>
            @endif
    </div>

    {{-- Inline Filter Panel (Collapsible) --}}
    <div id="filterPanel" class="{{ ((request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all')) ? '' : 'hidden' }} bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs transition-all duration-200">
        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filter Data Perusahaan</h3>
            </div>
            <button type="button" onclick="toggleFilter()" class="text-slate-400 hover:text-slate-600 text-xs font-semibold flex items-center gap-1 transition">
                <span>Tutup</span>
                <span class="text-base leading-none">&times;</span>
            </button>
        </div>

        <form method="GET" action="{{ route('admin.company.index') }}" class="flex flex-wrap items-end gap-3.5">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Mitra</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="all" {{ (!request('status') || request('status') === 'all') ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive (Non-aktif)</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tipe Perusahaan</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="all" {{ (!request('type') || request('type') === 'all') ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="Swasta" {{ request('type') === 'Swasta' ? 'selected' : '' }}>Swasta</option>
                    <option value="BUMN" {{ request('type') === 'BUMN' ? 'selected' : '' }}>BUMN</option>
                    <option value="Multinasional" {{ request('type') === 'Multinasional' ? 'selected' : '' }}>Multinasional</option>
                    <option value="Startup" {{ request('type') === 'Startup' ? 'selected' : '' }}>Startup</option>
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.company.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-[13px] hover:bg-slate-50 transition">Reset</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] shadow-sm transition">Terapkan Filter</button>
            </div>
        </form>
    </div>

    {{-- 3 Top Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">TOTAL PERUSAHAAN</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalCompanies) }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">MITRA AKTIF</div>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($activePartners) }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">PENGAJUAN BARU</div>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($newSubmissions) }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Main Table Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        {{-- Table Toolbar: Search & Sort --}}
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.company.index') }}" class="relative flex-1 max-w-md">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif

                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 rounded-xl px-3.5 h-10 w-full focus-within:border-blue-500 focus-within:bg-white transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama perusahaan, industri, atau lokasi..." 
                           class="bg-transparent outline-none text-[13.5px] text-slate-800 placeholder-slate-400 flex-1 min-w-0">
                    @if(request('search'))
                        <a href="{{ route('admin.company.index') }}" class="text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                    @endif
                </div>
            </form>

            <div class="flex items-center gap-2 text-[13.5px] text-slate-600 self-end sm:self-auto">
                <form method="GET" action="{{ route('admin.company.index') }}" id="sortForm" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <span class="text-slate-500 font-medium">Urutkan:</span>
                    <select name="sort" 
                            onchange="document.getElementById('sortForm').submit()"
                            class="bg-transparent font-semibold text-slate-900 outline-none cursor-pointer pr-4 hover:text-blue-600 transition">
                        <option value="recently_added" {{ request('sort') === 'recently_added' ? 'selected' : '' }}>Terbaru ▾</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A-Z) ▾</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama (Z-A) ▾</option>
                        <option value="region_asc" {{ request('sort') === 'region_asc' ? 'selected' : '' }}>Kota (A-Z) ▾</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama ▾</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Companies Table for Desktop & Tablet --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="cell">NAMA PERUSAHAAN</th>
                        <th class="cell">TIPE</th>
                        <th class="cell">INDUSTRI</th>
                        <th class="cell">LOKASI & MAPS</th>
                        <th class="cell">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-slate-50/70 transition group">
                            {{-- Company Name & Logo --}}
                            <td class="cell">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 text-white font-extrabold flex items-center justify-center text-xs shrink-0 overflow-hidden shadow-sm">
                                        @if(!empty($company->logo_url))
                                            <img src="{{ asset($company->logo_url) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.company.show', $company->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition block leading-snug truncate">
                                            {{ $company->name }}
                                        </a>
                                        <div class="text-[12px] text-slate-400 mt-0.5 truncate">
                                            Kode: {{ $company->npsn ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type Badge --}}
                            <td class="cell">
                                @if(strtoupper($company->type) === 'BUMN')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">{{ $company->type }}</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $company->type }}</span>
                                @endif
                            </td>

                            {{-- Industry --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $company->industry }}
                            </td>

                            {{-- Location & Maps Icon --}}
                            <td class="cell">
                                <div class="flex items-center gap-1.5 font-semibold text-slate-800 leading-tight">
                                    <span>{{ $company->location }}</span>
                                    @if($company->map_link)
                                        <a href="{{ $company->map_link }}" target="_blank" class="text-blue-600 hover:text-blue-700 shrink-0" title="Lihat Lokasi di Google Maps">
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                    @endif
                                </div>
                                @if(!empty($company->gallery) && count($company->gallery) > 0)
                                    <div class="text-[11px] text-blue-600 font-bold mt-0.5 flex items-center gap-1">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                        <span>{{ count($company->gallery) }} Foto Galeri</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="cell">
                                @if(strtolower($company->status) === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.company.edit', $company->id) }}" 
                                       class="p-2 rounded-xl text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition" 
                                       title="Edit Perusahaan">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    {{-- Delete Button (Modal Pop-Up, admin only) --}}
                                    @if (!empty($canManage))
                                    <button type="button" 
                                            onclick="openDeleteModal({{ $company->id }}, '{{ addslashes($company->name) }}')"
                                            class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer" 
                                            title="Hapus Perusahaan">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                    </div>
                                    <div class="font-bold text-slate-800">Tidak ada data perusahaan ditemukan</div>
                                    <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                                    <a href="{{ route('admin.company.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Pencarian</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Card List for Mobile (No Horizontal Scroll Needed) --}}
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse ($companies as $company)
                <div class="p-4 space-y-3">
                    {{-- Logo, Name, Type, Status --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 text-white font-extrabold flex items-center justify-center text-xs shrink-0 overflow-hidden shadow-sm">
                                @if(!empty($company->logo_url))
                                    <img src="{{ asset($company->logo_url) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.company.show', $company->id) }}" class="font-bold text-[14.5px] text-slate-900 hover:text-blue-600 transition block leading-snug break-words">
                                    {{ $company->name }}
                                </a>
                                <div class="text-[11.5px] text-slate-400 mt-0.5">
                                    Kode: {{ $company->npsn ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 pt-0.5">
                            @if(strtolower($company->status) === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Type & Industry & Location --}}
                    <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="font-semibold text-slate-700">{{ $company->industry }}</span>
                            @if(strtoupper($company->type) === 'BUMN')
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">{{ $company->type }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $company->type }}</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11.5px] pt-1 border-t border-slate-200/50">
                            <span class="break-words">{{ $company->location }}</span>
                            @if($company->map_link)
                                <a href="{{ $company->map_link }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center gap-0.5 shrink-0">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Peta</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Bar --}}
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.company.show', $company->id) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <span>Detail</span>
                        </a>
                        <a href="{{ route('admin.company.edit', $company->id) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50/50 text-xs font-semibold text-blue-600 hover:bg-blue-100/60 transition">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            <span>Edit</span>
                        </a>
                        @if (!empty($canManage))
                            <button type="button" 
                                    onclick="openDeleteModal({{ $company->id }}, '{{ addslashes($company->name) }}')"
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50/50 text-xs font-semibold text-rose-600 hover:bg-rose-100/60 transition cursor-pointer">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                <span>Hapus</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-slate-500">
                    <div class="max-w-sm mx-auto space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <div class="font-bold text-slate-800">Tidak ada data perusahaan ditemukan</div>
                        <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                        <a href="{{ route('admin.company.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Pencarian</a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-[13px] text-slate-500">
                Showing <span class="font-semibold text-slate-800">{{ $companies->firstItem() ?? 0 }}-{{ $companies->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-800">{{ $companies->total() }}</span> items
            </div>

            <div class="flex items-center gap-1.5 text-[13.5px]">
                @if ($companies->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">‹</span>
                @else
                    <a href="{{ $companies->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">‹</a>
                @endif

                @for ($page = 1; $page <= max(1, $companies->lastPage()); $page++)
                    @if ($page == $companies->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-600 text-white font-bold shadow-xs select-none">{{ $page }}</span>
                    @else
                        <a href="{{ $companies->url($page) }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">{{ $page }}</a>
                    @endif
                @endfor

                @if ($companies->hasMorePages())
                    <a href="{{ $companies->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">›</a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">›</span>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function openDeleteModal(id, name) {
        window.confirmDelete(`{{ url('/admin/company') }}/${id}`, name, {
            title: 'Konfirmasi Hapus Perusahaan',
            warning: 'Peringatan: Tindakan ini permanen dan data perusahaan serta galeri foto akan dihapus.'
        });
    }

    function closeDeleteModal() {
        window.closeGlobalDeleteModal();
    }

    function toggleFilter() {
        const panel = document.getElementById('filterPanel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }
</script>
@endpush
@endsection
