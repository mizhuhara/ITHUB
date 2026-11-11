@extends('admin.layouts.app')

@section('title', 'Kelola Banner & Antrean Beranda — Admin')

@section('content')
{{-- Header and Action Button --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-[20px] font-bold text-slate-900">Kelola Banner & Antrean Beranda</h1>
        <p class="text-[13px] text-slate-500">Maksimal 5 slide aktif di beranda. Banner promosi berikutnya otomatis masuk sistem antrean bergilir.</p>
    </div>

    <div class="flex items-center gap-2">
        @if ($canCreateBanner)
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-brand-600 text-white text-[13.5px] font-semibold hover:bg-blue-700 shadow-sm transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Banner
            </a>
        @elseif (! $membershipActive)
            <a href="{{ route('dashboard.membership') }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[13px] font-semibold hover:brightness-105 shadow-sm transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                Upgrade Membership untuk Pasang Banner
            </a>
        @else
            <button type="button" disabled class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-slate-100 text-slate-400 text-[13px] font-semibold cursor-not-allowed border border-slate-200">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                Kuota Minggu Ini Terpakai (1x/Minggu)
            </button>
        @endif
    </div>
</div>

{{-- Flash messages --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13.5px] font-medium">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[13.5px] font-medium">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-rose-500"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Institution Notice Banner --}}
@if (! $isAdmin)
    <div class="mb-6 rounded-2xl border {{ $membershipActive ? 'border-blue-200/80 bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-white' : 'border-amber-200 bg-amber-50/60' }} p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11.5px] font-bold {{ $membershipActive ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $membershipActive ? 'Membership Aktif' : 'Belum Berlangganan' }}
                    </span>
                    <span class="text-[13px] font-bold text-slate-800">Aturan Banner Promosi Institusi</span>
                </div>
                <p class="text-[12.5px] text-slate-600">
                    Institusi berlangganan dapat menambahkan <strong class="text-slate-800">maksimal 1 banner promosi per minggu</strong>. Slider beranda menampung maksimal 5 banner aktif selama masa tayang 7 hari. Jika kuota 5 slide penuh, banner Anda masuk antrean bergilir secara otomatis.
                </p>
                @if ($rateLimitMessage)
                    <div class="pt-1 text-[12px] font-semibold text-amber-700 flex items-center gap-1.5">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                        {{ $rateLimitMessage }}
                    </div>
                @else
                    <div class="pt-1 text-[12px] font-semibold text-emerald-600 flex items-center gap-1.5">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Kuota banner Anda minggu ini tersedia untuk ditambahkan.
                    </div>
                @endif
            </div>
            @if (! $membershipActive)
                <a href="{{ route('dashboard.membership') }}" class="shrink-0 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[12.5px] font-bold transition">
                    Aktifkan Langganan
                </a>
            @endif
        </div>
    </div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-[12.5px] font-semibold text-slate-500">Slide Aktif di Beranda</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeBanners >= $maxSlots ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $activeBanners >= $maxSlots ? 'Slot Penuh' : 'Tersedia ' . ($maxSlots - $activeBanners) . ' Slot' }}
            </span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-[28px] font-black text-slate-900 leading-none">{{ $activeBanners }}</span>
            <span class="text-[15px] font-bold text-slate-400">/ {{ $maxSlots }} Slide</span>
        </div>
        <div class="mt-2.5 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $activeBanners >= $maxSlots ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, ($activeBanners / $maxSlots) * 100) }}%"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-[12.5px] font-semibold text-slate-500">Antrean Banner (Queue)</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                Sistem Antre
            </span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-[28px] font-black text-slate-900 leading-none">{{ $queuedBanners }}</span>
            <span class="text-[14px] font-medium text-slate-500">Banner Menunggu</span>
        </div>
        <p class="mt-2 text-[11.5px] text-slate-400">Otomatis naik ke slide saat slot beranda selesai</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-[12.5px] font-semibold text-slate-500">Durasi Tayang Promosi</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700">
                Masa Aktif
            </span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-[28px] font-black text-slate-900 leading-none">7</span>
            <span class="text-[14px] font-medium text-slate-500">Hari per Banner</span>
        </div>
        <p class="mt-2 text-[11.5px] text-slate-400">Bergilir bergantian dengan antrean berikutnya</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-[12.5px] font-semibold text-slate-500">Total Banner Terdaftar</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                Semua
            </span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-[28px] font-black text-slate-900 leading-none">{{ $totalBanners }}</span>
            <span class="text-[14px] font-medium text-slate-500">Banner</span>
        </div>
        <p class="mt-2 text-[11.5px] text-slate-400">Termasuk slide aktif, antrean, dan riwayat</p>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex items-center gap-2 mb-4 overflow-x-auto pb-1">
    <button type="button" onclick="filterBanners('all')" id="tab-all" class="filter-tab px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold bg-slate-900 text-white transition">
        Semua ({{ $totalBanners }})
    </button>
    <button type="button" onclick="filterBanners('active')" id="tab-active" class="filter-tab px-3.5 py-1.5 rounded-xl text-[12.5px] font-medium bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition">
        Slide Aktif Beranda ({{ $activeBanners }}/{{ $maxSlots }})
    </button>
    <button type="button" onclick="filterBanners('queued')" id="tab-queued" class="filter-tab px-3.5 py-1.5 rounded-xl text-[12.5px] font-medium bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition">
        Antrean ({{ $queuedBanners }})
    </button>
    <button type="button" onclick="filterBanners('expired')" id="tab-expired" class="filter-tab px-3.5 py-1.5 rounded-xl text-[12.5px] font-medium bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition">
        Riwayat / Expired
    </button>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[11.5px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <th class="px-5 py-3 font-semibold">PREVIEW</th>
                    <th class="px-5 py-3 font-semibold">JUDUL & PEMBUAT</th>
                    <th class="px-5 py-3 font-semibold">STATUS SLIDE</th>
                    <th class="px-5 py-3 font-semibold">WAKTU & MASA TAYANG</th>
                    <th class="px-5 py-3 font-semibold text-center">URUTAN</th>
                    <th class="px-5 py-3 font-semibold text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="banners-tbody">
                @forelse ($banners as $banner)
                    @php
                        $isCurrentActive = $banner->isCurrentlyActive();
                        $isQueued = $banner->status === 'queued';
                        $isExpired = $banner->status === 'expired' || ($banner->expires_at && $banner->expires_at->isPast());
                        $rowCategory = $isCurrentActive ? 'active' : ($isQueued ? 'queued' : 'expired');
                        $isOwner = $banner->user_id === auth()->id();
                    @endphp
                    <tr class="banner-row hover:bg-slate-50/50 transition-colors" data-category="{{ $rowCategory }}">
                        <td class="px-5 py-3.5">
                            <div class="relative w-28 h-14 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shadow-xs">
                                <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                                @if ($isCurrentActive)
                                    <span class="absolute top-1 left-1 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="text-[13.5px] font-bold text-slate-900">{{ $banner->title }}</div>
                            @if($banner->subtitle)
                                <div class="text-[12px] text-slate-500 mt-0.5">{{ Str::limit($banner->subtitle, 50) }}</div>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                @if ($banner->user)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        {{ $banner->user->school?->name ?? $banner->user->university?->name ?? $banner->user->company?->name ?? $banner->user->name }}
                                        <span class="text-slate-400">({{ ucfirst($banner->user->role) }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/60">
                                        Admin Utama
                                    </span>
                                @endif
                                @if ($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-brand-600 hover:underline flex items-center gap-0.5">
                                        Tautan <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                                <span>Dibuat: {{ $banner->created_at ? $banner->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($isCurrentActive)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 text-[11.5px] font-bold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif di Slide
                                </span>
                            @elseif ($isQueued)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-1 text-[11.5px] font-bold text-amber-700">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                    Antrean Ke-{{ $banner->queue_position ?? 1 }}
                                </span>
                            @elseif ($isExpired)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-200 px-2.5 py-1 text-[11.5px] font-medium text-slate-500">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Masa Tayang Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11.5px] font-medium text-slate-500">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($isCurrentActive)
                                @if ($banner->expires_at)
                                    <div class="space-y-1">
                                        <div>
                                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200/70">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Sisa {{ $banner->expires_at->diffForHumans(null, true) }}
                                            </span>
                                        </div>
                                        <div class="text-[11.5px] text-slate-700 font-medium">
                                            <span class="text-slate-400">Mulai:</span> {{ ($banner->started_at ?? $banner->created_at)->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        <div class="text-[11.5px] text-slate-700 font-medium">
                                            <span class="text-slate-400">Selesai:</span> {{ $banner->expires_at->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-blue-700 border border-blue-200/70">
                                            Aktif Permanen
                                        </span>
                                        <div class="text-[11.5px] text-slate-700 font-medium">
                                            <span class="text-slate-400">Mulai:</span> {{ ($banner->started_at ?? $banner->created_at)->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">Tanpa batas waktu (Admin)</div>
                                    </div>
                                @endif
                            @elseif ($isQueued)
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700 border border-amber-200/70">
                                        Menunggu Antrean #{{ $banner->queue_position ?? 1 }}
                                    </span>
                                    <div class="text-[11.5px] text-slate-700 font-medium">
                                        <span class="text-slate-400">Diajukan:</span> {{ $banner->created_at->translatedFormat('d M Y, H:i') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">Durasi 7 hari dihitung saat mulai tayang</div>
                                </div>
                            @elseif ($isExpired)
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 border border-slate-200">
                                        Selesai Tayang
                                    </span>
                                    <div class="text-[11.5px] text-slate-600">
                                        <span class="text-slate-400">Mulai:</span> {{ $banner->started_at ? $banner->started_at->translatedFormat('d M Y, H:i') : ($banner->created_at ? $banner->created_at->translatedFormat('d M Y, H:i') : '-') }}
                                    </div>
                                    <div class="text-[11.5px] text-slate-600">
                                        <span class="text-slate-400">Berakhir:</span> {{ $banner->expires_at ? $banner->expires_at->translatedFormat('d M Y, H:i') : '-' }}
                                    </div>
                                </div>
                            @else
                                <div class="text-[12px] text-slate-500">
                                    <span class="text-slate-400">Dibuat:</span> {{ $banner->created_at ? $banner->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center text-[13px] text-slate-600 font-bold">
                            {{ $banner->sort_order }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Promote button for admin on queued banners --}}
                                @if ($isAdmin && $isQueued)
                                    <form action="{{ route('admin.banners.promote', $banner->id) }}" method="POST" data-async data-async-follow>
                                        @csrf
                                        <button type="submit" title="Promosikan langsung ke slide aktif" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11.5px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition cursor-pointer">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/></svg>
                                            Tayangkan
                                        </button>
                                    </form>
                                @endif

                                @if ($isAdmin || $isOwner)
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[12px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition cursor-pointer" title="Edit Banner">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        Edit
                                    </a>

                                    <button type="button" 
                                            onclick="openDeleteModal('{{ $banner->id }}', '{{ addslashes($banner->title) }}')" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[12px] font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/60 transition cursor-pointer" 
                                            title="Hapus Banner">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        Hapus
                                    </button>

                                    <form id="delete-banner-form-{{ $banner->id }}" action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="text-slate-400">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" class="mx-auto mb-3"><rect x="2" y="4" width="20" height="16" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                                <p class="text-[13.5px] font-medium text-slate-500">Belum ada banner terdaftar</p>
                                <p class="text-[12px] text-slate-400 mt-1">Klik tombol "Tambah Banner" untuk membuat banner baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function filterBanners(type) {
        const rows = document.querySelectorAll('.banner-row');
        const tabs = document.querySelectorAll('.filter-tab');

        tabs.forEach(t => {
            t.classList.remove('bg-slate-900', 'text-white');
            t.classList.add('bg-white', 'text-slate-600');
        });

        const activeTab = document.getElementById('tab-' + type);
        if (activeTab) {
            activeTab.classList.remove('bg-white', 'text-slate-600');
            activeTab.classList.add('bg-slate-900', 'text-white');
        }

        rows.forEach(r => {
            if (type === 'all' || r.dataset.category === type) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function openDeleteModal(id, title) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(`{{ url('/admin/banners') }}/${id}`, title, {
                title: 'Konfirmasi Hapus Banner',
                warning: 'Peringatan: Banner akan dihapus secara permanen. Jika ada antrean, banner berikutnya akan otomatis naik ke slide beranda.',
                submitText: 'Hapus Banner'
            });
        } else {
            if (confirm(`Yakin ingin menghapus banner "${title}"?`)) {
                const form = document.getElementById(`delete-banner-form-${id}`);
                if (form) form.submit();
            }
        }
    }
</script>
@endpush
@endsection

