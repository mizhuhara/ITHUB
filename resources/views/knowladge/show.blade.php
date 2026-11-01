@extends('layouts.app')

@section('title', ($article['title'] ?? 'Article Detail') . ' — IndoTech')

@section('content')
<div class="bg-white min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('knowledge.index') }}" class="hover:text-blue-600 transition">Knowledge Hub</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-bold">{{ $article['category_tag'] ?? $article['category'] }}</span>
        </nav>

        {{-- Main Article Header --}}
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight tracking-tight mb-6">
                {{ $article['title'] }}
            </h1>

            {{-- Author Info --}}
            <div class="flex items-center gap-3.5 pt-2">
                <img src="{{ $article['author_avatar'] }}" alt="{{ $article['author_name'] }}" class="w-11 h-11 rounded-full object-cover shrink-0 border border-slate-200">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ $article['author_name'] }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $article['author_role'] ?? 'Tech Author' }} <span class="mx-1.5">•</span> {{ $article['date'] }} <span class="mx-1.5">•</span> {{ $article['read_time'] }}
                    </p>
                </div>
            </div>
        </header>

        {{-- Main Hero Image & Caption --}}
        <div class="mb-10">
            <div class="w-full h-80 sm:h-[420px] rounded-2xl overflow-hidden shadow-sm bg-slate-100">
                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
            </div>
            @if(!empty($article['image_caption']))
                <p class="text-center text-xs text-slate-500 mt-3 font-medium">
                    {{ $article['image_caption'] }}
                </p>
            @endif
        </div>

        {{-- Article Content --}}
        <article class="prose prose-slate max-w-none space-y-6 text-slate-700 text-base leading-relaxed mb-12">
            @if(isset($article['content']) && is_array($article['content']))
                {{-- Intro --}}
                <p class="text-base sm:text-lg leading-relaxed text-slate-700 font-normal">
                    {{ $article['content']['intro'] }}
                </p>

                {{-- Section 1 --}}
                @if(!empty($article['content']['section1_title']))
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight pt-4">
                        {{ $article['content']['section1_title'] }}
                    </h2>
                @endif
                @if(!empty($article['content']['section1_body']))
                    <p class="text-slate-600 leading-relaxed">
                        {{ $article['content']['section1_body'] }}
                    </p>
                @endif

                {{-- Bullet points --}}
                @if(!empty($article['content']['bullets']))
                    <ul class="space-y-2.5 my-4 list-disc list-inside text-slate-600 text-sm sm:text-base">
                        @foreach($article['content']['bullets'] as $bullet)
                            <li class="leading-relaxed">{{ $bullet }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Industry Insight Callout --}}
                @if(!empty($article['content']['callout']))
                    <div class="bg-blue-50/80 border-l-4 border-blue-600 rounded-r-xl p-5 my-8">
                        <div class="flex items-center gap-2 text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                            </svg>
                            <span>Industry Insight</span>
                        </div>
                        <p class="text-slate-800 text-sm sm:text-base leading-relaxed font-medium italic">
                            "{{ $article['content']['callout'] }}"
                        </p>
                    </div>
                @endif

                {{-- Section 2 --}}
                @if(!empty($article['content']['section2_title']))
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight pt-4">
                        {{ $article['content']['section2_title'] }}
                    </h2>
                @endif
                @if(!empty($article['content']['section2_body']))
                    <p class="text-slate-600 leading-relaxed">
                        {{ $article['content']['section2_body'] }}
                    </p>
                @endif

            @else
                {{-- Konten HTML dari Summernote atau teks biasa --}}
                @if(!empty($article['excerpt']))
                    <p class="text-base sm:text-lg leading-relaxed text-slate-700 font-normal mb-4">
                        {{ $article['excerpt'] }}
                    </p>
                @endif
                @if(!empty($article['content']))
                    <div class="prose prose-slate max-w-none text-slate-700">
                        {!! $article['content'] !!}
                    </div>
                @endif
            @endif
        </article>

        {{-- Article Tags & Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-6 border-t border-slate-200 mb-12">
            
            {{-- Tags --}}
            <div class="flex flex-wrap items-center gap-2">
                @if(!empty($article['tags']))
                    @foreach($article['tags'] as $tag)
                        <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-lg">
                            #{{ $tag }}
                        </span>
                    @endforeach
                @else
                    <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-lg">#TechLink</span>
                    <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-lg">#IndonesiaIT</span>
                @endif
            </div>

            {{-- Save & Share Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 19.007l-5.593-3.111-5.593 3.111V5.25a2.25 2.25 0 012.25-2.25h6.686a2.25 2.25 0 012.25 2.25v13.757z" />
                    </svg>
                    <span>Save</span>
                </button>
                <button type="button" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <span>Share</span>
                </button>
            </div>
        </div>

        {{-- Related Articles Section --}}
        @if(!empty($relatedArticles))
            <div class="pt-8 border-t border-slate-200">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-6">
                    Related Articles
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $rel)
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl overflow-hidden hover:shadow-md transition duration-300 flex flex-col justify-between group">
                            <div>
                                {{-- Thumbnail --}}
                                <div class="relative h-40 w-full bg-slate-100 overflow-hidden">
                                    <img src="{{ $rel['image'] }}" alt="{{ $rel['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>

                                {{-- Card Content --}}
                                <div class="p-5 space-y-2">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        {{ $rel['category_tag'] ?? $rel['category'] }}
                                    </span>
                                    <h3 class="text-sm font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-blue-600 transition">
                                        <a href="{{ route('knowledge.show', $rel['id']) }}">
                                            {{ $rel['title'] }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-2">
                                        {{ $rel['excerpt'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-5 pt-0 flex items-center justify-between text-xs text-slate-400 font-semibold border-t border-slate-200/60 mt-2">
                                <span>{{ $rel['date'] }}</span>
                                <a href="{{ route('knowledge.show', $rel['id']) }}" class="text-blue-600 hover:underline font-bold">
                                    Read &rsaquo;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
