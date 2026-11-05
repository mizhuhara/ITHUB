@extends('layouts.app')

@section('title', 'Buat Komunitas Baru — Komunitas Tech Indonesia')

@section('content')

{{-- ===== HERO & BREADCRUMB ===== --}}
<div class="cmty-create-hero">
    <div class="cmty-create-hero-inner">
        <a href="{{ route('community.index') }}" class="cmty-back-btn">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Komunitas
        </a>

        <div class="cmty-create-badge">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Ruang Kolaborasi Baru
        </div>

        <h1 class="cmty-create-title">Buat Komunitas Baru</h1>
        <p class="cmty-create-sub">
            Bentuk wadah kolaborasi teknologi, bagikan pengetahuan, dan kembangkan ekosistem digital bersama talenta di seluruh penjuru Indonesia.
        </p>
    </div>
</div>

{{-- ===== FORM CONTAINER ===== --}}
<div class="cmty-create-wrap">
    @if ($errors->any())
        <div class="cmty-alert-error" role="alert">
            <div class="cmty-alert-error-header">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Mohon periksa kembali form berikut. Terdapat beberapa kesalahan input:</span>
            </div>
            <ul class="cmty-alert-error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form data-async data-async-follow action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="cmty-form" id="create-community-form">
        @csrf

        {{-- Section 1: Informasi Dasar --}}
        <div class="cmty-form-card">
            <div class="cmty-form-section-title">
                <div class="cmty-section-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h2>Informasi Komunitas</h2>
                    <p>Tentukan identitas dan fokus utama komunitas yang Anda dirikan.</p>
                </div>
            </div>

            <div class="cmty-form-grid">
                {{-- Nama Komunitas --}}
                <div class="cmty-field-full">
                    <label for="name" class="cmty-label">
                        Nama Komunitas <span class="cmty-req">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           class="cmty-input @error('name') is-invalid @enderror"
                           placeholder="Contoh: Laravel Developer Indonesia, UI/UX Malang Network"
                           required
                           autocomplete="off">
                    <p class="cmty-hint">Pilih nama yang deskriptif dan mudah ditemukan oleh calon anggota.</p>
                    @error('name')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div class="cmty-field">
                    <label for="category" class="cmty-label">
                        Kategori <span class="cmty-req">*</span>
                    </label>
                    <div class="cmty-select-wrap">
                        <select id="category" name="category" class="cmty-select @error('category') is-invalid @enderror" required>
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        <div class="cmty-select-arrow">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('category')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bahasa Utama --}}
                <div class="cmty-field">
                    <label for="language" class="cmty-label">
                        Bahasa Utama <span class="cmty-req">*</span>
                    </label>
                    <div class="cmty-select-wrap">
                        <select id="language" name="language" class="cmty-select @error('language') is-invalid @enderror" required>
                            <option value="Bahasa Indonesia" {{ old('language', 'Bahasa Indonesia') === 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa Indonesia</option>
                            <option value="English" {{ old('language') === 'English' ? 'selected' : '' }}>English</option>
                            <option value="Bilingual (Indonesia & English)" {{ old('language') === 'Bilingual (Indonesia & English)' ? 'selected' : '' }}>Bilingual (Indonesia & English)</option>
                            <option value="Lainnya" {{ old('language') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <div class="cmty-select-arrow">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('language')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mode Komunitas (Online / Offline / Hybrid) --}}
                <div class="cmty-field">
                    <label for="mode" class="cmty-label">
                        Mode Komunitas <span class="cmty-req">*</span>
                    </label>
                    <div class="cmty-select-wrap">
                        <select id="mode" name="mode" class="cmty-select @error('mode') is-invalid @enderror" required>
                            <option value="online" {{ old('mode', 'online') === 'online' ? 'selected' : '' }}>Online (Kegiatan Daring)</option>
                            <option value="offline" {{ old('mode') === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka / Luring)</option>
                            <option value="hybrid" {{ old('mode') === 'hybrid' ? 'selected' : '' }}>Hybrid (Kombinasi Online &amp; Offline)</option>
                        </select>
                        <div class="cmty-select-arrow">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('mode')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Komunitas (Publik / Privat) --}}
                <div class="cmty-field-full">
                    <label class="cmty-label">
                        Jenis Komunitas <span class="cmty-req">*</span>
                    </label>
                    <div class="cmty-type-cards">
                        <label class="cmty-type-card {{ old('type', 'public') === 'public' ? 'active' : '' }}">
                            <input type="radio" name="type" value="public" {{ old('type', 'public') === 'public' ? 'checked' : '' }} class="cmty-type-radio">
                            <div class="cmty-type-icon public">
                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <div class="cmty-type-content">
                                <div class="cmty-type-head">
                                    <span class="cmty-type-title">Publik</span>
                                    <span class="cmty-type-badge">Terbuka</span>
                                </div>
                                <p class="cmty-type-desc">Siapa saja dapat menemukan, melihat aktivitas, serta bergabung dengan komunitas secara bebas tanpa persetujuan khusus.</p>
                            </div>
                        </label>

                        <label class="cmty-type-card {{ old('type') === 'private' ? 'active' : '' }}">
                            <input type="radio" name="type" value="private" {{ old('type') === 'private' ? 'checked' : '' }} class="cmty-type-radio">
                            <div class="cmty-type-icon private">
                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div class="cmty-type-content">
                                <div class="cmty-type-head">
                                    <span class="cmty-type-title">Privat</span>
                                    <span class="cmty-type-badge private">Terbatas</span>
                                </div>
                                <p class="cmty-type-desc">Hanya anggota yang telah disetujui yang dapat mengakses konten diskusi, berkas internal, dan forum komunitas.</p>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tag / Topik Komunitas --}}
                <div class="cmty-field-full">
                    <label for="tags" class="cmty-label">
                        Tag / Topik Komunitas <span class="cmty-opt">(Opsional)</span>
                    </label>
                    <input type="text"
                           id="tags"
                           name="tags"
                           class="cmty-input @error('tags') is-invalid @enderror"
                           value="{{ old('tags') }}"
                           placeholder="Contoh: UI/UX, Figma, Prototyping, Design System">
                    <p class="cmty-hint">Tambahkan tag topik yang relevan untuk mempermudah anggota menemukan komunitas Anda. Pisahkan dengan tanda koma (,).</p>
                    @error('tags')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Visual & Media (Logo & Banner) --}}
        <div class="cmty-form-card">
            <div class="cmty-form-section-title">
                <div class="cmty-section-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2>Logo &amp; Banner Komunitas</h2>
                    <p>Unggah aset grafis agar komunitas Anda mudah dikenali dan terlihat kredibel.</p>
                </div>
            </div>

            <div class="cmty-form-grid">
                {{-- Logo Komunitas --}}
                <div class="cmty-field">
                    <label class="cmty-label">
                        Logo Komunitas <span class="cmty-req">*</span>
                    </label>
                    <div class="cmty-upload-box @error('logo') is-invalid @enderror" id="logo-dropzone">
                        <input type="file"
                               id="logo"
                               name="logo"
                               accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                               class="cmty-file-input"
                               required>
                        <div class="cmty-upload-content" id="logo-prompt">
                            <div class="cmty-upload-icon-circle">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="cmty-upload-text"><strong>Klik untuk unggah</strong> atau drag &amp; drop</p>
                            <p class="cmty-upload-sub">PNG, JPG, WebP, atau SVG (Maks. 2MB)</p>
                        </div>
                        {{-- Preview --}}
                        <div class="cmty-preview-wrapper" id="logo-preview-wrap" style="display: none;">
                            <img src="" alt="Logo Preview" id="logo-preview-img" class="cmty-logo-preview">
                            <button type="button" class="cmty-remove-preview" id="logo-remove-btn" title="Hapus foto">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('logo')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Banner Komunitas (Opsional) --}}
                <div class="cmty-field">
                    <label class="cmty-label">
                        Banner Komunitas <span class="cmty-opt">(Opsional)</span>
                    </label>
                    <div class="cmty-upload-box @error('banner') is-invalid @enderror" id="banner-dropzone">
                        <input type="file"
                               id="banner"
                               name="banner"
                               accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                               class="cmty-file-input">
                        <div class="cmty-upload-content" id="banner-prompt">
                            <div class="cmty-upload-icon-circle">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="cmty-upload-text"><strong>Klik untuk unggah</strong> gambar sampul</p>
                            <p class="cmty-upload-sub">Rasio 3:1 direkomendasikan (Maks. 4MB)</p>
                        </div>
                        {{-- Preview --}}
                        <div class="cmty-preview-wrapper" id="banner-preview-wrap" style="display: none;">
                            <img src="" alt="Banner Preview" id="banner-preview-img" class="cmty-banner-preview">
                            <button type="button" class="cmty-remove-preview" id="banner-remove-btn" title="Hapus banner">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('banner')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Deskripsi & Aturan --}}
        <div class="cmty-form-card">
            <div class="cmty-form-section-title">
                <div class="cmty-section-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2>Deskripsi &amp; Tata Tertib</h2>
                    <p>Berikan penjelasan lengkap tentang agenda dan panduan interaksi komunitas.</p>
                </div>
            </div>

            <div class="cmty-form-grid">
                {{-- Deskripsi Komunitas --}}
                <div class="cmty-field-full">
                    <label for="description" class="cmty-label">
                        Deskripsi Komunitas <span class="cmty-req">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="5"
                              class="cmty-textarea @error('description') is-invalid @enderror"
                              placeholder="Ceritakan tentang visi komunitas, topik apa saja yang sering dibahas, jadwal temu rutin, serta manfaat yang didapatkan anggota..."
                              required>{{ old('description') }}</textarea>
                    <p class="cmty-hint">Deskripsi yang jelas akan menarik minat talenta teknologi yang tepat untuk bergabung.</p>
                    @error('description')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Aturan Komunitas (Opsional) --}}
                <div class="cmty-field-full">
                    <label for="rules" class="cmty-label">
                        Aturan Komunitas <span class="cmty-opt">(Opsional)</span>
                    </label>
                    <textarea id="rules"
                              name="rules"
                              rows="4"
                              class="cmty-textarea @error('rules') is-invalid @enderror"
                              placeholder="Contoh:&#10;1. Saling menghargai dan bertutur kata sopan antaranggota.&#10;2. Dilarang menyebarkan hoaks, konten SARA, atau spam promosi tanpa izin.&#10;3. Bagikan solusi dan diskusi yang relevan dengan topik komunitas.">{{ old('rules') }}</textarea>
                    <p class="cmty-hint">Tata tertib membantu menjaga ruang diskusi tetap ramah, inklusif, dan produktif.</p>
                    @error('rules')
                        <p class="cmty-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="cmty-form-actions">
            <a href="{{ route('community.index') }}" class="cmty-btn-cancel">
                Batal
            </a>
            <button type="submit" class="cmty-btn-submit" id="submit-community-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Komunitas</span>
            </button>
        </div>
    </form>
