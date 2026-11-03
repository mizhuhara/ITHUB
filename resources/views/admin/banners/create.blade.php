@extends('admin.layouts.app')

@section('title', 'Tambah Banner — Admin')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.banners.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:border-slate-300 transition">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-[18px] font-bold text-slate-900">Tambah Banner Baru</h1>
            <p class="text-[12.5px] text-slate-500">Banner akan tampil sebagai slider di halaman beranda</p>
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

<form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" data-async data-async-follow>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Banner --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </span>
                    <h2 class="text-[15px] font-bold text-slate-900">Informasi Banner</h2>
                </div>

                <div>
                    <label class="form-label-sm">Judul Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="field" placeholder="Contoh: Promo Akhir Tahun" required>
                </div>

                <div>
                    <label class="form-label-sm">Subtitle</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="field" placeholder="Deskripsi singkat banner (opsional)">
                </div>

                <div>
                    <label class="form-label-sm">Link Tujuan</label>
                    <input type="url" name="link" value="{{ old('link') }}" class="field" placeholder="https://contoh.com/promo">
                </div>

                <div>
                    <label class="form-label-sm">Teks Tombol</label>
                    <input type="text" name="link_text" value="{{ old('link_text') }}" class="field" placeholder="Contoh: Selengkapnya">
                </div>
            </div>

            {{-- Gambar Banner --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/></svg>
                    </span>
                    <h2 class="text-[15px] font-bold text-slate-900">Gambar Banner</h2>
                </div>

                <p class="text-[12.5px] text-slate-500">Ukuran rekomendasi: 1200×400 piksel. Format: JPG, PNG, WebP. Maks 5MB.</p>

                {{-- Upload Mode Toggle --}}
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 text-[13px] font-medium text-slate-700 cursor-pointer">
                        <input type="radio" name="image_mode" value="upload" checked onchange="toggleImageMode('upload')" class="accent-blue-600"> Upload File
                    </label>
                    <label class="flex items-center gap-2 text-[13px] font-medium text-slate-700 cursor-pointer">
                        <input type="radio" name="image_mode" value="url" onchange="toggleImageMode('url')" class="accent-blue-600"> URL Gambar
                    </label>
                </div>

                <div id="image-upload-section">
                    <input type="file" name="image_file" accept="image/*" class="field" onchange="previewBannerImage(this)">
                </div>

                <div id="image-url-section" class="hidden">
                    <input type="url" name="image" value="{{ old('image') }}" class="field" placeholder="https://contoh.com/gambar-banner.jpg" onchange="previewBannerUrl(this)">
                </div>

                {{-- Preview --}}
                <div id="banner-preview" class="hidden mt-3">
                    <img id="banner-preview-img" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg border border-slate-200">
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- Pengaturan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    <h2 class="text-[15px] font-bold text-slate-900">Pengaturan</h2>
                </div>

                <div>
                    <label class="form-label-sm">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="field" min="0" placeholder="0">
                    <p class="text-[11.5px] text-slate-400 mt-1">Semakin kecil angka, semakin di depan urutannya.</p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-200 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="text-[13px] font-medium text-slate-700">Banner Aktif</span>
                </div>
            </div>

            {{-- Simpan --}}
            <button type="submit" class="w-full h-11 rounded-lg bg-brand-600 text-white text-[13.5px] font-semibold hover:bg-blue-700 shadow-sm transition">
                Simpan Banner
            </button>
            <a href="{{ route('admin.banners.index') }}" class="block text-center text-[13px] text-slate-500 hover:text-slate-700 transition">Batal</a>
        </div>
    </div>
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

