@extends('admin.layouts.app')

@section('title', 'Internship Management — IndoTech Admin')

@section('content')
<div class="space-y-6">

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

    {{-- Success Banner --}}
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm flex items-start gap-3 animate-fade-in">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-emerald-500 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">{{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
            {{-- Tambah Internship Button --}}
            <a href="{{ route('admin.internships.create') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-[42px] px-5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white text-[13px] sm:text-[13.5px] font-semibold shadow-xs transition transform active:scale-95 whitespace-nowrap">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="shrink-0">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>Tambah Internship</span>
            </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-5">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-3">
            <div>
                <div class="text-[11px] sm:text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">TOTAL INTERNSHIPS</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalPostings) }}</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4m0 2v4m0-11v2m0 0h2m-2 0h-2M9 11h2m-2 0h-2m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-3">
            <div>
                <div class="text-[11px] sm:text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">ACTIVE</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($activeInternships) }}</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-3">
            <div>
                <div class="text-[11px] sm:text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">INACTIVE</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($inactiveInternships) }}</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shrink-0">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Table Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50">
            <form action="{{ route('admin.internships.index') }}" method="GET" class="flex flex-1 items-center gap-2">
                <input type="text" name="search" placeholder="Cari berdasarkan judul, perusahaan, atau lokasi..." value="{{ $search }}" class="flex-1 min-w-0 bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs placeholder-slate-400 focus:border-blue-500 focus:outline-none">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shrink-0 cursor-pointer">Cari</button>
            </form>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse ($internships as $internship)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-bold text-[14px] text-slate-900 leading-snug">
                                {{ $internship->title }}
                            </div>
                            <div class="text-[12px] text-slate-500 mt-0.5">
                                {{ $internship->company }} · {{ $internship->user->name ?? '-' }}
                            </div>
                            @if($internship->location)
                                <div class="text-[11.5px] text-slate-400 mt-0.5">{{ $internship->location }}</div>
                            @endif
                        </div>

                        @if ($internship->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-medium text-[11.5px] shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @elseif ($internship->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-medium text-[11.5px] shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Completed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-medium text-[11.5px] shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="button"
                                onclick="openEditModal({{ json_encode($internship) }})"
                                class="flex-1 inline-flex items-center justify-center min-h-[38px] px-3 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-[12.5px] font-semibold transition cursor-pointer">
                            Edit
                        </button>
                        <button type="button"
                                onclick="openDeleteModal({{ $internship->id }}, '{{ addslashes($internship->title) }}')"
                                class="inline-flex items-center justify-center min-h-[38px] min-w-[38px] px-2.5 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 transition cursor-pointer"
                                title="Hapus Internship">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-slate-400">
                    <div class="max-w-sm mx-auto space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <div class="font-bold text-slate-800 text-sm">Tidak ada internship ditemukan</div>
                        <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau tambahkan internship baru.</p>
                        <a href="{{ route('admin.internships.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Search</a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Table (Desktop/Tablet) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs sm:text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Judul</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Perusahaan</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">User</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Lokasi</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Status</th>
                        <th class="px-6 py-3 text-center font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($internships as $internship)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $internship->title }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $internship->company }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $internship->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $internship->location ?? '-' }}</td>
                            <td class="cell">
                                @if ($internship->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-medium text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @elseif ($internship->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-medium text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-medium text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <button type="button" 
                                            onclick="openEditModal({{ json_encode($internship) }})" 
                                            class="p-2 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" 
                                            title="Edit Internship">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button type="button" 
                                            onclick="openDeleteModal({{ $internship->id }}, '{{ addslashes($internship->title) }}')" 
                                            class="p-2 rounded-xl text-slate-500 hover:text-red-600 hover:bg-red-50 transition" 
                                            title="Hapus Internship">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                    </div>
                                    <div class="font-bold text-slate-800 text-sm">Tidak ada internship ditemukan</div>
                                    <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau tambahkan internship baru.</p>
                                    <a href="{{ route('admin.internships.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Search</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer & Pagination --}}
        <div class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-[13px] text-slate-500 text-center sm:text-left">
                Showing <span class="font-semibold text-slate-800">{{ $internships->firstItem() ?? 0 }}-{{ $internships->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-800">{{ $internships->total() }}</span> internships
            </div>

            <div class="flex flex-wrap items-center justify-center gap-1.5 text-[13.5px]">
                @if ($internships->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">‹</span>
                @else
                    <a href="{{ $internships->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">‹</a>
                @endif

                @for ($page = 1; $page <= max(1, $internships->lastPage()); $page++)
                    @if ($page == $internships->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-600 text-white font-bold shadow-xs select-none">{{ $page }}</span>
                    @else
                        <a href="{{ $internships->url($page) }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">{{ $page }}</a>
                    @endif
                @endfor

                @if ($internships->hasMorePages())
                    <a href="{{ $internships->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">›</a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">›</span>
                @endif
            </div>
        </div>

    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP: EDIT DATA INTERNSHIP --}}
{{-- ========================================================================= --}}
<div id="modal-edit-internship" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        {{-- Modal Header --}}
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold">Edit Data Internship</h3>
                    <p class="text-xs text-slate-400">Perbarui informasi internship berikut ini.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 text-xl font-bold transition">&times;</button>
        </div>

        {{-- Modal Body Form --}}
        <form id="form-edit-internship" method="POST" enctype="multipart/form-data" data-async data-async-reload class="p-4 sm:p-6 space-y-4 text-xs sm:text-sm font-medium text-slate-700 max-h-[75vh] overflow-y-auto">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label-strong">User <span class="text-red-500">*</span></label>
                <select id="edit_user_id" name="user_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Pilih User</option>
                    @foreach (\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-strong">Judul <span class="text-red-500">*</span></label>
                <input type="text" id="edit_title" name="title" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
            </div>

            <div>
                <label class="form-label-strong">Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_company" name="company" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
            </div>

            <div>
                <label class="form-label-strong">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label-strong">Lokasi</label>
                    <input type="text" id="edit_location" name="location" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
                </div>

                <div>
                    <label class="form-label-strong">Status <span class="text-red-500">*</span></label>
                    <select id="edit_status" name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label-strong">Tanggal Mulai</label>
                    <input type="date" id="edit_start_date" name="start_date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
                </div>

                <div>
                    <label class="form-label-strong">Tanggal Selesai</label>
                    <input type="date" id="edit_end_date" name="end_date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label class="form-label-strong">Foto Profil <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="file" id="edit_profile_picture" name="profile_picture" accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 outline-none focus:border-blue-600 focus:bg-white transition text-xs">
                <p class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, GIF (Max 2MB). Biarkan kosong untuk tidak mengubah.</p>
                <div id="current_profile" class="mt-2"></div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5 sm:gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold transition cursor-pointer">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold shadow-md transition cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(internship) {
        const form = document.getElementById('form-edit-internship');
        form.action = `{{ url('/admin/internships') }}/${internship.id}`;
        
        document.getElementById('edit_user_id').value = internship.user_id || '';
        document.getElementById('edit_title').value = internship.title || '';
        document.getElementById('edit_company').value = internship.company || '';
        document.getElementById('edit_description').value = internship.description || '';
        document.getElementById('edit_location').value = internship.location || '';
        document.getElementById('edit_status').value = internship.status || 'active';
        document.getElementById('edit_start_date').value = internship.start_date || '';
        document.getElementById('edit_end_date').value = internship.end_date || '';

        // Show current profile picture if exists
        let profileHTML = '';
        if (internship.profile_picture) {
            profileHTML = `<div class="text-left"><p class="text-xs font-semibold text-slate-600 mb-1">Foto Profil Saat Ini:</p><img src="/storage/${internship.profile_picture}" alt="Profile" class="w-20 h-20 rounded-lg object-cover border border-slate-200"></div>`;
        }
        document.getElementById('current_profile').innerHTML = profileHTML;

        document.getElementById('modal-edit-internship').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit-internship').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openDeleteModal(id, title) {
        window.confirmDelete(`{{ url('/admin/internships') }}/${id}`, title, {
            title: 'Konfirmasi Hapus Internship',
            warning: 'Peringatan: Tindakan ini permanen dan data internship tidak dapat dikembalikan.'
        });
    }

    function closeDeleteModal() {
        window.closeGlobalDeleteModal();
    }
</script>
@endpush
@endsection
