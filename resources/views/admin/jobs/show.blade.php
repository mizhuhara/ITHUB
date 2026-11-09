@extends('admin.layouts.app')

@section('title', $job['title'] . ' — Detail Lowongan Admin')

@section('content')
<div class="space-y-6">
    {{-- Alerts --}}
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-emerald-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-2">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600 shrink-0"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    {{-- Back to Jobs Link --}}
    <div>
        <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center gap-2 text-[13px] font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5m7 7-7-7 7-7"/>
            </svg>
            <span>Kembali ke Daftar Lowongan</span>
        </a>
    </div>

    {{-- Title Header & Actions --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-[30px] font-extrabold text-slate-900 tracking-tight leading-tight">
                {{ $job['title'] }}
            </h1>
            {{-- Category / Location / Type Badges --}}
            <div class="flex flex-wrap items-center gap-2.5 mt-3">
                <span class="inline-block bg-[#d9e6ff] text-brand-700 text-[12px] font-bold px-3.5 py-1 rounded-full">
                    {{ $job['category'] ?? 'Engineering' }}
                </span>
                <span class="inline-block bg-slate-200/80 text-slate-700 text-[12px] font-bold px-3.5 py-1 rounded-full">
                    {{ str_contains(strtolower($job['location']), 'remote') ? 'Remote' : 'Hybrid / Remote' }}
                </span>
                <span class="inline-block bg-slate-200/80 text-slate-700 text-[12px] font-bold px-3.5 py-1 rounded-full">
                    {{ $job['type'] }}
                </span>
            </div>
        </div>

        {{-- Top Right Buttons --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.jobs.edit', $job['id']) }}" class="inline-flex items-center justify-center bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold px-5 py-2.5 rounded-lg text-[13.5px] shadow-2xs transition">
                Edit Lowongan
            </a>
            <form action="{{ route('admin.jobs.destroy', $job['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan pekerjaan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-lg text-[13.5px] shadow-2xs transition">
                    Hapus Lowongan
                </button>
            </form>
        </div>
    </div>

    {{-- Content Grid (Left Main 2/3, Right Details 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Job Banner / Image Card --}}
            @if (!empty($job['image']) || !empty($job['logo_url']))
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
                    <img src="{{ $job['image'] ?? $job['logo_url'] }}" alt="{{ $job['title'] }}" class="w-full h-56 sm:h-72 object-cover">
                </div>
            @endif

            {{-- Job Description Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
                <h2 class="text-[18px] font-extrabold text-slate-900 pb-4 border-b border-slate-100">
                    Deskripsi Pekerjaan
                </h2>
                <div class="mt-5 text-[14px] leading-relaxed text-slate-600 whitespace-pre-line space-y-4">
                    {{ $job['description'] }}
                </div>
            </div>

            {{-- Kualifikasi & Persyaratan Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
                <h2 class="text-[18px] font-extrabold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <span>Kualifikasi &amp; Persyaratan</span>
                </h2>
                <div class="mt-5 text-[14px] leading-relaxed text-slate-600">
                    @php
                        $reqList = [];
                        if (!empty($job['requirements'])) {
                            if (is_array($job['requirements'])) {
                                $reqList = $job['requirements'];
                            } else {
                                foreach (explode("\n", (string) $job['requirements']) as $line) {
                                    $clean = trim(ltrim($line, "-* "));
                                    if ($clean) $reqList[] = $clean;
                                }
                            }
                        }
                    @endphp

                    @if (!empty($reqList))
                        <ul class="space-y-2.5 list-none pl-0">
                            @foreach ($reqList as $item)
                                <li class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mt-2 shrink-0"></span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-slate-400">Tidak ada persyaratan khusus yang dicantumkan.</p>
                    @endif
                </div>
            </div>

            {{-- Tanggung Jawab Pekerjaan Card --}}
            @if (!empty($job['responsibilities']) && is_array($job['responsibilities']))
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
                    <h2 class="text-[18px] font-extrabold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 20.015A9.753 9.753 0 0 1 12 21.75c-5.385 0-9.75-4.365-9.75-9.75s4.365-9.75 9.75-9.75 9.75 4.365 9.75 9.75c0 1.956-.575 3.776-1.564 5.305m-3.436-1.84c.75-.75 1.72-1.28 2.804-1.51M9 10.5a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0Zm-6 9c0-3.314 3.134-6 7-6 1.834 0 3.497.607 4.743 1.589"/></svg>
                        <span>Tanggung Jawab Pekerjaan</span>
                    </h2>
                    <div class="mt-5 text-[14px] leading-relaxed text-slate-600">
                        <ul class="space-y-2.5 list-none pl-0">
                            @foreach ($job['responsibilities'] as $resp)
                                <li class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mt-2 shrink-0"></span>
                                    <span>{{ $resp }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Skills / Tags Card --}}
            @if (!empty($job['skills']) && is_array($job['skills']))
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
                    <h2 class="text-[18px] font-extrabold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                        <span>Skills &amp; Keahlian</span>
                    </h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($job['skills'] as $skill)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12.5px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Applicants Section --}}
            <div id="daftar-pelamar" class="scroll-mt-6 bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-[19px] font-extrabold text-slate-900 tracking-tight">
                                Daftar Pelamar Masuk
                            </h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $job->applications->count() }} Pelamar
                            </span>
                        </div>
                        <p class="text-[13px] text-slate-500 mt-1">
                            Data pelamar yang mengirimkan berkas lamaran untuk lowongan ini.
                        </p>
                    </div>
                </div>

                @forelse ($job->applications as $application)
                    <div class="rounded-xl border border-slate-200/90 bg-slate-50/40 p-5 space-y-4 hover:border-slate-300 transition">
                        {{-- Top: Applicant Name, Date, Status Badge --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shrink-0">
                                    {{ strtoupper(substr($application->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-[15px] font-bold text-slate-900 leading-tight">
                                        {{ $application->full_name }}
                                    </h4>
                                    <p class="text-[12px] text-slate-500">
                                        Melamar pada {{ $application->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            @php
                                $statusClasses = match($application->status) {
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'reviewed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'shortlisted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses }} self-start sm:self-center">
                                <span class="w-1.5 h-1.5 rounded-full {{ match($application->status) {
                                    'pending' => 'bg-amber-500',
                                    'reviewed' => 'bg-blue-500',
                                    'shortlisted' => 'bg-emerald-500',
                                    'rejected' => 'bg-rose-500',
                                    default => 'bg-slate-400'
                                } }}"></span>
                                {{ $application->statusLabel() }}
                            </span>
                        </div>

                        {{-- Contact Details --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-[13px] pt-1">
                            <div class="flex items-center gap-2 text-slate-600">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <a href="mailto:{{ $application->email }}" class="text-blue-600 hover:underline font-medium truncate">{{ $application->email }}</a>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H3.625A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                <a href="tel:{{ $application->phone }}" class="text-slate-700 hover:text-slate-900 font-medium">{{ $application->phone }}</a>
                            </div>
                            @if (!empty($application->linkedin))
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                    <a href="{{ $application->linkedin }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate">LinkedIn Profil</a>
                                </div>
                            @endif
                            @if (!empty($application->portfolio))
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 3a15.3 15.3 0 0 0 4 9 15.3 15.3 0 0 1-4 9 15.3 15.3 0 0 1-4-9 15.3 15.3 0 0 0 4-9Zm-9 9h18"/></svg>
                                    <a href="{{ $application->portfolio }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate">Website Portofolio</a>
                                </div>
                            @endif
                        </div>

                        {{-- Cover Letter preview with details/summary toggle --}}
                        @if (!empty($application->cover_letter))
                            <details class="group bg-white rounded-xl border border-slate-200/80 p-3.5 text-xs text-slate-700">
                                <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between select-none">
                                    <span class="flex items-center gap-1.5">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                        <span>Surat Lamaran / Cover Letter</span>
                                    </span>
                                    <span class="text-blue-600 font-semibold text-[11px] group-open:hidden">Lihat Pesan</span>
                                    <span class="text-slate-400 font-semibold text-[11px] hidden group-open:inline">Tutup</span>
                                </summary>
                                <div class="mt-3 pt-3 border-t border-slate-100 whitespace-pre-line leading-relaxed text-slate-600">
                                    {{ $application->cover_letter }}
                                </div>
                            </details>
                        @endif

                        {{-- Bottom Action Row: Download Resume Button & Status Update Form --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-200/70">
                            <div>
                                <a href="{{ route('admin.jobs.applications.resume', ['job' => $job['id'], 'application' => $application->id]) }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 font-semibold text-xs px-3.5 py-2 rounded-lg shadow-2xs transition">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    <span>Download Resume (CV)</span>
                                </a>
                            </div>

                            <form action="{{ route('admin.jobs.applications.status', ['job' => $job['id'], 'application' => $application->id]) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-800 font-medium focus:border-brand-600 focus:outline-none">
                                    @foreach (\App\Models\JobApplication::STATUS_LABELS as $sVal => $sText)
                                        <option value="{{ $sVal }}" @selected($application->status === $sVal)>{{ $sText }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="note" placeholder="Catatan (opsional)" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-800 focus:border-brand-600 focus:outline-none w-36 sm:w-44">
                                <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-1.5 transition">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 px-4 rounded-xl border border-dashed border-slate-200 bg-slate-50/50">
                        <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        </div>
                        <h4 class="text-[14.5px] font-bold text-slate-800">Belum Ada Pelamar</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                            Belum ada kandidat yang mengirimkan lamaran untuk lowongan ini. Lamaran baru dari pencari kerja akan langsung tampil di sini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Company Details Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
                <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
                    <div class="w-14 h-14 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">
                        @if (!empty($job['image']) || !empty($job['logo_url']))
                            <img src="{{ $job['image'] ?? $job['logo_url'] }}" alt="{{ $job['company'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-white text-[18px]" style="background: {{ $job['logo_color'] ?? '#2563eb' }}">
                                {{ $job['logo_text'] ?? strtoupper(substr($job['company'], 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-slate-900 leading-snug">
                            {{ $job['company'] }}
                        </h3>
                        <p class="text-[12.5px] text-slate-500 font-medium">
                            {{ $job['company_industry'] ?? 'Enterprise Software' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 divide-y divide-slate-100 text-[13.5px]">
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Lokasi</span>
                        <span class="font-bold text-slate-900 text-right">{{ $job['location'] }}</span>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Ukuran Perusahaan</span>
                        <span class="font-bold text-slate-900 text-right">{{ $job['company_size'] }}</span>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Tanggal Tayang</span>
                        <span class="font-bold text-slate-900 text-right">{{ $job['date_posted'] }}</span>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Batas Waktu Lamaran</span>
                        <span class="font-bold text-red-600 text-right flex items-center gap-1.5">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            {{ $job['deadline'] ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Application Summary Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
                <h3 class="text-[16px] font-bold text-slate-900">
                    Ringkasan Lamaran
                </h3>

                {{-- Views Stat --}}
                <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-4 flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-wider font-extrabold text-brand-600">
                        TOTAL DILIHAT
                    </span>
                    <span class="text-[28px] font-extrabold text-brand-700 leading-none">
                        {{ $job['total_views'] ?? 42 }}
                    </span>
                </div>

                {{-- Applicants Stat --}}
                <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-4 flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-wider font-extrabold text-brand-600">
                        TOTAL PELAMAR
                    </span>
                    <span class="text-[28px] font-extrabold text-brand-700 leading-none">
                        {{ $job->applications->count() }}
                    </span>
                </div>

                {{-- View Applicants Button --}}
                <a href="#daftar-pelamar" class="inline-flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13.5px] py-3 rounded-xl shadow-2xs transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    <span>Lihat Daftar Pelamar ({{ $job->applications->count() }})</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
