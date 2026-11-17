@extends('admin.layouts.app')

@section('title', 'Community Management — IndoTech')

@section('content')
<div class="space-y-6">

    {{-- Action Buttons --}}
    <div class="flex items-center justify-between sm:justify-end gap-2.5 sm:gap-3">
            {{-- Filter Button --}}
            <button type="button" 
                    onclick="document.getElementById('filterModal').classList.toggle('hidden')"
                    class="shrink-0 inline-flex items-center justify-center gap-2 h-[42px] px-4 rounded-xl border border-slate-200/90 bg-white text-slate-700 hover:bg-slate-50 text-[13px] sm:text-[13.5px] font-semibold shadow-xs transition cursor-pointer whitespace-nowrap">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-500 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
            </button>

            {{-- Add New Community Button --}}
            <a href="{{ route('admin.community.create') }}" 
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 h-[42px] px-4 sm:px-5 rounded-xl bg-brand-600 hover:bg-blue-700 text-white text-[13px] sm:text-[13.5px] font-semibold shadow-sm transition whitespace-nowrap">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="shrink-0">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>Add New Community</span>
            </a>
    </div>

    {{-- Filter Modal / Drawer (Collapsible) --}}
    <div id="filterModal" class="hidden bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm transition">
        <form method="GET" action="{{ route('admin.community.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Category</label>
                <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500">
                    <option value="all">Semua Kategori</option>
                    <option value="Technology" {{ request('category') === 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Design" {{ request('category') === 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Business" {{ request('category') === 'Business' ? 'selected' : '' }}>Business</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-brand-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition cursor-pointer">Terapkan Filter</button>
                <a href="{{ route('admin.community.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-200 transition">Reset</a>
            </div>
        </form>
    </div>

    {{-- 3 Top Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-5">
        {{-- Card 1: TOTAL COMMUNITIES --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[11px] sm:text-[12px] font-bold tracking-wider text-slate-400 uppercase">TOTAL COMMUNITIES</div>
            <div class="text-2xl sm:text-[34px] font-extrabold text-slate-900 leading-tight mt-1.5 sm:mt-2">{{ $totalCommunities }}</div>
        </div>

        {{-- Card 2: ACTIVE COMMUNITIES --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[11px] sm:text-[12px] font-bold tracking-wider text-slate-400 uppercase">ACTIVE COMMUNITIES</div>
            <div class="text-2xl sm:text-[34px] font-extrabold text-slate-900 leading-tight mt-1.5 sm:mt-2">{{ $activeCommunities }}</div>
        </div>

        {{-- Card 3: NEW MEMBERS --}}
        <div class="card relative overflow-hidden flex flex-col justify-between">
            <div class="text-[11px] sm:text-[12px] font-bold tracking-wider text-slate-400 uppercase">NEW MEMBERS</div>
            <div class="text-2xl sm:text-[34px] font-extrabold text-slate-900 leading-tight mt-1.5 sm:mt-2">{{ $newMembers }}</div>
        </div>
    </div>

    {{-- Main Table Container Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        {{-- Table Toolbar: Search & Sort --}}
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.community.index') }}" class="relative flex-1 max-w-md">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 rounded-xl px-3.5 h-10 w-full focus-within:border-blue-500 focus-within:bg-white transition">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-slate-400">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search Community..." 
                           class="bg-transparent outline-none text-[13.5px] text-slate-800 placeholder-slate-400 flex-1 min-w-0">
                    @if(request('search'))
                        <a href="{{ route('admin.community.index') }}" class="text-xs text-slate-400 hover:text-slate-600 font-bold">&times;</a>
                    @endif
                </div>
            </form>

            {{-- Sort Dropdown --}}
            <div class="flex items-center gap-2 text-[13.5px] text-slate-600 self-end sm:self-auto">
                <form method="GET" action="{{ route('admin.community.index') }}" id="sortForm" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    
                    <span class="text-slate-500 font-medium">Sort by:</span>
                    <select name="sort" 
                            onchange="document.getElementById('sortForm').submit()"
                            class="bg-transparent font-semibold text-slate-900 outline-none cursor-pointer pr-4 hover:text-blue-600 transition">
                        <option value="recently_added" {{ request('sort') === 'recently_added' ? 'selected' : '' }}>Recently Added ▾</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z) ▾</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A) ▾</option>
                        <option value="members_desc" {{ request('sort') === 'members_desc' ? 'selected' : '' }}>Most Members ▾</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest ▾</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse ($communities as $community)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-600 font-bold text-xs shrink-0 overflow-hidden shadow-2xs">
                                @if(!empty($community['logo_url']))
                                    <img src="{{ $community['logo_url'] }}" alt="{{ $community['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-extrabold">{{ substr($community['name'], 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.community.show', $community['id']) }}" class="font-bold text-[14px] text-slate-900 hover:text-blue-600 transition block leading-snug truncate">
                                    {{ $community['name'] }}
                                </a>
                                <div class="text-[12px] text-slate-400 mt-0.5 truncate">
                                    {{ $community['email'] }}
                                </div>
                            </div>
                        </div>

                        @if(strtolower($community['status']) === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11.5px] font-semibold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-[11.5px] font-semibold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-[12px]">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11.5px] font-medium bg-blue-50 text-brand-600">{{ $community['category'] }}</span>
                        <span class="font-semibold text-slate-700">{{ number_format($community['members'], 0, ',', '.') }} Members</span>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('admin.community.show', $community['id']) }}" class="flex-1 inline-flex items-center justify-center min-h-[38px] px-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-[12.5px] font-semibold transition">
                            Lihat
                        </a>
                        <a href="{{ route('admin.community.edit', $community['id']) }}" class="flex-1 inline-flex items-center justify-center min-h-[38px] px-3 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-[12.5px] font-semibold transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.community.destroy', $community['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komunitas {{ $community['name'] }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center min-h-[38px] min-w-[38px] px-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition cursor-pointer" title="Hapus Komunitas">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-slate-500">
                    <div class="max-w-sm mx-auto space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <div class="font-bold text-slate-800">Tidak ada data komunitas ditemukan</div>
                        <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                        <a href="{{ route('admin.community.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Pencarian</a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Communities Table (Desktop/Tablet) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="cell">COMMUNITY NAME</th>
                        <th class="cell">CATEGORY</th>
                        <th class="cell">MEMBERS</th>
                        <th class="cell">STATUS</th>
                        <th class="px-6 py-4 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13.5px]">
                    @forelse ($communities as $community)
                        <tr class="hover:bg-slate-50/70 transition group">
                            {{-- Community Name & Logo --}}
                            <td class="cell">
                                <div class="flex items-center gap-3.5">
                                    {{-- Logo Avatar --}}
                                    <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-600 font-bold text-xs shrink-0 overflow-hidden shadow-2xs">
                                        @if(!empty($community['logo_url']))
                                            <img src="{{ $community['logo_url'] }}" alt="{{ $community['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-extrabold">{{ substr($community['name'], 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.community.show', $community['id']) }}" class="font-bold text-slate-900 hover:text-blue-600 transition block leading-snug">
                                            {{ $community['name'] }}
                                        </a>
                                        <div class="text-[12px] text-slate-400 mt-0.5">
                                            {{ $community['email'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category Badge --}}
                            <td class="cell">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-medium bg-blue-50 text-brand-600">{{ $community['category'] }}</span>
                            </td>

                            {{-- Members --}}
                            <td class="cell">
                                <div class="font-semibold text-slate-800 leading-tight">
                                    {{ number_format($community['members'], 0, ',', '.') }}
                                </div>
                                <div class="text-[12px] text-slate-400 mt-0.5">
                                    Members
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="cell">
                                @if(strtolower($community['status']) === 'active')
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
                                <div class="flex items-center justify-end gap-2 text-slate-400">
                                    {{-- Edit Link --}}
                                    <a href="{{ route('admin.community.edit', $community['id']) }}" 
                                       class="p-1.5 rounded-lg hover:text-blue-600 hover:bg-blue-50 transition" 
                                       title="Edit Komunitas">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete Button with Form --}}
                                    <form method="POST" action="{{ route('admin.community.destroy', $community['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komunitas {{ $community['name'] }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:text-red-600 hover:bg-red-50 transition" title="Hapus Komunitas">
                                            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Detail Link --}}
                                    <a href="{{ route('admin.community.show', $community['id']) }}" class="p-1.5 rounded-lg hover:text-slate-800 hover:bg-slate-100 transition" title="Detail Komunitas">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="1"/>
                                            <circle cx="12" cy="5" r="1"/>
                                            <circle cx="12" cy="19" r="1"/>
                                        </svg>
                                    </a>
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
                                    <div class="font-bold text-slate-800">Tidak ada data komunitas ditemukan</div>
                                    <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                                    <a href="{{ route('admin.community.index') }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">Reset Pencarian</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="text-[13px] text-slate-500">
                Showing <span class="font-semibold text-slate-800">{{ $communities->firstItem() ?? 0 }}-{{ $communities->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-800">{{ $communities->total() }}</span> communities
            </div>

            {{-- Pagination Navigation --}}
            <div class="flex flex-wrap items-center justify-center gap-1.5 text-[13.5px]">
                {{-- Previous Link --}}
                @if ($communities->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">‹</span>
                @else
                    <a href="{{ $communities->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">‹</a>
                @endif

                {{-- Page Number Links --}}
                @for ($page = 1; $page <= max(1, $communities->lastPage()); $page++)
                    @if ($page == $communities->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-brand-600 text-white font-bold shadow-xs select-none">{{ $page }}</span>
                    @else
                        <a href="{{ $communities->url($page) }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">{{ $page }}</a>
                    @endif
                @endfor

                {{-- Next Link --}}
                @if ($communities->hasMorePages())
                    <a href="{{ $communities->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">›</a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 text-slate-300 cursor-not-allowed select-none">›</span>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
