@extends('admin.layouts.app')

@section('title', 'Edit Komunitas — ' . ($community->name ?? 'IndoTech'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- ===== TOP HEADER & BREADCRUMB ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            @php
                $backRoute = request('return_to') === 'approvals' 
                    ? route('admin.community-approvals.index') 
                    : route('admin.community.index');
                $backText = request('return_to') === 'approvals' 
                    ? 'Kembali ke Persetujuan Komunitas' 
                    : 'Kembali ke Daftar Komunitas';
            @endphp
            <a href="{{ $backRoute }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-1.5">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>{{ $backText }}</span>
            </a>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Edit Komunitas
                </h1>

                {{-- Status Badges --}}
                @if(($community->status ?? 'active') === 'active')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Nonaktif
                    </span>
                @endif

                @if(($community->approval_status ?? 'approved') === 'approved')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80">
                        Disetujui
                    </span>
                @elseif(($community->approval_status ?? '') === 'rejected')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                        Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                        Menunggu Persetujuan
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Perbarui detail informasi, media visual, dan konfigurasi komunitas &ldquo;<span class="font-semibold text-slate-700">{{ $community->name }}</span>&rdquo;.
            </p>
        </div>

        {{-- Top Action Buttons --}}
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ $backRoute }}" 
               class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 hover:text-slate-900 transition shadow-xs">
                Batal
            </a>
            <button type="submit" form="community-edit-form" 
                    class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    {{-- ===== VALIDATION ERRORS ALERT ===== --}}
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <div class="flex items-center gap-2 font-bold mb-1.5">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 font-medium ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== MAIN FORM ===== --}}
    <form id="community-edit-form" 
          method="POST" 
          action="{{ route('admin.community.update', $community->id) }}" 
          enctype="multipart/form-data" 
          data-async 
          data-async-follow 
          class="space-y-6">
        @csrf
        @method('PUT')

        @if(request('return_to'))
            <input type="hidden" name="return_to" value="{{ request('return_to') }}">
        @endif

        {{-- Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ===== LEFT COLUMN: Content & Details (8 Cols) ===== --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- CARD 1: INFORMASI UTAMA --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="3" width="16" height="18" rx="2"/>
                                <path stroke-linecap="round" d="M9 7h6m-6 4h6m-6 4h4"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Informasi Utama Komunitas</h2>
                            <p class="text-xs text-slate-400">Identitas dasar, klasifikasi kategori, dan format keanggotaan</p>
                        </div>
                    </div>

                    {{-- Nama Komunitas --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Komunitas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="name"
                               name="name" 
                               value="{{ old('name', $community->name) }}" 
                               required
                               placeholder="Contoh: Web Developer Indonesia, Flutter Enthusiast"
                               class="w-full h-11 px-3.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid: Kategori & Bahasa --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kategori --}}
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="category" 
                                        name="category" 
                                        required
                                        class="w-full h-11 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none cursor-pointer">
                                    @php
                                        $currentCategory = old('category', $community->category);
                                        $categoryOptions = $categories ?? ['Technology', 'Design', 'Business', 'Education', 'Science', 'Other'];
                                    @endphp
                                    @foreach($categoryOptions as $cat)
                                        <option value="{{ $cat }}" {{ strcasecmp((string)$currentCategory, (string)$cat) === 0 ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="m8 10 4 4 4-4"/>
                                </svg>
                            </div>
                            @error('category')
                                <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bahasa Utama --}}
                        <div>
                            <label for="language" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Bahasa Utama
                            </label>
                            <div class="relative">
                                <select id="language" 
                                        name="language" 
                                        class="w-full h-11 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none cursor-pointer">
                                    @php
                                        $currentLang = old('language', $community->language ?? 'Bahasa Indonesia');
                                    @endphp
                                    <option value="Bahasa Indonesia" {{ $currentLang === 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="English" {{ $currentLang === 'English' ? 'selected' : '' }}>English</option>
                                    <option value="Bilingual (Indonesia & English)" {{ $currentLang === 'Bilingual (Indonesia & English)' ? 'selected' : '' }}>Bilingual (Indonesia & English)</option>
                                    <option value="Lainnya" {{ !in_array($currentLang, ['Bahasa Indonesia', 'English', 'Bilingual (Indonesia & English)']) ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="m8 10 4 4 4-4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Jenis Akses Komunitas (Publik / Privat) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jenis Akses Komunitas <span class="text-rose-500">*</span>
                        </label>
                        @php
                            $currentType = strtolower((string) old('type', $community->type ?? 'public'));
                            if (in_array($currentType, ['publik', 'public'])) {
                                $currentType = 'public';
                            } else {
                                $currentType = 'private';
                            }
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none hover:bg-slate-50/70 {{ $currentType === 'public' ? 'border-blue-500 bg-blue-50/20 ring-1 ring-blue-500/20' : 'border-slate-200 bg-white' }}">
                                <input type="radio" name="type" value="public" {{ $currentType === 'public' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-900">Publik</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Terbuka</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-normal">
                                        Siapa saja dapat menemukan, melihat, dan bergabung ke komunitas langsung.
                                    </p>
                                </div>
                            </label>

                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none hover:bg-slate-50/70 {{ $currentType === 'private' ? 'border-blue-500 bg-blue-50/20 ring-1 ring-blue-500/20' : 'border-slate-200 bg-white' }}">
                                <input type="radio" name="type" value="private" {{ $currentType === 'private' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-900">Privat</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Terbatas</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-normal">
                                        Calon anggota harus mengajukan permohonan dan disetujui pengurus.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Grid: Mode Komunitas & Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Mode Komunitas --}}
                        <div>
                            <label for="mode" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Mode Komunitas
                            </label>
                            @php
                                $currentMode = strtolower((string) old('mode', $community->mode ?? 'online'));
                            @endphp
                            <div class="relative">
                                <select id="mode" 
                                        name="mode" 
                                        class="w-full h-11 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none cursor-pointer">
                                    <option value="online" {{ $currentMode === 'online' ? 'selected' : '' }}>Online (Kegiatan Daring)</option>
                                    <option value="offline" {{ $currentMode === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka / Luring)</option>
                                    <option value="hybrid" {{ $currentMode === 'hybrid' ? 'selected' : '' }}>Hybrid (Online &amp; Offline)</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="m8 10 4 4 4-4"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Lokasi Komunitas --}}
                        <div>
                            <label for="location" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Lokasi / Basis Komunitas
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="location"
                                       name="location" 
                                       value="{{ old('location', $community->location) }}" 
                                       placeholder="Contoh: Jakarta Selatan, Surabaya, atau Seluruh Indonesia"
                                       class="w-full h-11 pl-9 pr-3.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Dianjurkan diisi jika format kegiatan Offline atau Hybrid.</p>
                        </div>
                    </div>

                    {{-- Topik & Tags Komunitas --}}
                    <div>
                        <label for="tags" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tag / Topik Komunitas <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        @php
                            $tagsValue = old('tags');
                            if ($tagsValue === null) {
                                if (is_array($community->tags)) {
                                    $tagsValue = implode(', ', $community->tags);
                                } elseif (is_string($community->tags)) {
                                    $tagsValue = $community->tags;
                                } else {
                                    $tagsValue = '';
                                }
                            }
                        @endphp
                        <input type="text" 
                               id="tags"
                               name="tags" 
                               value="{{ $tagsValue }}" 
                               placeholder="Contoh: Laravel, Tailwind CSS, Frontend, AI, Open Source"
                               class="w-full h-11 px-3.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                        <p class="text-[11px] text-slate-400 mt-1">Pisahkan setiap topik/keahlian dengan tanda koma (,).</p>
                    </div>
                </div>

                {{-- CARD 2: DESKRIPSI & PROFIL --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Deskripsi &amp; Profil Komunitas</h2>
                            <p class="text-xs text-slate-400">Jelaskan visi, misi, dan aktivitas yang diadakan oleh komunitas</p>
                        </div>
                    </div>

                    <div>
                        <label for="communityDescription" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Deskripsi Komunitas
                        </label>
                        <textarea name="description" 
                                  id="communityDescription"
                                  rows="6"
                                  maxlength="2000"
                                  oninput="document.getElementById('charCount').textContent = this.value.length + ' / 2000 karakter'"
                                  placeholder="Ceritakan tentang profil komunitas, agenda berkala, target anggota, serta manfaat yang didapat dengan bergabung..."
                                  class="w-full p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none leading-relaxed transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 resize-y">{{ old('description', $community->description) }}</textarea>
                        
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-[11px] text-slate-400">Deskripsi yang komprehensif mempermudah calon anggota mengenali komunitas.</span>
                            <span id="charCount" class="text-xs font-medium text-slate-500 shrink-0">
                                {{ strlen((string) old('description', $community->description ?? '')) }} / 2000 karakter
                            </span>
                        </div>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- CARD 3: TATA TERTIB & PEDOMAN --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Peraturan &amp; Tata Tertib Komunitas</h2>
                            <p class="text-xs text-slate-400">Panduan etika berinteraksi bagi seluruh anggota</p>
                        </div>
                    </div>

                    <div>
                        <label for="rules" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Aturan Komunitas <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="rules" 
                                  id="rules"
                                  rows="4"
                                  maxlength="3000"
                                  placeholder="Contoh:&#10;1. Saling menghormati sesama anggota komunitas.&#10;2. Dilarang membagikan konten SARA, hoaks, atau spam promosi tanpa izin.&#10;3. Berbagi ilmu dan berdiskusi secara konstruktif."
                                  class="w-full p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none leading-relaxed transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 resize-y">{{ old('rules', $community->rules) }}</textarea>
                        @error('rules')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- CARD 4: KONTAK & MEDIA SOSIAL --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 shrink-0">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Kontak &amp; Tautan Resmi</h2>
                            <p class="text-xs text-slate-400">Kanal komunikasi eksternal dan alamat website komunitas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Email Resmi Komunitas
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       id="email"
                                       name="email" 
                                       value="{{ old('email', $community->email) }}" 
                                       placeholder="kontak@komunitas.id"
                                       class="w-full h-11 pl-9 pr-3.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Website --}}
                        <div>
                            <label for="website" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Website / Link Komunitas
                            </label>
                            <div class="relative">
                                <input type="url" 
                                       id="website"
                                       name="website" 
                                       value="{{ old('website', $community->website) }}" 
                                       placeholder="https://komunitas.id"
                                       class="w-full h-11 pl-9 pr-3.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('website')
                                <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT COLUMN: Status, Meta, Media (4 Cols) ===== --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- CARD 1: STATUS & MODERASI --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                    <h2 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>Status &amp; Visibilitas</span>
                        <span class="w-2 h-2 rounded-full {{ ($community->status ?? 'active') === 'active' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                    </h2>

                    {{-- Status Komunitas --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Status Publikasi <span class="text-rose-500">*</span>
                        </label>
                        @php
                            $currentStatus = old('status', $community->status ?? 'active');
                        @endphp
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer transition select-none text-xs font-bold {{ $currentStatus === 'active' ? 'border-emerald-500 bg-emerald-50/40 text-emerald-800' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                <input type="radio" name="status" value="active" {{ $currentStatus === 'active' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                <span>Aktif</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer transition select-none text-xs font-bold {{ $currentStatus === 'inactive' ? 'border-slate-400 bg-slate-100 text-slate-800' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                <input type="radio" name="status" value="inactive" {{ $currentStatus === 'inactive' ? 'checked' : '' }} class="text-slate-600 focus:ring-slate-500">
                                <span>Nonaktif</span>
                            </label>
                        </div>
                        @error('status')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Approval Status --}}
                    <div>
                        <label for="approval_status" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Status Persetujuan
                        </label>
                        @php
                            $currentApproval = old('approval_status', $community->approval_status ?? 'approved');
                        @endphp
                        <div class="relative">
                            <select id="approval_status" 
                                    name="approval_status" 
                                    class="w-full h-10 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none cursor-pointer">
                                <option value="approved" {{ $currentApproval === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                                <option value="pending" {{ $currentApproval === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                                <option value="rejected" {{ $currentApproval === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="m8 10 4 4 4-4"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Rejection note if any --}}
                    @if(!empty($community->rejection_reason))
                        <div class="p-3 rounded-xl bg-rose-50/80 border border-rose-200 text-rose-800 text-[11px] leading-relaxed">
                            <p class="font-bold text-rose-900 mb-0.5">Catatan Penolakan Terakhir:</p>
                            <p>{{ $community->rejection_reason }}</p>
                        </div>
                    @endif

                    {{-- Meta Information Box --}}
                    <div class="pt-3 border-t border-slate-100 text-xs space-y-2 text-slate-500">
                        <div class="flex items-center justify-between">
                            <span>Total Anggota:</span>
                            <span class="font-bold text-slate-800">{{ number_format($community->members ?? 0) }} orang</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Dibuat:</span>
                            <span class="font-semibold text-slate-700">{{ $community->created_at ? $community->created_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        @if($community->creator)
                            <div class="flex items-center justify-between pt-1">
                                <span>Pembuat:</span>
                                <span class="font-semibold text-slate-800 truncate max-w-[150px]" title="{{ $community->creator->name }}">
                                    {{ $community->creator->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CARD 2: LOGO KOMUNITAS --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">
                        Logo Komunitas
                    </h2>

                    {{-- Logo Preview Area --}}
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50/80 border border-dashed border-slate-200 text-center">
                        <div class="w-24 h-24 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center justify-center overflow-hidden mb-3 relative group">
                            <img id="logoPreviewImg" 
                                 src="{{ $community->logo_url ? $community->logo_url : '' }}" 
                                 alt="Logo Komunitas" 
                                 class="w-full h-full object-cover {{ empty($community->logo_url) ? 'hidden' : '' }}">
                            
                            <div id="logoFallbackInitial" class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-extrabold text-2xl {{ !empty($community->logo_url) ? 'hidden' : '' }}">
                                {{ strtoupper(substr($community->name ?? 'K', 0, 1)) }}
                            </div>
                        </div>

                        <span class="text-xs font-bold text-slate-800">Ubah Logo</span>
                        <p class="text-[11px] text-slate-400 mt-0.5 mb-3">
                            Format JPG, PNG, WebP, SVG. Maks 2MB.
                        </p>

                        <label for="logoInput" class="inline-flex items-center justify-center gap-2 w-full h-9 px-3 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs cursor-pointer transition">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Pilih Berkas Logo</span>
                            <input type="file" 
                                   id="logoInput" 
                                   name="logo" 
                                   accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" 
                                   class="hidden"
                                   onchange="previewImage(this, 'logoPreviewImg', 'logoFallbackInitial')">
                        </label>
                    </div>
                    @error('logo')
                        <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CARD 3: BANNER SAMPUL --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">
                        Banner / Sampul Komunitas
                    </h2>

                    {{-- Banner Preview Area --}}
                    <div class="space-y-3">
                        <div class="w-full aspect-[21/9] rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden relative flex items-center justify-center shadow-2xs">
                            <img id="bannerPreviewImg" 
                                 src="{{ $community->cover_url ? $community->cover_url : '' }}" 
                                 alt="Banner Komunitas" 
                                 class="w-full h-full object-cover {{ empty($community->cover_url) ? 'hidden' : '' }}">
                            
                            <div id="bannerFallback" class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200/70 text-slate-400 p-4 text-center {{ !empty($community->cover_url) ? 'hidden' : '' }}">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" class="mb-1">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                                </svg>
                                <span class="text-[11px] font-medium">Belum ada banner sampul</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400">
                            Rekomendasi rasio 3:1 (Maks. 4MB). Format JPG, PNG, WebP.
                        </p>

                        <label for="bannerInput" class="inline-flex items-center justify-center gap-2 w-full h-9 px-3 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs cursor-pointer transition">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Pilih Berkas Banner</span>
                            <input type="file" 
                                   id="bannerInput" 
                                   name="banner" 
                                   accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" 
                                   class="hidden"
                                   onchange="previewImage(this, 'bannerPreviewImg', 'bannerFallback')">
                        </label>
                    </div>
                    @error('banner')
                        <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

            </div>

        </div>

        {{-- ===== BOTTOM ACTION BAR ===== --}}
        <div class="pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ $backRoute }}" 
               class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs sm:text-sm font-bold hover:bg-slate-50 hover:text-slate-900 transition shadow-xs">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs transition cursor-pointer">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Perubahan Komunitas</span>
            </button>
        </div>

    </form>
</div>

{{-- ===== SCRIPTS ===== --}}
<script>
    function previewImage(input, imgElementId, fallbackElementId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.getElementById(imgElementId);
                const fallback = document.getElementById(fallbackElementId);

                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                if (fallback) {
                    fallback.classList.add('hidden');
                }
            };

            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
