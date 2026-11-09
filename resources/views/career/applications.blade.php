@extends('layouts.app')

@section('title', 'Lamaran Saya — ITHub')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Lamaran Saya</h1>
                <p class="text-sm text-slate-500 mt-1">Pantau status dan catatan dari admin untuk setiap lamaran.</p>
            </div>
            <a href="{{ route('career.index') }}"
               class="inline-flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition">
                Cari Lowongan
            </a>
        </div>

        @forelse ($applications as $application)
            @php
                $statusClasses = [
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'reviewed' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'shortlisted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                ];
                $dotClasses = [
                    'pending' => 'bg-amber-500',
                    'reviewed' => 'bg-blue-500',
                    'shortlisted' => 'bg-emerald-500',
                    'rejected' => 'bg-red-500',
                ];
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    {{ $application->job_listing_id ? 'Lowongan' : 'Magang' }}
                                </span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$application->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $application->statusLabel() }}
                                </span>
                            </div>
                            <h2 class="text-lg font-bold text-slate-900 mt-2">{{ $application->positionTitle() }}</h2>
                            <p class="text-sm text-slate-500 mt-1">{{ $application->companyName() }}</p>
                        </div>
                        <p class="text-xs text-slate-500 shrink-0">
                            Dilamar {{ $application->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Status</h3>

                    <div class="mt-4 space-y-4">
                        @forelse ($application->statusHistories as $history)
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <span class="mt-1.5 h-2.5 w-2.5 rounded-full {{ $dotClasses[$history->status] ?? 'bg-slate-400' }}"></span>
                                    @if (! $loop->last)
                                        <span class="mt-1 w-px flex-1 bg-slate-200"></span>
                                    @endif
                                </div>
                                <div class="pb-1">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ \App\Models\JobApplication::STATUS_LABELS[$history->status] ?? ucfirst($history->status) }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $history->created_at->format('d M Y, H:i') }}</p>
                                    @if ($history->note)
                                        <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="flex gap-3">
                                <span class="mt-1.5 h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Lamaran dikirim</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $application->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-800">Belum ada lamaran.</p>
                <p class="text-sm text-slate-500 mt-1">Lamaran yang Anda kirim saat login akan muncul di sini.</p>
                <a href="{{ route('career.index') }}"
                   class="inline-flex mt-4 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition">
                    Lihat Lowongan
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
