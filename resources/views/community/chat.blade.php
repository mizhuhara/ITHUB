@extends('layouts.app')

@section('title', 'Chat Komunitas — ' . $community->name)

@section('hide_navbar')@endsection
@section('hide_footer')@endsection

@section('content')
<div class="chat-page-shell">
    <div class="chat-container">

        {{-- ===== CHAT HEADER ===== --}}
        <div class="chat-header">
            <div class="chat-header-left">
                <a href="{{ route('community.show', $community) }}" class="chat-back-btn" title="Kembali ke Detail Komunitas">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>

                <div class="chat-header-avatar-wrap">
                    @if($community->logo_url)
                        <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="chat-header-avatar">
                    @else
                        <div class="chat-header-avatar-init">
                            {{ strtoupper(substr($community->name, 0, 2)) }}
                        </div>
                    @endif
                    <span class="chat-online-dot" title="Komunitas Aktif"></span>
                </div>

                <div class="chat-header-meta">
                    <div class="chat-header-title-row">
                        <h1 class="chat-header-title">{{ $community->name }}</h1>
                        <span class="chat-badge-type">{{ $community->type === 'private' ? 'Privat' : 'Publik' }}</span>
                        @if($isOwnerOrAdmin)
                            <span class="chat-badge-admin">Admin</span>
                        @endif
                    </div>
                    <p class="chat-header-sub">
                        <span id="chat-sub-members-count">{{ number_format($community->members) }}</span> Anggota &bull; {{ $community->category }}
                    </p>
                </div>
            </div>

            <div class="chat-header-actions">
                {{-- Daftar Anggota Komunitas --}}
                <button type="button" id="btn-toggle-members" class="chat-btn-header-secondary" title="Daftar Anggota Komunitas">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Anggota</span>
                    <span id="chat-header-members-count" class="chat-members-count-pill">{{ number_format($community->members) }}</span>
                </button>
                @if($isOwnerOrAdmin)
                    <a href="{{ route('community.manage', $community) }}" class="chat-btn-header-secondary" title="Kelola Komunitas">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="hidden sm:inline">Kelola</span>
                    </a>
                @endif
                <a href="{{ route('community.show', $community) }}" class="chat-btn-header-secondary" title="Detail Komunitas">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Detail</span>
                </a>
            </div>
        </div>

        {{-- ===== CHAT BODY (MESSAGES) ===== --}}
        <div class="chat-body" id="chat-body">
            {{-- Community Welcome Notice --}}
            <div class="chat-welcome-notice">
                <div class="chat-welcome-icon">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3>Selamat Datang di Chat {{ $community->name }}!</h3>
                <p>Ruang obrolan real-time untuk bertukar ide, berbagi foto, video, dokumen teknis, hingga direktori berkas bersama anggota lainnya.</p>
                @if($community->rules)
                    <div class="chat-welcome-rules">
                        <strong>Tata Tertib:</strong> {{ Str::limit($community->rules, 140) }}
                    </div>
                @endif
            </div>

            {{-- Message Stream Container --}}
            <div id="messages-stream" class="messages-stream">
                {{-- Messages will be dynamically rendered and updated here --}}
            </div>

            <div id="scroll-bottom-anchor"></div>
        </div>

        {{-- ===== ATTACHMENT TRAY (PREVIEW BEFORE SEND) ===== --}}
        <div id="attachment-tray" class="attachment-tray" style="display: none;">
            <div class="attachment-tray-header">
                <span class="attachment-tray-title" id="attachment-tray-title">Lampiran Dipilih</span>
                <button type="button" class="attachment-tray-close" id="btn-clear-attachments" title="Batalkan semua lampiran">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Folder Preview Banner if Folder --}}
            <div id="tray-folder-banner" class="tray-folder-banner" style="display: none;">
                <div class="tray-folder-icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div class="tray-folder-info">
                    <p class="tray-folder-name" id="tray-folder-name">Nama Folder</p>
                    <p class="tray-folder-meta" id="tray-folder-meta">0 file &bull; 0 KB</p>
                </div>
                <button type="button" class="tray-folder-view-btn" id="btn-toggle-folder-list">
                    Lihat Berkas
                </button>
            </div>

            {{-- Collapsible File List inside Tray --}}
            <div id="tray-files-list" class="tray-files-list"></div>
        </div>

        {{-- ===== UPLOAD PROGRESS BAR ===== --}}
        <div id="upload-progress-container" class="upload-progress-container" style="display: none;">
            <div class="upload-progress-header">
                <div class="upload-progress-label">
                    <span class="upload-spinner"></span>
                    <span id="upload-status-text">Mengunggah file...</span>
                </div>
                <div class="upload-progress-percent" id="upload-progress-percent">0%</div>
                <button type="button" class="upload-cancel-btn" id="btn-cancel-upload" title="Batalkan unggahan">Batal</button>
            </div>
            <div class="upload-progress-track">
                <div class="upload-progress-fill" id="upload-progress-fill" style="width: 0%;"></div>
            </div>
        </div>

        {{-- ===== EMOJI PICKER POPUP ===== --}}
        <div id="emoji-picker" class="emoji-picker" style="display: none;">
            <div class="emoji-grid">
                @php
                    $emojis = ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','😉','😍','🥰','😘','😋','😜','🤪','😎','🤓','🧐','🤔','🤝','👍','👏','🙌','🔥','🚀','🎉','❤️','💯','💻','⚡','💡','📚','✨','🎯','📌'];
                @endphp
                @foreach($emojis as $em)
                    <button type="button" class="emoji-item" data-emoji="{{ $em }}">{{ $em }}</button>
                @endforeach
            </div>
        </div>

        {{-- ===== ATTACHMENT MENU POPUP ===== --}}
        <div id="attachment-menu" class="attachment-menu" style="display: none;">
            <button type="button" class="attachment-menu-item" id="menu-item-files">
                <span class="attachment-menu-icon file-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <strong>Kirim File / Dokumen</strong>
                    <p>PDF, DOCX, XLSX, TXT, ZIP, dll.</p>
                </div>
            </button>

            <button type="button" class="attachment-menu-item" id="menu-item-media">
                <span class="attachment-menu-icon media-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <strong>Kirim Foto / Video</strong>
                    <p>PNG, JPG, MP4, WebM dengan player</p>
                </div>
            </button>

            <button type="button" class="attachment-menu-item" id="menu-item-folder">
                <span class="attachment-menu-icon folder-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </span>
                <div>
                    <strong>Kirim Folder</strong>
                    <p>Unggah satu direktori beserta subfoldernya</p>
                </div>
            </button>
        </div>

        {{-- Hidden Real File Inputs --}}
        <input type="file" id="input-files-general" multiple style="display: none;">
        <input type="file" id="input-files-media" accept="image/*,video/*" multiple style="display: none;">
        <input type="file" id="input-files-folder" webkitdirectory directory multiple style="display: none;">

        {{-- ===== CHAT INPUT AREA ===== --}}
        @if($isMember)
            <div class="chat-input-area">
                <div class="chat-input-container">
                    {{-- Attachment Toggle Button --}}
                    <button type="button" class="chat-btn-circle" id="btn-toggle-attach" title="Lampirkan File, Media, atau Folder">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </button>

                    {{-- Emoji Toggle Button --}}
                    <button type="button" class="chat-btn-circle" id="btn-toggle-emoji" title="Pilih Emoji">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>

                    {{-- Textarea input --}}
                    <textarea id="chat-text-input" rows="1" class="chat-textarea" placeholder="Ketik pesan..."></textarea>

                    {{-- Send Button --}}
                    <button type="button" class="chat-btn-send" id="btn-send-message" title="Kirim Pesan">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <div class="chat-join-prompt">
                <p>Anda belum menjadi anggota komunitas ini.</p>
                <form action="{{ route('community.join', $community) }}" method="POST">
                    @csrf
                    <button type="submit" class="chat-join-btn">Gabung Komunitas untuk Mengobrol</button>
                </form>
            </div>
        @endif

    </div>
</div>

{{-- ===== MODAL: PREVIEW FOTO / LIGHTBOX ===== --}}
<div id="modal-image-lightbox" class="chat-modal" style="display: none;">
    <div class="chat-modal-backdrop" onclick="closeImageLightbox()"></div>
    <div class="chat-modal-content image-lightbox-content">
        <button type="button" class="chat-modal-close" onclick="closeImageLightbox()" aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <img id="lightbox-img" src="" alt="Preview Gambar" class="lightbox-img">
        <div class="lightbox-toolbar">
            <span id="lightbox-filename" class="lightbox-filename"></span>
            <a id="lightbox-download-link" href="#" class="lightbox-download-btn" download>
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Asli
            </a>
        </div>
    </div>
</div>

{{-- ===== MODAL: LIHAT ISI FOLDER ===== --}}
<div id="modal-folder-explorer" class="chat-modal" style="display: none;">
    <div class="chat-modal-backdrop" onclick="closeFolderExplorer()"></div>
    <div class="chat-modal-content folder-explorer-content">
        <div class="folder-modal-header">
            <div class="flex items-center gap-3">
                <span class="folder-modal-icon">📁</span>
                <div>
                    <h3 id="folder-modal-title" class="font-bold text-slate-900 text-base">Berkas Folder</h3>
                    <p id="folder-modal-subtitle" class="text-xs text-slate-500">Struktur direktori berkas</p>
                </div>
            </div>
            <button type="button" class="chat-modal-close" onclick="closeFolderExplorer()" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="folder-modal-body">
            <div id="folder-modal-list" class="folder-modal-list"></div>
        </div>
        <div class="folder-modal-footer">
            <a id="folder-modal-download-zip" href="#" class="folder-modal-zip-btn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Seluruh Folder (ZIP)
            </a>
        </div>
    </div>
</div>

{{-- ===== MODAL: DAFTAR ANGGOTA KOMUNITAS ===== --}}
<div id="modal-community-members" class="chat-modal" style="display: none;">
    <div class="chat-modal-backdrop" onclick="closeMembersModal()"></div>
    <div class="chat-modal-content chat-members-modal-content">
        <div class="chat-modal-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 leading-tight">Daftar Anggota</h3>
                    <p class="text-xs text-slate-500"><span id="members-modal-count">{{ number_format($community->members) }}</span> anggota terdaftar</p>
                </div>
            </div>
            <button type="button" class="chat-modal-close" onclick="closeMembersModal()" aria-label="Tutup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Pencarian Anggota --}}
        <div class="chat-members-search-wrap">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="input-search-members" placeholder="Cari nama anggota..." class="chat-members-search-input">
        </div>

        {{-- Daftar Item Anggota --}}
        <div id="chat-members-list" class="chat-members-list">
            {{-- Rendered dynamically via JavaScript --}}
        </div>
    </div>
</div>

