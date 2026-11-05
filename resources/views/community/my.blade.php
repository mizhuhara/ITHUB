@extends('layouts.app')

@section('title', 'Komunitas Saya — IndoTech')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<div class="cmty-my-hero">
    <div class="cmty-my-hero-inner">
        <div class="cmty-my-badge">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Ruang Pengelola Komunitas
        </div>
        <h1 class="cmty-my-title">Komunitas Saya</h1>
        <p class="cmty-my-sub">
            Kelola komunitas yang telah Anda dirikan. Sebagai admin, Anda dapat mengatur detail profil komunitas, mengelola keanggotaan, serta memoderasi postingan diskusi.
        </p>

        <div class="cmty-my-actions-bar">
            <a href="{{ route('community.index') }}" class="cmty-my-back-link">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Jelajahi Semua Komunitas
            </a>
            <a href="{{ route('community.create') }}" class="cmty-my-add-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Komunitas Baru
            </a>
        </div>
    </div>
</div>

{{-- ===== MAIN CONTENT WRAPPER ===== --}}
<div class="cmty-my-wrap">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="cmty-my-alert cmty-my-alert-success">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="cmty-my-alert cmty-my-alert-error">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Content Condition --}}
    @if($communities->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="cmty-empty-box">
            <div class="cmty-empty-icon-wrap">
                <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="cmty-empty-title">Kamu belum membuat komunitas</h2>
            <p class="cmty-empty-desc">
                Mulai bangun wadah kolaborasi teknologi, bagikan pengetahuan, dan undang para talenta di seluruh Indonesia untuk bergabung dalam komunitasmu.
            </p>
            <a href="{{ route('community.create') }}" class="cmty-empty-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Komunitas
            </a>
        </div>
    @else
        {{-- LIST OF COMMUNITIES --}}
        <div class="cmty-my-header-row">
            <div>
                <h2 class="cmty-my-section-heading">Komunitas yang Anda Kelola ({{ $communities->total() }})</h2>
                <p class="cmty-my-section-sub">Anda memiliki hak penuh untuk mengatur informasi, mengelola anggota, dan memoderasi postingan.</p>
            </div>
        </div>

        <div class="cmty-cards-grid">
            @foreach($communities as $community)
                @php
                    $colors = [
                        ['#3b82f6','#6366f1'],
                        ['#ec4899','#f43f5e'],
                        ['#10b981','#14b8a6'],
                        ['#f59e0b','#f97316'],
                        ['#8b5cf6','#a855f7'],
                    ];
                    $c = $colors[$loop->index % count($colors)];
                @endphp
                <div class="cmty-card">
                    {{-- Cover Banner Strip if exists --}}
                    @if($community->cover_url)
                        <div class="cmty-card-cover-strip">
                            <img src="{{ $community->cover_url }}" alt="{{ $community->name }}">
                        </div>
                    @endif

                    <div class="cmty-card-top">
                        <div class="cmty-card-logo-wrap">
                            @if($community->logo_url)
                                <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="cmty-card-logo">
                            @else
                                <div class="cmty-card-logo-init" style="background: linear-gradient(135deg, {{ $c[0] }}, {{ $c[1] }});">
                                    {{ strtoupper(substr($community->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        <div class="cmty-card-badges">
                            {{-- Approval Status Badges --}}
                            @if($community->approval_status === 'pending')
                                <span class="cmty-badge-pending">
                                    <span class="cmty-pulse-dot-pending"></span> Menunggu Persetujuan
                                </span>
                            @elseif($community->approval_status === 'rejected')
                                <span class="cmty-badge-rejected">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Ditolak
                                </span>
                            @else
                                <span class="cmty-badge-approved">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Disetujui
                                </span>
                            @endif

                            <div class="cmty-badge-row">
                                <span class="cmty-badge cmty-badge-cat">{{ $community->category }}</span>
                                <span class="cmty-badge cmty-badge-type">
                                    {{ $community->type === 'private' ? 'Privat' : 'Publik' }}
                                </span>
                                <span class="cmty-badge cmty-badge-mode">
                                    {{ $community->formatted_mode }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Title & Meta --}}
                    <div>
                        <h3 class="cmty-card-title">
                            @if($community->approval_status === 'approved')
                                <a href="{{ route('community.show', $community) }}">{{ $community->name }}</a>
                            @else
                                <span>{{ $community->name }}</span>
                            @endif
                        </h3>
                        <p class="cmty-card-desc">
                            {{ Str::limit($community->description ?: 'Belum ada deskripsi untuk komunitas ini.', 120) }}
                        </p>
                        @if(!empty($community->tags))
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($community->tags as $tag)
                                    <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Rejection Box if Rejected --}}
                    @if($community->approval_status === 'rejected')
                        <div class="cmty-reject-box">
                            <div class="cmty-reject-title">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Alasan Penolakan:</span>
                            </div>
                            <p class="cmty-reject-reason">{{ $community->rejection_reason }}</p>
                            <div class="cmty-reject-footer">
                                <span class="cmty-reject-date">Ditolak {{ $community->rejected_at?->diffForHumans() }}</span>
                                <div class="cmty-reject-actions">
                                    <a href="{{ route('community.manage', $community) }}" class="cmty-btn-edit-fix">
                                        Perbaiki Informasi
                                    </a>
                                    <button type="button"
                                            onclick="openResubmitModal({{ $community->id }}, '{{ addslashes($community->name) }}', '{{ addslashes($community->rejection_reason ?? '') }}')"
                                            class="cmty-btn-resubmit">
                                        Ajukan Kembali
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($community->approval_status === 'pending')
                        <div class="cmty-pending-notice">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Menunggu persetujuan Super Admin sebelum tampil publik.</span>
                        </div>
                        @if($community->resubmit_notes)
                            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 12px; margin-top: 10px; font-size: 11.5px; color: #92400e;">
                                <div style="font-weight: 700; margin-bottom: 3px; display: flex; align-items: center; gap: 5px;">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Catatan Perbaikan yang Diajukan:</span>
                                </div>
                                <div style="line-height: 1.45; color: #78350f;">{{ $community->resubmit_notes }}</div>
                            </div>
                        @endif
                    @endif

                    {{-- Community Info Pills --}}
                    <div class="cmty-card-meta-pills">
                        @if($community->language)
                            <span class="cmty-meta-pill">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                                {{ $community->language }}
                            </span>
                        @endif
                    </div>

                    {{-- Member Counter & Admin Badge --}}
                    <div class="cmty-card-meta-row">
                        <div class="cmty-card-members">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <strong>{{ number_format($community->members) }}</strong>
                            <span>Anggota</span>
                        </div>
                        <span class="cmty-admin-tag">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.396 1.157l-.8 1.599L18.73 10a1 1 0 010 2l-1.582.791.8 1.599a1 1 0 01-1.396 1.157l-1.599-.8L11 16.323V18a1 1 0 11-2 0v-1.677l-3.954-.791-1.599.8a1 1 0 01-1.396-1.157l.8-1.599L1.27 12a1 1 0 010-2l1.582-.791-.8-1.599a1 1 0 011.396-1.157l1.599.8L9 4.323V3a1 1 0 011-1zm0 5a3 3 0 100 6 3 3 0 000-6z" clip-rule="evenodd"/>
                            </svg>
                            Admin &amp; Pemilik
                        </span>
                    </div>

                    {{-- Actions: Lihat Komunitas, Chat, & Kelola Komunitas --}}
                    <div class="cmty-card-actions">
                        @if($community->approval_status === 'approved')
                            {{-- Lihat Komunitas --}}
                            <a href="{{ route('community.show', $community) }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition"
                               title="Lihat Komunitas" aria-label="Lihat Komunitas">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="cmty-tooltip">Lihat Komunitas</span>
                            </a>
                            {{-- Chat Komunitas --}}
                            <a href="{{ route('community.chat', $community) }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 shadow-sm transition"
                               title="Chat Komunitas" aria-label="Chat Komunitas">
                                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="cmty-tooltip">Chat Komunitas</span>
                            </a>
                        @else
                            {{-- Pratinjau Komunitas --}}
                            <a href="{{ route('community.show', $community) }}"
                               class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 shadow-sm transition"
                               title="Pratinjau Komunitas" aria-label="Pratinjau Komunitas">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="cmty-tooltip">Pratinjau Komunitas</span>
                            </a>
                        @endif

                        {{-- Kelola Komunitas --}}
                        <a href="{{ route('community.manage', $community) }}"
                           class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition"
                           title="Kelola Komunitas" aria-label="Kelola Komunitas">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="cmty-tooltip">Kelola Komunitas</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($communities->hasPages())
            <div class="cmty-my-pagination">
                {{ $communities->links() }}
            </div>
        @endif
    @endif
</div>

<style>
/* ============================================
   KOMUNITAS SAYA (MY COMMUNITIES) STYLES
   ============================================ */

.cmty-my-hero {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 44px 24px 36px;
    text-align: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cmty-my-hero-inner {
    max-width: 760px;
    margin: 0 auto;
}
.cmty-my-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    font-size: 12.5px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 999px;
    margin-bottom: 16px;
}
.cmty-my-title {
    font-size: 36px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -0.5px;
    margin: 0 0 12px;
}
@media (min-width: 640px) { .cmty-my-title { font-size: 42px; } }
.cmty-my-sub {
    font-size: 15px;
    color: #64748b;
    line-height: 1.65;
    margin: 0 auto 24px;
    max-width: 620px;
}
.cmty-my-actions-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}
.cmty-my-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 9px 16px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.cmty-my-back-link:hover {
    color: #2563eb;
    background: #eff6ff;
    border-color: #bfdbfe;
}
.cmty-my-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
    background: #2563eb;
    border: 1.5px solid #1d4ed8;
    border-radius: 10px;
    padding: 9px 18px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    transition: all 0.15s ease;
}
.cmty-my-add-btn:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}

/* Page Wrapper */
.cmty-my-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 36px 24px 64px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Alerts */
.cmty-my-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
}
.cmty-my-alert-success {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    color: #166534;
}
.cmty-my-alert-success svg { color: #16a34a; flex-shrink: 0; }
.cmty-my-alert-error {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #991b1b;
}
.cmty-my-alert-error svg { color: #dc2626; flex-shrink: 0; }

/* Empty State */
.cmty-empty-box {
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 20px;
    padding: 64px 24px;
    text-align: center;
    max-width: 580px;
    margin: 20px auto 40px;
}
.cmty-empty-icon-wrap {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}
.cmty-empty-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}
.cmty-empty-desc {
    font-size: 14.5px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 auto 26px;
    max-width: 440px;
}
.cmty-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    background: #2563eb;
    border: 1.5px solid #1d4ed8;
    border-radius: 12px;
    padding: 12px 28px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
    transition: all 0.15s ease;
}
.cmty-empty-btn:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
}

/* Header Row */
.cmty-my-header-row {
    margin-bottom: 22px;
}
.cmty-my-section-heading {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}
.cmty-my-section-sub {
    font-size: 13.5px;
    color: #64748b;
    margin: 0;
}

/* Cards Grid */
.cmty-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}
@media (max-width: 768px) {
    .cmty-cards-grid { grid-template-columns: 1fr; }
}

