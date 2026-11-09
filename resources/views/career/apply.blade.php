@extends('layouts.app')

@section('title', 'Lamar — ' . $job['title'] . ' · IndoTech')

@section('content')
<style>
    /* ── page shell ── */
    .ap-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 36px 32px 64px;
    }
    /* ── breadcrumb ── */
    .ap-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .ap-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 600; }
    .ap-breadcrumb a:hover { text-decoration: underline; }

    /* ── two-column layout ── */
    .ap-grid {
        display: grid;
        grid-template-columns: 310px 1fr;
        gap: 32px;
        align-items: start;
    }

    /* ── sidebar job card ── */
    .ap-job-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        position: sticky;
        top: 80px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .ap-job-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
    }
    .ap-job-body { padding: 20px; }
    .ap-job-logo {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: #fff;
        margin-bottom: 12px;
    }
    .ap-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 99px;
        margin-bottom: 10px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .ap-tag.jobs      { background: #dbeafe; color: #2563eb; }
    .ap-tag.internship{ background: #fef3c7; color: #d97706; }
    .ap-tag.freelance { background: #fce7f3; color: #db2777; }
    .ap-tag.remote    { background: #d1fae5; color: #059669; }
    .ap-tag.graduate  { background: #ede9fe; color: #7c3aed; }
    .ap-job-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.35;
    }
    .ap-job-company { font-size: 13px; color: #64748b; margin: 0 0 14px; }
    .ap-meta-row { display: flex; flex-direction: column; gap: 8px; }
    .ap-meta-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #475569; }
    .ap-meta-item svg { color: #94a3b8; flex-shrink: 0; }
    .ap-divider { height: 1px; background: #f1f5f9; margin: 16px 0; }
    .ap-deadline {
        font-size: 12px;
        font-weight: 600;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ap-skills-wrap { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 14px; }
    .ap-chip {
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 3px 8px;
    }

    /* ── main content panel ── */
    .ap-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .ap-panel-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .ap-panel-sub { font-size: 14px; color: #64748b; margin: 0 0 28px; }

    /* ── premium tabs nav ── */
    .ap-nav-tabs {
        display: flex;
        gap: 20px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 32px;
    }
    .ap-nav-tab {
        background: none;
        border: none;
        padding: 10px 4px 14px;
        font-size: 15px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -2px;
        transition: color 0.15s, border-color 0.15s;
        font-family: inherit;
    }
    .ap-nav-tab:hover { color: #2563eb; }
    .ap-nav-tab.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    /* tab wrapper */
    .ap-tab-content { display: none; }
    .ap-tab-content.active { display: block; }

    /* job details view */
    .ap-detail-section {
        margin-bottom: 28px;
    }
    .ap-detail-subtitle {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ap-detail-subtitle svg { color: #2563eb; }
    .ap-detail-text {
        font-size: 14.5px;
        line-height: 1.75;
        color: #475569;
        margin: 0;
    }
    .ap-detail-list {
        margin: 0;
        padding-left: 20px;
        color: #475569;
        font-size: 14.5px;
        line-height: 1.8;
    }
    .ap-detail-list li {
        margin-bottom: 10px;
    }

    /* progress bar */
    .ap-progress { display: flex; gap: 4px; margin-bottom: 32px; }
    .ap-progress-step {
        flex: 1;
        height: 4px;
        background: #e2e8f0;
        border-radius: 99px;
        transition: background 0.3s;
    }
    .ap-progress-step.done { background: #2563eb; }

    /* section */
    .ap-section { margin-bottom: 32px; }
    .ap-section-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin: 0 0 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
    }

    /* fields */
    .ap-field { margin-bottom: 20px; }
    .ap-label { display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
    .ap-label span { color: #dc2626; margin-left: 2px; }
    .ap-input, .ap-textarea {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: #0f172a;
        font-family: inherit;
        background: #fafbfc;
        transition: border-color 0.15s, box-shadow 0.15s;
        outline: none;
        box-sizing: border-box;
    }
    .ap-input:focus, .ap-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        background: #fff;
    }
    .ap-input.is-error, .ap-textarea.is-error { border-color: #dc2626; }
    .ap-error-msg { font-size: 12px; color: #dc2626; margin-top: 5px; }
    .ap-textarea { resize: vertical; min-height: 160px; line-height: 1.65; }
    .ap-hint { font-size: 12px; color: #94a3b8; margin-top: 5px; }
    .ap-char-counter { font-size: 12px; color: #94a3b8; text-align: right; margin-top: 4px; transition: color 0.15s; }
    .ap-char-counter.warn { color: #f59e0b; }
    .ap-char-counter.over { color: #dc2626; }
    .ap-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* dropzone */
    .ap-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
        position: relative;
        background: #fafbfc;
    }
    .ap-dropzone:hover, .ap-dropzone.drag-over { border-color: #2563eb; background: #eff6ff; }
    .ap-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    .ap-dropzone-icon { color: #94a3b8; margin-bottom: 8px; }
    .ap-dropzone-label { font-size: 14px; font-weight: 600; color: #334155; }
    .ap-dropzone-label span { color: #2563eb; }
    .ap-dropzone-hint { font-size: 12px; color: #94a3b8; margin-top: 4px; }
    .ap-file-selected {
        display: none;
        align-items: center;
        gap: 10px;
        justify-content: center;
        font-size: 13.5px;
        font-weight: 600;
        color: #059669;
        margin-top: 10px;
    }

    /* consent */
    .ap-consent {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: #475569;
        margin-bottom: 24px;
        padding: 14px 16px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }
    .ap-consent input[type="checkbox"] { margin-top: 2px; accent-color: #2563eb; flex-shrink: 0; }

    /* server errors */
    .ap-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #991b1b;
    }
    .ap-alert ul { margin: 6px 0 0 16px; padding: 0; }
    .ap-alert li { margin-bottom: 4px; }

    /* submit */
    .ap-submit {
        width: 100%;
        padding: 14px 24px;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(37,99,235,0.35);
        transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
        font-family: inherit;
    }
    .ap-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(37,99,235,0.4); }
    .ap-submit:active { transform: translateY(0); }
    .ap-submit:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }

    @media (max-width: 860px) {
        .ap-grid { grid-template-columns: 1fr; }
        .ap-job-card { position: static; }
        .ap-panel { padding: 24px 20px; }
        .ap-row2 { grid-template-columns: 1fr; }
        .ap-wrap { padding: 24px 16px 48px; }
    }
</style>

<div class="ap-wrap">

    {{-- breadcrumb --}}
    <nav class="ap-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('career.index', ['type' => $job['category']]) }}">&#8592; Kembali ke Karir</a>
        <span>/</span>
        <span>{{ $job['company'] }}</span>
        <span>/</span>
        <span>{{ $job['title'] }}</span>
    </nav>

    <div class="ap-grid">

        {{-- ── sidebar ── --}}
        <aside>
            <div class="ap-job-card">
                @php
                    $companyLogo = $job->company_logo ?? $job->logo_url ?? $job['logo_url'] ?? null;
                    $bannerImage = $job->image ?? $job['image'] ?? null;
                    if ($bannerImage === $companyLogo) {
                        $bannerImage = null;
                    }
                @endphp
                @if (!empty($bannerImage))
                <img
                    src="{{ $bannerImage }}"
                    alt="{{ $job['company'] }}"
                    class="ap-job-img"
                >
                @endif
                <div class="ap-job-body">
                    @if (!empty($companyLogo))
                        <div class="ap-job-logo" style="background: #ffffff; overflow: hidden; border: 1px solid #e2e8f0; padding: 0;">
                            <img src="{{ $companyLogo }}" alt="Logo {{ $job['company'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 9px; display: block;">
                        </div>
                    @else
                        <div class="ap-job-logo" style="background: {{ $job['logo_color'] }}">{{ $job['logo_text'] }}</div>
                    @endif
                    @php
                        $tagMap   = ['jobs'=>'Jobs','internship'=>'Internship','freelance'=>'Freelance','remote'=>'Remote Work','graduate'=>'Graduate Job'];
                        $tagClass = $job['category'];
                        $tagLabel = $tagMap[$job['category']] ?? 'Jobs';
                    @endphp
                    <span class="ap-tag {{ $tagClass }}">{{ $tagLabel }}</span>
                    <h2 class="ap-job-title">{{ $job['title'] }}</h2>
                    <p class="ap-job-company">{{ $job['company'] }}</p>

                    <div class="ap-meta-row">
                        <span class="ap-meta-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            {{ $job['location'] }}
                        </span>
                        <span class="ap-meta-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            {{ $job['salary_range'] }}
                        </span>
                        <span class="ap-meta-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            {{ $job['job_type'] }} &middot; {{ $job['experience'] }}
                        </span>
                    </div>

                    <div class="ap-divider"></div>

                    <div class="ap-deadline">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                        Deadline: {{ $job['deadline'] }}
                    </div>

                    <div class="ap-skills-wrap">
                        @foreach($job['skills'] as $skill)
                            <span class="ap-chip">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── main panels ── --}}
        <main>
            <div class="ap-panel">
                <h1 class="ap-panel-title">{{ $job['title'] }}</h1>
                <p class="ap-panel-sub">{{ $job['company'] }} &middot; {{ $job['location'] }}</p>

                {{-- premium tabs nav --}}
                <div class="ap-nav-tabs">
                    <button type="button" class="ap-nav-tab active" id="tab-btn-detail" onclick="switchApplyTab('detail')">Detail Pekerjaan</button>
                    <button type="button" class="ap-nav-tab" id="tab-btn-form" onclick="switchApplyTab('form')">Formulir Lamaran</button>
                </div>

                {{-- ── TAB 1: JOB DETAILS ── --}}
                <div class="ap-tab-content active" id="tab-content-detail">
                    <div class="ap-detail-section">
                        <h3 class="ap-detail-subtitle">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            Deskripsi Lowongan
                        </h3>
                        <p class="ap-detail-text">{{ $job['description'] }}</p>
                    </div>

                    <div class="ap-detail-section">
                        <h3 class="ap-detail-subtitle">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Kualifikasi &amp; Persyaratan
                        </h3>
                        <ul class="ap-detail-list">
                            @if (!empty($job['requirements']) && is_array($job['requirements']))
                                @foreach ($job['requirements'] as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            @else
                                @foreach ($job['skills'] ?? [] as $skill)
                                    <li>Menguasai <strong>{{ $skill }}</strong>.</li>
                                @endforeach
                                <li>Kualifikasi tingkat pengalaman level <strong>{{ $job['experience'] }}</strong>.</li>
                                <li>Dapat bekerja secara kolaboratif dalam tim dan memiliki inisiatif tinggi.</li>
                                <li>Memiliki kemampuan analisa dan pemecahan masalah (troubleshooting) yang baik.</li>
                            @endif
                        </ul>
                    </div>

                    <div class="ap-detail-section">
                        <h3 class="ap-detail-subtitle">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 20.015A9.753 9.753 0 0 1 12 21.75c-5.385 0-9.75-4.365-9.75-9.75s4.365-9.75 9.75-9.75 9.75 4.365 9.75 9.75c0 1.956-.575 3.776-1.564 5.305m-3.436-1.84c.75-.75 1.72-1.28 2.804-1.51M9 10.5a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0Zm-6 9c0-3.314 3.134-6 7-6 1.834 0 3.497.607 4.743 1.589"/></svg>
                            Tanggung Jawab Pekerjaan
                        </h3>
                        <ul class="ap-detail-list">
                            @if (!empty($job['responsibilities']) && is_array($job['responsibilities']))
                                @foreach ($job['responsibilities'] as $resp)
                                    <li>{{ $resp }}</li>
                                @endforeach
                            @else
                                <li>Membangun, mengembangkan, serta memelihara kode program agar tetap bersih, teruji, dan scalable.</li>
                                <li>Melakukan troubleshooting dan perbaikan bug sistem yang didelegasikan.</li>
                                <li>Bekerja sama erat dengan tim Product Manager dan UI/UX Designer untuk merealisasikan rencana fitur.</li>
                                <li>Berpartisipasi aktif dalam kegiatan code review guna menjaga kualitas kode tim.</li>
                            @endif
                        </ul>
                    </div>

                    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                        <button type="button" class="ap-submit" style="width: auto; padding: 12px 32px;" onclick="switchApplyTab('form')">
                            Lamar Sekarang
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ── TAB 2: APPLICATION FORM ── --}}
                <div class="ap-tab-content" id="tab-content-form">
                    {{-- visual progress bar --}}
                    <div class="ap-progress" aria-hidden="true">
                        <div class="ap-progress-step done" id="prog-1"></div>
                        <div class="ap-progress-step" id="prog-2"></div>
                        <div class="ap-progress-step" id="prog-3"></div>
                    </div>

                    @if($errors->any())
                    <div class="ap-alert" role="alert">
                        <strong>Mohon perbaiki kesalahan berikut:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form
                        method="POST"
                        data-async
                        data-async-follow
                        action="{{ route('career.apply.store', ['type' => $applyType, 'id' => $job['id']]) }}"
                        enctype="multipart/form-data"
                        id="ap-form"
                        novalidate
                    >
                        @csrf

                        {{-- section 1: personal info --}}
                        <div class="ap-section">
                            <h2 class="ap-section-title">1 &middot; Informasi Pribadi</h2>

                            <div class="ap-row2">
                                <div class="ap-field">
                                    <label for="full_name" class="ap-label">Nama Lengkap <span>*</span></label>
                                    <input
                                        type="text"
                                        id="full_name"
                                        name="full_name"
                                        class="ap-input{{ $errors->has('full_name') ? ' is-error' : '' }}"
                                        value="{{ old('full_name') }}"
                                        placeholder="Contoh: Budi Santoso"
                                        autocomplete="name"
                                        required
                                    >
                                    @error('full_name')
                                        <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="ap-field">
                                    <label for="email" class="ap-label">Email <span>*</span></label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="ap-input{{ $errors->has('email') ? ' is-error' : '' }}"
                                        value="{{ old('email') }}"
                                        placeholder="kamu@email.com"
                                        autocomplete="email"
                                        required
                                    >
                                    @error('email')
                                        <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="ap-field">
                                <label for="phone" class="ap-label">Nomor Telepon / WhatsApp <span>*</span></label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    class="ap-input{{ $errors->has('phone') ? ' is-error' : '' }}"
                                    value="{{ old('phone') }}"
                                    placeholder="+62 812-3456-7890"
                                    autocomplete="tel"
                                    required
                                >
                                @error('phone')
                                    <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- section 2: links --}}
                        <div class="ap-section">
                            <h2 class="ap-section-title">2 &middot; Profil &amp; Portfolio</h2>

                            <div class="ap-row2">
                                <div class="ap-field">
                                    <label for="linkedin" class="ap-label">LinkedIn URL</label>
                                    <input
                                        type="url"
                                        id="linkedin"
                                        name="linkedin"
                                        class="ap-input{{ $errors->has('linkedin') ? ' is-error' : '' }}"
                                        value="{{ old('linkedin') }}"
                                        placeholder="https://linkedin.com/in/username"
                                    >
                                    @error('linkedin')
                                        <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="ap-field">
                                    <label for="portfolio" class="ap-label">Portfolio / GitHub URL</label>
                                    <input
                                        type="url"
                                        id="portfolio"
                                        name="portfolio"
                                        class="ap-input{{ $errors->has('portfolio') ? ' is-error' : '' }}"
                                        value="{{ old('portfolio') }}"
                                        placeholder="https://github.com/username"
                                    >
                                    @error('portfolio')
                                        <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- section 3: cover letter + cv --}}
                        <div class="ap-section">
                            <h2 class="ap-section-title">3 &middot; Surat Lamaran &amp; CV</h2>

                            <div class="ap-field">
                                <label for="cover_letter" class="ap-label">Surat Lamaran <span>*</span></label>
                                <textarea
                                    id="cover_letter"
                                    name="cover_letter"
                                    class="ap-textarea{{ $errors->has('cover_letter') ? ' is-error' : '' }}"
                                    placeholder="Ceritakan mengapa kamu cocok untuk posisi ini. Sertakan pengalaman relevan, motivasi, dan kontribusi yang ingin kamu berikan..."
                                    maxlength="3000"
                                    required
                                >{{ old('cover_letter') }}</textarea>
                                <div class="ap-char-counter" id="cl-counter">0 / 3000 karakter</div>
                                <p class="ap-hint">Minimal 50 karakter. Gunakan bahasa yang jelas dan profesional.</p>
                                @error('cover_letter')
                                    <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="ap-field">
                                <label class="ap-label">Unggah CV / Resume <span>*</span></label>
                                <div class="ap-dropzone" id="ap-dropzone">
                                    <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" required>
                                    <div id="dz-placeholder">
                                        <div class="ap-dropzone-icon">
                                            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                        </div>
                                        <p class="ap-dropzone-label"><span>Klik untuk pilih file</span> atau drag &amp; drop di sini</p>
                                        <p class="ap-dropzone-hint">PDF, DOC, atau DOCX &middot; Maks. 5 MB</p>
                                    </div>
                                    <div class="ap-file-selected" id="dz-selected">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#059669"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        <span id="dz-filename"></span>
                                        <button type="button" id="dz-remove" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:13px;font-weight:600;">Hapus</button>
                                    </div>
                                </div>
                                @error('resume')
                                    <p class="ap-error-msg">&#9888; {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- consent --}}
                        <label class="ap-consent">
                            <input type="checkbox" name="consent" id="consent" required>
                            <span>
                                Saya menyetujui bahwa data yang saya berikan akurat dan setuju data ini
                                digunakan untuk proses rekrutmen sesuai
                                <a href="#" style="color:#2563eb;font-weight:600;">Kebijakan Privasi</a> IndoTech.
                            </span>
                        </label>

                        <button type="submit" class="ap-submit" id="ap-submit" disabled>
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                            Kirim Lamaran
                        </button>
                    </form>
                </div>
            </div>
        </main>

    </div>
</div>

<script>
    function switchApplyTab(tabId) {
        // Toggle tab buttons
        document.getElementById('tab-btn-detail').classList.toggle('active', tabId === 'detail');
        document.getElementById('tab-btn-form').classList.toggle('active', tabId === 'form');
        
        // Toggle tab contents
        document.getElementById('tab-content-detail').classList.toggle('active', tabId === 'detail');
        document.getElementById('tab-content-form').classList.toggle('active', tabId === 'form');

        // Scroll to top of panel smoothly
        document.querySelector('.ap-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Auto mount tab based on validation status
    var currentTab = '{{ $errors->any() ? "form" : "detail" }}';
    switchApplyTab(currentTab);

(function () {
    /* char counter */
    var ta = document.getElementById('cover_letter');
    var counter = document.getElementById('cl-counter');
    if (ta && counter) {
        function updateCounter() {
            var len = ta.value.length;
            counter.textContent = len + ' / 3000 karakter';
            counter.classList.toggle('warn', len >= 2500 && len < 3000);
            counter.classList.toggle('over', len >= 3000);
        }
        ta.addEventListener('input', updateCounter);
        updateCounter();
    }

    /* drag & drop */
    var dz    = document.getElementById('ap-dropzone');
    var fileIn = document.getElementById('resume');
    var ph    = document.getElementById('dz-placeholder');
    var sel   = document.getElementById('dz-selected');
    var fn    = document.getElementById('dz-filename');
    var rmBtn = document.getElementById('dz-remove');

    function showFile(name) { ph.style.display = 'none'; sel.style.display = 'flex'; fn.textContent = name; }
    function clearFile()    { fileIn.value = ''; ph.style.display = ''; sel.style.display = 'none'; }

    if (fileIn) { fileIn.addEventListener('change', function () { if (fileIn.files.length) { showFile(fileIn.files[0].name); } }); }
    if (rmBtn)  { rmBtn.addEventListener('click', function (e) { e.stopPropagation(); clearFile(); }); }
    if (dz) {
        dz.addEventListener('dragover',  function (e) { e.preventDefault(); dz.classList.add('drag-over'); });
        dz.addEventListener('dragleave', function ()  { dz.classList.remove('drag-over'); });
        dz.addEventListener('drop',      function (e) {
            e.preventDefault(); dz.classList.remove('drag-over');
            if (e.dataTransfer.files.length) { fileIn.files = e.dataTransfer.files; showFile(e.dataTransfer.files[0].name); }
        });
    }

    /* consent gates submit */
    var consent = document.getElementById('consent');
    var btn     = document.getElementById('ap-submit');
    if (consent && btn) {
        consent.addEventListener('change', function () { btn.disabled = !consent.checked; });
    }

    /* progress bar */
    var sections = [
        ['full_name','email','phone'],
        ['linkedin','portfolio'],
        ['cover_letter'],
    ];
    function updateProgress() {
        sections.forEach(function (fields, i) {
            var filled = fields.some(function (id) {
                var el = document.getElementById(id);
                if (!el) { return false; }
                if (el.type === 'file') { return el.files && el.files.length > 0; }
                return el.value.trim() !== '';
            });
            var step = document.getElementById('prog-' + (i + 1));
            if (step) { step.classList.toggle('done', filled); }
        });
    }
    document.querySelectorAll('.ap-input,.ap-textarea').forEach(function (el) { el.addEventListener('input', updateProgress); });
    if (fileIn) { fileIn.addEventListener('change', updateProgress); }
    updateProgress();
})();
</script>
@endsection

