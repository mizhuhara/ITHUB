@extends('admin.layouts.app')

@section('title', 'Kelola Banner — Admin')

@section('content')
{{-- Action Button --}}
<div class="flex items-center justify-end mb-6">
    <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-brand-600 text-white text-[13.5px] font-semibold hover:bg-blue-700 shadow-sm transition">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Banner
    </a>
</div>

{{-- Flash message --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13.5px] font-medium">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#0b57d0] flex items-center justify-center">
            <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
        </div>
        <div class="mt-3 text-[24px] font-extrabold text-slate-900 leading-none">{{ $totalBanners }}</div>
        <div class="mt-1.5 text-[13px] text-slate-500">Total Banner</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12.5 2.5 2.5 5-5.5"/></svg>
        </div>
        <div class="mt-3 text-[24px] font-extrabold text-slate-900 leading-none">{{ $activeBanners }}</div>
        <div class="mt-1.5 text-[13px] text-slate-500">Banner Aktif</div>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div>
            <h2 class="text-[16px] font-bold text-slate-900">Semua Banner</h2>
            <p class="text-[12.5px] text-slate-500">Kelola banner slider yang tampil di halaman beranda</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-[11.5px] uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">PREVIEW</th>
                    <th class="px-5 py-3 font-semibold">JUDUL</th>
                    <th class="px-5 py-3 font-semibold">STATUS</th>
                    <th class="px-5 py-3 font-semibold">URUTAN</th>
                    <th class="px-5 py-3 font-semibold text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($banners as $banner)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-14 w-28 object-cover rounded-lg border border-slate-200">
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-[13.5px] font-semibold text-slate-900">{{ $banner->title }}</div>
                            @if($banner->subtitle)
                                <div class="text-[12px] text-slate-500 mt-0.5">{{ Str::limit($banner->subtitle, 60) }}</div>
                            @endif
                            @if($banner->link)
                                <div class="text-[11px] text-blue-500 mt-0.5">{{ Str::limit($banner->link, 40) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($banner->is_active)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11.5px] font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11.5px] font-semibold text-slate-500">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-[13px] text-slate-600 font-medium">{{ $banner->sort_order }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" data-async data-async-follow onsubmit="return confirm('Yakin ingin menghapus banner ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="text-slate-400">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" class="mx-auto mb-3"><rect x="2" y="4" width="20" height="16" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                                <p class="text-[13.5px] font-medium text-slate-500">Belum ada banner</p>
                                <p class="text-[12px] text-slate-400 mt-1">Klik "Tambah Banner" untuk membuat banner baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