</div>

<style>
/* ============================================
   COMMUNITY CREATE PAGE STYLES
   ============================================ */

/* Hero */
.cmty-create-hero {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 36px 24px 44px;
    text-align: center;
}
.cmty-create-hero-inner {
    max-width: 720px;
    margin: 0 auto;
    position: relative;
}
.cmty-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    margin-bottom: 18px;
    padding: 6px 12px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.15s ease;
}
.cmty-back-btn:hover {
    color: #2563eb;
    background: #eff6ff;
    border-color: #bfdbfe;
    transform: translateX(-2px);
}
.cmty-create-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    font-size: 12.5px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 999px;
    margin-bottom: 14px;
}
.cmty-create-title {
    font-size: 34px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.25;
    letter-spacing: -0.5px;
    margin: 0 0 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
@media (min-width: 640px) { .cmty-create-title { font-size: 40px; } }
.cmty-create-sub {
    font-size: 15px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 auto;
    max-width: 580px;
}

/* Page Wrap */
.cmty-create-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 36px 20px 80px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Alert Error */
.cmty-alert-error {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 24px;
    color: #991b1b;
}
.cmty-alert-error-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 8px;
}
.cmty-alert-error-header svg { color: #dc2626; flex-shrink: 0; }
.cmty-alert-error-list {
    margin: 0;
    padding-left: 28px;
    font-size: 13.5px;
    line-height: 1.6;
}

/* Form Shell */
.cmty-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Form Card */
.cmty-form-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: border-color 0.15s ease;
}
@media (min-width: 640px) {
    .cmty-form-card { padding: 32px; }
}

