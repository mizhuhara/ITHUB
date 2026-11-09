@extends('layouts.app')

@section('title', 'Karir — ITHub')

@section('content')
<style>
    .cr-page {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .cr-tabs {
        position: sticky;
        top: 62px;
        z-index: 20;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
    }
    .cr-tabs-inner {
        display: flex;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;   /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .cr-tabs-inner::-webkit-scrollbar { display: none; } /* Chrome/Safari */
    .cr-tabs a {
        flex-shrink: 0;
        padding: 14px 16px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1px;
        white-space: nowrap;
        transition: color 0.15s, border-color 0.15s;
    }
    .cr-tabs a:hover { color: #2563eb; }
    .cr-tabs a.on {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }
    .cr-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 20px;
        padding-top: 20px;
        padding-bottom: 40px;
    }
    .cr-sidebar {
        position: sticky;
        top: 115px;
        max-height: calc(100vh - 130px);
        overflow-y: auto;
        padding-right: 2px;
        padding-bottom: 24px;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
    }
    .cr-sidebar::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
        width: 0;
        height: 0;
    }
    .cr-main {
        padding-right: 4px;
        padding-bottom: 24px;
    }
    .cr-card-image {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 7px;
        margin-bottom: 8px;
        background: #f1f5f9;
    }
    /* Clean, Unified Filter Card */
    .cr-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 4px 16px 0 rgba(15, 23, 42, 0.02);
        overflow: hidden;
    }
    .cr-filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cr-filter-title-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cr-filter-icon {
        color: #2563eb;
        display: flex;
        align-items: center;
    }
    .cr-filter-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.01em;
        margin: 0;
    }
    .cr-clear {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .cr-clear:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
        text-decoration: none;
    }
    .cr-filter-section {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cr-filter-section:last-child {
        border-bottom: none;
    }
    .cr-filter-section-title {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0 0 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cr-check-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .cr-check-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 6px 8px;
        margin: 0 -8px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease;
        user-select: none;
    }
    .cr-check-item:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }
    .cr-check-item.active {
        background-color: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
    }
    .cr-check-item input {
        width: 15px;
        height: 15px;
        border-radius: 4px;
        border: 1.5px solid #cbd5e1;
        accent-color: #2563eb;
        cursor: pointer;
    }
    .cr-skills-cloud {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .cr-skill-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        padding: 4px 9px;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .cr-skill-pill input {
        display: none;
    }
    .cr-skill-pill:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
        color: #0f172a;
    }
    .cr-skill-pill.active {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .cr-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .cr-count {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }
    .cr-count strong { color: #0f172a; }
    .cr-search {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0 12px;
        min-width: 240px;
        flex: 1;
        max-width: 360px;
    }
    .cr-search svg { color: #94a3b8; flex-shrink: 0; }
    .cr-search input {
        border: none;
        outline: none;
        font-size: 13.5px;
        padding: 9px 10px;
        width: 100%;
        font-family: inherit;
        background: transparent;
        color: #334155;
    }
    .cr-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    @media (max-width: 1080px) {
        .cr-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .cr-list {
            grid-template-columns: 1fr;
        }
    }
    .cr-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 13px;
        transition: box-shadow 0.2s, transform 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .cr-card:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }
    .cr-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .cr-logo {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 11px;
        color: #fff;
    }
    .cr-tag {
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
    }
    .cr-tag.jobs { background: #dbeafe; color: #2563eb; }
    .cr-tag.internship { background: #fef3c7; color: #d97706; }
    .cr-tag.freelance { background: #fce7f3; color: #db2777; }
    .cr-tag.remote { background: #d1fae5; color: #059669; }
    .cr-tag.graduate { background: #ede9fe; color: #7c3aed; }
    .cr-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 1px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cr-card-company {
        font-size: 11.5px;
        color: #64748b;
        margin: 0;
    }
    .cr-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-top: 6px;
    }
    .cr-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #64748b;
    }
    .cr-meta-item svg { color: #94a3b8; flex-shrink: 0; width: 12px; height: 12px; }
    .cr-skill-row {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 6px;
    }
    .cr-chip {
        font-size: 10.5px;
        font-weight: 600;
        color: #334155;
        background: #f1f5f9;
        border-radius: 4px;
        padding: 1.5px 6px;
    }
    .cr-desc {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.45;
        margin: 6px 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cr-card-bottom {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cr-card-bottom .it-status {
        font-size: 11px;
        padding: 2px 7px;
    }
    .cr-apply {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        font-size: 11.5px;
        font-weight: 600;
        color: #fff;
        background: #2563eb;
        border-radius: 6px;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(37,99,235,0.2);
        transition: background 0.15s;
    }
    .cr-apply { background: #2563eb; }
    .cr-apply:hover { background: #1d4ed8; }
    .cr-open {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        font-size: 11.5px;
        font-weight: 600;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .cr-open:hover {
        background: #dbeafe;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    .cr-empty {
        text-align: center;
        padding: 64px 24px;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
    }
    .cr-empty h3 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .cr-empty p { font-size: 14px; color: #64748b; margin: 0; }
    .cr-filter-toggle {
        display: none;
        width: 100%;
        margin-bottom: 12px;
        padding: 10px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #2563eb;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 10px;
        cursor: pointer;
        font-family: inherit;
    }
    @media (max-width: 860px) {
        .cr-layout {
            grid-template-columns: 1fr;
            height: auto;
            min-height: 0;
        }
        .cr-sidebar {
            display: none;
            max-height: 60vh;
            overflow-y: auto;
            margin-bottom: 8px;
        }
        .cr-sidebar.open { display: block; }
        .cr-main { overflow: visible; }
        .cr-filter-toggle { display: block; }
        .cr-tabs { top: 62px; }
    }
</style>

<div class="cr-page">
    <nav class="cr-tabs" aria-label="Kategori Karir">
        <div class="cr-tabs-inner">
            @foreach($tabs as $key => $label)
                <a href="{{ route('career.index', array_merge(request()->except('type'), ['type' => $key])) }}"
                   class="{{ $tab === $key ? 'on' : '' }}"
                   id="tab-{{ $key }}">{{ $label }}</a>
            @endforeach
        </div>
    </nav>

    @if(session('apply_success'))
    <div id="cr-success-banner" style="
        display:flex;align-items:center;gap:12px;
        background:linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%);
        border:1px solid #6ee7b7;border-radius:12px;
        padding:14px 20px;margin:16px 0 0;
        font-size:14px;font-weight:600;color:#065f46;
    ">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#059669"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        {{ session('apply_success') }}
        <button onclick="document.getElementById('cr-success-banner').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#059669;font-size:18px;line-height:1;" aria-label="Tutup">&times;</button>
    </div>
    @endif

    <div class="cr-layout">
        @include('career._filters')

        <div class="cr-main">
            <button type="button" class="cr-filter-toggle" id="cr-filter-toggle">Tampilkan Filter</button>

            <div class="cr-toolbar">
                <p class="cr-count"><strong>{{ $jobs->total() }}</strong> lowongan tersedia</p>
                <form class="cr-search" method="GET" action="{{ route('career.index') }}">
                    <input type="hidden" name="type" value="{{ $tab }}">
                    @foreach((array) request('job_type', []) as $v)
                        <input type="hidden" name="job_type[]" value="{{ $v }}">
                    @endforeach
                    @foreach((array) request('experience', []) as $v)
                        <input type="hidden" name="experience[]" value="{{ $v }}">
                    @endforeach
                    @foreach((array) request('salary', []) as $v)
                        <input type="hidden" name="salary[]" value="{{ $v }}">
                    @endforeach
                    @foreach((array) request('skills', []) as $v)
                        <input type="hidden" name="skills[]" value="{{ $v }}">
                    @endforeach
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul, perusahaan, lokasi..." id="career-search">
                </form>
            </div>

            @if(count($jobs) === 0)
                <div class="cr-empty">
                    <h3>Tidak ada lowongan yang sesuai</h3>
                    <p>Hapus filter atau coba kategori lain.</p>
                </div>
            @else
                <div class="cr-list">
                    @foreach($jobs as $job)
                        @include('career._job-card', ['job' => $job, 'tabs' => $tabs])
                    @endforeach
                </div>

                {{-- Pagination (Centered) --}}
                @if($jobs->hasPages())
                    <div class="cr-pagination mt-10 mb-4 flex justify-center w-full">
                        {{ $jobs->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        var btn = document.getElementById('cr-filter-toggle');
        var side = document.getElementById('cr-sidebar');
        if (btn && side) {
            btn.addEventListener('click', function () {
                side.classList.toggle('open');
                btn.textContent = side.classList.contains('open') ? 'Sembunyikan Filter' : 'Tampilkan Filter';
            });
        }
    })();
</script>
@endsection
