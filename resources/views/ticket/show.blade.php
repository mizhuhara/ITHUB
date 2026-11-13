@extends('layouts.app')

@section('title', 'Tiket '.$registration->ticket_code.' - IndoTech Events')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="mx-auto max-w-2xl space-y-5 px-4 sm:px-6">

        <a href="{{ route('event.show', $event->id) }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-blue-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Event
        </a>

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

        @php
            $statusLabel = match (true) {
                $registration->checked_in_at !== null => 'Sudah Check-in',
                $registration->joined_at !== null => 'Sudah Masuk Meeting',
                $registration->payment_status === 'pending' => 'Menunggu Verifikasi Pembayaran',
                $registration->payment_status === 'rejected' => 'Pembayaran Ditolak',
                default => 'Terdaftar',
            };
            $statusClass = match (true) {
                $registration->checked_in_at !== null => 'bg-emerald-100 text-emerald-700',
                $registration->joined_at !== null => 'bg-emerald-100 text-emerald-700',
                $registration->payment_status === 'pending' => 'bg-amber-100 text-amber-700',
                $registration->payment_status === 'rejected' => 'bg-red-100 text-red-700',
                default => 'bg-blue-100 text-blue-700',
            };
        @endphp

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-900 px-6 py-5 text-white">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Tiket Event</p>
                <h1 class="mt-1 text-xl font-extrabold leading-tight">{{ $event->title }}</h1>
                <p class="mt-1 text-xs text-slate-300">{{ $event->organizer }}</p>
            </div>

            <div class="space-y-5 px-6 py-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kode Tiket</p>
                        <p class="mt-1 font-mono text-lg font-extrabold tracking-widest text-slate-900">
                            {{ $registration->ticket_code }}
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                @if ($qrCode)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-5 text-center">
                        <img src="{{ $qrCode }}" alt="QR Tiket {{ $registration->ticket_code }}"
                             class="mx-auto h-48 w-48">
                        <p class="mt-3 text-xs font-semibold text-slate-600">
                            Tunjukkan QR ini ke panitia di lokasi acara.
                        </p>
                    </div>
                @endif

                @if ($event->isOnline() && $registration->isConfirmed())
                    <div class="space-y-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-5 text-center">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Link Meeting</p>
                        @if ($event->hasMeetingLink())
                            <p class="text-xs text-slate-600">
                                {{ $registration->hasJoinedOnline()
                                    ? 'Tercatat masuk pada '.$registration->joined_at->format('d M Y, H:i')
                                    : 'Anda belum tercatat masuk meeting.' }}
                            </p>
                            <a href="{{ route('event.join', $registration->meeting_token) }}" target="_blank" rel="noopener"
                               class="flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                Masuk ke Meeting
                            </a>
                            <p class="text-[11px] font-semibold text-amber-600">
                                Link ini khusus akun Anda. Jangan dibagikan ke orang lain.
                            </p>
                        @else
                            <p class="text-xs text-slate-600">Penyelenggara belum menambahkan link meeting.</p>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Nama Peserta</p>
                        <p class="mt-1 text-sm font-bold text-slate-900">{{ $registration->name }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Email</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $registration->masked_email }}</p>
                    </div>
                    @if ($registration->phone)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Telepon</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $registration->masked_phone }}</p>
                        </div>
                    @endif
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $event->full_date ?? $event->date }}
                            @if ($event->time)
                                &middot; {{ $event->time }}
                            @endif
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 sm:col-span-2">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $event->isOnline() ? 'Online' : ($event->location ?? '-') }}</p>
                    </div>
                </div>

                @if ($registration->checked_in_at)
                    <p class="text-center text-xs font-semibold text-emerald-600">
                        Check-in pada {{ $registration->checked_in_at->format('d M Y, H:i') }}
                    </p>
                @endif

                @if ($certificateAvailable)
                    <a href="{{ route('ticket.certificate', $registration->ticket_code) }}"
                       class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                        Unduh Sertifikat
                    </a>
                @elseif ($event->requiresOnlineAttendance())
                    <p class="text-center text-xs text-slate-500">
                        Sertifikat aktif setelah event selesai dan Anda masuk melalui link meeting pribadi.
                    </p>
                @endif

                @if ($canCheckIn)
                    <form action="{{ route('ticket.check-in', $registration->ticket_code) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-xl px-4 py-3 text-sm font-bold text-white shadow-sm transition {{ $registration->checked_in_at ? 'bg-rose-600 hover:bg-rose-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                            {{ $registration->checked_in_at ? 'Batalkan Check-in' : 'Check-in Peserta' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
