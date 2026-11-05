@extends('admin.layouts.app')

@section('title', 'Edit Informasi Universitas — IndoTech')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Summernote Lite CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .leaflet-container {
        font-family: inherit;
        z-index: 10 !important;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        padding: 4px;
    }
    .leaflet-popup-content {
        margin: 10px 14px;
        font-family: inherit;
        font-size: 12px;
        line-height: 1.5;
        color: #1e293b;
    }
    /* Summernote styling adjustments for clean Tailwind admin integration */
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        overflow: hidden !important;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 0 !important;
        padding: 8px 10px !important;
    }
    .note-toolbar .note-btn:not(.note-color-btn):not(.note-color-reset) {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        color: #334155 !important;
        padding: 5px 9px !important;
        font-size: 13px !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
    }
    .note-toolbar .note-btn:not(.note-color-btn):not(.note-color-reset):hover {
        background: #eff6ff !important;
        border-color: #93c5fd !important;
        color: #2563eb !important;
    }
    .note-toolbar .note-btn-group {
        gap: 2px !important;
    }
    .note-editable {
        font-family: inherit !important;
        font-size: 14px !important;
        color: #1e293b !important;
        line-height: 1.7 !important;
        padding: 18px !important;
        background-color: #ffffff !important;
    }
    .note-editable ul { list-style-type: disc !important; padding-left: 24px !important; }
    .note-editable ol { list-style-type: decimal !important; padding-left: 24px !important; }
    .note-editable h1 { font-size: 2rem !important; font-weight: 700 !important; margin-bottom: 0.5rem !important; }
    .note-editable h2 { font-size: 1.5rem !important; font-weight: 700 !important; margin-bottom: 0.5rem !important; }
    .note-editable h3 { font-size: 1.25rem !important; font-weight: 600 !important; margin-bottom: 0.5rem !important; }
    .note-editable p { margin-bottom: 0.75rem !important; }
    .note-editable blockquote { border-left: 4px solid #2563eb !important; padding-left: 16px !important; color: #64748b !important; font-style: italic !important; }
    .note-statusbar {
        border-top: 1px solid #f1f5f9 !important;
        background: #f8fafc !important;
    }
    .note-dropdown-menu {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
        padding: 6px !important;
        z-index: 1050 !important;
    }
    .note-dropdown-item {
        color: #334155 !important;
        padding: 6px 10px !important;
        border-radius: 6px !important;
        font-size: 13px !important;
    }
    .note-dropdown-item:hover {
        background: #eff6ff !important;
        color: #2563eb !important;
    }
    .note-modal { z-index: 1050 !important; }
    .note-modal-backdrop { z-index: 1040 !important; }
    .note-modal .modal-content {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
    }
    .note-modal .btn-primary {
        background: #2563eb !important;
        border-color: #2563eb !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 16px !important;
    }
    .note-color-palette .note-color-btn {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid rgba(0,0,0,0.15) !important;
        border-radius: 3px !important;
    }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('admin.univ.update', $univ->id) }}" enctype="multipart/form-data" data-async class="space-y-6 max-w-7xl mx-auto" id="univForm">
    @csrf
    @method('PUT')

    {{-- Top Back Link & Breadcrumb --}}
    <div>
        <a href="{{ route('admin.univ.index') }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 mb-2 transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Card 1: Basic Information --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                {{-- Card Header with Blue Icon --}}
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 text-slate-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900">Informasi Dasar</h2>
                        <p class="text-[12px] text-slate-500">Identitas utama, akreditasi, dan kontak universitas</p>
                    </div>
                </div>

                {{-- Fields Grid (2 cols) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- University Name --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Nama Universitas</label>
                        <input type="text" 
                               name="name" 
                               id="univNameInput"
                               value="{{ old('name', $univ['name']) }}" 
                               required
                               placeholder="Universitas Indonesia"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NPSN --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">NPSN</label>
                        <input type="text" 
                               name="npsn" 
                               value="{{ old('npsn', $univ['npsn'] ?? '') }}" 
                               required
                               placeholder="20109988"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('npsn')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- University Type --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tipe Institusi</label>
                        <div class="relative">
                            <select name="type" 
                                    class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer pr-10">
                                <option value="Negeri" {{ old('type', $univ['type'] ?? '') == 'Negeri' ? 'selected' : '' }}>Negeri (PTN)</option>
                                <option value="Swasta" {{ old('type', $univ['type'] ?? '') == 'Swasta' ? 'selected' : '' }}>Swasta (PTS)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Location (Kota / Wilayah di Indonesia) with Autocomplete --}}
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[13px] font-semibold text-slate-700">Kota / Wilayah (Indonesia)</label>
                            <span class="text-[10.5px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded-full">🇮🇩 Hanya Indonesia</span>
                        </div>
                        <input type="text" 
                               name="location" 
                               id="univLocationInput"
                               value="{{ old('location', $univ['location']) }}" 
                               required
                               autocomplete="off"
                               placeholder="Contoh: Depok, Jawa Barat"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        
                        {{-- Location suggestions dropdown --}}
                        <div id="locationSuggestions" class="hidden absolute top-full left-0 right-0 z-50 bg-white border border-slate-200 rounded-2xl shadow-xl mt-1.5 max-h-56 overflow-y-auto divide-y divide-slate-100"></div>

                        @error('location')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Accreditation --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Akreditasi</label>
                        <div class="relative">
                            <select name="accreditation" 
                                    class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer pr-10">
                                <option value="A" {{ old('accreditation', $univ['accreditation'] ?? '') == 'A' ? 'selected' : '' }}>A (Unggul)</option>
                                <option value="B" {{ old('accreditation', $univ['accreditation'] ?? '') == 'B' ? 'selected' : '' }}>B (Baik)</option>
                                <option value="C" {{ old('accreditation', $univ['accreditation'] ?? '') == 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                <option value="Belum Terakreditasi" {{ old('accreditation', $univ['accreditation'] ?? '') == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Website --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Website</label>
                        <input type="url" 
                               name="website" 
                               value="{{ old('website', $univ['website'] ?? '') }}" 
                               placeholder="https://www.ui.ac.id" 
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('website')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $univ['email'] ?? '') }}"
                               placeholder="info@universitas.ac.id"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Nomor Telepon</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', $univ['phone'] ?? '') }}"
                               placeholder="(021) 1234567"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Total Mahasiswa --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Total Mahasiswa</label>
                        <input type="number"
                               name="total_students"
                               value="{{ old('total_students', $univ['total_students'] ?? '') }}"
                               placeholder="12000"
                               min="0"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('total_students')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Total Fakultas --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Total Fakultas / Prodi</label>
                        <input type="number"
                               name="total_faculties"
                               value="{{ old('total_faculties', $univ['total_faculties'] ?? '') }}"
                               placeholder="10"
                               min="0"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('total_faculties')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tahun Berdiri --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tahun Berdiri</label>
                        <input type="number"
                               name="founded"
                               value="{{ old('founded', $univ['founded'] ?? '') }}"
                               placeholder="1950"
                               min="1900"
                               max="{{ date('Y') }}"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('founded')
                            <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card 2: University Description --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 text-slate-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900">Deskripsi Universitas</h2>
                        <p class="text-[12px] text-slate-500">Profil lengkap, fakultas, akreditasi prodi, dan keunggulan</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 mb-2">Profil, Fakultas & Keunggulan</label>
                    <textarea name="description"
                              id="univDescription">{{ old('description', $univ['description'] ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Card 3: IT Study Programs / Tags --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 text-slate-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900">Program Studi IT</h2>
                        <p class="text-[12px] text-slate-500">Tambahkan program studi / jurusan IT yang tersedia di universitas ini</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[13px] font-semibold text-slate-700">Daftar Program Studi</label>
                    <div class="flex gap-2">
                        <input type="text" id="tagInputField"
                               placeholder="Contoh: Teknik Informatika"
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addTag();}">
                        <button type="button" onclick="addTag()"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-bold rounded-lg transition shrink-0 shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Tambah
                        </button>
                    </div>
                    <div id="tagContainer" class="flex flex-wrap gap-2 min-h-[40px] pt-1">
                        @php
                            $existingTags = is_array($univ['tags'] ?? null) ? $univ['tags'] : [];
                        @endphp
                        @foreach($existingTags as $tag)
                            <span class="tag-item inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 text-[12.5px] font-semibold rounded-xl">
                                {{ $tag }}
                                <input type="hidden" name="tags[]" value="{{ $tag }}">
                                <button type="button" onclick="removeTag(this)" class="text-blue-400 hover:text-red-500 transition ml-0.5 leading-none">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <p class="text-[11.5px] text-slate-400">Tekan Enter atau klik tombol Tambah untuk menambahkan program studi. Klik × untuk menghapus.</p>
                </div>
            </div>

            {{-- Card 4: Campus Gallery --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-900">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-[16px] font-bold text-slate-900">Galeri Kampus</h2>
                                <span id="galleryCountBadge" class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">0 / 4 Foto</span>
                            </div>
                            <p class="text-[12px] text-slate-500">Unggah foto suasana kampus, laboratorium, dan fasilitas (Maksimal 4 foto)</p>
                        </div>
                    </div>

                    {{-- Upload Button Trigger --}}
                    <label for="galleryInput" id="galleryBtnTrigger" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-brand-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/80 px-3.5 py-2 rounded-lg transition cursor-pointer">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Tambah Foto
                    </label>
                    <input type="file" name="gallery[]" id="galleryInput" multiple accept="image/*" class="hidden">
                </div>

                {{-- Gallery Grid Container --}}
                <div id="galleryContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    @php
                        $existingPhotos = is_array($univ->gallery) ? $univ->gallery : [];
                    @endphp

                    @foreach($existingPhotos as $index => $photoUrl)
                        <div class="gallery-item relative aspect-square rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden group shadow-xs" data-type="existing">
                            <img src="{{ $photoUrl }}" alt="Galeri Kampus" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <input type="hidden" name="existing_gallery[]" value="{{ $photoUrl }}">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" onclick="removeGalleryItem(this)" class="p-2 rounded-xl bg-red-600/90 hover:bg-red-600 text-white shadow-md transition" title="Hapus foto ini">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Add Photo Slot Card --}}
                    <label for="galleryInput" id="addPhotoSlot" class="aspect-square rounded-2xl bg-slate-50 hover:bg-blue-50/40 border-2 border-dashed border-slate-300 hover:border-blue-400 flex flex-col items-center justify-center text-slate-400 hover:text-blue-600 cursor-pointer transition group p-3 text-center">
                        <div class="w-10 h-10 rounded-full bg-white group-hover:bg-blue-50 flex items-center justify-center text-slate-500 group-hover:text-blue-600 shadow-xs mb-1.5 transition">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </div>
                        <span class="text-[12px] font-bold">Tambah Foto</span>
                        <span class="text-[10px] text-slate-400">JPG, PNG max 5MB</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Card 1: Logo & Branding --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col items-center text-center">
                <div class="w-full flex items-center justify-between mb-5 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-[16px] font-bold text-slate-900">Logo & Profil</h2>
                    </div>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">Format Gambar</span>
                </div>

                {{-- Logo Preview Box --}}
                <div class="w-full aspect-square max-w-[200px] rounded-2xl bg-[#f0f4f9] border border-slate-200/80 flex items-center justify-center p-4 mb-4 relative overflow-hidden group shadow-inner">
                    @php
                        $hasLogo = !empty($univ['logo_url']);
                    @endphp

                    {{-- Actual Image Preview --}}
                    <img id="logoPreviewImg" 
                         src="{{ $hasLogo ? $univ['logo_url'] : '' }}" 
                         alt="{{ $univ['name'] }}" 
                         class="{{ $hasLogo ? '' : 'hidden' }} w-full h-full object-contain rounded-xl transition duration-300">

                    {{-- Default Icon Placeholder when no image --}}
                    <div id="logoDefaultPlaceholder" class="{{ $hasLogo ? 'hidden' : 'flex' }} flex-col items-center justify-center text-slate-400">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-brand-600 font-extrabold text-lg mb-2 shadow-xs border border-blue-100/60">
                            {{ $univ['logo_text'] ?? 'UNIV' }}
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">Belum ada logo</span>
                    </div>

                    {{-- Hover Overlay to Change --}}
                    <label for="logoInput" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white cursor-pointer gap-1.5 backdrop-blur-xs">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                        </svg>
                        <span class="text-xs font-bold">Ganti Foto</span>
                    </label>
                </div>

                <div id="logoFileName" class="font-bold text-slate-900 text-[13.5px] truncate max-w-full px-2">
                    {{ $univ['logo_name'] ?? 'Logo Universitas' }}
                </div>
                <p class="text-[12px] text-slate-500 mt-0.5 mb-4">
                    JPG, PNG, WebP atau SVG. Maksimal 5MB.
                </p>

                {{-- Upload Button --}}
                <label for="logoInput" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-brand-600 font-bold text-[13.5px] cursor-pointer transition">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                    </svg>
                    Pilih / Ganti Logo
                </label>
                <input type="file" name="logo" id="logoInput" class="hidden" accept="image/*">
            </div>

            {{-- Card 2: Interactive Real-time Map Location (Strictly Indonesia) --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-[16px] font-bold text-slate-900">Peta Lokasi Kampus</h2>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full flex items-center gap-1 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Indonesia Saja
                    </span>
                </div>

                {{-- Address Input with Live Suggestions Dropdown --}}
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[13px] font-semibold text-slate-700">Alamat Lengkap</label>
                        <span class="text-[11px] text-slate-400">Peta otomatis mencari wilayah Indonesia</span>
                    </div>
                    <textarea name="address" 
                              id="univAddressInput"
                              rows="2"
                              autocomplete="off"
                              placeholder="Ketik alamat jalan di Indonesia..."
                              class="w-full p-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 leading-relaxed resize-none">{{ old('address', $univ['address'] ?? '') }}</textarea>

                    {{-- Autocomplete Suggestions Dropdown for Address --}}
                    <div id="addressSuggestions" class="hidden absolute top-full left-0 right-0 z-50 bg-white border border-slate-200 rounded-2xl shadow-xl mt-1.5 max-h-56 overflow-y-auto divide-y divide-slate-100"></div>

                    {{-- Real-time Status Indicator --}}
                    <div id="locationStatus" class="hidden text-xs mt-1.5 font-medium flex items-center gap-1.5"></div>
                </div>

                {{-- Interactive Leaflet Map restricted to Indonesia --}}
                <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs">
                    <div id="univMap" class="w-full h-56 bg-slate-100 z-0"></div>
                    <div class="absolute bottom-2 left-2 z-10 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-700 shadow-xs border border-slate-200/80 pointer-events-none flex items-center gap-1">
                        <span>🇮🇩</span>
                        <span>Wilayah Indonesia • Geser pin untuk atur titik</span>
                    </div>
                </div>

                {{-- Lat & Long Hidden Inputs + Indicator Bar --}}
                <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', $univ['latitude'] ?? '') }}">
                <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', $univ['longitude'] ?? '') }}">
                <input type="hidden" name="map_link" id="mapLinkInput" value="{{ old('map_link', $univ['map_link'] ?? '') }}">

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-[12px] flex items-center justify-between text-slate-600">
                    <div class="flex items-center gap-1.5 truncate max-w-[70%]">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2.2" class="shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <span id="coordinateDisplay" class="font-mono font-medium truncate">
                            @if(!empty($univ['latitude']) && !empty($univ['longitude']))
                                {{ number_format((float)$univ['latitude'], 5) }}, {{ number_format((float)$univ['longitude'], 5) }}
                            @else
                                Titik peta akan otomatis muncul
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" onclick="getCurrentLocation()" class="text-brand-600 hover:underline font-bold text-[11.5px]">GPS Saya</button>
                        <span class="text-slate-300">•</span>
                        <button type="button" onclick="openInGoogleMaps()" class="text-slate-600 hover:text-blue-600 font-bold text-[11.5px] flex items-center gap-0.5">
                            Maps
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Bottom Action Bar --}}
    <div class="pt-6 border-t border-slate-200/80 flex items-center justify-end gap-4">
        <a href="{{ route('admin.univ.index') }}" 
           class="px-6 py-2.5 text-[14px] font-semibold text-slate-600 hover:text-slate-900 transition">
            Batal
        </a>

        <button type="submit" 
                class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white font-semibold text-[14px] shadow-sm transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-8H7v8M7 3v5h8"/>
            </svg>
            Simpan Perubahan
        </button>
    </div>

</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    // ---- Summernote for University Description ----
    $(document).ready(function() {
        $('#univDescription').summernote({
            placeholder: 'Tuliskan profil universitas, visi misi, program unggulan, dan detail kurikulum...',
            tabsize: 2,
            height: 380,
            minHeight: 250,
            maxHeight: 700,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            fontNames: ['Plus Jakarta Sans', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
            fontNamesIgnoreCheck: ['Plus Jakarta Sans'],
            callbacks: {
                onChange: function(contents) {
                    $('#univDescription').val(contents);
                },
                onImageUpload: function(files) {
                    var formData = new FormData();
                    formData.append('file', files[0]);
                    formData.append('_token', '{{ csrf_token() }}');
                    $.ajax({
                        url: '{{ route('admin.editor.upload') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#univDescription').summernote('insertImage', data.url);
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Gagal mengunggah gambar.';
                            alert(msg);
                        }
                    });
                }
            }
        });
    });

    // ---- IT Study Programs / Tags ----
    function addTag() {
        const input = document.getElementById('tagInputField');
        const value = input.value.trim();
        if (!value) return;

        const existing = Array.from(document.querySelectorAll('#tagContainer .tag-item input[type=hidden]')).map(i => i.value.toLowerCase());
        if (existing.includes(value.toLowerCase())) {
            input.value = '';
            input.focus();
            return;
        }

        const container = document.getElementById('tagContainer');
        const tag = document.createElement('span');
        tag.className = 'tag-item inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 text-[12.5px] font-semibold rounded-xl';
        tag.innerHTML = `
            ${value}
            <input type="hidden" name="tags[]" value="${value}">
            <button type="button" onclick="removeTag(this)" class="text-blue-400 hover:text-red-500 transition ml-0.5 leading-none">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(tag);
        input.value = '';
        input.focus();
    }

    function removeTag(button) {
        button.closest('.tag-item')?.remove();
    }

    // -------------------------------------------------------------
    // 1. LOGO PREVIEW & UPLOAD
    // -------------------------------------------------------------
    const logoInput = document.getElementById('logoInput');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const logoDefaultPlaceholder = document.getElementById('logoDefaultPlaceholder');
    const logoFileName = document.getElementById('logoFileName');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                const previewUrl = URL.createObjectURL(file);

                if (logoPreviewImg) {
                    logoPreviewImg.src = previewUrl;
                    logoPreviewImg.classList.remove('hidden');
                    logoPreviewImg.style.display = 'block';
                }
                if (logoDefaultPlaceholder) {
                    logoDefaultPlaceholder.classList.remove('flex');
                    logoDefaultPlaceholder.classList.add('hidden');
                    logoDefaultPlaceholder.style.display = 'none';
                }
                if (logoFileName) {
                    logoFileName.textContent = file.name;
                }

                // Update avatar di header navbar seketika jika ada di halaman
                const userBtn = document.getElementById('admin-user-btn');
                if (userBtn) {
                    let navAvatar = userBtn.querySelector('img');
                    if (!navAvatar) {
                        const fallbackDiv = userBtn.querySelector('.rounded-full.bg-brand-600, .rounded-full');
                        if (fallbackDiv && !fallbackDiv.tagName.toLowerCase().includes('svg')) {
                            navAvatar = document.createElement('img');
                            navAvatar.className = 'w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover shadow-sm ring-2 ring-slate-100 group-hover:ring-brand-200 transition-all';
                            navAvatar.alt = file.name;
                            fallbackDiv.replaceWith(navAvatar);
                        }
                    }
                    if (navAvatar) {
                        navAvatar.src = previewUrl;
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 2. CAMPUS GALLERY UPLOAD & PREVIEW (Maksimal 4 foto & sinkronisasi file)
    // -------------------------------------------------------------
    const galleryInput = document.getElementById('galleryInput');
    const galleryContainer = document.getElementById('galleryContainer');
    const addPhotoSlot = document.getElementById('addPhotoSlot');
    const galleryBtnTrigger = document.getElementById('galleryBtnTrigger');
    const galleryCountBadge = document.getElementById('galleryCountBadge');
    let galleryDataTransfer = new DataTransfer();

    function updateGalleryUI() {
        if (!galleryContainer) return;
        const total = galleryContainer.querySelectorAll('.gallery-item').length;
        if (galleryCountBadge) {
            galleryCountBadge.textContent = `${total} / 4 Foto`;
        }
        if (total >= 4) {
            if (addPhotoSlot) addPhotoSlot.style.display = 'none';
            if (galleryBtnTrigger) {
                galleryBtnTrigger.classList.add('opacity-50', 'pointer-events-none');
            }
        } else {
            if (addPhotoSlot) addPhotoSlot.style.display = 'flex';
            if (galleryBtnTrigger) {
                galleryBtnTrigger.classList.remove('opacity-50', 'pointer-events-none');
            }
        }
    }

    if (galleryInput) {
        galleryInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files || []);
            if (!files.length) return;

            const currentTotal = galleryContainer.querySelectorAll('.gallery-item').length;
            const remaining = 4 - currentTotal;

            if (remaining <= 0) {
                alert('Maksimal hanya 4 foto yang diperbolehkan di galeri.');
                galleryInput.files = galleryDataTransfer.files;
                return;
            }

            let validFiles = files.filter(f => f.type.startsWith('image/'));
            if (validFiles.length > remaining) {
                alert(`Hanya ${remaining} foto yang dapat ditambahkan karena batas maksimal 4 foto.`);
                validFiles = validFiles.slice(0, remaining);
            }

            validFiles.forEach(file => {
                galleryDataTransfer.items.add(file);

                const card = document.createElement('div');
                card.className = 'gallery-item relative aspect-square rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden group shadow-xs animate-fade-in';
                card.dataset.type = 'new';
                card.fileReference = file;

                const previewUrl = URL.createObjectURL(file);
                card.innerHTML = `
                    <img src="${previewUrl}" alt="Preview Foto" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 right-2">
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">Baru</span>
                    </div>
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                        <button type="button" onclick="removeGalleryItem(this)" class="p-2 rounded-xl bg-red-600/90 hover:bg-red-600 text-white shadow-md transition" title="Hapus foto ini">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </div>
                `;
                galleryContainer.insertBefore(card, addPhotoSlot);
            });

            galleryInput.files = galleryDataTransfer.files;
            updateGalleryUI();
        });
    }

    function removeGalleryItem(button) {
        const item = button.closest('.gallery-item');
        if (!item) return;

        if (item.dataset.type === 'new' && item.fileReference) {
            const newDT = new DataTransfer();
            for (let i = 0; i < galleryDataTransfer.files.length; i++) {
                const f = galleryDataTransfer.files[i];
                if (f !== item.fileReference) {
                    newDT.items.add(f);
                }
            }
            galleryDataTransfer = newDT;
            if (galleryInput) galleryInput.files = galleryDataTransfer.files;
        }

        item.remove();
        updateGalleryUI();
    }

    updateGalleryUI();

    // -------------------------------------------------------------
    // 3. REAL-TIME INTERACTIVE LEAFLET MAP (STRICTLY INDONESIA)
    // -------------------------------------------------------------
    let map, marker;
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    const coordinateDisplay = document.getElementById('coordinateDisplay');
    const mapLinkInput = document.getElementById('mapLinkInput');
    const addressInput = document.getElementById('univAddressInput');
    const locationInput = document.getElementById('univLocationInput');
    const nameInput = document.getElementById('univNameInput');
    const addressSuggestions = document.getElementById('addressSuggestions');
    const locationSuggestions = document.getElementById('locationSuggestions');
    const locationStatus = document.getElementById('locationStatus');

    // Indonesia Geographical Bounds (Sabang to Merauke)
    const INDONESIA_BOUNDS = L.latLngBounds(
        L.latLng(-11.5, 94.5), // Southwest
        L.latLng(6.5, 141.5)   // Northeast
    );

    const initialLat = parseFloat("{{ $univ['latitude'] ?? '-6.2088' }}") || -6.2088;
    const initialLng = parseFloat("{{ $univ['longitude'] ?? '106.8456' }}") || 106.8456;
    const hasInitialCoords = "{{ !empty($univ['latitude']) && !empty($univ['longitude']) }}" === "1";

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map bounded to Indonesia
        map = L.map('univMap', {
            maxBounds: INDONESIA_BOUNDS,
            maxBoundsViscosity: 1.0,
            minZoom: 4,
            maxZoom: 19
        }).setView([initialLat, initialLng], hasInitialCoords ? 15 : 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Custom Pin Icon
        const pinIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        marker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: pinIcon
        }).addTo(map);

        marker.bindPopup("<b>{{ $univ['name'] }}</b><br>Titik Lokasi Kampus").openPopup();

        // Update when marker is dragged (keep within Indonesia) + reverse geocode
        marker.on('dragend', function(e) {
            let pos = e.target.getLatLng();
            if (INDONESIA_BOUNDS.contains(pos)) {
                updateCoordinates(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            } else {
                marker.setLatLng([initialLat, initialLng]);
            }
        });

        // Click on map to place/move marker (within Indonesia) + reverse geocode
        map.on('click', function(e) {
            if (INDONESIA_BOUNDS.contains(e.latlng)) {
                marker.setLatLng(e.latlng);
                map.panTo(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            }
        });

        if (hasInitialCoords) {
            updateCoordinates(initialLat, initialLng, false);
        } else {
            // Automatically find location from location/address on load if coordinates not set
            const locVal = locationInput?.value?.trim();
            const addrVal = addressInput?.value?.trim();
            const query = addrVal || locVal || nameInput?.value?.trim();
            if (query && query.length > 2) {
                performSmartGeocode(query, null, true);
            }
        }
    });

    function updateCoordinates(lat, lng, updateMapLink = true) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        coordinateDisplay.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        if (updateMapLink) {
            mapLinkInput.value = `https://www.google.com/maps?q=${lat.toFixed(7)},${lng.toFixed(7)}`;
        }
    }

    // -------------------------------------------------------------
    // 4. SMART INDONESIA-ONLY GEOCODING ENGINE
    // -------------------------------------------------------------
    let geocodeTimer = null;

    function triggerAutoSearch(targetField) {
        clearTimeout(geocodeTimer);

        let query = '';
        if (targetField === 'location') {
            query = locationInput ? locationInput.value.trim() : '';
        } else if (targetField === 'address') {
            query = addressInput ? addressInput.value.trim() : '';
        } else {
            query = nameInput ? nameInput.value.trim() : '';
        }

        if (!query || query.length < 2) {
            hideSuggestions();
            if (locationStatus) locationStatus.classList.add('hidden');
            return;
        }

        if (locationStatus) {
            locationStatus.className = 'text-xs mt-1.5 font-medium flex items-center gap-1.5 text-blue-600';
            locationStatus.innerHTML = `
                <svg class="animate-spin h-3.5 w-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Mencari lokasi di Indonesia...</span>
            `;
            locationStatus.classList.remove('hidden');
        }

        geocodeTimer = setTimeout(() => {
            performSmartGeocode(query, targetField);
        }, 400);
    }

    async function performSmartGeocode(query, targetField, isSilent = false) {
        // Multi-tier query candidates constrained to Indonesia
        const candidates = [
            query,
            query + ', Indonesia',
            (nameInput?.value ? nameInput.value + ', ' : '') + (locationInput?.value || '') + ', Indonesia',
            locationInput?.value ? locationInput.value + ', Indonesia' : '',
            nameInput?.value ? nameInput.value + ', Indonesia' : ''
        ].filter(Boolean);

        let results = [];

        // Strategy 1: Nominatim OpenStreetMap strictly scoped to Indonesia (countrycodes=id & viewbox)
        for (const candidate of candidates) {
            if (!candidate || candidate.trim().length < 2) continue;
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(candidate)}&countrycodes=id&viewbox=94.5,-11.5,141.5,6.5&bounded=1&limit=6`;
                const res = await fetch(url);
                const data = await res.json();
                if (data && data.length > 0) {
                    // Filter strictly inside Indonesia bounds
                    const valid = data.filter(item => {
                        const lat = parseFloat(item.lat);
                        const lon = parseFloat(item.lon);
                        return lat >= -11.5 && lat <= 6.5 && lon >= 94.5 && lon <= 141.5;
                    });
                    if (valid.length > 0) {
                        results = valid;
                        break;
                    }
                }
            } catch (err) {}
        }

        // Strategy 2: Photon API strictly scoped to Indonesia bbox fallback
        if (results.length === 0) {
            try {
                const photonUrl = `https://photon.komoot.io/api/?q=${encodeURIComponent(query + ' Indonesia')}&bbox=94.5,-11.5,141.5,6.5&limit=6`;
                const res = await fetch(photonUrl);
                const data = await res.json();
                if (data && data.features && data.features.length > 0) {
                    results = data.features
                        .filter(f => {
                            const [lon, lat] = f.geometry.coordinates;
                            const country = f.properties.country || '';
                            return (country.toLowerCase() === 'indonesia' || country === '') &&
                                   lat >= -11.5 && lat <= 6.5 && lon >= 94.5 && lon <= 141.5;
                        })
                        .map(f => ({
                            lat: f.geometry.coordinates[1],
                            lon: f.geometry.coordinates[0],
                            display_name: [f.properties.name, f.properties.city || f.properties.district, f.properties.state, 'Indonesia'].filter(Boolean).join(', ')
                        }));
                }
            } catch (err) {}
        }

        if (results.length > 0) {
            renderSuggestions(results, targetField);

            const top = results[0];
            const lat = parseFloat(top.lat);
            const lon = parseFloat(top.lon);

            if (map && marker) {
                map.flyTo([lat, lon], 14, { duration: 0.9 });
                marker.setLatLng([lat, lon]);
                updateCoordinates(lat, lon);
                const title = nameInput?.value || locationInput?.value || 'Lokasi Kampus';
                marker.bindPopup(`<b>${title}</b><br>${top.display_name}`).openPopup();
            }

            if (locationStatus && !isSilent) {
                locationStatus.className = 'text-xs mt-1.5 font-medium flex items-center gap-1.5 text-emerald-600';
                locationStatus.innerHTML = `
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span class="truncate">Lokasi di Indonesia ditemukan: <b>${top.display_name.split(',')[0]}</b></span>
                `;
            }
        } else {
            hideSuggestions();
            if (locationStatus && !isSilent) {
                locationStatus.className = 'text-xs mt-1.5 font-medium text-slate-400';
                locationStatus.innerHTML = '<span>Lokasi tidak ditemukan di Indonesia, pastikan nama kota/wilayah sesuai</span>';
            }
        }
    }

    function renderSuggestions(results, targetField) {
        const box = targetField === 'location' ? locationSuggestions : addressSuggestions;
        if (!box) return;

        box.innerHTML = '';
        results.forEach(item => {
            const option = document.createElement('div');
            option.className = 'p-3 hover:bg-blue-50 cursor-pointer text-[12.5px] text-slate-700 hover:text-blue-700 transition flex items-start gap-2.5';
            option.innerHTML = `
                <span class="text-base shrink-0 mt-0.5">🇮🇩</span>
                <span class="line-clamp-2 leading-relaxed">${item.display_name}</span>
            `;
            option.addEventListener('click', () => {
                if (targetField === 'location') {
                    if (locationInput) locationInput.value = item.display_name.split(',').slice(0, 2).join(',').trim();
                } else {
                    if (addressInput) addressInput.value = item.display_name;
                }
                hideSuggestions();

                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);
                if (map && marker) {
                    map.flyTo([lat, lon], 16, { duration: 0.9 });
                    marker.setLatLng([lat, lon]);
                    updateCoordinates(lat, lon);
                    marker.bindPopup(`<b>${nameInput?.value || 'Lokasi Kampus'}</b><br>${item.display_name}`).openPopup();
                }

                if (locationStatus) {
                    locationStatus.className = 'text-xs mt-1.5 font-medium flex items-center gap-1.5 text-emerald-600';
                    locationStatus.innerHTML = `
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Lokasi terpilih & peta sinkron</span>
                    `;
                }
            });
            box.appendChild(option);
        });
        box.classList.remove('hidden');
    }

    function hideSuggestions() {
        if (addressSuggestions) addressSuggestions.classList.add('hidden');
        if (locationSuggestions) locationSuggestions.classList.add('hidden');
    }

    // Reverse Geocoding when user clicks or drags pin on the map
    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&countrycodes=id`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    if (addressInput && !addressInput.value.trim()) {
                        addressInput.value = data.display_name;
                    }
                    if (locationInput && !locationInput.value.trim()) {
                        const addr = data.address || {};
                        const city = addr.city || addr.town || addr.regency || addr.county || '';
                        const state = addr.state || '';
                        if (city || state) {
                            locationInput.value = [city, state].filter(Boolean).join(', ');
                        }
                    }
                    if (marker) {
                        marker.bindPopup(`<b>${nameInput?.value || 'Lokasi Terpilih'}</b><br>${data.display_name}`).openPopup();
                    }
                }
            })
            .catch(() => {});
    }

    // Attach real-time input event listeners
    if (locationInput) {
        locationInput.addEventListener('input', () => triggerAutoSearch('location'));
        locationInput.addEventListener('change', () => triggerAutoSearch('location'));
    }
    if (addressInput) {
        addressInput.addEventListener('input', () => triggerAutoSearch('address'));
        addressInput.addEventListener('change', () => triggerAutoSearch('address'));
    }
    if (nameInput) {
        nameInput.addEventListener('input', () => triggerAutoSearch('name'));
    }

    // Hide dropdown on outside click
    document.addEventListener('click', function(e) {
        if (addressSuggestions && !addressSuggestions.contains(e.target) && e.target !== addressInput) {
            addressSuggestions.classList.add('hidden');
        }
        if (locationSuggestions && !locationSuggestions.contains(e.target) && e.target !== locationInput) {
            locationSuggestions.classList.add('hidden');
        }
    });

    function openInGoogleMaps() {
        const lat = latInput.value;
        const lng = lngInput.value;
        const name = nameInput ? nameInput.value : '';
        const address = addressInput ? addressInput.value : '';
        const loc = locationInput ? locationInput.value : '';

        if (lat && lng) {
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        } else {
            const query = (address || [name, loc].filter(Boolean).join(', ')) + ', Indonesia';
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`, '_blank');
        }
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (INDONESIA_BOUNDS.contains([lat, lng])) {
                        map.flyTo([lat, lng], 16, { duration: 0.9 });
                        marker.setLatLng([lat, lng]);
                        updateCoordinates(lat, lng);
                        reverseGeocode(lat, lng);
                        marker.bindPopup("<b>Lokasi Anda Saat Ini</b>").openPopup();
                    } else {
                        alert("Lokasi GPS terdeteksi di luar wilayah Indonesia.");
                    }
                },
                function(err) {
                    alert("Tidak dapat mengakses GPS browser: " + err.message);
                }
            );
        } else {
            alert("Browser Anda tidak mendukung geolokasi.");
        }
    }
</script>
@endpush
