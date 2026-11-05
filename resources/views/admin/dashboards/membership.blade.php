@extends('admin.layouts.app')

@section('title', 'Status Membership & Perpanjangan — IndoTech')
@section('header_title', 'Status Keanggotaan & Perpanjangan Membership')
@section('header_subtitle', 'Pantau masa aktif keanggotaan institusi, informasi rekening pembayaran, dan ajukan perpanjangan keanggotaan.')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13.5px] font-medium shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13.5px] font-medium shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13px] shadow-xs">
            <div class="font-bold mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Periksa kembali input formulir Anda:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $hasPendingPayment = $payments->where('status', 'pending')->first();
    @endphp

    @if ($hasPendingPayment)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-[13.5px] flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-brand-600 shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold text-[14px]">Bukti Pembayaran Sedang Dicek Manual oleh Super Admin</p>
                <p class="mt-0.5 text-blue-800">
                    Konfirmasi pembayaran <strong>{{ $hasPendingPayment->plan_label }} (Rp {{ number_format($hasPendingPayment->amount, 0, ',', '.') }})</strong> yang Anda kirimkan pada {{ $hasPendingPayment->created_at->translatedFormat('d M Y H:i') }} WIB sedang dalam proses validasi manual oleh tim Super Admin. Masa aktif premium Anda akan otomatis aktif segera setelah validasi disetujui.
                </p>
            </div>
        </div>
    @endif

    @if ($isGlobalDisabled)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-[13.5px] flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold text-[14px]">Mode Akses Terbuka Aktif</p>
                <p class="mt-0.5 text-blue-700">Administrator sedang menonaktifkan pembatasan keanggotaan sementara waktu. Profil institusi Anda saat ini tetap tampil di direktori publik tanpa batasan masa berlaku.</p>
            </div>
        </div>
    @elseif (!$isPubliclyVisible)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[13.5px] flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="font-bold text-[14px]">Masa Keanggotaan Telah Berakhir</p>
                <p class="mt-0.5 text-amber-800">Masa trial gratis 1 bulan atau keanggotaan institusi Anda telah berakhir. Saat ini profil institusi Anda disembunyikan dari direktori publik sampai perpanjangan keanggotaan diverifikasi.</p>
            </div>
        </div>
    @elseif ($user->membership_days_remaining <= 5)
        <div class="p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-900 text-[13.5px] flex items-start gap-3">
            <svg class="w-5 h-5 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold text-[14px]">Masa Berlaku Segera Berakhir</p>
                <p class="mt-0.5 text-orange-800">Sisa masa aktif Anda tersisa <strong>{{ $user->membership_days_remaining }} hari lagi</strong>. Lakukan perpanjangan segera agar profil institusi tetap dapat diakses publik.</p>
            </div>
        </div>
    @endif

    {{-- Main Status Overview Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[12px] font-bold text-slate-400 uppercase tracking-wider">
                            {{ match($user->role) { 'school', 'school_admin' => 'Institusi Sekolah / SMK', 'university', 'univ_rep' => 'Perguruan Tinggi / Kampus', 'company', 'company_hr' => 'Perusahaan Mitra', default => 'Institusi' } }}
                        </div>
                        <h2 class="text-[20px] font-black text-slate-900 tracking-tight mt-0.5">
                            {{ $institution->name ?? $user->name }}
                        </h2>
                        <p class="text-[13px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-semibold {{ $user->membership_badge_class }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                        {{ $user->membership_status_label }}
                    </span>

                    @if ($isPubliclyVisible)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Tayang di Publik
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <svg class="w-3.5 h-3.5 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Profil Tersembunyi
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stat Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[11.5px] font-semibold text-slate-400 uppercase tracking-wider block">Sisa Masa Aktif</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-black text-slate-900 leading-none">{{ $user->membership_days_remaining }}</span>
                        <span class="text-[13px] font-medium text-slate-500">Hari</span>
                    </div>
                    <span class="text-[11.5px] text-slate-500 mt-1 block">
                        @if ($user->membership_expires_at)
                            Berakhir {{ $user->membership_expires_at->translatedFormat('d F Y') }}
                        @else
                            Masa aktif tidak terbatas
                        @endif
                    </span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[11.5px] font-semibold text-slate-400 uppercase tracking-wider block">Tanggal Mulai Pendaftaran</span>
                    <div class="mt-1.5">
                        <span class="text-[16px] font-bold text-slate-900">
                            {{ $user->membership_started_at ? $user->membership_started_at->translatedFormat('d M Y') : $user->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <span class="text-[11.5px] text-slate-500 mt-1 block">
                        Paket: {{ $user->membership_plan === 'trial_1_month' ? 'Trial 1 Bulan Gratis' : ($user->membership_plan === '1_year' ? 'Paket 1 Tahun' : 'Paket 1 Bulan') }}
                    </span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[11.5px] font-semibold text-slate-400 uppercase tracking-wider block">Status Fitur Premium</span>
                    <div class="mt-1.5 flex items-center gap-2">
                        @if ($user->is_premium_active)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[16px] font-bold text-emerald-700">Diaktifkan</span>
                        @else
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-[16px] font-bold text-rose-700">Dinonaktifkan</span>
                        @endif
                    </div>
                    <span class="text-[11.5px] text-slate-500 mt-1 block">
                        Dikelola oleh Administrator
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Perpanjangan & Rekening Pembayaran --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Informasi Rekening & Nominal Transfer --}}
        <div class="space-y-6">
            {{-- Card Rekening Tujuan --}}
            <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-brand-900 text-white rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold tracking-wider uppercase text-blue-200">Rekening Resmi IndoTech</span>
                        <span class="px-2 py-0.5 rounded-md bg-white/10 text-[10px] font-bold text-blue-100 border border-white/15">Verifikasi Manual</span>
                    </div>

                    <div>
                        <div class="text-[13px] text-blue-200 font-medium">Bank Tujuan</div>
                        <div class="text-[18px] font-black text-white tracking-wide mt-0.5">{{ $bankSettings['bank_name'] }}</div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/10 border border-white/15">
                        <div class="text-[11.5px] text-blue-200 font-medium">Nomor Rekening</div>
                        <div class="flex items-center justify-between mt-1">
                            <span id="accNumber" class="text-[18px] font-mono font-black text-white tracking-wider">{{ $bankSettings['account_number'] }}</span>
                            <button type="button" id="btnCopyAcc" onclick="copyAccountNumber('{{ $bankSettings['account_number'] }}')" class="px-2.5 py-1 rounded-md bg-white/20 text-white text-[11px] font-bold hover:bg-white/30 transition cursor-pointer flex items-center gap-1">
                                <svg id="copyIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span id="copyText">Salin</span>
                            </button>
                        </div>
                        <div class="text-[12px] text-blue-100 font-medium mt-1">a.n. {{ $bankSettings['account_name'] }}</div>
                    </div>

                    {{-- Pilihan Nominal Paket --}}
                    <div class="space-y-2 pt-2 border-t border-white/10">
                        <div class="text-[12px] font-bold text-blue-200 uppercase tracking-wider">Pilihan Biaya Membership:</div>
                        <div class="flex items-center justify-between text-[13px] py-1 border-b border-white/10">
                            <span class="text-blue-100">1 Bulan</span>
                            <span class="font-extrabold text-white">Rp {{ number_format($bankSettings['price_monthly'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[13px] py-1">
                            <span class="text-blue-100">1 Tahun (Hemat)</span>
                            <span class="font-extrabold text-amber-300">Rp {{ number_format($bankSettings['price_yearly'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if(!empty($bankSettings['instructions']))
                        <div class="pt-2 text-[12px] text-blue-200/90 leading-relaxed italic border-t border-white/10">
                            "{{ $bankSettings['instructions'] }}"
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bantuan WhatsApp --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.199.662.59 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.13.332.202.043.073.043.419-.101.824z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-[14px] text-slate-900">Butuh Bantuan Transfer?</h4>
                        <p class="text-[12.5px] text-slate-500">Hubungi langsung tim support kami untuk panduan transaksi.</p>
                    </div>
                </div>
                @php
                    $waMsg = rawurlencode("Halo Admin IndoTech, saya dari {$user->name} ingin bertanya perihal pembayaran perpanjangan membership.");
                @endphp
                <a href="https://wa.me/6281234567890?text={{ $waMsg }}" target="_blank" rel="noopener" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-[12.5px] border border-emerald-200 transition">
                    Chat WhatsApp Support
                </a>
            </div>
        </div>

        {{-- Kolom Kanan: Form Konfirmasi Pembayaran --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-7">
            <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-[17px] font-black text-slate-900 tracking-tight">Formulir Konfirmasi Pembayaran Membership</h3>
                    <p class="text-[12.5px] text-slate-500">Setelah melakukan transfer, silakan lengkapi data dan unggah bukti transfer di bawah ini.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.membership.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf

                {{-- Pilihan Paket --}}
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Paket Membership <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer has-checked:border-brand-600 has-checked:bg-blue-50/50 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[14px] text-slate-900">Paket 1 Bulan</span>
                                <input type="radio" name="plan" value="1_month" checked class="text-brand-600 focus:ring-brand-500">
                            </div>
                            <span class="text-[16px] font-black text-brand-700 mt-2">Rp {{ number_format($bankSettings['price_monthly'], 0, ',', '.') }}</span>
                            <span class="text-[11.5px] text-slate-500 mt-0.5">Perpanjangan masa aktif 30 hari</span>
                        </label>

                        <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer has-checked:border-brand-600 has-checked:bg-blue-50/50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[14px] text-slate-900">Paket 1 Tahun</span>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Hemat</span>
                                </div>
                                <input type="radio" name="plan" value="1_year" class="text-brand-600 focus:ring-brand-500">
                            </div>
                            <span class="text-[16px] font-black text-brand-700 mt-2">Rp {{ number_format($bankSettings['price_yearly'], 0, ',', '.') }}</span>
                            <span class="text-[11.5px] text-slate-500 mt-0.5">Perpanjangan masa aktif 365 hari penuh</span>
                        </label>
                    </div>
                </div>

                {{-- Bank Pengirim & Nama Pengirim --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sender_bank" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Bank / Metode Pengirim <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_bank" id="sender_bank" value="{{ old('sender_bank') }}" required placeholder="Contoh: BCA, Mandiri, BRI, QRIS" class="w-full h-11 px-3.5 text-[13px] rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label for="sender_name" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Pemilik Rekening Pengirim <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_name" id="sender_name" value="{{ old('sender_name') }}" required placeholder="Nama sesuai bukti transfer" class="w-full h-11 px-3.5 text-[13px] rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>
                </div>

                {{-- Nomor Rekening Pengirim --}}
                <div>
                    <label for="sender_account_number" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Rekening Pengirim <span class="text-slate-400 font-normal">(Opsional jika via e-wallet/QRIS)</span>
                    </label>
                    <input type="text" name="sender_account_number" id="sender_account_number" value="{{ old('sender_account_number') }}" placeholder="Contoh: 8830192817" class="w-full h-11 px-3.5 text-[13px] rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                {{-- Upload Bukti Pembayaran --}}
                <div>
                    <label for="payment_proof" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Unggah Bukti Transfer / Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl hover:border-brand-500 transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-[13px] text-slate-600 justify-center">
                                <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-bold text-brand-600 hover:text-brand-500 focus-within:outline-none">
                                    <span>Pilih file bukti transfer</span>
                                    <input id="payment_proof" name="payment_proof" type="file" required accept="image/*,.pdf" class="sr-only">
                                </label>
                            </div>
                            <p class="text-[11.5px] text-slate-400">Format JPG, PNG, WEBP, atau PDF (Maksimal 5MB)</p>
                            <p id="filePreviewName" class="text-[12px] font-bold text-brand-700 mt-2 hidden"></p>
                        </div>
                    </div>
                </div>

                {{-- Catatan Tambahan --}}
                <div>
                    <label for="notes" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Tambahan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Tuliskan keterangan bila ada (misal: transfer dari rekening bendahara yayasan)" class="w-full p-3 text-[13px] rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-blue-700 text-white font-bold text-[13.5px] shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Riwayat Pengajuan Pembayaran --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-[16px] font-black text-slate-900">Riwayat Pengajuan Pembayaran</h3>
                <p class="text-[12.5px] text-slate-500">Daftar permohonan konfirmasi perpanjangan membership yang pernah Anda ajukan.</p>
            </div>
            <span class="text-[12px] font-semibold text-slate-400">{{ $payments->count() }} Pengajuan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-bold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4">Tanggal Pengajuan</th>
                        <th class="py-3 px-3">Paket</th>
                        <th class="py-3 px-3">Nominal Transfer</th>
                        <th class="py-3 px-3">Rekening Pengirim</th>
                        <th class="py-3 px-3 text-center">Bukti Transfer</th>
                        <th class="py-3 px-4 text-right">Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $p->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[11.5px] text-slate-400">{{ $p->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-slate-800">{{ $p->plan_label }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-extrabold text-slate-900">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-slate-800">{{ $p->sender_bank }} — {{ $p->sender_name }}</div>
                                @if($p->sender_account_number)
                                    <div class="text-[11.5px] text-slate-400 font-mono">Rek: {{ $p->sender_account_number }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ $p->proof_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-brand-600 hover:bg-blue-100 text-[11.5px] font-bold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Bukti
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11.5px] font-semibold {{ $p->status_badge_class }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ $p->status_label }}
                                </span>
                                @if($p->status === 'rejected' && $p->admin_notes)
                                    <div class="text-[11px] text-rose-600 mt-1 max-w-xs ml-auto">
                                        Alasan: {{ $p->admin_notes }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-[13px]">
                                Belum ada riwayat pengajuan pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function copyAccountNumber(text) {
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('btnCopyAcc');
            const txt = document.getElementById('copyText');
            if (btn && txt) {
                const originalBg = btn.className;
                btn.className = 'px-2.5 py-1 rounded-md bg-emerald-500 text-white text-[11px] font-extrabold shadow-sm transition flex items-center gap-1';
                txt.textContent = '✓ Tersalin!';
                setTimeout(() => {
                    btn.className = originalBg;
                    txt.textContent = 'Salin';
                }, 2200);
            }
        });
    }

    document.getElementById('payment_proof')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const previewEl = document.getElementById('filePreviewName');
        if (fileName && previewEl) {
            previewEl.textContent = 'File dipilih: ' + fileName;
            previewEl.classList.remove('hidden');
        }
    });
</script>
@endsection
