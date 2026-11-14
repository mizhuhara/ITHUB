@extends('admin.layouts.app')

@section('title', 'Edit Lowongan Pekerjaan — Admin IndoTech')

@section('content')
<form action="{{ route('admin.jobs.update', $job['id']) }}" method="POST" enctype="multipart/form-data" data-async class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13.5px]">
            <p class="font-semibold mb-1">Mohon perbaiki kesalahan berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <a href="{{ route('admin.jobs.show', $job['id']) }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            Kembali
        </a>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.jobs.show', $job['id']) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-lg text-[13.5px] transition text-center">
                Batal
            </a>
            <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-[13.5px] shadow-2xs transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/></svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Left Column (2/3 width) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Information Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Informasi Dasar Lowongan</span>
                        <span class="text-[12px] font-normal text-slate-400">Atur posisi, departemen, dan kriteria utama pekerjaan</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    {{-- Job Title --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Judul Posisi / Pekerjaan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $job['title']) }}" required
                               placeholder="Contoh: Senior Backend Engineer atau UI/UX Designer"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                        <select name="department" class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            @foreach (['Engineering', 'Product', 'Design', 'Data', 'Marketing', 'Operations', 'Finance', 'Human Resources'] as $dept)
                                <option value="{{ $dept }}" {{ old('department', $job['department'] ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Employment Type --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            @foreach (['Full-time', 'Part-time', 'Contract', 'Internship (Magang / PKL)', 'Freelance'] as $tp)
                                <option value="{{ $tp }}" {{ strtolower(old('type', $job['type'])) === strtolower($tp) || (str_contains(strtolower($job['type']), 'intern') && str_contains(strtolower($tp), 'intern')) ? 'selected' : '' }}>{{ $tp }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Salary Range --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Rentang Gaji / Uang Saku (IDR)</label>
                        <input type="text" name="salary_range" value="{{ old('salary_range', $job['salary_range'] ?? '') }}"
                               placeholder="Contoh: 8.000.000 - 15.000.000"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    {{-- Career Category --}}
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Kategori Karir</label>
                        <select name="category" class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="jobs" {{ old('category', $job['category'] ?? '') === 'jobs' ? 'selected' : '' }}>Lowongan Kerja (Reguler)</option>
                            <option value="internship" {{ old('category', $job['category'] ?? '') === 'internship' ? 'selected' : '' }}>Lowongan Magang / PKL (Internship)</option>
                            <option value="freelance" {{ old('category', $job['category'] ?? '') === 'freelance' ? 'selected' : '' }}>Freelance / Kontrak</option>
                            <option value="remote" {{ old('category', $job['category'] ?? '') === 'remote' ? 'selected' : '' }}>Remote Work</option>
                            <option value="graduate" {{ old('category', $job['category'] ?? '') === 'graduate' ? 'selected' : '' }}>Fresh Graduate</option>
                        </select>
                    </div>

                    {{-- Experience Level --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tingkat Pengalaman</label>
                        <select name="experience" class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="Entry" {{ old('experience', $job['experience'] ?? '') === 'Entry' ? 'selected' : '' }}>Entry Level (Pemula / 0 - 1 tahun)</option>
                            <option value="Mid" {{ old('experience', $job['experience'] ?? '') === 'Mid' ? 'selected' : '' }}>Mid Level (Menengah / 1 - 3 tahun)</option>
                            <option value="Senior" {{ old('experience', $job['experience'] ?? '') === 'Senior' ? 'selected' : '' }}>Senior Level (Berpengalaman / 3+ tahun)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Job Description Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Deskripsi Pekerjaan <span class="text-red-500">*</span></span>
                        <span class="text-[12px] font-normal text-slate-400">Jelaskan ringkasan peran, tugas, dan tujuan posisi ini</span>
                    </div>
                </div>

                <div>
                    <textarea name="description" rows="7" required id="desc-field"
                              placeholder="Tuliskan deskripsi lengkap posisi pekerjaan ini..."
                              class="w-full p-4 rounded-xl border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 leading-relaxed resize-y">{{ old('description', $job['description']) }}</textarea>
                    <div class="flex justify-between items-center text-[12px] text-slate-400 mt-1 font-medium">
                        <span>Gunakan deskripsi yang informatif untuk menarik kandidat terbaik.</span>
                        <div><span id="char-count">0</span> / 2000 karakter</div>
                    </div>
                </div>
            </div>

            {{-- Kualifikasi & Persyaratan Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Kualifikasi &amp; Persyaratan</span>
                        <span class="text-[12px] font-normal text-slate-400">Tambahkan poin syarat pendidikan, pengalaman, dan keahlian</span>
                    </div>
                </div>

                @php $existingRequirements = is_array($er = old('requirements', $job->requirements)) ? $er : (empty($er) ? [] : (array) $er); @endphp
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tambah Persyaratan</label>
                    <div class="flex gap-2">
                        <input type="text" id="req-input" placeholder="mis. Minimal S1 Teknik Informatika, Menguasai REST API..."
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" onkeydown="handleTagKey(event,'req-input','req-list','requirements')">
                        <button type="button" onclick="addTag('req-input','req-list','requirements')"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold rounded-lg transition shrink-0">
                            Tambah
                        </button>
                    </div>
                    <div id="req-list" class="flex flex-wrap gap-2 mt-3">
                        @foreach ($existingRequirements as $req)
                            <span class="tag-badge-green">
                                {{ $req }}
                                <button type="button" onclick="removeTag(this)" class="ml-1 text-slate-500 hover:text-red-600 font-bold leading-none">&times;</button>
                                <input type="hidden" name="requirements[]" value="{{ $req }}">
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tanggung Jawab Pekerjaan Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 20.015A9.753 9.753 0 0 1 12 21.75c-5.385 0-9.75-4.365-9.75-9.75s4.365-9.75 9.75-9.75 9.75 4.365 9.75 9.75c0 1.956-.575 3.776-1.564 5.305m-3.436-1.84c.75-.75 1.72-1.28 2.804-1.51M9 10.5a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0Zm-6 9c0-3.314 3.134-6 7-6 1.834 0 3.497.607 4.743 1.589"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Tanggung Jawab Pekerjaan</span>
                        <span class="text-[12px] font-normal text-slate-400">Rincian aktivitas dan tanggung jawab harian</span>
                    </div>
                </div>

                @php $existingResponsibilities = is_array($er = old('responsibilities', $job->responsibilities)) ? $er : (empty($er) ? [] : (array) $er); @endphp
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tambah Tanggung Jawab</label>
                    <div class="flex gap-2">
                        <input type="text" id="resp-input" placeholder="mis. Merancang arsitektur microservices, Melakukan review kode..."
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" onkeydown="handleTagKey(event,'resp-input','resp-list','responsibilities')">
                        <button type="button" onclick="addTag('resp-input','resp-list','responsibilities')"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold rounded-lg transition shrink-0">
                            Tambah
                        </button>
                    </div>
                    <div id="resp-list" class="flex flex-wrap gap-2 mt-3">
                        @foreach ($existingResponsibilities as $resp)
                            <span class="tag-badge-indigo">
                                {{ $resp }}
                                <button type="button" onclick="removeTag(this)" class="ml-1 text-indigo-800 hover:text-red-600 font-bold leading-none">&times;</button>
                                <input type="hidden" name="responsibilities[]" value="{{ $resp }}">
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Skills / Tags Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Skills / Keahlian</span>
                        <span class="text-[12px] font-normal text-slate-400">Teknologi atau kompetensi kunci yang dibutuhkan</span>
                    </div>
                </div>

                @php $existingSkills = is_array($es = old('skills', $job->skills)) ? $es : (empty($es) ? [] : (array) $es); @endphp
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Tambah Skill / Tag</label>
                    <div class="flex gap-2">
                        <input type="text" id="skill-input" placeholder="mis. Laravel, React, Tailwind, MySQL..."
                               class="flex-1 h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" onkeydown="handleTagKey(event,'skill-input','skill-list','skills')">
                        <button type="button" onclick="addTag('skill-input','skill-list','skills')"
                                class="h-11 px-5 bg-blue-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold rounded-lg transition shrink-0">
                            Tambah
                        </button>
                    </div>
                    <div id="skill-list" class="flex flex-wrap gap-2 mt-3">
                        @foreach ($existingSkills as $skill)
                            <span class="tag-badge-blue">
                                {{ $skill }}
                                <button type="button" onclick="removeTag(this)" class="ml-1 text-blue-800 hover:text-red-600 font-bold leading-none">&times;</button>
                                <input type="hidden" name="skills[]" value="{{ $skill }}">
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column (1/3 width) --}}
        <div class="space-y-6">
            {{-- Detail Perusahaan Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Detail Perusahaan</span>
                        <span class="text-[12px] font-normal text-slate-400">Profil institusi penerbit lowongan</span>
                    </div>
                </div>

                {{-- Company Notice if logged in as company --}}
                @if (!empty($isCompany) && $company)
                    <div class="bg-blue-50/80 border border-blue-200/90 rounded-xl p-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                            <span class="text-[12.5px] font-semibold text-blue-900">Perusahaan Mitra Terverifikasi</span>
                        </div>
                        <p class="text-[12px] text-blue-700 mt-1">Lowongan ini diterbitkan atas nama <strong>{{ $company->name }}</strong>.</p>
                    </div>
                @endif

                {{-- Company Name --}}
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                    @if (!empty($isCompany) && $company)
                        <input type="text" name="company" value="{{ old('company', $company->name) }}" readonly
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-200 bg-slate-50 text-[14px] text-slate-700 font-semibold cursor-not-allowed outline-none">
                    @else
                        <input type="text" name="company" value="{{ old('company', $job['company'] ?? 'TechCorp Indonesia') }}" required
                               placeholder="Nama perusahaan"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    @endif
                </div>

                {{-- Location --}}
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Lokasi Perusahaan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="absolute left-3.5 top-3 text-slate-400 pointer-events-none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <input type="text" name="location" value="{{ old('location', $job['location_full'] ?? $job['location']) }}" required
                               placeholder="Contoh: Denpasar, Bali atau Jakarta Selatan"
                               class="w-full h-11 pl-10 pr-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                {{-- Company Size --}}
                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Ukuran Perusahaan <span class="text-red-500">*</span></label>
                    <select name="company_size" class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                        <option value="1-50 Employees" {{ old('company_size', $job['company_size'] ?? '') === '1-50 Employees' || old('company_size', $job['company_size'] ?? '') === '1-50 Karyawan' ? 'selected' : '' }}>1-50 Karyawan</option>
                        <option value="51-200 Employees" {{ old('company_size', $job['company_size'] ?? '') === '51-200 Employees' || old('company_size', $job['company_size'] ?? '') === '51-200 Karyawan' ? 'selected' : '' }}>51-200 Karyawan</option>
                        <option value="201-500 Employees" {{ old('company_size', $job['company_size'] ?? '') === '201-500 Employees' || old('company_size', $job['company_size'] ?? '') === '201-500 Karyawan' ? 'selected' : '' }}>201-500 Karyawan</option>
                        <option value="500+ Employees" {{ old('company_size', $job['company_size'] ?? '') === '500+ Employees' || old('company_size', $job['company_size'] ?? '') === '500+ Karyawan' ? 'selected' : '' }}>500+ Karyawan</option>
                    </select>
                </div>

                {{-- Featured / Company Image --}}
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <label class="block text-[13.5px] font-semibold text-slate-800">Foto / Banner Lowongan</label>
                    
                    {{-- Selector URL vs Upload --}}
                    <div class="flex gap-3 p-1 bg-slate-50 border border-slate-200 rounded-lg w-fit">
                        <label class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[12.5px] font-medium text-slate-700 cursor-pointer hover:text-blue-600 transition has-[:checked]:bg-white has-[:checked]:text-blue-600 has-[:checked]:shadow-xs">
                            <input type="radio" name="image_source" value="url" checked class="accent-blue-600">
                            <span>Tautan URL</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1 rounded-md text-[12.5px] font-medium text-slate-700 cursor-pointer hover:text-blue-600 transition has-[:checked]:bg-white has-[:checked]:text-blue-600 has-[:checked]:shadow-xs">
                            <input type="radio" name="image_source" value="upload" class="accent-blue-600">
                            <span>Unggah File</span>
                        </label>
                    </div>

                    {{-- Image Preview Container --}}
                    @php
                        $currentJobImage = old('image', $job['image'] ?? $job['logo_url'] ?? '');
                    @endphp
                    <div id="image-preview-container" class="w-full h-36 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center relative shadow-inner">
                        <img id="image-preview" src="{{ $currentJobImage }}" alt="Preview" class="w-full h-full object-cover {{ !empty($currentJobImage) ? '' : 'hidden' }}" onerror="handleImageError(this)">
                        <div id="image-placeholder" class="flex flex-col items-center justify-center text-slate-400 {{ !empty($currentJobImage) ? 'hidden' : '' }}">
                            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="m21 15-5-5L5 21"/>
                            </svg>
                            <span class="text-[11.5px] mt-1 font-medium">Pratinjau Foto</span>
                        </div>
                    </div>

                    {{-- URL Field --}}
                    <div id="image-url-field">
                        <input id="image" name="image" type="text" value="{{ old('image', $job['image'] ?? $job['logo_url'] ?? '') }}"
                               placeholder="https://... atau /storage/..."
                               oninput="updateUrlPreview(this.value)"
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        <p class="text-[11.5px] text-slate-400 mt-1">Tempelkan tautan gambar publik atau path gambar banner lowongan.</p>
                    </div>

                    {{-- Upload Field --}}
                    <div id="image-upload-field" class="hidden">
                        <label for="image_file" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-xl p-4 bg-slate-50/50 hover:bg-blue-50/20 transition cursor-pointer text-center">
                            <span class="text-[13px] font-semibold text-slate-700">Pilih berkas foto</span>
                            <span class="text-[11.5px] text-slate-400 mt-0.5">JPG, PNG, atau WebP hingga 2MB</span>
                            <input id="image_file" name="image_file" type="file" accept="image/*"
                                   onchange="previewUploadedFile(this)" class="sr-only">
                        </label>
                    </div>
                </div>
            </div>

            {{-- Posting Status Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 text-slate-900 font-bold text-[16px] pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9"/></svg>
                    </div>
                    <div>
                        <span class="block leading-tight">Status Publikasi</span>
                        <span class="text-[12px] font-normal text-slate-400">Batas waktu lamaran dan visibilitas</span>
                    </div>
                </div>

                {{-- Deadline --}}
                @php
                    $rawDeadline = old('deadline', $job['deadline'] ?? '');
                    $deadlineFormatted = '';
                    if (!empty($rawDeadline)) {
                        try {
                            $deadlineFormatted = \Carbon\Carbon::parse($rawDeadline)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $deadlineFormatted = $rawDeadline;
                        }
                    }
                @endphp
                <div>
                    <label for="deadline-input" class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Deadline Lamaran</label>
                    <div class="relative">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="absolute left-3.5 top-3 text-slate-400 pointer-events-none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        <input type="date" id="deadline-input" name="deadline" value="{{ $deadlineFormatted }}"
                               onclick="this.showPicker && this.showPicker()"
                               class="w-full h-11 pl-10 pr-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                    </div>
                </div>

                {{-- Active Green Light Toggle Box --}}
                <div class="bg-emerald-50/90 border border-emerald-200/90 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <div class="text-[13.5px] font-bold text-emerald-900">Lowongan Aktif</div>
                        <div class="text-[12px] font-medium text-emerald-700">Tampilkan ke para pencari kerja</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $job['is_active'] ?? false) || $job['status'] === 'Active' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<style>
.tag-badge-green { display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; color:#334155; border:1px solid #e2e8f0; font-size:12.5px; font-weight:600; padding:4px 10px; border-radius:999px; }
.tag-badge-indigo { display:inline-flex; align-items:center; gap:4px; background:#e0e7ff; color:#3730a3; border:1px solid #c7d2fe; font-size:12.5px; font-weight:600; padding:4px 10px; border-radius:999px; }
.tag-badge-blue { display:inline-flex; align-items:center; gap:4px; background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; font-size:12.5px; font-weight:600; padding:4px 10px; border-radius:999px; }
</style>
<script>
    const initialJobImage = @json($currentJobImage);

    document.addEventListener('DOMContentLoaded', function() {
        const desc = document.getElementById('desc-field');
        const count = document.getElementById('char-count');
        if (desc && count) {
            count.textContent = desc.value.length;
            desc.addEventListener('input', function() {
                count.textContent = this.value.length;
            });
        }

        // Toggle URL vs Upload
        document.querySelectorAll('input[name="image_source"]').forEach(function(el) {
            el.addEventListener('change', function() {
                if (this.value === 'url') {
                    document.getElementById('image-url-field').classList.remove('hidden');
                    document.getElementById('image-upload-field').classList.add('hidden');
                    const urlVal = document.getElementById('image').value;
                    updateUrlPreview(urlVal);
                } else {
                    document.getElementById('image-url-field').classList.add('hidden');
                    document.getElementById('image-upload-field').classList.remove('hidden');
                    const fileInput = document.getElementById('image_file');
                    if (fileInput && fileInput.files && fileInput.files[0]) {
                        previewUploadedFile(fileInput);
                    } else if (initialJobImage) {
                        const preview = document.getElementById('image-preview');
                        const placeholder = document.getElementById('image-placeholder');
                        preview.src = initialJobImage;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    }
                }
            });
        });
    });

    function updateUrlPreview(val) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        if (val && val.trim() !== '') {
            preview.src = val;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else if (initialJobImage) {
            preview.src = initialJobImage;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }

    function previewUploadedFile(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        if (input && input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else if (initialJobImage) {
            preview.src = initialJobImage;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
    }

    function handleImageError(img) {
        img.classList.add('hidden');
        const placeholder = document.getElementById('image-placeholder');
        if (placeholder) {
            placeholder.classList.remove('hidden');
        }
    }

    // ── Tag input helpers ──────────────────────────────────────────
    function addTag(inputId, listId, fieldName) {
        const input = document.getElementById(inputId);
        const val = input.value.trim();
        if (!val) { return; }
        const list = document.getElementById(listId);
        const colorMap = {
            'requirements': 'tag-badge-green',
            'responsibilities': 'tag-badge-indigo',
            'skills': 'tag-badge-blue',
        };
        const cls = colorMap[fieldName] || 'tag-badge-blue';
        const span = document.createElement('span');
        span.className = cls;
        span.innerHTML = `${val} <button type="button" onclick="removeTag(this)" class="ml-1 hover:text-red-600 font-bold leading-none">&times;</button><input type="hidden" name="${fieldName}[]" value="${val.replace(/"/g,'&quot;')}">`;
        list.appendChild(span);
        input.value = '';
        input.focus();
    }

    function handleTagKey(event, inputId, listId, fieldName) {
        if (event.key === 'Enter') {
            event.preventDefault();
            addTag(inputId, listId, fieldName);
        }
    }

    function removeTag(btn) {
        btn.closest('span').remove();
    }
</script>
@endpush
@endsection