{{-- ===== MODAL: KONFIRMASI BAN ANGGOTA ===== --}}
<div id="modal-confirm-kick" class="chat-modal" style="display: none; z-index: 130;">
    <div class="chat-modal-backdrop" onclick="closeKickConfirmModal()"></div>
    <div class="chat-modal-content chat-kick-confirm-content">
        <button type="button" class="chat-modal-close" onclick="closeKickConfirmModal()" aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="chat-kick-icon-wrap" style="background:#fff7ed;">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
            </svg>
        </div>
        <h3 class="chat-kick-title">Blokir Anggota?</h3>
        <p class="chat-kick-desc">
            Apakah Anda yakin ingin memblokir <strong id="kick-confirm-user-name" class="text-slate-900">...</strong> dari komunitas <strong>{{ $community->name }}</strong>?
        </p>
        <div class="chat-kick-warning-box">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Anggota ini akan diblokir dan tidak bisa bergabung kembali ke komunitas ini.</span>
        </div>
        <div class="chat-kick-actions">
            <button type="button" class="chat-btn-cancel-kick" onclick="closeKickConfirmModal()">Batal</button>
            <button type="button" id="btn-confirm-kick" class="chat-btn-confirm-kick" style="background:#ea580c;">Ya, Blokir</button>
        </div>
    </div>
</div>

{{-- ===== MODAL: KONFIRMASI KELUAR KOMUNITAS (DARI CHAT) ===== --}}
<div id="modal-confirm-leave-chat" class="chat-modal" style="display: none; z-index: 130;">
    <div class="chat-modal-backdrop" onclick="closeChatLeaveModal()"></div>
    <div class="chat-modal-content chat-kick-confirm-content">
        <button type="button" class="chat-modal-close" onclick="closeChatLeaveModal()" aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="chat-kick-icon-wrap">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </div>
        <h3 class="chat-kick-title">Keluar dari Komunitas?</h3>
        <p class="chat-kick-desc">
            Apakah Anda yakin ingin keluar dari komunitas <strong>{{ $community->name }}</strong>?
        </p>
        <div class="chat-kick-warning-box">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Anda akan kehilangan akses ke ruang chat dan berkas privat komunitas. Anda dapat bergabung kembali kapan saja sesuai aturan komunitas.</span>
        </div>
        <div class="chat-kick-actions">
            <button type="button" class="chat-btn-cancel-kick" onclick="closeChatLeaveModal()">Batal</button>
            <form action="{{ route('community.leave', $community) }}" method="POST" style="flex: 1.3; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="chat-btn-confirm-kick" style="width: 100%;">Ya, Keluar</button>
            </form>
        </div>
    </div>
</div>

{{-- ===== FLOATING CONTEXT MENU (KLIK KANAN) ===== --}}
<div id="chat-context-menu" class="chat-context-menu" style="display: none;">
    <div class="chat-context-menu-header">
        <div id="ctx-user-avatar-wrap" class="chat-context-avatar-wrap"></div>
        <div class="chat-context-user-info">
            <div id="ctx-user-name" class="chat-context-name"></div>
            <div id="ctx-user-badge" class="chat-member-badge"></div>
        </div>
    </div>
    <div class="chat-context-menu-divider"></div>
    <button type="button" class="chat-context-item" id="ctx-btn-info">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Info Anggota</span>
    </button>
    <button type="button" class="chat-context-item chat-context-item-danger" id="ctx-btn-kick" style="display: none;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
        </svg>
        <span>Blokir dari Grup</span>
    </button>
</div>

{{-- ===== MODAL: INFO ANGGOTA ===== --}}
<div id="modal-member-info" class="chat-modal" style="display: none; z-index: 125;">
    <div class="chat-modal-backdrop" onclick="closeMemberInfoModal()"></div>
    <div class="chat-modal-content chat-member-info-content">
        <button type="button" class="chat-modal-close" onclick="closeMemberInfoModal()" aria-label="Tutup">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="chat-member-info-body">
            <div class="chat-info-avatar-center">
                <div id="info-modal-avatar-wrap" class="chat-info-large-avatar-wrap"></div>
                <h4 id="info-modal-name" class="chat-info-name"></h4>
                <div id="info-modal-badge" class="chat-member-badge"></div>
            </div>

            <div class="chat-info-details-card">
                <div class="chat-info-row">
                    <span class="chat-info-label">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Peran di Komunitas
                    </span>
                    <strong id="info-modal-role" class="chat-info-val"></strong>
                </div>
                <div class="chat-info-row">
                    <span class="chat-info-label">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Komunitas
                    </span>
                    <strong class="chat-info-val">{{ $community->name }}</strong>
                </div>
                <div class="chat-info-row">
                    <span class="chat-info-label">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Bergabung Sejak
                    </span>
                    <span id="info-modal-joined" class="chat-info-val"></span>
                </div>
            </div>

            <div class="chat-info-actions">
                <button type="button" id="info-modal-view-page-btn" class="chat-info-btn-close" style="background: #2563eb; color: #fff; border-color: #2563eb; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Lihat Halaman Lengkap
                </button>
                <button type="button" id="info-modal-kick-btn" class="chat-info-btn-kick" style="display: none;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                    Blokir dari Grup
                </button>
                <button type="button" id="info-modal-leave-btn" class="chat-info-btn-leave" style="display: none;" onclick="openChatLeaveModal()">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar dari Komunitas
                </button>
                <button type="button" class="chat-info-btn-close" onclick="closeMemberInfoModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== TOAST NOTIFICATION ===== --}}
<div id="chat-toast" class="chat-toast" style="display: none;">
    <span id="chat-toast-icon"></span>
    <span id="chat-toast-msg"></span>
</div>

{{-- Pass config data to JavaScript --}}
<script>
    window.COMMUNITY_CHAT = {
        communityId: {{ $community->id }},
        communityName: @json($community->name),
        canSend: {{ $isMember ? 'true' : 'false' }},
        canModerate: {{ $canModerate ? 'true' : 'false' }},
        currentUserId: {{ auth()->id() ?? 0 }},
        endpoints: {
            messages: "{{ route('community.chat.messages', $community) }}",
            send: "{{ route('community.chat.send', $community) }}",
            uploadChunk: "{{ route('community.chat.upload_chunk', $community) }}",
            members: "{{ route('community.chat.members', $community) }}",
            kickMemberBase: "{{ url('/community/' . $community->id . '/members') }}",
            memberInfoBase: "{{ url('/community/' . $community->id . '/members') }}",
        },
        maxFileSize: {{ $maxFileSizeMb * 1048576 }},
        maxTotalSize: {{ $maxTotalSizeMb * 1048576 }},
        initialMessages: @json($initialMessages),
        initialMembers: @json($initialMembers),
    };
</script>

<style>
/* =========================================================
   COMMUNITY CHAT INTERFACE STYLES
   ========================================================= */

.chat-page-shell {
    background: #f1f5f9;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    padding: 16px 12px 24px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
}

