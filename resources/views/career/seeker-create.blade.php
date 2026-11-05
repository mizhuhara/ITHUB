@extends('layouts.app')

@section('title', 'Daftarkan Diri — Siap Kerja · IndoTech')

@section('content')
<style>
    /* ── page shell ── */
    .skc-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 36px 32px 64px;
    }

    /* ── breadcrumb ── */
    .skc-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .skc-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 600; }
    .skc-breadcrumb a:hover { text-decoration: underline; }

    /* ── two-column layout ── */
    .skc-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
        align-items: start;
    }

    /* ── sidebar benefit card ── */
    .skc-sidebar-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        position: sticky;
        top: 80px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .skc-sidebar-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        padding: 24px;
        color: #fff;
    }
    .skc-sidebar-banner h2 {
        font-size: 17px;
        font-weight: 800;
        margin: 0 0 6px;
        color: #fff;
    }
    .skc-sidebar-banner p {
        font-size: 13px;
        color: #bfdbfe;
        margin: 0;
        line-height: 1.5;
    }
    .skc-sidebar-body {
        padding: 22px 24px;
    }
    .skc-benefit-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .skc-benefit-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
    }
    .skc-benefit-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ── main content panel ── */
    .skc-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .skc-panel-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
    .skc-panel-sub { font-size: 14px; color: #64748b; margin: 0 0 28px; }

    /* form grid */
    .skc-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    .skc-form-grid .full { grid-column: 1 / -1; }
    .skc-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .skc-label span { color: #ef4444; }
    .skc-input {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        font-size: 14px;
        font-family: inherit;
        color: #0f172a;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }
    .skc-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }
    select.skc-input {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }
    textarea.skc-input {
        height: auto;
        padding: 12px 14px;
        resize: vertical;
        min-height: 100px;
    }
    .skc-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 5px;
    }

    /* server errors */
    .skc-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-size: 13.5px;
        color: #991b1b;
    }
    .skc-alert ul { margin: 6px 0 0 16px; padding: 0; }
    .skc-alert li { margin-bottom: 4px; }

    /* footer actions */
    .skc-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #f1f5f9;
    }
    .skc-btn-cancel {
        display: inline-flex;
        align-items: center;
        padding: 11px 22px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #f8fafc;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.15s;
    }
    .skc-btn-cancel:hover {
        background: #f1f5f9;
        color: #334155;
        text-decoration: none;
    }
    .skc-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 26px;
        border: none;
        border-radius: 9px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 14px rgba(37,99,235,0.3);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .skc-btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(37,99,235,0.4);
    }

    @media (max-width: 860px) {
        .skc-grid { grid-template-columns: 1fr; }
        .skc-sidebar-card { position: static; }
        .skc-panel { padding: 24px 20px; }
        .skc-form-grid { grid-template-columns: 1fr; }
        .skc-wrap { padding: 24px 16px 48px; }
    }
</style>

