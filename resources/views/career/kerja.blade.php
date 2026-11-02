@extends('layouts.app')

@section('title', 'Siap Kerja — IndoTech')

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
        scrollbar-width: none;
        -ms-overflow-style: none;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .cr-tabs-inner::-webkit-scrollbar { display: none; }
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

    /* ── Hero Banner ── */
    .sk-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-top: 20px;
        margin-bottom: 4px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 4px 18px rgba(37, 99, 235, 0.14);
    }
    .sk-hero-text h1 { font-size: 20px; font-weight: 800; margin: 0 0 4px; color: #fff; letter-spacing: -0.01em; }
    .sk-hero-text p  { font-size: 13.5px; color: #bfdbfe; margin: 0; line-height: 1.4; }
    .sk-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #fff;
        color: #2563eb;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        transition: transform 0.15s, box-shadow 0.15s, color 0.15s;
        cursor: pointer;
        border: none;
        font-family: inherit;
        flex-shrink: 0;
    }
    .sk-hero-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.18);
        color: #1d4ed8;
        text-decoration: none;
    }

    /* ── Layout & Sidebar (Identical to Career) ── */
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
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .cr-sidebar::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    .cr-main {
        padding-right: 4px;
        padding-bottom: 24px;
    }

    /* Filter Card */
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

    /* ── Toolbar ── */
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

    /* ── Grid ── */
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

    /* ── Card (Same as Karir) ── */
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
    .cr-apply:hover { background: #1d4ed8; color: #fff; text-decoration: none; }

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
        .sk-hero { padding: 22px 20px; }
    }
</style>

<div class="cr-page">
    {{-- Tabs --}}
    <nav class="cr-tabs" aria-label="Kategori Karir">
        <div class="cr-tabs-inner">
            @foreach($tabs as $key => $label)
                <a href="{{ route('career.index', array_merge(request()->except('type'), ['type' => $key])) }}"
                   class="{{ $tab === $key ? 'on' : '' }}"
                   id="tab-{{ $key }}">{{ $label }}</a>
            @endforeach
        </div>
    </nav>

    {{-- Success banner --}}
    @if(session('seeker_success'))
        <div id="sk-success" style="display:flex;align-items:center;gap:12px;background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;border-radius:12px;padding:14px 20px;margin-top:16px;font-size:14px;font-weight:600;color:#065f46;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#059669"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            {{ session('seeker_success') }}
            <button onclick="document.getElementById('sk-success').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#059669;font-size:18px;line-height:1;">&times;</button>
        </div>
    @endif

    {{-- Hero banner --}}
    <div class="sk-hero">
        <div class="sk-hero-text">
            <h1>Direktori Pencari Kerja</h1>
            <p>Temukan kandidat berbakat atau daftarkan dirimu agar perusahaan bisa menemukanmu lebih mudah.</p>
        </div>
        <a href="{{ route('career.seeker.create') }}" class="sk-hero-btn" id="btn-daftar-dirimu">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Daftarkan Dirimu
        </a>
    </div>

    {{-- 2-Column Layout with Sidebar --}}
    <div class="cr-layout">
        @include('career._seeker-filters')

        <div class="cr-main">
            <button type="button" class="cr-filter-toggle" id="cr-filter-toggle">Tampilkan Filter</button>

            <div class="cr-toolbar">
                <p class="cr-count"><strong>{{ $seekers->total() }}</strong> pencari kerja tersedia</p>
                <form class="cr-search" method="GET" action="{{ route('career.index') }}">
                    <input type="hidden" name="type" value="kerja">
                    @foreach((array) request('experience', []) as $v)
                        <input type="hidden" name="experience[]" value="{{ $v }}">
                    @endforeach
                    @foreach((array) request('availability', []) as $v)
                        <input type="hidden" name="availability[]" value="{{ $v }}">
                    @endforeach
                    @foreach((array) request('skills', []) as $v)
                        <input type="hidden" name="skills[]" value="{{ $v }}">
                    @endforeach
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, posisi, lokasi..." id="seeker-search">
                </form>
            </div>

            @if(count($seekers) === 0)
                <div class="cr-empty">
                    <h3>Tidak ada pencari kerja yang sesuai</h3>
                    <p>Hapus filter pencarian atau coba kata kunci lain.</p>
                </div>
            @else
                <div class="cr-list">
                    @foreach($seekers as $seeker)
                        @include('career._seeker-card', ['seeker' => $seeker])
                    @endforeach
                </div>

                {{-- Pagination (Centered) --}}
                @if($seekers->hasPages())
                    <div class="cr-pagination sk-pagination mt-10 mb-4 flex justify-center w-full">
                        {{ $seekers->withQueryString()->links('vendor.pagination.tailwind') }}
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
