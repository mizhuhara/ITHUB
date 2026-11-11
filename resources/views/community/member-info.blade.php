@extends('layouts.app')

@section('title', 'Info Anggota: ' . $member->name . ' — ' . $community->name)

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumb / Back Link --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('community.chat', $community) }}"
               class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Chat Komunitas
            </a>

            <a href="{{ route('community.show', $community) }}"
               class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                <span>Detail Komunitas</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-semibold text-emerald-800 flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm font-semibold text-rose-800 flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Main Member Profile Card --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            
            {{-- Header Cover Banner (matches community show & manage page cover sizing) --}}
            <div class="relative h-44 sm:h-60 bg-slate-100 overflow-hidden">
                @if ($community->cover_url)
                    <img src="{{ $community->cover_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500"></div>
                @endif
                
                {{-- Community badge top right --}}
                <div class="absolute top-4 right-4 z-10">
                    <a href="{{ route('community.show', $community) }}" 
                       class="inline-flex items-center gap-2 bg-slate-950/60 hover:bg-slate-950/80 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-full border border-white/20 transition shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>{{ $community->name }}</span>
                    </a>
                </div>
            </div>

            {{-- Profile Details Area --}}
            <div class="px-6 sm:px-8 pb-8 pt-0">
                
                {{-- Floating Avatar & Action Button Row --}}
                <div class="relative flex items-end justify-between gap-4 -mt-12 sm:-mt-14 mb-4">
                    {{-- Avatar Wrap --}}
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white p-1.5 shadow-xl ring-4 ring-white border border-slate-200 shrink-0 overflow-hidden">
                        @if ($member->avatar_url)
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <div class="w-full h-full rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-3xl font-extrabold uppercase shadow-inner">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Actions Button on Header (aligned neatly on right side) --}}
                    <div class="flex items-center gap-2 pb-1">
                        <a href="{{ route('community.chat', $community) }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Buka Chat Komunitas</span>
                        </a>
                    </div>
                </div>

                {{-- Member Name and Badges (placed cleanly in the white body area below avatar) --}}
                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ $member->name }}
                        </h1>
                        @if ($isSelf)
                            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                Anda
                            </span>
                        @endif

                        {{-- Role badge --}}
                        @if ($role === 'admin' && $isOwner)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-600 fill-amber-500" viewBox="0 0 24 24">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                Pemilik Komunitas
                            </span>
                        @elseif ($role === 'admin')
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Admin
                            </span>
                        @elseif ($role === 'moderator')
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                                Moderator
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Anggota
                            </span>
                        @endif

                        {{-- Banned status --}}
                        @if ($isBanned)
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                </svg>
                                Diblokir
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- PRIMARY INFORMATION: Join Date & Member Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    
                    {{-- Highlight Box: Tanggal Bergabung Komunitas --}}
                    <div class="bg-gradient-to-br from-blue-50/80 to-indigo-50/60 border border-blue-200/70 rounded-2xl p-5 shadow-xs flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-1">
                                Bergabung dengan Komunitas
                            </p>
                            <p class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                                {{ $joinedAt ? $joinedAt->translatedFormat('d F Y') : '-' }}
                            </p>
                            @if ($joinedAt)
                                <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-600 font-medium">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        Pukul {{ $joinedAt->format('H:i') }} WIB
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-blue-700 font-semibold bg-blue-100/70 px-2 py-0.5 rounded-md">
                                        {{ $joinedAt->diffForHumans() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Highlight Box: Komunitas yang Diikuti --}}
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                            @if ($community->logo_url)
                                <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Komunitas
                            </p>
                            <a href="{{ route('community.show', $community) }}" class="text-base sm:text-lg font-extrabold text-slate-900 hover:text-blue-600 transition truncate block">
                                {{ $community->name }}
                            </a>
                            <p class="text-xs text-slate-500 mt-1">
                                Kategori: <span class="font-semibold text-slate-700">{{ $community->category }}</span>
                                <span class="mx-1">•</span>
                                {{ number_format($community->members) }} Anggota
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Detailed Info List Table --}}
                <div class="mt-6 border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs">
                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rincian Informasi Anggota
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-100">
                        {{-- Nama Lengkap --}}
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                            <span class="text-slate-500 font-medium">Nama Anggota</span>
                            <strong class="text-slate-900 font-bold">{{ $member->name }}</strong>
                        </div>

                        {{-- Peran di Komunitas --}}
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                            <span class="text-slate-500 font-medium">Peran di Komunitas</span>
                            <span class="font-semibold text-slate-900">{{ $roleLabel }}</span>
                        </div>

                        {{-- Tanggal Bergabung Komunitas --}}
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm bg-blue-50/30">
                            <span class="text-slate-700 font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Kapan Join Komunitas
                            </span>
                            <div class="text-slate-900 font-bold text-left sm:text-right">
                                <span>{{ $joinedAt ? $joinedAt->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                                @if ($joinedAt)
                                    <span class="block text-xs font-medium text-blue-600">({{ $joinedAt->diffForHumans() }})</span>
                                @endif
                            </div>
                        </div>

                        {{-- Email --}}
                        @if ($member->email)
                            <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                                <span class="text-slate-500 font-medium">Email</span>
                                <span class="text-slate-800 font-medium">{{ $member->email }}</span>
                            </div>
                        @endif

                        {{-- Akun IndoTech Terdaftar --}}
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                            <span class="text-slate-500 font-medium">Akun Terdaftar di IndoTech</span>
                            <span class="text-slate-700">{{ $member->created_at?->translatedFormat('d F Y') ?: '-' }}</span>
                        </div>

                        {{-- Aktivitas Komunitas --}}
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                            <span class="text-slate-500 font-medium">Aktivitas di Komunitas Ini</span>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    {{ $messagesCount }} Pesan Chat
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                    {{ $postsCount }} Postingan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Moderation / Management Actions (if permitted) --}}
                @if ($canBan || $canKick || ($isSelf && ! $isOwner))
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                            Aksi Keanggotaan
                        </h4>

                        <div class="flex flex-wrap items-center gap-3">
                            {{-- Toggle Ban Button --}}
                            @if ($canBan)
                                <form method="POST" action="{{ route('community.manage.ban', [$community, $member]) }}"
                                      onsubmit="return confirm('{{ $isBanned ? 'Yakin ingin mencabut blokir anggota ini?' : 'Yakin ingin memblokir anggota ini dari grup?' }}');">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ $isBanned ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                        </svg>
                                        <span>{{ $isBanned ? 'Buka Blokir Anggota' : 'Blokir dari Komunitas' }}</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Kick Button --}}
                            @if ($canKick)
                                <form method="POST" action="{{ route('community.manage.kick', [$community, $member]) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $member->name }} dari komunitas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-700 border border-slate-200 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Keluarkan dari Komunitas</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Leave Button for oneself --}}
                            @if ($isSelf && ! $isOwner)
                                <form method="POST" action="{{ route('community.leave', $community) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin keluar dari komunitas {{ $community->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Keluar dari Komunitas</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection

