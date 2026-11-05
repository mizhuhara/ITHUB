<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - IndoTech</title>
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg px-10 py-12">

        <div class="text-center">
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">IndoTech</span>
        </div>

        <h1 class="mt-8 text-center text-[28px] font-bold text-gray-900">Reset Password</h1>
        <p class="mt-2 text-center text-[15px] text-gray-500">Buat password baru untuk akun Anda.</p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form data-async data-async-follow action="{{ route('password.store') }}" method="POST" class="mt-9 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-[13.5px] font-semibold text-gray-900">Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email', $email) }}"
                       class="mt-2 w-full h-[46px] px-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="password" class="block text-[13.5px] font-semibold text-gray-900">New Password</label>
                <input id="password" name="password" type="password" required minlength="8"
                       class="mt-2 w-full h-[46px] px-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="password_confirmation" class="block text-[13.5px] font-semibold text-gray-900">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                       class="mt-2 w-full h-[46px] px-4 rounded-lg border border-gray-300 text-[14px] text-gray-900 bg-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <button type="submit"
                    class="w-full h-[46px] rounded-lg bg-blue-700 text-white text-[14.5px] font-semibold transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                Reset Password
            </button>
        </form>

        <p class="mt-7 text-center text-[14px] text-gray-500">
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Back to Sign In</a>
        </p>
    </div>

    @include('partials.async-form')
</body>
</html>
