@extends('layouts.app')

@section('title', $event['title'] . ' — IndoTech Events')

@section('content')
<div class="bg-slate-50 min-h-screen py-8" x-data="{ registered: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Back Navigation --}}
        <div>
            <a href="{{ route('event.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All Events
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Hero Banner Image --}}
        <div class="w-full h-64 sm:h-80 md:h-96 rounded-2xl overflow-hidden shadow-sm bg-slate-200 relative">
            <img src="{{ $event['image'] }}" 
                 alt="{{ $event['title'] }}" 
                 class="w-full h-full object-cover">
        </div>

        {{-- Main 2-Column Content Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-2 items-start">
            
            {{-- Left Main Column --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Event Header (Tags, Title, Host) --}}
                <div class="space-y-4">
                    {{-- Badges --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $event['category'] }}
                        </span>
                        @if(!empty($event['is_verified']))
                            <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full inline-flex items-center gap-1.5 border border-emerald-200/50">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        {{ $event['title'] }}
                    </h1>

                    {{-- Host Info --}}
                    @php
                        $hostLogo = $event->organizer_logo ?? ($event['organizer_logo'] ?? null) ?? $event->user?->avatar_url;
                    @endphp
                    <div class="flex items-center gap-2.5 pt-1">
                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs overflow-hidden shrink-0">
                            @if(!empty($hostLogo))
                                <img src="{{ $hostLogo }}" alt="{{ $event['organizer'] }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($event['organizer'] ?? 'EV', 0, 2)) }}
                            @endif
                        </div>
                        <span class="text-xs sm:text-sm font-medium text-slate-600">
                            Hosted by <strong class="text-slate-900 font-semibold">{{ $event['organizer'] }}</strong>
                        </span>
                    </div>
                </div>

                <hr class="border-slate-200">

                {{-- About This Event --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-slate-900">About This Workshop</h2>
                    
                    <div class="text-sm text-slate-600 leading-relaxed space-y-3">
                        {!! nl2br(e($event['description'])) !!}
                    </div>

                    {{-- What You'll Learn --}}
                    @if(!empty($event['what_you_will_learn']))
                        <div class="pt-4 space-y-3">
                            <h3 class="text-sm font-bold text-slate-900">What You'll Learn:</h3>
                            <ul class="space-y-2 text-sm text-slate-600">
                                @foreach($event['what_you_will_learn'] as $item)
                                    <li class="flex items-start gap-2.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mt-2 shrink-0"></span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Speakers Section --}}
                @if(!empty($event['speakers']))
                    <div class="space-y-4 pt-2">
                        <h2 class="text-lg font-bold text-slate-900">Speakers</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($event['speakers'] as $speaker)
                                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3.5 shadow-sm hover:border-slate-300 transition">
                                    <img src="{{ $speaker['avatar'] }}" 
                                         alt="{{ $speaker['name'] }}" 
                                         class="w-13 h-13 rounded-full object-cover shrink-0">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">{{ $speaker['name'] }}</h4>
                                        <p class="text-xs text-slate-500 font-medium leading-normal mt-0.5">{{ $speaker['role'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Certificate of Completion Box --}}
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900">Certificate of Completion</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            All participants who complete the workshop and final project will receive an industry-recognized certificate from TechLink.id.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Right Sticky Sidebar (Enrollment Card) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900">Enrollment Open</h3>
                        <span class="bg-emerald-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-md">
                            Active
                        </span>
                    </div>

                    {{-- Event Meta Items --}}
                    <div class="space-y-4 text-xs text-slate-600 border-t border-b border-slate-100 py-4">
                        {{-- Date --}}
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <div>
                                <span class="font-bold text-slate-900 block">Date</span>
                                <span>{{ $event['full_date'] ?? $event['date'] }}</span>
                            </div>
                        </div>

                        {{-- Time --}}
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <span class="font-bold text-slate-900 block">Time</span>
                                <span>{{ $event['time'] ?? '09:00 - 16:00 (WIB)' }}</span>
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <span class="font-bold text-slate-900 block">Location</span>
                                <span>{{ $event['location'] }}</span>
                                <a href="https://maps.google.com/?q={{ urlencode($event['location']) }}" target="_blank" class="block text-blue-600 hover:underline font-semibold mt-0.5">
                                    View on Map
                                </a>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 {{ $event->isPaid() ? 'text-emerald-600' : 'text-slate-400' }} mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10m-7 5h7"/>
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                            </svg>
                            <div>
                                <span class="font-bold text-slate-900 block text-xs">Biaya Pendaftaran</span>
                                <span class="font-extrabold {{ $event->isPaid() ? 'text-emerald-600 text-base' : 'text-slate-900' }}">
                                    {{ $event->formatted_price }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Rekening Transfer untuk Event Berbayar --}}
                    @if ($event->isPaid())
                        @if ($bankAccount)
                            <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($bankAccount->bank_name, 0, 3)) }}
                                        </span>
                                        <span class="font-bold text-xs text-blue-900">Rekening Pembayaran</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                        {{ $bankAccount->bank_name }}
                                    </span>
                                </div>

                                <div class="bg-white rounded-xl p-3 border border-blue-100 flex items-center justify-between shadow-2xs">
                                    <div>
                                        <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Rekening</span>
                                        <span class="text-sm font-black text-slate-900 font-mono tracking-wider">{{ $bankAccount->account_number }}</span>
                                    </div>
                                    <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $bankAccount->account_number }}'); this.innerText='Tersalin!'; setTimeout(() => this.innerText='Salin', 2000)"
                                            class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg border border-blue-200 transition">
                                        Salin
                                    </button>
                                </div>

                                <div class="text-[11px] text-slate-600 space-y-1">
                                    <div>Atas Nama: <strong class="text-slate-900">{{ $bankAccount->account_holder }}</strong></div>
                                    @if($bankAccount->instructions)
                                        <div class="text-[10.5px] text-slate-500 bg-white/70 p-2.5 rounded-xl border border-blue-100 mt-1">
                                            {{ $bankAccount->instructions }}
                                        </div>
                                    @endif
                                </div>

                                @if($bankAccount->qr_code_image)
                                    <div class="pt-2 border-t border-blue-100 text-center">
                                        <p class="text-[10.5px] font-bold text-slate-600 mb-1.5">Scan QRIS Penyelenggara:</p>
                                        <img src="{{ $bankAccount->qr_code_image }}" alt="QRIS" class="w-32 h-32 mx-auto rounded-xl object-contain bg-white p-1 border border-slate-200 shadow-xs">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3.5 text-xs text-amber-800">
                                <strong class="font-bold">Info Pembayaran:</strong> Hubungi penyelenggara melalui <strong class="text-amber-900">{{ $event->contact_email }}</strong> untuk rincian transfer tiket sebesar <strong class="text-amber-900">{{ $event->formatted_price }}</strong>.
                            </div>
                        @endif
                    @endif

                    {{-- Registration Quota Progress Bar --}}
                    @php
                        $total = (int) ($event['total_quota'] ?? 0);
                        $percent = $total > 0 ? min(100, max(0, round(($registeredCount / $total) * 100))) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-900">Registration Quota</span>
                            <span class="text-slate-700">
                                {{ $registeredCount }} / {{ $total > 0 ? $total : 'Unlimited' }} Spots
                            </span>
                        </div>

                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    {{-- Registration Action --}}
                    <div>
                        @if ($isRegistered)
                            @if ($registration && $registration->payment_status === 'pending')
                                {{-- Status: Menunggu Konfirmasi Penyelenggara --}}
                                <div class="rounded-2xl border border-amber-300 bg-amber-50/80 p-4 space-y-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </span>
                                        <div>
                                            <h4 class="font-extrabold text-amber-900 text-xs">Menunggu Konfirmasi Pembayaran</h4>
                                            <p class="text-[11px] text-amber-700">Bukti pembayaran telah terkirim.</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed bg-white/70 p-3 rounded-xl border border-amber-200">
                                        Tiket resmi dan kode QR Anda akan aktif otomatis setelah pembayaran Anda diverifikasi oleh panitia (<strong>{{ $event->organizer }}</strong>).
                                    </p>
                                    @if($registration->payment_proof)
                                        <div class="flex items-center justify-between text-xs pt-1">
                                            <span class="text-slate-500 text-[11px]">Bukti Transfer:</span>
                                            <a href="{{ $registration->payment_proof }}" target="_blank" class="font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                                Lihat Bukti &rarr;
                                            </a>
                                        </div>
                                    @endif
                                    <form action="{{ route('event.unregister', $event['id']) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full mt-2 py-2 px-3 rounded-xl border border-red-200 bg-white hover:bg-red-50 text-red-600 text-xs font-bold transition">
                                            Batalkan Pendaftaran
                                        </button>
                                    </form>
                                </div>
                            @elseif ($registration && $registration->payment_status === 'rejected')
                                {{-- Status: Ditolak oleh Penyelenggara --}}
                                <div class="rounded-2xl border border-red-300 bg-red-50 p-4 space-y-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0 font-bold text-sm">
                                            !
                                        </span>
                                        <div>
                                            <h4 class="font-extrabold text-red-900 text-xs">Pembayaran Ditolak</h4>
                                            <p class="text-[11px] text-red-700">Bukti transfer belum valid.</p>
                                        </div>
                                    </div>
                                    @if($registration->rejection_reason)
                                        <div class="p-3 rounded-xl bg-white border border-red-200 text-xs text-slate-700">
                                            <span class="font-bold text-red-600 block text-[10.5px] uppercase">Alasan dari Penyelenggara:</span>
                                            <p class="mt-0.5">{{ $registration->rejection_reason }}</p>
                                        </div>
                                    @endif
                                    <p class="text-xs text-slate-600">
                                        Unggah bukti transfer yang valid untuk mengajukan verifikasi kembali:
                                    </p>
                                    <form action="{{ route('event.reupload-proof', $event['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-2.5">
                                        @csrf
                                        <input type="file" name="payment_proof" required accept="image/jpeg,image/png,image/webp,application/pdf"
                                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                        <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                                            Kirim Ulang Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Status: Dikonfirmasi / Tiket Aktif --}}
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 text-center">
                                    {{ $event->isPaid() ? 'Pembayaran Anda telah dikonfirmasi!' : 'Anda sudah terdaftar di event ini.' }}
                                </div>
                                @if ($registration)
                                    <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kode Tiket Resmi</p>
                                        <p class="text-lg font-extrabold tracking-widest text-slate-900 mt-1">{{ $registration->ticket_code }}</p>
                                        <p class="text-xs mt-1 {{ $registration->checked_in_at ? 'text-emerald-600 font-semibold' : 'text-slate-500' }}">
                                            {{ $registration->checked_in_at
                                                ? 'Sudah check-in '.$registration->checked_in_at->format('d M Y, H:i')
                                                : 'Belum check-in' }}
                                        </p>
                                        @if (!empty($ticketQr))
                                            <img src="{{ $ticketQr }}" alt="QR Tiket {{ $registration->ticket_code }}"
                                                 class="mx-auto mt-3 h-36 w-36">
                                            <a href="{{ route('ticket.show', $registration->ticket_code) }}"
                                               class="mt-2 inline-block text-xs font-bold text-blue-600 hover:underline">
                                                Buka Halaman Tiket
                                            </a>
                                        @endif
                                        @if (!empty($certificateAvailable))
                                            <a href="{{ route('ticket.certificate', $registration->ticket_code) }}"
                                               class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700">
                                                Unduh Sertifikat
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <form action="{{ route('event.unregister', $event['id']) }}" method="POST" class="mt-3"
                                      onsubmit="return confirm('Batalkan pendaftaran event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full border border-red-200 bg-white text-red-600 hover:bg-red-50 font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                                        Batalkan Pendaftaran
                                    </button>
                                </form>
                            @endif
                        @elseif ($isFull)
                            <button type="button" disabled
                                    class="w-full bg-slate-300 text-slate-600 font-bold py-3 px-4 rounded-xl text-sm cursor-not-allowed">
                                Event Full
                            </button>
                        @elseif (auth()->check())
                            <form action="{{ route('event.register', $event['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                    <input name="name" type="text" required value="{{ old('name', auth()->user()->name) }}"
                                           placeholder="Nama lengkap peserta"
                                           class="w-full h-11 px-4 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                                    <input name="email" type="email" required value="{{ old('email', auth()->user()->email) }}"
                                           placeholder="Alamat email aktif"
                                           class="w-full h-11 px-4 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Nomor WhatsApp / Telepon {{ $event->isPaid() ? '*' : '(opsional)' }}
                                    </label>
                                    <input name="phone" type="text" {{ $event->isPaid() ? 'required' : '' }} value="{{ old('phone', auth()->user()->org_phone) }}"
                                           placeholder="Nomor telepon aktif"
                                           class="w-full h-11 px-4 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Field Tambahan untuk Event Berbayar --}}
                                @if ($event->isPaid())
                                    <div class="p-3.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 space-y-3">
                                        <div class="text-xs font-extrabold text-slate-800 flex items-center justify-between">
                                            <span>Rincian Transfer Pembayaran</span>
                                            <span class="text-emerald-600">{{ $event->formatted_price }}</span>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Bank Pengirim (Opsional)</label>
                                            <input name="sender_bank" type="text" value="{{ old('sender_bank') }}"
                                                   placeholder="Contoh: BCA / Mandiri / BRI / DANA"
                                                   class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs bg-white outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Pemilik Rekening Pengirim (Opsional)</label>
                                            <input name="sender_name" type="text" value="{{ old('sender_name') }}"
                                                   placeholder="Nama di rekening Anda"
                                                   class="w-full h-9 px-3 rounded-lg border border-slate-300 text-xs bg-white outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                                Unggah Bukti Transfer <span class="text-red-500">*</span>
                                            </label>
                                            <input name="payment_proof" type="file" required accept="image/jpeg,image/png,image/webp,application/pdf"
                                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                            <p class="text-[10.5px] text-slate-500 mt-1">Format: JPG, PNG, WEBP, atau PDF (Maks. 2 MB)</p>
                                            @error('payment_proof') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @endif

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-3 px-4 rounded-xl text-sm transition shadow-sm">
                                    {{ $event->isPaid() ? 'Daftar & Kirim Bukti Pembayaran' : 'Register for Event' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition shadow-sm">
                                Login to Register
                            </a>
                        @endif
                    </div>

                    {{-- Footnote --}}
                    @guest
                        <p class="text-[11px] text-slate-400 text-center">
                            Requires an IndoTech account to register.
                        </p>
                    @endguest

                </div>
            </div>

        </div>

    </div>
</div>
@endsection
