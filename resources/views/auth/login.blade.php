<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — ITHub</title>
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

        {{-- Logo --}}
        <div class="text-center">
            <a href="/" class="inline-flex flex-col items-center leading-none no-underline notranslate" translate="no">
                <span class="text-3xl font-black tracking-tight font-sans">
                    <span class="text-black">IT</span><span class="text-blue-600">HUB</span>
                </span>
                <div class="w-full self-stretch flex justify-between text-[11px] font-medium text-slate-900 mt-1 select-none" aria-hidden="true">
                    <span>i</span><span>n</span><span>d</span><span>o</span><span>n</span><span>e</span><span>s</span><span>i</span><span>a</span>
                </div>
                <span class="sr-only">ITHUB indonesia</span>
            </a>
        </div>

        {{-- Heading --}}
        <h1 class="mt-8 text-center text-[28px] font-bold text-gray-900">Welcome Back</h1>
        <p class="mt-2 text-center text-[15px] text-gray-500">Please enter your details to sign in.</p>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form data-async data-async-follow action="{{ url('/login') }}" method="POST" class="mt-9 space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-[13.5px] font-semibold text-gray-900">Email</label>
                <div class="relative mt-2">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/>
                    </svg>
                    <input id="email" name="email" type="email" required
                           placeholder="you@example.com"
                           class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-[13.5px] font-semibold text-gray-900">Password</label>
                    <a href="{{ route('password.request') }}" class="text-[13px] font-medium text-blue-600 hover:text-blue-700">Forgot Password?</a>
                </div>
                <div class="relative mt-2">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="4" y="11" width="16" height="9" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                    <input id="password" name="password" type="password" required
                           placeholder="••••••••"
                           class="w-full h-[46px] pl-11 pr-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 placeholder-gray-400 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            {{-- Sign In button --}}
            <button type="submit"
                    class="w-full h-[46px] rounded-lg bg-blue-700 text-white text-[14.5px] font-semibold transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                Sign In
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[13px] text-gray-400">or continue with</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        {{-- Google button --}}
        <a href="#" class="flex items-center justify-center gap-3 w-full h-[46px] rounded-lg border border-gray-300 bg-white text-[14px] font-semibold text-gray-800 transition hover:bg-gray-50">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.5 12.27c0-.85-.08-1.66-.22-2.45H12v4.64h6.45a5.52 5.52 0 0 1-2.39 3.62v3h3.87c2.26-2.09 3.57-5.16 3.57-8.81z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.93-2.91l-3.87-3c-1.07.72-2.45 1.15-4.06 1.15-3.12 0-5.77-2.11-6.71-4.95H1.29v3.1A12 12 0 0 0 12 24z"/>
                <path fill="#FBBC05" d="M5.29 14.29a7.21 7.21 0 0 1 0-4.58v-3.1H1.29a12 12 0 0 0 0 10.78l4-3.1z"/>
                <path fill="#EA4335" d="M12 4.77c1.76 0 3.34.6 4.58 1.79l3.44-3.44A11.98 11.98 0 0 0 12 0 12 12 0 0 0 1.29 6.6l4 3.1C6.23 6.88 8.88 4.77 12 4.77z"/>
            </svg>
            Sign in with Google
        </a>

        {{-- Footer --}}
        <p class="mt-7 text-center text-[14px] text-gray-500">
            Don't have an account?
            <a href="{{ url('/register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign Up</a>
        </p>
    </div>

    @include('partials.async-form')
</body>
</html>
