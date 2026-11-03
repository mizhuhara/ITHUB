@extends('layouts.app')

@section('title', 'Profil Saya — IndoTech')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Profil Saya</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data akun, portofolio, media sosial, dan berkas CV Anda.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('applications.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition">
                    Lamaran Saya
                </a>
                <a href="{{ route('career.index') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition">
                    Cari Lowongan
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 flex items-center gap-2">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-emerald-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <div class="font-bold mb-1">Ada beberapa kesalahan input:</div>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Card Profile Summary --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-2xs">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-extrabold shadow-2xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-slate-900 truncate">{{ $user->name }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                {{ ucfirst($user->status) }}
                            </span>
                            <span class="text-xs text-slate-400">
                                Bergabung {{ $user->created_at?->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick Social, Portfolio & CV Pills --}}
                <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
                    @if($user->portfolio_url)
                        <a href="{{ str_starts_with($user->portfolio_url, 'http') ? $user->portfolio_url : 'https://' . $user->portfolio_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
                           title="Lihat Portofolio">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500">
                                <circle cx="12" cy="12" r="10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <span>Portofolio</span>
                        </a>
                    @endif

                    @if($user->instagram_url)
                        @php
                            $igHref = str_starts_with($user->instagram_url, 'http') 
                                ? $user->instagram_url 
                                : 'https://instagram.com/' . ltrim($user->instagram_url, '@');
                        @endphp
                        <a href="{{ $igHref }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
                           title="Instagram">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-linecap="round" stroke-width="2.5"/>
                            </svg>
                            <span>Instagram</span>
                        </a>
                    @endif

                    @if($user->linkedin_url)
                        @php
                            $inHref = str_starts_with($user->linkedin_url, 'http') 
                                ? $user->linkedin_url 
                                : (str_starts_with($user->linkedin_url, 'linkedin.com') ? 'https://' . $user->linkedin_url : 'https://linkedin.com/in/' . ltrim($user->linkedin_url, '/'));
                        @endphp
                        <a href="{{ $inHref }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition"
                           title="LinkedIn">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect x="2" y="9" width="4" height="12"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                            <span>LinkedIn</span>
                        </a>
                    @endif

                    @if($user->cv_url)
                        <a href="{{ $user->cv_url }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition"
                           title="Buka / Unduh CV">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-rose-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>CV (PDF)</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Data Akun & Sosial / Portofolio / CV --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-2xs">
                <h2 class="text-base font-bold text-slate-900">Data Profil, Portofolio & CV</h2>
                <p class="text-sm text-slate-500 mt-1">Perbarui foto profil, identitas, tautan portofolio, sosmed, dan berkas CV Anda.</p>

                <form method="POST" data-async data-async-reload action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Upload Foto Profil --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Profil</label>
                        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/80">
                            <div class="relative shrink-0">
                                <img id="avatar-preview-img" 
                                     src="{{ $user->avatar_url ?? '' }}" 
                                     alt="Preview" 
                                     class="{{ $user->avatar_url ? '' : 'hidden' }} w-14 h-14 rounded-xl object-cover border border-slate-200">
                                
                                <div id="avatar-fallback" 
                                     class="{{ $user->avatar_url ? 'hidden' : '' }} w-14 h-14 rounded-xl bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <label for="avatar" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold cursor-pointer shadow-2xs transition">
                                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <circle cx="12" cy="13" r="4"/>
                                        </svg>
                                        <span>Ganti Foto</span>
                                    </label>
                                    <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="previewAvatar(this)">
                                    
                                    @if($user->avatar)
                                        <label class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 cursor-pointer ml-1">
                                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 text-xs">
                                            <span>Hapus Foto</span>
                                        </label>
                                    @endif
                                </div>
                                <p id="avatar-filename" class="text-[11.5px] text-slate-400 mt-1.5 truncate">Format JPG, PNG, atau WEBP (Maks. 2MB).</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                               class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('name') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email (Readonly) --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email" id="email" value="{{ $user->email }}" readonly
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                        <p class="text-xs text-slate-400 mt-1">Email dipakai untuk login dan verifikasi akun.</p>
                    </div>

                    {{-- Section Divider: Portofolio & Media Sosial --}}
                    <div class="pt-2 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Tautan Portofolio & Sosial</h3>
                        
                        <div class="space-y-3">
                            {{-- Link Portofolio --}}
                            <div>
                                <label for="portfolio_url" class="block text-sm font-semibold text-slate-700 mb-1.5">Link Portofolio</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="portfolio_url" 
                                           id="portfolio_url" 
                                           value="{{ old('portfolio_url', $user->portfolio_url) }}" 
                                           placeholder="https://portofolio-anda.com atau link Drive / GitHub"
                                           class="w-full rounded-xl border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
                                </div>
                                @error('portfolio_url')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Instagram --}}
                            <div>
                                <label for="instagram_url" class="block text-sm font-semibold text-slate-700 mb-1.5">Instagram</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-linecap="round" stroke-width="2.5"/>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="instagram_url" 
                                           id="instagram_url" 
                                           value="{{ old('instagram_url', $user->instagram_url) }}" 
                                           placeholder="https://instagram.com/username atau @username"
                                           class="w-full rounded-xl border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
                                </div>
                                @error('instagram_url')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- LinkedIn --}}
                            <div>
                                <label for="linkedin_url" class="block text-sm font-semibold text-slate-700 mb-1.5">LinkedIn</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                            <rect x="2" y="9" width="4" height="12"/>
                                            <circle cx="4" cy="4" r="2"/>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="linkedin_url" 
                                           id="linkedin_url" 
                                           value="{{ old('linkedin_url', $user->linkedin_url) }}" 
                                           placeholder="https://linkedin.com/in/username"
                                           class="w-full rounded-xl border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
                                </div>
                                @error('linkedin_url')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section Divider: Unggah CV --}}
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Berkas Curriculum Vitae (CV)</h3>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">PDF, Maks. 2MB</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-3">
                            @if($user->cv_path)
                                <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-white border border-slate-200 text-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-800 truncate block">{{ $user->cv_name }}</span>
                                            <span class="text-[11px] text-slate-400">CV saat ini aktif tersimpan</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2.5 shrink-0">
                                        <a href="{{ $user->cv_url }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Buka CV</span>
                                        </a>
                                        <label class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 cursor-pointer">
                                            <input type="checkbox" name="remove_cv" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 text-xs">
                                            <span>Hapus</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-2.5">
                                <label for="cv" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold cursor-pointer shadow-2xs transition shrink-0">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-rose-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>{{ $user->cv_path ? 'Ganti Berkas CV' : 'Pilih Berkas CV (PDF)' }}</span>
                                </label>
                                <input type="file" name="cv" id="cv" accept="application/pdf" class="hidden" onchange="previewCv(this)">
                                <span id="cv-filename" class="text-xs text-slate-500 truncate">
                                    {{ $user->cv_path ? 'Pilih berkas PDF baru untuk mengganti.' : 'Belum ada berkas PDF dipilih.' }}
                                </span>
                            </div>
                        </div>

                        @error('cv')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Column: Ganti Password & Laporkan Masalah --}}
            <div class="space-y-6">
                {{-- Ganti Password Card --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-2xs space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Ganti Password</h2>
                        <p class="text-sm text-slate-500 mt-1">Gunakan minimal 8 karakter dengan kombinasi yang kuat.</p>

                        <form method="POST" data-async data-async-reload action="{{ route('profile.password') }}" class="mt-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                                       class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('current_password') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                                @error('current_password')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                                <input type="password" name="password" id="password" required autocomplete="new-password"
                                       class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('password') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                                @error('password')
                                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Ulangi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
                            </div>

                            <button type="submit"
                                    class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition cursor-pointer">
                                Perbarui Password
                            </button>
                        </form>
                    </div>

                    {{-- Security Tip Box --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                        <div class="font-bold text-slate-800 flex items-center gap-1.5">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Tips Profil & Portofolio</span>
                        </div>
                        <p class="leading-relaxed">Pastikan CV yang Anda unggah bertipe PDF maksimal 2MB dengan informasi kontak dan keahlian yang terbaru untuk memudahkan proses rekrutmen kerja/magang.</p>
                    </div>
                </div>

                {{-- Laporkan Masalah Card (di bawah tempat perbarui password) --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-rose-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Laporkan Masalah</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Temukan bug atau kendala sistem? Beritahu kami.</p>
                            </div>
                        </div>
                        <button type="button" id="open-report-modal" onclick="showReportModal()"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-500 hover:bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs transition cursor-pointer shrink-0">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            Laporkan Masalah
                        </button>
                    </div>

                    @if(session('report_success'))
                        {{-- Top-right floating toast alert --}}
                        <div id="report-toast" class="fixed top-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900 text-white text-[13.5px] shadow-2xl border border-slate-800 transition-all duration-300 animate-fade-in max-w-md">
                            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pr-1">
                                <p class="font-bold text-[13px] text-white">Laporan Terkirim!</p>
                                <p class="text-[12px] text-slate-300 leading-snug mt-0.5">{{ session('report_success') }}</p>
                            </div>
                            <button type="button" onclick="this.closest('#report-toast').remove()" class="text-slate-400 hover:text-white p-1 transition">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <script>
                            setTimeout(() => {
                                const toast = document.getElementById('report-toast');
                                if (toast) {
                                    toast.style.opacity = '0';
                                    toast.style.transform = 'translateY(-10px)';
                                    setTimeout(() => toast.remove(), 300);
                                }
                            }, 5000);
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Laporkan Masalah --}}
<div id="report-modal-overlay"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg animate-fade-in">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[15px] font-bold text-slate-900">Laporkan Masalah</h3>
                    <p class="text-[12px] text-slate-500">Laporan akan diteruskan ke tim IndoTech.</p>
                </div>
            </div>
            <button type="button" id="close-report-modal" onclick="hideReportModal()"
                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <form method="POST" action="{{ route('user.report.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="modal_category" class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Masalah</label>
                    <select id="modal_category" name="category" required
                            class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('category') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Bug" @selected(old('category') === 'Bug')>Bug / Error Sistem</option>
                        <option value="Konten" @selected(old('category') === 'Konten')>Konten Tidak Sesuai</option>
                        <option value="Teknis" @selected(old('category') === 'Teknis')>Masalah Teknis</option>
                        <option value="Lainnya" @selected(old('category') === 'Lainnya')>Lainnya</option>
                    </select>
                    @error('category')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="modal_priority" class="block text-sm font-semibold text-slate-700 mb-1.5">Prioritas</label>
                    <select id="modal_priority" name="priority" required
                            class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('priority') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="low" @selected(old('priority') === 'low')>Rendah</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Sedang</option>
                        <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                    </select>
                    @error('priority')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="modal_subject" class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                <input type="text" id="modal_subject" name="subject" required
                       value="{{ old('subject') }}"
                       placeholder="cth. Tombol simpan tidak berfungsi di halaman profil"
                       class="w-full rounded-xl border px-3.5 py-2.5 text-sm focus:outline-none {{ $errors->has('subject') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">
                @error('subject')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="modal_description" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Masalah</label>
                <textarea id="modal_description" name="description" rows="4" required maxlength="2000"
                          placeholder="Jelaskan masalah yang Anda temukan secara detail: langkah untuk mereproduksi, halaman yang bermasalah, dll."
                          class="w-full rounded-xl border px-3.5 py-2.5 text-sm resize-none focus:outline-none {{ $errors->has('description') ? 'border-red-300 focus:border-red-400' : 'border-slate-200 focus:border-blue-500' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between gap-3 pt-1">
                <p class="text-xs text-slate-400">Laporan akan dikirim ke tim IndoTech untuk ditindaklanjuti.</p>
                <div class="flex gap-2 shrink-0">
                    <button type="button" id="cancel-report-modal" onclick="hideReportModal()"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-500 hover:bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs transition cursor-pointer">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const previewEl = document.getElementById('avatar-preview-img');
            const fallbackEl = document.getElementById('avatar-fallback');
            const filenameEl = document.getElementById('avatar-filename');

            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewEl) {
                    previewEl.src = e.target.result;
                    previewEl.classList.remove('hidden');
                }
                if (fallbackEl) {
                    fallbackEl.classList.add('hidden');
                }
                if (filenameEl) {
                    filenameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function previewCv(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const filenameEl = document.getElementById('cv-filename');
            if (filenameEl) {
                filenameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                filenameEl.classList.add('font-semibold', 'text-slate-800');
            }
        }
    }

    // Modal Laporkan Masalah
    function showReportModal() {
        const overlay = document.getElementById('report-modal-overlay');
        if (!overlay) return;
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    function hideReportModal() {
        const overlay = document.getElementById('report-modal-overlay');
        if (!overlay) return;
        overlay.classList.remove('flex');
        overlay.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('report-modal-overlay');

        overlay?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideReportModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && !overlay.classList.contains('hidden')) {
                hideReportModal();
            }
        });

        @if($errors->hasAny(['category', 'priority', 'subject', 'description']))
            showReportModal();
        @endif
    });
</script>
@endsection
