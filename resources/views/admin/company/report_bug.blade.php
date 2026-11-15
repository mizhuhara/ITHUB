@extends('admin.layouts.app')

@section('title', 'Laporkan Masalah — IndoTech Admin')
@section('header_title', 'Laporkan Masalah')
@section('header_breadcrumb', 'Laporkan Masalah')
@section('header_subtitle', 'Kirim laporan bug sistem, kendala teknis, atau masalah konten langsung ke tim IndoTech.')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header Bar --}}
    <div>
        <a href="{{ route('dashboard.company') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-1">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Dashboard Perusahaan</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
            Laporkan Masalah / Bug
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
            Kirim laporan bug sistem, kendala teknis, atau masalah konten langsung ke tim IndoTech.
        </p>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs shadow-2xs">
            <p class="font-bold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-2xs">
        <form id="report-bug-form" action="{{ route('admin.company.report-bug.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Kategori --}}
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Masalah <span class="text-rose-500">*</span>
                    </label>
                    <select id="category" name="category" required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition {{ $errors->has('category') ? 'border-rose-300' : '' }}">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Bug" @selected(old('category') === 'Bug')>Bug / Error Sistem</option>
                        <option value="Konten" @selected(old('category') === 'Konten')>Konten Tidak Sesuai</option>
                        <option value="Teknis" @selected(old('category') === 'Teknis')>Masalah Teknis</option>
                        <option value="Lainnya" @selected(old('category') === 'Lainnya')>Lainnya</option>
                    </select>
                    @error('category')
                        <p class="text-[11.5px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prioritas --}}
                <div>
                    <label for="priority" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition {{ $errors->has('priority') ? 'border-rose-300' : '' }}">
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="low" @selected(old('priority') === 'low')>Rendah</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Sedang</option>
                        <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                    </select>
                    @error('priority')
                        <p class="text-[11.5px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Judul Laporan --}}
            <div>
                <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Laporan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="subject" name="subject" required
                       value="{{ old('subject') }}"
                       placeholder="Contoh: Tombol simpan profil perusahaan tidak merespon"
                       class="w-full h-11 px-3.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition {{ $errors->has('subject') ? 'border-rose-300' : '' }}">
                @error('subject')
                    <p class="text-[11.5px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi Masalah --}}
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi Masalah Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="6" required maxlength="2000"
                          placeholder="Jelaskan secara detail: langkah yang Anda lakukan sebelum error muncul, pesan error yang tampil, atau bagian mana yang mengalami kendala..."
                          class="w-full px-3.5 py-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white resize-none focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition {{ $errors->has('description') ? 'border-rose-300' : '' }}">{{ old('description') }}</textarea>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                    <span>Maksimal 2.000 karakter.</span>
                </div>
                @error('description')
                    <p class="text-[11.5px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-slate-100">
                <a href="{{ route('dashboard.company') }}" 
                   class="inline-flex items-center justify-center h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 transition shadow-2xs w-full sm:w-auto">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold shadow-sm transition cursor-pointer w-full sm:w-auto">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Kirim Laporan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

