@extends('layouts.app')

@section('title', $seeker->name . ' — ' . $seeker->job_title . ' · IndoTech')

@section('content')
<style>
    /* ── page shell ── */
    .sks-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 36px 32px 64px;
    }

    /* ── breadcrumb ── */
    .sks-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .sks-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 600; }
    .sks-breadcrumb a:hover { text-decoration: underline; }

    /* ── two-column layout ── */
    .sks-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
        align-items: start;
    }

    /* ── sidebar profile card ── */
    .sks-sidebar-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        position: sticky;
        top: 80px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .sks-sidebar-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        padding: 28px 24px 20px;
        text-align: center;
        color: #fff;
    }
    .sks-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 24px;
        color: #fff;
        border: 3px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .sks-name {
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 4px;
    }
    .sks-title {
        font-size: 13px;
        color: #bfdbfe;
        font-weight: 600;
        margin: 0;
    }
    .sks-sidebar-body {
        padding: 20px 24px 24px;
    }
    .sks-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 99px;
        margin-bottom: 14px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .sks-tag.segera { background: #dcfce7; color: #15803d; }
    .sks-tag.bulan  { background: #fef3c7; color: #b45309; }
    .sks-tag.lebih  { background: #f1f5f9; color: #64748b; }

    .sks-meta-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0 0 18px;
        padding: 0;
        list-style: none;
    }
    .sks-meta-item {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        color: #475569;
    }
    .sks-meta-item svg {
        width: 15px;
        height: 15px;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .sks-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 18px 0;
    }

    /* skills cloud */
    .sks-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }
    .sks-chip {
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 3px 9px;
    }

    /* cta buttons */
    .sks-contact-btns {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 18px;
    }
    .sks-btn-wa {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 16px;
        background: #16a34a;
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 9px;
        text-decoration: none;
        transition: background 0.15s;
        box-sizing: border-box;
    }
    .sks-btn-wa:hover { background: #15803d; color: #fff; text-decoration: none; }
    .sks-btn-mail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px 16px;
        background: #2563eb;
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 9px;
        text-decoration: none;
        transition: background 0.15s;
        box-sizing: border-box;
    }
    .sks-btn-mail:hover { background: #1d4ed8; color: #fff; text-decoration: none; }

    /* ── main content panel ── */
    .sks-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .sks-panel-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 28px;
    }
    .sks-panel-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .sks-panel-sub {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* detail section */
    .sks-section {
        margin-bottom: 32px;
    }
    .sks-section:last-child {
        margin-bottom: 0;
    }
    .sks-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sks-section-title svg { color: #2563eb; }
    .sks-section-text {
        font-size: 14.5px;
        line-height: 1.75;
        color: #475569;
        margin: 0;
        white-space: pre-line;
    }

    /* info grid */
    .sks-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }
    .sks-info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .sks-info-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0 0 4px;
    }
    .sks-info-val {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    /* social links */
    .sks-links-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .sks-ext-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s;
    }
    .sks-ext-link:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
        text-decoration: none;
    }

    /* banner recruit cta */
    .sks-recruit-cta {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 24px;
        margin-top: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .sks-recruit-cta h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1e3a8a;
        margin: 0 0 4px;
    }
    .sks-recruit-cta p {
        font-size: 13px;
        color: #3b82f6;
        margin: 0;
    }

    @media (max-width: 860px) {
        .sks-grid { grid-template-columns: 1fr; }
        .sks-sidebar-card { position: static; }
        .sks-panel { padding: 24px 20px; }
        .sks-wrap { padding: 24px 16px 48px; }
        .sks-info-grid { grid-template-columns: 1fr; }
    }
</style>

