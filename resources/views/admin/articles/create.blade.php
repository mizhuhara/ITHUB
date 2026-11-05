@extends('admin.layouts.app')

@section('title', 'Buat Artikel Baru — Admin')

@push('styles')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Summernote Lite CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
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
    /* Standard toolbar buttons (exclude color swatches and reset buttons) */
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

    /* Recent Color button in toolbar */
    .note-toolbar .note-current-color-button {
        padding: 4px 6px !important;
    }
    .note-toolbar .note-current-color-button .note-recent-color {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 19px !important;
        height: 19px !important;
        border-radius: 3px !important;
        font-weight: 800 !important;
        font-size: 12px !important;
        line-height: 1 !important;
        border: 1px solid rgba(0, 0, 0, 0.25) !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        font-style: normal !important;
    }
    .note-btn-group.note-color > .note-current-color-button {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-right: none !important;
    }
    .note-btn-group.note-color > .dropdown-toggle {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    /* Color Dropdown Menu styling */
    .note-color .note-dropdown-menu {
        min-width: 380px !important;
        padding: 12px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.08) !important;
    }
    .note-color .note-dropdown-menu > div {
        display: flex !important;
        gap: 16px !important;
    }
    .note-color .btn-background-color,
    .note-color .btn-foreground-color {
        flex: 1 !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }
    .note-color .note-palette-title {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
        color: #475569 !important;
        margin: 0 0 6px 0 !important;
        padding-bottom: 4px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        text-align: left !important;
    }
    .note-color .note-color-reset {
        display: block !important;
        width: 100% !important;
        padding: 5px 8px !important;
        margin: 0 0 8px 0 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        color: #475569 !important;
        cursor: pointer !important;
        text-align: center !important;
        box-shadow: none !important;
        transition: all 0.15s ease !important;
    }
    .note-color .note-color-reset:hover {
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #2563eb !important;
    }

    /* Color Palette Grid & Swatches */
    .note-color-palette {
        line-height: 1 !important;
    }
    .note-color-palette .note-color-row {
        display: flex !important;
        flex-direction: row !important;
        height: auto !important;
        margin-bottom: 2px !important;
        gap: 2px !important;
    }
    .note-color-palette .note-color-btn {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid rgba(0, 0, 0, 0.15) !important;
        border-radius: 3px !important;
        box-shadow: none !important;
        outline: none !important;
        display: inline-block !important;
        cursor: pointer !important;
        transition: transform 0.12s ease, box-shadow 0.12s ease !important;
    }
    .note-color-palette .note-color-btn:hover {
        transform: scale(1.35) !important;
        z-index: 20 !important;
        position: relative !important;
        border-color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
    }

    /* Custom HTML5 color picker */
    .note-color input[type="color"] {
        height: 30px !important;
        width: 100% !important;
        padding: 1px 2px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        background: #ffffff !important;
        cursor: pointer !important;
        margin-top: 6px !important;
        box-sizing: border-box !important;
    }
    .note-dropdown-menu {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1) !important;
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
    .note-modal {
        z-index: 1050 !important;
    }
    .note-modal-backdrop {
        z-index: 1040 !important;
    }
    .note-modal .modal-content {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
    }
    .note-modal .modal-header {
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 16px 20px !important;
    }
    .note-modal .modal-footer {
        border-top: 1px solid #e2e8f0 !important;
        padding: 14px 20px !important;
    }
    .note-modal .btn-primary {
        background: #2563eb !important;
        border-color: #2563eb !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 16px !important;
    }
    .note-editable {
        font-family: inherit !important;
        font-size: 14px !important;
        color: #1e293b !important;
        line-height: 1.7 !important;
        padding: 18px !important;
        background-color: #ffffff !important;
    }
    .note-editable ul {
        list-style-type: disc !important;
        padding-left: 24px !important;
    }
    .note-editable ol {
        list-style-type: decimal !important;
        padding-left: 24px !important;
    }
    .note-editable h1 { font-size: 2rem !important; font-weight: 700 !important; margin-bottom: 0.5rem !important; }
    .note-editable h2 { font-size: 1.5rem !important; font-weight: 700 !important; margin-bottom: 0.5rem !important; }
    .note-editable h3 { font-size: 1.25rem !important; font-weight: 600 !important; margin-bottom: 0.5rem !important; }
    .note-editable h4 { font-size: 1.125rem !important; font-weight: 600 !important; margin-bottom: 0.5rem !important; }
    .note-editable p { margin-bottom: 0.75rem !important; }
    .note-editable table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 1rem !important; }
    .note-editable table td, .note-editable table th { border: 1px solid #cbd5e1 !important; padding: 6px 10px !important; }
    .note-editable blockquote { border-left: 4px solid #2563eb !important; padding-left: 16px !important; color: #64748b !important; font-style: italic !important; }
    .note-statusbar {
        border-top: 1px solid #f1f5f9 !important;
        background: #f8fafc !important;
    }
</style>
@endpush

@section('content')
{{-- Actions --}}
<div class="flex items-center justify-end mb-6 gap-3">
    <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center h-10 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 text-[13.5px] font-semibold hover:bg-slate-50 transition">
        Batal
    </a>
    <button type="submit" form="article-form" class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-brand-600 text-white text-[13.5px] font-semibold hover:bg-blue-700 shadow-sm transition">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h11l3 3v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1zm3 0v5h8V4M7 15h10m-10 4h10"/></svg>
        Simpan Artikel
    </button>
</div>
{{-- Validation errors --}}
@if ($errors->any())
    <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13.5px]">
        <p class="font-semibold mb-1">Mohon perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="article-form" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" data-async data-async-reset>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT: Main form ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Article Title --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <label for="title" class="block text-[13.5px] font-semibold text-slate-800 mb-2">
                    Judul Artikel <span class="text-red-500">*</span>
                </label>
                <input id="title" name="title" type="text" required placeholder="Masukkan judul artikel..."
                       value="{{ old('title') }}"
                       oninput="autoSlug(this.value)"
                       class="w-full h-11 px-3.5 rounded-lg border border-slate-300 text-[14px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">

                {{-- Slug --}}
                <label for="slug" class="block text-[13.5px] font-semibold text-slate-800 mt-5 mb-2">Slug URL</label>
                <div class="flex items-center h-11 rounded-lg border border-slate-300 bg-white overflow-hidden focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-blue-500/20 transition">
                    <span class="h-full px-3.5 flex items-center text-[13px] text-slate-400 bg-slate-50 border-r border-slate-200 whitespace-nowrap">indotech.com/blog/</span>
                    <input id="slug" name="slug" type="text" placeholder="slug-artikel" value="{{ old('slug') }}"
                           class="h-full flex-1 min-w-0 px-3.5 text-[14px] text-slate-900 placeholder-slate-400 outline-none bg-transparent">
                </div>
                <p class="text-[12px] text-slate-400 mt-1.5">Dihasilkan otomatis dari judul. Anda dapat mengubahnya sesuai kebutuhan.</p>
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <label for="excerpt" class="block text-[13.5px] font-semibold text-slate-800 mb-2">Ringkasan / Sinopsis Artikel</label>
                <textarea id="excerpt" name="excerpt" rows="3" placeholder="Ringkasan singkat isi artikel (ditampilkan pada kartu artikel)…"
                          class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 resize-none">{{ old('excerpt') }}</textarea>
            </div>

            {{-- Content (Summernote) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <label for="content" class="block text-[13.5px] font-semibold text-slate-800 mb-2">
                    Konten Lengkap Artikel <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content">{{ old('content') }}</textarea>
            </div>
        </div>

        {{-- ===== RIGHT: Sidebar panel ===== --}}
        <div class="space-y-6">

            {{-- Publish Status --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                @php
                    $isSuperAdmin = in_array(auth()->user()?->role, ['super_admin', 'admin'], true);
                @endphp

                @if(!$isSuperAdmin)
                    <div class="bg-amber-50 border border-amber-200/90 rounded-xl p-3.5 mb-3.5">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                            <p class="text-[12px] text-amber-800 leading-relaxed font-medium">
                                Artikel yang dikirim akan berstatus <strong>Pending</strong> dan menunggu persetujuan (approval) dari Super Admin sebelum tayang di website.
                            </p>
                        </div>
                    </div>
                @endif

                <label for="status" class="block text-[13.5px] font-semibold text-slate-800 mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select id="status" name="status"
                            class="w-full h-11 px-3.5 pr-9 rounded-lg border border-slate-300 bg-white text-[14px] text-slate-900 appearance-none outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @if(!$isSuperAdmin)
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Kirim untuk Approval (Pending)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan Sebagai Draf (Draft)</option>
                        @else
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Langsung Tayang)</option>
                            <option value="pending"   {{ old('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                            <option value="draft"     {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (Draf)</option>
                            <option value="archived"  {{ old('status') === 'archived' ? 'selected' : '' }}>Archived (Arsip)</option>
                        @endif
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m8 10 4 4 4-4"/></svg>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <label for="category" class="block text-[13.5px] font-semibold text-slate-800 mb-2">Kategori Artikel <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select id="category" name="category"
                            class="w-full h-11 px-3.5 pr-9 rounded-lg border border-slate-300 bg-white text-[14px] text-slate-900 appearance-none outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                        @foreach (['Technology','Education','Career','News','AI','Programming','Web Development','Cybersecurity','Cloud','Database','UI/UX','Press Release'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m8 10 4 4 4-4"/></svg>
                </div>
            </div>

            {{-- Author --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-[14px] font-bold text-slate-800">Informasi Penulis</h3>
                <div>
                    <label for="author_name" class="form-label-sm">Nama Penulis</label>
                    <input id="author_name" name="author_name" type="text" value="{{ old('author_name', $defaultAuthor ?? '') }}"
                           placeholder="mis. Budi Santoso"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="author_role" class="form-label-sm">Profesi / Peran Penulis</label>
                    <input id="author_role" name="author_role" type="text" value="{{ old('author_role', $defaultRole ?? '') }}"
                           placeholder="mis. Senior Software Engineer"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="read_time" class="form-label-sm">Estimasi Waktu Baca</label>
                    <input id="read_time" name="read_time" type="text" value="{{ old('read_time') }}"
                           placeholder="mis. 5 menit baca"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <label class="block text-[13.5px] font-semibold text-slate-800 mb-2">Gambar Utama / Thumbnail</label>
                <div class="flex items-center space-x-4 mb-2">
                    <label class="inline-flex items-center text-[13px] text-slate-700 cursor-pointer">
                        <input type="radio" name="image_source" value="url" checked class="mr-1.5 accent-blue-600">
                        Tautan URL
                    </label>
                    <label class="inline-flex items-center text-[13px] text-slate-700 cursor-pointer">
                        <input type="radio" name="image_source" value="upload" class="mr-1.5 accent-blue-600">
                        Unggah Berkas
                    </label>
                </div>
                <div id="image-url-field" class="mb-2">
                    <input id="image" name="image" type="url" value="{{ old('image') }}"
                           placeholder="https://images.unsplash.com/…"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    <p class="text-[12px] text-slate-400 mt-1.5">Tempel tautan URL gambar publik untuk thumbnail artikel.</p>
                </div>
                <div id="image-upload-field" class="hidden mb-2">
                    <input id="image_file" name="image_file" type="file" accept="image/*"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    <p class="text-[12px] text-slate-400 mt-1.5">Unggah berkas foto gambar untuk thumbnail artikel (maks 2MB).</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <label for="image_caption" class="block text-[13px] font-semibold text-slate-700 mb-1.5">Keterangan / Caption Gambar</label>
                    <input id="image_caption" name="image_caption" type="text" value="{{ old('image_caption') }}"
                           placeholder="Masukkan caption gambar (contoh: Dokumentasi sesi workshop IndoTech)"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                    <p class="text-[12px] text-slate-400 mt-1.5">Keterangan gambar akan ditampilkan tepat di bawah foto utama artikel.</p>
                </div>
                <script>
                    document.querySelectorAll('input[name="image_source"]').forEach(function(el){
                        el.addEventListener('change', function(){
                            if(this.value === 'url'){
                                document.getElementById('image-url-field').classList.remove('hidden');
                                document.getElementById('image-upload-field').classList.add('hidden');
                            } else {
                                document.getElementById('image-url-field').classList.add('hidden');
                                document.getElementById('image-upload-field').classList.remove('hidden');
                            }
                        });
                    });
                </script>
            </div>

            {{-- Meta & SEO --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
                <h3 class="text-[14px] font-bold text-slate-800">Pengaturan Meta & SEO</h3>
                <div>
                    <label for="tags" class="form-label">Tag / Kata Kunci (pisahkan dengan koma)</label>
                    <input id="tags" name="tags" type="text" value="{{ old('tags') }}"
                           placeholder="mis. AI, Teknologi, Pemrograman"
                           class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="meta_description" class="form-label">Deskripsi Meta (SEO)</label>
                    <textarea id="meta_description" name="meta_description" rows="3" placeholder="Deskripsi singkat artikel untuk mesin pencari..."
                              class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-[13.5px] text-slate-900 placeholder-slate-400 bg-white outline-none transition focus:border-brand-600 focus:ring-2 focus:ring-blue-500/20 resize-none">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<!-- Summernote Lite JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#content').summernote({
            placeholder: 'Tulis isi konten artikel lengkap di sini...',
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
                onChange: function(contents, $editable) {
                    $('#content').val(contents);
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
                            $('#content').summernote('insertImage', data.url);
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

        // Ensure initial recent-color swatches are visible
        $('.note-current-color-button .note-recent-color').each(function() {
            var $el = $(this);
            var $btn = $el.closest('.note-current-color-button');
            var bg = $btn.attr('data-backcolor') || '#FFFF00';
            var fg = $btn.attr('data-forecolor') || '#000000';
            $el.css({
                'background-color': bg,
                'color': fg
            });
        });

        // Listen for color changes in the palette to update the recent color preview
        $(document).on('click', '.note-color-btn, .note-color-reset', function() {
            var $btn = $(this);
            var eventName = $btn.data('event');
            var value = $btn.data('value');
            var $container = $btn.closest('.note-color');
            var $recent = $container.find('.note-recent-color');
            if (eventName === 'backColor') {
                $recent.css('background-color', value === 'transparent' ? 'transparent' : value);
            } else if (eventName === 'foreColor' || eventName === 'removeFormat') {
                $recent.css('color', value === 'foreColor' ? '#000000' : value);
            }
        });

        // Ensure Summernote content syncs before form submit
        $('#article-form').on('submit', function() {
            var code = $('#content').summernote('code');
            $('#content').val(code);
        });
    });

    function autoSlug(value) {
        const slug = document.getElementById('slug');
        if (slug.dataset.touched !== 'true') {
            slug.value = value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/[\s_]+/g, '-')
                .replace(/-+/g, '-');
        }
    }
    document.getElementById('slug').addEventListener('input', function () {
        this.dataset.touched = 'true';
    });
</script>
@endpush