.cmty-form-section-title {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f1f5f9;
}
.cmty-section-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cmty-form-section-title h2 {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 3px;
    letter-spacing: -0.2px;
}
.cmty-form-section-title p {
    font-size: 13.5px;
    color: #64748b;
    margin: 0;
}

/* Grid & Fields */
.cmty-form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}
@media (min-width: 640px) {
    .cmty-form-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px 24px;
    }
}
.cmty-field-full {
    grid-column: 1 / -1;
}
.cmty-field {
    display: flex;
    flex-direction: column;
}

/* Labels & Hints */
.cmty-label {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 7px;
}
.cmty-req { color: #ef4444; margin-left: 2px; }
.cmty-opt { color: #94a3b8; font-weight: 500; font-size: 12px; margin-left: 4px; }
.cmty-hint {
    font-size: 12px;
    color: #64748b;
    margin: 6px 0 0;
    line-height: 1.4;
}
.cmty-error {
    font-size: 12.5px;
    color: #dc2626;
    margin: 6px 0 0;
    font-weight: 600;
}

/* Inputs & Select */
.cmty-input,
.cmty-select,
.cmty-textarea {
    width: 100%;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 11px 16px;
    font-size: 14px;
    color: #0f172a;
    font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none;
    transition: all 0.15s ease;
    box-sizing: border-box;
}
.cmty-input::placeholder,
.cmty-textarea::placeholder {
    color: #94a3b8;
}
.cmty-input:focus,
.cmty-select:focus,
.cmty-textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
}
.cmty-input.is-invalid,
.cmty-select.is-invalid,
.cmty-textarea.is-invalid,
.cmty-upload-box.is-invalid {
    border-color: #ef4444;
    background: #fffafa;
}
.cmty-input.is-invalid:focus,
.cmty-select.is-invalid:focus,
.cmty-textarea.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
}

