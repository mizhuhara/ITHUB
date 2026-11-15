@extends('admin.layouts.app')

@section('title', ($event['title'] ?? 'Event Detail') . ' — Detail Event')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Top Back Bar --}}
    <div>
        <a href="{{ route('admin.events.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-3">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Event</span>
        </a>
    </div>

    {{-- Main Detail Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">

        {{-- Photo --}}
        @if(!empty($event['image']))
            <div class="w-full h-56 sm:h-64 md:h-72 overflow-hidden">
                <img src="{{ $event['image'] }}"
                     alt="{{ $event['title'] ?? 'Event Photo' }}"
                     class="w-full h-full object-cover object-center">
            </div>
        @endif
        
        {{-- Card Header --}}
        <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $event['type'] ?? 'General' }}
                    </span>
                    @if(($event['status'] ?? 'Pending') === 'Pending')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            • Pending
                        </span>
                    @elseif(($event['status'] ?? '') === 'Approved')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            • Approved
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            • Rejected
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight pt-1">
                    {{ $event['title'] ?? 'Event Title' }}
                </h1>
                <p class="text-xs font-semibold text-slate-500">
                    Kategori: {{ $event['category'] ?? 'General Event' }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.events.edit', $event['id']) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    <span>Edit Event</span>
                </a>

                <form action="{{ route('admin.events.complete', $event['id']) }}" method="POST" data-async data-async-reload>
                    @csrf
                    <button type="submit"
                            class="font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition {{ $event->completed_at ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $event->completed_at ? 'Batalkan Selesai' : 'Selesaikan Acara' }}</span>
                    </button>
                </form>

                @if(!$isSchool)
                    <form action="{{ route('admin.events.approve', $event['id']) }}" method="POST" data-async data-async-reload>
                        @csrf
                        <button type="submit" 
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                            <span>Approve Event</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.events.reject', $event['id']) }}" method="POST" data-async data-async-reload>
                        @csrf
                        <button type="submit"
                                class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                            <span>Reject</span>
                        </button>
                    </form>
                @endif

                {{-- Delete Form --}}
                <form action="{{ route('admin.events.destroy', $event['id']) }}" method="POST"
                      onsubmit="return confirm('Hapus event ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-slate-100 hover:bg-rose-50 text-rose-600 border border-slate-200 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.857L5 7m5 4v6m4-6v6M1 7h22"/>
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6 sm:p-8 space-y-8 text-xs text-slate-700">
            
            {{-- Quick Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/80 p-5 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Penyelenggara / Organizer</span>
                    <div class="font-extrabold text-slate-900 text-base">{{ $event['organizer'] ?? '-' }}</div>
                    <div class="text-slate-500 font-semibold mt-0.5">{{ $event['type'] ?? 'Partner' }} Partner</div>
                </div>

                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Pelaksanaan</span>
                    <div class="font-extrabold text-slate-900 text-base">{{ $event['proposed_date'] ?? '-' }}</div>
                </div>

                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Lokasi Event</span>
                    <div class="font-bold text-slate-900 text-sm">{{ $event['location'] ?? 'Lokasi belum ditentukan' }}</div>
                    @if ($event->requiresOnlineAttendance())
                        <a href="{{ $event->meeting_url }}" target="_blank" rel="noopener"
                           class="mt-1 inline-block break-all text-[11px] font-bold text-blue-600 hover:underline">
                            Link Meeting: {{ $event->meeting_url }}
                        </a>
                    @endif
                </div>

                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Target Quota Peserta</span>
                    <div class="font-bold text-slate-900 text-sm">{{ $event['quota'] ?? 'Tanpa Batasan' }}</div>
                </div>

                <div class="md:col-span-2 border-t border-slate-200/60 pt-4 flex flex-wrap gap-6 text-xs text-slate-600">
                    <div>
                        <span class="font-extrabold text-slate-800">Email Kontak:</span> {{ $event['contact_email'] ?? '-' }}
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-800">No. Telepon:</span> {{ $event['contact_phone'] ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Description & Proposal --}}
            <div class="space-y-2">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                    Deskripsi & Proposal Event
                </h3>
                <div class="text-slate-600 text-sm leading-relaxed bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
                    {{ $event['description'] ?? 'Belum ada deskripsi lengkap yang dilampirkan untuk event ini.' }}
                </div>
            </div>

            {{-- Registered Participants --}}
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                        Peserta Terdaftar
                    </h3>
                    <span class="text-xs font-bold text-slate-500">
                        {{ $registrations->count() }} peserta · {{ $registrations->filter(fn ($registration) => $registration->checked_in_at || $registration->joined_at)->count() }} hadir
                    </span>
                </div>

                <form method="GET" action="{{ route('admin.events.show', $event['id']) }}" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Cari nama, email, atau kode tiket"
                           class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none">
                    <button type="submit" class="rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white">
                        Cari
                    </button>
                    @if ($searchQuery !== '')
                        <a href="{{ route('admin.events.show', $event['id']) }}"
                           class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 text-center">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/90">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 font-extrabold">Nama</th>
                                <th class="px-4 py-3 font-extrabold">Email / Telepon</th>
                                <th class="px-4 py-3 font-extrabold">Tiket</th>
                                <th class="px-4 py-3 font-extrabold">Pembayaran</th>
                                <th class="px-4 py-3 font-extrabold">Kehadiran</th>
                                <th class="px-4 py-3 font-extrabold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($registrations as $registration)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $registration->name }}
                                        <div class="text-[11px] font-normal text-slate-400">{{ $registration->created_at->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <div>{{ $registration->email }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $registration->phone ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-600 font-bold">{{ $registration->ticket_code }}</td>
                                    <td class="px-4 py-3">
                                        @if($registration->amount > 0)
                                            <div class="font-extrabold text-slate-900">Rp {{ number_format($registration->amount, 0, ',', '.') }}</div>
                                            @if($registration->payment_status === 'pending')
                                                <span class="inline-flex rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-700 mt-0.5">
                                                    Menunggu Verifikasi
                                                </span>
                                            @elseif(in_array($registration->payment_status, ['confirmed', 'free']))
                                                <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700 mt-0.5">
                                                    Lunas
                                                </span>
                                            @elseif($registration->payment_status === 'rejected')
                                                <span class="inline-flex rounded-full bg-red-50 border border-red-200 px-2 py-0.5 text-[10px] font-bold text-red-700 mt-0.5">
                                                    Ditolak
                                                </span>
                                            @endif
                                            @if($registration->payment_proof)
                                                <div class="mt-1">
                                                    <a href="{{ $registration->payment_proof }}" target="_blank" class="text-blue-600 font-bold text-[10.5px] hover:underline">
                                                        Lihat Bukti &rarr;
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10.5px] font-bold text-slate-600">
                                                Gratis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $attendedAt = $event->isOnline() ? $registration->joined_at : $registration->checked_in_at;
                                        @endphp
                                        @if ($attendedAt)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700">
                                                Hadir · {{ $attendedAt->format('d M H:i') }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-500">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($registration->payment_status === 'pending')
                                                <form action="{{ route('admin.events.registrations.confirm', $registration->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran peserta?')">
                                                    @csrf
                                                    <button type="submit" class="font-bold text-emerald-600 hover:underline">
                                                        Konfirmasi
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.events.registrations.check-in', [$event, $registration]) }}" method="POST" data-async data-async-reload>
                                                @csrf
                                                <button type="submit"
                                                        class="font-semibold {{ $attendedAt ? 'text-rose-600 hover:underline' : 'text-blue-600 hover:underline' }}">
                                                    {{ $attendedAt ? 'Batalkan Hadir' : 'Check-in' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada peserta terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