<div class="skc-wrap">
    {{-- breadcrumb --}}
    <nav class="skc-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('career.index', ['type' => 'kerja']) }}">&#8592; Kembali ke Siap Kerja</a>
        <span>/</span>
        <span>Pencari Kerja</span>
        <span>/</span>
        <span>Daftarkan Dirimu</span>
    </nav>

    <div class="skc-grid">
        {{-- ── Sidebar Info ── --}}
        <aside>
            <div class="skc-sidebar-card">
                <div class="skc-sidebar-banner">
                    <h2>Direktori Siap Kerja</h2>
                    <p>Buka peluang karir impianmu bersama ratusan mitra industri teknologi di IndoTech.</p>
                </div>
                <div class="skc-sidebar-body">
                    <ul class="skc-benefit-list">
                        <li class="skc-benefit-item">
                            <span class="skc-benefit-icon">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                            </span>
                            <div>
                                <strong>Dilihat Recruiter</strong>
                                <p style="margin:2px 0 0;font-size:12px;color:#64748b;">Profilmu dapat ditemukan langsung oleh perusahaan yang sedang aktif mencari kandidat.</p>
                            </div>
                        </li>
                        <li class="skc-benefit-item">
                            <span class="skc-benefit-icon">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-.974-.94 4.502 4.502 0 0 0 .97-2.616A7.95 7.95 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                            </span>
                            <div>
                                <strong>Kontak Langsung</strong>
                                <p style="margin:2px 0 0;font-size:12px;color:#64748b;">Perusahaan dapat langsung menghubungimu via WhatsApp atau Email tanpa perantara.</p>
                            </div>
                        </li>
                        <li class="skc-benefit-item">
                            <span class="skc-benefit-icon">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                            </span>
                            <div>
                                <strong>100% Gratis</strong>
                                <p style="margin:2px 0 0;font-size:12px;color:#64748b;">Pendaftaran terbuka bebas untuk umum, siswa SMK, mahasiswa, maupun profesional.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        {{-- ── Main Form Panel ── --}}
        <main class="skc-panel">
            <h1 class="skc-panel-title">Daftarkan Diri Sebagai Pencari Kerja</h1>
            <p class="skc-panel-sub">Lengkapi formulir di bawah ini agar recruiter dapat mengenal profil dan keahlianmu.</p>

            {{-- Server errors --}}
            @if($errors->any())
                <div class="skc-alert">
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data" data-async data-async-follow action="{{ route('career.seeker.store') }}">
                @csrf
                <div class="skc-form-grid">
                    {{-- Foto Profil --}}
                    <div class="full">
                        <label class="skc-label" for="photo">Foto Profil</label>
                        <div style="display:flex;align-items:center;gap:14px;">
                            @if(auth()->check() && auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar Saat Ini" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #2563eb;flex-shrink:0;">
                            @endif
                            <div style="flex:1;">
                                <input id="photo" name="photo" type="file" class="skc-input" accept="image/png,image/jpeg,image/jpg,image/webp" style="padding:8px 12px;height:auto;">
                                <p class="skc-hint">Format: JPG, JPEG, PNG, WEBP (Maksimal 2MB). @if(auth()->check() && auth()->user()->avatar_url) Biarkan kosong jika ingin otomatis memakai foto profil akun IndoTech Anda. @endif</p>
                            </div>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="skc-label" for="name">Nama Lengkap <span>*</span></label>
                        <input id="name" name="name" type="text" class="skc-input" value="{{ old('name', auth()->user()?->name) }}" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="skc-label" for="email">Email <span>*</span></label>
                        <input id="email" name="email" type="email" class="skc-input" value="{{ old('email', auth()->user()?->email) }}" placeholder="budi@example.com" required>
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="skc-label" for="phone">No. WhatsApp / HP</label>
                        <input id="phone" name="phone" type="text" class="skc-input" value="{{ old('phone', auth()->user()?->phone) }}" placeholder="+62 812 3456 7890">
                        <p class="skc-hint">Akan digunakan perusahaan untuk menghubungimu langsung.</p>
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="skc-label" for="location">Lokasi / Kota Domisili</label>
                        <input id="location" name="location" type="text" class="skc-input" value="{{ old('location', auth()->user()?->city ?? auth()->user()?->location) }}" placeholder="Contoh: Jakarta Selatan, Bandung...">
                    </div>

                    {{-- Posisi yang Dicari --}}
                    <div class="full">
                        <label class="skc-label" for="job_title">Posisi / Pekerjaan yang Dicari <span>*</span></label>
                        <input id="job_title" name="job_title" type="text" class="skc-input" value="{{ old('job_title') }}" placeholder="Contoh: Fullstack Web Developer, UI/UX Designer, Network Engineer" required>
                    </div>

                    {{-- Level Pengalaman --}}
                    <div>
                        <label class="skc-label" for="experience_level">Level Pengalaman <span>*</span></label>
                        <select id="experience_level" name="experience_level" class="skc-input" required>
                            <option value="">-- Pilih Level Pengalaman --</option>
                            <option value="Entry" {{ old('experience_level') === 'Entry' ? 'selected' : '' }}>Entry Level (0–2 tahun)</option>
                            <option value="Mid"   {{ old('experience_level') === 'Mid'   ? 'selected' : '' }}>Mid Level (2–5 tahun)</option>
                            <option value="Senior"{{ old('experience_level') === 'Senior'? 'selected' : '' }}>Senior Level (5+ tahun)</option>
                        </select>
                    </div>

                    {{-- Ekspektasi Gaji --}}
                    <div>
                        <label class="skc-label" for="expected_salary">Ekspektasi Gaji</label>
                        <input id="expected_salary" name="expected_salary" type="text" class="skc-input" value="{{ old('expected_salary') }}" placeholder="Contoh: 8–15 jt / bulan, Negosiasi">
                    </div>

                    {{-- Ketersediaan --}}
                    <div class="full">
                        <label class="skc-label" for="availability">Ketersediaan Mulai Kerja <span>*</span></label>
                        <select id="availability" name="availability" class="skc-input" required>
                            <option value="Segera"      {{ old('availability', 'Segera') === 'Segera'      ? 'selected' : '' }}>Segera (Bisa mulai dalam 1–2 minggu)</option>
                            <option value="Bulan Depan" {{ old('availability') === 'Bulan Depan' ? 'selected' : '' }}>Bulan Depan (Notice period 1 bulan)</option>
                            <option value="> 1 Bulan"  {{ old('availability') === '> 1 Bulan'  ? 'selected' : '' }}>> 1 Bulan</option>
                        </select>
                    </div>

                    {{-- Keahlian / Skills --}}
                    <div class="full">
                        <label class="skc-label" for="skills">Keahlian / Skills</label>
                        <input id="skills" name="skills" type="text" class="skc-input" value="{{ old('skills') }}" placeholder="Laravel, React, Figma, Python, MySQL, Git">
                        <p class="skc-hint">Pisahkan keahlian dengan tanda koma (,).</p>
                    </div>

                    {{-- Tentang Diri --}}
                    <div class="full">
                        <label class="skc-label" for="about">Deskripsi Diri / Ringkasan Profesional</label>
                        <textarea id="about" name="about" class="skc-input" placeholder="Ceritakan pengalamanmu, teknologi yang kamu kuasai, dan nilai tambah yang bisa kamu berikan kepada perusahaan...">{{ old('about') }}</textarea>
                    </div>

                    {{-- Link Portfolio --}}
                    <div>
                        <label class="skc-label" for="portfolio_url">Link Portofolio / GitHub / Website</label>
                        <input id="portfolio_url" name="portfolio_url" type="url" class="skc-input" value="{{ old('portfolio_url') }}" placeholder="https://github.com/username">
                    </div>

                    {{-- Link LinkedIn --}}
                    <div>
                        <label class="skc-label" for="linkedin_url">Link Profil LinkedIn</label>
                        <input id="linkedin_url" name="linkedin_url" type="url" class="skc-input" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="skc-actions">
                    <a href="{{ route('career.index', ['type' => 'kerja']) }}" class="skc-btn-cancel">Batal</a>
                    <button type="submit" class="skc-btn-submit">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                        </svg>
                        Daftarkan Profil
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection

