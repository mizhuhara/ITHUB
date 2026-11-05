@extends('admin.layouts.app')

@section('title', 'Approval Komunitas — IndoTech Admin')

@section('content')
<div class="space-y-6">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm font-medium shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold p-1">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm font-medium shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold p-1">&times;</button>
        </div>
    @endif

    {{-- Stats Cards Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold tracking-wider text-amber-600 uppercase">PENDING REVIEW</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($pendingCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Menunggu keputusan</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold tracking-wider text-emerald-600 uppercase">DISETUJUI (APPROVED)</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($approvedCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Aktif di halaman publik</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold tracking-wider text-rose-600 uppercase">DITOLAK (REJECTED)</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($rejectedCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Dapat diajukan kembali</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">TOTAL PENGAJUAN</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($totalCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Seluruh riwayat komunitas</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center font-bold">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Filter & Search Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Toolbar: Tabs & Search --}}
        <div class="p-5 border-b border-slate-100 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            {{-- Status Quick Filter Tabs --}}
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-2xl border border-slate-200 text-xs font-semibold overflow-x-auto shrink-0">
                <a href="{{ route('admin.community-approvals.index', ['status' => 'all', 'search' => request('search'), 'category' => request('category')]) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status', 'all') === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Semua</span>
                    <span class="bg-slate-200/70 text-slate-700 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $totalCount }}</span>
                </a>

                <a href="{{ route('admin.community-approvals.index', ['status' => 'pending', 'search' => request('search'), 'category' => request('category')]) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Pending</span>
                    <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span>
                </a>

                <a href="{{ route('admin.community-approvals.index', ['status' => 'approved', 'search' => request('search'), 'category' => request('category')]) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Disetujui</span>
                    <span class="bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $approvedCount }}</span>
                </a>

                <a href="{{ route('admin.community-approvals.index', ['status' => 'rejected', 'search' => request('search'), 'category' => request('category')]) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'rejected' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Ditolak</span>
                    <span class="bg-rose-100 text-rose-800 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $rejectedCount }}</span>
                </a>
            </div>

            {{-- Search & Category Filter Form --}}
            <form method="GET" action="{{ route('admin.community-approvals.index') }}" class="flex items-center gap-2 flex-1 max-w-xl">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">

                {{-- Category Select --}}
                <select name="category" onchange="this.form.submit()" class="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 shrink-0">
                    <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                {{-- Search Input --}}
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama komunitas, deskripsi, pembuat..." 
                           class="w-full pl-9 pr-9 py-2 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @if(request('search'))
                        <a href="{{ route('admin.community-approvals.index', ['status' => request('status', 'all'), 'category' => request('category')]) }}" 
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                    @endif
                </div>

                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold transition shrink-0">
                    Cari
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Komunitas</th>
                        <th class="py-3.5 px-4">Kategori &amp; Mode</th>
                        <th class="py-3.5 px-4">Pembuat (Creator)</th>
                        <th class="py-3.5 px-4">Pengajuan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($communities as $community)
                        @php
                            $initials = strtoupper(substr($community->name, 0, 2));
                            $colors = [
                                ['#3b82f6', '#1d4ed8'],
                                ['#8b5cf6', '#6d28d9'],
                                ['#ec4899', '#be185d'],
                                ['#10b981', '#047857'],
                                ['#f59e0b', '#b45309'],
                            ];
                            $c = $colors[$community->id % count($colors)];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            {{-- Komunitas (Logo + Name + Deskripsi) --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-3 max-w-sm">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-slate-200/80 bg-white flex items-center justify-center shadow-2xs">
                                        @if($community->logo_url)
                                            <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full text-white font-bold text-xs flex items-center justify-center" style="background: linear-gradient(135deg, {{ $c[0] }}, {{ $c[1] }})">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 group-hover:text-brand-600 transition truncate text-[13px]">
                                            {{ $community->name }}
                                        </div>
                                        <p class="text-[11.5px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ $community->description ?: 'Tidak ada deskripsi.' }}
                                        </p>
                                        @if($community->resubmit_notes && $community->approval_status === 'pending')
                                            <div class="mt-1.5">
                                                <button type="button" 
                                                        onclick="openNotesModal({{ json_encode([
                                                            'id' => $community->id,
                                                            'name' => $community->name,
                                                            'creator_name' => $community->creator?->name ?? 'Pembuat',
                                                            'rejection_reason' => $community->rejection_reason,
                                                            'resubmit_notes' => $community->resubmit_notes,
                                                        ]) }})"
                                                        class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 hover:text-amber-950 px-2.5 py-1 rounded-lg border border-amber-300 hover:border-amber-400 shadow-2xs transition cursor-pointer"
                                                        title="Klik untuk membaca catatan perbaikan dari pembuat">
                                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="text-amber-700 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Ada Catatan Perbaikan</span>
                                                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="text-amber-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori, Jenis & Mode --}}
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold text-[10.5px] border border-blue-100">
                                            {{ $community->category }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md {{ $community->type === 'private' ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-slate-100 text-slate-700 border-slate-200' }} font-medium text-[10.5px] border">
                                            {{ $community->type === 'private' ? 'Privat' : 'Publik' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ strtolower($community->mode) === 'online' ? 'bg-sky-500' : (strtolower($community->mode) === 'offline' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                        <span class="font-medium text-slate-600">{{ $community->formatted_mode }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Pembuat (Creator) --}}
                            <td class="py-3.5 px-4">
                                @if($community->creator)
                                    <div class="font-semibold text-slate-900 text-[12px]">{{ $community->creator->name }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-[150px]">{{ $community->creator->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Admin</span>
                                @endif
                            </td>

                            {{-- Tanggal Pengajuan --}}
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                <div class="font-medium text-[11.5px]">{{ $community->created_at->format('d M Y') }}</div>
                                <div class="text-[10.5px] text-slate-400">{{ $community->created_at->format('H:i') }} WIB</div>
                            </td>

                            {{-- Status Pengajuan --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($community->approval_status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Persetujuan
                                    </span>
                                @elseif($community->approval_status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @elseif($community->approval_status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80" title="{{ $community->rejection_reason }}">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Tombol Detail (Buka Modal) --}}
                                    <button type="button" 
                                            onclick="openDetailModal({{ json_encode([
                                                'id' => $community->id,
                                                'name' => $community->name,
                                                'category' => $community->category,
                                                'description' => $community->description,
                                                'type' => $community->type,
                                                'mode' => $community->formatted_mode,
                                                'location' => $community->location,
                                                'language' => $community->language,
                                                'rules' => $community->rules,
                                                'logo_url' => $community->logo_url,
                                                'cover_url' => $community->cover_url,
                                                'creator_name' => $community->creator?->name ?? 'Sistem',
                                                'creator_email' => $community->creator?->email ?? '-',
                                                'created_at' => $community->created_at->format('d M Y, H:i') . ' WIB',
                                                'approval_status' => $community->approval_status,
                                                'rejection_reason' => $community->rejection_reason,
                                                'resubmit_notes' => $community->resubmit_notes,
                                                'rejected_at' => $community->rejected_at ? $community->rejected_at->format('d M Y, H:i') . ' WIB' : null,
                                                'rejecter_name' => $community->rejecter?->name,
                                                'approved_at' => $community->approved_at ? $community->approved_at->format('d M Y, H:i') . ' WIB' : null,
                                                'approver_name' => $community->approver?->name,
                                            ]) }})"
                                            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" 
                                            title="Lihat Detail Komunitas">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>

                                    {{-- Tombol Setujui (hanya tampil jika status masih pending) --}}
                                    @if($community->approval_status === 'pending')
                                        <button type="button" 
                                                onclick="openApproveModal({{ $community->id }}, '{{ addslashes($community->name) }}', '{{ addslashes($community->creator?->name ?? 'Pembuat') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition font-bold text-[11.5px] flex items-center gap-1 shadow-2xs"
                                                title="Setujui Komunitas">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Setujui</span>
                                        </button>
                                    @endif

                                    {{-- Tombol Tolak (hanya tampil jika status masih pending) --}}
                                    @if($community->approval_status === 'pending')
                                        <button type="button" 
                                                onclick="openRejectModal({{ $community->id }}, '{{ addslashes($community->name) }}', '{{ addslashes($community->creator?->name ?? 'Pembuat') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 transition font-bold text-[11.5px] flex items-center gap-1 shadow-2xs"
                                                title="Tolak Pengajuan">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Tolak</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-xs mx-auto text-slate-400 space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada pengajuan komunitas</p>
                                    <p class="text-xs text-slate-400">
                                        @if(request('search') || request('status') || request('category'))
                                            Tidak ditemukan hasil sesuai filter yang dipilih. Coba atur ulang pencarian Anda.
                                        @else
                                            Belum ada pengajuan komunitas yang membutuhkan review saat ini.
                                        @endif
                                    </p>
                                    @if(request('search') || request('category') || request('status') !== 'all')
                                        <div class="pt-2">
                                            <a href="{{ route('admin.community-approvals.index') }}" class="inline-block text-xs font-semibold text-brand-600 hover:underline">
                                                Reset Semua Filter
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($communities->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-center bg-slate-50/50">
                {{ $communities->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP 1: DETAIL LENGKAP PENGAJUAN KOMUNITAS --}}
{{-- ========================================================================= --}}
<div id="modal-detail-approval" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all max-h-[90vh] flex flex-col text-left">
        {{-- Modal Header --}}
        <div class="p-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/70">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center font-bold border border-blue-100">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 leading-tight">Detail Pengajuan Komunitas</h3>
                    <p class="text-xs text-slate-500">Tinjau seluruh data komunitas sebelum mengambil keputusan</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-700 font-bold p-1 rounded-lg hover:bg-slate-200 transition">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs text-slate-700">
            {{-- Banner & Logo Header --}}
            <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                <div id="modal-banner-wrap" class="h-28 w-full bg-slate-200 overflow-hidden">
                    <img id="modal-banner-img" src="" alt="Banner" class="w-full h-full object-cover hidden">
                    <div id="modal-banner-placeholder" class="w-full h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 opacity-90 flex items-center justify-center text-white/70 font-semibold text-xs">
                        Tidak ada banner khusus
                    </div>
                </div>
                <div class="p-4 bg-white flex items-center gap-4">
                    <div class="w-14 h-14 -mt-10 rounded-2xl border-2 border-white shadow-md bg-white overflow-hidden shrink-0 flex items-center justify-center">
                        <img id="modal-logo-img" src="" alt="Logo" class="w-full h-full object-cover hidden">
                        <div id="modal-logo-placeholder" class="w-full h-full bg-brand-600 text-white font-bold text-base flex items-center justify-center">
                            CM
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 id="modal-name" class="text-base font-bold text-slate-900 truncate"></h4>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <span id="modal-cat" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold text-[11px] border border-blue-100"></span>
                            <span id="modal-type" class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px] border border-slate-200"></span>
                            <span id="modal-mode" class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-100"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rejection Notice (if rejected) --}}
            <div id="modal-rejection-box" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1.5 hidden">
                <div class="flex items-center gap-2 font-bold text-xs text-rose-900">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Komunitas ini telah ditolak:</span>
                </div>
                <p id="modal-rejection-reason" class="text-xs text-rose-700 leading-relaxed pl-6"></p>
                <div id="modal-rejection-meta" class="text-[10.5px] text-rose-500 pl-6 pt-1"></div>
            </div>

            {{-- Catatan Perbaikan dari Pembuat (jika ada) --}}
            <div id="modal-resubmit-box" class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1.5 hidden">
                <div class="flex items-center gap-2 font-bold text-xs text-amber-900">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Catatan Perbaikan dari Pembuat Komunitas:</span>
                </div>
                <p id="modal-resubmit-notes" class="text-xs text-amber-800 leading-relaxed pl-6 font-medium"></p>
            </div>

            {{-- Approval Notice (if approved) --}}
            <div id="modal-approved-box" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2.5 hidden">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="text-emerald-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs">
                    <span class="font-bold">Komunitas telah disetujui</span>
                    <span id="modal-approved-meta" class="text-emerald-700 ml-1"></span>
                </div>
            </div>

            {{-- Grid Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pembuat (Creator)</span>
                    <div id="modal-creator-name" class="font-semibold text-slate-900 text-sm mt-0.5"></div>
                    <div id="modal-creator-email" class="text-slate-500 text-[11.5px]"></div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pengajuan</span>
                    <div id="modal-created-at" class="font-semibold text-slate-900 text-sm mt-0.5"></div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Bahasa Utama</span>
                    <div id="modal-language" class="font-semibold text-slate-900 text-sm mt-0.5"></div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Deskripsi Komunitas</h5>
                <div id="modal-desc" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 leading-relaxed text-slate-700 whitespace-pre-line text-xs"></div>
            </div>

            {{-- Aturan Komunitas --}}
            <div id="modal-rules-wrap">
                <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Aturan &amp; Tata Tertib</h5>
                <div id="modal-rules" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 leading-relaxed text-slate-700 whitespace-pre-line text-xs"></div>
            </div>
        </div>

        {{-- Modal Footer with Action Buttons --}}
        <div class="p-5 border-t border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/70">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition">
                Tutup
            </button>

            <div class="flex items-center gap-2">
                <button type="button" 
                        id="modal-btn-reject" 
                        onclick="triggerRejectFromDetail()" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-600 hover:text-white border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Tolak</span>
                </button>

                <button type="button" 
                        id="modal-btn-approve" 
                        onclick="triggerApproveFromDetail()" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition flex items-center gap-1.5 shadow-2xs">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP 2: KONFIRMASI SETUJUI (APPROVE) --}}
{{-- ========================================================================= --}}
<div id="modal-approve-approval" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 space-y-4 text-left">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 mt-0.5">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold text-slate-900">Setujui Pengajuan Komunitas?</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Komunitas <strong id="approve_community_name" class="text-slate-900"></strong> akan langsung berstatus <span class="font-semibold text-emerald-600">Disetujui</span> dan muncul di direktori publik IndoTech. Pembuat (<span id="approve_creator_name" class="font-semibold text-slate-700"></span>) otomatis menjadi Admin.
                </p>
            </div>
        </div>

        <form id="form-approve-approval" method="POST" action="">
            @csrf
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                    Ya, Setujui Komunitas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP 3: KONFIRMASI TOLAK (REJECT) --}}
{{-- ========================================================================= --}}
<div id="modal-reject-approval" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 space-y-4 text-left">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 mt-0.5">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold text-slate-900">Tolak Pengajuan Komunitas</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Pengajuan <strong id="reject_community_name" class="text-slate-900"></strong> oleh <span id="reject_creator_name" class="font-semibold text-slate-700"></span> akan ditolak dan tidak akan muncul di direktori publik.
                </p>
            </div>
        </div>

        <form id="form-reject-approval" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea id="rejection_reason" 
                          name="rejection_reason" 
                          rows="4" 
                          required 
                          minlength="3"
                          maxlength="2000"
                          placeholder="Tuliskan alasan penolakan secara jelas agar pembuat komunitas memahami dan dapat memperbaiki pengajuannya..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition leading-relaxed"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Alasan ini akan dikirimkan dan dapat dibaca oleh pembuat komunitas.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP 4: LIHAT CATATAN PERBAIKAN KOMUNITAS --}}
{{-- ========================================================================= --}}
<div id="modal-notes-approval" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 text-left relative animate-in fade-in zoom-in-95 duration-150">
        {{-- Close 'X' Button --}}
        <button type="button" 
                onclick="closeNotesModal()" 
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition"
                aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1 pr-6">
                <h3 class="text-base font-extrabold text-slate-900">Catatan Perbaikan Komunitas</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Pengajuan ulang untuk <strong id="notes_community_name" class="text-slate-800"></strong> oleh <span id="notes_creator_name" class="font-semibold text-slate-700"></span>
                </p>
            </div>
        </div>

        {{-- Alasan Penolakan Sebelumnya jika ada --}}
        <div id="notes_rejection_wrap" class="bg-rose-50/80 border border-rose-200/80 rounded-2xl p-3.5 mb-3.5 text-xs text-rose-800 hidden">
            <div class="font-bold flex items-center gap-1.5 text-rose-900 mb-1">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Catatan Penolakan Sebelumnya dari Super Admin:</span>
            </div>
            <p id="notes_rejection_reason" class="text-rose-700 leading-relaxed pl-5 font-normal"></p>
        </div>

        {{-- Catatan Perbaikan yang Ditulis Pembuat --}}
        <div class="bg-amber-50/90 border border-amber-200 rounded-2xl p-4 mb-5 text-xs text-amber-900">
            <div class="font-bold flex items-center gap-1.5 text-amber-950 mb-1.5">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Penjelasan Perbaikan dari Pembuat:</span>
            </div>
            <div id="notes_resubmit_content" class="text-amber-900 leading-relaxed pl-5 font-medium whitespace-pre-line text-[12.5px]"></div>
        </div>

        {{-- Footer Buttons --}}
        <div class="flex items-center justify-between gap-2.5 pt-3 border-t border-slate-100">
            <button type="button" onclick="closeNotesModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <button type="button" 
                        onclick="approveFromNotesModal()" 
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui Komunitas</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentDetailData = null;

    function openDetailModal(data) {
        currentDetailData = data;

        document.getElementById('modal-name').textContent = data.name;
        document.getElementById('modal-cat').textContent = data.category;
        document.getElementById('modal-type').textContent = data.type === 'private' ? 'Privat' : 'Publik';
        document.getElementById('modal-mode').textContent = data.mode;
        document.getElementById('modal-language').textContent = data.language || '-';
        document.getElementById('modal-creator-name').textContent = data.creator_name;
        document.getElementById('modal-creator-email').textContent = data.creator_email;
        document.getElementById('modal-created-at').textContent = data.created_at;
        document.getElementById('modal-desc').textContent = data.description || 'Tidak ada deskripsi.';

        // Rules
        const rulesWrap = document.getElementById('modal-rules-wrap');
        const rulesEl = document.getElementById('modal-rules');
        if (data.rules && data.rules.trim() !== '') {
            rulesEl.textContent = data.rules;
            rulesWrap.classList.remove('hidden');
        } else {
            rulesWrap.classList.add('hidden');
        }

        // Logo
        const logoImg = document.getElementById('modal-logo-img');
        const logoPlaceholder = document.getElementById('modal-logo-placeholder');
        if (data.logo_url) {
            logoImg.src = data.logo_url;
            logoImg.classList.remove('hidden');
            logoPlaceholder.classList.add('hidden');
        } else {
            logoImg.classList.add('hidden');
            logoPlaceholder.classList.remove('hidden');
            logoPlaceholder.textContent = (data.name || 'CM').substring(0, 2).toUpperCase();
        }

        // Banner
        const bannerImg = document.getElementById('modal-banner-img');
        const bannerPlaceholder = document.getElementById('modal-banner-placeholder');
        if (data.cover_url) {
            bannerImg.src = data.cover_url;
            bannerImg.classList.remove('hidden');
            bannerPlaceholder.classList.add('hidden');
        } else {
            bannerImg.classList.add('hidden');
            bannerPlaceholder.classList.remove('hidden');
        }

        // Rejection / Approved Notices & Revision Notes
        const rejectBox = document.getElementById('modal-rejection-box');
        const approvedBox = document.getElementById('modal-approved-box');
        const resubmitBox = document.getElementById('modal-resubmit-box');
        const btnReject = document.getElementById('modal-btn-reject');
        const btnApprove = document.getElementById('modal-btn-approve');

        if (data.resubmit_notes) {
            resubmitBox.classList.remove('hidden');
            document.getElementById('modal-resubmit-notes').textContent = data.resubmit_notes;
        } else {
            resubmitBox.classList.add('hidden');
        }

        if (data.approval_status === 'rejected') {
            rejectBox.classList.remove('hidden');
            approvedBox.classList.add('hidden');
            document.getElementById('modal-rejection-reason').textContent = data.rejection_reason || 'Tidak ada keterangan alasan.';
            document.getElementById('modal-rejection-meta').textContent = 'Ditolak pada ' + (data.rejected_at || '-') + (data.rejecter_name ? ' oleh ' + data.rejecter_name : '');
            btnReject.classList.add('hidden');
            btnApprove.classList.add('hidden');
        } else if (data.approval_status === 'approved') {
            approvedBox.classList.remove('hidden');
            rejectBox.classList.add('hidden');
            document.getElementById('modal-approved-meta').textContent = 'pada ' + (data.approved_at || '-') + (data.approver_name ? ' oleh ' + data.approver_name : '');
            btnApprove.classList.add('hidden');
            btnReject.classList.add('hidden');
        } else {
            // Pending: tampilkan kedua aksi (Setujui & Tolak)
            approvedBox.classList.add('hidden');
            btnApprove.classList.remove('hidden');
            btnReject.classList.remove('hidden');

            if (data.rejection_reason && String(data.rejection_reason).trim() !== '') {
                rejectBox.classList.remove('hidden');
                document.getElementById('modal-rejection-reason').textContent = data.rejection_reason;
                document.getElementById('modal-rejection-meta').textContent = 'Catatan penolakan pada pengajuan sebelumnya.';
            } else {
                rejectBox.classList.add('hidden');
            }
        }

        document.getElementById('modal-detail-approval').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDetailModal() {
        document.getElementById('modal-detail-approval').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function triggerApproveFromDetail() {
        if (!currentDetailData) return;
        closeDetailModal();
        openApproveModal(currentDetailData.id, currentDetailData.name, currentDetailData.creator_name);
    }

    function triggerRejectFromDetail() {
        if (!currentDetailData) return;
        closeDetailModal();
        openRejectModal(currentDetailData.id, currentDetailData.name, currentDetailData.creator_name);
    }

    function openApproveModal(id, name, creatorName) {
        closeDetailModal();
        const form = document.getElementById('form-approve-approval');
        form.action = `{{ url('/admin/community-approvals') }}/${id}/approve`;
        document.getElementById('approve_community_name').textContent = name;
        document.getElementById('approve_creator_name').textContent = creatorName;

        document.getElementById('modal-approve-approval').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeApproveModal() {
        document.getElementById('modal-approve-approval').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openRejectModal(id, name, creatorName) {
        closeDetailModal();
        const form = document.getElementById('form-reject-approval');
        form.action = `{{ url('/admin/community-approvals') }}/${id}/reject`;
        document.getElementById('reject_community_name').textContent = name;
        document.getElementById('reject_creator_name').textContent = creatorName;
        document.getElementById('rejection_reason').value = '';

        document.getElementById('modal-reject-approval').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeRejectModal() {
        document.getElementById('modal-reject-approval').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    let currentNotesCommunity = null;

    function openNotesModal(data) {
        currentNotesCommunity = data;
        document.getElementById('notes_community_name').textContent = data.name;
        document.getElementById('notes_creator_name').textContent = data.creator_name || 'Pembuat';
        document.getElementById('notes_resubmit_content').textContent = data.resubmit_notes || '-';

        const rejWrap = document.getElementById('notes_rejection_wrap');
        const rejText = document.getElementById('notes_rejection_reason');
        if (data.rejection_reason && String(data.rejection_reason).trim() !== '') {
            rejText.textContent = data.rejection_reason;
            rejWrap.classList.remove('hidden');
        } else {
            rejWrap.classList.add('hidden');
        }

        document.getElementById('modal-notes-approval').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeNotesModal() {
        document.getElementById('modal-notes-approval').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function approveFromNotesModal() {
        if (!currentNotesCommunity) return;
        const comm = currentNotesCommunity;
        closeNotesModal();
        openApproveModal(comm.id, comm.name, comm.creator_name);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
            closeApproveModal();
            closeRejectModal();
            closeNotesModal();
        }
    });
</script>
@endpush
@endsection

