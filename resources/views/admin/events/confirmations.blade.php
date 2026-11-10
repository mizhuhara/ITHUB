@extends('admin.layouts.app')

@section('title', 'Konfirmasi Pembayaran Event — IndoTech Admin')

@section('content')
<div class="space-y-6" x-data="{
    proofModalOpen: false,
    proofUrl: '',
    proofTitle: '',
    rejectModalOpen: false,
    rejectAction: '',
    participantName: '',
    openProof(url, title) {
        this.proofUrl = url;
        this.proofTitle = title;
        this.proofModalOpen = true;
    },
    openReject(action, name) {
        this.rejectAction = action;
        this.participantName = name;
        this.rejectModalOpen = true;
    }
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Konfirmasi Pembayaran Event</span>
                @if($totalPending > 0)
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold bg-amber-100 text-amber-700 rounded-full border border-amber-200">
                        {{ $totalPending }} Menunggu
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Verifikasi bukti transfer pembayaran dari peserta event berbayar dan setujui tiket masuk.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.finance') }}" class="inline-flex items-center gap-1.5 h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Penghasilan Event
            </a>
            <a href="{{ route('admin.events.bank-account') }}" class="inline-flex items-center gap-1.5 h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Kelola Rekening
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.events.confirmations', ['status' => 'pending', 'event_id' => $selectedEventId]) }}"
           class="p-4 rounded-2xl border transition {{ $selectedStatus === 'pending' ? 'bg-amber-50/80 border-amber-300 ring-2 ring-amber-400/20' : 'bg-white border-slate-200 hover:border-amber-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu Konfirmasi</span>
                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $totalPending }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Peserta yang telah upload bukti transfer</p>
        </a>

        <a href="{{ route('admin.events.confirmations', ['status' => 'confirmed', 'event_id' => $selectedEventId]) }}"
           class="p-4 rounded-2xl border transition {{ $selectedStatus === 'confirmed' ? 'bg-emerald-50/80 border-emerald-300 ring-2 ring-emerald-400/20' : 'bg-white border-slate-200 hover:border-emerald-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Dikonfirmasi (Aktif)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $totalConfirmed }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Tiket peserta telah aktif & terverifikasi</p>
        </a>

        <a href="{{ route('admin.events.confirmations', ['status' => 'rejected', 'event_id' => $selectedEventId]) }}"
           class="p-4 rounded-2xl border transition {{ $selectedStatus === 'rejected' ? 'bg-red-50/80 border-red-300 ring-2 ring-red-400/20' : 'bg-white border-slate-200 hover:border-red-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-red-700 uppercase tracking-wider">Ditolak</span>
                <span class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $totalRejected }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Pembayaran tidak valid / ditolak</p>
        </a>
    </div>

    {{-- Filter Toolbar --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.confirmations') }}" class="flex flex-col md:flex-row items-center gap-3">
            <input type="hidden" name="status" value="{{ $selectedStatus }}">

            {{-- Event Selector --}}
            <div class="w-full md:w-72">
                <select name="event_id" onchange="this.form.submit()"
                        class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                    <option value="all">Semua Event Anda</option>
                    @foreach($myEvents as $ev)
                        <option value="{{ $ev->id }}" {{ (string)$selectedEventId === (string)$ev->id ? 'selected' : '' }}>
                            {{ $ev->title }} ({{ $ev->formatted_price }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Tabs --}}
            <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                <a href="{{ route('admin.events.confirmations', ['status' => 'all', 'event_id' => $selectedEventId, 'q' => $searchQuery]) }}"
                   class="px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $selectedStatus === 'all' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </a>
                <a href="{{ route('admin.events.confirmations', ['status' => 'pending', 'event_id' => $selectedEventId, 'q' => $searchQuery]) }}"
                   class="px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $selectedStatus === 'pending' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Menunggu Konfirmasi
                </a>
                <a href="{{ route('admin.events.confirmations', ['status' => 'confirmed', 'event_id' => $selectedEventId, 'q' => $searchQuery]) }}"
                   class="px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $selectedStatus === 'confirmed' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Dikonfirmasi
                </a>
                <a href="{{ route('admin.events.confirmations', ['status' => 'rejected', 'event_id' => $selectedEventId, 'q' => $searchQuery]) }}"
                   class="px-3 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $selectedStatus === 'rejected' ? 'bg-red-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Ditolak
                </a>
            </div>

            {{-- Search Input --}}
            <div class="relative w-full md:flex-1 md:ml-auto">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $searchQuery }}"
                       placeholder="Cari nama, email, hp, atau kode tiket..."
                       class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
            </div>

            <button type="submit" class="h-10 px-4 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                Cari
            </button>
            @if(!empty($searchQuery) || $selectedStatus !== 'all' || $selectedEventId !== 'all')
                <a href="{{ route('admin.events.confirmations') }}" class="h-10 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-100 flex items-center justify-center transition" title="Reset filter">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Registrations Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Peserta</th>
                        <th class="py-3.5 px-4">Event</th>
                        <th class="py-3.5 px-4">Nominal</th>
                        <th class="py-3.5 px-4">Bukti Transfer</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-slate-50/50 transition">
                            {{-- Peserta --}}
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $reg->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $reg->email }}</div>
                                @if($reg->phone)
                                    <div class="text-[11px] text-slate-500">{{ $reg->phone }}</div>
                                @endif
                                <div class="mt-1 font-mono text-[10.5px] text-blue-600 font-bold bg-blue-50 inline-block px-1.5 py-0.5 rounded border border-blue-200">
                                    {{ $reg->ticket_code }}
                                </div>
                            </td>

                            {{-- Event --}}
                            <td class="py-3.5 px-4 max-w-[220px]">
                                <a href="{{ route('event.show', $reg->event_id) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-2">
                                    {{ $reg->event?->title }}
                                </a>
                                <span class="text-[11px] text-slate-500">{{ $reg->created_at->format('d M Y, H:i') }}</span>
                            </td>

                            {{-- Nominal --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($reg->amount > 0)
                                    <span class="font-extrabold text-slate-900 text-sm">
                                        Rp {{ number_format($reg->amount, 0, ',', '.') }}
                                    </span>
                                    @if($reg->sender_bank || $reg->sender_name)
                                        <div class="text-[10.5px] text-slate-500 mt-0.5">
                                            {{ $reg->sender_bank ?? 'Bank' }} a.n {{ $reg->sender_name ?? '-' }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold">
                                        Gratis
                                    </span>
                                @endif
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="py-3.5 px-4">
                                @if($reg->payment_proof)
                                    <button type="button"
                                            @click="openProof('{{ $reg->payment_proof }}', 'Bukti Transfer: {{ addslashes($reg->name) }} (Rp {{ number_format($reg->amount, 0, ',', '.') }})')"
                                            class="group inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold transition">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tidak ada (Event Gratis)</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($reg->payment_status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Konfirmasi
                                    </span>
                                @elseif(in_array($reg->payment_status, ['confirmed', 'free']))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Dikonfirmasi
                                    </span>
                                    @if($reg->checked_in_at)
                                        <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Sudah Check-in</div>
                                    @endif
                                @elseif($reg->payment_status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        Ditolak
                                    </span>
                                    @if($reg->rejection_reason)
                                        <div class="text-[10px] text-red-600 mt-0.5 max-w-[160px] truncate" title="{{ $reg->rejection_reason }}">
                                            {{ $reg->rejection_reason }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($reg->payment_status === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Tombol Konfirmasi --}}
                                        <form method="POST" action="{{ route('admin.events.registrations.confirm', $reg->id) }}" onsubmit="return confirm('Konfirmasi pembayaran peserta {{ addslashes($reg->name) }}?')">
                                            @csrf
                                            <button type="submit" class="h-8 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-1">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Konfirmasi
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak --}}
                                        <button type="button"
                                                @click="openReject('{{ route('admin.events.registrations.reject', $reg->id) }}', '{{ addslashes($reg->name) }}')"
                                                class="h-8 px-2.5 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-600 text-xs font-bold transition">
                                            Tolak
                                        </button>
                                    </div>
                                @elseif(in_array($reg->payment_status, ['confirmed', 'free']))
                                    <form method="POST" action="{{ route('admin.events.registrations.check-in', [$reg->event_id, $reg->id]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="h-8 px-3 rounded-lg border text-xs font-semibold transition {{ $reg->checked_in_at ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100' }}">
                                            {{ $reg->checked_in_at ? 'Batalkan Check-in' : 'Check-in Peserta' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="font-bold text-slate-600 text-sm">Belum ada data pendaftaran</p>
                                <p class="text-xs text-slate-400 mt-0.5">Pendaftaran peserta event berbayar akan muncul di sini untuk dikonfirmasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL PREVIEW BUKTI TRANSFER --}}
    <div x-show="proofModalOpen" style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200"
             @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 text-sm" x-text="proofTitle">Bukti Transfer</h3>
                <button type="button" @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 flex items-center justify-center bg-slate-950/5 min-h-[300px]">
                <template x-if="proofUrl.endsWith('.pdf')">
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-red-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p class="font-bold text-slate-800 text-sm mb-2">Dokumen Bukti PDF</p>
                        <a :href="proofUrl" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                            Buka / Unduh File PDF
                        </a>
                    </div>
                </template>
                <template x-if="!proofUrl.endsWith('.pdf')">
                    <img :src="proofUrl" alt="Bukti Transfer" class="max-h-[70vh] max-w-full rounded-xl object-contain shadow-sm">
                </template>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <a :href="proofUrl" target="_blank" class="text-xs font-bold text-blue-600 hover:underline">
                    Buka Ukuran Penuh &rarr;
                </a>
                <button type="button" @click="proofModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK PEMBAYARAN --}}
    <div x-show="rejectModalOpen" style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200"
             @click.away="rejectModalOpen = false">
            <form method="POST" :action="rejectAction">
                @csrf
                <div class="p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Tolak Pembayaran Peserta</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Peserta: <strong class="text-slate-700" x-text="participantName"></strong></p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-3">
                    <label class="block text-xs font-bold text-slate-700">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" required rows="3"
                              placeholder="Contoh: Bukti transfer buram/tidak terbaca, nominal transfer tidak sesuai tiket, atau dana belum masuk rekening..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none"></textarea>
                    <p class="text-[11px] text-slate-500">
                        Alasan ini akan ditampilkan kepada peserta di halaman event agar mereka dapat mengunggah bukti yang benar.
                    </p>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition shadow-sm">
                        Ya, Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

