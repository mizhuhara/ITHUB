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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach ([
            ['label' => 'TOTAL APPLICATIONS', 'value' => $totalApplications],
            ['label' => 'PENDING REVIEW', 'value' => $pendingApplications],
            ['label' => 'SHORTLISTED', 'value' => $shortlistedApplications],
        ] as $stat)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-[11.5px] font-bold tracking-wider text-slate-400 uppercase">{{ $stat['label'] }}</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($stat['value']) }}</div>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.applications.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 flex flex-wrap items-center gap-3">
        <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Cari nama, email, atau telepon"
               class="flex-1 min-w-52 rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-600 focus:outline-none">
        <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-600 focus:outline-none">
            @foreach (['all' => 'Semua Status', 'pending' => 'Pending', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected'] as $value => $label)
                <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
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
        @if ($applications->hasPages())
            <div class="border-t border-slate-100 p-4">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
