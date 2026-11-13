@extends('admin.layouts.app')

@section('title', 'SMK IT Management — IndoTech')

@section('content')
    @if (session('status'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">
            {{ session('status') }}
        </div>
    @endif
    <div class="space-y-6">

    {{-- Action Buttons --}}
    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-end gap-2.5 sm:gap-3">
            {{-- Filter Button --}}
            <button type="button" 
                    onclick="toggleFilter()" 
                    id="filterBtn"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200/90 bg-white text-slate-700 hover:bg-slate-50 text-[13.5px] font-semibold shadow-xs transition">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
                @if((request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all'))
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                @endif
            </button>

            {{-- Add New School Button (hanya admin) --}}
            @if (!empty($canManage))
            <a href="{{ route('admin.schools.create') }}" 
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white text-[13.5px] font-semibold shadow-sm transition">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add New School
            </a>
            @endif
    </div>

    {{-- Inline Filter Panel (Collapsible) --}}
    <div id="filterPanel" class="{{ ((request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all')) ? '' : 'hidden' }} bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs transition-all duration-200">
        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filter Data SMK IT</h3>
            </div>
            <button type="button" onclick="toggleFilter()" class="text-slate-400 hover:text-slate-600 text-xs font-semibold flex items-center gap-1 transition">
                <span>Tutup</span>
                <span class="text-base leading-none">&times;</span>
            </button>
        </div>

        <form method="GET" action="{{ route('admin.schools.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-end gap-3.5">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="w-full lg:flex-1 lg:min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Sekolah</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="all" {{ (!request('status') || request('status') === 'all') ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive (Non-aktif)</option>
                </select>
            </div>

            <div class="w-full lg:flex-1 lg:min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tipe Sekolah</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-800 outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="all" {{ (!request('type') || request('type') === 'all') ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="SMK IT" {{ strtolower(request('type')) === 'smk it' ? 'selected' : '' }}>SMK IT</option>
                    <option value="SMK" {{ strtolower(request('type')) === 'smk' ? 'selected' : '' }}>SMK</option>
                    <option value="SMA IT" {{ strtolower(request('type')) === 'sma it' ? 'selected' : '' }}>SMA IT</option>
                    <option value="Vokasi IT" {{ strtolower(request('type')) === 'vokasi it' ? 'selected' : '' }}>Vokasi IT</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto shrink-0 mt-1 lg:mt-0">
                <a href="{{ route('admin.schools.index') }}" class="flex-1 sm:flex-initial text-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-[13px] hover:bg-slate-50 transition">Reset</a>
                <button type="submit" class="flex-1 sm:flex-initial text-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] shadow-sm transition">Terapkan Filter</button>
            </div>
        </form>
    </div>

    {{-- 3 Top Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        {{-- Card 1: SCHOOLS --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[12px] font-bold tracking-wider text-slate-400 uppercase">SCHOOLS</div>
            <div class="text-[34px] font-extrabold text-slate-900 leading-tight mt-2">{{ $totalSchools }}</div>
        </div>

        {{-- Card 2: ACTIVE PARTNERS --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[12px] font-bold tracking-wider text-slate-400 uppercase">ACTIVE PARTNERS</div>
            <div class="text-[34px] font-extrabold text-slate-900 leading-tight mt-2">{{ $activePartners }}</div>
        </div>

        {{-- Card 3: NEW SUBMISSIONS --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[12px] font-bold tracking-wider text-slate-400 uppercase">NEW SUBMISSIONS</div>
            <div class="text-[34px] font-extrabold text-slate-900 leading-tight mt-2">{{ $newSubmissions }}</div>
        </div>
    </div>

    {{-- Main Table Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        {{-- Table Toolbar: Search & Sort --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.schools.index') }}" class="relative w-full sm:max-w-md">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 rounded-xl px-3.5 h-10 w-full focus-within:border-blue-500 focus-within:bg-white transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search SMK IT..." 
                           class="bg-transparent outline-none text-[13.5px] text-slate-800 placeholder-slate-400 flex-1 min-w-0">
                    @if(request('search'))
                        <a href="{{ route('admin.schools.index') }}" class="text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                    @endif
                </div>
            </form>

            {{-- Sort Dropdown --}}
            <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-2 text-[13.5px] text-slate-600">
                <form method="GET" action="{{ route('admin.schools.index') }}" id="sortForm" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <span class="text-slate-500 font-medium">Sort by:</span>
                    <select name="sort" 
                            onchange="document.getElementById('sortForm').submit()"
                            class="bg-transparent font-semibold text-slate-900 outline-none cursor-pointer pr-4 hover:text-blue-600 transition">
                        <option value="recently_added" {{ request('sort') === 'recently_added' ? 'selected' : '' }}>Recently Added ▾</option>
                        <option value="region_asc" {{ request('sort') === 'region_asc' ? 'selected' : '' }}>Daerah (A-Z) ▾</option>
                        <option value="region_desc" {{ request('sort') === 'region_desc' ? 'selected' : '' }}>Daerah (Z-A) ▾</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z) ▾</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A) ▾</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest ▾</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Schools Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="cell">SCHOOL NAME</th>
                        <th class="cell">TYPE</th>
                        <th class="cell">LOCATION</th>
                        <th class="cell">STATUS</th>
                        <th class="px-6 py-4 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse ($schools as $school)
                        <tr class="hover:bg-slate-50/70 transition group">
                            {{-- School Name & NPSN & Logo --}}
                            <td class="cell">
                                <div class="flex items-center gap-3.5">
                                    {{-- Logo Avatar --}}
                                    <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-600 font-bold text-xs shrink-0 overflow-hidden shadow-2xs">
                                        @if(!empty($school['logo_url']))
                                            <img src="{{ $school['logo_url'] }}" alt="{{ $school['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-extrabold">{{ $school['logo_text'] ?? 'SMK' }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.schools.show', $school['id']) }}" class="font-bold text-slate-900 hover:text-blue-600 transition block leading-snug">
                                            {{ $school['name'] }}
                                        </a>
                                        <div class="text-[12px] text-slate-400 mt-0.5">
                                            NPSN: {{ $school['npsn'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type Badge --}}
                            <td class="cell">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-medium bg-[#eef4ff] text-brand-600">
                                    {{ $school['institution_type'] ?? 'SMK (IT)' }}
                                </span>
                                @if (!empty($school['tags']))
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($school['tags'] as $tag)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="cell">
                                <div class="font-semibold text-slate-800 leading-tight">
                                    {{ $school['city'] }}
                                </div>
                                <div class="text-[12px] text-slate-400 mt-0.5">
                                    {{ $school['province'] }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="cell">
                                @if(strtolower($school['status']) === 'active')
                                    <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-800">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-800">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-slate-400">
                                    {{-- View Icon (Eye) --}}
                                    <a href="{{ route('admin.schools.show', $school['id']) }}" class="hover:text-slate-700 transition" title="Lihat Detail Sekolah">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>

                                    {{-- Edit Icon --}}
                                    <a href="{{ route('admin.schools.edit', $school['id']) }}" class="hover:text-blue-600 transition" title="Edit Data Sekolah">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                    </a>

                                    {{-- Delete Button (hanya admin) --}}
                                    @if (!empty($canManage))
                                    <button type="button" 
                                            onclick="openDeleteModal('{{ $school['id'] }}', {{ json_encode($school['name']) }}, {{ json_encode($school['npsn'] ?? '') }}, {{ json_encode($school['city'] ?? '') }})"
                                            class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer" 
                                            title="Hapus Sekolah">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                    </div>
                                    <div class="font-bold text-slate-800">Tidak ada data sekolah ditemukan</div>
                                    <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                                    <a href="{{ route('admin.schools.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Pencarian</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-center sm:text-left">
            <div class="text-[13px] text-slate-500">
                Showing <span class="font-semibold text-slate-800">{{ $schools->firstItem() ?? 0 }}-{{ $schools->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-800">{{ $schools->total() }}</span> schools
            </div>

            {{-- Pagination Navigation --}}
            <div class="flex flex-wrap justify-center items-center gap-1.5 text-[13.5px]">
                {{-- Previous Link --}}
                @if ($schools->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">‹</span>
                @else
                    <a href="{{ $schools->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">‹</a>
                @endif

                {{-- Page Number Links --}}
                @for ($page = 1; $page <= max(1, $schools->lastPage()); $page++)
                    @if ($page == $schools->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-brand-600 text-white font-bold shadow-xs select-none">{{ $page }}</span>
                    @else
                        <a href="{{ $schools->url($page) }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">{{ $page }}</a>
                    @endif
                @endfor

                {{-- Next Link --}}
                @if ($schools->hasMorePages())
                    <a href="{{ $schools->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">›</a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">›</span>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function openDeleteModal(id, name, npsn, city) {
        let extra = npsn && city ? ` (${npsn} - ${city})` : (npsn ? ` (${npsn})` : (city ? ` (${city})` : ''));
        window.confirmDelete(`{{ url('/admin/schools') }}/${id}`, `${name}${extra}`, {
            title: 'Konfirmasi Hapus Sekolah',
            warning: 'Peringatan: Tindakan ini permanen dan data sekolah tidak dapat dikembalikan.'
        });
    }

    function closeDeleteModal() {
        window.closeGlobalDeleteModal();
    }

    function toggleFilter() {
        const panel = document.getElementById('filterPanel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }
</script>
@endpush
@endsection
