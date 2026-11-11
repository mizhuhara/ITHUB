<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email - ITHub</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

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
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg px-10 py-12">
        <div class="text-center">
            <span class="text-2xl font-extrabold tracking-tight text-slate-900 notranslate" translate="no">IT<span class="text-blue-600">Hub</span></span>
        </div>

        <div class="mt-8 w-14 h-14 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="5" width="18" height="14" rx="2"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/>
            </svg>
        </div>

        <h1 class="mt-5 text-center text-[24px] font-bold text-gray-900">Verifikasi Email</h1>
        <p class="mt-2 text-center text-[14px] text-gray-500">
            Kami sudah mengirim tautan verifikasi ke <span class="font-semibold text-gray-700">{{ auth()->user()->email }}</span>.
            Buka tautan itu untuk mengaktifkan akses akun.
        </p>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @error('email')
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $message }}
            </div>
        @enderror

        <form data-async data-async-follow action="{{ route('verification.send') }}" method="POST" class="mt-7">
            @csrf
            <button type="submit"
                    class="w-full h-[46px] rounded-lg bg-blue-700 text-white text-[14.5px] font-semibold transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <p class="mt-4 text-center text-[12.5px] text-gray-500">
            Tautan berlaku 60 menit. Cek folder spam jika email belum terlihat.
        </p>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <form data-async data-async-follow action="{{ route('logout') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-[13px] font-medium text-gray-500 hover:text-gray-700">
                    Keluar dari akun
                </button>
            </form>
        </div>
    </div>

    @include('partials.async-form')
</body>
</html>
