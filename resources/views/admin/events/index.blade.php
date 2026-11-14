@extends('admin.layouts.app')

@php
    $role = $userRole ?? auth()->user()?->role;
    $isClientRole = in_array($role, ['school', 'university', 'company'], true);

    $pageHeading = match($role) {
        'company' => 'Manajemen Event Perusahaan',
        'university' => 'Manajemen Event Universitas',
        'school' => 'Manajemen Event Sekolah',
        default => 'Verifikasi & Manajemen Event',
    };

    $pageSub = match($role) {
        'company' => 'Kelola agenda kegiatan, workshop, webinar, atau job fair yang diselenggarakan oleh perusahaan Anda.',
        'university' => 'Kelola agenda kegiatan, seminar, atau kompetisi teknologi kampus Anda.',
        'school' => 'Kelola agenda kegiatan, workshop, atau webinar yang diselenggarakan oleh sekolah Anda.',
        default => 'Kelola dan verifikasi pengajuan event dari sekolah, universitas, maupun perusahaan mitra.',
    };

    $badgeLabel = match($role) {
        'company' => 'Event Milik Perusahaan Anda',
        'university' => 'Event Milik Universitas Anda',
        'school' => 'Event Milik Sekolah Anda',
        default => null,
    };
@endphp

@section('title', $pageHeading . ' — IndoTech Admin')

