@extends('admin.layouts.app')

@section('title', 'Manajemen Artikel — Admin')

@section('content')
{{-- Action Button --}}
<div class="flex items-center justify-end mb-5 sm:mb-6">
    <a href="{{ route('admin.articles.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-[42px] px-4 rounded-xl bg-brand-600 text-white text-[13.5px] font-semibold hover:bg-blue-700 shadow-sm transition whitespace-nowrap">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" class="shrink-0"><path d="M12 5v14M5 12h14"/></svg>
        <span>Buat Artikel Baru</span>
    </a>
</div>

{{-- Flash message --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13.5px] font-medium">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @php
        $stats = [
            ['label' => 'Total Artikel', 'value' => $totalArticles,  'icon' => 'articles', 'bg' => 'bg-blue-50 text-[#0b57d0]'],
            ['label' => 'Menunggu Approval', 'value' => $pendingCount, 'icon' => 'pending', 'bg' => 'bg-amber-50 text-amber-600'],
            ['label' => 'Tayang (Published)', 'value' => $publishedCount, 'icon' => 'check', 'bg' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Draf & Ditolak', 'value' => number_format((int)str_replace(',', '', $draftCount) + (int)str_replace(',', '', $rejectedCount)), 'icon' => 'draft', 'bg' => 'bg-slate-100 text-slate-600'],
        ];
    @endphp
    @foreach ($stats as $s)
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg {{ $s['bg'] }} flex items-center justify-center">
                    @if ($s['icon'] === 'articles')
                        <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
                    @elseif ($s['icon'] === 'pending')
                        <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    @elseif ($s['icon'] === 'check')
                        <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12.5 2.5 2.5 5-5.5"/></svg>
                    @elseif ($s['icon'] === 'draft')
                        <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a1 1 0 0 0 1 1h4M6 21h12a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2zm4-6h4"/></svg>
                    @endif
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 text-xl sm:text-[24px] font-extrabold text-slate-900 leading-none">{{ $s['value'] }}</div>
            <div class="mt-1.5 text-[12px] sm:text-[13px] text-slate-500">{{ $s['label'] }}</div>
        </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-slate-100">
        <div>
            <h2 class="text-[16px] font-bold text-slate-900">Semua Artikel</h2>
            <p class="text-[12.5px] text-slate-500">Total {{ $articles->total() }} artikel</p>
        </div>
        {{-- Search + filter --}}
        <form method="GET" action="{{ route('admin.articles.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul artikel..."
                   class="h-10 sm:h-9 px-3 rounded-lg border border-slate-200 bg-white text-[13px] text-slate-600 outline-none focus:border-blue-500 w-full sm:w-52">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="tab" onchange="this.form.submit()"
                        class="flex-1 sm:flex-initial min-w-0 h-10 sm:h-9 px-3 rounded-lg border border-slate-200 bg-white text-[13px] text-slate-600 focus:outline-none focus:border-blue-500">
                    <option value="all"      {{ $currentTab === 'all'      ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending"  {{ $currentTab === 'pending'  ? 'selected' : '' }}>Menunggu Approval ({{ $pendingCount }})</option>
                    <option value="published"{{ $currentTab === 'published' ? 'selected' : '' }}>Tayang (Published)</option>
                    <option value="draft"    {{ $currentTab === 'draft'    ? 'selected' : '' }}>Draf (Draft)</option>
                    <option value="rejected" {{ $currentTab === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    <option value="archived" {{ $currentTab === 'archived' ? 'selected' : '' }}>Arsip (Archived)</option>
                </select>
                <button type="submit" class="h-10 sm:h-9 px-4 sm:px-3 rounded-lg bg-brand-600 text-white text-[13px] font-semibold hover:bg-blue-700 transition shrink-0 cursor-pointer">Cari</button>
            </div>
        </form>
    </div>

    {{-- Mobile Card List --}}
    <div class="md:hidden divide-y divide-slate-100">
        @forelse ($articles as $article)
            @php
                $initials = collect(explode(' ', $article->author_name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                $statusCfg = match($article->status) {
                    'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 before:bg-emerald-500',
                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-200/80 before:bg-amber-500',
                    'rejected'  => 'bg-rose-50 text-rose-700 border-rose-200/80 before:bg-rose-500',
                    'archived'  => 'bg-slate-100 text-slate-600 border-slate-200 before:bg-slate-400',
                    default     => 'bg-slate-100 text-slate-700 border-slate-200 before:bg-slate-400',
                };
                $statusLabel = match($article->status) {
                    'published' => 'Tayang',
                    'pending'   => 'Pending Approval',
                    'rejected'  => 'Ditolak',
                    'archived'  => 'Arsip',
                    default     => 'Draf',
                };
                $isSuperAdmin = in_array(auth()->user()->role, ['super_admin', 'admin'], true);
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-bold text-[14px] text-slate-900 leading-snug">
                            {{ $article->title }}
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[12px] text-slate-500 mt-1">
                            <span>{{ $article->category }}</span>
                            <span>·</span>
                            <span>{{ $article->formatted_date }}</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-semibold border shrink-0 {{ $statusCfg }} before:content-[''] before:w-1.5 before:h-1.5 before:rounded-full">{{ $statusLabel }}</span>
                </div>

                <div class="flex items-center gap-2 text-[12.5px] text-slate-700">
                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10.5px] font-bold shrink-0">{{ $initials }}</span>
                    <span class="truncate">{{ $article->author_name }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if($isSuperAdmin && $article->status !== 'published')
                        <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST" data-async data-async-reload class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1 min-h-[38px] px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 font-bold text-xs transition cursor-pointer">
                                Approve
                            </button>
                        </form>
                    @endif

                    @if($isSuperAdmin && $article->status === 'pending')
                        <form action="{{ route('admin.articles.reject', $article->id) }}" method="POST" data-async data-async-reload class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1 min-h-[38px] px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/90 font-bold text-xs transition cursor-pointer">
                                Reject
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.articles.edit', $article->id) }}"
                       class="flex-1 inline-flex items-center justify-center min-h-[38px] px-3 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs transition">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}"
                          onsubmit="return confirm('Hapus artikel ini? Tindakan tidak bisa dibatalkan.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center min-h-[38px] min-w-[38px] px-2.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition cursor-pointer" aria-label="Delete" title="Hapus Artikel">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14zM10 11v6m4-6v6"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-slate-400">
                <svg class="mx-auto mb-3 text-slate-300" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
                <p class="text-[14px] font-medium">Belum ada artikel</p>
                <p class="text-[12.5px] mt-1">Klik <strong>Buat Artikel Baru</strong> untuk membuat artikel pertama.</p>
            </div>
        @endforelse
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[11.5px] uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">JUDUL ARTIKEL</th>
                    <th class="px-5 py-3 font-semibold">KATEGORI</th>
                    <th class="px-5 py-3 font-semibold">PENULIS</th>
                    <th class="px-5 py-3 font-semibold">TANGGAL PUBLIKASI</th>
                    <th class="px-5 py-3 font-semibold">STATUS</th>
                    <th class="px-5 py-3 font-semibold text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-[13.5px]">
                @forelse ($articles as $article)
                    @php
                        $initials = collect(explode(' ', $article->author_name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                        $statusCfg = match($article->status) {
                            'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 before:bg-emerald-500',
                            'pending'   => 'bg-amber-50 text-amber-700 border-amber-200/80 before:bg-amber-500',
                            'rejected'  => 'bg-rose-50 text-rose-700 border-rose-200/80 before:bg-rose-500',
                            'archived'  => 'bg-slate-100 text-slate-600 border-slate-200 before:bg-slate-400',
                            default     => 'bg-slate-100 text-slate-700 border-slate-200 before:bg-slate-400',
                        };
                        $statusLabel = match($article->status) {
                            'published' => 'Tayang',
                            'pending'   => 'Pending Approval',
                            'rejected'  => 'Ditolak',
                            'archived'  => 'Arsip',
                            default     => 'Draf',
                        };
                        $isSuperAdmin = in_array(auth()->user()->role, ['super_admin', 'admin'], true);
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-medium text-slate-900 max-w-[280px] truncate">
                            <span class="block truncate" title="{{ $article->title }}">{{ $article->title }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $article->category }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[11px] font-bold">{{ $initials }}</span>
                                <span class="text-slate-700">{{ $article->author_name }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ $article->formatted_date }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold border {{ $statusCfg }} before:content-[''] before:w-1.5 before:h-1.5 before:rounded-full">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($isSuperAdmin && $article->status !== 'published')
                                    <form action="{{ route('admin.articles.approve', $article->id) }}" method="POST" data-async data-async-reload class="inline">
                                        @csrf
                                        <button type="submit" title="Setujui dan Publikasikan Artikel"
                                                class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 font-bold text-xs px-2.5 py-1.5 rounded-lg inline-flex items-center gap-1 transition shadow-2xs">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>
                                            <span>Approve</span>
                                        </button>
                                    </form>
                                @endif

                                @if($isSuperAdmin && $article->status === 'pending')
                                    <form action="{{ route('admin.articles.reject', $article->id) }}" method="POST" data-async data-async-reload class="inline">
                                        @csrf
                                        <button type="submit" title="Tolak Artikel"
                                                class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/90 font-bold text-xs px-2.5 py-1.5 rounded-lg inline-flex items-center gap-1 transition shadow-2xs">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                                            <span>Reject</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.articles.edit', $article->id) }}"
                                   class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" aria-label="Edit" title="Edit Artikel">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>

                                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}"
                                      onsubmit="return confirm('Hapus artikel ini? Tindakan tidak bisa dibatalkan.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" aria-label="Delete" title="Hapus Artikel">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14zM10 11v6m4-6v6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                            <svg class="mx-auto mb-3 text-slate-300" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
                            <p class="text-[14px] font-medium">Belum ada artikel</p>
                            <p class="text-[12.5px] mt-1">Klik <strong>Buat Artikel Baru</strong> untuk membuat artikel pertama.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($articles->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 sm:px-5 py-4 border-t border-slate-100 text-center sm:text-left">
            <span class="text-[12.5px] text-slate-500">
                Menampilkan {{ $articles->firstItem() ?? 0 }} sampai {{ $articles->lastItem() ?? 0 }} dari total {{ $articles->total() }} artikel
            </span>
            <div class="flex flex-wrap items-center justify-center gap-1">
                {{-- Prev --}}
                @if ($articles->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-300 flex items-center justify-center cursor-not-allowed">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m15 6-6 6 6 6"/></svg>
                    </span>
                @else
                    <a href="{{ $articles->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center justify-center transition">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m15 6-6 6 6 6"/></svg>
                    </a>
                @endif

                @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                    @if ($page === $articles->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-brand-600 text-white text-[13px] font-semibold flex items-center justify-center">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-[13px] flex items-center justify-center transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($articles->hasMorePages())
                    <a href="{{ $articles->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center justify-center transition">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m9 6 6 6-6 6"/></svg>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-300 flex items-center justify-center cursor-not-allowed">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m9 6 6 6-6 6"/></svg>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