@php
    $initials = collect(explode(' ', $seeker->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
    $colors = ['#2563eb','#7c3aed','#db2777','#059669','#d97706','#0891b2','#dc2626','#65a30d'];
    $avatarColor = $colors[$seeker->id % count($colors)];
    $availClass = match($seeker->availability) {
        'Segera' => 'segera',
        'Bulan Depan' => 'bulan',
        default => 'lebih',
    };
    $cleanPhone = $seeker->phone ? preg_replace('/[^0-9]/', '', $seeker->phone) : null;
    if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
        $cleanPhone = '62' . substr($cleanPhone, 1);
    }
@endphp

<div class="sks-wrap">
    {{-- breadcrumb --}}
    <nav class="sks-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('career.index', ['type' => 'kerja']) }}">&#8592; Kembali ke Siap Kerja</a>
        <span>/</span>
        <span>Pencari Kerja</span>
        <span>/</span>
        <span>{{ $seeker->name }}</span>
    </nav>

    {{-- Success banner --}}
    @if(session('seeker_success'))
        <div style="display:flex;align-items:center;gap:12px;background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;border-radius:12px;padding:14px 20px;margin-bottom:24px;font-size:14px;font-weight:600;color:#065f46;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#059669"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            {{ session('seeker_success') }}
        </div>
    @endif

    <div class="sks-grid">
        {{-- ── Left Sidebar Profile Card ── --}}
        <aside>
            <div class="sks-sidebar-card">
                <div class="sks-sidebar-header">
                    @if($seeker->photo_url)
                        <img src="{{ $seeker->photo_url }}" alt="{{ $seeker->name }}" class="sks-avatar" style="object-fit:cover;">
                    @else
                        <div class="sks-avatar" style="background: {{ $avatarColor }}">{{ $initials }}</div>
                    @endif
                    <h2 class="sks-name" style="display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;">
                        <span>{{ $seeker->name }}</span>
                        @if($seeker->user)
                            <span title="Akun Terverifikasi IndoTech" style="display:inline-flex;align-items:center;color:#60a5fa;font-size:11px;font-weight:600;background:rgba(255,255,255,0.15);padding:2px 8px;border-radius:99px;border:1px solid rgba(255,255,255,0.25);">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-right:3px"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                Terverifikasi
                            </span>
                        @endif
                    </h2>
                    <p class="sks-title">{{ $seeker->job_title }}</p>
                </div>
                <div class="sks-sidebar-body">
                    <div style="display:flex;gap:6px;margin-bottom:14px;">
                        <span class="sks-tag {{ $availClass }}">{{ $seeker->availability }}</span>
                        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:#eff6ff;color:#2563eb;text-transform:uppercase;">{{ $seeker->experience_level }}</span>
                    </div>

                    <ul class="sks-meta-list">
                        @if($seeker->location)
                        <li class="sks-meta-item">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span>{{ $seeker->location }}</span>
                        </li>
                        @endif
                        @if($seeker->expected_salary)
                        <li class="sks-meta-item">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span>{{ $seeker->expected_salary }}</span>
                        </li>
                        @endif
                        <li class="sks-meta-item">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            <span>Bergabung {{ $seeker->created_at->isoFormat('D MMMM Y') }}</span>
                        </li>
                    </ul>

                    @if(!empty($seeker->skills))
                        <div class="sks-divider"></div>
                        <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;">Keahlian Utama</div>
                        <div class="sks-skills">
                            @foreach($seeker->skills as $skill)
                                <span class="sks-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="sks-divider"></div>
                    <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;">Hubungi Kandidat</div>
                    <div class="sks-contact-btns">
                        @if($cleanPhone)
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode('Halo ' . $seeker->name . ', kami melihat profil Anda di IndoTech untuk posisi ' . $seeker->job_title . '.') }}"
                               target="_blank" rel="noopener" class="sks-btn-wa">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.592 2.654-.696c1.02.582 1.83.896 2.806.897l.004-.002c3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.77-5.772-5.772zm3.374 8.243c-.145.409-.837.77-1.164.819-.327.049-.757.067-1.229-.083-.346-.11-1.398-.517-2.404-1.413-1.304-1.16-1.92-2.316-2.065-2.562-.145-.246-.016-.379.107-.502.11-.11.246-.287.369-.431.123-.145.164-.246.246-.41.082-.164.041-.308-.021-.431-.061-.123-.553-1.332-.758-1.825-.199-.48-.401-.415-.553-.423l-.472-.008c-.164 0-.431.061-.656.308-.225.246-.861.841-.861 2.05s.881 2.378 1.004 2.542c.123.164 1.733 2.647 4.2 3.712.587.254 1.045.405 1.402.519.589.187 1.125.161 1.549.097.473-.071 1.455-.594 1.66-1.168.205-.574.205-1.066.144-1.168-.061-.103-.225-.164-.471-.287z"/></svg>
                                Hubungi via WhatsApp
                            </a>
                        @endif
                        <a href="mailto:{{ $seeker->email }}?subject={{ rawurlencode('Tawaran Pekerjaan: ' . $seeker->job_title . ' — IndoTech') }}"
                           class="sks-btn-mail">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            Kirim Email
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── Main Detail Panel ── --}}
        <main class="sks-panel">
            <div class="sks-panel-header">
                <h1 class="sks-panel-title">Profil Kandidat</h1>
                <p class="sks-panel-sub">Informasi lengkap kualifikasi, kemampuan, dan kesiapan kerja {{ $seeker->name }}.</p>
            </div>

            {{-- Tentang Saya --}}
            <section class="sks-section">
                <h2 class="sks-section-title">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    Tentang Kandidat
                </h2>
                @if($seeker->about)
                    <p class="sks-section-text">{{ $seeker->about }}</p>
                @else
                    <p class="sks-section-text" style="color:#94a3b8;font-style:italic;">Kandidat belum menambahkan deskripsi diri.</p>
                @endif
            </section>

            {{-- Ringkasan Kualifikasi --}}
            <section class="sks-section">
                <h2 class="sks-section-title">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    Kualifikasi & Kesiapan Kerja
                </h2>
                <div class="sks-info-grid">
                    <div class="sks-info-card">
                        <p class="sks-info-label">Posisi yang Dicari</p>
                        <p class="sks-info-val">{{ $seeker->job_title }}</p>
                    </div>
                    <div class="sks-info-card">
                        <p class="sks-info-label">Level Pengalaman</p>
                        <p class="sks-info-val">{{ $seeker->experience_level }} Level</p>
                    </div>
                    <div class="sks-info-card">
                        <p class="sks-info-label">Ketersediaan Mulai</p>
                        <p class="sks-info-val">{{ $seeker->availability }}</p>
                    </div>
                    <div class="sks-info-card">
                        <p class="sks-info-label">Ekspektasi Gaji</p>
                        <p class="sks-info-val">{{ $seeker->expected_salary ?? 'Dapat dinegosiasikan' }}</p>
                    </div>
                </div>
            </section>

            {{-- Link Portofolio & Jejaring --}}
            @if($seeker->portfolio_url || $seeker->linkedin_url)
            <section class="sks-section">
                <h2 class="sks-section-title">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                    Portofolio & Jejaring
                </h2>
                <div class="sks-links-row">
                    @if($seeker->portfolio_url)
                        <a href="{{ $seeker->portfolio_url }}" target="_blank" rel="noopener" class="sks-ext-link">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            Kunjungi Portofolio / Proyek
                        </a>
                    @endif
                    @if($seeker->linkedin_url)
                        <a href="{{ $seeker->linkedin_url }}" target="_blank" rel="noopener" class="sks-ext-link">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            Profil LinkedIn
                        </a>
                    @endif
                </div>
            </section>
            @endif

            {{-- Recruit CTA Box --}}
            <div class="sks-recruit-cta">
                <div>
                    <h3>Tertarik Merekrut {{ $seeker->name }}?</h3>
                    <p>Hubungi kandidat langsung melalui WhatsApp atau Email untuk jadwal wawancara.</p>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    @if($cleanPhone)
                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode('Halo ' . $seeker->name . ', kami dari [Nama Perusahaan] tertarik dengan profil Anda di IndoTech untuk posisi ' . $seeker->job_title . '.') }}"
                           target="_blank" rel="noopener"
                           style="display:inline-flex;align-items:center;gap:6px;background:#16a34a;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                            WhatsApp
                        </a>
                    @endif
                    <a href="mailto:{{ $seeker->email }}?subject={{ rawurlencode('Undangan Wawancara: ' . $seeker->job_title) }}"
                       style="display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                        Kirim Email
                    </a>
                </div>
            </div>

            <div style="margin-top:28px;padding-top:20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                <a href="{{ route('career.index', ['type' => 'kerja']) }}"
                   style="font-size:13.5px;font-weight:600;color:#64748b;text-decoration:none;">
                    &#8592; Kembali ke Direktori Siap Kerja
                </a>
            </div>
        </main>
    </div>
</div>
@endsection