/* Card Shell */
.cmty-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #ffffff;
    position: relative;
    overflow: visible;
    transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
}
.cmty-card:hover {
    box-shadow: 0 8px 26px rgba(0,0,0,0.07);
    border-color: #bfdbfe;
    transform: translateY(-2px);
}
.cmty-card-cover-strip {
    margin: -20px -20px 0 -20px;
    height: 72px;
    overflow: hidden;
    background: #f1f5f9;
    border-radius: 14px 14px 0 0;
}
.cmty-card-cover-strip img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cmty-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.cmty-card-logo-wrap { flex-shrink: 0; }
.cmty-card-logo {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    object-fit: cover;
    border: 1.5px solid #e2e8f0;
}
.cmty-card-logo-init {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 800;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.cmty-card-badges {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
}
.cmty-badge-pending {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    border: 1px solid #fde68a;
    padding: 3px 10px;
    border-radius: 999px;
}
.cmty-pulse-dot-pending {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #f59e0b;
    animation: cmty-pulse 2s infinite;
}
.cmty-badge-approved {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #15803d;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    padding: 3px 10px;
    border-radius: 999px;
}
.cmty-badge-rejected {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #b91c1c;
    background: #fee2e2;
    border: 1px solid #fecaca;
    padding: 3px 10px;
    border-radius: 999px;
}
.cmty-badge-mode {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.cmty-pending-notice {
    display: flex;
    align-items: center;
    gap: 7px;
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 11.5px;
    color: #92400e;
    font-weight: 500;
}
.cmty-reject-box {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.cmty-reject-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #991b1b;
}
.cmty-reject-reason {
    font-size: 12px;
    color: #7f1d1d;
    line-height: 1.5;
    margin: 0;
    padding-left: 20px;
}
.cmty-reject-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 4px;
    padding-top: 8px;
    border-top: 1px dashed #fca5a5;
    flex-wrap: wrap;
}
.cmty-reject-date {
    font-size: 10.5px;
    color: #b91c1c;
    font-weight: 500;
}
.cmty-reject-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.cmty-btn-edit-fix {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 4px 10px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.15s;
}
.cmty-btn-edit-fix:hover {
    background: #f8fafc;
    color: #0f172a;
}
.cmty-btn-resubmit {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    background: #dc2626;
    border: none;
    border-radius: 6px;
    padding: 4px 10px;
    cursor: pointer;
    transition: all 0.15s;
}
.cmty-btn-resubmit:hover {
    background: #b91c1c;
}
.cmty-badge-aktif {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    color: #16a34a;
    background: #dcfce7;
    padding: 3px 9px;
    border-radius: 999px;
}
.cmty-badge-inaktif {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 9px;
    border-radius: 999px;
}
.cmty-pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16a34a;
    animation: cmty-pulse 2s infinite;
}
@keyframes cmty-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
}
.cmty-badge-row {
    display: flex;
    align-items: center;
    gap: 5px;
}
.cmty-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2.5px 8.5px;
    border-radius: 6px;
}
.cmty-badge-cat {
    background: #f1f5f9;
    color: #475569;
}
.cmty-badge-type {
    background: #ede9fe;
    color: #6d28d9;
}

