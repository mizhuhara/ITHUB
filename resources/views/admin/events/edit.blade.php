@extends('admin.layouts.app')

@section('title', 'Edit Event — ' . $event->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.events.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-1">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Daftar Event</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Edit Event
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Perbarui detail agenda event '{{ $event->title }}'.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.events.index') }}" 
               class="flex-1 sm:flex-initial inline-flex items-center justify-center h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                Batal
            </a>
            <button type="submit" form="event-form" 
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 h-10 px-5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 shadow-sm transition">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Perbarui Event</span>
            </button>
        </div>
    </div>

    {{-- Validation Errors Alert --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <p class="font-bold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form id="event-form" action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== LEFT COLUMN: Main Info ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Judul & Kategori --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-5">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                        Informasi Utama Event
                    </h2>

                    {{-- Judul Event --}}
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama / Judul Event <span class="text-rose-500">*</span>
                        </label>
                        <input id="title" name="title" type="text" required 
                               value="{{ old('title', $event->title) }}"
                               placeholder="Contoh: Workshop Cloud Computing & Devops untuk SMK"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    {{-- Grid Kategori & Mode --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kategori --}}
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="category" name="category" required
                                        class="w-full h-10 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none">
                                    @php
                                        $currentCat = old('category', $event->category);
                                    @endphp
                                    @foreach(['Workshop', 'Webinar', 'Seminar', 'Bootcamp', 'Conference', 'Competition', 'Festival', 'Lainnya'] as $cat)
                                        <option value="{{ $cat }}" {{ $currentCat === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                            </div>
                        </div>

                        {{-- Mode Pelaksanaan --}}
                        <div>
                            <label for="mode" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Mode Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                @php
                                    $currentMode = old('mode', $event->mode ?? 'In-Person');
                                @endphp
                                <select id="mode" name="mode" required
                                        class="w-full h-10 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none">
                                    <option value="Online" {{ $currentMode === 'Online' ? 'selected' : '' }}>Online (Webinar / Virtual)</option>
                                    <option value="In-Person" {{ $currentMode === 'In-Person' ? 'selected' : '' }}>In-Person (Tatap Muka / Onsite)</option>
                                    <option value="Hybrid" {{ $currentMode === 'Hybrid' ? 'selected' : '' }}>Hybrid (Online & Onsite)</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Lokasi / Venue --}}
                    <div>
                        <label for="location" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Lokasi / Venue (Event Offline)
                        </label>
                        <input id="location" name="location" type="text"
                               value="{{ old('location', $event->location) }}"
                               placeholder="Contoh: Gedung Serbaguna Kampus 1"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    @include('admin.events.partials.meeting-fields', ['event' => $event])

                    {{-- Deskripsi Event --}}
                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Deskripsi & Agenda Event
                        </label>
                        <textarea id="description" name="description" rows="7"
                                  placeholder="Jelaskan gambaran umum, target peserta, agenda, dan persyaratan event..."
                                  class="w-full p-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 resize-y leading-relaxed">{{ old('description', $event->description) }}</textarea>
                    </div>
                </div>

                {{-- Banner / Foto Event --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                        Banner / Gambar Event
                    </h2>

                    @if($event->image)
                        <div class="space-y-1.5">
                            <span class="text-[11px] font-bold text-slate-500">Banner Saat Ini:</span>
                            <div class="rounded-xl overflow-hidden border border-slate-200 max-h-56">
                                <img src="{{ $event->image }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Ganti Gambar Banner (Opsional)
                        </label>
                        <input type="file" name="image" id="event-image-input" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Kosongkan jika tidak ingin mengubah banner saat ini. Maks 2MB.
                        </p>
                    </div>

                    <div id="image-preview-container" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200 max-h-56">
                        <img id="image-preview" src="" alt="Preview" class="w-full h-full object-cover">
                    </div>
                </div>

                @include('admin.events.partials.certificate-fields', ['event' => $event])

            </div>

            {{-- ===== RIGHT COLUMN: Schedule, Organizer & Quota ===== --}}
            <div class="space-y-6">

                {{-- Waktu & Biaya --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                        Waktu & Biaya
                    </h2>

                    {{-- Tanggal --}}
                    <div>
                        <label for="date" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                        </label>
                        <input id="date" name="date" type="date" required
                               value="{{ old('date', $event->date) }}"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    {{-- Waktu / Jam --}}
                    <div>
                        <label for="time" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jam Pelaksanaan
                        </label>
                        <input id="time" name="time" type="text"
                               value="{{ old('time', $event->time ?? '09:00 - 15:00 WIB') }}"
                               placeholder="Contoh: 09:00 - 12:30 WIB"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    {{-- Biaya / Tiket --}}
                    <div>
                        <label for="price" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Biaya Masuk / Tiket
                        </label>
                        <input id="price" name="price" type="text"
                               value="{{ old('price', $event->price ?? 'Free') }}"
                               placeholder="Free atau contoh: Rp 50.000"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    {{-- Total Kuota --}}
                    <div>
                        <label for="total_quota" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Kuota Peserta (Maksimal)
                        </label>
                        <input id="total_quota" name="total_quota" type="number" min="1"
                               value="{{ old('total_quota', $event->total_quota ?? 100) }}"
                               placeholder="Contoh: 100"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>
                </div>

                {{-- Kontak Penyelenggara --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                    <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                        Kontak & Penyelenggara
                    </h2>

                    @if($isSchool)
                        @php
                            $instName = $event->organizer;
                            $instType = $event->organizer_type ?: ((isset($company) && $company) || (isset($userRole) && $userRole === 'company') ? 'Perusahaan (Company)' : (isset($univ) && $univ ? 'Universitas (University)' : 'Sekolah (School)'));
                        @endphp
                        {{-- Institution Info Box --}}
                        <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-1">
                            <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Penyelenggara</span>
                            <p class="text-xs font-extrabold text-blue-900">
                                {{ $instName }}
                            </p>
                            <p class="text-[11px] text-blue-700">
                                Tipe: <strong>{{ $instType }}</strong>
                            </p>
                            <p class="text-[11px] text-slate-600 mt-1">
                                Status saat ini: <strong>{{ $event->status }}</strong>
                            </p>
                        </div>
                    @else
                        {{-- Admin can edit Organizer & Type --}}
                        <div>
                            <label for="organizer" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nama Penyelenggara
                            </label>
                            <input id="organizer" name="organizer" type="text"
                                   value="{{ old('organizer', $event->organizer) }}"
                                   class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                        </div>

                        <div>
                            <label for="organizer_type" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tipe Penyelenggara
                            </label>
                            <div class="relative">
                                @php
                                    $currType = old('organizer_type', $event->organizer_type);
                                @endphp
                                <select id="organizer_type" name="organizer_type"
                                        class="w-full h-10 px-3.5 pr-8 rounded-xl border border-slate-200 text-xs text-slate-900 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 appearance-none">
                                    <option value="School" {{ $currType === 'School' ? 'selected' : '' }}>School (Sekolah)</option>
                                    <option value="University" {{ $currType === 'University' ? 'selected' : '' }}>University (Universitas)</option>
                                    <option value="Company" {{ $currType === 'Company' ? 'selected' : '' }}>Company (Perusahaan)</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m8 10 4 4 4-4"/></svg>
                            </div>
                        </div>
                    @endif

                    {{-- Email Kontak --}}
                    <div>
                        <label for="contact_email" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Email Kontak / Pertanyaan
                        </label>
                        <input id="contact_email" name="contact_email" type="email"
                               value="{{ old('contact_email', $event->contact_email) }}"
                               placeholder="kontak@sekolah.sch.id"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    {{-- No. Telp / WhatsApp --}}
                    <div>
                        <label for="contact_phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            No. WhatsApp / Telepon
                        </label>
                        <input id="contact_phone" name="contact_phone" type="text"
                               value="{{ old('contact_phone', $event->contact_phone) }}"
                               placeholder="Contoh: 08123456789"
                               class="w-full h-10 px-3.5 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10">
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
    const input = document.getElementById('event-image-input');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');

    if (input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }
</script>
@endpush
@endsection

