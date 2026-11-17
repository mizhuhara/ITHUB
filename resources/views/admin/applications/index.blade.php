@extends('admin.layouts.app')

@section('title', 'Data Pelamar — IndoTech Admin')
@section('header_title', 'Data Pelamar')
@section('header_breadcrumb', 'Data Pelamar')
@section('header_subtitle', 'Pantau dan kelola seluruh lamaran pekerjaan yang masuk dari para talenta.')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        @foreach ([
            ['label' => 'TOTAL APPLICATIONS', 'value' => $totalApplications],
            ['label' => 'PENDING REVIEW', 'value' => $pendingApplications],
            ['label' => 'SHORTLISTED', 'value' => $shortlistedApplications],
        ] as $stat)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <div class="text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">{{ $stat['label'] }}</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($stat['value']) }}</div>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.applications.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Cari nama, email, atau telepon"
               class="flex-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-600 focus:outline-none">
        <select name="status" class="w-full sm:w-auto rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-600 focus:outline-none">
            @foreach (['all' => 'Semua Status', 'pending' => 'Pending', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected'] as $value => $label)
                <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="w-full sm:w-auto rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden">
        {{-- Table for Desktop & Tablet --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Pelamar</th>
                        <th class="px-5 py-3">Posisi</th>
                        <th class="px-5 py-3">Kontak</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $application)
                        <tr class="align-top">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $application->full_name }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $application->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-800">{{ $application->positionTitle() }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $application->companyName() }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div>{{ $application->email }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $application->phone }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.applications.update', $application) }}" data-async data-async-reload class="space-y-2 min-w-48">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-brand-600 focus:outline-none">
                                        @foreach (['pending', 'reviewed', 'shortlisted', 'rejected'] as $status)
                                            <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="note" maxlength="1000" placeholder="Catatan (opsional)"
                                           class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-brand-600 focus:outline-none">
                                    <button type="submit"
                                            class="w-full rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.applications.resume', $application) }}" class="text-brand-600 font-semibold hover:underline">Resume</a>
                                    <form method="POST" action="{{ route('admin.applications.destroy', $application) }}" onsubmit="return confirm('Hapus lamaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 font-semibold hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500">Belum ada lamaran masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Card List for Mobile (No Horizontal Scrolling Needed!) --}}
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse ($applications as $application)
                <div class="p-4 space-y-3">
                    {{-- Header: Pelamar & Waktu --}}
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-bold text-[14.5px] text-slate-900 leading-snug">{{ $application->full_name }}</div>
                            <div class="text-[11.5px] text-slate-400 mt-0.5">{{ $application->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider
                            {{ match($application->status) {
                                'shortlisted' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                'reviewed' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                default => 'bg-amber-50 text-amber-700 border border-amber-200'
                            } }}">
                            {{ $application->status }}
                        </span>
                    </div>

                    {{-- Posisi & Perusahaan --}}
                    <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                        <div class="font-semibold text-brand-600">{{ $application->positionTitle() }}</div>
                        <div class="text-slate-500 mt-0.5">{{ $application->companyName() }}</div>
                    </div>

                    {{-- Kontak --}}
                    <div class="flex items-center justify-between text-xs text-slate-600 flex-wrap gap-2 pt-1 border-t border-slate-50">
                        <a href="mailto:{{ $application->email }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-600 break-all">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <span>{{ $application->email }}</span>
                        </a>
                        @if($application->phone)
                            <a href="tel:{{ $application->phone }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-600">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/></svg>
                                <span>{{ $application->phone }}</span>
                            </a>
                        @endif
                    </div>

                    {{-- Form Status & Aksi --}}
                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <form method="POST" action="{{ route('admin.applications.update', $application) }}" data-async data-async-reload class="space-y-2">
                            @csrf
                            @method('PATCH')
                            <div class="flex gap-2">
                                <select name="status" class="flex-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-brand-600 focus:outline-none">
                                    @foreach (['pending', 'reviewed', 'shortlisted', 'rejected'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-brand-600 text-xs font-semibold text-white hover:bg-blue-700 transition shrink-0">
                                    Update
                                </button>
                            </div>
                            <input type="text" name="note" maxlength="1000" placeholder="Catatan (opsional)" value="{{ $application->note ?? '' }}"
                                   class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-brand-600 focus:outline-none">
                        </form>

                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ route('admin.applications.resume', $application) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg transition">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                                <span>Unduh Resume</span>
                            </a>
                            <form method="POST" action="{{ route('admin.applications.destroy', $application) }}" onsubmit="return confirm('Hapus lamaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 px-3 py-1.5 rounded-lg transition cursor-pointer">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Belum ada lamaran masuk.</div>
            @endforelse
        </div>
        @if ($applications->hasPages())
            <div class="border-t border-slate-100 p-4 overflow-x-auto flex justify-center sm:justify-start">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