.chat-container {
    width: 100%;
    max-width: 980px;
    height: 84vh;
    min-height: 580px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

/* Header */
.chat-header {
    height: 68px;
    border-bottom: 1px solid #e2e8f0;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    flex-shrink: 0;
    z-index: 10;
}
.chat-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}
.chat-back-btn {
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.chat-back-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chat-header-avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.chat-header-avatar {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}
.chat-header-avatar-init {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, #2563eb, #6366f1);
    color: #ffffff;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
.chat-online-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #ffffff;
}
.chat-header-meta {
    min-width: 0;
}
.chat-header-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.chat-header-title {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.chat-badge-type {
    font-size: 11px;
    font-weight: 700;
    color: #6d28d9;
    background: #ede9fe;
    padding: 2px 8px;
    border-radius: 6px;
}
.chat-badge-admin {
    font-size: 11px;
    font-weight: 700;
    color: #1d4ed8;
    background: #eff6ff;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #bfdbfe;
}
.chat-header-sub {
    font-size: 12.5px;
    color: #64748b;
    margin: 2px 0 0;
}
.chat-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.chat-btn-header-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 7px 12px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.chat-btn-header-secondary:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* Chat Body (Scrollable stream) */
.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 16px;
    scroll-behavior: smooth;
}
.chat-welcome-notice {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    text-align: center;
    max-width: 520px;
    margin: 10px auto 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.chat-welcome-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.chat-welcome-notice h3 {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}
.chat-welcome-notice p {
    font-size: 13px;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
}
.chat-welcome-rules {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px dashed #e2e8f0;
    font-size: 12px;
    color: #475569;
}

/* Message rows */
.messages-stream {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.msg-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    max-width: 82%;
}
.msg-row.msg-me {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.msg-row.msg-other {
    align-self: flex-start;
}
.msg-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #3b82f6;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.12s ease;
}
.msg-avatar:hover {
    transform: scale(1.06);
}
.msg-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: inherit;
}
.msg-me .msg-avatar {
    background: #2563eb;
}
.msg-bubble-wrap {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.msg-me .msg-bubble-wrap {
    align-items: flex-end;
}
.msg-other .msg-bubble-wrap {
    align-items: flex-start;
}
.msg-author-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
    font-size: 11.5px;
    color: #64748b;
}
.msg-author-name {
    font-weight: 700;
    color: #334155;
}
.msg-time {
    color: #94a3b8;
}

/* Message Bubble */
.msg-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.55;
    position: relative;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.msg-other .msg-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-top-left-radius: 4px;
}
.msg-me .msg-bubble {
    background: #2563eb;
    color: #ffffff;
    border-top-right-radius: 4px;
}
.msg-me .msg-bubble a { color: #bfdbfe; }
.msg-other .msg-bubble a { color: #2563eb; }

/* Media-only bubble (no text caption) */
.msg-bubble-media-only {
    padding: 0 !important;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
.msg-bubble-media-only .msg-media-grid {
    margin-top: 0 !important;
}

/* Media Grid in Message */
.msg-media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 8px;
    margin-top: 8px;
}
.msg-media-single {
    grid-template-columns: 1fr;
    max-width: 380px;
}
.msg-media-item {
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    background: #000;
    cursor: pointer;
}
.msg-photo-thumb {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    display: block;
    transition: transform 0.2s ease;
}
.msg-photo-thumb:hover {
    transform: scale(1.02);
}
.msg-video-player {
    width: 100%;
    max-height: 280px;
    border-radius: 12px;
    background: #000;
    display: block;
    outline: none;
}

/* Document Cards in Message */
.msg-doc-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 8px;
}
.msg-doc-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    padding: 10px 14px;
    text-decoration: none;
    color: #0f172a;
    transition: all 0.15s ease;
}
.msg-me .msg-doc-card {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.msg-doc-card:hover {
    background: #f1f5f9;
}
.msg-me .msg-doc-card:hover {
    background: rgba(255, 255, 255, 0.25);
}
.msg-doc-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
}
.doc-icon-pdf { background: #fee2e2; color: #dc2626; }
.doc-icon-word { background: #dbeafe; color: #2563eb; }
.doc-icon-sheet { background: #dcfce7; color: #16a34a; }
.doc-icon-zip { background: #fef3c7; color: #d97706; }
.doc-icon-generic { background: #f1f5f9; color: #475569; }

.msg-doc-details {
    flex: 1;
    min-width: 0;
}
.msg-doc-name {
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}
.msg-doc-meta {
    font-size: 11.5px;
    opacity: 0.8;
    margin: 2px 0 0;
}
.msg-doc-download-btn {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: inherit;
    transition: background 0.15s;
}
.msg-doc-download-btn:hover {
    background: rgba(0,0,0,0.12);
}

/* Folder Card in Message */
.msg-folder-card {
    background: rgba(255, 255, 255, 0.98);
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 14px;
    margin-top: 8px;
    color: #0f172a;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.msg-me .msg-folder-card {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.35);
    color: #ffffff;
}
.msg-folder-top {
    display: flex;
    align-items: center;
    gap: 12px;
}
.msg-folder-big-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #fef3c7;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.msg-me .msg-folder-big-icon {
    background: rgba(255, 255, 255, 0.25);
    color: #fef08a;
}
.msg-folder-title {
    font-size: 14px;
    font-weight: 800;
    margin: 0;
}
.msg-folder-stats {
    font-size: 12px;
    opacity: 0.85;
    margin: 2px 0 0;
}
.msg-folder-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px dashed rgba(0,0,0,0.1);
}
.msg-me .msg-folder-actions {
    border-top-color: rgba(255,255,255,0.2);
}
.msg-folder-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all 0.15s ease;
}
.msg-folder-btn.btn-outline {
    background: #f1f5f9;
    color: #1e293b;
}
.msg-me .msg-folder-btn.btn-outline {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}
.msg-folder-btn.btn-zip {
    background: #10b981;
    color: #ffffff;
}
.msg-folder-btn.btn-zip:hover {
    background: #059669;
}

/* Attachment Tray */
.attachment-tray {
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 12px 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 220px;
    overflow-y: auto;
}
.attachment-tray-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.attachment-tray-title {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.attachment-tray-close {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
}
.attachment-tray-close:hover {
    color: #ef4444;
    background: #fee2e2;
}

.tray-folder-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    border: 1.5px solid #fed7aa;
    border-radius: 12px;
    padding: 10px 14px;
}
.tray-folder-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #ffedd5;
    color: #ea580c;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.tray-folder-info { flex: 1; min-width: 0; }
.tray-folder-name { font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0; }
.tray-folder-meta { font-size: 12px; color: #64748b; margin: 2px 0 0; }
.tray-folder-view-btn {
    font-size: 12px;
    font-weight: 700;
    color: #ea580c;
    background: #fff7ed;
    border: 1px solid #fdba74;
    border-radius: 8px;
    padding: 5px 10px;
    cursor: pointer;
}

.tray-files-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.tray-file-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 12px;
    color: #1e293b;
    max-width: 240px;
}
.tray-file-chip-thumb {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    object-fit: cover;
}
.tray-file-chip-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
}
.tray-file-chip-size {
    color: #94a3b8;
    font-size: 11px;
}
.tray-file-chip-remove {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px;
    font-size: 14px;
    line-height: 1;
}
.tray-file-chip-remove:hover {
    color: #ef4444;
}

/* Upload Progress Bar */
.upload-progress-container {
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 12px 20px;
}
.upload-progress-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12.5px;
    margin-bottom: 6px;
}
.upload-progress-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #1e293b;
}
.upload-spinner {
    width: 14px;
    height: 14px;
    border: 2px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.upload-progress-percent {
    font-weight: 700;
    color: #2563eb;
}
.upload-cancel-btn {
    background: none;
    border: none;
    color: #ef4444;
    font-weight: 700;
    font-size: 12px;
    cursor: pointer;
    margin-left: 12px;
}
.upload-progress-track {
    height: 6px;
    background: #f1f5f9;
    border-radius: 999px;
    overflow: hidden;
}
.upload-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
    border-radius: 999px;
    transition: width 0.2s ease;
}

/* Chat Input Bar */
.chat-input-area {
    padding: 14px 18px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
.chat-input-container {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 28px;
    padding: 6px 8px 6px 14px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.chat-input-container:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    background: #ffffff;
}
.chat-btn-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.chat-btn-circle:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.chat-btn-circle.active {
    color: #2563eb;
    background: #eff6ff;
}
.chat-textarea {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    resize: none;
    font-size: 14px;
    font-family: inherit;
    color: #0f172a;
    max-height: 120px;
    min-height: 24px;
    line-height: 1.5;
    padding: 4px 0;
    box-sizing: border-box;
}
.chat-textarea::placeholder {
    color: #94a3b8;
}
.chat-btn-send {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.chat-btn-send:hover {
    background: #1d4ed8;
    transform: scale(1.05);
}
.chat-btn-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Join Prompt if not member */
.chat-join-prompt {
    padding: 16px 20px;
    background: #eff6ff;
    border-top: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    font-size: 13.5px;
    color: #1e40af;
}
.chat-join-btn {
    background: #2563eb;
    color: #ffffff;
    border: none;
    padding: 8px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.15s;
}
.chat-join-btn:hover {
    background: #1d4ed8;
}

/* Attachment Menu Popup */
.attachment-menu {
    position: absolute;
    bottom: 74px;
    left: 20px;
    width: 290px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    display: flex;
    flex-direction: column;
    gap: 4px;
    z-index: 50;
    animation: popUp 0.15s ease;
}
@keyframes popUp {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.attachment-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    background: transparent;
    border: none;
    text-align: left;
    cursor: pointer;
    transition: background 0.12s;
    width: 100%;
}
.attachment-menu-item:hover {
    background: #f1f5f9;
}
.attachment-menu-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.attachment-menu-icon.file-icon { background: #eff6ff; color: #2563eb; }
.attachment-menu-icon.media-icon { background: #fdf2f8; color: #db2777; }
.attachment-menu-icon.folder-icon { background: #fff7ed; color: #ea580c; }
.attachment-menu-item strong {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}
.attachment-menu-item p {
    font-size: 11px;
    color: #64748b;
    margin: 2px 0 0;
}

/* Emoji Picker Popup */
.emoji-picker {
    position: absolute;
    bottom: 74px;
    left: 64px;
    width: 280px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    z-index: 50;
    animation: popUp 0.15s ease;
}
.emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
}
.emoji-item {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    transition: transform 0.12s, background 0.12s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.emoji-item:hover {
    background: #f1f5f9;
    transform: scale(1.2);
}

/* Modals */
.chat-modal {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.chat-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(4px);
}
.chat-modal-content {
    position: relative;
    z-index: 101;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    max-width: 90vw;
    max-height: 90vh;
    overflow: hidden;
    animation: modalIn 0.2s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.chat-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 14px;
    border-bottom: 1px solid #f1f5f9;
    position: relative;
}
.chat-modal-close {
    position: absolute;
    top: 14px;
    right: 16px;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 102;
    transition: all 0.15s ease;
    padding: 0;
}
.chat-modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: scale(1.05);
}
.image-lightbox-content .chat-modal-close {
    background: rgba(0, 0, 0, 0.65);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.image-lightbox-content .chat-modal-close:hover {
    background: rgba(0, 0, 0, 0.9);
    color: #ffffff;
}

/* Lightbox Content */
.image-lightbox-content {
    background: #000000;
    display: flex;
    flex-direction: column;
}
.lightbox-img {
    max-width: 85vw;
    max-height: 78vh;
    object-fit: contain;
}
.lightbox-toolbar {
    padding: 12px 18px;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #ffffff;
    font-size: 13px;
}
.lightbox-filename {
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 60%;
}
.lightbox-download-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #2563eb;
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}
.lightbox-download-btn:hover { background: #1d4ed8; }

/* Folder Explorer Modal */
.folder-explorer-content {
    width: 580px;
    display: flex;
    flex-direction: column;
    max-height: 80vh;
}
.folder-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.folder-modal-icon { font-size: 24px; }
.folder-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px 20px;
}
.folder-modal-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.folder-entry-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 12.5px;
}
.folder-entry-name {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #0f172a;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 70%;
}
.folder-entry-size { color: #94a3b8; font-size: 11px; }
.folder-entry-dl {
    color: #2563eb;
    text-decoration: none;
    font-weight: 700;
    font-size: 11.5px;
}
.folder-entry-dl:hover { text-decoration: underline; }
.folder-modal-footer {
    padding: 14px 20px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
}
.folder-modal-zip-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #10b981;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    padding: 9px 18px;
    border-radius: 10px;
    text-decoration: none;
    transition: background 0.15s;
}
.folder-modal-zip-btn:hover { background: #059669; }

/* Members Header Pill */
.chat-members-count-pill {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-size: 11px;
    font-weight: 700;
    padding: 1px 7px;
    border-radius: 999px;
    margin-left: 2px;
}

/* Members Modal Content */
.chat-members-modal-content {
    width: 440px;
    max-width: 92vw;
    background: #ffffff;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    max-height: 82vh;
}
.chat-members-search-wrap {
    padding: 12px 18px;
    border-bottom: 1px solid #f1f5f9;
    position: relative;
    display: flex;
    align-items: center;
}
.chat-members-search-input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-family: inherit;
    color: #0f172a;
    outline: none;
    transition: all 0.15s ease;
}
.chat-members-search-input:focus {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
.chat-members-search-wrap svg {
    position: absolute;
    left: 30px;
    pointer-events: none;
}
.chat-members-list {
    flex: 1;
    overflow-y: auto;
    padding: 8px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-height: 420px;
}
.chat-member-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border-radius: 12px;
    transition: background 0.15s ease, opacity 0.25s ease, transform 0.25s ease;
    position: relative;
}
.chat-member-item:hover {
    background: #f8fafc;
}
.chat-member-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1;
}
.chat-member-avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.chat-member-avatar {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}
.chat-member-avatar-init {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chat-member-info {
    min-width: 0;
    flex: 1;
}
.chat-member-name-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.chat-member-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.chat-member-badge {
    font-size: 10.5px;
    font-weight: 700;
    padding: 1.5px 6.5px;
    border-radius: 6px;
    line-height: 1.2;
    text-transform: capitalize;
}
.chat-member-badge.badge-owner {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #d8b4fe;
}
.chat-member-badge.badge-admin {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.chat-member-badge.badge-moderator {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.chat-member-badge.badge-member {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.chat-member-action-wrap {
    position: relative;
    flex-shrink: 0;
    display: flex;
    align-items: center;
}
.chat-member-dots-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.chat-member-dots-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.chat-member-dropdown {
    position: absolute;
    right: calc(100% + 9px);
    top: 50%;
    transform: translateY(-50%);
    background: #ffffff;
    border: 1.5px solid #fecaca;
    border-radius: 10px;
    box-shadow: 0 4px 18px rgba(220, 38, 38, 0.14), 0 2px 6px rgba(0, 0, 0, 0.04);
    padding: 3px 5px;
    z-index: 120;
    white-space: nowrap;
    animation: sidePopIn 0.14s cubic-bezier(0.16, 1, 0.3, 1);
}
.chat-member-dropdown::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -5px;
    transform: translateY(-50%);
    border-width: 5px 0 5px 5px;
    border-style: solid;
    border-color: transparent transparent transparent #ffffff;
    display: block;
    width: 0;
}
.chat-member-dropdown::before {
    content: '';
    position: absolute;
    top: 50%;
    right: -7px;
    transform: translateY(-50%);
    border-width: 6px 0 6px 6px;
    border-style: solid;
    border-color: transparent transparent transparent #fecaca;
    display: block;
    width: 0;
}
@keyframes sidePopIn {
    from { opacity: 0; transform: translateY(-50%) translateX(6px); }
    to { opacity: 1; transform: translateY(-50%) translateX(0); }
}
.chat-member-kick-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    border: none;
    background: #fef2f2;
    border-radius: 7px;
    color: #dc2626;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    line-height: 1.2;
    font-family: inherit;
}
.chat-member-kick-btn:hover {
    background: #dc2626;
    color: #ffffff;
}

/* Kick & Leave Confirm Modal */
.chat-kick-confirm-content {
    width: 410px;
    max-width: 92vw;
    background: #ffffff;
    border-radius: 24px;
    padding: 26px 24px 22px;
    text-align: center;
    position: relative;
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25);
    border: 1px solid rgba(226, 232, 240, 0.9);
}
.chat-kick-icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}
.chat-kick-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
}
.chat-kick-desc {
    font-size: 13.5px;
    color: #475569;
    line-height: 1.5;
    margin: 0 0 16px;
}
.chat-kick-warning-box {
    background: #fff5f5;
    border: 1px solid #fee2e2;
    color: #991b1b;
    font-size: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    margin: 0 0 20px;
    text-align: left;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    line-height: 1.45;
}
.chat-kick-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
.chat-btn-cancel-kick {
    flex: 1;
    padding: 11px 16px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}
.chat-btn-cancel-kick:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.chat-btn-confirm-kick {
    flex: 1.3;
    padding: 11px 16px;
    border-radius: 12px;
    background: #dc2626;
    color: #ffffff;
    border: 1px solid #b91c1c;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    transition: all 0.15s ease;
    font-family: inherit;
}
.chat-btn-confirm-kick:hover {
    background: #b91c1c;
}
.chat-btn-confirm-kick:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Toast */
.chat-toast {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: #0f172a;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.22);
    font-size: 13px;
    font-weight: 600;
    z-index: 200;
    pointer-events: none;
    animation: toastIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes toastIn {
    from { opacity: 0; transform: translate(-50%, 14px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}
.chat-toast.toast-error {
    background: #dc2626;
}
.chat-toast.toast-success {
    background: #059669;
}

/* =====================================================
   CONTEXT MENU (KLIK KANAN / LONG PRESS)
   ===================================================== */
.chat-context-menu {
    position: fixed;
    z-index: 160;
    width: 220px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.18), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    padding: 6px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    animation: ctxPopIn 0.12s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes ctxPopIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.chat-context-menu-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 8px 8px;
}
.chat-context-avatar-wrap {
    flex-shrink: 0;
}
.chat-context-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}
.chat-context-avatar-init {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #6366f1);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chat-context-user-info {
    min-width: 0;
    flex: 1;
}
.chat-context-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 2px;
}
.chat-context-menu-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 4px 4px 6px;
}
.chat-context-item {
    display: flex;
    align-items: center;
    gap: 9px;
    width: 100%;
    padding: 8px 10px;
    border: none;
    background: transparent;
    border-radius: 8px;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.12s, color 0.12s;
    text-align: left;
    font-family: inherit;
}
.chat-context-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chat-context-item-danger {
    color: #dc2626;
}
.chat-context-item-danger:hover {
    background: #fef2f2;
    color: #b91c1c;
}

