@extends('admin.layouts.app')

@section('title', 'Kelola Keanggotaan & Fitur Premium — IndoTech')
@section('header_title', 'Kelola Keanggotaan Institusi & Fitur Premium')
@section('header_subtitle', 'Atur status membership, verifikasi permohonan pembayaran, dan konfigurasikan rekening bank & nominal.')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Flash Message --}}
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
            <div class="font-bold mb-1">Periksa kembali input data:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Global Master Switch Banner --}}
    <div class="rounded-2xl border p-5 sm:p-6 shadow-xs {{ $isGlobalPremiumEnabled ? 'bg-gradient-to-r from-blue-900 to-indigo-900 text-white border-blue-800' : 'bg-gradient-to-r from-amber-900 to-orange-950 text-white border-amber-800' }}">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold tracking-wider uppercase {{ $isGlobalPremiumEnabled ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-400/20 text-amber-300 border border-amber-400/30' }}">
                        <span class="w-2 h-2 rounded-full {{ $isGlobalPremiumEnabled ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }} mr-1.5"></span>
                        {{ $isGlobalPremiumEnabled ? 'Sistem Premium Aktif' : 'Fitur Premium Dimatikan Sementara' }}
                    </span>
                    <span class="text-xs text-slate-300">Master Switch Global</span>
                </div>
                <h3 class="text-[19px] sm:text-[21px] font-black tracking-tight leading-snug">
                    {{ $isGlobalPremiumEnabled ? 'Aturan Keanggotaan & Batas 1 Bulan Berjalan Normal' : 'Mode Akses Bebas Sementara Sedang Aktif' }}
                </h3>
                <p class="text-[13px] {{ $isGlobalPremiumEnabled ? 'text-blue-100' : 'text-amber-100' }} max-w-2xl leading-relaxed">
                    {{ $isGlobalPremiumEnabled
                        ? 'Hanya institusi dengan masa trial 1 bulan aktif atau member yang profilnya ditampilkan di website publik. Institusi yang masa berlakunya habis akan otomatis disembunyikan.'
                        : 'Pembatasan membership dinonaktifkan sementara. Seluruh institusi (termasuk yang masa trialnya habis) saat ini tetap ditampilkan di direktori publik IndoTech.' }}
                </p>
            </div>

            <div class="shrink-0">
                <button type="button" onclick="openToggleGlobalModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13.5px] font-bold shadow-md transition cursor-pointer {{ $isGlobalPremiumEnabled ? 'bg-amber-500 hover:bg-amber-600 text-slate-950' : 'bg-emerald-500 hover:bg-emerald-600 text-slate-950' }}">
                    @if ($isGlobalPremiumEnabled)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Matikan Sementara Fitur Premium</span>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Aktifkan Kembali Fitur Premium</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 sm:gap-4">
        <a href="{{ route('admin.memberships.index') }}" class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-blue-400 transition block">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Institusi</span>
            <div class="mt-1 text-[22px] font-extrabold text-slate-900 leading-tight">{{ $totalCount }}</div>
            <span class="text-[11.5px] text-slate-500 mt-0.5 block">Sekolah, Kampus, Mitra</span>
        </a>

        <a href="{{ route('admin.memberships.index', ['status' => 'trial']) }}" class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-blue-400 transition block">
            <span class="text-[11px] font-bold text-blue-500 uppercase tracking-wider block">Trial 1 Bulan</span>
            <div class="mt-1 text-[22px] font-extrabold text-blue-600 leading-tight">{{ $trialCount }}</div>
            <span class="text-[11.5px] text-slate-500 mt-0.5 block">Masa gratis berjalan</span>
        </a>

        <a href="{{ route('admin.memberships.index', ['status' => 'active']) }}" class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-emerald-400 transition block">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Member Aktif</span>
            <div class="mt-1 text-[22px] font-extrabold text-emerald-600 leading-tight">{{ $activeMemberCount }}</div>
            <span class="text-[11.5px] text-slate-500 mt-0.5 block">Berlangganan resmi</span>
        </a>

        <a href="{{ route('admin.memberships.index', ['status' => 'expired']) }}" class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-amber-400 transition block">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Kadaluarsa</span>
            <div class="mt-1 text-[22px] font-extrabold text-amber-600 leading-tight">{{ $expiredCount }}</div>
            <span class="text-[11.5px] text-slate-500 mt-0.5 block">Perlu perpanjangan</span>
        </a>

        <a href="{{ route('admin.memberships.index', ['status' => 'disabled']) }}" class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-rose-400 transition block">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Dinonaktifkan</span>
            <div class="mt-1 text-[22px] font-extrabold text-rose-600 leading-tight">{{ $disabledCount }}</div>
            <span class="text-[11.5px] text-slate-500 mt-0.5 block">Dimatikan admin</span>
        </a>

        <a href="#paymentsSection" class="p-4 rounded-xl bg-blue-50 border border-blue-200 shadow-xs hover:border-blue-400 transition block">
            <span class="text-[11px] font-bold text-brand-700 uppercase tracking-wider block">Verifikasi Pembayaran</span>
            <div class="mt-1 text-[22px] font-black text-brand-700 leading-tight">{{ $pendingPaymentsCount }}</div>
            <span class="text-[11.5px] text-brand-600 mt-0.5 block">Menunggu verifikasi</span>
        </a>
    </div>

    {{-- Pengaturan Rekening Bank & Nominal Biaya --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-[16px] font-black text-slate-900">Pengaturan Rekening Bank & Nominal Biaya Membership</h3>
                <p class="text-[12.5px] text-slate-500">Ubah rekening tujuan transfer resmi dan harga paket yang ditampilkan ke institusi pemohon.</p>
            </div>
            <button type="button" onclick="toggleSettingsForm()" id="btnToggleSettings" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12.5px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer shrink-0">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span id="textToggleSettings">Buka Pengaturan</span>
            </button>
        </div>

        <div id="settingsContainer" class="p-6 border-t border-slate-100 bg-slate-50/50 hidden">
            <form method="POST" action="{{ route('admin.memberships.settings') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Bank Tujuan</label>
                        <input type="text" name="membership_bank_name" value="{{ old('membership_bank_name', $bankSettings['bank_name']) }}" required class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening</label>
                        <input type="text" name="membership_bank_account_number" value="{{ old('membership_bank_account_number', $bankSettings['account_number']) }}" required class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Atas Nama Pemilik Rekening</label>
                        <input type="text" name="membership_bank_account_name" value="{{ old('membership_bank_account_name', $bankSettings['account_name']) }}" required class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Biaya Membership Bulanan (Rp)</label>
                        <input type="number" name="membership_price_monthly" value="{{ old('membership_price_monthly', $bankSettings['price_monthly']) }}" required min="0" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Biaya Membership Tahunan (Rp)</label>
                        <input type="number" name="membership_price_yearly" value="{{ old('membership_price_yearly', $bankSettings['price_yearly']) }}" required min="0" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Instruksi Pembayaran Tambahan</label>
                    <textarea name="membership_payment_instructions" rows="2" class="w-full p-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 bg-white">{{ old('membership_payment_instructions', $bankSettings['instructions']) }}</textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white font-bold text-[13px] shadow-sm transition cursor-pointer">
                        Simpan Pengaturan Rekening & Harga
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Pengajuan Perpanjangan Pembayaran --}}
    <div id="paymentsSection" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-[16px] font-black text-slate-900">Permohonan Pembayaran & Bukti Transfer</h3>
                    @if($pendingPaymentsCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800">
                            {{ $pendingPaymentsCount }} Menunggu
                        </span>
                    @endif
                </div>
                <p class="text-[12.5px] text-slate-500">Verifikasi bukti transfer dari institusi untuk perpanjangan masa keanggotaan.</p>
            </div>

            {{-- Filter Status Pembayaran --}}
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-[12px]">
                <a href="{{ route('admin.memberships.index', array_merge(request()->except('payment_status'), ['payment_status' => ''])) }}#paymentsSection" class="px-3 py-1 rounded-lg font-bold transition {{ !request('payment_status') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Semua</a>
                <a href="{{ route('admin.memberships.index', array_merge(request()->except('payment_status'), ['payment_status' => 'pending'])) }}#paymentsSection" class="px-3 py-1 rounded-lg font-bold transition {{ request('payment_status') === 'pending' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Pending</a>
                <a href="{{ route('admin.memberships.index', array_merge(request()->except('payment_status'), ['payment_status' => 'approved'])) }}#paymentsSection" class="px-3 py-1 rounded-lg font-bold transition {{ request('payment_status') === 'approved' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Disetujui</a>
                <a href="{{ route('admin.memberships.index', array_merge(request()->except('payment_status'), ['payment_status' => 'rejected'])) }}#paymentsSection" class="px-3 py-1 rounded-lg font-bold transition {{ request('payment_status') === 'rejected' ? 'bg-white text-rose-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Ditolak</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-bold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4">Institusi Pemohon</th>
                        <th class="py-3 px-3">Paket & Nominal</th>
                        <th class="py-3 px-3">Pengirim</th>
                        <th class="py-3 px-3 text-center">Bukti Transfer</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $pay)
                        @php
                            $userInst = $pay->user->school ?? $pay->user->university ?? $pay->user->company ?? null;
                            $instName = $userInst->name ?? $pay->user->name ?? 'Institusi';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-[13.5px]">{{ $instName }}</div>
                                <div class="text-[12px] text-slate-500 mt-0.5">{{ $pay->user->email ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $pay->created_at->format('d/m/Y H:i') }} WIB</div>
                            </td>

                            <td class="py-3.5 px-3">
                                <div class="font-bold text-slate-900">{{ $pay->plan_label }}</div>
                                <div class="text-[13px] font-extrabold text-brand-700 mt-0.5">Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                            </td>

                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-slate-800">{{ $pay->sender_bank }}</div>
                                <div class="text-[12px] text-slate-600 font-medium">a.n. {{ $pay->sender_name }}</div>
                                @if($pay->sender_account_number)
                                    <div class="text-[11.5px] text-slate-400 font-mono">No: {{ $pay->sender_account_number }}</div>
                                @endif
                                @if($pay->notes)
                                    <div class="text-[11.5px] text-slate-500 italic mt-1 max-w-xs">"{{ $pay->notes }}"</div>
                                @endif
                            </td>

                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ $pay->proof_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-brand-700 hover:bg-blue-100 text-[11.5px] font-bold border border-blue-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Cek Bukti
                                </a>
                            </td>

                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $pay->status_badge_class }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ $pay->status_label }}
                                </span>
                                @if($pay->status === 'rejected' && $pay->admin_notes)
                                    <div class="text-[11px] text-rose-600 mt-1 max-w-xs">
                                        Alasan: {{ $pay->admin_notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($pay->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" 
                                                onclick="openReviewModal(
                                                    {{ $pay->id }},
                                                    '{{ addslashes($instName) }}',
                                                    '{{ addslashes($pay->user->email ?? '') }}',
                                                    '{{ addslashes($pay->plan_label) }}',
                                                    'Rp {{ number_format($pay->amount, 0, ',', '.') }}',
                                                    '{{ addslashes($pay->sender_bank) }}',
                                                    '{{ addslashes($pay->sender_name) }}',
                                                    '{{ addslashes($pay->sender_account_number ?? '-') }}',
                                                    '{{ $pay->created_at->format('d/m/Y H:i') }} WIB',
                                                    '{{ addslashes($pay->notes ?? '') }}',
                                                    '{{ $pay->proof_url }}'
                                                )"
                                                class="px-3.5 py-1.5 rounded-lg text-[12px] font-bold bg-brand-600 text-white hover:bg-blue-700 transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Cek & Validasi
                                        </button>

                                        <button type="button" onclick="openRejectModal({{ $pay->id }}, '{{ addslashes($instName) }}')" class="px-2.5 py-1.5 rounded-lg text-[11.5px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition cursor-pointer">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <div class="text-[12px] text-slate-500 font-medium">
                                        @if($pay->status === 'approved')
                                            <span class="text-emerald-700 font-bold">✓ Divalidasi</span>
                                            @if($pay->approved_at)
                                                <span class="text-slate-400 block text-[11px]">{{ $pay->approved_at->format('d/m/y H:i') }}</span>
                                            @endif
                                        @else
                                            <span class="text-rose-600 font-bold">✕ Ditolak</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-[13px]">
                                Belum ada permohonan pembayaran yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Filter & Search Form Institusi --}}
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.memberships.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama institusi atau email..." class="w-full h-10 pl-9 pr-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="role" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    <option value="">Semua Tipe Institusi</option>
                    <option value="school" {{ request('role') === 'school' ? 'selected' : '' }}>Sekolah (SMK)</option>
                    <option value="university" {{ request('role') === 'university' ? 'selected' : '' }}>Perguruan Tinggi</option>
                    <option value="company" {{ request('role') === 'company' ? 'selected' : '' }}>Perusahaan Mitra</option>
                </select>
            </div>

            <div class="flex gap-2">
                <select name="status" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial Aktif</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Member Aktif</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>

                <button type="submit" class="h-10 px-4 rounded-lg bg-slate-900 text-white text-[13px] font-bold hover:bg-slate-800 transition shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table of All Institutions --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-[16px] font-black text-slate-900">Daftar Seluruh Akun Institusi</h2>
                <p class="text-[12.5px] text-slate-500">Kelola izin tampil di website publik dan durasi keanggotaan institusi.</p>
            </div>
            <span class="text-[12.5px] text-slate-500 font-medium">Menampilkan {{ $users->total() }} akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-bold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4">Institusi & Akun</th>
                        <th class="py-3 px-3">Tipe</th>
                        <th class="py-3 px-3">Status Membership</th>
                        <th class="py-3 px-3">Masa Berlaku</th>
                        <th class="py-3 px-3 text-center">Tampil di Publik?</th>
                        <th class="py-3 px-4 text-right">Aksi & Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        @php
                            $inst = match ($u->role) {
                                'school', 'school_admin' => $u->school,
                                'university', 'univ_rep' => $u->university,
                                'company', 'company_hr' => $u->company,
                                default => null,
                            };
                            $roleLabel = match ($u->role) {
                                'school', 'school_admin' => 'Sekolah',
                                'university', 'univ_rep' => 'Universitas',
                                'company', 'company_hr' => 'Perusahaan',
                                default => $u->role,
                            };
                            $roleBg = match ($u->role) {
                                'school', 'school_admin' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'university', 'univ_rep' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'company', 'company_hr' => 'bg-blue-50 text-blue-700 border-blue-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                            $isActuallyVisible = $u->hasActiveMembership();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-[13.5px]">
                                    {{ $inst->name ?? $u->name }}
                                </div>
                                <div class="text-[12px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $u->email }}</span>
                                    <span>•</span>
                                    <span>Terdaftar {{ $u->created_at->format('d/m/Y') }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11.5px] font-bold border {{ $roleBg }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11.5px] font-semibold {{ $u->membership_badge_class }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ $u->membership_status_label }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3">
                                @if ($u->membership_expires_at)
                                    <div class="font-semibold text-slate-800 text-[12.5px]">
                                        {{ $u->membership_expires_at->format('d M Y') }}
                                    </div>
                                    <div class="text-[11.5px] {{ $u->membership_days_remaining > 0 ? 'text-slate-500' : 'text-rose-600 font-semibold' }}">
                                        @if ($u->membership_days_remaining > 0)
                                            Sisa {{ $u->membership_days_remaining }} hari
                                        @else
                                            Sudah lewat {{ abs((int) now()->diffInDays($u->membership_expires_at, false)) }} hari lalu
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[12px]">Tanpa batas waktu</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-3 text-center">
                                @if ($isActuallyVisible)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                        <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Tayang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">
                                        <svg class="w-3 h-3 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tersembunyi
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick="openToggleUserPremiumModal({{ $u->id }}, '{{ addslashes($inst->name ?? $u->name) }}', {{ $u->is_premium_active ? 'true' : 'false' }})"
                                            title="{{ $u->is_premium_active ? 'Matikan sementara fitur premium akun ini' : 'Aktifkan kembali fitur premium' }}" 
                                            class="px-2.5 py-1.5 rounded-lg text-[11.5px] font-bold border transition cursor-pointer {{ $u->is_premium_active ? 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                        {{ $u->is_premium_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <button type="button" onclick="openExtendModal({{ $u->id }}, '{{ addslashes($inst->name ?? $u->name) }}', '{{ $u->membership_status ?? 'trial' }}', '{{ $u->membership_expires_at ? $u->membership_expires_at->format('Y-m-d') : '' }}')" class="px-3 py-1.5 rounded-lg text-[11.5px] font-bold bg-brand-600 text-white hover:bg-blue-700 transition cursor-pointer">
                                        Perpanjang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-[13px]">
                                Tidak ada data akun institusi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modal Konfirmasi Master Switch Global Premium --}}
