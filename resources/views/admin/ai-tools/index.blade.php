@extends('admin.layouts.app')

@section('title', 'Kelola AI Info — Admin')
@section('header_title', 'Kelola AI Info')
@section('header_subtitle', 'Kelola daftar rekomendasi dan direktori alat kecerdasan buatan (AI) dari berbagai bidang.')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">

    {{-- Top Action & Filter Row --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        
        {{-- Search & Filter Form --}}
        <form method="GET" action="{{ route('admin.ai-tools.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama AI, kategori, atau tagline..."
                       class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 bg-white text-[13.5px] text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>

            @if(!empty($categories))
                <select name="category" onchange="this.form.submit()" class="h-10 px-3 rounded-xl border border-slate-200 bg-white text-[13px] text-slate-700 outline-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold transition">
                Filter
            </button>
            @if(request()->hasAny(['q', 'category']))
                <a href="{{ route('admin.ai-tools.index') }}" class="h-10 px-3 flex items-center text-rose-600 hover:text-rose-700 text-[13px] font-semibold">
                    Reset
                </a>
            @endif
        </form>

        {{-- Add AI Button --}}
        <div class="shrink-0">
            <a href="{{ route('admin.ai-tools.create') }}"
               class="inline-flex items-center justify-center gap-2 h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] shadow-sm transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah AI Baru</span>
            </a>
        </div>
    </div>

    {{-- Flash Success Message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13.5px] font-medium shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[12px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">Nama & Info AI</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Lisensi / Biaya</th>
                        <th class="py-3.5 px-4">URL Resmi</th>
                        <th class="py-3.5 px-4 text-right sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse($tools as $tool)
                        <tr class="hover:bg-slate-50/50 transition">
                            {{-- Name & Icon --}}
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center shrink-0 overflow-hidden p-1 shadow-2xs">
                                        @if($tool->logo_url)
                                            <img src="{{ $tool->logo_url }}" alt="{{ $tool->name }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="text-xs font-bold text-blue-600 uppercase">{{ substr($tool->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-tight truncate">
                                            {{ $tool->name }}
                                        </div>
                                        <div class="text-[12px] text-slate-500 truncate max-w-xs mt-0.5">
                                            {{ $tool->tagline ?: Str::limit($tool->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11.5px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $tool->category }}
                                </span>
                            </td>

                            {{-- Pricing --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11.5px] font-bold bg-slate-100 text-slate-700">
                                    {{ $tool->pricing ?: 'Gratis / Berlangganan' }}
                                </span>
                            </td>

                            {{-- URL --}}
                            <td class="py-4 px-4 max-w-[200px] truncate text-[12.5px] text-slate-500">
                                @if($tool->url)
                                    <a href="{{ $tool->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate inline-flex items-center gap-1">
                                        <span>{{ Str::limit(str_replace(['https://', 'http://', 'www.'], '', $tool->url), 25) }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-4 text-right whitespace-nowrap sm:px-6">
                                <div class="inline-flex items-center gap-2">
                                    {{-- Preview Link --}}
                                    <a href="{{ route('ai.show', $tool->id) }}" target="_blank" title="Lihat di Halaman Publik"
                                       class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.ai-tools.edit', $tool->id) }}" title="Edit AI"
                                       class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.ai-tools.destroy', $tool->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus AI \'{{ $tool->name }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus AI"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-[13.5px]">
                                Belum ada data AI. Klik "Tambah AI Baru" untuk menambahkan data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tools->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $tools->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