/* =====================================================
   MODAL INFO ANGGOTA
   ===================================================== */
.chat-member-info-content {
    width: 360px;
    max-width: 90vw;
    background: #ffffff;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.04);
    animation: modalPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.chat-member-info-body {
    padding: 24px 20px 20px;
}
.chat-info-avatar-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-top: 4px;
    margin-bottom: 16px;
}
.chat-info-large-avatar-wrap {
    margin-bottom: 10px;
}
.chat-info-large-avatar {
    width: 68px;
    height: 68px;
    border-radius: 18px;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}
.chat-info-large-avatar-init {
    width: 68px;
    height: 68px;
    border-radius: 18px;
    background: linear-gradient(135deg, #2563eb, #6366f1);
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.28);
}
.chat-info-name {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.3;
}
.chat-info-details-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 16px;
}
.chat-info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    gap: 10px;
}
.chat-info-label {
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 7px;
}
.chat-info-val {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
    font-size: 13px;
}
.chat-info-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.chat-info-btn-kick {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}
.chat-info-btn-kick:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}
.chat-info-btn-leave {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}
.chat-info-btn-leave:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}
.chat-info-btn-close {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}
.chat-info-btn-close:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Responsive adjustments */
/* =====================================================
   RESPONSIVE — TABLET (641px – 768px)
   ===================================================== */
@media (min-width: 641px) and (max-width: 768px) {
    .chat-page-shell { padding: 8px; }
    .chat-container { height: 90vh; border-radius: 14px; }
    .chat-header { padding: 0 16px; }
    .chat-body { padding: 16px; }
    .msg-row { max-width: 88%; }
    .attachment-menu { left: 12px; width: 270px; }
    .emoji-picker { left: 56px; width: 260px; }
}

/* =====================================================
   RESPONSIVE — MOBILE (≤ 640px)
   ===================================================== */
@media (max-width: 640px) {

    /* Shell & Container */
    .chat-page-shell {
        padding: 0;
        min-height: 100dvh;
    }
    .chat-container {
        height: 100dvh;
        border-radius: 0;
        border: none;
        box-shadow: none;
        max-width: 100%;
    }

    /* Header */
    .chat-header {
        height: auto;
        min-height: 58px;
        padding: 8px 12px;
    }
    .chat-header-left {
        gap: 8px;
        flex: 1;
        min-width: 0;
    }
    .chat-back-btn {
        width: 32px;
        height: 32px;
    }
    .chat-header-avatar,
    .chat-header-avatar-init {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 12px;
    }
    .chat-header-title {
        font-size: 14px;
        max-width: 160px;
    }
    .chat-header-sub {
        font-size: 11px;
    }
    .chat-badge-type,
    .chat-badge-admin {
        display: none; /* hide badges on very small screens */
    }
    .chat-btn-header-secondary {
        padding: 6px 8px;
        font-size: 12px;
    }

    /* Body */
    .chat-body {
        padding: 12px 10px;
        gap: 12px;
    }
    .chat-welcome-notice {
        padding: 16px;
        max-width: 100%;
    }
    .chat-welcome-icon {
        width: 40px;
        height: 40px;
    }
    .chat-welcome-notice h3 {
        font-size: 14px;
    }
    .chat-welcome-notice p {
        font-size: 12px;
    }

    /* Messages */
    .msg-row {
        max-width: 92%;
    }
    .msg-avatar {
        width: 28px;
        height: 28px;
        font-size: 10px;
        border-radius: 8px;
    }
    .msg-bubble {
        padding: 9px 12px;
        font-size: 13px;
        border-radius: 14px;
    }
    .msg-author-row {
        font-size: 10.5px;
        gap: 4px;
    }

    /* Photo / Video in bubble */
    .msg-photo-thumb,
    .msg-video-player {
        max-width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 12px;
    }
    .msg-media-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 4px;
    }
    .msg-media-grid.msg-media-single {
        grid-template-columns: 1fr !important;
        max-width: 260px;
    }

    /* File / Folder bubble */
    .msg-file-card,
    .msg-folder-card {
        padding: 10px 12px;
        gap: 10px;
    }
    .msg-file-icon,
    .msg-folder-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }
    .msg-folder-actions {
        flex-wrap: wrap;
        gap: 6px;
    }
    .msg-folder-btn {
        font-size: 11px;
        padding: 5px 10px;
    }

    /* Attachment Tray */
    .attachment-tray {
        padding: 10px 12px;
        max-height: 180px;
    }
    .tray-files-list {
        gap: 6px;
    }
    .tray-file-chip {
        padding: 6px 8px;
        border-radius: 8px;
    }
    .tray-file-chip-thumb {
        width: 28px;
        height: 28px;
    }
    .tray-file-chip-name {
        font-size: 12px;
    }
    .tray-folder-banner {
        padding: 8px 10px;
        gap: 8px;
    }
    .tray-folder-icon {
        width: 32px;
        height: 32px;
    }
    .tray-folder-name {
        font-size: 12.5px;
    }

    /* Upload progress */
    .upload-progress-container {
        padding: 10px 14px;
    }

    /* Input Area */
    .chat-input-area {
        padding: 10px 12px;
    }
    .chat-input-container {
        padding: 5px 6px 5px 12px;
        border-radius: 24px;
        gap: 6px;
    }
    .chat-textarea {
        font-size: 13px;
        min-height: 22px;
    }
    .chat-btn-circle {
        width: 32px;
        height: 32px;
    }
    .chat-btn-send {
        width: 34px;
        height: 34px;
    }

    /* Attachment Menu Popup */
    .attachment-menu {
        left: 10px;
        right: 10px;
        width: auto;
        bottom: 70px;
    }

    /* Emoji Picker Popup */
    .emoji-picker {
        left: 10px;
        right: 10px;
        width: auto;
        bottom: 70px;
    }
    .emoji-grid {
        grid-template-columns: repeat(7, 1fr);
    }
    .emoji-item {
        font-size: 18px;
        padding: 4px;
    }

    /* Modals */
    .chat-modal {
        padding: 0;
        align-items: flex-end;
    }
    .chat-modal-content {
        border-radius: 20px 20px 0 0;
        max-height: 92dvh;
        max-width: 100vw;
        width: 100%;
    }
    .chat-modal-header {
        padding: 16px 16px 12px;
    }
    .chat-modal-body {
        padding: 0 16px 16px;
    }
    .chat-modal-footer {
        padding: 12px 16px;
        gap: 8px;
    }
    .chat-members-modal-content {
        max-width: 100vw;
        width: 100%;
        border-radius: 20px 20px 0 0;
        max-height: 85dvh;
    }
    .chat-kick-confirm-content {
        margin: auto;
        border-radius: 24px;
        max-width: calc(100vw - 32px);
        width: 380px;
        padding: 24px 20px;
    }
    .chat-member-info-content {
        max-width: 100vw;
        width: 100%;
        border-radius: 24px 24px 0 0;
    }
}
</style>

