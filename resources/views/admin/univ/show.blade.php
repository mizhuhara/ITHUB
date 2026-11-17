@extends('admin.layouts.app')

@section('title', $univ['name'] . ' — Detail Universitas')
@section('header_title', $univ['name'])
@section('header_breadcrumb', 'Detail Universitas')
@section('header_subtitle', 'Informasi profil dan status kemitraan universitas')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .leaflet-container {
        font-family: inherit;
        z-index: 10 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Top Navigation & Action --}}
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('admin.univ.index') }}" class="inline-flex items-center gap-2 text-[14px] font-semibold text-slate-700 hover:text-blue-600 transition">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.univ.edit', $univ['id']) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold shadow-sm transition">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Informasi Universitas
            </a>
        </div>
    </div>

    {{-- Details Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-6">
            {{-- Overview Card --}}
            <div class="card space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-600 font-bold text-lg shrink-0 overflow-hidden shadow-xs p-2">
                        @if(!empty($univ['logo_url']))
                            <img src="{{ $univ['logo_url'] }}" alt="{{ $univ['name'] }}" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full rounded-xl bg-blue-600 text-white flex items-center justify-center font-black">
                                {{ $univ['logo_text'] ?? 'UNIV' }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $univ['name'] }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-500">
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-600 font-semibold">{{ $univ['type'] }}</span>
                            <span>•</span>
                            <span>{{ $univ['location'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi & Profil</h3>
                    <p class="text-sm text-slate-700 leading-relaxed font-medium">
                        {{ $univ['description'] ?? 'Belum ada deskripsi profil untuk universitas ini.' }}
                    </p>
                </div>
            </div>

            {{-- Campus Gallery Card --}}
            <div class="card">
                <div class="flex items-center gap-2.5 mb-4 text-slate-900">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2" class="shrink-0">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
                    </svg>
                    <h2 class="text-[17px] font-bold text-slate-900">Galeri Kampus</h2>
                </div>

                @php
                    $photos = is_array($univ->gallery) ? $univ->gallery : [];
                @endphp

                @if(count($photos) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($photos as $photo)
                            <div class="aspect-square rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden shadow-xs group">
                                <img src="{{ $photo }}" alt="Galeri {{ $univ['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-sm font-medium text-slate-500">Belum ada foto galeri kampus yang diunggah.</p>
                        <a href="{{ route('admin.univ.edit', $univ['id']) }}" class="inline-block mt-3 text-xs font-bold text-brand-600 hover:underline">
                            + Tambah Foto Galeri Sekarang
                        </a>
                    </div>
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
                        <span class="font-semibold text-emerald-600">{{ $univ['status'] }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Akreditasi</span>
                        <span class="font-medium text-slate-800">{{ $univ['accreditation'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Email</span>
                        <span class="font-medium text-slate-800">{{ $univ['email'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Telepon</span>
                        <span class="font-medium text-slate-800">{{ $univ['phone'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500">Website</span>
                        @if(!empty($univ['website']))
                            <a href="{{ $univ['website'] }}" target="_blank" class="font-medium text-brand-600 hover:underline truncate max-w-[180px]">{{ $univ['website'] }}</a>
                        @else
                            <span class="font-medium text-slate-800">-</span>
                        @endif
                    </div>
                    <div class="py-1">
                        <span class="block text-slate-500 mb-1">Alamat</span>
                        <span class="font-medium text-slate-800 leading-relaxed block">{{ $univ['address'] ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Map Location Card --}}
            <div class="card space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Peta Lokasi</h3>
                    @php
                        $mapQuery = !empty($univ['latitude']) && !empty($univ['longitude']) 
                            ? "{$univ['latitude']},{$univ['longitude']}" 
                            : ($univ['address'] ?? $univ['name']);
                    @endphp
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}" target="_blank" class="text-xs font-bold text-brand-600 hover:underline flex items-center gap-1">
                        Google Maps
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    </a>
                </div>

                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-xs">
                    <div id="showUnivMap" class="w-full h-44 bg-slate-100 z-0"></div>
                </div>

                @if(!empty($univ['latitude']) && !empty($univ['longitude']))
                    <p class="text-xs text-slate-500 font-mono text-center">
                        Koordinat: {{ number_format((float)$univ['latitude'], 5) }}, {{ number_format((float)$univ['longitude'], 5) }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat("{{ $univ['latitude'] ?? '-6.2088' }}") || -6.2088;
        const lng = parseFloat("{{ $univ['longitude'] ?? '106.8456' }}") || 106.8456;
        const hasCoords = "{{ !empty($univ['latitude']) && !empty($univ['longitude']) }}" === "1";

        const map = L.map('showUnivMap', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([lat, lng], hasCoords ? 15 : 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const pinIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const marker = L.marker([lat, lng], { icon: pinIcon }).addTo(map);
        marker.bindPopup("<b>{{ $univ['name'] }}</b><br>{{ $univ['location'] }}");
    });
</script>
@endpush
