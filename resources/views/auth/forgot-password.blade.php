<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - ITHub</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg px-10 py-12">

        <div class="text-center">
            <span class="text-2xl font-extrabold tracking-tight text-slate-900 notranslate" translate="no">IT<span class="text-blue-600">Hub</span></span>
        </div>

        <h1 class="mt-8 text-center text-[28px] font-bold text-gray-900">Forgot Password</h1>
        <p class="mt-2 text-center text-[15px] text-gray-500">Masukkan email akun Anda. Kami akan mengirim tautan reset password.</p>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form data-async data-async-follow action="{{ route('password.email') }}" method="POST" class="mt-9 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-[13.5px] font-semibold text-gray-900">Email</label>
                <div class="relative mt-2">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/>
                    </svg>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           placeholder="you@example.com"
                           class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <button type="submit"
                    class="w-full h-[46px] rounded-lg bg-blue-700 text-white text-[14.5px] font-semibold transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                Send Reset Link
            </button>
        </form>

        <p class="mt-7 text-center text-[14px] text-gray-500">
            Ingat password Anda?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign In</a>
        </p>
    </div>

    @include('partials.async-form')
</body>
</html>
