@extends('admin.layouts.app')

@section('title', 'Penghasilan & Keuangan Event — IndoTech Admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Penghasilan & Keuangan Event
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pantau seluruh omzet tiket, dana terkonfirmasi, serta riwayat transaksi event Anda secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap">
            <a href="{{ route('admin.events.confirmations') }}"
               class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition whitespace-nowrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Konfirmasi Peserta</span>
            </a>
            <a href="{{ route('admin.events.bank-account') }}"
               class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition whitespace-nowrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Kelola Rekening</span>
            </a>
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Terkonfirmasi --}}
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-sm shadow-emerald-500/20">
            <div class="flex items-center justify-between opacity-90">
                <span class="text-xs font-bold uppercase tracking-wider">Total Pendapatan</span>
                <span class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black mt-3">
                Rp {{ number_format($totalEarned, 0, ',', '.') }}
            </p>
            <p class="text-[11px] opacity-80 mt-1">Dana dari pembayaran tiket yang terkonfirmasi</p>
        </div>

        {{-- Menunggu Verifikasi --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu Konfirmasi</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">
                Rp {{ number_format($totalPendingRevenue, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Potensi dana dalam verifikasi bukti transfer</p>
        </div>

        {{-- Tiket Terjual --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Tiket Berbayar Terjual</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">
                {{ $totalPaidTicketsSold }} <span class="text-sm font-semibold text-slate-400">Tiket</span>
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Peserta yang telah melunasi tiket berbayar</p>
        </div>

        {{-- Peserta Gratis --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Peserta Event Gratis</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-3">
                {{ $totalFreeParticipants }} <span class="text-sm font-semibold text-slate-400">Orang</span>
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Total peserta terdaftar di event gratis</p>
        </div>
    </div>

    {{-- Bank Account Banner Alert --}}
    @if($bankAccount)
        <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-start sm:items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 font-extrabold text-sm shadow-2xs">
                    {{ strtoupper(substr($bankAccount->bank_name, 0, 3)) }}
                </span>
                <div>
                    <h4 class="font-extrabold text-slate-900 text-sm">
                        Rekening Penerima Aktif: {{ $bankAccount->bank_name }} - {{ $bankAccount->account_number }}
                    </h4>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Atas Nama: <strong class="text-slate-900">{{ $bankAccount->account_holder }}</strong> &bull; Rekening ini otomatis ditampilkan di seluruh halaman event berbayar Anda.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.events.bank-account') }}" class="w-full sm:w-auto text-center justify-center inline-flex px-3.5 py-2 rounded-xl border border-blue-300 bg-white hover:bg-blue-50 text-blue-700 text-xs font-bold transition whitespace-nowrap shadow-2xs">
                Ubah Rekening &rarr;
            </a>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-start sm:items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
                <div>
                    <h4 class="font-extrabold text-amber-900 text-sm">
                        Rekening Pembayaran Belum Diatur!
                    </h4>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Atur nomor rekening Anda agar peserta event berbayar dapat melakukan transfer dan mengunggah bukti pembayaran.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.events.bank-account') }}" class="w-full sm:w-auto text-center justify-center inline-flex px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition whitespace-nowrap">
                Atur Rekening Sekarang
            </a>
        </div>
    @endif

    {{-- Per-Event Financial Breakdown --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm">Rincian Penghasilan Per Event</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Ringkasan tiket terjual dan total omzet untuk setiap event Anda.</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-blue-600 hover:underline shrink-0">
                Kelola Semua Event &rarr;
            </a>
        </div>

        {{-- Mobile Card List (visible on small screens) --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($events as $evt)
                @php
                    $isPaid = $evt->isPaid();
                    $confirmedCount = $evt->confirmed_registrations_count ?? 0;
                    $pendingCount = $evt->pending_registrations_count ?? 0;
                    $eventTotalRevenue = $isPaid ? ($confirmedCount * $evt->price_numeric) : 0;
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('event.show', $evt->id) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600 text-sm leading-snug line-clamp-2">
                                {{ $evt->title }}
                            </a>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $evt->date }} &bull; {{ $evt->category }}</div>
                        </div>
                        <div class="shrink-0 text-right">
                            @if($isPaid)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Berbayar
                                </span>
                                <div class="font-extrabold text-slate-900 text-xs mt-0.5">
                                    {{ $evt->formatted_price }}
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-700">
                                    Gratis
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Stats Box --}}
                    <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kuota</span>
                            <span class="font-extrabold text-slate-800 text-xs mt-0.5 block">
                                {{ $confirmedCount }}<span class="text-slate-400 font-normal">/{{ $evt->total_quota > 0 ? $evt->total_quota : '∞' }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Menunggu</span>
                            <span class="font-bold text-xs mt-0.5 block {{ $pendingCount > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                {{ $pendingCount > 0 ? $pendingCount . ' Verifikasi' : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pendapatan</span>
                            <span class="font-extrabold text-xs mt-0.5 block {{ $isPaid ? 'text-emerald-600' : 'text-slate-400 font-normal' }}">
                                {{ $isPaid ? 'Rp ' . number_format($eventTotalRevenue, 0, ',', '.') : 'Rp 0' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="flex justify-end pt-0.5">
                        <a href="{{ route('admin.events.confirmations', ['event_id' => $evt->id]) }}"
                           class="w-full text-center justify-center inline-flex items-center gap-1 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                            Lihat Peserta
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 px-4 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Belum ada event</p>
                    <p class="text-xs text-slate-400 mt-0.5">Anda belum memiliki event yang dibuat.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table (hidden on mobile, visible on sm and up) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Event</th>
                        <th class="py-3 px-4">Tipe & Harga</th>
                        <th class="py-3 px-4 text-center">Kuota Terisi</th>
                        <th class="py-3 px-4 text-center">Menunggu</th>
                        <th class="py-3 px-4 text-right">Total Pendapatan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $evt)
                        @php
                            $isPaid = $evt->isPaid();
                            $confirmedCount = $evt->confirmed_registrations_count ?? 0;
                            $pendingCount = $evt->pending_registrations_count ?? 0;
                            $eventTotalRevenue = $isPaid ? ($confirmedCount * $evt->price_numeric) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 max-w-[280px]">
                                <a href="{{ route('event.show', $evt->id) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-1 text-sm">
                                    {{ $evt->title }}
                                </a>
                                <span class="text-[11px] text-slate-500">{{ $evt->date }} &bull; {{ $evt->category }}</span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Berbayar
                                    </span>
                                    <div class="font-extrabold text-slate-900 mt-0.5">
                                        {{ $evt->formatted_price }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-700">
                                        Gratis
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-800 text-sm">{{ $confirmedCount }}</span>
                                <span class="text-slate-400">/ {{ $evt->total_quota > 0 ? $evt->total_quota : '∞' }}</span>
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($pendingCount > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                        {{ $pendingCount }} Verifikasi
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-extrabold text-slate-900 text-sm">
                                @if($isPaid)
                                    <span class="text-emerald-600">Rp {{ number_format($eventTotalRevenue, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400 font-normal">Rp 0 (Gratis)</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.events.confirmations', ['event_id' => $evt->id]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                                    Lihat Peserta
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Anda belum memiliki event yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-sm">Riwayat Pembayaran Tiket Masuk</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Daftar transaksi pembayaran tiket dari peserta event berbayar.</p>
        </div>

        {{-- Mobile Card List (visible on small screens) --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($recentTransactions as $tx)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-bold text-slate-900 text-sm leading-snug">{{ $tx->name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $tx->email }}</div>
                        </div>
                        <div class="shrink-0 text-right">
                            @if($tx->payment_status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800">Menunggu</span>
                            @elseif($tx->payment_status === 'confirmed')
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-100 text-emerald-800">Dikonfirmasi</span>
                            @elseif($tx->payment_status === 'rejected')
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-red-100 text-red-800">Ditolak</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-slate-400 font-medium text-[11px] shrink-0">Event:</span>
                            <span class="font-bold text-slate-800 text-right line-clamp-1">{{ $tx->event?->title }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-400 font-medium text-[11px]">Bank:</span>
                            <span class="text-slate-600 font-medium">
                                {{ $tx->sender_bank ?? 'Transfer Bank' }}
                                @if($tx->sender_name)
                                    <span class="text-slate-400">({{ $tx->sender_name }})</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <span class="text-slate-400 font-medium text-[11px]">Nominal:</span>
                            <span class="font-extrabold text-slate-900 text-xs">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] text-slate-400 pt-0.5">
                            <span>Waktu:</span>
                            <span>{{ $tx->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 px-4 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2 shadow-2xs">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Belum ada transaksi</p>
                    <p class="text-xs text-slate-400 mt-0.5">Belum ada riwayat pembayaran tiket.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table (hidden on mobile, visible on sm and up) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Peserta</th>
                        <th class="py-3 px-4">Event</th>
                        <th class="py-3 px-4">Bank Pengirim</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $tx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $tx->name }}
                                <div class="text-[10.5px] text-slate-400 font-normal">{{ $tx->email }}</div>
                            </td>
                            <td class="py-3 px-4 max-w-[200px] truncate text-slate-800">
                                {{ $tx->event?->title }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                {{ $tx->sender_bank ?? 'Transfer Bank' }}
                                @if($tx->sender_name)
                                    <span class="text-slate-400">({{ $tx->sender_name }})</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-extrabold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($tx->payment_status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800">Menunggu</span>
                                @elseif($tx->payment_status === 'confirmed')
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-100 text-emerald-800">Dikonfirmasi</span>
                                @elseif($tx->payment_status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat pembayaran tiket.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

