<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up — ITHub</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- ============ STEP 1: PICK ROLE ============ --}}
    <div id="step-role" class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-4xl">

            <div class="text-center mb-10">
                <h1 class="text-[32px] font-bold text-slate-900">What role are you joining as?</h1>
                <p class="mt-2 text-[15px] text-slate-600">Choose your primary role to customize your ITHub experience.</p>
            </div>

            <div id="role-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                @php
                    $roles = [
                        ['key' => 'user', 'title' => 'User', 'desc' => 'Individual — belajar, cari kerja, ikut event, jadi mentor.', 'icon' => 'user'],
                        ['key' => 'school', 'title' => 'School', 'desc' => 'Institusi pendidikan — tampilkan program vokasi & koneksi industri.', 'icon' => 'school'],
                        ['key' => 'university', 'title' => 'University', 'desc' => 'Kampus — kelola program & kemitraan industri.', 'icon' => 'landmark'],
                        ['key' => 'company', 'title' => 'Company', 'desc' => 'Perusahaan — rekrut talenta IT & promot brand.', 'icon' => 'office'],
                    ];
                @endphp

                @foreach ($roles as $role)
                    <button type="button" data-role="{{ $role['key'] }}"
                            class="role-card group text-left bg-white border border-slate-200 rounded-xl p-5 transition hover:border-blue-400 hover:shadow-md outline-none focus:ring-2 focus:ring-blue-500/30">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                            @if ($role['icon'] === 'user')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 21c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5"/></svg>
                            @elseif ($role['icon'] === 'book')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25C10.5 5 8.5 4.5 6 4.5H3v13h3c2.5 0 4.5.5 6 1.75m0-13c1.5-1.25 3.5-1.75 6-1.75h3v13h-3c-2.5 0-4.5.5-6 1.75m0-13v13"/></svg>
                            @elseif ($role['icon'] === 'badge')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="9" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12.5 12.5 15 20l-3.5-2-2.5 2 2.5-9.5"/></svg>
                            @elseif ($role['icon'] === 'award')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="9" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.5 13.5 7 21l5-2.5L17 21l-1.5-7.5"/></svg>
                            @elseif ($role['icon'] === 'monitor')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="12" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-5v5"/></svg>
                            @elseif ($role['icon'] === 'school')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6"/></svg>
                            @elseif ($role['icon'] === 'landmark')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 9h16M4 9l8-5 8 5M5 12v6m4.5-6v6m5-6v6M19 12v6M3 21h18"/></svg>
                            @elseif ($role['icon'] === 'office')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 4h2m2 0h2M9 21v-3h6v3"/></svg>
                            @elseif ($role['icon'] === 'id-card')
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.5 16c.6-1.2 1.6-1.8 2.5-1.8s1.9.6 2.5 1.8M14 10h4m-4 3h4"/></svg>
                            @endif
                        </div>
                        <h3 class="text-[15px] font-bold text-slate-900">{{ $role['title'] }}</h3>
                        <p class="mt-1 text-[13px] text-slate-600 leading-relaxed">{{ $role['desc'] }}</p>
                    </button>
                @endforeach
            </div>

            {{-- Continue --}}
            <div class="mt-10 text-center">
                <button id="btn-continue" type="button" disabled
                        class="inline-flex items-center justify-center gap-2 w-80 max-w-full h-12 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[15px] font-semibold transition opacity-50 cursor-not-allowed">
                    Continue
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- ============ STEP 2: ACCOUNT FORM ============ --}}
    <div id="step-form" class="hidden min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg bg-white border border-slate-200 rounded-2xl px-8 py-10">

            {{-- Header --}}
            <div class="text-center">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 notranslate" translate="no">IT<span class="text-blue-600">Hub</span></span>
                <h1 class="mt-3 text-[26px] font-bold text-gray-900">Create Your Account</h1>
                <p class="mt-1.5 text-[14px] text-gray-500">Join us in bridging Indonesian Education and Global IT.</p>
            </div>

            {{-- Institution verification notice --}}
            <div id="inst-notice" class="hidden mt-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-[13px] text-amber-800 flex items-start gap-2.5">
                <svg class="shrink-0 mt-0.5 text-amber-600" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
                <span>Akun institusi butuh verifikasi super admin lalu di-<b>approve</b> sebelum akun bisa login.</span>
            </div>

            <form id="register-form" data-async data-async-follow action="{{ url('/register') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-5">
                @csrf
                <input type="hidden" name="role" id="input-role" value="{{ old('role') }}">
                
                {{-- Error Alert --}}
                <div id="error-alert" class="hidden p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                    <div class="font-semibold mb-2">❌ Pendaftaran gagal!</div>
                    <ul id="error-list" class="space-y-1 list-disc pl-5">
                        @if($errors->any())
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('error-alert').classList.remove('hidden');
                        });
                    </script>
                @endif

                {{-- Role chips --}}
                <div>
                    <label class="block text-[13.5px] font-semibold text-gray-900">I'm registering as:</label>
                    <div id="role-chips" class="mt-2 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($roles as $role)
                            <button type="button" data-rolechip="{{ $role['key'] }}"
                                    class="chip shrink-0 px-4 py-2 rounded-full border text-[13px] font-medium bg-white border-gray-200 text-gray-600 whitespace-nowrap transition hover:border-blue-400">
                                {{ $role['title'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Institution fields (school/university/company only) --}}
                <div id="inst-fields" class="hidden space-y-5">
                    {{-- Logo / Foto Profil Institusi --}}
                    <div>
                        <label id="label-logo" for="logo" class="block text-[13.5px] font-semibold text-gray-900">
                            Foto Profil / Logo Institusi
                            <span class="text-gray-400 font-normal text-[12px]">(Opsional)</span>
                        </label>
                        <div class="mt-2.5 flex items-center gap-4 p-3.5 rounded-xl border border-gray-200 bg-gray-50/60">
                            <div class="relative w-16 h-16 rounded-2xl border-2 border-dashed border-gray-300 bg-white flex items-center justify-center shrink-0 overflow-hidden shadow-xs">
                                <img id="logo-preview-img" src="" alt="Preview Logo" class="hidden w-full h-full object-cover">
                                <div id="logo-placeholder" class="flex flex-col items-center justify-center text-gray-400">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label for="logo" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white border border-gray-300 hover:border-blue-500 hover:bg-blue-50/30 text-[13px] font-semibold text-gray-700 hover:text-blue-600 transition cursor-pointer shadow-xs">
                                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <span id="logo-btn-text">Pilih Foto Profil / Logo</span>
                                        <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="sr-only">
                                    </label>
                                    <button type="button" id="btn-remove-logo" class="hidden px-2 py-1.5 text-[12px] font-medium text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition">
                                        Hapus
                                    </button>
                                </div>
                                <p class="text-[11.5px] text-gray-500 mt-1.5">Format JPG, PNG, WEBP (maks. 5MB). Langsung sinkron ke profil & logo institusi.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="org_contact" class="block text-[13.5px] font-semibold text-gray-900">Contact Person &nbsp;<span class="text-gray-400 font-normal">(nama penanggung jawab)</span></label>
                        <div class="relative mt-2">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 21c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5"/></svg>
                            <input id="org_contact" name="org_contact" type="text" placeholder="Nama contact person" value="{{ old('org_contact') }}"
                                   class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    <div>
                        <label for="org_phone" class="block text-[13.5px] font-semibold text-gray-900">Nomor Telepon / WhatsApp</label>
                        <div class="relative mt-2">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>
                            <input id="org_phone" name="org_phone" type="text" placeholder="+62 ..." value="{{ old('org_phone') }}"
                                   class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    <div>
                        <label for="org_address" class="block text-[13.5px] font-semibold text-gray-900">Alamat Institusi</label>
                        <div class="relative mt-2">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-5.3-7-11a7 7 0 0 1 14 0c0 5.7-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <textarea id="org_address" name="org_address" rows="2" placeholder="Alamat lengkap institusi"
                                      class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('org_address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Full Name --}}
                <div>
                    <label id="label-name" for="name" class="block text-[13.5px] font-semibold text-gray-900">Full Name</label>
                    <div class="relative mt-2">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 21c1.5-3.5 4.5-5 8-5s6.5 1.5 8 5"/></svg>
                        <input id="name" name="name" type="text" required placeholder="Enter your full name" value="{{ old('name') }}"
                               class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[13.5px] font-semibold text-gray-900">Email Address</label>
                    <div class="relative mt-2">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/></svg>
                        <input id="email" name="email" type="email" required placeholder="Enter your email" value="{{ old('email') }}"
                               class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-[13.5px] font-semibold text-gray-900">Password</label>
                    <div class="relative mt-2">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        <input id="password" name="password" type="password" required placeholder="Create a password"
                               class="w-full h-[46px] pl-11 pr-11 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <button type="button" data-toggle-pw aria-label="Show password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                            <svg data-icon-eye class="hidden" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            <svg data-icon-eye-off width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.5 5.2A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6A16.6 16.6 0 0 0 2 12s3.5 7 10 7a9.8 9.8 0 0 0 4.6-1.1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-[13.5px] font-semibold text-gray-900">Confirm Password</label>
                    <div class="relative mt-2">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Confirm your password"
                               class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                {{-- Terms --}}
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input id="agree" type="checkbox" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500/30">
                    <span class="text-[13px] text-gray-600">I agree to the <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Terms and Conditions</a> and <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Privacy Policy</a>.</span>
                </label>

                {{-- Sign Up --}}
                <button id="btn-signup" type="submit" disabled
                        class="flex items-center justify-center gap-2 w-full h-[46px] rounded-lg bg-blue-700 text-white text-[14.5px] font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed hover:enabled:bg-blue-800">
                    Sign Up
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </form>

            {{-- Footer --}}
            <p class="mt-6 text-center text-[14px] text-gray-500">
                Already have an account?
                <a href="{{ url('/login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign in here</a>
            </p>
        </div>
    </div>

    <script>
        (function () {
            var selected = null;

            var INST = ['school', 'university', 'company'];
            function applyRole(role) {
                var isInst = INST.indexOf(role) !== -1;
                document.getElementById('inst-fields').classList.toggle('hidden', !isInst);
                document.getElementById('inst-notice').classList.toggle('hidden', !isInst);
                var nameEl = document.getElementById('name');
                document.getElementById('label-name').textContent = isInst ? 'Nama Institusi / Perusahaan' : 'Full Name';
                nameEl.placeholder = isInst ? 'Nama institusi / perusahaan' : 'Enter your full name';
                ['org_contact', 'org_phone', 'org_address'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.required = isInst;
                    }
                });

                var logoLabelEl = document.getElementById('label-logo');
                var logoBtnTextEl = document.getElementById('logo-btn-text');
                if (logoLabelEl && logoBtnTextEl) {
                    var roleLogoTitle = 'Foto Profil / Logo Institusi';
                    if (role === 'company') roleLogoTitle = 'Logo Perusahaan';
                    else if (role === 'university') roleLogoTitle = 'Logo Perguruan Tinggi / Kampus';
                    else if (role === 'school') roleLogoTitle = 'Logo Sekolah / SMK';
                    logoLabelEl.innerHTML = roleLogoTitle + ' <span class="text-gray-400 font-normal text-[12px]">(Opsional)</span>';
                    logoBtnTextEl.textContent = 'Pilih ' + roleLogoTitle;
                }
            }

            // Check if we should show step-form (on page load after failed submission)
            window.addEventListener('DOMContentLoaded', function() {
                var inputRole = document.getElementById('input-role');
                var nameInput = document.getElementById('name');
                var emailInput = document.getElementById('email');
                var errorAlert = document.getElementById('error-alert');
                var hasErrors = !errorAlert.classList.contains('hidden');
                
                // Show step-form if:
                // 1. Form has data filled in (failed validation), OR
                // 2. Error alert is visible (validation errors from backend)
                var shouldShowForm = (nameInput.value || emailInput.value || hasErrors) && inputRole.value;
                
                if (shouldShowForm) {
                    selected = inputRole.value;
                    applyRole(selected);
                    
                    // Update chips to reflect selected role
                    document.querySelectorAll('.chip').forEach(function (c) {
                        var on = c.dataset.rolechip === selected;
                        c.classList.toggle('bg-blue-50', on);
                        c.classList.toggle('border-blue-500', on);
                        c.classList.toggle('text-blue-600', on);
                        c.classList.toggle('bg-white', !on);
                        c.classList.toggle('border-gray-200', !on);
                        c.classList.toggle('text-gray-600', !on);
                    });
                    
                    // Show step-form, hide step-role
                    document.getElementById('step-role').classList.add('hidden');
                    document.getElementById('step-form').classList.remove('hidden');
                    document.body.classList.remove('bg-slate-50');
                    document.body.classList.add('bg-white');
                    
                    // Scroll to error or form
                    setTimeout(function() {
                        if (hasErrors) {
                            window.scrollTo(0, errorAlert.offsetTop - 100);
                        }
                    }, 100);
                }
            });

            // Step 1: select role card
            document.querySelectorAll('.role-card').forEach(function (card) {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.role-card').forEach(function (c) {
                        c.classList.remove('border-blue-500', 'bg-blue-50/40', 'ring-2', 'ring-blue-500/20');
                        c.classList.add('border-slate-200');
                    });
                    card.classList.remove('border-slate-200');
                    card.classList.add('border-blue-500', 'bg-blue-50/40', 'ring-2', 'ring-blue-500/20');
                    selected = card.dataset.role;
                    document.getElementById('btn-continue').disabled = false;
                    document.getElementById('btn-continue').classList.remove('opacity-50', 'cursor-not-allowed');
                });
            });

            // Continue -> step 2
            document.getElementById('btn-continue').addEventListener('click', function () {
                var role = selected;
                applyRole(role);
                document.getElementById('input-role').value = role;
                document.querySelectorAll('.chip').forEach(function (chip) {
                    chip.classList.toggle('bg-blue-50', chip.dataset.rolechip === role);
                    chip.classList.toggle('border-blue-500', chip.dataset.rolechip === role);
                    chip.classList.toggle('text-blue-600', chip.dataset.rolechip === role);
                    chip.classList.toggle('bg-white', chip.dataset.rolechip !== role);
                    chip.classList.toggle('border-gray-200', chip.dataset.rolechip !== role);
                    chip.classList.toggle('text-gray-600', chip.dataset.rolechip !== role);
                });
                document.getElementById('step-role').classList.add('hidden');
                document.getElementById('step-form').classList.remove('hidden');
                document.body.classList.remove('bg-slate-50');
                document.body.classList.add('bg-white');
                window.scrollTo(0, 0);
            });

            // Step 2: chips switch role
            document.querySelectorAll('.chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    selected = chip.dataset.rolechip;
                    applyRole(selected);
                    document.getElementById('input-role').value = selected;
                    document.querySelectorAll('.chip').forEach(function (c) {
                        var on = c.dataset.rolechip === selected;
                        c.classList.toggle('bg-blue-50', on);
                        c.classList.toggle('border-blue-500', on);
                        c.classList.toggle('text-blue-600', on);
                        c.classList.toggle('bg-white', !on);
                        c.classList.toggle('border-gray-200', !on);
                        c.classList.toggle('text-gray-600', !on);
                    });
                });
            });

            // Show/hide password
            document.querySelector('[data-toggle-pw]').addEventListener('click', function () {
                var input = document.getElementById('password');
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                this.querySelector('[data-icon-eye]').classList.toggle('hidden', !show);
                this.querySelector('[data-icon-eye-off]').classList.toggle('hidden', show);
            });

            // Enable Sign Up only when agreed + form valid
            var agree = document.getElementById('agree');
            var btn = document.getElementById('btn-signup');
            var form = btn.closest('form');
            function updateBtn() {
                var enabled = agree.checked && form.checkValidity();
                btn.disabled = !enabled;
                btn.classList.toggle('disabled:opacity-50', !enabled);
            }
            agree.addEventListener('change', updateBtn);
            ['name', 'email', 'password', 'password_confirmation', 'org_contact', 'org_phone', 'org_address'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateBtn);
                }
            });

            // Logo preview and remove handler
            var logoInput = document.getElementById('logo');
            var logoPreviewImg = document.getElementById('logo-preview-img');
            var logoPlaceholder = document.getElementById('logo-placeholder');
            var btnRemoveLogo = document.getElementById('btn-remove-logo');

            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    var file = e.target.files && e.target.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(evt) {
                            logoPreviewImg.src = evt.target.result;
                            logoPreviewImg.classList.remove('hidden');
                            logoPlaceholder.classList.add('hidden');
                            btnRemoveLogo.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                if (btnRemoveLogo) {
                    btnRemoveLogo.addEventListener('click', function() {
                        logoInput.value = '';
                        logoPreviewImg.src = '';
                        logoPreviewImg.classList.add('hidden');
                        logoPlaceholder.classList.remove('hidden');
                        btnRemoveLogo.classList.add('hidden');
                    });
                }
            }

            // Handle form submission with error handling
            document.getElementById('register-form').addEventListener('submit', function(e) {
                var errorAlert = document.getElementById('error-alert');
                var errorList = document.getElementById('error-list');
                
                // Clear previous errors
                errorList.innerHTML = '';
                errorAlert.classList.add('hidden');
                
                // Check role is selected
                if (!document.getElementById('input-role').value) {
                    e.preventDefault();
                    errorList.innerHTML = '<li>Pilih role terlebih dahulu</li>';
                    errorAlert.classList.remove('hidden');
                    return false;
                }
                
                // Check institution fields if needed
                var role = document.getElementById('input-role').value;
                if (['school', 'university', 'company'].indexOf(role) !== -1) {
                    var instFields = ['org_contact', 'org_phone', 'org_address'];
                    var hasError = false;
                    
                    instFields.forEach(function(id) {
                        var el = document.getElementById(id);
                        if (!el || !el.value) {
                            hasError = true;
                            var label = el && el.previousElementSibling ? el.previousElementSibling.textContent.trim() : id;
                            errorList.innerHTML += '<li>' + label + ' wajib diisi</li>';
                        }
                    });
                    
                    if (hasError) {
                        e.preventDefault();
                        errorAlert.classList.remove('hidden');
                        window.scrollTo(0, document.getElementById('error-alert').offsetTop - 100);
                        return false;
                    }
                }
            });
        })();
    </script>

    @include('partials.async-form')
</body>
</html>