/* Card Content */
.cmty-card-title {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
    line-height: 1.35;
}
.cmty-card-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.15s;
}
.cmty-card-title a:hover {
    color: #2563eb;
}
.cmty-card-desc {
    font-size: 13.5px;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
}

/* Meta pills */
.cmty-card-meta-pills {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}
.cmty-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 9px;
    border-radius: 6px;
}
.cmty-meta-pill svg { color: #64748b; }

/* Meta row */
.cmty-card-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}
.cmty-card-members {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #64748b;
}
.cmty-card-members svg { color: #2563eb; }
.cmty-card-members strong { color: #0f172a; font-weight: 800; }
.cmty-admin-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #1d4ed8;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 2.5px 8px;
    border-radius: 6px;
}

/* Action Buttons */
.cmty-card-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}

/* Tooltip muncul ke bawah agar tidak terpotong overflow:hidden card */
.cmty-tooltip {
    pointer-events: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    border-radius: 6px;
    background: #0f172a;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 500;
    color: #ffffff;
    opacity: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,.18);
    transition: opacity 0.15s ease;
    z-index: 50;
}
a:hover > .cmty-tooltip {
    opacity: 1;
}

.cmty-my-pagination {
    margin-top: 36px;
    display: flex;
    justify-content: center;
}
</style>

{{-- ========================================================================= --}}
{{-- MODAL POP-UP: AJUKAN KEMBALI KOMUNITAS BESERTA CATATAN PERBAIKAN --}}
{{-- ========================================================================= --}}
<div id="modal-resubmit-community" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 text-left relative animate-in fade-in zoom-in-95 duration-150">
        {{-- Close Button --}}
        <button type="button" onclick="closeResubmitModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition" aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1 pr-6">
                <h3 class="text-base font-extrabold text-slate-900">Ajukan Kembali Komunitas</h3>
                <p id="resubmit-community-name" class="text-xs text-blue-600 font-bold mt-0.5 truncate"></p>
            </div>
        </div>

        {{-- Alasan Penolakan Sebelumnya --}}
        <div class="bg-rose-50/90 border border-rose-200/90 rounded-2xl p-3.5 mb-4 text-xs text-rose-800">
            <div class="font-bold flex items-center gap-1.5 text-rose-900 mb-1">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Catatan Penolakan Super Admin:</span>
            </div>
            <p id="resubmit-rejection-reason" class="text-rose-700 leading-relaxed pl-5 font-normal"></p>
        </div>

        {{-- Form Resubmit --}}
        <form id="form-resubmit-community" method="POST" action="">
            @csrf
            <div class="space-y-1.5 mb-5">
                <label for="resubmit-notes-input" class="block text-xs font-bold text-slate-900">
                    Catatan / Keterangan Perbaikan Komunitas <span class="text-red-500">*</span>
                </label>
                <p class="text-[11.5px] text-slate-500 leading-relaxed">
                    Tuliskan penjelasan mengenai apa saja yang telah Anda perbaiki sesuai alasan penolakan di atas agar Super Admin dapat meninjaunya kembali.
                </p>
                <textarea id="resubmit-notes-input"
                          name="resubmit_notes"
                          rows="4"
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition leading-relaxed placeholder:text-slate-400"
                          placeholder="Contoh: Kami telah memperbarui deskripsi komunitas, melengkapi aturan tata tertib anggota, dan mengganti logo agar sesuai panduan..."
                          required></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeResubmitModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition flex items-center gap-1.5">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim Pengajuan Ulang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResubmitModal(communityId, communityName, rejectionReason) {
    const modal = document.getElementById('modal-resubmit-community');
    const form = document.getElementById('form-resubmit-community');
    form.action = `{{ url('/community') }}/${communityId}/resubmit`;
    document.getElementById('resubmit-community-name').textContent = communityName;
    document.getElementById('resubmit-rejection-reason').textContent = rejectionReason || 'Tidak ada catatan penolakan spesifik.';
    document.getElementById('resubmit-notes-input').value = '';

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => document.getElementById('resubmit-notes-input').focus(), 100);
}

function closeResubmitModal() {
    const modal = document.getElementById('modal-resubmit-community');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeResubmitModal();
    }
});
</script>

@endsection

