@extends('admin.layouts.app')

@section('title', 'Report Management — IndoTech Admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <h1 class="text-[22px] font-extrabold text-slate-900 tracking-tight leading-tight">Report Management</h1>
            <p class="text-[13.5px] text-slate-500 mt-0.5">Monitor and resolve system, content, and user reports.</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            {{-- Filter Button --}}
            <button type="button" id="toggle-filter"
                class="inline-flex items-center gap-2 h-9 px-4 rounded-xl border border-slate-200 bg-white text-[13px] font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
            </button>
            {{-- Export Data Button --}}
            <button type="button" id="generate-report"
                class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-brand-600 text-white text-[13px] font-semibold hover:bg-blue-700 shadow-xs transition">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16" />
                </svg>
                Export Data
            </button>
        </div>
    </div>

    {{-- Filter Drawer --}}
    <div id="filter-drawer" class="hidden">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5">
            <form id="report-filter-form" method="GET" action="{{ route('admin.reports.index') }}"
                class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="report-type" class="form-label">Report Type</label>
                    <select id="report-type" name="type"
                        class="w-full h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-[13px] text-slate-700 outline-none focus:border-blue-500 focus:bg-white">
                        <option value="">All types</option>
                        @foreach ($reports as $report)
                            <option value="{{ $report['slug'] }}" @selected($activeType === $report['slug'])>
                                {{ $report['type'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report-from" class="form-label">From</label>
                    <input id="report-from" name="from" type="date" value="{{ $from }}"
                        class="w-full h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-[13px] text-slate-700 outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label for="report-to" class="form-label">To</label>
                    <input id="report-to" name="to" type="date" value="{{ $to }}"
                        class="w-full h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-[13px] text-slate-700 outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div class="sm:col-span-3 flex justify-end gap-2">
                    <a href="{{ route('admin.reports.index') }}"
                        class="h-9 px-4 inline-flex items-center rounded-lg border border-slate-200 bg-white text-[13px] font-semibold text-slate-700 hover:bg-slate-50 transition">Reset</a>
                    <button type="submit"
                        class="h-9 px-4 inline-flex items-center rounded-lg bg-brand-600 text-white text-[13px] font-semibold hover:bg-blue-700 transition">Apply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Reports --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-brand-600">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="text-[13px] text-slate-500 mb-1 font-medium">Total Laporan Masalah</div>
            <div class="text-[28px] font-extrabold text-slate-900 leading-none">
                {{ number_format(\App\Models\UserReport::count()) }}
            </div>
        </div>

        {{-- Pending Investigation --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
            <div class="mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-[13px] text-slate-500 mb-1 font-medium">Menunggu Tindak Lanjut</div>
            <div class="text-[28px] font-extrabold text-slate-900 leading-none">
                {{ number_format(\App\Models\UserReport::where('status', 'pending')->count()) }}
            </div>
        </div>

        {{-- Resolved --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
            <div class="mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-[13px] text-slate-500 mb-1 font-medium">Laporan Selesai</div>
            <div class="text-[28px] font-extrabold text-slate-900 leading-none">
                {{ number_format(\App\Models\UserReport::where('status', 'resolved')->count()) }}
            </div>
        </div>
    </div>

    {{-- Recent Reports Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-[15px] font-bold text-slate-900">Laporan dari Pengguna</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Bug, masalah konten, atau teknis yang dilaporkan pengguna.</p>
            </div>
            {{-- Filter status --}}
            <div class="flex items-center gap-2">
                @foreach(['', 'pending', 'investigating', 'resolved', 'closed'] as $s)
                    @php
                        $labelMap = ['' => 'Semua', 'pending' => 'Menunggu', 'investigating' => 'Ditinjau', 'resolved' => 'Selesai', 'closed' => 'Ditutup'];
                        $isActive = request('report_status', '') === $s;
                    @endphp
                    <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['report_status' => $s, 'ur_page' => 1])) }}"
                       class="text-[12px] font-semibold px-3 py-1 rounded-full transition {{ $isActive ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $labelMap[$s] }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 text-[11.5px] uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-3.5 font-semibold">Pelapor</th>
                        <th class="px-6 py-3.5 font-semibold">Kategori</th>
                        <th class="px-6 py-3.5 font-semibold">Judul</th>
                        <th class="px-6 py-3.5 font-semibold">Prioritas</th>
                        <th class="px-6 py-3.5 font-semibold">Tanggal</th>
                        <th class="px-6 py-3.5 font-semibold">Status</th>
                        <th class="px-6 py-3.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @php
                        $statusClasses = [
                            'investigating' => 'bg-blue-100 text-blue-600',
                            'pending'       => 'bg-slate-100 text-slate-600',
                            'resolved'      => 'bg-emerald-100 text-emerald-700',
                            'closed'        => 'bg-slate-100 text-slate-500',
                        ];
                        $priorityConfig = [
                            'high'   => ['bg' => 'bg-rose-50',   'text' => 'text-rose-600',   'dot' => 'bg-rose-500',   'label' => 'Tinggi'],
                            'medium' => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500',   'label' => 'Sedang'],
                            'low'    => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'dot' => 'bg-indigo-400', 'label' => 'Rendah'],
                        ];
                        $statusLabels = [
                            'pending'       => 'Menunggu',
                            'investigating' => 'Sedang Ditinjau',
                            'resolved'      => 'Selesai',
                            'closed'        => 'Ditutup',
                        ];
                    @endphp
                    @forelse ($userReports as $report)
                        @php
                            $p = $priorityConfig[$report->priority] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400', 'label' => $report->priority];
                            $sClass = $statusClasses[$report->status] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $report->user?->name ?? '—' }}</div>
                                <div class="text-[11.5px] text-slate-400">{{ $report->user?->email }}</div>
                                @php
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin'       => 'Admin',
                                        'school'      => 'Sekolah',
                                        'university'  => 'Kampus',
                                        'company'     => 'Perusahaan',
                                        'user'        => 'User',
                                    ];
                                    $roleLabel = $roleLabels[$report->user?->role] ?? ($report->user?->role ?? '—');
                                @endphp
                                <div class="mt-0.5">
                                    <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $report->category }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 max-w-[200px] truncate" title="{{ $report->subject }}">{{ $report->subject }}</div>
                                @if($report->description)
                                    <div class="text-[11.5px] text-slate-400 max-w-[200px] truncate" title="{{ $report->description }}">{{ $report->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold {{ $p['bg'] }} {{ $p['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $p['dot'] }}"></span>
                                    {{ $p['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $report->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-[12px] font-semibold {{ $sClass }}">
                                    {{ $statusLabels[$report->status] ?? $report->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button"
                                        onclick="openReportModal({{ $report->id }}, '{{ addslashes($report->subject) }}', '{{ addslashes($report->description) }}', '{{ $report->status }}', '{{ addslashes($report->admin_notes ?? '') }}')"
                                        class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-[#edf4ff] border border-[#d3e4ff] text-[12.5px] font-semibold text-[#2563eb] hover:bg-blue-100 transition shadow-2xs">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Tindak Lanjut
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-slate-400">
                                <svg class="mx-auto mb-3 text-slate-300" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-[13.5px] font-medium">Belum ada laporan dari pengguna</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($userReports->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $userReports->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tindak Lanjut Laporan --}}
    <div id="report-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" style="display:none!important;">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg mx-4 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-[15px] font-bold text-slate-900">Tindak Lanjut Laporan</h3>
                <button type="button" onclick="closeReportModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 space-y-1.5">
                <div class="text-[13px] font-bold text-slate-800" id="modal-subject"></div>
                <div class="text-[12.5px] text-slate-500 leading-relaxed" id="modal-description"></div>
            </div>
            <form id="report-update-form" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Perbarui Status</label>
                    <select name="status" id="modal-status" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        <option value="pending">Menunggu</option>
                        <option value="investigating">Sedang Ditinjau</option>
                        <option value="resolved">Selesai</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Admin (opsional)</label>
                    <textarea name="admin_notes" id="modal-notes" rows="3" maxlength="1000"
                              placeholder="Tambahkan catatan penanganan..."
                              class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm resize-none focus:outline-none focus:border-blue-500"></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-1">
                    <button type="button" onclick="closeReportModal()"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-blue-700 text-sm font-semibold text-white transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Export History --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-[15px] font-bold text-slate-900">Export History</h2>
                <p class="text-[12px] text-slate-500">Archive of previously exported reports.</p>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-600">{{ number_format($exports->total()) }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[11.5px] uppercase tracking-wider text-slate-400">
                        <th class="table-head">Waktu</th>
                        <th class="table-head">Laporan</th>
                        <th class="table-head">Periode</th>
                        <th class="table-head">Format</th>
                        <th class="table-head">Baris</th>
                        <th class="table-head">Oleh</th>
                        <th class="px-6 py-3.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse ($exports as $export)
                        @php
                            $definition = collect($reports)->firstWhere('slug', $export->type);
                            $formatClass = match ($export->format) {
                                'pdf'   => 'bg-rose-50 text-rose-600',
                                'xlsx'  => 'bg-emerald-50 text-emerald-600',
                                default => 'bg-blue-50 text-blue-600',
                            };
                            $exportQuery = array_filter([
                                'type' => $export->type,
                                'from' => $export->period_from?->toDateString(),
                                'to'   => $export->period_to?->toDateString(),
                            ]);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $export->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $definition['title'] ?? strtoupper($export->type) }}</div>
                                <div class="text-[12px] text-slate-400">{{ $export->file_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $export->period_from?->format('d M Y') ?? 'Beginning' }} - {{ $export->period_to?->format('d M Y') ?? 'Now' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[12px] font-semibold uppercase {{ $formatClass }}">{{ $export->format }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ number_format($export->rows_count) }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $export->user?->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.reports.show', $exportQuery) }}"
                                    class="text-[13px] font-semibold text-brand-600 hover:underline">Buka</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 text-[13.5px]">Belum ada riwayat export.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($exports->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $exports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Toggle filter drawer
    document.getElementById('toggle-filter')?.addEventListener('click', function () {
        const drawer = document.getElementById('filter-drawer');
        drawer.classList.toggle('hidden');
    });

    // Generate / Export Data button
    document.getElementById('generate-report')?.addEventListener('click', function () {
        const type = document.getElementById('report-type')?.value;
        const from = document.getElementById('report-from')?.value;
        const to   = document.getElementById('report-to')?.value;

        const params = new URLSearchParams();
        if (from) params.set('from', from);
        if (to)   params.set('to', to);

        if (type) {
            const query = params.toString();
            window.location.href = `{{ url('/admin/reports') }}/${type}${query ? '?' + query : ''}`;
            return;
        }

        document.getElementById('report-filter-form')?.submit();
    });

    // Modal laporan pengguna
    function openReportModal(id, subject, description, status, notes) {
        document.getElementById('modal-subject').textContent = subject;
        document.getElementById('modal-description').textContent = description;
        document.getElementById('modal-status').value = status;
        document.getElementById('modal-notes').value = notes;
        document.getElementById('report-update-form').action = `/admin/user-reports/${id}`;
        const modal = document.getElementById('report-modal');
        modal.style.removeProperty('display');
        modal.style.display = 'flex';
    }

    function closeReportModal() {
        document.getElementById('report-modal').style.display = 'none';
    }

    document.getElementById('report-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeReportModal();
    });

    // Toast sukses
    @if(session('success'))
        (function() {
            const toast = document.createElement('div');
            toast.className = 'fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-900 text-white text-[13.5px] shadow-xl border border-slate-800 transition-all duration-300';
            toast.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        })();
    @endif
</script>
@endpush