{{-- =========================================================
     CLIENT-SIDE CHAT ENGINE (Vanilla JS - Realtime & Uploads)
     ========================================================= --}}
<script>
const messagesMap = new Map();
let currentCommunityMembers = (window.COMMUNITY_CHAT && window.COMMUNITY_CHAT.initialMembers) || [];
let currentContextUser = null;

document.addEventListener('DOMContentLoaded', function () {
    const config = window.COMMUNITY_CHAT;
    if (config && config.initialMembers) {
        currentCommunityMembers = config.initialMembers;
    }
    let selectedFiles = []; // Array of { file, relativePath, folderName, previewUrl }
    let lastMessageId = 0;
    let isUploading = false;
    let activeXhr = null;
    let pollInterval = null;

    // DOM Elements
    const messagesStream = document.getElementById('messages-stream');
    const chatBody = document.getElementById('chat-body');
    const textInput = document.getElementById('chat-text-input');
    const btnSend = document.getElementById('btn-send-message');

    // Attachments
    const btnToggleAttach = document.getElementById('btn-toggle-attach');
    const attachMenu = document.getElementById('attachment-menu');
    const inputFilesGeneral = document.getElementById('input-files-general');
    const inputFilesMedia = document.getElementById('input-files-media');
    const inputFilesFolder = document.getElementById('input-files-folder');

    // Tray
    const attachmentTray = document.getElementById('attachment-tray');
    const trayFilesList = document.getElementById('tray-files-list');
    const trayFolderBanner = document.getElementById('tray-folder-banner');
    const trayFolderName = document.getElementById('tray-folder-name');
    const trayFolderMeta = document.getElementById('tray-folder-meta');
    const btnClearAttachments = document.getElementById('btn-clear-attachments');

    // Progress
    const uploadProgressContainer = document.getElementById('upload-progress-container');
    const uploadProgressFill = document.getElementById('upload-progress-fill');
    const uploadProgressPercent = document.getElementById('upload-progress-percent');
    const uploadStatusText = document.getElementById('upload-status-text');
    const btnCancelUpload = document.getElementById('btn-cancel-upload');

    // Emoji
    const btnToggleEmoji = document.getElementById('btn-toggle-emoji');
    const emojiPicker = document.getElementById('emoji-picker');

    // --- 1. RENDER INITIAL MESSAGES ---
    if (config.initialMessages && config.initialMessages.length > 0) {
        config.initialMessages.forEach(msg => appendMessageDOM(msg, false));
        lastMessageId = config.initialMessages[config.initialMessages.length - 1].id;
        scrollToBottom();
    }

    // --- 2. START REAL-TIME POLLING ---
    startPolling();

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(fetchNewMessages, 2500);
        window.chatPollInterval = pollInterval;
    }

    function fetchNewMessages() {
        if (isUploading) return; // Don't interrupt during upload

        fetch(`${config.endpoints.messages}?after_id=${lastMessageId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            if (res.status === 403) {
                handleUserKicked('Akses ke obrolan ini telah dicabut karena Anda bukan lagi anggota komunitas.');
                return null;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            if (data.is_member === false) {
                handleUserKicked('Anda telah dikeluarkan dari komunitas ini.');
                return;
            }
            if (data.members_count !== undefined) {
                updateMemberCount(data.members_count);
            }
            if (data.success && data.messages && data.messages.length > 0) {
                const wasNearBottom = isScrollNearBottom();
                data.messages.forEach(msg => {
                    // Check if message is already in DOM
                    if (!document.getElementById(`msg-${msg.id}`)) {
                        appendMessageDOM(msg, true);
                        if (msg.id > lastMessageId) {
                            lastMessageId = msg.id;
                        }
                    }
                });
                if (wasNearBottom) {
                    scrollToBottom();
                }
            }
        })
        .catch(err => {
            // Silently retry on next poll cycle
            console.debug('Polling check...', err);
        });
    }

    // --- 3. DOM MESSAGE BUILDER ---
    function appendMessageDOM(msg, isNew) {
        messagesMap.set(Number(msg.id), msg);
        messagesMap.set(String(msg.id), msg);

        const row = document.createElement('div');
        row.id = `msg-${msg.id}`;
        row.className = `msg-row ${msg.is_me ? 'msg-me' : 'msg-other'}`;
        row.setAttribute('data-msg-id', msg.id);
        row.setAttribute('data-user-id', msg.user_id);

        let innerHTML = '';

        // Avatar (Photo or Initials)
        let avatarContent = '';
        if (msg.user_avatar_url) {
            avatarContent = `<img src="${escapeHtml(msg.user_avatar_url)}" alt="${escapeHtml(msg.user_name)}" class="msg-avatar-img">`;
        } else {
            avatarContent = escapeHtml(msg.user_initials || 'U');
        }
        innerHTML += `<div class="msg-avatar" data-user-id="${msg.user_id}" title="Klik atau klik kanan untuk info">${avatarContent}</div>`;

        // Bubble Wrap
        innerHTML += `<div class="msg-bubble-wrap">`;

        // Author meta (for other users)
        if (!msg.is_me) {
            innerHTML += `
                <div class="msg-author-row">
                    <span class="msg-author-name">${escapeHtml(msg.user_name)}</span>
                    <span class="msg-time">${escapeHtml(msg.time_formatted || '')}</span>
                </div>
            `;
        }

        // Message Bubble
        const hasOnlyMedia = (!msg.message || msg.message.trim() === '') &&
                             msg.attachments && msg.attachments.length > 0 &&
                             msg.attachments.every(a => a.is_image || a.is_video);

        innerHTML += `<div class="msg-bubble ${hasOnlyMedia ? 'msg-bubble-media-only' : ''}">`;

        // Text Content
        if (msg.message) {
            innerHTML += `<div class="msg-text">${formatMessageText(msg.message)}</div>`;
        }

        // Attachments
        if (msg.attachments && msg.attachments.length > 0) {
            if (msg.folder_summary) {
                // Folder Representation
                innerHTML += renderFolderCard(msg);
            } else {
                // Media or Documents
                const photosAndVideos = msg.attachments.filter(a => a.is_image || a.is_video);
                const docs = msg.attachments.filter(a => !a.is_image && !a.is_video);

                if (photosAndVideos.length > 0) {
                    innerHTML += renderMediaGrid(photosAndVideos);
                }

                if (docs.length > 0) {
                    innerHTML += renderDocList(docs);
                }
            }
        }

        // End Bubble & Wrap
        innerHTML += `</div>`; // .msg-bubble

        if (msg.is_me) {
            innerHTML += `<span class="msg-time text-[10.5px] text-slate-400 mt-1">${escapeHtml(msg.time_formatted || '')}</span>`;
        }

        innerHTML += `</div>`; // .msg-bubble-wrap

        row.innerHTML = innerHTML;
        messagesStream.appendChild(row);
    }

    function renderMediaGrid(items) {
        let isSingle = items.length === 1;
        let html = `<div class="msg-media-grid ${isSingle ? 'msg-media-single' : ''}">`;

        items.forEach(item => {
            if (item.is_image) {
                html += `
                    <div class="msg-media-item" onclick="openImageLightbox('${escapeHtml(item.preview_url)}', '${escapeHtml(item.file_name)}', '${escapeHtml(item.download_url)}')">
                        <img src="${escapeHtml(item.preview_url)}" alt="${escapeHtml(item.file_name)}" class="msg-photo-thumb" loading="lazy">
                    </div>
                `;
            } else if (item.is_video) {
                html += `
                    <div class="msg-media-item">
                        <video controls playsinline preload="metadata" class="msg-video-player">
                            <source src="${escapeHtml(item.preview_url)}" type="${escapeHtml(item.mime_type || 'video/mp4')}">
                            Browser Anda tidak mendukung tag video.
                        </video>
                    </div>
                `;
            }
        });

        html += `</div>`;
        return html;
    }

    function renderDocList(docs) {
        let html = `<div class="msg-doc-list">`;
        docs.forEach(doc => {
            let iconClass = 'doc-icon-generic';
            let iconLabel = doc.extension || 'DOC';
            if (doc.extension === 'pdf') { iconClass = 'doc-icon-pdf'; iconLabel = 'PDF'; }
            else if (['doc', 'docx'].includes(doc.extension)) { iconClass = 'doc-icon-word'; iconLabel = 'DOC'; }
            else if (['xls', 'xlsx', 'csv'].includes(doc.extension)) { iconClass = 'doc-icon-sheet'; iconLabel = 'XLS'; }
            else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(doc.extension)) { iconClass = 'doc-icon-zip'; iconLabel = 'ZIP'; }

            html += `
                <a href="${escapeHtml(doc.download_url)}" class="msg-doc-card" download>
                    <div class="msg-doc-icon ${iconClass}">${escapeHtml(iconLabel)}</div>
                    <div class="msg-doc-details">
                        <p class="msg-doc-name" title="${escapeHtml(doc.file_name)}">${escapeHtml(doc.file_name)}</p>
                        <p class="msg-doc-meta">${escapeHtml(doc.formatted_size)}</p>
                    </div>
                    <div class="msg-doc-download-btn">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                </a>
            `;
        });
        html += `</div>`;
        return html;
    }

    function renderFolderCard(msg) {
        const s = msg.folder_summary;
        const jsonAtts = encodeURIComponent(JSON.stringify(msg.attachments));

        return `
            <div class="msg-folder-card">
                <div class="msg-folder-top">
                    <div class="msg-folder-big-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="msg-folder-title">${escapeHtml(s.folder_name)}</h4>
                        <p class="msg-folder-stats">${escapeHtml(s.files_count)} berkas &bull; ${escapeHtml(s.formatted_total_size)}</p>
                    </div>
                </div>
                <div class="msg-folder-actions">
                    <button type="button" class="msg-folder-btn btn-outline" onclick="openFolderExplorer('${escapeHtml(s.folder_name)}', '${jsonAtts}', '${escapeHtml(s.download_zip_url)}')">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Isi Folder
                    </button>
                    <a href="${escapeHtml(s.download_zip_url)}" class="msg-folder-btn btn-zip" download>
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh ZIP
                    </a>
                </div>
            </div>
        `;
    }

    // --- 4. ATTACHMENT MENU & FILE SELECTION ---
    if (btnToggleAttach) {
        btnToggleAttach.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = attachMenu.style.display !== 'none';
            closeAllPopups();
            if (!isOpen) {
                attachMenu.style.display = 'flex';
                btnToggleAttach.classList.add('active');
            }
        });
    }

    document.getElementById('menu-item-files')?.addEventListener('click', function () {
        closeAllPopups();
        inputFilesGeneral.click();
    });

    document.getElementById('menu-item-media')?.addEventListener('click', function () {
        closeAllPopups();
        inputFilesMedia.click();
    });

    document.getElementById('menu-item-folder')?.addEventListener('click', function () {
        closeAllPopups();
        inputFilesFolder.click();
    });

    inputFilesGeneral?.addEventListener('change', handleFilePickerSelection);
    inputFilesMedia?.addEventListener('change', handleFilePickerSelection);
    inputFilesFolder?.addEventListener('change', handleFolderPickerSelection);

    function handleFilePickerSelection(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        files.forEach(f => {
            selectedFiles.push({
                file: f,
                relativePath: null,
                folderName: null,
            });
        });

        e.target.value = '';
        renderAttachmentTray();
    }

    function handleFolderPickerSelection(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        // Extract folder name from the first file's webkitRelativePath
        let rootFolderName = 'Folder_Unggahan';
        if (files[0].webkitRelativePath) {
            const parts = files[0].webkitRelativePath.split('/');
            if (parts.length > 1) {
                rootFolderName = parts[0];
            }
        }

        files.forEach(f => {
            selectedFiles.push({
                file: f,
                relativePath: f.webkitRelativePath || f.name,
                folderName: rootFolderName,
            });
        });

        e.target.value = '';
        renderAttachmentTray();
    }

    // --- 5. RENDER ATTACHMENT TRAY ---
    function renderAttachmentTray() {
        if (selectedFiles.length === 0) {
            attachmentTray.style.display = 'none';
            return;
        }

        attachmentTray.style.display = 'flex';
        trayFilesList.innerHTML = '';

        // Detect if this selection is a folder upload
        const hasFolder = selectedFiles.some(f => f.folderName);
        if (hasFolder) {
            const folderName = selectedFiles.find(f => f.folderName)?.folderName || 'Folder Berkas';
            const totalBytes = selectedFiles.reduce((acc, f) => acc + f.file.size, 0);

            trayFolderBanner.style.display = 'flex';
            trayFolderName.textContent = folderName;
            trayFolderMeta.textContent = `${selectedFiles.length} file dipilih \u2022 ${formatBytes(totalBytes)}`;
        } else {
            trayFolderBanner.style.display = 'none';
        }

        // Render chips
        selectedFiles.forEach((item, index) => {
            const chip = document.createElement('div');
            chip.className = 'tray-file-chip';

            const isImg = item.file.type.startsWith('image/');
            let thumbHTML = '';
            if (isImg) {
                const objectUrl = URL.createObjectURL(item.file);
                thumbHTML = `<img src="${objectUrl}" class="tray-file-chip-thumb">`;
            } else {
                thumbHTML = `<span>📄</span>`;
            }

            const displayName = item.relativePath || item.file.name;

            chip.innerHTML = `
                ${thumbHTML}
                <div class="min-w-0">
                    <div class="tray-file-chip-name" title="${escapeHtml(displayName)}">${escapeHtml(displayName)}</div>
                    <span class="tray-file-chip-size">${formatBytes(item.file.size)}</span>
                </div>
                <button type="button" class="tray-file-chip-remove" data-index="${index}">&times;</button>
            `;
            trayFilesList.appendChild(chip);
        });

        // Bind remove buttons
        document.querySelectorAll('.tray-file-chip-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                selectedFiles.splice(idx, 1);
                renderAttachmentTray();
            });
        });
    }

    btnClearAttachments?.addEventListener('click', function () {
        selectedFiles = [];
        renderAttachmentTray();
    });

    document.getElementById('btn-toggle-folder-list')?.addEventListener('click', function () {
        const isHidden = trayFilesList.style.display === 'none';
        trayFilesList.style.display = isHidden ? 'flex' : 'none';
        this.textContent = isHidden ? 'Tutup Berkas' : 'Lihat Berkas';
    });

    // --- 6. SEND MESSAGE (WITH PROGRESS & CHUNKED OPTION) ---
    btnSend?.addEventListener('click', sendMessage);

    textInput?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        if (isUploading) return;

        const text = textInput.value.trim();
        if (text === '' && selectedFiles.length === 0) {
            return;
        }

        // Check total upload size limit
        const totalBytes = selectedFiles.reduce((acc, f) => acc + f.file.size, 0);
        if (totalBytes > config.maxTotalSize) {
            alert(`Total ukuran file (${formatBytes(totalBytes)}) melebihi batas maksimal ${formatBytes(config.maxTotalSize)}.`);
            return;
        }

        isUploading = true;
        btnSend.disabled = true;

        if (selectedFiles.length > 0) {
            uploadProgressContainer.style.display = 'block';
            updateUploadProgress(0, 'Menyiapkan berkas...');
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', text);

        const hasFolder = selectedFiles.some(f => f.folderName);
        if (hasFolder) {
            formData.append('folder_name', selectedFiles.find(f => f.folderName)?.folderName || 'Folder');
            formData.append('is_folder', '1');
        }

        selectedFiles.forEach((item, index) => {
            formData.append('files[]', item.file, item.file.name);
            if (item.relativePath) {
                formData.append('relative_paths[]', item.relativePath);
            }
        });

        const xhr = new XMLHttpRequest();
        activeXhr = xhr;

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                updateUploadProgress(percent, `Mengunggah berkas... ${percent}%`);
            }
        };

        xhr.onload = function () {
            isUploading = false;
            activeXhr = null;
            btnSend.disabled = false;
            uploadProgressContainer.style.display = 'none';

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success && res.message) {
                        appendMessageDOM(res.message, true);
                        if (res.message.id > lastMessageId) {
                            lastMessageId = res.message.id;
                        }
                        scrollToBottom();
                    }
                } catch (ex) {
                    console.error('Invalid response format', ex);
                }

                // Reset form
                textInput.value = '';
                selectedFiles = [];
                renderAttachmentTray();
            } else {
                if (xhr.status === 403) {
                    handleUserKicked('Anda tidak dapat mengirim pesan karena telah dikeluarkan dari komunitas ini.');
                    return;
                }
                let errorMsg = 'Terjadi kesalahan saat mengirim pesan.';
                try {
                    const errRes = JSON.parse(xhr.responseText);
                    if (errRes.error) errorMsg = errRes.error;
                } catch (_) {}
                showToast(errorMsg, 'error');
            }
        };

        xhr.onerror = function () {
            isUploading = false;
            activeXhr = null;
            btnSend.disabled = false;
            uploadProgressContainer.style.display = 'none';
            showToast('Gagal mengunggah berkas. Periksa koneksi internet Anda.', 'error');
        };

        xhr.open('POST', config.endpoints.send, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    }

    btnCancelUpload?.addEventListener('click', function () {
        if (activeXhr) {
            activeXhr.abort();
            activeXhr = null;
            isUploading = false;
            btnSend.disabled = false;
            uploadProgressContainer.style.display = 'none';
            updateUploadProgress(0, 'Unggahan dibatalkan');
        }
    });

    function updateUploadProgress(percent, label) {
        uploadProgressFill.style.width = `${percent}%`;
        uploadProgressPercent.textContent = `${percent}%`;
        if (label) uploadStatusText.textContent = label;
    }

    // --- 7. EMOJI PICKER ---
    if (btnToggleEmoji) {
        btnToggleEmoji.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = emojiPicker.style.display !== 'none';
            closeAllPopups();
            if (!isOpen) {
                emojiPicker.style.display = 'block';
                btnToggleEmoji.classList.add('active');
            }
        });
    }

    document.querySelectorAll('.emoji-item').forEach(btn => {
        btn.addEventListener('click', function () {
            const emoji = this.getAttribute('data-emoji');
            insertAtCursor(textInput, emoji);
            textInput.focus();
        });
    });

    function closeAllPopups() {
        if (attachMenu) attachMenu.style.display = 'none';
        if (emojiPicker) emojiPicker.style.display = 'none';
        if (btnToggleAttach) btnToggleAttach.classList.remove('active');
        if (btnToggleEmoji) btnToggleEmoji.classList.remove('active');
    }

    document.addEventListener('click', function (e) {
        if (!attachMenu?.contains(e.target) && !btnToggleAttach?.contains(e.target) &&
            !emojiPicker?.contains(e.target) && !btnToggleEmoji?.contains(e.target)) {
            closeAllPopups();
        }
        if (!e.target.closest('.chat-member-action-wrap')) {
            document.querySelectorAll('.chat-member-dropdown').forEach(d => d.style.display = 'none');
        }
        const ctxMenu = document.getElementById('chat-context-menu');
        if (ctxMenu && ctxMenu.style.display !== 'none' && !ctxMenu.contains(e.target)) {
            hideContextMenu();
        }
    });

    // --- 8. HELPERS ---
    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function isScrollNearBottom() {
        return chatBody.scrollHeight - chatBody.scrollTop - chatBody.clientHeight < 120;
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatMessageText(text) {
        if (!text) return '';
        const escaped = escapeHtml(text);
        // Convert URLs to clickable links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        const withLinks = escaped.replace(urlRegex, function (url) {
            return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
        });
        return withLinks.replace(/\n/g, '<br>');
    }

    function insertAtCursor(myField, myValue) {
        if (document.selection) {
            myField.focus();
            const sel = document.selection.createRange();
            sel.text = myValue;
        } else if (myField.selectionStart || myField.selectionStart === 0) {
            const startPos = myField.selectionStart;
            const endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)
                + myValue
                + myField.value.substring(endPos, myField.value.length);
            myField.selectionStart = startPos + myValue.length;
            myField.selectionEnd = startPos + myValue.length;
        } else {
            myField.value += myValue;
        }
    }

    // --- 9. MEMBERS & MODERATION (KICK) LOGIC ---
    let communityMembers = config.initialMembers || [];
    const membersListContainer = document.getElementById('chat-members-list');
    const searchMembersInput = document.getElementById('input-search-members');
    const btnToggleMembers = document.getElementById('btn-toggle-members');
    const btnConfirmKick = document.getElementById('btn-confirm-kick');

    // Render initial members
    renderMembersList(communityMembers);

    if (btnToggleMembers) {
        btnToggleMembers.addEventListener('click', function () {
            openMembersModal();
            // Refresh members list via JSON API
            fetch(config.endpoints.members, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.members) {
                    communityMembers = data.members;
                    currentCommunityMembers = data.members;
                    renderMembersList(communityMembers);
                    if (data.total !== undefined) {
                        updateMemberCount(data.total);
                    }
                }
            })
            .catch(err => console.debug('Members refresh...', err));
        });
    }

    if (searchMembersInput) {
        searchMembersInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = document.querySelectorAll('.chat-member-item');
            let visibleCount = 0;
            items.forEach(el => {
                const name = el.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    el.style.display = 'flex';
                    visibleCount++;
                } else {
                    el.style.display = 'none';
                }
            });

            let noResult = document.getElementById('chat-members-no-result');
            if (visibleCount === 0) {
                if (!noResult) {
                    noResult = document.createElement('div');
                    noResult.id = 'chat-members-no-result';
                    noResult.style.cssText = 'text-align: center; padding: 24px 12px; color: #94a3b8; font-size: 13px;';
                    noResult.textContent = 'Tidak ada anggota dengan nama tersebut.';
                    membersListContainer.appendChild(noResult);
                }
            } else if (noResult) {
                noResult.remove();
            }
        });
    }

    if (btnConfirmKick) {
        btnConfirmKick.addEventListener('click', function () {
            if (!pendingKickUserId) return;
            const targetId = pendingKickUserId;
            const targetName = pendingKickUserName;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            btnConfirmKick.disabled = true;
            btnConfirmKick.textContent = 'Memblokir...';

            fetch(`${config.endpoints.kickMemberBase}/${targetId}/toggle-ban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(({ ok, data }) => {
                btnConfirmKick.disabled = false;
                btnConfirmKick.textContent = 'Ya, Blokir';
                closeKickConfirmModal();

                if (ok && data.success) {
                    showToast(data.message || `Pengguna ${targetName} berhasil diblokir.`, 'success');

                    // Smoothly remove item from DOM
                    const itemEl = document.getElementById(`member-item-${targetId}`);
                    if (itemEl) {
                        itemEl.style.opacity = '0';
                        itemEl.style.transform = 'translateX(20px)';
                        setTimeout(() => itemEl.remove(), 250);
                    }

                    // Update internal list
                    communityMembers = communityMembers.filter(m => m.id !== targetId);
                    currentCommunityMembers = communityMembers;

                    // Update count
                    if (data.members_count !== undefined) {
                        updateMemberCount(data.members_count);
                    } else {
                        updateMemberCount(communityMembers.length);
                    }
                } else {
                    showToast(data.message || 'Gagal memblokir anggota.', 'error');
                }
            })
            .catch(err => {
                btnConfirmKick.disabled = false;
                btnConfirmKick.textContent = 'Ya, Blokir';
                closeKickConfirmModal();
                showToast('Terjadi kesalahan saat memproses permintaan.', 'error');
                console.error('Ban error:', err);
            });
        });
    }

    function renderMembersList(members) {
        if (!membersListContainer) return;
        if (!members || members.length === 0) {
            membersListContainer.innerHTML = `
                <div style="text-align: center; padding: 28px 12px; color: #94a3b8; font-size: 13px;">
                    Belum ada anggota terdaftar.
                </div>
            `;
            return;
        }

        membersListContainer.innerHTML = '';
        members.forEach(member => {
            const item = document.createElement('div');
            item.className = 'chat-member-item';
            item.id = `member-item-${member.id}`;
            item.setAttribute('data-name', (member.name || '').toLowerCase());

            const avatarHtml = member.avatar_url
                ? `<img src="${escapeHtml(member.avatar_url)}" alt="${escapeHtml(member.name)}" class="chat-member-avatar">`
                : `<div class="chat-member-avatar-init">${escapeHtml(member.initials || 'U')}</div>`;

            let badgeClass = 'badge-member';
            if (member.is_owner) badgeClass = 'badge-owner';
            else if (member.role === 'admin') badgeClass = 'badge-admin';
            else if (member.role === 'moderator') badgeClass = 'badge-moderator';

            let actionHtml = '';
            if (member.can_be_kicked) {
                const safeName = escapeHtml(member.name).replace(/'/g, "\\'");
                actionHtml = `
                    <div class="chat-member-action-wrap">
                        <button type="button" class="chat-member-dots-btn" onclick="toggleMemberDropdown(event, ${member.id})" title="Pilihan Anggota" aria-label="Opsi untuk ${escapeHtml(member.name)}">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>
                        <div id="member-dropdown-${member.id}" class="chat-member-dropdown" style="display: none;">
                            <button type="button" class="chat-member-kick-btn" onclick="openKickConfirmModal(${member.id}, '${safeName}')">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluarkan Anggota
                            </button>
                        </div>
                    </div>
                `;
            }

            item.innerHTML = `
                <div class="chat-member-left" style="cursor: pointer;" onclick="openMemberInfoModalById(${member.id})" title="Lihat info ${escapeHtml(member.name)}">
                    <div class="chat-member-avatar-wrap">
                        ${avatarHtml}
                    </div>
                    <div class="chat-member-info">
                        <div class="chat-member-name-row">
                            <span class="chat-member-name">${escapeHtml(member.name)}</span>
                            <span class="chat-member-badge ${badgeClass}">${escapeHtml(member.role_label || 'Anggota')}</span>
                        </div>
                    </div>
                </div>
                ${actionHtml}
            `;

            // Right-click on member in modal list opens context menu
            item.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                e.stopPropagation();
                showContextMenu(e.clientX, e.clientY, member);
            });

            membersListContainer.appendChild(item);
        });
    }

    // --- CONTEXT MENU BUTTON ACTIONS ---
    const ctxBtnInfo = document.getElementById('ctx-btn-info');
    if (ctxBtnInfo) {
        ctxBtnInfo.addEventListener('click', function (e) {
            e.stopPropagation();
            hideContextMenu();
            if (currentContextUser && currentContextUser.id) {
                navigateToMemberInfo(currentContextUser.id);
            }
        });
    }

    const ctxBtnKick = document.getElementById('ctx-btn-kick');
    if (ctxBtnKick) {
        ctxBtnKick.addEventListener('click', function (e) {
            e.stopPropagation();
            hideContextMenu();
            if (currentContextUser) {
                openKickConfirmModal(currentContextUser.id, currentContextUser.name);
            }
        });
    }

    // --- CHAT BODY INTERACTION (RIGHT-CLICK, LEFT-CLICK AVATAR, LONG-PRESS) ---
    // 1. Right-click on message row or avatar
    chatBody.addEventListener('contextmenu', function (e) {
        const row = e.target.closest('.msg-row');
        if (!row) return;

        e.preventDefault();
        e.stopPropagation();

        const msgId = row.getAttribute('data-msg-id');
        const msg = messagesMap.get(Number(msgId)) || messagesMap.get(String(msgId));
        if (!msg) return;

        const userData = getUserDataForMessage(msg);
        showContextMenu(e.clientX, e.clientY, userData);
    });

    // 2. Left-click on message avatar opens Member Info page directly
    chatBody.addEventListener('click', function (e) {
        const avatar = e.target.closest('.msg-avatar');
        if (avatar) {
            const row = avatar.closest('.msg-row');
            if (row) {
                const msgId = row.getAttribute('data-msg-id');
                const msg = messagesMap.get(Number(msgId)) || messagesMap.get(String(msgId));
                if (msg) {
                    const userData = getUserDataForMessage(msg);
                    if (userData && userData.id) {
                        navigateToMemberInfo(userData.id);
                    }
                }
            }
        }
    });

    // 3. Touch long-press (480ms) for mobile devices
    let touchTimer = null;
    let touchStartX = 0;
    let touchStartY = 0;

    chatBody.addEventListener('touchstart', function (e) {
        const row = e.target.closest('.msg-row');
        if (!row) return;

        const touch = e.touches[0];
        touchStartX = touch.clientX;
        touchStartY = touch.clientY;

        touchTimer = setTimeout(() => {
            const msgId = row.getAttribute('data-msg-id');
            const msg = messagesMap.get(Number(msgId)) || messagesMap.get(String(msgId));
            if (!msg) return;

            const userData = getUserDataForMessage(msg);
            showContextMenu(touch.clientX, touch.clientY, userData);
        }, 480);
    }, { passive: true });

    chatBody.addEventListener('touchmove', function (e) {
        if (touchTimer) {
            const touch = e.touches[0];
            if (Math.abs(touch.clientX - touchStartX) > 10 || Math.abs(touch.clientY - touchStartY) > 10) {
                clearTimeout(touchTimer);
                touchTimer = null;
            }
        }
    }, { passive: true });

    chatBody.addEventListener('touchend', function () {
        if (touchTimer) {
            clearTimeout(touchTimer);
            touchTimer = null;
        }
    });

    chatBody.addEventListener('scroll', function () {
        hideContextMenu();
    }, { passive: true });
});