<div id="modalToggleGlobal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 space-y-4 text-left">
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-2xl {{ $isGlobalPremiumEnabled ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }} flex items-center justify-center shrink-0 mt-0.5">
                @if ($isGlobalPremiumEnabled)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-[16px] font-black text-slate-900 tracking-tight">
                    {{ $isGlobalPremiumEnabled ? 'Matikan Sementara Fitur Premium?' : 'Aktifkan Kembali Fitur Premium?' }}
                </h3>
                <p class="text-[13px] text-slate-500 leading-relaxed mt-1">
                    {{ $isGlobalPremiumEnabled
                        ? 'Apakah Anda yakin ingin menonaktifkan pembatasan keanggotaan sementara waktu?'
                        : 'Apakah Anda yakin ingin mengaktifkan kembali pembatasan keanggotaan institusi?' }}
                </p>
            </div>
            <button type="button" onclick="closeToggleGlobalModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="text-[12px] {{ $isGlobalPremiumEnabled ? 'text-amber-900 bg-amber-50/80 border-amber-200' : 'text-blue-900 bg-blue-50/80 border-blue-200' }} border px-3.5 py-3 rounded-xl flex items-start gap-2.5">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span class="leading-relaxed">
                {{ $isGlobalPremiumEnabled
                    ? 'Mode Akses Terbuka akan aktif. Seluruh profil institusi di sistem (termasuk yang masa trialnya sudah habis) akan tetap ditampilkan di direktori publik IndoTech.'
                    : 'Aturan trial 1 bulan gratis dan status membership akan ditegakkan kembali. Institusi yang masa aktifnya telah habis otomatis disembunyikan dari direktori publik.' }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.memberships.toggle_global') }}" class="pt-1 flex items-center justify-end gap-2.5">
            @csrf
            <button type="button" onclick="closeToggleGlobalModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-[13px] transition cursor-pointer">
                Batal
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl {{ $isGlobalPremiumEnabled ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }} font-bold text-[13px] shadow-sm transition cursor-pointer">
                {{ $isGlobalPremiumEnabled ? 'Ya, Matikan Sementara' : 'Ya, Aktifkan Kembali' }}
            </button>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Saklar Fitur Premium Per Institusi --}}
