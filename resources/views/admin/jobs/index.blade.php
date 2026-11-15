@extends('admin.layouts.app')

@section('title', 'Manajemen Lowongan Pekerjaan — Admin IndoTech')

@section('content')
<div class="space-y-6">
    {{-- Search Bar in Content / Header Area --}}
    <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-2.5 shadow-2xs flex items-center gap-3">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
        </svg>
        <form action="{{ route('admin.jobs.index') }}" method="GET" class="flex-1">
            <input type="text" 
                   name="search"
                   value="{{ $search ?? '' }}" 
                   placeholder="Cari lowongan pekerjaan, posisi, atau kode ID..." 
                   class="w-full bg-transparent text-[13.5px] text-slate-800 placeholder-slate-400 focus:outline-none"
                   onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-emerald-800 text-[13.5px] font-semibold flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" class="text-emerald-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-lg leading-none">&times;</button>
    </div>
    @endif

    {{-- Action Button --}}
    <div class="flex items-center justify-end">
        <a href="{{ route('admin.jobs.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-blue-700 text-white font-semibold text-[13.5px] px-5 py-2.5 rounded-lg shadow-xs transition">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8"><path d="M12 5v14M5 12h14"/></svg>
            <span>TAMBAH LOWONGAN BARU</span>
        </a>
    </div>

    {{-- Stats Cards (3 Columns) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        {{-- Total Postings --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
            <div class="text-[11.5px] font-bold text-slate-400 uppercase tracking-wider">TOTAL LOWONGAN</div>
            <div class="text-[32px] font-extrabold text-slate-900 tracking-tight leading-tight mt-2.5">{{ $totalPostings }}</div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[12.5px] font-semibold text-emerald-600">
                <span class="font-bold">+12%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>

        {{-- Active Jobs --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
            <div class="text-[11.5px] font-bold text-slate-400 uppercase tracking-wider">LOWONGAN AKTIF</div>
            <div class="text-[32px] font-extrabold text-slate-900 tracking-tight leading-tight mt-2.5">{{ $activeJobs }}</div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[12.5px] font-semibold text-emerald-600">
                <span class="font-bold">+5%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>

        {{-- Applications --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
            <div class="text-[11.5px] font-bold text-slate-400 uppercase tracking-wider">TOTAL PELAMAR</div>
            <div class="text-[32px] font-extrabold text-slate-900 tracking-tight leading-tight mt-2.5">{{ $applicationsCount }}</div>
            <div class="mt-2.5 flex items-center gap-1.5 text-[12.5px] font-semibold text-emerald-600">
                <span class="font-bold">+24%</span>
                <span class="text-slate-400 font-normal">dari bulan lalu</span>
            </div>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        {{-- Filter Tabs and Actions Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-4 sm:px-6 border-b border-slate-200/80 gap-2 sm:gap-4 pt-2">
            {{-- Nav Tabs --}}
            <div class="flex items-center gap-4 sm:gap-6 text-[13px] font-bold overflow-x-auto whitespace-nowrap -mx-4 px-4 sm:mx-0 sm:px-0">
                <a href="{{ route('admin.jobs.index', ['tab' => 'all']) }}" 
                    class="py-3 sm:py-3.5 border-b-2 transition relative shrink-0 {{ $currentTab === 'all' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    SEMUA LOWONGAN
                </a>
                <a href="{{ route('admin.jobs.index', ['tab' => 'active']) }}" 
                    class="py-3 sm:py-3.5 border-b-2 transition relative shrink-0 {{ $currentTab === 'active' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    AKTIF
                </a>
                <a href="{{ route('admin.jobs.index', ['tab' => 'drafts']) }}" 
                    class="py-3 sm:py-3.5 border-b-2 transition relative shrink-0 {{ $currentTab === 'drafts' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    DRAF
                </a>
                <a href="{{ route('admin.jobs.index', ['tab' => 'closed']) }}" 
                    class="py-3 sm:py-3.5 border-b-2 transition relative shrink-0 {{ $currentTab === 'closed' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    DITUTUP
                </a>
            </div>

            {{-- Right Actions (Filter & Export) --}}
            <div class="flex items-center justify-between sm:justify-end gap-4 py-2.5 sm:py-3 text-[12.5px] font-bold text-slate-500 border-t sm:border-t-0 border-slate-100">
                <button type="button" onclick="openFilterModal()" class="flex items-center gap-1.5 {{ $hasActiveFilter ? 'text-blue-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }} transition cursor-pointer">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18m-15 5h12m-9 5h6m-3 5h0"/></svg>
                    <span>FILTER</span>
                    @if($hasActiveFilter)
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    @endif
                </button>
                <a href="{{ route('admin.jobs.export', request()->query()) }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 transition cursor-pointer" title="Export Data ke Excel (CSV)">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 12 12 16.5m0 0 4.5-4.5M12 16.5v-13.5"/></svg>
                    <span>EKSPOR</span>
                </a>
            </div>
        </div>

        {{-- Active Filters Info Bar --}}
        @if($hasActiveFilter)
        <div class="px-4 sm:px-6 py-2.5 bg-blue-50/60 border-b border-blue-100/80 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-slate-500 font-medium">Filter Aktif:</span>
                @if(request('status') && request('status') !== 'all')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white border border-blue-200 text-blue-700 font-semibold shadow-2xs">
                        Status: {{ request('status') }}
                        <a href="{{ route('admin.jobs.index', array_merge(request()->except('status'), ['status' => 'all'])) }}" class="hover:text-blue-900 ml-0.5">&times;</a>
                    </span>
                @endif
                @if(request('type') && request('type') !== 'all')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white border border-blue-200 text-blue-700 font-semibold shadow-2xs">
                        Tipe: {{ request('type') }}
                        <a href="{{ route('admin.jobs.index', array_merge(request()->except('type'), ['type' => 'all'])) }}" class="hover:text-blue-900 ml-0.5">&times;</a>
                    </span>
                @endif
                @if(request('department') && request('department') !== 'all')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white border border-blue-200 text-blue-700 font-semibold shadow-2xs">
                        Departemen: {{ request('department') }}
                        <a href="{{ route('admin.jobs.index', array_merge(request()->except('department'), ['department' => 'all'])) }}" class="hover:text-blue-900 ml-0.5">&times;</a>
                    </span>
                @endif
            </div>
            <a href="{{ route('admin.jobs.index', ['tab' => $currentTab]) }}" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">Reset Filter</a>
        </div>
        @endif

        {{-- Table for Desktop & Tablet --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-4 pl-6 pr-4 font-bold">JUDUL LOWONGAN</th>
                        <th class="py-4 px-4 font-bold">PERUSAHAAN</th>
                        <th class="py-4 px-4 font-bold">TIPE</th>
                        <th class="py-4 px-4 font-bold">PELAMAR</th>
                        <th class="py-4 px-4 font-bold">TANGGAL TAYANG</th>
                        <th class="py-4 px-4 font-bold">STATUS</th>
                        <th class="py-4 pr-6 pl-4 font-bold text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse ($jobs as $job)
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="py-4 pl-6 pr-4">
                                <a href="{{ route('admin.jobs.show', $job['id']) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block leading-tight">
                                    {{ $job['title'] }}
                                </a>
                                <span class="text-[11.5px] text-slate-400 font-medium block mt-0.5">
                                    ID: {{ $job['code'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-700">
                                {{ $job['company'] }}
                            </td>
                            <td class="py-4 px-4 text-slate-600 font-medium">
                                {{ $job['type'] }}
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $appCount = $job->applications_count ?? ($job->applications ? $job->applications->count() : 0);
                                @endphp
                                @if ($appCount > 0)
                                    <a href="{{ route('admin.jobs.show', $job['id']) }}#daftar-pelamar" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition" title="Lihat {{ $appCount }} pelamar">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                        <span>{{ $appCount }} Pelamar</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">0 Pelamar</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-500 font-medium">
                                {{ $job['date_posted'] }}
                            </td>
                            <td class="py-4 px-4">
                                @if ($job['status'] === 'Active')
                                    <span class="font-bold text-emerald-600 text-[13px]">Aktif</span>
                                @elseif ($job['status'] === 'Paused')
                                    <span class="font-bold text-amber-500 text-[13px]">Dijeda</span>
                                @elseif ($job['status'] === 'Draft')
                                    <span class="font-bold text-slate-400 text-[13px]">Draf</span>
                                @else
                                    <span class="font-bold text-red-500 text-[13px]">Ditutup</span>
                                @endif
                            </td>
                            <td class="py-4 pr-6 pl-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-slate-400">
                                    {{-- View Icon --}}
                                    <a href="{{ route('admin.jobs.show', $job['id']) }}" class="hover:text-slate-700 transition" title="Lihat Detail Lowongan">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    {{-- Edit Icon --}}
                                    <a href="{{ route('admin.jobs.edit', $job['id']) }}" class="hover:text-blue-600 transition" title="Edit Lowongan">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                    </a>
                                    {{-- Delete Icon --}}
                                    <form id="delete-form-{{ $job['id'] }}" action="{{ route('admin.jobs.destroy', $job['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="openDeleteModal('{{ $job['id'] }}', '{{ addslashes($job['title']) }}', '{{ addslashes($job['company']) }}')"
                                                class="hover:text-red-600 transition cursor-pointer" 
                                                title="Hapus Lowongan">
                                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-[13.5px]">
                                Tidak ada data lowongan pekerjaan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Card List for Mobile (No Horizontal Scrolling Needed!) --}}
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse ($jobs as $job)
                <div class="p-4 space-y-3">
                    {{-- Title + Status --}}
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.jobs.show', $job['id']) }}" class="font-bold text-[14.5px] text-slate-900 hover:text-brand-600 transition block leading-snug break-words">
                                {{ $job['title'] }}
                            </a>
                            <div class="flex items-center gap-2 mt-1 text-[11.5px] text-slate-500 flex-wrap">
                                <span class="font-semibold text-slate-700">{{ $job['company'] }}</span>
                                <span>•</span>
                                <span>ID: {{ $job['code'] }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 pt-0.5">
                            @if ($job['status'] === 'Active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @elseif ($job['status'] === 'Paused')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Dijeda</span>
                            @elseif ($job['status'] === 'Draft')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Draf</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Ditutup</span>
                            @endif
                        </div>
                    </div>

                    {{-- Type, Date, Applicants --}}
                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-50">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium text-[11px]">{{ $job['type'] }}</span>
                            <span class="text-[11.5px] text-slate-400">{{ $job['date_posted'] }}</span>
                        </div>
                        <div>
                            @php
                                $appCount = $job->applications_count ?? ($job->applications ? $job->applications->count() : 0);
                            @endphp
                            @if ($appCount > 0)
                                <a href="{{ route('admin.jobs.show', $job['id']) }}#daftar-pelamar" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                    <span>{{ $appCount }} Pelamar</span>
                                </a>
                            @else
                                <span class="text-[11.5px] text-slate-400 font-medium">0 Pelamar</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Bar --}}
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('admin.jobs.show', $job['id']) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <span>Detail</span>
                        </a>
                        <a href="{{ route('admin.jobs.edit', $job['id']) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50/50 text-xs font-semibold text-blue-600 hover:bg-blue-100/60 transition">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            <span>Edit</span>
                        </a>
                        <button type="button" 
                                onclick="openDeleteModal('{{ $job['id'] }}', '{{ addslashes($job['title']) }}', '{{ addslashes($job['company']) }}')"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50/50 text-xs font-semibold text-rose-600 hover:bg-rose-100/60 transition cursor-pointer">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    Tidak ada data lowongan pekerjaan yang ditemukan.
                </div>
            @endforelse
        </div>

        {{-- Footer Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-[13px] text-slate-500 text-center sm:text-left">
            <div>
                Menampilkan <span class="font-semibold text-slate-800">{{ $jobs->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-slate-800">{{ $jobs->lastItem() ?? 0 }}</span> dari total <span class="font-semibold text-slate-800">{{ $jobs->total() }}</span> lowongan
            </div>
            <div class="flex justify-center w-full sm:w-auto">
                {{ $jobs->links('pagination::simple-tailwind') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL POP-UP: FILTER JOBS --}}
<div id="modal-filter-jobs" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl space-y-4 border border-slate-100" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h3 class="text-sm font-bold text-slate-900">Filter Data Lowongan</h3>
            </div>
            <button type="button" onclick="closeFilterModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('admin.jobs.index') }}" method="GET" class="space-y-4 text-xs font-medium">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif

            <div>
                <label class="block text-slate-700 mb-1.5 font-bold">Status Lowongan</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:border-blue-600 cursor-pointer">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Paused" {{ request('status') === 'Paused' ? 'selected' : '' }}>Dijeda</option>
                    <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draf</option>
                    <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1.5 font-bold">Tipe Pekerjaan</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:border-blue-600 cursor-pointer">
                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="Full-time" {{ request('type') === 'Full-time' ? 'selected' : '' }}>Purna Waktu (Full-time)</option>
                    <option value="Part-time" {{ request('type') === 'Part-time' ? 'selected' : '' }}>Paruh Waktu (Part-time)</option>
                    <option value="Internship" {{ request('type') === 'Internship' ? 'selected' : '' }}>Magang / PKL (Internship)</option>
                    <option value="Contract" {{ request('type') === 'Contract' ? 'selected' : '' }}>Kontrak</option>
                    <option value="Remote" {{ request('type') === 'Remote' ? 'selected' : '' }}>Remote</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-700 mb-1.5 font-bold">Departemen</label>
                <select name="department" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 outline-none focus:border-blue-600 cursor-pointer">
                    <option value="all" {{ request('department') === 'all' ? 'selected' : '' }}>Semua Departemen</option>
                    <option value="Engineering" {{ request('department') === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                    <option value="Design" {{ request('department') === 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Data" {{ request('department') === 'Data' ? 'selected' : '' }}>Data</option>
                    <option value="Product" {{ request('department') === 'Product' ? 'selected' : '' }}>Product</option>
                    <option value="Marketing" {{ request('department') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="Sales" {{ request('department') === 'Sales' ? 'selected' : '' }}>Sales</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.jobs.index', ['tab' => $currentTab]) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition cursor-pointer">Reset</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-blue-700 text-white font-semibold shadow-xs transition cursor-pointer">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openFilterModal() {
        const modal = document.getElementById('modal-filter-jobs');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeFilterModal() {
        const modal = document.getElementById('modal-filter-jobs');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openDeleteModal(id, title, company) {
        let label = company ? `${title} (${company})` : title;
        window.confirmDelete(`{{ url('/admin/jobs') }}/${id}`, label, {
            title: 'Konfirmasi Hapus Lowongan',
            warning: 'Peringatan: Tindakan ini permanen dan data lowongan serta lamaran terkait akan dihapus.'
        });
    }

    // Backdrop click to close filter
    document.getElementById('modal-filter-jobs')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFilterModal();
        }
    });

    // Escape key to close filter
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilterModal();
        }
    });
</script>
@endpush
@endsection