// --- GLOBAL KICK & MEMBERS MODAL FUNCTIONS ---
let pendingKickUserId = null;
let pendingKickUserName = '';
let toastTimer = null;

function openMembersModal() {
    const modal = document.getElementById('modal-community-members');
    if (modal) {
        modal.style.display = 'flex';
        const search = document.getElementById('input-search-members');
        if (search) {
            search.value = '';
            search.focus();
            const items = document.querySelectorAll('.chat-member-item');
            items.forEach(el => el.style.display = 'flex');
        }
    }
}

function closeMembersModal() {
    const modal = document.getElementById('modal-community-members');
    if (modal) modal.style.display = 'none';
    document.querySelectorAll('.chat-member-dropdown').forEach(d => d.style.display = 'none');
}

function toggleMemberDropdown(event, memberId) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    const targetDropdown = document.getElementById(`member-dropdown-${memberId}`);
    const isCurrentlyOpen = targetDropdown && targetDropdown.style.display !== 'none';
    document.querySelectorAll('.chat-member-dropdown').forEach(d => d.style.display = 'none');
    if (targetDropdown && !isCurrentlyOpen) {
        targetDropdown.style.display = 'block';
    }
}

function openKickConfirmModal(userId, userName) {
    pendingKickUserId = userId;
    pendingKickUserName = userName;
    document.querySelectorAll('.chat-member-dropdown').forEach(d => d.style.display = 'none');
    const nameEl = document.getElementById('kick-confirm-user-name');
    if (nameEl) nameEl.textContent = userName;
    const modal = document.getElementById('modal-confirm-kick');
    if (modal) modal.style.display = 'flex';
}

