@extends('admin.layouts.app')

@section('title', 'Kelola Rekening Pembayaran — IndoTech Admin')

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Kelola Rekening Pembayaran
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Atur informasi rekening bank tujuan transfer peserta untuk seluruh event berbayar yang Anda buat.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.finance') }}"
               class="inline-flex items-center gap-1.5 h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Penghasilan Event
            </a>
            <a href="{{ route('admin.events.confirmations') }}"
               class="inline-flex items-center gap-1.5 h-10 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Konfirmasi Pembayaran
            </a>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('admin.events.bank-account.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Info Callout --}}
                <div class="rounded-2xl bg-blue-50/70 border border-blue-200 p-4 flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 text-sm font-bold">
                        i
                    </div>
                    <div class="text-xs text-blue-900 leading-relaxed">
                        <strong class="font-bold">Bagaimana rekening ini digunakan?</strong><br>
                        Saat peserta membuka halaman pendaftaran event berbayar Anda, sistem secara otomatis menampilkan nama bank, nomor rekening, dan atas nama rekening di bawah ini sebagai tujuan pembayaran resmi. Peserta kemudian mentransfer dan mengunggah foto bukti transfernya.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Nama Bank --}}
                    <div>
                        <label for="bank_name" class="block text-xs font-bold text-slate-700 mb-2">
                            Nama Bank / Penyedia Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="bank_name" name="bank_name" required
                               value="{{ old('bank_name', $bankAccount?->bank_name) }}"
                               placeholder="Contoh: BCA, Mandiri, BRI, BNI, Bank Jago, QRIS"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                        @error('bank_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor Rekening --}}
                    <div>
                        <label for="account_number" class="block text-xs font-bold text-slate-700 mb-2">
                            Nomor Rekening / No. Virtual Account <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="account_number" name="account_number" required
                               value="{{ old('account_number', $bankAccount?->account_number) }}"
                               placeholder="Contoh: 1234567890"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 font-mono placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                        @error('account_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Atas Nama --}}
                <div>
                    <label for="account_holder" class="block text-xs font-bold text-slate-700 mb-2">
                        Atas Nama Pemilik Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="account_holder" name="account_holder" required
                           value="{{ old('account_holder', $bankAccount?->account_holder) }}"
                           placeholder="Contoh: PT Bali IT Academy / Made Suartika"
                           class="w-full h-11 px-4 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                    @error('account_holder')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruksi Pembayaran --}}
                <div>
                    <label for="instructions" class="block text-xs font-bold text-slate-700 mb-2">
                        Petunjuk / Catatan Transfer untuk Peserta (Opsional)
                    </label>
                    <textarea id="instructions" name="instructions" rows="3"
                              placeholder="Contoh: Harap sertakan nama lengkap Anda pada kolom berita transfer. Konfirmasi tiket akan diproses maksimal 1x24 jam kerja."
                              class="w-full p-4 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">{{ old('instructions', $bankAccount?->instructions) }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- QRIS / Barcode Image --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Foto QRIS Pembayaran (Opsional)
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        @if($bankAccount?->qr_code_image)
                            <div class="relative w-24 h-24 rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 shrink-0">
                                <img src="{{ $bankAccount->qr_code_image }}" alt="QRIS" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="qr_code_image" accept="image/png,image/jpeg,image/webp"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB). Gambar QRIS akan dapat di-scan langsung oleh peserta.</p>
                        </div>
                    </div>
                    @error('qr_code_image')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer Button --}}
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Pastikan nomor rekening aktif dan benar.
                </span>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-11 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Rekening Pembayaran
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

