<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Berhasil — IndoTech</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            max-width: 520px;
            width: 100%;
            padding: 2rem;
            text-align: center;
        }
        .success-icon {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .success-icon svg {
            width: 36px;
            height: 36px;
            color: #16a34a;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-secondary:hover {
            background-color: #e2e8f0;
        }
        .badge {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        .badge-institution {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-individual {
            background-color: #dcfce7;
            color: #166534;
        }
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            margin: 1.25rem 0;
        }
        .info-box p {
            font-size: 0.875rem;
            color: #374151;
            margin: 0.25rem 0;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.25rem 0;
        }
        .warning-box p {
            font-size: 0.875rem;
            color: #92400e;
            margin: 0.25rem 0;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 1.75rem 0;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            flex-direction: column;
            sm-flex-direction: row;
            margin-top: 1.5rem;
        }
        @media (min-width: 640px) {
            .btn-group {
                flex-direction: row;
            }
            .btn-group .btn {
                flex: 1;
            }
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="card">

    <!-- Success Icon -->
    <div class="success-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75L11.25 15l3-3m0 0L15 9.75m-3 3H8.25"/>
        </svg>
    </div>

    <!-- Main Heading -->
    <h1 class="text-2xl font-bold text-slate-800 mb-2">
        Pendaftaran Berhasil
    </h1>

    <p class="text-slate-500 text-sm mb-4">
        Akun Anda telah terdaftar di IndoTech
    </p>

    @if ($user && in_array($user->role, ['school', 'university', 'company']))

        <!-- Institution Badge -->
        <span class="badge badge-institution">
            {{ ucfirst($user->role === 'school' ? 'Sekolah Vokasi' : ($user->role === 'university' ? 'Universitas' : 'Perusahaan')) }}
        </span>

        <!-- Warning Box: Waiting for Admin Verification -->
        <div class="warning-box">
            <p><strong>Menunggu Verifikasi Admin</strong></p>
            <p>Akun Anda belum bisa dipakai. Harap menunggu konfirmasi dari tim admin kami.</p>
        </div>

        <!-- Verification Info -->
        <div class="info-box">
            <p>Proses verifikasi biasanya memakan waktu 1�?"3 hari kerja.</p>
            <p>Anda akan menerima notifikasi email ketika akun telah disetujui.</p>
            <p>Setelah disetujui, kami akan mengirim tautan verifikasi alamat email. Buka tautan itu sebelum login.</p>
        </div>

        <!-- Email -->
        @if ($user->email)
            <div class="info-box">
                <p class="font-medium">Email terdaftar:</p>
                <p class="break-all">{{ $user->email }}</p>
            </div>
        @endif

    @else

        <!-- Individual Badge -->
        <span class="badge badge-individual">
            @if ($user && $user->role === 'user')
                Akun Individu
            @elseif ($user && $user->role === 'school')
                Akun Sekolah
            @elseif ($user && $user->role === 'university')
                Akun Universitas
            @elseif ($user && $user->role === 'company')
                Akun Perusahaan
            @else
                Akun Individu
            @endif
        </span>

        <!-- Success Message -->
        <div class="info-box">
            <p>Akun Anda sudah aktif dan siap dipakai.</p>
            <p>Silakan login untuk mulai menggunakan IndoTech.</p>
        </div>

        <!-- Email -->
        @if ($user?->email)
            <div class="info-box">
                <p class="font-medium">Email terdaftar:</p>
                <p class="break-all">{{ $user->email }}</p>
            </div>
        @endif

    @endif

    <!-- Action Buttons -->
    <div class="btn-group">
        <a href="{{ route('login') }}" class="btn btn-primary">
            Kembali ke Login
        </a>
        <a href="{{ route('welcome') }}" class="btn btn-secondary">
            Kembali ke Beranda
        </a>
    </div>

</div>
</body>
</html>