<div id="modalToggleUserPremium" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 space-y-4 text-left">
        <div class="flex items-start gap-3.5">
            <div id="toggleUserIconBox" class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 mt-0.5 border">
                <svg id="toggleUserIconDisable" class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <svg id="toggleUserIconEnable" class="w-6 h-6 text-emerald-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="toggleUserModalTitle" class="text-[16px] font-black text-slate-900 tracking-tight">Konfirmasi Fitur Premium</h3>
                <p class="text-[13px] text-slate-500 leading-relaxed mt-1">
                    <span id="toggleUserModalDesc">Ubah status fitur premium untuk</span> <strong id="toggleUserInstName" class="text-slate-800 font-bold block mt-0.5"></strong>
                </p>
            </div>
            <button type="button" onclick="closeToggleUserPremiumModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="toggleUserWarningBox" class="text-[12px] px-3.5 py-3 rounded-xl border flex items-start gap-2.5">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="toggleUserWarningText" class="leading-relaxed"></span>
        </div>

        <form id="formToggleUserPremium" method="POST" action="" class="pt-1 flex items-center justify-end gap-2.5">
            @csrf
            <button type="button" onclick="closeToggleUserPremiumModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-[13px] transition cursor-pointer">
                Batal
            </button>
            <button type="submit" id="toggleUserSubmitBtn" class="px-5 py-2.5 rounded-xl font-bold text-[13px] shadow-sm transition cursor-pointer text-white">
                Konfirmasi
            </button>
        </form>
    </div>
