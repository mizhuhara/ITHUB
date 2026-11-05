@extends('admin.layouts.app')

@section('title', 'Edit Banner — Admin')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.banners.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:border-slate-300 transition">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-[18px] font-bold text-slate-900">Edit Banner</h1>
            <p class="text-[12.5px] text-slate-500">{{ $banner->title }}</p>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13px]">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" data-async data-async-follow>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Banner --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </span>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Informasi Banner</h2>
                        <p class="text-[12px] text-slate-400">Perbarui judul dan deskripsi yang akan tampil pada banner slider</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">
                        Judul Banner <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}" 
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                           placeholder="Contoh: Promo Akhir Tahun atau Bootcamp IT 2026" required>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Subtitle / Deskripsi Singkat</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" 
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                           placeholder="Deskripsi singkat yang tampil di bawah judul banner (opsional)">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Link Tujuan (URL)</label>
                        <input type="url" name="link" value="{{ old('link', $banner->link) }}" 
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                               placeholder="https://contoh.com/event">
                        <p class="text-[11.5px] text-slate-400 mt-1">Kosongkan jika banner tidak dapat diklik.</p>
                    </div>

                    <div>
                        <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Teks Tombol</label>
                        <input type="text" name="link_text" value="{{ old('link_text', $banner->link_text) }}" 
                               class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                               placeholder="Contoh: Selengkapnya, Daftar Sekarang">
                        <p class="text-[11.5px] text-slate-400 mt-1">Default: "Selengkapnya".</p>
                    </div>
                </div>
            </div>

            {{-- Gambar Banner --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/></svg>
                    </span>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Gambar Banner</h2>
                        <p class="text-[12px] text-slate-400">Rekomendasi rasio banner 3:1 (misal 1200×400 piksel). Format JPG, PNG, WebP (maks 5MB)</p>
                    </div>
                </div>

                {{-- Current image preview --}}
                @if($banner->image)
                    <div>
                        <p class="text-[12px] font-semibold text-slate-600 mb-1.5">Gambar Banner Saat Ini:</p>
                        <div class="relative h-48 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                            <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                        </div>
                    </div>
                @endif

                <p class="text-[12.5px] text-slate-500">Unggah berkas baru atau masukkan URL baru jika ingin mengganti gambar banner saat ini.</p>

                {{-- Upload Mode Toggle --}}
                <div class="flex gap-4 p-1.5 bg-slate-50 border border-slate-200 rounded-xl w-fit">
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[13px] font-medium text-slate-700 cursor-pointer hover:text-blue-600 transition has-[:checked]:bg-white has-[:checked]:text-blue-600 has-[:checked]:shadow-xs">
                        <input type="radio" name="image_mode" value="upload" checked onchange="toggleImageMode('upload')" class="accent-blue-600">
                        <span>Upload File</span>
                    </label>
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[13px] font-medium text-slate-700 cursor-pointer hover:text-blue-600 transition has-[:checked]:bg-white has-[:checked]:text-blue-600 has-[:checked]:shadow-xs">
                        <input type="radio" name="image_mode" value="url" onchange="toggleImageMode('url')" class="accent-blue-600">
                        <span>URL Gambar</span>
                    </label>
                </div>

                <div id="image-upload-section">
                    <label for="image_file_input_edit" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-xl p-6 bg-slate-50/50 hover:bg-blue-50/20 transition cursor-pointer group">
                        <div class="h-12 w-12 rounded-full bg-blue-50 text-brand-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[13.5px] font-semibold text-slate-700 group-hover:text-brand-600 transition">Pilih file gambar baru</span>
                        <span class="text-[12px] text-slate-400 mt-0.5">JPG, PNG, atau WebP hingga 5MB</span>
                        <input id="image_file_input_edit" type="file" name="image_file" accept="image/*" class="sr-only" onchange="previewBannerImage(this)">
                    </label>
                </div>

                <div id="image-url-section" class="hidden">
                    <label class="block text-[13px] font-medium text-slate-700 mb-1.5">Tautan Gambar Langsung</label>
                    <input type="url" name="image" value="{{ old('image') }}" 
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                           placeholder="https://images.unsplash.com/photo-..." onchange="previewBannerUrl(this)" oninput="previewBannerUrl(this)">
                </div>

                {{-- Preview --}}
                <div id="banner-preview" class="hidden">
                    <p class="text-[12px] font-semibold text-slate-600 mb-1.5">Pratinjau Gambar Baru:</p>
                    <div class="relative h-48 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                        <img id="banner-preview-img" src="" alt="Preview" class="h-full w-full object-cover">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- Pengaturan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Pengaturan</h2>
                        <p class="text-[12px] text-slate-400">Visibilitas dan prioritas urutan</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[13.5px] font-semibold text-slate-800 mb-1.5">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" 
                           class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20" 
                           min="0">
                    <p class="text-[11.5px] text-slate-400 mt-1">Semakin kecil angka (misal 0, 1, 2), semakin di depan urutannya pada slider.</p>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/20" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <div>
                            <span class="text-[13.5px] font-semibold text-slate-800 block">Banner Aktif</span>
                            <span class="text-[12px] text-slate-400 block">Tampilkan langsung pada slider halaman utama</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <button type="submit" class="w-full h-11 rounded-lg bg-brand-600 text-white text-[14px] font-semibold hover:bg-blue-700 shadow-sm transition flex items-center justify-center gap-2">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Perbarui Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="block text-center py-2.5 text-[13px] font-medium text-slate-500 hover:text-slate-800 transition">
                    Batalkan
                </a>
            </div>
        </div>
    </div>
</form>
</form>

@push('scripts')
<script>
    function toggleImageMode(mode) {
        document.getElementById('image-upload-section').classList.toggle('hidden', mode !== 'upload');
        document.getElementById('image-url-section').classList.toggle('hidden', mode !== 'url');
    }
    function previewBannerImage(input) {
        const preview = document.getElementById('banner-preview');
        const img = document.getElementById('banner-preview-img');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; preview.classList.remove('hidden'); };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function previewBannerUrl(input) {
        const preview = document.getElementById('banner-preview');
        const img = document.getElementById('banner-preview-img');
        if (input.value) { img.src = input.value; preview.classList.remove('hidden'); }
        else { preview.classList.add('hidden'); }
    }
</script>
@endpush
@endsection