@section('content')
<div class="space-y-6">

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $pageHeading }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $pageSub }}
            </p>
        </div>

        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.events.create') }}" 
               class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition w-full sm:w-auto">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Event Baru</span>
            </a>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-7 shadow-xs space-y-5 sm:space-y-6">

        {{-- Filter Toolbar: Segmented Pills + Search --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            @if(!$isClientRole)
                {{-- Left: Filter By Pills (Admin only) --}}
                <div class="flex items-center gap-3 overflow-x-auto pb-1 md:pb-0">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider shrink-0 select-none">
                        FILTER BY:
                    </span>

                    <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-xl gap-1 shrink-0">
                        {{-- All Types --}}
                        <a href="{{ route('admin.events.index', array_filter(['type' => 'all', 'q' => request('q')])) }}" 
                           class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $activeType === 'all' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            All Types
                        </a>

                        {{-- School --}}
                        <a href="{{ route('admin.events.index', array_filter(['type' => 'school', 'q' => request('q')])) }}" 
                           class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $activeType === 'school' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            School
                        </a>

                        {{-- University --}}
                        <a href="{{ route('admin.events.index', array_filter(['type' => 'university', 'q' => request('q')])) }}" 
                           class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $activeType === 'university' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            University
                        </a>

                        {{-- Company --}}
                        <a href="{{ route('admin.events.index', array_filter(['type' => 'company', 'q' => request('q')])) }}" 
                           class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $activeType === 'company' ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Company
                        </a>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        @if($role === 'company')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="3" width="16" height="18" rx="2"/>
                                <path d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/>
                            </svg>
                        @elseif($role === 'university')
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        @endif
                        {{ $badgeLabel }}
                    </span>
                </div>
            @endif

            {{-- Right: Search Input --}}
            <form method="GET" action="{{ route('admin.events.index') }}" class="w-full md:w-auto shrink-0">
                @if(request()->has('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="relative flex items-center">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" 
                           name="q" 
                           value="{{ $searchQuery }}" 
                           placeholder="Cari event..." 
                           class="w-full md:w-64 bg-slate-50/70 border border-slate-200/90 rounded-xl pl-9 pr-4 py-2 text-xs font-medium text-slate-700 placeholder-slate-400 outline-none focus:border-blue-500 focus:bg-white transition">
                </div>
            </form>

        </div>

        {{-- Table for Desktop & Tablet --}}
        <div class="hidden md:block overflow-x-auto border-t border-slate-100 pt-4">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-100">
                        <th class="pb-3.5 pr-4 font-extrabold">NAMA EVENT</th>
                        <th class="pb-3.5 px-4 font-extrabold">PENYELENGGARA</th>
                        <th class="pb-3.5 px-4 font-extrabold">TIPE</th>
                        <th class="pb-3.5 px-4 font-extrabold">TANGGAL PELAKSANAAN</th>
                        <th class="pb-3.5 px-4 font-extrabold">STATUS</th>
                        <th class="pb-3.5 pl-4 text-right font-extrabold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($events as $event)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- Event Name --}}
                            <td class="py-4 pr-4">
                                <span class="font-extrabold text-slate-900 text-[13.5px] block">
                                    {{ $event['title'] }}
                                </span>
                            </td>

                            {{-- Organizer --}}
                            <td class="py-4 px-4">
                                <span class="text-slate-600 font-medium text-[13px]">
                                    {{ $event['organizer'] }}
                                </span>
                            </td>

                            {{-- Type Badge --}}
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11.5px] font-semibold bg-indigo-50/80 text-indigo-700 border border-indigo-100/90">
                                    {{ $event['type'] }}
                                </span>
                            </td>

                            {{-- Proposed Date --}}
                            <td class="py-4 px-4">
                                <span class="text-slate-600 font-medium text-[13px]">
                                    {{ $event['proposed_date'] }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-4 px-4">
                                @if($event['status'] === 'Pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif($event['status'] === 'Approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Rejected
                                    </span>
                                @endif
                            </td>

                            {{-- Action Buttons --}}
                            <td class="py-4 pl-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Eye Detail Link Button --}}
                                    <a href="{{ route('admin.events.show', $event['id']) }}" 
                                       title="Lihat Detail Event"
                                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold text-xs p-2 rounded-lg inline-flex items-center justify-center transition shadow-2xs">
                                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>

                                    {{-- Edit Event Button --}}
                                    <a href="{{ route('admin.events.edit', $event['id']) }}" 
                                       title="Edit Event"
                                       class="bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 font-bold text-xs p-2 rounded-lg inline-flex items-center justify-center transition shadow-2xs">
                                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>

                                    @if(!$isClientRole)
                                        {{-- Approve Form (Admin only) --}}
                                        <form action="{{ route('admin.events.approve', $event['id']) }}" method="POST" data-async data-async-reload class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 font-bold text-xs px-3 py-1.5 rounded-lg inline-flex items-center gap-1 transition shadow-2xs">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 6L9 17l-5-5"/>
                                                </svg>
                                                <span>Approve</span>
                                            </button>
                                        </form>

                                        {{-- Reject Form (Admin only) --}}
                                        <form action="{{ route('admin.events.reject', $event['id']) }}" method="POST" data-async data-async-reload class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/90 font-bold text-xs px-3 py-1.5 rounded-lg inline-flex items-center gap-1 transition shadow-2xs">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18 6L6 18M6 6l12 12"/>
                                                </svg>
                                                <span>Reject</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete Event Button --}}
                                    <form id="delete-form-{{ $event['id'] }}" method="POST" action="{{ route('admin.events.destroy', $event['id']) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="openDeleteModal('{{ $event['id'] }}', '{{ addslashes($event['title']) }}', '{{ addslashes($event['organizer'] ?? '') }}')"
                                                title="Hapus Event"
                                                class="bg-slate-100 hover:bg-rose-50 text-rose-600 border border-slate-200 font-bold text-xs p-2 rounded-lg inline-flex items-center justify-center transition shadow-2xs">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18M5 6l1 14a2 2 0 002 2h10a2 2 0 002-2L19 6M10 11v6m4-6v6M8 6l1-3h6l1 3"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-medium">
                                Tidak ada data event yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (No Horizontal Scrolling Needed!) --}}
        <div class="block md:hidden divide-y divide-slate-100 border-t border-slate-100 pt-1">
            @forelse($events as $event)
                <div class="py-3.5 space-y-2.5">
                    {{-- Title + Status --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-slate-900 text-sm block leading-snug break-words">
                                {{ $event['title'] }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium block mt-0.5">
                                {{ $event['organizer'] }}
                            </span>
                        </div>
                        <div class="shrink-0">
                            @if($event['status'] === 'Pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @elseif($event['status'] === 'Approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Metadata info --}}
                    <div class="flex items-center gap-2 flex-wrap text-xs text-slate-600">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $event['type'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-slate-500 text-[11.5px] font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event['proposed_date'] }}
                        </span>
                    </div>

                    {{-- Actions for mobile --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-1">
                            <a href="{{ route('admin.events.show', $event['id']) }}"
                               class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-2xs">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                            <a href="{{ route('admin.events.edit', $event['id']) }}"
                               class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2.5 rounded-xl border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-700 font-bold text-xs transition shadow-2xs">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                                Edit
                            </a>
                            @if(!$isClientRole)
                                <form action="{{ route('admin.events.approve', $event['id']) }}" method="POST" data-async data-async-reload class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="py-1.5 px-2.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/>
                                        </svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.reject', $event['id']) }}" method="POST" data-async data-async-reload class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="py-1.5 px-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                                        </svg>
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                        <button type="button"
                                onclick="openDeleteModal('{{ $event['id'] }}', '{{ addslashes($event['title']) }}', '{{ addslashes($event['organizer'] ?? '') }}')"
                                title="Hapus Event"
                                class="p-2 rounded-xl border border-red-200 bg-red-50/70 hover:bg-red-100 text-red-600 transition shadow-2xs">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 font-medium text-xs">
                    Tidak ada data event yang ditemukan.
                </div>
            @endforelse
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 text-xs font-semibold text-slate-500">
            <div>
                Menampilkan {{ $events->firstItem() ?? 0 }} sampai {{ $events->lastItem() ?? 0 }} dari total {{ $events->total() }} event
            </div>

            {{-- Pagination Controls --}}
            <div>
                {{ $events->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function openDeleteModal(id, name, organizer) {
        let extra = organizer ? ` (${organizer})` : '';
        window.confirmDelete(`{{ url('/admin/events') }}/${id}`, `${name}${extra}`, {
            title: 'Konfirmasi Hapus Event',
            warning: 'Peringatan: Tindakan ini permanen dan data event tidak dapat dikembalikan.'
        });
    }

    function closeDeleteModal() {
        window.closeGlobalDeleteModal();
    }
</script>
@endpush
@endsection
