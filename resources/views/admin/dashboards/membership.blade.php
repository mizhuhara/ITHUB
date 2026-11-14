@extends('admin.layouts.app')

@section('title', 'Status Membership & Perpanjangan — ITHub')
@section('header_title', 'Status Keanggotaan & Perpanjangan Membership')
@section('header_subtitle', 'Pantau masa aktif keanggotaan institusi, informasi rekening pembayaran, dan ajukan perpanjangan keanggotaan.')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13.5px] font-medium">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13.5px] font-medium">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13px]">
            <div class="font-bold mb-1.5 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                Periksa kembali input formulir Anda:
            </div>
            <ul class="list-disc pl-5 space-y-0.5 text-rose-700">
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
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-slate-800 text-[13px] flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-[13.5px] text-blue-900">Bukti Pembayaran Sedang Diverifikasi</p>
                <p class="mt-0.5 text-slate-600 leading-relaxed">
                    Konfirmasi pembayaran <span class="font-semibold text-slate-900">{{ $hasPendingPayment->plan_label }} (Rp {{ number_format($hasPendingPayment->amount, 0, ',', '.') }})</span> yang dikirimkan pada {{ $hasPendingPayment->created_at->translatedFormat('d M Y H:i') }} WIB sedang dalam antrean verifikasi tim admin. Masa aktif keanggotaan akan diperbarui setelah pembayaran disetujui.
                </p>
            </div>
        </div>
    @endif

    @if ($isGlobalDisabled)
        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-[13px] flex items-start gap-3">
            <svg class="w-5 h-5 text-slate-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <div>
                <p class="font-bold text-[13.5px] text-slate-900">Akses Terbuka Aktif</p>
                <p class="mt-0.5 text-slate-600 leading-relaxed">Pembatasan masa keanggotaan sedang dinonaktifkan sementara. Profil institusi Anda tetap tampil di direktori publik.</p>
            </div>
        </div>
    @elseif (!$isPubliclyVisible)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[13px] flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <p class="font-bold text-[13.5px] text-amber-950">Masa Keanggotaan Telah Berakhir</p>
                <p class="mt-0.5 text-amber-800 leading-relaxed">Masa aktif keanggotaan institusi Anda telah berakhir. Saat ini profil institusi Anda disembunyikan dari direktori publik sampai perpanjangan keanggotaan diverifikasi.</p>
            </div>
        </div>
    @elseif ($user->membership_days_remaining <= 5)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[13px] flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold text-[13.5px] text-amber-950">Masa Aktif Segera Berakhir</p>
                <p class="mt-0.5 text-amber-800 leading-relaxed">Sisa masa aktif tersisa <strong class="font-bold text-amber-950">{{ $user->membership_days_remaining }} hari lagi</strong>. Lakukan perpanjangan agar profil institusi tetap dapat diakses publik tanpa jeda.</p>
            </div>
        </div>
    @endif

    {{-- Main Status Overview Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Header Identitas Institusi --}}
        <div class="px-6 py-5 sm:px-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    @php
                        $role = $user->role;
                        $isSchool = in_array($role, ['school', 'school_admin']);
                        $isUniv   = in_array($role, ['university', 'univ_rep']);
                        $isComp   = in_array($role, ['company', 'company_hr']);
                    @endphp
                    @if ($isSchool)
                        {{-- Academic / School Icon --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557q0-.001 0 0z" />
                        </svg>
                    @elseif ($isUniv)
                        {{-- University / Campus Building Icon --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                    @else
                        {{-- Company / Business Building Icon --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5V21" />
                        </svg>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        {{ match($user->role) {
                            'school', 'school_admin'  => 'Institusi Sekolah / SMK',
                            'university', 'univ_rep'  => 'Perguruan Tinggi / Kampus',
                            'company', 'company_hr'   => 'Perusahaan Mitra',
                            default                   => 'Institusi',
                        } }}
                    </div>
                    <h2 class="text-[18px] font-bold text-slate-900 tracking-tight mt-0.5 truncate">
                        {{ $institution->name ?? $user->name }}
                    </h2>
                    <p class="text-[12px] text-slate-500 truncate">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold {{ $user->membership_badge_class }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                    {{ $user->membership_status_label }}
                </span>
                @if ($isPubliclyVisible)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Tayang di Publik
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        Profil Tersembunyi
                    </span>
                @endif
            </div>
        </div>

        {{-- Stat Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            {{-- Sisa Masa Aktif --}}
            <div class="p-6">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sisa Masa Aktif</span>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-[28px] font-bold tracking-tight leading-none
                        {{ $user->membership_days_remaining > 5 ? 'text-slate-900' : ($user->membership_days_remaining > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ $user->membership_days_remaining }}
                    </span>
                    <span class="text-[13px] font-medium text-slate-500">Hari</span>
                </div>
                <span class="text-[12px] text-slate-500 mt-2 block">
                    @if ($user->membership_expires_at)
                        Berakhir {{ $user->membership_expires_at->translatedFormat('d F Y') }}
                    @else
                        Masa aktif tidak terbatas
                    @endif
                </span>
            </div>

            {{-- Mulai Berlangganan --}}
            <div class="p-6">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Mulai Terdaftar</span>
                <div class="mt-2">
                    <span class="text-[17px] font-bold text-slate-900 leading-snug">
                        {{ $user->membership_started_at
                            ? $user->membership_started_at->translatedFormat('d M Y')
                            : $user->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
                <span class="text-[12px] text-slate-500 mt-2 block">
                    Paket: {{ $user->membership_plan === 'trial_1_month'
                        ? 'Trial 1 Bulan Gratis'
                        : ($user->membership_plan === '1_year' ? 'Paket 1 Tahun' : 'Paket 1 Bulan') }}
                </span>
            </div>

            {{-- Status Fitur Premium --}}
            <div class="p-6">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status Akses</span>
                <div class="mt-2 flex items-center gap-2">
                    @if ($user->is_premium_active)
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-[16px] font-bold text-slate-900">Aktif</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                        <span class="text-[16px] font-bold text-slate-900">Nonaktif</span>
                    @endif
                </div>
                <span class="text-[12px] text-slate-500 mt-2 block">Dikelola oleh Administrator</span>
            </div>
        </div>
    </div>

    {{-- Form Perpanjangan & Rekening Pembayaran --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Kolom Kiri: Informasi Rekening --}}
        <div class="space-y-4">
            {{-- Card Rekening Resmi ITHub --}}
            <div class="bg-slate-900 text-white rounded-2xl p-6 border border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <span class="text-[11px] font-bold tracking-wider uppercase text-slate-400">Rekening Resmi</span>
                    <span class="text-[11px] font-semibold text-blue-400">ITHub</span>
                </div>

                <div>
                    <div class="text-[11.5px] text-slate-400">Bank Tujuan</div>
                    <div class="text-[16px] font-bold text-white tracking-wide mt-0.5">{{ $bankSettings['bank_name'] }}</div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                    <div class="text-[11px] text-slate-400 font-medium">Nomor Rekening</div>
                    <div class="flex items-center justify-between mt-1 gap-2">
                        <span id="accNumber" class="text-[16px] font-mono font-bold text-white tracking-wider truncate">{{ $bankSettings['account_number'] }}</span>
                        <button type="button" id="btnCopyAcc" onclick="copyAccountNumber('{{ $bankSettings['account_number'] }}')" class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-semibold transition cursor-pointer flex items-center gap-1 shrink-0">
                            <svg id="copyIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                            </svg>
                            <span id="copyText">Salin</span>
                        </button>
                    </div>
                    <div class="text-[11.5px] text-slate-300 mt-1">a.n. {{ $bankSettings['account_name'] }}</div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-800 text-[12.5px]">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tarif Membership</div>
                    <div class="flex items-center justify-between text-slate-300 py-1 border-b border-slate-800/60">
                        <span>1 Bulan</span>
                        <span class="font-bold text-white">Rp {{ number_format($bankSettings['price_monthly'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-300 py-1">
                        <div class="flex items-center gap-1.5">
                            <span>1 Tahun</span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300">Hemat</span>
                        </div>
                        <span class="font-bold text-white">Rp {{ number_format($bankSettings['price_yearly'], 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(!empty($bankSettings['instructions']))
                    <div class="pt-2 text-[11.5px] text-slate-400 leading-relaxed border-t border-slate-800">
                        {{ $bankSettings['instructions'] }}
                    </div>
                @endif
            </div>

            {{-- Bantuan WhatsApp --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.199.662.59 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.13.332.202.043.073.043.419-.101.824z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-[13px] text-slate-900">Bantuan Transfer</h4>
                        <p class="text-[12px] text-slate-500">Hubungi tim kami jika mengalami kendala transaksi.</p>
                    </div>
                </div>
                @php
                    $waMsg = rawurlencode("Halo Admin ITHub, saya dari {$user->name} ingin bertanya perihal pembayaran perpanjangan membership.");
                @endphp
                <a href="https://wa.me/6281234567890?text={{ $waMsg }}" target="_blank" rel="noopener" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-[12.5px] border border-slate-200 transition">
                    Chat WhatsApp Support
                </a>
            </div>
        </div>

        {{-- Kolom Kanan: Form Konfirmasi Pembayaran --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7">
            <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-slate-900 tracking-tight">Formulir Konfirmasi Pembayaran</h3>
                    <p class="text-[12px] text-slate-500">Unggah bukti transfer untuk perpanjangan masa aktif keanggotaan institusi.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.membership.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                {{-- Pilihan Paket --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-2">Pilih Paket Membership <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer has-checked:border-blue-600 has-checked:bg-blue-50/30 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[13.5px] text-slate-900">Paket 1 Bulan</span>
                                <input type="radio" name="plan" value="1_month" checked class="text-blue-600 focus:ring-blue-500">
                            </div>
                            <span class="text-[17px] font-bold text-slate-900 mt-2">Rp {{ number_format($bankSettings['price_monthly'], 0, ',', '.') }}</span>
                            <span class="text-[11.5px] text-slate-500 mt-0.5">Perpanjangan masa aktif 30 hari</span>
                        </label>

                        <label class="relative flex flex-col p-4 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer has-checked:border-blue-600 has-checked:bg-blue-50/30 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-[13.5px] text-slate-900">Paket 1 Tahun</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-700">Hemat</span>
                                </div>
                                <input type="radio" name="plan" value="1_year" class="text-blue-600 focus:ring-blue-500">
                            </div>
                            <span class="text-[17px] font-bold text-slate-900 mt-2">Rp {{ number_format($bankSettings['price_yearly'], 0, ',', '.') }}</span>
                            <span class="text-[11.5px] text-slate-500 mt-0.5">Perpanjangan masa aktif 365 hari penuh</span>
                        </label>
                    </div>
                </div>

                {{-- Bank Pengirim & Nama Pengirim --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="sender_bank" class="block text-[12px] font-semibold text-slate-700 mb-1">
                            Bank / Metode Pengirim <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_bank" id="sender_bank" value="{{ old('sender_bank') }}" required placeholder="Misal: BCA, Mandiri, BRI, QRIS" class="w-full h-10 px-3 text-[13px] rounded-xl border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10 transition">
                    </div>

                    <div>
                        <label for="sender_name" class="block text-[12px] font-semibold text-slate-700 mb-1">
                            Nama Pemilik Rekening <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_name" id="sender_name" value="{{ old('sender_name') }}" required placeholder="Nama pengirim di bukti transfer" class="w-full h-10 px-3 text-[13px] rounded-xl border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10 transition">
                    </div>
                </div>

                {{-- Nomor Rekening Pengirim --}}
                <div>
                    <label for="sender_account_number" class="block text-[12px] font-semibold text-slate-700 mb-1">
                        Nomor Rekening Pengirim <span class="text-slate-400 font-normal">(Opsional untuk e-wallet / QRIS)</span>
                    </label>
                    <input type="text" name="sender_account_number" id="sender_account_number" value="{{ old('sender_account_number') }}" placeholder="Contoh: 8830192817" class="w-full h-10 px-3 text-[13px] rounded-xl border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10 transition">
                </div>

                {{-- Upload Bukti Pembayaran --}}
                <div>
                    <label for="payment_proof" class="block text-[12px] font-semibold text-slate-700 mb-1">
                        Unggah Bukti Transfer <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-5 py-4 border border-dashed border-slate-300 hover:border-blue-500 rounded-xl bg-slate-50/50 hover:bg-blue-50/20 transition cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-7 w-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <div class="mt-2 text-[12.5px] text-slate-600">
                                <label for="payment_proof" class="cursor-pointer font-semibold text-blue-600 hover:text-blue-700 focus-within:outline-none">
                                    <span>Pilih file dokumen</span>
                                    <input id="payment_proof" name="payment_proof" type="file" required accept="image/*,.pdf" class="sr-only">
                                </label>
                                <span class="text-slate-400"> atau seret ke sini</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, WEBP, atau PDF (Maksimal 5MB)</p>
                            <p id="filePreviewName" class="text-[12px] font-semibold text-blue-700 mt-2 hidden"></p>
                        </div>
                    </div>
                </div>

                {{-- Catatan Tambahan --}}
                <div>
                    <label for="notes" class="block text-[12px] font-semibold text-slate-700 mb-1">
                        Catatan Tambahan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Catatan tambahan bila ada (misal: transfer rekening yayasan)" class="w-full p-3 text-[13px] rounded-xl border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10 transition">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Kirim Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Riwayat Pengajuan Pembayaran --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-[15px] font-bold text-slate-900">Riwayat Pengajuan Pembayaran</h3>
                <p class="text-[12px] text-slate-500">Daftar konfirmasi pembayaran membership institusi.</p>
            </div>
            <span class="text-[12px] font-semibold text-slate-400">{{ $payments->count() }} Pengajuan</span>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-[13px] border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-3">Paket</th>
                        <th class="py-3 px-3">Nominal</th>
                        <th class="py-3 px-3">Pengirim</th>
                        <th class="py-3 px-3 text-center">Bukti</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 block">{{ $p->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[11.5px] text-slate-400">{{ $p->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-medium text-slate-800">{{ $p->plan_label }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-slate-900">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-medium text-slate-800">{{ $p->sender_bank }} — {{ $p->sender_name }}</div>
                                @if($p->sender_account_number)
                                    <div class="text-[11.5px] text-slate-400 font-mono">Rek: {{ $p->sender_account_number }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ $p->proof_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 text-[12px] font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    Lihat File
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11.5px] font-semibold {{ $p->status_badge_class }}">
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

        {{-- Mobile Cards --}}
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse ($payments as $p)
                <div class="px-4 py-4 space-y-2">
                    {{-- Top: tanggal + status --}}
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-semibold text-slate-900 text-[13px]">{{ $p->created_at->translatedFormat('d M Y') }}</div>
                            <div class="text-[11.5px] text-slate-400">{{ $p->created_at->format('H:i') }} WIB</div>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $p->status_badge_class }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                            {{ $p->status_label }}
                        </span>
                    </div>

                    {{-- Paket + Nominal --}}
                    <div class="flex items-center justify-between gap-2 text-[12.5px]">
                        <span class="text-slate-600">{{ $p->plan_label }}</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                    </div>

                    {{-- Pengirim --}}
                    <div class="text-[12px] text-slate-700">
                        <span class="font-medium">{{ $p->sender_bank }} — {{ $p->sender_name }}</span>
                        @if($p->sender_account_number)
                            <div class="text-[11px] text-slate-400 font-mono">Rek: {{ $p->sender_account_number }}</div>
                        @endif
                    </div>

                    {{-- Alasan tolak --}}
                    @if($p->status === 'rejected' && $p->admin_notes)
                        <div class="text-[11px] text-rose-600">Alasan: {{ $p->admin_notes }}</div>
                    @endif

                    {{-- Bukti --}}
                    <div class="pt-1">
                        <a href="{{ $p->proof_url }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 text-[12px] font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Lihat Bukti Transfer
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-[13px]">
                    Belum ada riwayat pengajuan pembayaran.
                </div>
            @endforelse
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
                btn.className = 'px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-semibold transition flex items-center gap-1 shrink-0';
                txt.textContent = 'Tersalin!';
                setTimeout(() => {
                    btn.className = originalBg;
                    txt.textContent = 'Salin';
                }, 2000);
            }
        });
    }

    document.getElementById('payment_proof')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const previewEl = document.getElementById('filePreviewName');
        if (fileName && previewEl) {
            previewEl.textContent = 'File: ' + fileName;
            previewEl.classList.remove('hidden');
        }
    });
</script>
@endsection
