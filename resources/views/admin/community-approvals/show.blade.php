@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan Komunitas — IndoTech Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.community-approvals.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-brand-600 transition">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Approval
        </a>

        {{-- Status Badge --}}
        <div>
            @if($community->approval_status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Persetujuan
                </span>
            @elseif($community->approval_status === 'approved')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Disetujui (Approved)
                </span>
            @elseif($community->approval_status === 'rejected')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Ditolak (Rejected)
                </span>
            @endif
        </div>
    </div>

    {{-- Rejection Box if Rejected --}}
    @if($community->approval_status === 'rejected')
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1.5 shadow-xs">
            <div class="flex items-center gap-2 font-bold text-sm text-rose-900">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Alasan Penolakan:</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed pl-6">{{ $community->rejection_reason }}</p>
            <div class="text-[11px] text-rose-500 pl-6 pt-1">
                Ditolak pada {{ $community->rejected_at?->format('d M Y, H:i') }} WIB {{ $community->rejecter ? 'oleh ' . $community->rejecter->name : '' }}
            </div>
        </div>
    @endif

    {{-- Catatan Perbaikan dari Pembuat Komunitas --}}
    @if($community->resubmit_notes)
        <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-1.5 shadow-xs">
            <div class="flex items-center gap-2 font-bold text-sm text-amber-900">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Catatan Perbaikan dari Pembuat Komunitas:</span>
            </div>
            <p class="text-xs text-amber-800 leading-relaxed pl-6 font-medium">{{ $community->resubmit_notes }}</p>
        </div>
    @endif

    {{-- Main Community Card --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Banner & Logo --}}
        <div class="h-44 w-full bg-slate-200 overflow-hidden relative">
            @if($community->cover_url)
                <img src="{{ $community->cover_url }}" alt="Banner {{ $community->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center text-white/70 font-semibold text-sm">
                    IndoTech Community
                </div>
            @endif
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 -mt-16 sm:-mt-20">
                <div class="flex items-end gap-4">
                    <div class="w-20 h-20 rounded-2xl border-4 border-white shadow-lg bg-white overflow-hidden shrink-0 flex items-center justify-center">
                        @if($community->logo_url)
                            <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-brand-600 text-white font-bold text-xl flex items-center justify-center">
                                {{ strtoupper(substr($community->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $community->name }}</h1>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold text-xs border border-blue-100">
                                {{ $community->category }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                {{ $community->type === 'private' ? 'Privat' : 'Publik' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-xs border border-emerald-100">
                                {{ $community->formatted_mode }}
                            </span>
                        </div>
                        @if(!empty($community->tags))
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($community->tags as $tag)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[11px] border border-slate-200">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 self-stretch sm:self-auto">
                    @if($community->approval_status === 'pending')
                        <form method="POST" action="{{ route('admin.community-approvals.reject', $community) }}" class="flex-1 sm:flex-none">
                            @csrf
                            <button type="button" onclick="openRejectModal()" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-600 hover:text-white border border-rose-200 transition">
                                Tolak
                            </button>
                        </form>
                    @endif

                    @if($community->approval_status === 'pending')
                        <form method="POST" action="{{ route('admin.community-approvals.approve', $community) }}" class="flex-1 sm:flex-none">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                                Setujui Komunitas
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Metadata Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-200/80 text-xs">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Pembuat (Creator)</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $community->creator?->name ?? 'Sistem / Admin' }}</div>
                    <div class="text-slate-500">{{ $community->creator?->email ?? '-' }}</div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pengajuan</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $community->created_at->format('d M Y, H:i') }} WIB</div>
                    <div class="text-slate-500">Terdaftar {{ $community->created_at->diffForHumans() }}</div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Bahasa Utama</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $community->language }}</div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Deskripsi Komunitas</h3>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 leading-relaxed text-slate-700 text-xs whitespace-pre-line">
                    {{ $community->description ?: 'Belum ada deskripsi.' }}
                </div>
            </div>

            {{-- Aturan Komunitas --}}
            @if($community->rules)
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Aturan &amp; Tata Tertib Komunitas</h3>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 leading-relaxed text-slate-700 text-xs whitespace-pre-line">
                        {{ $community->rules }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject Modal with reason --}}
<div id="modal-reject-single" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">Tolak Pengajuan Komunitas</h3>
        <p class="text-xs text-slate-500 leading-relaxed">
            Berikan alasan penolakan yang jelas agar pemilik komunitas dapat memperbaikinya.
        </p>

        <form method="POST" action="{{ route('admin.community-approvals.reject', $community) }}" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason_single" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea id="rejection_reason_single" 
                          name="rejection_reason" 
                          rows="4" 
                          required 
                          minlength="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('modal-reject-single').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('modal-reject-single').classList.add('hidden');
    }
</script>
@endsection