</div>

{{-- Modal Perpanjang Keanggotaan Manual --}}
<div id="extendModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
        <button type="button" onclick="closeExtendModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h3 class="text-[17px] font-black text-slate-900">Perpanjang Keanggotaan Manual</h3>
        <p id="modalInstitutionName" class="text-[13px] text-slate-500 mt-0.5 font-medium"></p>

        <form id="extendForm" method="POST" action="" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Pilihan Durasi Perpanjangan</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="extension_type" value="add_month" checked class="text-brand-600 focus:ring-brand-500" onchange="toggleCustomDate(false)">
                        <span class="text-[13px] font-medium text-slate-800">+1 Bulan (30 Hari)</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="extension_type" value="add_year" class="text-brand-600 focus:ring-brand-500" onchange="toggleCustomDate(false)">
                        <span class="text-[13px] font-medium text-slate-800">+1 Tahun (365 Hari)</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="extension_type" value="custom_date" class="text-brand-600 focus:ring-brand-500" onchange="toggleCustomDate(true)">
                        <span class="text-[13px] font-medium text-slate-800">Tanggal Kustom</span>
                    </label>
                </div>
            </div>

            <div id="customDateContainer" class="hidden">
                <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Berakhir Pada Tanggal</label>
                <input type="date" name="custom_expires_at" id="custom_expires_at" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">Status Keanggotaan</label>
                <select name="membership_status" id="membership_status" class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    <option value="active">Member Aktif (Resmi)</option>
                    <option value="trial">Trial Gratis</option>
                    <option value="expired">Tandai Kadaluarsa</option>
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button type="button" onclick="closeExtendModal()" class="px-4 py-2 rounded-xl text-[13px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-bold bg-brand-600 text-white hover:bg-blue-700 shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Cek Manual & Validasi Pembayaran --}}
<div id="reviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 relative my-8">
        <button type="button" onclick="closeReviewModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-[18px] font-black text-slate-900">Cek Manual & Validasi Pembayaran</h3>
                <p class="text-[12.5px] text-slate-500">Periksa bukti transfer sebelum mengaktifkan perpanjangan premium institusi.</p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
            {{-- Preview Bukti Transfer --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bukti Transfer Institusi</label>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-2 flex flex-col items-center justify-center min-h-[220px] max-h-[340px] overflow-hidden">
                    <img id="reviewProofImg" src="" alt="Bukti Transfer" class="max-h-[300px] w-auto object-contain rounded-lg shadow-xs hidden">
                    <div id="reviewProofPdf" class="text-center p-4 hidden">
                        <svg class="w-12 h-12 text-rose-500 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2v20h10V8l-6-6H7zm8 7V3.5L18.5 9H15z"/></svg>
                        <p class="text-[12.5px] font-bold text-slate-700 mt-2">Dokumen Bukti (PDF)</p>
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <a id="reviewProofLink" href="" target="_blank" rel="noopener" class="text-[12px] font-bold text-brand-600 hover:text-brand-800 underline inline-flex items-center gap-1">
                        Buka Gambar Ukuran Penuh
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>

            {{-- Detail Rincian Pembayaran --}}
            <div class="space-y-3">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rincian Data Transfer</label>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-[12.5px]">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Institusi Pemohon</span>
                        <span id="reviewInstName" class="font-bold text-slate-900 block text-[13.5px]"></span>
                        <span id="reviewInstEmail" class="text-slate-500 text-[11.5px] block"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Paket Dipilih</span>
                            <span id="reviewPlan" class="font-bold text-slate-900"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Nominal Transfer</span>
                            <span id="reviewAmount" class="font-extrabold text-brand-700"></span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60">
                        <span class="text-slate-400 block text-[11px]">Rekening & Pengirim</span>
                        <span id="reviewSenderBank" class="font-bold text-slate-800"></span>
                        <span id="reviewSenderName" class="text-slate-600 block"></span>
                        <span id="reviewSenderAcc" class="text-slate-400 font-mono text-[11px] block"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60">
                        <span class="text-slate-400 block text-[11px]">Waktu Pengajuan</span>
                        <span id="reviewCreatedAt" class="text-slate-700 font-medium"></span>
                    </div>

                    <div id="reviewNotesContainer" class="pt-2 border-t border-slate-200/60 hidden">
                        <span class="text-slate-400 block text-[11px]">Catatan Institusi</span>
                        <span id="reviewNotes" class="text-slate-600 italic block"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Validasi / Persetujuan --}}
        <form id="reviewApproveForm" method="POST" action="" class="mt-5 pt-4 border-t border-slate-100">
            @csrf
            <div>
                <label for="reviewAdminNotes" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Validasi Administrator <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input type="text" name="admin_notes" id="reviewAdminNotes" placeholder="Contoh: Telah dicocokkan dengan mutasi rekening bank, dana sudah masuk." class="w-full h-10 px-3 text-[13px] rounded-lg border border-slate-200 focus:outline-none focus:border-brand-500">
            </div>

            <div class="mt-4 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                <button type="button" id="btnRejectFromReview" class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-[12.5px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                    Tolak Bukti Pembayaran
                </button>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeReviewModal()" class="px-4 py-2.5 rounded-xl text-[12.5px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-[13px] font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Validasi & Lanjutkan Premium
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak Pembayaran --}}
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
        <button type="button" onclick="closeRejectModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h3 class="text-[17px] font-black text-rose-700">Tolak Permohonan Pembayaran</h3>
        <p id="rejectModalInstName" class="text-[13px] text-slate-500 mt-0.5 font-medium"></p>

        <form id="rejectForm" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="admin_notes" class="block text-[12px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="admin_notes" id="admin_notes" required rows="3" placeholder="Contoh: Bukti transfer buram/tidak terbaca, nominal kurang, atau rekening pengirim tidak sesuai." class="w-full p-3 text-[13px] rounded-xl border border-slate-200 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-[13px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-bold bg-rose-600 text-white hover:bg-rose-700 shadow-sm transition">
                    Tolak Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openToggleGlobalModal() {
        document.getElementById('modalToggleGlobal').classList.remove('hidden');
    }

    function closeToggleGlobalModal() {
        document.getElementById('modalToggleGlobal').classList.add('hidden');
    }

    function openToggleUserPremiumModal(userId, instName, isCurrentlyActive) {
        const modal = document.getElementById('modalToggleUserPremium');
        const form = document.getElementById('formToggleUserPremium');
        const nameEl = document.getElementById('toggleUserInstName');
        const titleEl = document.getElementById('toggleUserModalTitle');
        const descEl = document.getElementById('toggleUserModalDesc');
        const warnText = document.getElementById('toggleUserWarningText');
        const warnBox = document.getElementById('toggleUserWarningBox');
        const submitBtn = document.getElementById('toggleUserSubmitBtn');
        const iconBox = document.getElementById('toggleUserIconBox');
        const iconDisable = document.getElementById('toggleUserIconDisable');
        const iconEnable = document.getElementById('toggleUserIconEnable');

        form.action = `/admin/memberships/${userId}/toggle`;
        nameEl.textContent = instName;

        if (isCurrentlyActive) {
            titleEl.textContent = 'Matikan Fitur Premium Institusi?';
            descEl.textContent = 'Nonaktifkan sementara fitur premium untuk';
            warnText.textContent = 'Profil institusi ini akan otomatis disembunyikan dari direktori pencarian publik IndoTech hingga diaktifkan kembali.';
            warnBox.className = 'text-[12px] px-3.5 py-3 rounded-xl border flex items-start gap-2.5 text-rose-900 bg-rose-50/80 border-rose-200';
            submitBtn.textContent = 'Ya, Matikan Premium';
            submitBtn.className = 'px-5 py-2.5 rounded-xl font-bold text-[13px] shadow-sm transition cursor-pointer text-white bg-rose-600 hover:bg-rose-700';
            iconBox.className = 'w-11 h-11 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center shrink-0 mt-0.5';
            iconDisable.classList.remove('hidden');
            iconEnable.classList.add('hidden');
        } else {
            titleEl.textContent = 'Aktifkan Kembali Fitur Premium?';
            descEl.textContent = 'Aktifkan kembali fitur premium untuk';
            warnText.textContent = 'Fitur premium institusi ini akan diaktifkan kembali dan profil institusi akan tayang di direktori publik sesuai masa aktifnya.';
            warnBox.className = 'text-[12px] px-3.5 py-3 rounded-xl border flex items-start gap-2.5 text-emerald-900 bg-emerald-50/80 border-emerald-200';
            submitBtn.textContent = 'Ya, Aktifkan Premium';
            submitBtn.className = 'px-5 py-2.5 rounded-xl font-bold text-[13px] shadow-sm transition cursor-pointer text-white bg-emerald-600 hover:bg-emerald-700';
            iconBox.className = 'w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center shrink-0 mt-0.5';
            iconDisable.classList.add('hidden');
            iconEnable.classList.remove('hidden');
        }

        modal.classList.remove('hidden');
    }

    function closeToggleUserPremiumModal() {
        document.getElementById('modalToggleUserPremium').classList.add('hidden');
    }

    function toggleSettingsForm() {
        const container = document.getElementById('settingsContainer');
        const text = document.getElementById('textToggleSettings');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            text.textContent = 'Tutup Pengaturan';
        } else {
            container.classList.add('hidden');
            text.textContent = 'Buka Pengaturan';
        }
    }

    function openExtendModal(userId, institutionName, currentStatus, currentExpires) {
        const modal = document.getElementById('extendModal');
        const form = document.getElementById('extendForm');
        const nameEl = document.getElementById('modalInstitutionName');
        const statusSelect = document.getElementById('membership_status');
        const dateInput = document.getElementById('custom_expires_at');

        nameEl.textContent = institutionName;
        form.action = `/admin/memberships/${userId}/extend`;
        if (currentStatus === 'trial') {
            statusSelect.value = 'active';
        } else {
            statusSelect.value = currentStatus || 'active';
        }
        if (currentExpires) {
            dateInput.value = currentExpires;
        }

        modal.classList.remove('hidden');
    }

    function closeExtendModal() {
        document.getElementById('extendModal').classList.add('hidden');
    }

    function toggleCustomDate(show) {
        const container = document.getElementById('customDateContainer');
        if (show) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function openRejectModal(paymentId, institutionName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const nameEl = document.getElementById('rejectModalInstName');

        nameEl.textContent = institutionName;
        form.action = `/admin/memberships/payments/${paymentId}/reject`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function openReviewModal(paymentId, instName, instEmail, plan, amount, senderBank, senderName, senderAcc, createdAt, notes, proofUrl) {
        const modal = document.getElementById('reviewModal');
        const form = document.getElementById('reviewApproveForm');
        const img = document.getElementById('reviewProofImg');
        const pdf = document.getElementById('reviewProofPdf');
        const link = document.getElementById('reviewProofLink');

        document.getElementById('reviewInstName').textContent = instName;
        document.getElementById('reviewInstEmail').textContent = instEmail;
        document.getElementById('reviewPlan').textContent = plan;
        document.getElementById('reviewAmount').textContent = amount;
        document.getElementById('reviewSenderBank').textContent = senderBank;
        document.getElementById('reviewSenderName').textContent = 'a.n. ' + senderName;
        document.getElementById('reviewSenderAcc').textContent = senderAcc !== '-' ? 'No. Rek: ' + senderAcc : '';
        document.getElementById('reviewCreatedAt').textContent = createdAt;

        const notesContainer = document.getElementById('reviewNotesContainer');
        if (notes && notes.trim() !== '') {
            document.getElementById('reviewNotes').textContent = `"${notes}"`;
            notesContainer.classList.remove('hidden');
        } else {
            notesContainer.classList.add('hidden');
        }

        form.action = `/admin/memberships/payments/${paymentId}/approve`;

        // Handle proof preview
        link.href = proofUrl;
        const isPdf = proofUrl.toLowerCase().endsWith('.pdf');
        if (isPdf) {
            img.classList.add('hidden');
            pdf.classList.remove('hidden');
        } else {
            pdf.classList.add('hidden');
            img.src = proofUrl;
            img.classList.remove('hidden');
        }

        // Setup reject button from review modal
        document.getElementById('btnRejectFromReview').onclick = function() {
            closeReviewModal();
            openRejectModal(paymentId, instName);
        };

        modal.classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }
</script>
@endsection
