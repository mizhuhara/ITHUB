@extends('admin.layouts.app')

@section('title', 'Promo Popup - Admin')
@section('header_title', 'Promo Popup')
@section('header_subtitle', 'Atur popup promosi yang muncul sekali di beranda setiap sesi browser.')

@section('content')
<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-6">

    <form method="POST" action="{{ route('admin.promo-popup.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @if (session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13.5px] font-medium">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13px]">
                <div class="font-bold mb-1">Periksa kembali input data:</div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Status Popup</h2>
                    <p class="text-[12.5px] text-slate-500 mt-1">Jika dimatikan, popup tidak tampil di beranda.</p>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(old('enabled', $settings['enabled']))>
                    <span class="text-[13px] font-semibold text-slate-700">Aktifkan</span>
                </label>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs space-y-5">
            <div>
                <h2 class="text-[16px] font-bold text-slate-900">Isi Promo</h2>
                <p class="text-[12.5px] text-slate-500 mt-1">Teks singkat lebih nyaman dibaca di popup.</p>
            </div>

            <div>
                <label for="title" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Judul</label>
                <input id="title" name="title" type="text" required value="{{ old('title', $settings['title']) }}"
                       class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>

            <div>
                <label for="subtitle" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Subjudul</label>
                <textarea id="subtitle" name="subtitle" rows="3"
                          class="w-full px-3.5 py-3 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-y">{{ old('subtitle', $settings['subtitle']) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="button_text" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Teks Tombol</label>
                    <input id="button_text" name="button_text" type="text" value="{{ old('button_text', $settings['button_text']) }}"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
                <div>
                    <label for="button_link" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Link Tombol</label>
                    <input id="button_link" name="button_link" type="text" value="{{ old('button_link', $settings['button_link']) }}" placeholder="/career atau https://..."
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs space-y-5">
            <div>
                <h2 class="text-[16px] font-bold text-slate-900">Gambar & Desain</h2>
                <p class="text-[12.5px] text-slate-500 mt-1">Gunakan gambar landscape. Rekomendasi 1200 x 675 px.</p>
            </div>

            <div>
                <label for="image" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">URL Gambar</label>
                <input id="image" name="image" type="text" value="{{ old('image', $settings['image']) }}" placeholder="https://..."
                       class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            </div>

            <div>
                <label for="image_file" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Upload Gambar</label>
                <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                       class="block w-full text-[13px] text-slate-600 file:mr-3 file:px-4 file:py-2.5 file:rounded-xl file:border-0 file:bg-blue-50 file:text-blue-700 file:text-[12.5px] file:font-bold hover:file:bg-blue-100">
                <label class="inline-flex items-center gap-2 mt-3 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-[12.5px] font-medium text-slate-600">Hapus gambar saat ini</span>
                </label>
            </div>

            <div>
                <label for="theme" class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Tema</label>
                <select id="theme" name="theme" class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    <option value="light" @selected(old('theme', $settings['theme']) === 'light')>Terang</option>
                    <option value="dark" @selected(old('theme', $settings['theme']) === 'dark')>Gelap</option>
                    <option value="brand" @selected(old('theme', $settings['theme']) === 'brand')>Biru Brand</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center h-11 px-5 rounded-xl border border-slate-300 text-[13.5px] font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="inline-flex items-center h-11 px-5 rounded-xl bg-blue-600 text-white text-[13.5px] font-bold hover:bg-blue-700 shadow-sm transition">Simpan Pengaturan</button>
        </div>
    </form>

    <aside class="space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <h2 class="text-[14px] font-bold text-slate-900">Pratinjau</h2>
            <p class="text-[12px] text-slate-500 mt-1">Tampilan popup di beranda.</p>

            <div id="promo-preview" class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div id="promo-preview-image-wrap" class="{{ $settings['image'] ? '' : 'hidden' }} relative aspect-[16/9] bg-slate-100">
                    <img id="promo-preview-image" src="{{ $settings['image'] }}" alt="" class="h-full w-full object-cover">
                </div>
                <div class="p-5">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">PROMO</p>
                    <h3 id="promo-preview-title" class="mt-1 text-[17px] font-black text-slate-900 leading-tight">{{ $settings['title'] }}</h3>
                    <p id="promo-preview-subtitle" class="mt-1.5 text-[12.5px] text-slate-600 leading-relaxed">{{ $settings['subtitle'] }}</p>
                    <span id="promo-preview-button" class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-2 text-[12px] font-bold text-white">{{ $settings['button_text'] ?: 'Lihat Promo' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-[12.5px] text-blue-900 leading-relaxed">
            Popup hanya muncul di beranda, sekali per sesi browser, dan muncul lagi setelah login. Super Admin tidak melihat popup.
        </div>
    </aside>
</div>

<script>
    (function () {
        var title = document.getElementById('title');
        var subtitle = document.getElementById('subtitle');
        var buttonText = document.getElementById('button_text');
        var image = document.getElementById('image');
        var imageFile = document.getElementById('image_file');
        var previewTitle = document.getElementById('promo-preview-title');
        var previewSubtitle = document.getElementById('promo-preview-subtitle');
        var previewButton = document.getElementById('promo-preview-button');
        var previewImage = document.getElementById('promo-preview-image');
        var previewImageWrap = document.getElementById('promo-preview-image-wrap');

        function updateText() {
            if (previewTitle) previewTitle.textContent = title.value || 'Judul promo';
            if (previewSubtitle) previewSubtitle.textContent = subtitle.value || 'Subjudul promo';
            if (previewButton) previewButton.textContent = buttonText.value || 'Lihat Promo';
        }

        [title, subtitle, buttonText].forEach(function (field) {
            if (field) field.addEventListener('input', updateText);
        });

        if (image) {
            image.addEventListener('input', function () {
                if (image.value) {
                    previewImage.src = image.value;
                    previewImageWrap.classList.remove('hidden');
                }
            });
        }

        if (imageFile) {
            imageFile.addEventListener('change', function () {
                if (imageFile.files && imageFile.files[0]) {
                    previewImage.src = URL.createObjectURL(imageFile.files[0]);
                    previewImageWrap.classList.remove('hidden');
                }
            });
        }
    })();
</script>
@endsection
