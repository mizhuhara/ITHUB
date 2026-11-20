@extends('admin.layouts.app')

@section('title', 'Kelola Konten Teks — Admin')
@section('header_title', 'Kelola Konten Teks')
@section('header_subtitle', 'Ubah teks judul, deskripsi banner di setiap halaman utama dan navigasi navbar.')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <form method="POST" action="{{ route('admin.site-content.update') }}" data-async class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Flash Success / Error --}}
        @if (session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13.5px] font-medium shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13px]">
                <div class="font-bold mb-1">Periksa kembali data berikut:</div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Section 1: Navbar --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Teks Navigasi Navbar</h2>
                    <p class="text-[12.5px] text-slate-500">Label menu yang tampil di baris navigasi atas (desktop & mobile).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 1 (Beranda)</label>
                    <input type="text" name="nav_home" value="{{ old('nav_home', $settings['nav_home']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 2 (Pendidikan)</label>
                    <input type="text" name="nav_education" value="{{ old('nav_education', $settings['nav_education']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 3 (Industri)</label>
                    <input type="text" name="nav_industry" value="{{ old('nav_industry', $settings['nav_industry']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 4 (Karir)</label>
                    <input type="text" name="nav_career" value="{{ old('nav_career', $settings['nav_career']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 5 (Acara)</label>
                    <input type="text" name="nav_events" value="{{ old('nav_events', $settings['nav_events']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 6 (Komunitas)</label>
                    <input type="text" name="nav_community" value="{{ old('nav_community', $settings['nav_community']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Menu 7 (Pusat Pengetahuan)</label>
                    <input type="text" name="nav_knowledge" value="{{ old('nav_knowledge', $settings['nav_knowledge']) }}" required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>
            </div>
        </div>

        {{-- Section 2: Beranda (Home) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Halaman Beranda (Home Hero)</h2>
                    <p class="text-[12.5px] text-slate-500">Judul utama dan deskripsi pada banner beranda publik.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Hero Beranda</label>
                    <input type="text" name="home_hero_title" value="{{ old('home_hero_title', $settings['home_hero_title']) }}" required
                           placeholder="Contoh: IT EKOSISTEM"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi Hero Beranda</label>
                    <textarea name="home_hero_subtitle" rows="3" required
                              placeholder="Deskripsi singkat tentang platform di beranda..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('home_hero_subtitle', $settings['home_hero_subtitle']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 3: Halaman Pendidikan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5 2 9.5l10 5 10-5-10-5Zm0 7.5v6.5m6-4.5v3.5c0 1.5-2.69 3-6 3s-6-1.5-6-3V12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Halaman Pendidikan (SMK & Perguruan Tinggi)</h2>
                    <p class="text-[12.5px] text-slate-500">Judul header dan deskripsi untuk direktori SMK dan Kampus.</p>
                </div>
            </div>

            {{-- Sub-card: Direktori SMK --}}
            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-[14px]">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Direktori SMK
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Header SMK</label>
                    <input type="text" name="education_smk_title" value="{{ old('education_smk_title', $settings['education_smk_title']) }}" required
                           placeholder="Contoh: Direktori SMK (Sekolah Menengah Kejuruan)"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi SMK</label>
                    <textarea name="education_smk_subtitle" rows="3" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('education_smk_subtitle', $settings['education_smk_subtitle']) }}</textarea>
                </div>
            </div>

            {{-- Sub-card: Perguruan Tinggi (Kampus) --}}
            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-[14px]">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    Perguruan Tinggi (Kampus)
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Header Kampus</label>
                    <input type="text" name="campus_title" value="{{ old('campus_title', $settings['campus_title']) }}" required
                           placeholder="Contoh: University (Kampus) Directory"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi Kampus</label>
                    <textarea name="campus_subtitle" rows="3" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('campus_subtitle', $settings['campus_subtitle']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 4: Halaman Industri --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <rect x="4" y="3" width="16" height="18" rx="2"/><path stroke-linecap="round" d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Halaman Industri</h2>
                    <p class="text-[12.5px] text-slate-500">Judul header dan deskripsi direktori mitra perusahaan industri.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Header Industri</label>
                    <input type="text" name="industry_title" value="{{ old('industry_title', $settings['industry_title']) }}" required
                           placeholder="Contoh: Ekosistem IT di Bali"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi Industri</label>
                    <textarea name="industry_subtitle" rows="3" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('industry_subtitle', $settings['industry_subtitle']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 5: Halaman Komunitas --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Halaman Komunitas</h2>
                    <p class="text-[12.5px] text-slate-500">Judul header dan deskripsi direktori komunitas teknologi.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Header Komunitas</label>
                    <input type="text" name="community_title" value="{{ old('community_title', $settings['community_title']) }}" required
                           placeholder="Contoh: Komunitas Tech Indonesia"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi Komunitas</label>
                    <textarea name="community_subtitle" rows="3" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('community_subtitle', $settings['community_subtitle']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 6: Halaman Pusat Pengetahuan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Halaman Pusat Pengetahuan (Knowledge Hub)</h2>
                    <p class="text-[12.5px] text-slate-500">Judul header dan deskripsi untuk artikel & tren teknologi.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul Header Knowledge Hub</label>
                    <input type="text" name="knowledge_title" value="{{ old('knowledge_title', $settings['knowledge_title']) }}" required
                           placeholder="Contoh: Knowledge Hub"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul / Deskripsi Knowledge Hub</label>
                    <textarea name="knowledge_subtitle" rows="3" required
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('knowledge_subtitle', $settings['knowledge_subtitle']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Bottom Action Bar (Static at bottom of the form) --}}
        <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 text-white shadow-sm border border-slate-800">
            <div class="text-[13px] text-slate-300 hidden sm:block">
                Pastikan teks yang dimasukkan sudah sesuai sebelum menyimpan perubahan.
            </div>
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13.5px] shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer ml-auto">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Perubahan Teks
            </button>
        </div>
    </form>
</div>
@endsection