/* Input Icon Wrap */
.cmty-input-icon-wrap {
    position: relative;
    width: 100%;
}
.cmty-input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}
.cmty-input.has-icon {
    padding-left: 42px;
}

/* Select Wrap */
.cmty-select-wrap {
    position: relative;
    width: 100%;
}
.cmty-select {
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    padding-right: 40px;
}
.cmty-select-arrow {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    pointer-events: none;
}

/* Textarea */
.cmty-textarea {
    resize: vertical;
    min-height: 110px;
    line-height: 1.6;
}

/* Type Cards (Public vs Private) */
.cmty-type-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
}
@media (min-width: 640px) {
    .cmty-type-cards { grid-template-columns: 1fr 1fr; }
}
.cmty-type-card {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 20px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.18s ease;
}
.cmty-type-card:hover {
    border-color: #cbd5e1;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
}
.cmty-type-card.active {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.08);
}
.cmty-type-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.cmty-type-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.cmty-type-icon.public {
    background: #dbeafe;
    color: #2563eb;
}
.cmty-type-icon.private {
    background: #f1f5f9;
    color: #475569;
}
.cmty-type-card.active .cmty-type-icon.private {
    background: #e0e7ff;
    color: #4f46e5;
}
.cmty-type-content { flex: 1; }
.cmty-type-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}
.cmty-type-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}
.cmty-type-badge {
    font-size: 11px;
    font-weight: 700;
    background: #dcfce7;
    color: #15803d;
    padding: 2px 8px;
    border-radius: 6px;
}
.cmty-type-badge.private {
    background: #f1f5f9;
    color: #475569;
}
.cmty-type-card.active .cmty-type-badge.private {
    background: #ede9fe;
    color: #6d28d9;
}
.cmty-type-desc {
    font-size: 12.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

/* File Upload Boxes */
.cmty-upload-box {
    position: relative;
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    background: #f8fafc;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    overflow: hidden;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cmty-upload-box:hover {
    border-color: #2563eb;
    background: #eff6ff;
}
.cmty-file-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
    z-index: 2;
}
.cmty-upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.cmty-upload-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.cmty-upload-text {
    font-size: 13.5px;
    color: #334155;
    margin: 0;
}
.cmty-upload-text strong {
    color: #2563eb;
}
.cmty-upload-sub {
    font-size: 11.5px;
    color: #94a3b8;
    margin: 0;
}

