@extends('layouts.app')

@section('title', $community->name . ' — Komunitas IndoTech')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div>
            <a href="{{ route('community.index') }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Komunitas
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($community->approval_status === 'pending')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-medium text-amber-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <span class="font-bold">Pratinjau Komunitas:</span> Komunitas ini sedang dalam antrean review oleh Super Admin dan belum dapat diakses oleh publik.
                </div>
            </div>
        @elseif ($community->approval_status === 'rejected')
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-800 space-y-1">
                <div class="flex items-center gap-2 font-bold text-rose-900">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Pengajuan Komunitas Ini Ditolak:</span>
                </div>
                <p class="pl-7 text-rose-700 leading-relaxed">{{ $community->rejection_reason }}</p>
                <div class="pl-7 pt-1">
                    <a href="{{ route('community.manage', $community) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-600 text-white font-bold hover:bg-rose-700 transition">
                        Perbaiki &amp; Ajukan Kembali
                    </a>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            @if ($community->cover_url)
                <div class="h-44 sm:h-60 bg-slate-100 overflow-hidden">
                    <img src="{{ $community->cover_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-extrabold overflow-hidden shrink-0 shadow-sm">
                        @if ($community->logo_url)
                            <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($community->name, 0, 2)) }}
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $community->category }}
                            </span>
                            @if ($community->approval_status === 'pending')
                                <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Persetujuan
                                </span>
                            @elseif ($community->approval_status === 'rejected')
                                <span class="bg-rose-50 text-rose-700 text-xs font-semibold px-3 py-1 rounded-full border border-rose-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                </span>
                            @else
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full border border-slate-200/60 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @endif
                            @if ($community->mode)
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full border border-indigo-100 inline-flex items-center gap-1">
                                    {{ $community->formatted_mode }}
                                </span>
                            @endif
                            @if ($community->type)
                                <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full border border-blue-100 inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        @if($community->type === 'private')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        @endif
                                    </svg>
                                    {{ $community->type === 'private' ? 'Privat' : 'Publik' }}
                                </span>
                            @endif
                            @if ($community->language)
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                    </svg>
                                    {{ $community->language }}
                                </span>
                            @endif
                            @if ($isMember)
                                <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full border border-blue-200/60">
                                    {{ $community->user_id === auth()->id() ? 'Pemilik' : 'Anda anggota' }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                            {{ $community->name }}
                        </h1>

                        <p class="text-sm text-slate-600 leading-relaxed mt-3">
                            {{ $community->description ?: 'Belum ada deskripsi komunitas.' }}
                        </p>

                        @if (! empty($community->tags))
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach ($community->tags as $tag)
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-100">
                    <div>
                        <p class="text-lg font-extrabold text-slate-900">{{ number_format($community->members) }}</p>
                        <p class="text-xs text-slate-500">Anggota</p>
                    </div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-900">{{ $memberUsers->count() }}</p>
                        <p class="text-xs text-slate-500">Anggota platform terbaru</p>
                    </div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-900">{{ $community->created_at?->format('Y') }}</p>
                        <p class="text-xs text-slate-500">Bergabung sejak</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 mt-8">
                    @auth
                        @if (! $isMember && $community->approval_status === 'approved' && $community->status === 'active')
                            <form action="{{ route('community.join', $community) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl text-sm shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span>Gabung Komunitas</span>
                                </button>
                            </form>
                        @endif

                        @if ($community->canAccessChat(auth()->user()))
                            <a href="{{ route('community.chat', $community) }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-blue-600 hover:bg-blue-50/50 hover:border-blue-200 shadow-sm transition"
                               title="Chat Komunitas"
                               aria-label="Chat Komunitas">
                                <svg class="w-4.5 h-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-0.5 text-[11px] font-medium text-white opacity-0 shadow transition group-hover:opacity-100 z-20">Chat Komunitas</span>
                            </a>
                        @endif

                        @if (($canModerate ?? false) || $isOwnerOrAdmin)
                            <a href="{{ route('community.manage', $community) }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition"
                               title="Kelola Komunitas"
                               aria-label="Kelola Komunitas">
                                <svg class="w-4.5 h-4.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-0.5 text-[11px] font-medium text-white opacity-0 shadow transition group-hover:opacity-100 z-20">Kelola Komunitas</span>
                            </a>
                        @endif

                        @if ($isMember && $community->user_id !== auth()->id())
                            <form id="form-leave-community" action="{{ route('community.leave', $community) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                    onclick="openLeaveModal()"
                                    class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-red-600 hover:text-red-700 hover:bg-red-50/60 hover:border-red-200 shadow-sm transition"
                                    title="Keluar dari Komunitas"
                                    aria-label="Keluar dari Komunitas">
                                <svg class="w-4.5 h-4.5 text-red-600 group-hover:text-red-700 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-0.5 text-[11px] font-medium text-white opacity-0 shadow transition group-hover:opacity-100 z-20">Keluar dari Komunitas</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl text-sm shadow-sm transition">
                            Masuk untuk Bergabung
                        </a>
                    @endauth

                    @if ($community->website)
                        <a href="{{ $community->website }}" target="_blank" rel="noopener noreferrer"
                           class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition"
                           title="Kunjungi Website"
                           aria-label="Kunjungi Website">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-0.5 text-[11px] font-medium text-white opacity-0 shadow transition group-hover:opacity-100 z-20">Kunjungi Website</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ruang Chat Komunitas Banner --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 transition">
            <div class="space-y-2.5">

                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Ruang Chat Komunitas</h2>
                <p class="text-slate-600 text-sm max-w-xl leading-relaxed">
                    Terhubung langsung dengan anggota lain secara real-time. Bagikan teks, emoji, foto, video, dokumen, hingga satu folder penuh sekaligus!
                </p>
            </div>
            <div class="shrink-0">
                @auth
                    @if ($community->canAccessChat(auth()->user()))
                        <a href="{{ route('community.chat', $community) }}" class="inline-flex items-center justify-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-semibold px-5 py-2.5 rounded-xl text-sm border border-slate-200 shadow-sm transition">
                            <svg class="w-4.5 h-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Buka Chat Komunitas</span>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-500 font-medium text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                            Gabung komunitas untuk membuka chat
                        </span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-semibold px-5 py-2.5 rounded-xl text-sm border border-slate-200 shadow-sm transition">
                        <svg class="w-4.5 h-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Login untuk Masuk Chat</span>
                    </a>
                @endauth
            </div>
        </div>

        @if ($community->rules)
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-lg mb-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <h2>Aturan Komunitas</h2>
                </div>
                <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                    {{ $community->rules }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Anggota Platform</h2>
            <p class="text-sm text-slate-500 mt-1">Talenta IndoTech yang sudah bergabung di komunitas ini.</p>

            @if ($memberUsers->isEmpty())
                <p class="text-sm text-slate-500 mt-6">Belum ada anggota platform. Jadilah yang pertama bergabung.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-6">
                    @foreach ($memberUsers as $member)
                        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $member->name }}
                                    @if (auth()->id() === $member->id)
                                        <span class="text-xs font-normal text-blue-600">(Anda)</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 truncate">{{ ucfirst($member->role) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@if ($isMember)
{{-- ===== MODAL: KONFIRMASI KELUAR KOMUNITAS ===== --}}
<div id="modal-confirm-leave" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modal-leave-title">
    {{-- Backdrop blur --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeLeaveModal()"></div>

    {{-- Dialog Box --}}
    <div class="relative bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 z-10 animate-in fade-in zoom-in-95 duration-150">
        {{-- Close 'X' Button --}}
        <button type="button"
                onclick="closeLeaveModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition"
                aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        {{-- Exit Red Icon --}}
        <div class="w-14 h-14 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-100 shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </div>

        {{-- Title & Description --}}
        <h3 id="modal-leave-title" class="text-lg font-extrabold text-slate-900 mb-2">Keluar dari Komunitas?</h3>
        <p class="text-sm text-slate-600 mb-4 leading-relaxed">
            Apakah Anda yakin ingin keluar dari komunitas <strong class="text-slate-900">{{ $community->name }}</strong>?
        </p>

        {{-- Subtle warning box --}}
        <div class="bg-red-50/60 border border-red-100 rounded-xl p-3 text-xs text-red-700 text-left mb-6 flex items-start gap-2.5 leading-relaxed">
            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Anda akan kehilangan akses ke ruang chat dan berkas privat komunitas. Anda dapat bergabung kembali kapan saja sesuai aturan komunitas.</span>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            <button type="button"
                    onclick="closeLeaveModal()"
                    class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">
                Batal
            </button>
            <button type="button"
                    onclick="document.getElementById('form-leave-community').submit()"
                    class="flex-1 py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold text-sm shadow-sm transition">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

<script>
function openLeaveModal() {
    const modal = document.getElementById('modal-confirm-leave');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}
function closeLeaveModal() {
    const modal = document.getElementById('modal-confirm-leave');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLeaveModal();
    }
});
</script>
@endif
@endsection
