@extends('layouts.app')

@section('title', 'Kelola Komunitas — ' . $community->name)

@section('content')
<div class="cmty-manage-page">
    <div class="cmty-manage-inner">

        {{-- Top Navigation & Quick Links --}}
        <div class="cmty-manage-top-nav">
            <a href="{{ route('community.my') }}" class="cmty-manage-back-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Komunitas Saya
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('community.chat', $community) }}" class="cmty-manage-chat-btn" target="_blank">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Buka Chat Komunitas</span>
                </a>
                <a href="{{ route('community.show', $community) }}" class="cmty-manage-view-public" target="_blank">
                    <span>Lihat Halaman Publik</span>
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="cmty-manage-alert cmty-manage-alert-success">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="cmty-manage-alert cmty-manage-alert-error">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="cmty-manage-alert cmty-manage-alert-error">
                <div>
                    <strong>Terdapat beberapa kesalahan validasi:</strong>
                    <ul class="mt-1 list-disc pl-5 text-xs">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Community Identity Banner Card --}}
        <div class="cmty-manage-banner-card">
            @if($community->cover_url)
                <div class="cmty-manage-cover h-44 sm:h-60">
                    <img src="{{ $community->cover_url }}" alt="{{ $community->name }}">
                </div>
            @endif

            <div class="cmty-manage-header-body">
                <div class="cmty-manage-header-flex">
                    <div class="cmty-manage-logo-wrap">
                        @if($community->logo_url)
                            <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="cmty-manage-logo">
                        @else
                            <div class="cmty-manage-logo-init">
                                {{ strtoupper(substr($community->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    <div class="cmty-manage-info">
                        <div class="cmty-manage-badges">
                            @if($isOwner)
                                <span class="cmty-manage-badge cmty-manage-badge-role">Admin &amp; Pemilik</span>
                            @elseif(($userRole ?? null) === 'admin')
                                <span class="cmty-manage-badge cmty-manage-badge-role">Admin Komunitas</span>
                            @elseif(($userRole ?? null) === 'moderator')
                                <span class="cmty-manage-badge cmty-manage-badge-role cmty-manage-badge-moderator">Moderator</span>
                            @endif
                            <span class="cmty-manage-badge cmty-manage-badge-cat">{{ $community->category }}</span>
                            <span class="cmty-manage-badge cmty-manage-badge-type">{{ $community->type === 'private' ? 'Privat' : 'Publik' }}</span>
                            @if($community->status === 'active')
                                <span class="cmty-manage-badge cmty-manage-badge-active">● Aktif</span>
                            @else
                                <span class="cmty-manage-badge cmty-manage-badge-inactive">● Nonaktif</span>
                            @endif
                        </div>

                        <h1 class="cmty-manage-title">{{ $community->name }}</h1>
                        <p class="cmty-manage-desc">{{ $community->description ?: 'Belum ada deskripsi komunitas.' }}</p>

                        <div class="cmty-manage-stats">
                            <div class="cmty-stat-item">
                                <span class="cmty-stat-value">{{ number_format($community->members) }}</span>
                                <span class="cmty-stat-label">Anggota Terdaftar</span>
                            </div>
                            <div class="cmty-stat-item">
                                <span class="cmty-stat-value">{{ $community->created_at?->format('d M Y') }}</span>
                                <span class="cmty-stat-label">Tanggal Dibuat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Manage Navigation Tabs --}}
        <div class="cmty-tabs-bar">
            @if($isOwner)
                <button type="button" class="cmty-tab-btn active" onclick="showManageTab('settings', this)">
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pengaturan Komunitas
                </button>
            @endif
            <button type="button" class="cmty-tab-btn {{ ! $isOwner ? 'active' : '' }}" onclick="showManageTab('members', this)">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Kelola Anggota ({{ $members->count() }})
            </button>
        </div>

        @if($isOwner)
        {{-- ============================================================ --}}
        {{-- TAB 1: PENGATURAN KOMUNITAS                                   --}}
        {{-- ============================================================ --}}
        <div id="tab-settings" class="cmty-tab-pane active">
            <div class="cmty-pane-card">
                <div class="cmty-pane-header">
                    <h2>Pengaturan Informasi Komunitas</h2>
                    <p>Perbarui profil, visibilitas, status, serta tata tertib komunitas Anda.</p>
                </div>

                @if($community->approval_status === 'rejected')
                    <div style="margin-bottom: 24px; padding: 16px; border-radius: 14px; background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Pengajuan Komunitas Ini Ditolak oleh Super Admin</span>
                        </div>
                        <p style="font-size: 12.5px; color: #7f1d1d; margin: 0 0 6px 26px; line-height: 1.5;">
                            Alasan penolakan: <strong>{{ $community->rejection_reason }}</strong>
                        </p>
                        <p style="font-size: 11.5px; color: #b91c1c; margin: 0 0 0 26px;">
                            Silakan perbarui data di bawah, lalu klik tombol <strong>"Simpan &amp; Ajukan Kembali"</strong> di bawah untuk mengirim ulang ke Super Admin.
                        </p>
                    </div>
                @elseif($community->approval_status === 'pending')
                    <div style="margin-bottom: 24px; padding: 14px; border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; display: flex; align-items: center; gap: 10px; font-size: 12px; font-weight: 500;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #d97706; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Komunitas ini sedang dalam antrean review persetujuan Super Admin sebelum tampil di direktori publik.</span>
                    </div>
                @endif

                <form data-async data-async-reload action="{{ route('community.manage.update', $community) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="cmty-manage-form-grid">
                        {{-- Nama Komunitas --}}
                        <div class="cmty-form-full">
                            <label class="cmty-m-label" for="m-name">Nama Komunitas <span class="text-red-500">*</span></label>
                            <input type="text" id="m-name" name="name" class="cmty-m-input" value="{{ old('name', $community->name) }}" required>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="cmty-m-label" for="m-cat">Kategori <span class="text-red-500">*</span></label>
                            <select id="m-cat" name="category" class="cmty-m-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $community->category) === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Komunitas --}}
                        <div>
                            <label class="cmty-m-label" for="m-status">Status Komunitas <span class="text-red-500">*</span></label>
                            <select id="m-status" name="status" class="cmty-m-select" required>
                                <option value="active" {{ old('status', $community->status) === 'active' ? 'selected' : '' }}>Aktif (Tampil di direktori)</option>
                                <option value="inactive" {{ old('status', $community->status) === 'inactive' ? 'selected' : '' }}>Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>

                        {{-- Jenis Komunitas --}}
                        <div>
                            <label class="cmty-m-label" for="m-type">Jenis Komunitas <span class="text-red-500">*</span></label>
                            <select id="m-type" name="type" class="cmty-m-select" required>
                                <option value="public" {{ old('type', $community->type) === 'public' ? 'selected' : '' }}>Publik (Semua orang bisa bergabung)</option>
                                <option value="private" {{ old('type', $community->type) === 'private' ? 'selected' : '' }}>Privat (Akses terbatas)</option>
                            </select>
                        </div>

                        {{-- Mode Komunitas --}}
                        <div>
                            <label class="cmty-m-label" for="m-mode">Mode Komunitas <span class="text-red-500">*</span></label>
                            <select id="m-mode" name="mode" class="cmty-m-select" required>
                                <option value="online" {{ old('mode', $community->mode) === 'online' ? 'selected' : '' }}>Online (Daring)</option>
                                <option value="offline" {{ old('mode', $community->mode) === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                                <option value="hybrid" {{ old('mode', $community->mode) === 'hybrid' ? 'selected' : '' }}>Hybrid (Kombinasi)</option>
                            </select>
                        </div>

                        {{-- Bahasa Utama --}}
                        <div>
                            <label class="cmty-m-label" for="m-lang">Bahasa Utama <span class="text-red-500">*</span></label>
                            <input type="text" id="m-lang" name="language" class="cmty-m-input" value="{{ old('language', $community->language) }}" placeholder="Contoh: Bahasa Indonesia" required>
                        </div>

                        {{-- Ganti Logo --}}
                        <div>
                            <label class="cmty-m-label" for="m-logo">Ganti Logo Komunitas</label>
                            <input type="file" id="m-logo" name="logo" accept="image/*" class="cmty-m-file">
                            <p class="cmty-m-hint">Format: JPG, PNG, WebP, SVG. Maks 2MB.</p>
                        </div>

                        {{-- Ganti Banner --}}
                        <div>
                            <label class="cmty-m-label" for="m-banner">Ganti Banner Komunitas</label>
                            <input type="file" id="m-banner" name="banner" accept="image/*" class="cmty-m-file">
                            <p class="cmty-m-hint">Format: JPG, PNG, WebP. Maks 4MB.</p>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="cmty-form-full">
                            <label class="cmty-m-label" for="m-desc">Deskripsi Komunitas <span class="text-red-500">*</span></label>
                            <textarea id="m-desc" name="description" rows="4" class="cmty-m-textarea" required>{{ old('description', $community->description) }}</textarea>
                        </div>

                        {{-- Aturan Komunitas --}}
                        <div class="cmty-form-full">
                            <label class="cmty-m-label" for="m-rules">Aturan Komunitas (Opsional)</label>
                            <textarea id="m-rules" name="rules" rows="4" class="cmty-m-textarea" placeholder="Tata tertib bagi anggota...">{{ old('rules', $community->rules) }}</textarea>
                        </div>

                        {{-- Tag Komunitas --}}
                        <div class="cmty-form-full">
                            <label class="cmty-m-label" for="m-tags">Tag / Topik Komunitas (Opsional)</label>
                            <input type="text"
                                   id="m-tags"
                                   name="tags"
                                   class="cmty-m-input"
                                   value="{{ old('tags', !empty($community->tags) ? implode(', ', $community->tags) : '') }}"
                                   placeholder="Contoh: UI/UX, Figma, Prototyping, Design System">
                            <p class="cmty-m-hint">Pisahkan tag dengan tanda koma (,). Tag akan ditampilkan pada kartu komunitas di halaman direktori.</p>
                        </div>

                        {{-- Catatan Perbaikan Komunitas (Khusus jika status ditolak) --}}
                        @if($community->approval_status === 'rejected')
                            <div class="cmty-form-full" style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 14px; padding: 18px; margin-top: 10px;">
                                <label class="cmty-m-label" for="m-resubmit-notes" style="color: #92400e; font-weight: 700;">
                                    Catatan / Caption Perbaikan Komunitas <span class="text-red-500">*</span>
                                </label>
                                <p style="font-size: 12px; color: #78350f; margin: 0 0 8px; line-height: 1.5;">
                                    Tuliskan penjelasan mengenai apa saja yang telah Anda perbaiki sesuai alasan penolakan agar Super Admin dapat meninjaunya kembali.
                                </p>
                                <textarea id="m-resubmit-notes" 
                                          name="resubmit_notes" 
                                          rows="3" 
                                          class="cmty-m-textarea" 
                                          placeholder="Contoh: Kami telah memperjelas deskripsi komunitas dan melengkapi tata tertib sesuai arahan..." 
                                          required>{{ old('resubmit_notes', $community->resubmit_notes) }}</textarea>
                            </div>
                        @endif
                    </div>

                    <div class="cmty-manage-actions">
                        @if($community->approval_status === 'rejected')
                            <button type="submit" name="resubmit" value="1" class="cmty-m-submit-btn" style="background: #dc2626;">
                                Simpan &amp; Ajukan Kembali ke Super Admin
                            </button>
                        @else
                            <button type="submit" class="cmty-m-submit-btn">
                                Simpan Perubahan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- ============================================================ --}}
        {{-- TAB 2: KELOLA ANGGOTA & KICK/BAN                              --}}
        {{-- ============================================================ --}}
        <div id="tab-members" class="cmty-tab-pane {{ ! $isOwner ? 'active' : '' }}">
            <div class="cmty-pane-card">
                <div class="cmty-pane-header">
                    <h2>Daftar Anggota Komunitas</h2>
                    <p>
                        @if($isOwner)
                            Sebagai pemilik, Anda dapat mengatur peran anggota (Anggota, Moderator, Admin), mengeluarkan anggota yang melanggar, atau memblokir akses ke komunitas.
                        @else
                            Sebagai moderator/admin, Anda dapat meninjau keanggotaan dan menertibkan anggota yang tidak sopan (kick / ban).
                        @endif
                    </p>
                </div>

                @if($members->isEmpty())
                    <div class="cmty-empty-box-small">
                        <p>Belum ada anggota yang terdaftar di komunitas ini.</p>
                    </div>
                @else
                    <div class="cmty-members-table-wrap">
                        <table class="cmty-members-table">
                            <thead>
                                <tr>
                                    <th>Anggota</th>
                                    <th>Peran</th>
                                    <th>Status Akses</th>
                                    <th>Bergabung Pada</th>
                                    <th class="text-right">Aksi Moderasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $m)
                                    @php
                                        $isCreator = ($m->id === $community->user_id);
                                        $isBanned = (bool) ($m->pivot->is_banned ?? false);
                                        $mRole = $m->pivot->role ?? 'member';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="cmty-member-cell">
                                                <div class="cmty-member-avatar">
                                                    {{ strtoupper(substr($m->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="cmty-member-name">
                                                        {{ $m->name }}
                                                        @if($isCreator)
                                                            <span class="cmty-tag-you">(Anda / Pemilik)</span>
                                                        @elseif(auth()->id() === $m->id)
                                                            <span class="cmty-tag-you">(Anda)</span>
                                                        @endif
                                                    </span>
                                                    <span class="cmty-member-email">{{ $m->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isCreator)
                                                <span class="cmty-role-pill admin">Pemilik &amp; Admin</span>
                                            @elseif($isOwner)
                                                <form data-async data-async-reload action="{{ route('community.manage.member.role', [$community, $m]) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="role" onchange="this.form.submit()" class="cmty-role-select role-{{ $mRole }}" title="Ubah role anggota">
                                                        <option value="member" {{ $mRole === 'member' ? 'selected' : '' }}>Anggota</option>
                                                        <option value="moderator" {{ $mRole === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                        <option value="admin" {{ $mRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </form>
                                            @else
                                                @if($mRole === 'admin')
                                                    <span class="cmty-role-pill admin">Admin</span>
                                                @elseif($mRole === 'moderator')
                                                    <span class="cmty-role-pill moderator">Moderator</span>
                                                @else
                                                    <span class="cmty-role-pill member">Anggota</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($isBanned)
                                                <span class="cmty-status-pill banned">Diblokir (Banned)</span>
                                            @else
                                                <span class="cmty-status-pill active">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="cmty-time-pill">{{ $m->pivot->created_at?->format('d M Y') ?: '-' }}</span>
                                        </td>
                                        <td class="text-right">
                                            @if($isCreator)
                                                <span class="text-xs text-slate-400 font-semibold italic">Pemilik Utama</span>
                                            @else
                                                @php
                                                    $canKick = $community->canKickMember(auth()->user(), $m);
                                                    $canBan = $community->canBanMember(auth()->user(), $m);
                                                @endphp

                                                @if($canKick || $canBan)
                                                    <div class="cmty-action-buttons">
                                                        {{-- Kick Button --}}
                                                        @if($canKick)
                                                            <form data-async data-async-reload action="{{ route('community.manage.kick', [$community, $m]) }}" method="POST"
                                                                  id="kick-form-{{ $m->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="cmty-btn-kick" title="Keluarkan anggota"
                                                                        onclick="openConfirmModal('kick', '{{ $m->id }}', '{{ e($m->name) }}', false)">
                                                                    Keluarkan
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Ban / Unban Toggle Button --}}
                                                        @if($canBan)
                                                            <form data-async data-async-reload action="{{ route('community.manage.ban', [$community, $m]) }}" method="POST"
                                                                  id="ban-form-{{ $m->id }}">
                                                                @csrf
                                                                <button type="button" class="cmty-btn-ban {{ $isBanned ? 'unban' : '' }}" title="{{ $isBanned ? 'Buka blokir anggota' : 'Blokir anggota' }}"
                                                                        onclick="openConfirmModal('ban', '{{ $m->id }}', '{{ e($m->name) }}', {{ $isBanned ? 'true' : 'false' }})">
                                                                    {{ $isBanned ? 'Buka Blokir' : 'Blokir' }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 font-medium italic">Tidak ada aksi</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
/* ============================================
   KELOLA KOMUNITAS (COMMUNITY MANAGEMENT) STYLES
   ============================================ */

.cmty-manage-page {
    background: #f8fafc;
    min-height: 100vh;
    padding: 32px 20px 80px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cmty-manage-inner {
    max-width: 1100px;
    margin: 0 auto;
}

/* Top Nav */
.cmty-manage-top-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.cmty-manage-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 700;
    color: #475569;
    text-decoration: none;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 10px;
    transition: all 0.15s ease;
}
.cmty-manage-back-btn:hover {
    color: #2563eb;
    border-color: #bfdbfe;
    background: #eff6ff;
}
.cmty-manage-view-public {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    transition: all 0.15s ease;
}
.cmty-manage-view-public:hover {
    background: #dbeafe;
}
.cmty-manage-chat-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #059669;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 10px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    transition: all 0.15s ease;
}
.cmty-manage-chat-btn:hover {
    background: #d1fae5;
    color: #047857;
}

/* Alerts */
.cmty-manage-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}
.cmty-manage-alert-success {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    color: #166534;
}
.cmty-manage-alert-success svg { color: #16a34a; flex-shrink: 0; }
.cmty-manage-alert-error {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #991b1b;
}
.cmty-manage-alert-error svg { color: #dc2626; flex-shrink: 0; }

/* Banner Card */
.cmty-manage-banner-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.cmty-manage-cover {
    height: 176px;
    background: #f1f5f9;
    overflow: hidden;
}
@media (min-width: 640px) {
    .cmty-manage-cover {
        height: 240px;
    }
}
.cmty-manage-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.cmty-manage-header-body {
    padding: 24px 28px;
}
.cmty-manage-header-flex {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}
@media (max-width: 640px) {
    .cmty-manage-header-flex { flex-direction: column; }
}
.cmty-manage-logo-wrap { flex-shrink: 0; }
.cmty-manage-logo {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.cmty-manage-logo-init {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    background: linear-gradient(135deg, #2563eb, #6366f1);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 800;
}
.cmty-manage-info { flex: 1; }
.cmty-manage-badges {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}
.cmty-manage-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
}
.cmty-manage-badge-role { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.cmty-manage-badge-role.cmty-manage-badge-moderator { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
.cmty-manage-badge-cat { background: #f1f5f9; color: #334155; }
.cmty-manage-badge-type { background: #ede9fe; color: #6d28d9; }
.cmty-manage-badge-active { background: #dcfce7; color: #15803d; }
.cmty-manage-badge-inactive { background: #f1f5f9; color: #64748b; }

.cmty-manage-title {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}
.cmty-manage-desc {
    font-size: 14px;
    color: #64748b;
    line-height: 1.55;
    margin: 0 0 16px;
}
.cmty-manage-stats {
    display: flex;
    align-items: center;
    gap: 24px;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
.cmty-stat-item {
    display: flex;
    flex-direction: column;
}
.cmty-stat-value {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
}
.cmty-stat-label {
    font-size: 12px;
    color: #64748b;
}

/* Tabs Bar */
.cmty-tabs-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 6px;
    margin-bottom: 24px;
    overflow-x: auto;
}
.cmty-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    font-size: 13.5px;
    font-weight: 700;
    color: #64748b;
    background: transparent;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cmty-tab-btn:hover {
    color: #0f172a;
    background: #f8fafc;
}
.cmty-tab-btn.active {
    color: #2563eb;
    background: #eff6ff;
}

/* Panes */
.cmty-tab-pane {
    display: none;
}
.cmty-tab-pane.active {
    display: block;
}
.cmty-pane-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.cmty-pane-header {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}
.cmty-pane-header h2 {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}
.cmty-pane-header p {
    font-size: 13.5px;
    color: #64748b;
    margin: 0;
}

/* Form Grid */
.cmty-manage-form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
}
@media (min-width: 640px) {
    .cmty-manage-form-grid { grid-template-columns: 1fr 1fr; }
}
.cmty-form-full { grid-column: 1 / -1; }
.cmty-m-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}
.cmty-m-input, .cmty-m-select, .cmty-m-textarea {
    width: 100%;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: #0f172a;
    font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none;
    box-sizing: border-box;
    transition: all 0.15s ease;
}
.cmty-m-input:focus, .cmty-m-select:focus, .cmty-m-textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
.cmty-m-file {
    font-size: 13px;
    color: #64748b;
    width: 100%;
}
.cmty-m-hint {
    font-size: 11.5px;
    color: #94a3b8;
    margin: 4px 0 0;
}
.cmty-manage-actions {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
}
.cmty-m-submit-btn {
    padding: 11px 26px;
    background: #2563eb;
    color: #ffffff;
    border: 1.5px solid #1d4ed8;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    transition: all 0.15s ease;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.cmty-m-submit-btn:hover {
    background: #1d4ed8;
}

/* Members Table */
.cmty-members-table-wrap {
    overflow-x: auto;
}
.cmty-members-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.cmty-members-table th {
    text-align: left;
    padding: 12px 14px;
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    border-bottom: 1.5px solid #e2e8f0;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.cmty-members-table td {
    padding: 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.cmty-member-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cmty-member-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 13px;
    flex-shrink: 0;
}
.cmty-member-name {
    display: block;
    font-weight: 700;
    color: #0f172a;
}
.cmty-member-email {
    display: block;
    font-size: 12px;
    color: #64748b;
}
.cmty-tag-you {
    font-size: 11px;
    font-weight: 700;
    color: #2563eb;
}
.cmty-role-pill {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
}
.cmty-role-pill.admin { background: #eff6ff; color: #1d4ed8; }
.cmty-role-pill.moderator { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
.cmty-role-pill.member { background: #f1f5f9; color: #475569; }

.cmty-role-select {
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background-color: #ffffff;
    color: #334155;
    cursor: pointer;
    outline: none;
    transition: all 0.15s ease;
}
.cmty-role-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}
.cmty-role-select.role-admin {
    background-color: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}
.cmty-role-select.role-moderator {
    background-color: #ecfeff;
    color: #0891b2;
    border-color: #a5f3fc;
}
.cmty-role-select.role-member {
    background-color: #f8fafc;
    color: #475569;
    border-color: #e2e8f0;
}
.cmty-status-pill {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
}
.cmty-status-pill.active { background: #dcfce7; color: #15803d; }
.cmty-status-pill.banned { background: #fee2e2; color: #b91c1c; }
.cmty-time-pill { font-size: 12.5px; color: #64748b; }

.cmty-action-buttons {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cmty-btn-kick {
    font-size: 12px;
    font-weight: 700;
    color: #dc2626;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 7px;
    padding: 5px 10px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmty-btn-kick:hover {
    background: #dc2626;
    color: #ffffff;
}
.cmty-btn-ban {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    padding: 5px 10px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmty-btn-ban:hover {
    background: #334155;
    color: #ffffff;
}
.cmty-btn-ban.unban {
    color: #16a34a;
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.cmty-btn-ban.unban:hover {
    background: #16a34a;
    color: #ffffff;
}

/* Empty Box Small */
.cmty-empty-box-small {
    padding: 40px 20px;
    text-align: center;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
}

/* Posts List */
.cmty-posts-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.cmty-post-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 20px;
    background: #ffffff;
    transition: all 0.15s ease;
}
.cmty-post-card.is-hidden {
    background: #f8fafc;
    border-color: #cbd5e1;
    opacity: 0.85;
}
.cmty-post-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.cmty-post-author {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cmty-post-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #3b82f6;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
}
.cmty-post-author-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.cmty-post-time {
    font-size: 11.5px;
    color: #94a3b8;
}
.cmty-post-title {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}
.cmty-post-body {
    font-size: 14px;
    color: #475569;
    line-height: 1.6;
    white-space: pre-line;
}
.cmty-post-actions-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.cmty-btn-sm {
    font-size: 12px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmty-btn-sm.warning {
    background: #fef3c7;
    color: #b45309;
}
.cmty-btn-sm.warning:hover {
    background: #fde68a;
}
.cmty-btn-sm.success {
    background: #dcfce7;
    color: #15803d;
}
.cmty-btn-sm.success:hover {
    background: #bbf7d0;
}
.cmty-btn-sm.danger {
    background: #fee2e2;
    color: #b91c1c;
}
.cmty-btn-sm.danger:hover {
    background: #fecaca;
}
</style>

<script>
function showManageTab(tabId, btn) {
    document.querySelectorAll('.cmty-tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.cmty-tab-btn').forEach(el => el.classList.remove('active'));

    const target = document.getElementById('tab-' + tabId);
    if (target) {
        target.classList.add('active');
    }
    if (btn) {
        btn.classList.add('active');
    } else {
        const foundBtn = document.querySelector(`.cmty-tab-btn[onclick*="'${tabId}'"]`);
        if (foundBtn) {
            foundBtn.classList.add('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const hash = window.location.hash ? window.location.hash.replace('#tab-', '').replace('#', '') : null;
    const activeTab = tabParam || hash;
    if (activeTab && document.getElementById('tab-' + activeTab)) {
        showManageTab(activeTab);
    }
});
</script>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI KICK / BAN                                --}}
{{-- ============================================================ --}}
<div id="cmty-confirm-modal" class="cmty-modal-backdrop" style="display:none;" onclick="closeConfirmModal(event)">
    <div class="cmty-modal-box" onclick="event.stopPropagation()">
        <div class="cmty-modal-icon-wrap" id="modal-icon-wrap">
            {{-- icon diisi JS --}}
        </div>
        <h3 class="cmty-modal-title" id="modal-title">Konfirmasi</h3>
        <p class="cmty-modal-desc" id="modal-desc"></p>
        <div class="cmty-modal-actions">
            <button type="button" class="cmty-modal-cancel" onclick="closeConfirmModal()">Batal</button>
            <button type="button" class="cmty-modal-confirm" id="modal-confirm-btn" onclick="submitConfirmedAction()">Konfirmasi</button>
        </div>
    </div>
</div>

<style>
/* ---- CONFIRM MODAL ---- */
.cmty-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(3px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: modalFadeIn 0.18s ease;
}
@keyframes modalFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
.cmty-modal-box {
    background: #ffffff;
    border-radius: 20px;
    padding: 36px 32px 28px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    animation: modalSlideUp 0.2s ease;
}
@keyframes modalSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.cmty-modal-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 28px;
}
.cmty-modal-icon-wrap.kick { background: #fff1f2; }
.cmty-modal-icon-wrap.ban  { background: #fff7ed; }
.cmty-modal-icon-wrap.unban{ background: #f0fdf4; }
.cmty-modal-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}
.cmty-modal-desc {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 28px;
}
.cmty-modal-desc strong {
    color: #0f172a;
    font-weight: 700;
}
.cmty-modal-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.cmty-modal-cancel {
    flex: 1;
    padding: 11px 0;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmty-modal-cancel:hover {
    background: #e2e8f0;
    color: #334155;
}
.cmty-modal-confirm {
    flex: 1;
    padding: 11px 0;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    color: #fff;
}
.cmty-modal-confirm.kick  { background: #dc2626; }
.cmty-modal-confirm.kick:hover  { background: #b91c1c; }
.cmty-modal-confirm.ban   { background: #ea580c; }
.cmty-modal-confirm.ban:hover   { background: #c2410c; }
.cmty-modal-confirm.unban { background: #16a34a; }
.cmty-modal-confirm.unban:hover { background: #15803d; }
</style>

<script>
let _modalAction = null;
let _modalMemberId = null;

function openConfirmModal(type, memberId, memberName, isBanned) {
    _modalAction   = type === 'kick' ? 'kick' : (isBanned ? 'unban' : 'ban');
    _modalMemberId = memberId;

    const iconWrap = document.getElementById('modal-icon-wrap');
    const title    = document.getElementById('modal-title');
    const desc     = document.getElementById('modal-desc');
    const btn      = document.getElementById('modal-confirm-btn');

    iconWrap.className = 'cmty-modal-icon-wrap ' + _modalAction;
    btn.className      = 'cmty-modal-confirm ' + _modalAction;

    const iconKick = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 17l5-5-5-5"/>
        <path d="M21 12H9"/>
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
    </svg>`;

    const iconBan = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
    </svg>`;

    const iconUnban = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        <polyline points="9 12 11 14 15 10"/>
    </svg>`;

    if (_modalAction === 'kick') {
        iconWrap.innerHTML = iconKick;
        title.textContent  = 'Keluarkan Anggota?';
        desc.innerHTML     = `Apakah Anda yakin ingin mengeluarkan <strong>${memberName}</strong> dari komunitas? Mereka dapat bergabung kembali jika komunitas bersifat publik.`;
        btn.textContent    = 'Ya, Keluarkan';
    } else if (_modalAction === 'ban') {
        iconWrap.innerHTML = iconBan;
        title.textContent  = 'Blokir Anggota?';
        desc.innerHTML     = `Apakah Anda yakin ingin memblokir <strong>${memberName}</strong>? Mereka tidak akan bisa bergabung kembali ke komunitas ini.`;
        btn.textContent    = 'Ya, Blokir';
    } else {
        iconWrap.innerHTML = iconUnban;
        title.textContent  = 'Buka Blokir Anggota?';
        desc.innerHTML     = `Apakah Anda yakin ingin membuka blokir <strong>${memberName}</strong>? Mereka akan bisa bergabung kembali ke komunitas ini.`;
        btn.textContent    = 'Ya, Buka Blokir';
    }

    document.getElementById('cmty-confirm-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeConfirmModal(event) {
    if (event && event.target !== document.getElementById('cmty-confirm-modal')) return;
    document.getElementById('cmty-confirm-modal').style.display = 'none';
    document.body.style.overflow = '';
    _modalAction   = null;
    _modalMemberId = null;
}

function submitConfirmedAction() {
    if (! _modalAction || ! _modalMemberId) return;
    const formId = (_modalAction === 'kick' ? 'kick' : 'ban') + '-form-' + _modalMemberId;
    const form   = document.getElementById(formId);
    if (form) { form.submit(); }
    document.getElementById('cmty-confirm-modal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>

@endsection