/* Image Preview Inside Box */
.cmty-preview-wrapper {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 3;
}
.cmty-logo-preview {
    width: 88px;
    height: 88px;
    border-radius: 16px;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.cmty-banner-preview {
    width: 100%;
    max-height: 140px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.cmty-remove-preview {
    position: absolute;
    top: -6px;
    right: calc(50% - 56px);
    background: #dc2626;
    color: #ffffff;
    border: 2px solid #ffffff;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: transform 0.15s ease, background 0.15s;
    z-index: 4;
}
#banner-preview-wrap .cmty-remove-preview {
    right: 8px;
    top: 8px;
}
.cmty-remove-preview:hover {
    background: #b91c1c;
    transform: scale(1.1);
}

/* Actions Footer */
.cmty-form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    margin-top: 8px;
    padding-top: 16px;
}
.cmty-btn-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.15s ease;
    cursor: pointer;
}
.cmty-btn-cancel:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.cmty-btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    background: #2563eb;
    border: 1.5px solid #1d4ed8;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
    transition: all 0.15s ease;
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cmty-btn-submit:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
}
.cmty-btn-submit:active {
    transform: translateY(0);
}
.cmty-btn-submit.is-submitting {
    opacity: 0.8;
    pointer-events: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Radio Cards for Jenis Komunitas
    const typeCards = document.querySelectorAll('.cmty-type-card');
    typeCards.forEach(card => {
        card.addEventListener('click', function () {
            typeCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // 2. Logo Upload Preview
    const logoInput = document.getElementById('logo');
    const logoPrompt = document.getElementById('logo-prompt');
    const logoWrap = document.getElementById('logo-preview-wrap');
    const logoImg = document.getElementById('logo-preview-img');
    const logoRemove = document.getElementById('logo-remove-btn');

    if (logoInput) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    logoImg.src = evt.target.result;
                    logoPrompt.style.display = 'none';
                    logoWrap.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (logoRemove) {
        logoRemove.addEventListener('click', function (e) {
            e.stopPropagation();
            logoInput.value = '';
            logoImg.src = '';
            logoWrap.style.display = 'none';
            logoPrompt.style.display = 'flex';
        });
    }

    // 3. Banner Upload Preview
    const bannerInput = document.getElementById('banner');
    const bannerPrompt = document.getElementById('banner-prompt');
    const bannerWrap = document.getElementById('banner-preview-wrap');
    const bannerImg = document.getElementById('banner-preview-img');
    const bannerRemove = document.getElementById('banner-remove-btn');

    if (bannerInput) {
        bannerInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    bannerImg.src = evt.target.result;
                    bannerPrompt.style.display = 'none';
                    bannerWrap.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (bannerRemove) {
        bannerRemove.addEventListener('click', function (e) {
            e.stopPropagation();
            bannerInput.value = '';
            bannerImg.src = '';
            bannerWrap.style.display = 'none';
            bannerPrompt.style.display = 'flex';
        });
    }

    // 4. Form Submit State
    const form = document.getElementById('create-community-form');
    const submitBtn = document.getElementById('submit-community-btn');
    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.classList.add('is-submitting');
            submitBtn.querySelector('span').textContent = 'Membuat Komunitas...';
        });
    }
});
</script>

@endsection

