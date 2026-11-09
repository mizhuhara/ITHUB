@php
    $initials = collect(explode(' ', $seeker->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
    $colors = ['#2563eb','#7c3aed','#db2777','#059669','#d97706','#0891b2','#dc2626','#65a30d'];
    $avatarColor = $colors[$seeker->id % count($colors)];
    $availClass = match($seeker->availability) {
        'Segera' => 'remote',
        'Bulan Depan' => 'internship',
        default => 'jobs',
    };
@endphp

<article class="cr-card" id="seeker-card-{{ $seeker->id }}">
    <div class="cr-card-top">
        @if($seeker->photo_url)
            <img src="{{ $seeker->photo_url }}" alt="{{ $seeker->name }}" class="cr-logo" style="object-fit:cover; border-radius:6px;">
        @else
            <div class="cr-logo" style="background: {{ $avatarColor }}; border-radius:6px; font-weight:700; font-size:11px;">{{ $initials }}</div>
        @endif
        <span class="cr-tag {{ $availClass }}">{{ $seeker->availability }}</span>
    </div>

    <h3 class="cr-card-title" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
        <span>{{ $seeker->name }}</span>
        @if($seeker->user)
            <span title="Akun Terverifikasi ITHub" style="display:inline-flex;align-items:center;color:#2563eb;font-size:11px;font-weight:600;background:#eff6ff;padding:1px 7px;border-radius:99px;border:1px solid #bfdbfe;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" style="margin-right:3px"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                Terverifikasi
            </span>
        @endif
    </h3>
    <p class="cr-card-company" style="color: #2563eb; font-weight: 600;">{{ $seeker->job_title }}</p>

    <div class="cr-meta">
        @if($seeker->expected_salary)
            <span class="cr-meta-item">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                Gaji: {{ $seeker->expected_salary }}
            </span>
        @endif
        @if($seeker->location)
            <span class="cr-meta-item">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
                {{ $seeker->location }}
            </span>
        @endif
        <span class="cr-meta-item">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
            </svg>
            {{ $seeker->experience_level }} Level
        </span>
        <span class="cr-meta-item">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            Siap: {{ $seeker->availability }}
        </span>
    </div>

    @if(!empty($seeker->skills))
        <div class="cr-skill-row">
            @foreach(array_slice($seeker->skills, 0, 4) as $skill)
                <span class="cr-chip">{{ $skill }}</span>
            @endforeach
            @if(count($seeker->skills) > 4)
                <span class="cr-chip" style="background:#e2e8f0;color:#64748b;">+{{ count($seeker->skills) - 4 }}</span>
            @endif
        </div>
    @endif

    <p class="cr-desc">{{ $seeker->about ?: 'Pencari kerja siap bergabung dengan tim dan berkontribusi secara maksimal.' }}</p>

    <div class="cr-card-bottom">
        <span class="it-status"><span class="it-status-dot"></span> Job Seeker</span>
        <a href="{{ route('career.seeker.show', $seeker->id) }}" class="cr-apply" id="view-seeker-{{ $seeker->id }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" style="margin-right:4px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
            Lihat Detail
        </a>
    </div>
</article>

