@extends('admin.layouts.app')

@section('title', 'Tambah AI Baru — Admin')
@section('header_title', 'Tambah AI Baru')
@section('header_subtitle', 'Tambahkan rekomendasi alat kecerdasan buatan (AI) baru ke direktori AI Info.')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <div class="mb-4">
        <a href="{{ route('admin.ai-tools.index') }}" class="inline-flex items-center gap-2 text-[13.5px] font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Kembali ke Daftar AI Info</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.ai-tools.store') }}" enctype="multipart/form-data" data-async data-async-follow class="space-y-6">
        @csrf

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

        {{-- Card 1: Identitas & Informasi Dasar --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Identitas Utama AI</h2>
                    <p class="text-[12.5px] text-slate-500">Nama resmi, kategori bidang, dan link situs resmi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Nama AI <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: GitHub Copilot"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Kategori / Bidang <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" list="category-suggestions" value="{{ old('category') }}" required
                           placeholder="Contoh: Pemrograman, Kesehatan, Pertanian"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    <datalist id="category-suggestions">
                        @foreach($existingCategories as $cat)
                            <option value="{{ $cat }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Model Lisensi / Biaya (Pricing)</label>
                    <input type="text" name="pricing" value="{{ old('pricing', 'Freemium / Berlangganan') }}"
                           placeholder="Contoh: Freemium / Berlangganan, Gratis, Berbayar"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">URL Situs Resmi (Kunjungi Situs)</label>
                    <input type="url" name="url" value="{{ old('url') }}"
                           placeholder="https://github.com/features/copilot"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Tagline / Subjudul AI</label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}"
                           placeholder="Contoh: Asisten Pemrograman AI Berbasis OpenAI Codex & GPT-4"
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Deskripsi Ringkas (Listing Card)</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                           placeholder="Ringkasan 1-2 kalimat untuk kartu di halaman katalog..."
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Foto / Logo AI</label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-dashed border-slate-300 bg-slate-50/50">
                        <div id="logo-preview-container" class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                            <img id="logo-preview" src="" alt="Preview" class="w-full h-full object-contain hidden p-1.5">
                            <div id="logo-placeholder" class="text-slate-400 flex flex-col items-center justify-center">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2 w-full">
                            <div>
                                <input type="file" name="logo_file" id="logo_file" accept="image/*"
                                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <p class="text-[11.5px] text-slate-400 mt-1">Unggah file foto / logo AI (JPG, PNG, WEBP, SVG, maks 2MB).</p>
                            </div>
                            <div class="pt-2 border-t border-slate-200/70">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-semibold text-slate-400 shrink-0">Atau URL Gambar:</span>
                                    <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url') }}"
                                           placeholder="https://example.com/logo.png"
                                           class="w-full h-8 px-2.5 rounded-lg border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Overview & Peran Utama --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Ulasan & Peran Utama</h2>
                    <p class="text-[12.5px] text-slate-500">Penjelasan apa itu AI ini, fungsinya, dan peran utama dalam pekerjaannya.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Apa itu AI ini dan Apa Fungsinya? (Overview)</label>
                    <textarea name="overview" rows="4"
                              placeholder="Jelaskan secara lengkap latar belakang dan fungsi utama alat AI ini..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('overview') }}</textarea>
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Peran Utama (Highlight Box)</label>
                    <textarea name="what_it_does" rows="3"
                              placeholder="Contoh: Mengkonversi instruksi bahasa alami menjadi kode program executable, menyarankan penyelesaian baris demi baris..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('what_it_does') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Card 3: Fitur & Kemampuan Utama --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900">Fitur & Kemampuan Utama</h2>
                        <p class="text-[12.5px] text-slate-500">Daftar kartu fitur utama yang ditampilkan di halaman detail.</p>
                    </div>
                </div>
                <button type="button" id="btn-add-capability" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Fitur
                </button>
            </div>

            <div id="capabilities-container" class="space-y-3">
                @php
                    $initialCaps = old('capabilities', [
                        ['title' => '', 'desc' => ''],
                        ['title' => '', 'desc' => ''],
                    ]);
                @endphp
                @foreach($initialCaps as $index => $cap)
                    <div class="capability-item p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative group">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider capability-number">Fitur #{{ $index + 1 }}</span>
                            <button type="button" onclick="this.closest('.capability-item').remove()" class="text-slate-400 hover:text-rose-600 text-xs font-bold transition">
                                Hapus
                            </button>
                        </div>
                        <input type="text" name="capabilities[{{ $index }}][title]" value="{{ $cap['title'] ?? '' }}" placeholder="Judul Fitur (contoh: Penyelesaian Kode Otomatis)"
                               class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                        <textarea name="capabilities[{{ $index }}][desc]" rows="2" placeholder="Penjelasan singkat kemampuan fitur ini..."
                                  class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-[13px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-y">{{ $cap['desc'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Card 4: Contoh Penggunaan & Target Pengguna --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-900">Penggunaan & Target Pengguna</h2>
                    <p class="text-[12.5px] text-slate-500">Contoh kasus penggunaan (sidebar kanan) dan siapa target penggunanya.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Contoh Penggunaan (1 baris per poin)</label>
                    <textarea name="use_cases" rows="4"
                              placeholder="Mempercepat pembuatan REST API dan komponen UI.&#10;Memahami repositori kode legacy yang besar.&#10;Mencari rujukan sintaks dan algoritma tanpa harus meninggalkan IDE."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition resize-y">{{ old('use_cases') }}</textarea>
                    <p class="text-[12px] text-slate-400 mt-1">Tiap baris otomatis menjadi bullet point di sidebar detail AI.</p>
                </div>

                <div>
                    <label class="block text-[12.5px] font-bold text-slate-700 mb-1.5">Target Pengguna</label>
                    <input type="text" name="target_audience" value="{{ old('target_audience') }}"
                           placeholder="Contoh: Pengembang Perangkat Lunak, Student Developer, Data Engineer, & Enterprise Tech Team."
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-[13.5px] text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>
            </div>
        </div>

        {{-- Bottom Action Bar --}}
        <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 text-white shadow-sm border border-slate-800">
            <div class="text-[13px] text-slate-300 hidden sm:block">
                Pastikan seluruh informasi AI sudah terisi dengan benar.
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <a href="{{ route('admin.ai-tools.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[13.5px] transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13.5px] shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan AI Baru
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('btn-add-capability')?.addEventListener('click', function () {
        const container = document.getElementById('capabilities-container');
        const count = container.querySelectorAll('.capability-item').length;
        const newIndex = Date.now();

        const item = document.createElement('div');
        item.className = 'capability-item p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative group';
        item.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider capability-number">Fitur #${count + 1}</span>
                <button type="button" onclick="this.closest('.capability-item').remove()" class="text-slate-400 hover:text-rose-600 text-xs font-bold transition">
                    Hapus
                </button>
            </div>
            <input type="text" name="capabilities[${newIndex}][title]" placeholder="Judul Fitur..."
                   class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-[13px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            <textarea name="capabilities[${newIndex}][desc]" rows="2" placeholder="Penjelasan singkat kemampuan fitur ini..."
                      class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-[13px] text-slate-900 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none resize-y"></textarea>
        `;
        container.appendChild(item);
    });
    // Logo preview handler
    const fileInput = document.getElementById('logo_file');
    const urlInput = document.getElementById('logo_url');
    const preview = document.getElementById('logo-preview');
    const placeholder = document.getElementById('logo-placeholder');

    fileInput?.addEventListener('change', function(e) {
        const file = e.target.files && e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                if (preview) {
                    preview.src = evt.target.result;
                    preview.classList.remove('hidden');
                }
                placeholder?.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    urlInput?.addEventListener('input', function(e) {
        const val = e.target.value.trim();
        if (val && (!fileInput || !fileInput.files.length)) {
            if (preview) {
                preview.src = val;
                preview.classList.remove('hidden');
            }
            placeholder?.classList.add('hidden');
        } else if (!val && (!fileInput || !fileInput.files.length)) {
            preview?.classList.add('hidden');
            placeholder?.classList.remove('hidden');
        }
    });
</script>
@endsection
