@extends('admin.layouts.app')

@section('title', $school['name'] . ' — Detail Sekolah')
@section('header_title', $school['name'])
@section('header_breadcrumb', 'Detail Sekolah')
@section('header_subtitle', 'Informasi profil dan status verifikasi sekolah vokasi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Top Navigation & Action --}}
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('admin.schools.index') }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.schools.edit', $school['id']) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold shadow-sm transition">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Informasi Sekolah
            </a>
        </div>
    </div>

    {{-- Details Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-6">
            {{-- Overview Card --}}
            <div class="card space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-600 font-bold text-lg shrink-0 overflow-hidden shadow-xs">
                        @if(!empty($school['logo_url']))
                            <img src="{{ $school['logo_url'] }}" alt="{{ $school['name'] }}" class="w-full h-full object-cover">
                        @else
                            <span>{{ $school['logo_text'] ?? 'SMK' }}</span>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $school['name'] }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">NPSN: {{ $school['npsn'] }}</span>
                            <span>•</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-600 font-semibold">{{ $school['type'] }}</span>
                            <span>•</span>
                            <span>{{ $school['location'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi & Profil</h3>
                    <div class="prose max-w-none text-sm text-slate-700 leading-relaxed font-medium [&_a]:text-blue-600 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-blue-800 [&_a]:break-all">
                        {!! $school['description'] ?? 'Belum ada deskripsi profil untuk sekolah ini.' !!}
                    </div>
                </div>

                @php
                    $schoolCompetencies = is_array($school['competencies'] ?? null) ? $school['competencies'] : [];
                @endphp
                @if(!empty($schoolCompetencies))
                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Kompetensi Keahlian</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($schoolCompetencies as $comp)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-xl">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    {{ $comp }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Gallery Card --}}
            @php
                $galleryPhotos = is_array($school['gallery'] ?? null) ? $school['gallery'] : [];
            @endphp
            <div class="card space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Galeri Sekolah</h3>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-600">{{ count($galleryPhotos) }} Foto</span>
                </div>

                @if(!empty($galleryPhotos))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($galleryPhotos as $photo)
                            <div class="aspect-square rounded-xl overflow-hidden border border-slate-200/80 bg-slate-100 shadow-2xs group">
                                <img src="{{ $photo }}" alt="Galeri Sekolah" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Belum ada foto galeri yang diunggah.</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            {{-- Quick Info Card --}}
            <div class="card space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Tambahan</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Status</span>
                        <span class="font-semibold text-emerald-600">{{ $school['status'] }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Email</span>
                        <span class="font-medium text-slate-800">{{ $school['email'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Telepon</span>
                        <span class="font-medium text-slate-800">{{ $school['phone'] ?? '-' }}</span>
                    </div>
                    <div class="py-1">
                        <span class="block text-slate-500 mb-1">Alamat</span>
                        <span class="font-medium text-slate-800 leading-relaxed block">{{ $school['address'] ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
