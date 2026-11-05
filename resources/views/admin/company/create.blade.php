@extends('admin.layouts.app')

@section('title', 'Tambah Perusahaan Baru — IndoTech Admin')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Summernote Lite CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
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
        transition: all 0.15s ease !important;
    }
    .note-toolbar .note-btn:not(.note-color-btn):not(.note-color-reset):hover,
    .note-toolbar .note-btn:not(.note-color-btn):not(.note-color-reset).active {
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #2563eb !important;
    }
    .note-editable {
        font-family: inherit !important;
        font-size: 14px !important;
        line-height: 1.7 !important;
        color: #1e293b !important;
        background: #ffffff !important;
        padding: 16px !important;
        min-height: 250px !important;
    }
    .note-editable p { margin-bottom: 0.75rem !important; }
    .note-editable ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 0.75rem !important; }
    .note-editable ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 0.75rem !important; }
    .note-editable a { color: #2563eb !important; text-decoration: underline !important; }
    .note-editable img { max-width: 100% !important; height: auto !important; border-radius: 8px !important; margin: 8px 0 !important; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('admin.company.store') }}" enctype="multipart/form-data" data-async data-async-reset class="space-y-6 max-w-7xl mx-auto">
    @csrf

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm flex items-start gap-3 animate-fade-in">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-red-500 shrink-0 mt-0.5">
                <circle cx="12" cy="12" r="9"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="flex-1">
                <div class="font-bold">Terdapat beberapa kesalahan:</div>
                <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if (session('info'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-[13.5px]">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 mt-0.5 text-blue-500"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if (session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-[13.5px]">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 mt-0.5 text-green-500"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Top Back Link & Breadcrumb --}}
    <div>
        @if (auth()->user()->role === 'company')
            <a href="{{ route('dashboard.company') }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 mb-2 transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Dashboard
            </a>
        @else
            <a href="{{ route('admin.company.index') }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 mb-2 transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        @endif
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Card 1: Basic Information --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <rect x="4" y="3" width="16" height="18" rx="2"/>
                            <path stroke-linecap="round" d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Informasi Dasar Perusahaan</span>
                        <span class="text-[12px] font-normal text-slate-400">Identitas resmi, legalitas, dan bidang usaha perusahaan</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    {{-- Company Name --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="companyNameInput"
                               value="{{ old('name') }}" 
                               required
                               placeholder="Contoh: PT. Inovasi Teknologi Nusantara"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company Code --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Kode Perusahaan / NIB</label>
                        <input type="text" 
                               name="npsn" 
                               value="{{ old('npsn') }}" 
                               placeholder="Contoh: CMP001"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Company Type --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tipe Perusahaan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="type" 
                                    required
                                    class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer appearance-none pr-10">
                                <option value="Swasta" {{ old('type') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                <option value="BUMN" {{ old('type') == 'BUMN' ? 'selected' : '' }}>BUMN</option>
                                <option value="Multinasional" {{ old('type') == 'Multinasional' ? 'selected' : '' }}>Multinasional</option>
                                <option value="Startup" {{ old('type') == 'Startup' ? 'selected' : '' }}>Startup</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Industry --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Bidang Industri <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="industry" 
                               value="{{ old('industry') }}" 
                               required
                               placeholder="mis. Teknologi Informasi, Software House"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @error('industry')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Kota / Kabupaten</label>
                        <input type="text" 
                               name="city" 
                               value="{{ old('city') }}" 
                               placeholder="mis. Denpasar atau Jakarta Selatan"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Province --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Provinsi</label>
                        <input type="text" 
                               name="province" 
                               value="{{ old('province') }}" 
                               placeholder="mis. Bali atau DKI Jakarta"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Location Summary --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Ringkasan Wilayah (Location)</label>
                        <input type="text" 
                               name="location" 
                               id="companyLocationInput"
                               value="{{ old('location') }}" 
                               placeholder="Contoh: Denpasar, Bali atau Jakarta Selatan, DKI Jakarta"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Employees, Founded & Portfolio --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Jumlah Karyawan</label>
                        <input type="number" 
                               name="total_employees" 
                               value="{{ old('total_employees') }}" 
                               placeholder="Contoh: 100"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tahun Berdiri</label>
                        <input type="number" 
                               name="founded" 
                               value="{{ old('founded') }}" 
                               placeholder="Contoh: 2018"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Portofolio / Proyek Selesai</label>
                        <input type="text" 
                               name="projects_completed" 
                               value="{{ old('projects_completed') }}" 
                               placeholder="Contoh: 100+ Proyek atau 25 Proyek Web & Mobile"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        <p class="text-[12px] text-slate-400 mt-1">Ditampilkan pada ringkasan metrik portofolio perusahaan.</p>
                    </div>

                    {{-- Status Kemitraan --}}
                    @if (auth()->user()->role === 'company')
                        <div class="sm:col-span-2">
                            <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Status Kemitraan</label>
                            <div class="px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[13px] font-semibold flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Pending (Menunggu Verifikasi Admin)
                            </div>
                        </div>
                    @else
                        <div class="sm:col-span-2">
                            <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Status Kemitraan <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active (Aktif)</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive (Non-aktif)</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 2: Company Description with Summernote --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Deskripsi Perusahaan</span>
                        <span class="text-[12px] font-normal text-slate-400">Profil, visi misi, produk/layanan, dan budaya kerja perusahaan</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Profil & Detail Bisnis</label>
                    <textarea name="description" 
                              id="companyDescription"
                              class="w-full p-4 rounded-xl border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 leading-relaxed">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Card 3: Layanan Utama & Keunggulan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Layanan Utama & Keunggulan</span>
                        <span class="text-[12px] font-normal text-slate-400">Tambahkan poin keunggulan, spesialisasi, atau layanan utama yang ditawarkan</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tambah Layanan / Keunggulan</label>
                    <div class="flex gap-2">
                        <input type="text" id="specialtyInputField"
                               placeholder="Contoh: Pengembangan Sistem Terdistribusi & Cloud"
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addSpecialty();}">
                        <button type="button" onclick="addSpecialty()"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold rounded-lg transition shrink-0">
                            Tambah
                        </button>
                    </div>

                    <div id="specialtiesContainer" class="flex flex-wrap gap-2 mt-3 min-h-[32px]">
                        @if(is_array(old('specialties')))
                            @foreach(old('specialties') as $spec)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12.5px] font-semibold rounded-xl">
                                    <span>{{ $spec }}</span>
                                    <input type="hidden" name="specialties[]" value="{{ $spec }}">
                                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-red-500 transition ml-0.5 leading-none">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <p class="text-[12px] text-slate-400 mt-2">Tekan Enter atau klik Tambah. Poin-poin ini akan tampil pada bagian Layanan Utama di profil perusahaan.</p>
                </div>
            </div>

            {{-- Card 4: Teknologi & Tech Stack --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                        </svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Teknologi & Tech Stack</span>
                        <span class="text-[12px] font-normal text-slate-400">Bahasa pemrograman, framework, database, dan teknologi kerja</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tambah Tag Teknologi</label>
                    <div class="flex gap-2">
                        <input type="text" id="techStackInputField"
                               placeholder="Contoh: Laravel, React, Flutter, Docker, AWS"
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addTechStack();}">
                        <button type="button" onclick="addTechStack()"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold rounded-lg transition shrink-0">
                            Tambah
                        </button>
                    </div>

                    <div id="techStackContainer" class="flex flex-wrap gap-2 mt-3 min-h-[32px]">
                        @if(is_array(old('tech_stack')))
                            @foreach(old('tech_stack') as $tech)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-[12.5px] font-semibold rounded-xl">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                    <span>{{ $tech }}</span>
                                    <input type="hidden" name="tech_stack[]" value="{{ $tech }}">
                                    <button type="button" onclick="this.parentElement.remove()" class="text-indigo-400 hover:text-red-500 transition ml-0.5 leading-none">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <p class="text-[12px] text-slate-400 mt-2">Tekan Enter atau klik Tambah. Tag teknologi akan ditampilkan pada profil perusahaan.</p>
                </div>
            </div>

            {{-- Card 5: Galeri Perusahaan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px]">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                            </svg>
                        </div>
                        <div>
                            <span class="block leading-tight">Galeri Perusahaan</span>
                            <span class="text-[12px] font-normal text-slate-400">Foto kantor, suasana kerja, kegiatan tim, dan fasilitas</span>
                        </div>
                    </div>

                    <label for="galleryInput" class="inline-flex items-center gap-1.5 text-[13.5px] font-semibold text-brand-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/80 px-3.5 py-2 rounded-lg transition cursor-pointer">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Tambah Foto
                    </label>
                    <input type="file" name="gallery[]" id="galleryInput" multiple accept="image/*" class="hidden">
                </div>

                <div id="galleryContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
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

            {{-- Card 1: Logo & Profil --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col items-center text-center">
                <div class="w-full flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h2 class="text-[16px] font-bold text-slate-900">Logo Perusahaan</h2>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Format Gambar</span>
                </div>

                <div class="w-full aspect-square max-w-[200px] rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center p-4 mb-4 relative overflow-hidden group shadow-inner">
                    <img id="logoPreviewImg" src="" alt="Logo" class="hidden w-full h-full object-contain rounded-xl transition duration-300">

                    <div id="logoDefaultPlaceholder" class="flex flex-col items-center justify-center text-slate-400">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-brand-600 font-extrabold text-lg mb-2 shadow-xs border border-blue-100/60">
                            CMP
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">Belum ada logo</span>
                    </div>

                    <label for="logoInput" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white cursor-pointer gap-1.5 backdrop-blur-xs">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                        </svg>
                        <span class="text-xs font-bold">Pilih Foto</span>
                    </label>
                </div>

                <div id="logoFileName" class="font-bold text-slate-900 text-[13.5px] truncate max-w-full px-2">
                    Logo Perusahaan
                </div>
                <p class="text-[12px] text-slate-500 mt-0.5 mb-4">
                    JPG, PNG, WebP atau SVG. Maksimal 5MB.
                </p>

                <label for="logoInput" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-brand-600 font-bold text-[13.5px] cursor-pointer transition">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                    </svg>
                    Pilih Logo Baru
                    <input type="file" id="logoInput" name="logo" class="hidden" accept="image/*">
                </label>
            </div>

            {{-- Card 2: Contact Information --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Informasi Kontak</span>
                        <span class="text-[12px] font-normal text-slate-400">Saluran komunikasi dan kontak resmi</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Email Perusahaan</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="contact@company.com"
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Website Perusahaan</label>
                    <input type="url" 
                           name="website" 
                           value="{{ old('website') }}" 
                           placeholder="https://company.co.id"
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" 
                           name="phone" 
                           value="{{ old('phone') }}" 
                           placeholder="081234567890"
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            {{-- Card 3: Interactive Real-time Map Location (Strictly Indonesia) --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px]">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="block leading-tight">Peta Lokasi Perusahaan</span>
                            <span class="text-[12px] font-normal text-slate-400">Titik koordinat dan peta interaktif</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full flex items-center gap-1 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Indonesia
                    </span>
                </div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[13.5px] font-semibold text-slate-800">Alamat Lengkap</label>
                        <span class="text-[11px] text-slate-400">Peta otomatis mencari wilayah Indonesia</span>
                    </div>
                    <textarea name="address" 
                              id="companyAddressInput"
                              rows="2"
                              autocomplete="off"
                              placeholder="Ketik alamat kantor di Indonesia..."
                              class="w-full p-3 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 leading-relaxed resize-none">{{ old('address') }}</textarea>

                    <div id="addressSuggestions" class="hidden absolute top-full left-0 right-0 z-50 bg-white border border-slate-200 rounded-2xl shadow-xl mt-1.5 max-h-56 overflow-y-auto divide-y divide-slate-100"></div>

                    <div id="locationStatus" class="hidden text-xs mt-1.5 font-medium flex items-center gap-1.5"></div>
                </div>

                <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs">
                    <div id="companyMap" class="w-full h-56 bg-slate-100 z-0"></div>
                    <div class="absolute bottom-2 left-2 z-10 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-700 shadow-xs border border-slate-200/80 pointer-events-none flex items-center gap-1">
                        <span>🇮🇩</span>
                        <span>Wilayah Indonesia • Geser pin untuk atur titik</span>
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', '-6.2088') }}">
                <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', '106.8456') }}">
                <input type="hidden" name="map_link" id="mapLinkInput" value="{{ old('map_link') }}">

                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between text-[11.5px] text-slate-600">
                    <div class="flex items-center gap-1.5">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <span>Koordinat: <b id="coordinateDisplay" class="font-mono text-slate-800">-6.20880, 106.84560</b></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="detectCurrentLocation()" class="text-blue-600 hover:text-blue-700 font-bold hover:underline cursor-pointer">
                            Deteksi Lokasi
                        </button>
                        <span>•</span>
                        <button type="button" onclick="openInGoogleMaps()" class="text-blue-600 hover:text-blue-700 font-bold hover:underline cursor-pointer">
                            Buka di Maps
                        </button>
                    </div>
                </div>
            </div>
            </div>

        </div>

    </div>

    {{-- Bottom Action Bar --}}
    <div class="pt-6 border-t border-slate-200/80 flex items-center justify-end gap-4">
        @if (auth()->user()->role === 'company')
            <a href="{{ route('dashboard.company') }}" class="px-6 py-2.5 text-[14px] font-semibold text-slate-600 hover:text-slate-900 transition">
                Batal
            </a>
        @else
            <a href="{{ route('admin.company.index') }}" class="px-6 py-2.5 text-[14px] font-semibold text-slate-600 hover:text-slate-900 transition">
                Batal
            </a>
        @endif

        <button type="submit" class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white font-semibold text-[14px] shadow-sm transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Simpan Perusahaan
        </button>
    </div>

</form>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    // ---- Summernote for Company Description ----
    $(document).ready(function() {
        $('#companyDescription').summernote({
            placeholder: 'Tuliskan profil perusahaan, visi misi, produk/layanan, peluang karir, dan fasilitas kantor...',
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
                    $('#companyDescription').val(contents);
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
                            $('#companyDescription').summernote('insertImage', data.url);
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message 
                                ? xhr.responseJSON.message 
                                : 'Gagal mengunggah gambar. Pastikan format berkas JPG, PNG, atau WebP (maksimal 25MB).';
                            alert(msg);
                        }
                    });
                }
            }
        });
    });

    // ---- Specialties Tags ----
    function addSpecialty() {
        const input = document.getElementById('specialtyInputField');
        if (!input) return;
        const val = input.value.trim();
        if (!val) return;
        const container = document.getElementById('specialtiesContainer');
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12.5px] font-semibold rounded-xl';
        span.innerHTML = `
            <span>${val}</span>
            <input type="hidden" name="specialties[]" value="${val}">
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-red-500 transition ml-0.5 leading-none">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(span);
        input.value = '';
    }

    // ---- Tech Stack Tags ----
    function addTechStack() {
        const input = document.getElementById('techStackInputField');
        if (!input) return;
        const val = input.value.trim();
        if (!val) return;
        const container = document.getElementById('techStackContainer');
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-[12.5px] font-semibold rounded-xl';
        span.innerHTML = `
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
            <span>${val}</span>
            <input type="hidden" name="tech_stack[]" value="${val}">
            <button type="button" onclick="this.parentElement.remove()" class="text-indigo-400 hover:text-red-500 transition ml-0.5 leading-none">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(span);
        input.value = '';
    }

    // Logo image preview
    const logoInput = document.getElementById('logoInput');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const logoDefaultPlaceholder = document.getElementById('logoDefaultPlaceholder');
    const logoFileName = document.getElementById('logoFileName');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    logoPreviewImg.src = evt.target.result;
                    logoPreviewImg.classList.remove('hidden');
                    if (logoDefaultPlaceholder) logoDefaultPlaceholder.classList.add('hidden');
                    if (logoFileName) logoFileName.textContent = file.name;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Gallery upload and preview
    const galleryInput = document.getElementById('galleryInput');
    const galleryContainer = document.getElementById('galleryContainer');
    const addPhotoSlot = document.getElementById('addPhotoSlot');

    if (galleryInput) {
        galleryInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        const card = document.createElement('div');
                        card.className = 'gallery-item relative aspect-square rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden group shadow-xs animate-fade-in';
                        card.innerHTML = `
                            <img src="${evt.target.result}" alt="Foto Galeri" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" onclick="removeGalleryItem(this)" class="p-2 rounded-xl bg-red-600/90 hover:bg-red-600 text-white shadow-md transition cursor-pointer" title="Hapus foto ini">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                        galleryContainer.insertBefore(card, addPhotoSlot);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    function removeGalleryItem(button) {
        const item = button.closest('.gallery-item');
        if (item) {
            item.remove();
        }
    }

    // Leaflet map setup for Indonesia
    let map, marker;
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    const coordinateDisplay = document.getElementById('coordinateDisplay');
    const mapLinkInput = document.getElementById('mapLinkInput');
    const addressInput = document.getElementById('companyAddressInput');
    const locationInput = document.getElementById('companyLocationInput');
    const nameInput = document.getElementById('companyNameInput');
    const addressSuggestions = document.getElementById('addressSuggestions');
    const locationStatus = document.getElementById('locationStatus');

    const INDONESIA_BOUNDS = L.latLngBounds(
        L.latLng(-11.5, 94.5),
        L.latLng(6.5, 141.5)
    );

    const initialLat = -6.2088;
    const initialLng = 106.8456;

    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('companyMap')) return;

        map = L.map('companyMap', {
            maxBounds: INDONESIA_BOUNDS,
            maxBoundsViscosity: 1.0,
            minZoom: 4,
            maxZoom: 19
        }).setView([initialLat, initialLng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

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

        marker.bindPopup(`<b>Perusahaan Baru</b><br>Titik Lokasi Perusahaan`).openPopup();

        marker.on('dragend', function(e) {
            let pos = e.target.getLatLng();
            if (INDONESIA_BOUNDS.contains(pos)) {
                updateCoordinates(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            } else {
                marker.setLatLng([initialLat, initialLng]);
            }
        });

        map.on('click', function(e) {
            if (INDONESIA_BOUNDS.contains(e.latlng)) {
                marker.setLatLng(e.latlng);
                map.panTo(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            }
        });

        if (addressInput) {
            addressInput.addEventListener('input', () => triggerAutoSearch('address'));
        }
        if (locationInput) {
            locationInput.addEventListener('blur', () => {
                if (!addressInput?.value?.trim() && locationInput.value.trim().length >= 2) {
                    triggerAutoSearch('location');
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#addressSuggestions') && e.target !== addressInput) {
                hideSuggestions();
            }
        });
    });

    function updateCoordinates(lat, lng, updateMapLink = true) {
        if (latInput) latInput.value = lat.toFixed(7);
        if (lngInput) lngInput.value = lng.toFixed(7);
        if (coordinateDisplay) {
            coordinateDisplay.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        }
        if (updateMapLink && mapLinkInput) {
            mapLinkInput.value = `https://www.google.com/maps?q=${lat.toFixed(7)},${lng.toFixed(7)}`;
        }
    }

    let geocodeTimer = null;
    function triggerAutoSearch(targetField) {
        clearTimeout(geocodeTimer);
        let query = targetField === 'location' ? (locationInput?.value.trim() || '') : (addressInput?.value.trim() || '');

        if (!query || query.length < 2) {
            hideSuggestions();
            if (locationStatus) locationStatus.classList.add('hidden');
            return;
        }

        if (locationStatus) {
            locationStatus.className = 'text-xs mt-1.5 font-medium flex items-center gap-1.5 text-blue-600';
            locationStatus.innerHTML = `<span>Mencari lokasi di Indonesia...</span>`;
            locationStatus.classList.remove('hidden');
        }

        geocodeTimer = setTimeout(() => {
            performSmartGeocode(query, targetField);
        }, 400);
    }

    async function performSmartGeocode(query, targetField) {
        const candidate = query + ', Indonesia';
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(candidate)}&countrycodes=id&viewbox=94.5,-11.5,141.5,6.5&bounded=1&limit=5`;
            const res = await fetch(url);
            const data = await res.json();
            if (data && data.length > 0) {
                renderSuggestions(data);
                const top = data[0];
                const lat = parseFloat(top.lat);
                const lon = parseFloat(top.lon);
                if (map && marker) {
                    map.flyTo([lat, lon], 14, { duration: 0.9 });
                    marker.setLatLng([lat, lon]);
                    updateCoordinates(lat, lon);
                    marker.bindPopup(`<b>${nameInput?.value || 'Lokasi Perusahaan'}</b><br>${top.display_name}`).openPopup();
                }
                if (locationStatus) {
                    locationStatus.className = 'text-xs mt-1.5 font-medium flex items-center gap-1.5 text-emerald-600';
                    locationStatus.innerHTML = `<span>Lokasi ditemukan: <b>${top.display_name.split(',')[0]}</b></span>`;
                }
            } else {
                hideSuggestions();
                if (locationStatus) {
                    locationStatus.className = 'text-xs mt-1.5 font-medium text-slate-400';
                    locationStatus.innerHTML = '<span>Lokasi tidak ditemukan di Indonesia</span>';
                }
            }
        } catch(e) {}
    }

    function renderSuggestions(results) {
        if (!addressSuggestions) return;
        addressSuggestions.innerHTML = '';
        results.forEach(item => {
            const option = document.createElement('div');
            option.className = 'p-3 hover:bg-blue-50 cursor-pointer text-[12.5px] text-slate-700 hover:text-blue-700 transition flex items-start gap-2.5';
            option.innerHTML = `<span class="text-base shrink-0 mt-0.5">🇮🇩</span><span class="line-clamp-2 leading-relaxed">${item.display_name}</span>`;
            option.addEventListener('click', () => {
                if (addressInput) addressInput.value = item.display_name;
                hideSuggestions();
                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);
                if (map && marker) {
                    map.flyTo([lat, lon], 16, { duration: 0.9 });
                    marker.setLatLng([lat, lon]);
                    updateCoordinates(lat, lon);
                    marker.bindPopup(`<b>${nameInput?.value || 'Lokasi Perusahaan'}</b><br>${item.display_name}`).openPopup();
                }
            });
            addressSuggestions.appendChild(option);
        });
        addressSuggestions.classList.remove('hidden');
    }

    function hideSuggestions() {
        if (addressSuggestions) addressSuggestions.classList.add('hidden');
    }

    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&countrycodes=id`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    if (addressInput && !addressInput.value.trim()) {
                        addressInput.value = data.display_name;
                    }
                    if (marker) {
                        marker.bindPopup(`<b>${nameInput?.value || 'Lokasi Terpilih'}</b><br>${data.display_name}`).openPopup();
                    }
                }
            }).catch(() => {});
    }

    function detectCurrentLocation() {
        if (!navigator.geolocation) return alert('Browser tidak mendukung deteksi lokasi.');
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            if (INDONESIA_BOUNDS.contains([lat, lng])) {
                if (map && marker) {
                    map.flyTo([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoordinates(lat, lng);
                    reverseGeocode(lat, lng);
                }
            } else {
                alert('Posisi GPS di luar wilayah Indonesia.');
            }
        });
    }

    function openInGoogleMaps() {
        const lat = latInput?.value;
        const lng = lngInput?.value;
        if (lat && lng) {
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        } else if (addressInput?.value) {
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(addressInput.value)}`, '_blank');
        }
    }
</script>
@endpush
@endsection