function closeKickConfirmModal() {
    pendingKickUserId = null;
    pendingKickUserName = '';
    const modal = document.getElementById('modal-confirm-kick');
    if (modal) modal.style.display = 'none';
}

function openChatLeaveModal() {
    closeMemberInfoModal();
    const modal = document.getElementById('modal-confirm-leave-chat');
    if (modal) modal.style.display = 'flex';
}

function closeChatLeaveModal() {
    const modal = document.getElementById('modal-confirm-leave-chat');
    if (modal) modal.style.display = 'none';
}

function getUserDataForMessage(msg) {
    if (!msg) return null;
    const member = currentCommunityMembers.find(m => Number(m.id) === Number(msg.user_id));
    if (member) {
        return {
            id: member.id,
            name: member.name,
            initials: member.initials || msg.user_initials || 'U',
            avatar_url: member.avatar_url || msg.user_avatar_url,
            role: member.role || msg.user_role,
            role_label: member.role_label || msg.user_role_label || 'Anggota',
            is_owner: !!member.is_owner,
            can_be_kicked: !!member.can_be_kicked,
            joined_at: member.joined_at || msg.user_joined_at || '-',
            email: member.email || msg.user_email || ''
        };
    }
    return {
        id: msg.user_id,
        name: msg.user_name || 'Pengguna',
        initials: msg.user_initials || 'U',
        avatar_url: msg.user_avatar_url,
        role: msg.user_role || 'member',
        role_label: msg.user_role_label || 'Anggota',
        is_owner: msg.user_role === 'owner' || msg.user_role_label === 'Pemilik',
        can_be_kicked: !!msg.can_be_kicked,
        joined_at: msg.user_joined_at || '-',
        email: msg.user_email || ''
    };
}

function navigateToMemberInfo(userId) {
    if (!userId) return;
    const base = (window.COMMUNITY_CHAT && window.COMMUNITY_CHAT.endpoints && window.COMMUNITY_CHAT.endpoints.memberInfoBase)
        || `/community/{{ $community->id }}/members`;
    window.location.href = `${base}/${userId}`;
}

function openMemberInfoModalById(memberId) {
    navigateToMemberInfo(memberId);
}

function showContextMenu(x, y, userData) {
    if (!userData) return;
    currentContextUser = userData;

    const menu = document.getElementById('chat-context-menu');
    if (!menu) return;

    // Set avatar
    const avatarWrap = document.getElementById('ctx-user-avatar-wrap');
    if (avatarWrap) {
        if (userData.avatar_url) {
            avatarWrap.innerHTML = `<img src="${escapeHtml(userData.avatar_url)}" alt="${escapeHtml(userData.name)}" class="chat-context-avatar">`;
        } else {
            avatarWrap.innerHTML = `<div class="chat-context-avatar-init">${escapeHtml(userData.initials || 'U')}</div>`;
        }
    }

    // Set user name
    const nameEl = document.getElementById('ctx-user-name');
    if (nameEl) nameEl.textContent = userData.name;

    // Set role badge
    const badgeEl = document.getElementById('ctx-user-badge');
    if (badgeEl) {
        let badgeClass = 'badge-member';
        if (userData.is_owner) badgeClass = 'badge-owner';
        else if (userData.role === 'admin') badgeClass = 'badge-admin';
        else if (userData.role === 'moderator') badgeClass = 'badge-moderator';
        badgeEl.className = `chat-member-badge ${badgeClass}`;
        badgeEl.textContent = userData.role_label || 'Anggota';
    }

    // Show/hide Kick button based on permission
    const kickBtn = document.getElementById('ctx-btn-kick');
    if (kickBtn) {
        if (userData.can_be_kicked) {
            kickBtn.style.display = 'flex';
        } else {
            kickBtn.style.display = 'none';
        }
    }

    // Display menu and calculate position within viewport bounds
    menu.style.display = 'block';
    const menuWidth = 220;
    const menuHeight = kickBtn && userData.can_be_kicked ? 140 : 100;
    const maxX = window.innerWidth - menuWidth - 14;
    const maxY = window.innerHeight - menuHeight - 14;
    const posX = Math.max(14, Math.min(x, maxX));
    const posY = Math.max(14, Math.min(y, maxY));
    menu.style.left = `${posX}px`;
    menu.style.top = `${posY}px`;
}

function hideContextMenu() {
    const menu = document.getElementById('chat-context-menu');
    if (menu) menu.style.display = 'none';
}

function openMemberInfoModal(userData) {
    if (!userData) return;
    const modal = document.getElementById('modal-member-info');
    if (!modal) return;

    // Avatar
    const avatarWrap = document.getElementById('info-modal-avatar-wrap');
    if (avatarWrap) {
        if (userData.avatar_url) {
            avatarWrap.innerHTML = `<img src="${escapeHtml(userData.avatar_url)}" alt="${escapeHtml(userData.name)}" class="chat-info-large-avatar">`;
        } else {
            avatarWrap.innerHTML = `<div class="chat-info-large-avatar-init">${escapeHtml(userData.initials || 'U')}</div>`;
        }
    }

    // Name
    const nameEl = document.getElementById('info-modal-name');
    if (nameEl) nameEl.textContent = userData.name;

    // Badge
    const badgeEl = document.getElementById('info-modal-badge');
    if (badgeEl) {
        let badgeClass = 'badge-member';
        if (userData.is_owner) badgeClass = 'badge-owner';
        else if (userData.role === 'admin') badgeClass = 'badge-admin';
        else if (userData.role === 'moderator') badgeClass = 'badge-moderator';
        badgeEl.className = `chat-member-badge ${badgeClass}`;
        badgeEl.textContent = userData.role_label || 'Anggota';
    }

    // Details
    const roleEl = document.getElementById('info-modal-role');
    if (roleEl) roleEl.textContent = userData.role_label || 'Anggota';

    const joinedEl = document.getElementById('info-modal-joined');
    if (joinedEl) joinedEl.textContent = userData.joined_at || '-';

    // Kick button inside Info Modal
    const kickBtn = document.getElementById('info-modal-kick-btn');
    if (kickBtn) {
        if (userData.can_be_kicked) {
            kickBtn.style.display = 'flex';
            kickBtn.onclick = function () {
                closeMemberInfoModal();
                openKickConfirmModal(userData.id, userData.name);
            };
        } else {
            kickBtn.style.display = 'none';
            kickBtn.onclick = null;
        }
    }

    // Leave button inside Info Modal (for oneself if not owner)
    const leaveBtn = document.getElementById('info-modal-leave-btn');
    if (leaveBtn) {
        if (Number(userData.id) === Number(currentUserId) && !userData.is_owner) {
            leaveBtn.style.display = 'flex';
        } else {
            leaveBtn.style.display = 'none';
        }
    }

    // View Full Page button inside Info Modal
    const viewPageBtn = document.getElementById('info-modal-view-page-btn');
    if (viewPageBtn) {
        viewPageBtn.onclick = function () {
            closeMemberInfoModal();
            navigateToMemberInfo(userData.id);
        };
    }

    modal.style.display = 'flex';
}

function closeMemberInfoModal() {
    const modal = document.getElementById('modal-member-info');
    if (modal) modal.style.display = 'none';
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('chat-toast');
    const toastMsg = document.getElementById('chat-toast-msg');
    const toastIcon = document.getElementById('chat-toast-icon');
    if (!toast || !toastMsg) return;

    if (toastTimer) clearTimeout(toastTimer);

    toastMsg.textContent = message;
    toast.className = `chat-toast toast-${type}`;
    if (toastIcon) {
        toastIcon.textContent = type === 'success' ? '✓' : '⚠️';
    }
    toast.style.display = 'flex';

    toastTimer = setTimeout(() => {
        toast.style.display = 'none';
    }, 3200);
}

function updateMemberCount(count) {
    const formatted = new Intl.NumberFormat('id-ID').format(count);
    const badge = document.getElementById('chat-header-members-count');
    const sub = document.getElementById('chat-sub-members-count');
    const modalCount = document.getElementById('members-modal-count');
    if (badge) badge.textContent = formatted;
    if (sub) sub.textContent = formatted;
    if (modalCount) modalCount.textContent = formatted;
}

function handleUserKicked(message) {
    if (window.chatPollInterval) clearInterval(window.chatPollInterval);
    const inputArea = document.querySelector('.chat-input-area');
    if (inputArea) {
        inputArea.innerHTML = `
            <div class="chat-join-prompt" style="padding: 16px 20px; text-align: center; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; margin: 12px;">
                <p style="color: #be123c; font-weight: 700; margin-bottom: 6px; font-size: 14px;">${escapeHtml(message)}</p>
                <p style="font-size: 12px; color: #9f1239; margin-bottom: 12px;">Akses Anda ke ruang chat telah dicabut oleh pengelola komunitas.</p>
                <a href="{{ route('community.show', $community) }}" class="chat-join-btn" style="display: inline-block; padding: 7px 16px; text-decoration: none; border-radius: 8px;">Kembali ke Halaman Komunitas</a>
            </div>
        `;
    }
    showToast(message, 'error');
}

// --- GLOBAL MODAL FUNCTIONS ---
function openImageLightbox(previewUrl, fileName, downloadUrl) {
    const modal = document.getElementById('modal-image-lightbox');
    const img = document.getElementById('lightbox-img');
    const title = document.getElementById('lightbox-filename');
    const dl = document.getElementById('lightbox-download-link');

    if (modal && img) {
        img.src = previewUrl;
        title.textContent = fileName;
        dl.href = downloadUrl;
        modal.style.display = 'flex';
    }
}

function closeImageLightbox() {
    const modal = document.getElementById('modal-image-lightbox');
    const img = document.getElementById('lightbox-img');
    if (modal) {
        modal.style.display = 'none';
        if (img) img.src = '';
    }
}

function openFolderExplorer(folderName, encodedAtts, downloadZipUrl) {
    const modal = document.getElementById('modal-folder-explorer');
    const title = document.getElementById('folder-modal-title');
    const subtitle = document.getElementById('folder-modal-subtitle');
    const list = document.getElementById('folder-modal-list');
    const zipBtn = document.getElementById('folder-modal-download-zip');

    if (modal && list) {
        title.textContent = folderName;
        zipBtn.href = downloadZipUrl;

        let items = [];
        try {
            items = JSON.parse(decodeURIComponent(encodedAtts));
        } catch (e) {
            console.error('Failed to parse folder attachments', e);
        }

        subtitle.textContent = `${items.length} berkas di dalam direktori`;
        list.innerHTML = '';

        items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'folder-entry-row';
            const displayPath = item.relative_path || item.file_name;

            row.innerHTML = `
                <div class="folder-entry-name" title="${escapeHtml(displayPath)}">
                    <span>📄</span>
                    <span>${escapeHtml(displayPath)}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="folder-entry-size">${escapeHtml(item.formatted_size)}</span>
                    <a href="${escapeHtml(item.download_url)}" class="folder-entry-dl" download>Unduh</a>
                </div>
            `;
            list.appendChild(row);
        });

        modal.style.display = 'flex';
    }
}

function closeFolderExplorer() {
    const modal = document.getElementById('modal-folder-explorer');
    if (modal) modal.style.display = 'none';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeMembersModal();
        closeMemberInfoModal();
        closeKickConfirmModal();
        closeChatLeaveModal();
        closeImageLightbox();
        closeFolderExplorer();
        hideContextMenu();
    }
});
</script>

@endsection

