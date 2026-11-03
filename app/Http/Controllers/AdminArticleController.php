<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Company;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request): View
    {
        $query = $this->visibleArticles();
        $user = $request->user();

        // Status tab filter
        $tab = $request->query('tab', 'all');
        if ($tab === 'published') {
            $query->where('status', 'published');
        } elseif ($tab === 'draft') {
            $query->where('status', 'draft');
        } elseif ($tab === 'archived') {
            $query->where('status', 'archived');
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $visible = $this->visibleArticles();

        $totalArticles = (clone $visible)->count();
        $publishedCount = (clone $visible)->where('status', 'published')->count();
        $draftCount = (clone $visible)->where('status', 'draft')->count();
        $totalViews = (clone $visible)->sum('views');

        return view('admin.articles.index', [
            'articles' => $articles,
            'currentTab' => $tab,
            'totalArticles' => number_format($totalArticles),
            'publishedCount' => number_format($publishedCount),
            'draftCount' => number_format($draftCount),
            'totalViews' => $totalViews >= 1000
                ? number_format($totalViews / 1000, 1).'K'
                : number_format($totalViews),
            'search' => $request->query('search', ''),
        ]);
    }

    /**
     * Show form for creating a new article.
     */
    public function create(): View
    {
        $this->authorizeCreate();

        $user = auth()->user();
        $company = $user && $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;
        $school = $user && $user->role === 'school' ? School::where('user_id', $user->id)->first() : null;
        $univ = $user && $user->role === 'university' ? University::where('user_id', $user->id)->first() : null;

        $defaultAuthor = $company?->name ?? $school?->name ?? $univ?->name ?? ($user ? $user->name : 'Admin IndoTech');
        $defaultRole = match ($user?->role) {
            'company' => 'Perusahaan Mitra',
            'school' => 'Sekolah Vokasi',
            'university' => 'Perguruan Tinggi',
            default => 'Admin IndoTech',
        };

        return view('admin.articles.create', compact('defaultAuthor', 'defaultRole'));
    }

    /**
     * Store a new article in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeCreate();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'string'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'author_role' => ['nullable', 'string', 'max:255'],
            'author_avatar' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'url', 'max:500'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status' => ['required', 'in:published,draft,archived'],
            'read_time' => ['nullable', 'string', 'max:50'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $tagsArray = ! empty($validated['tags'])
            ? array_map('trim', explode(',', $validated['tags']))
            : null;

        // Determine image URL: either uploaded file or provided URL
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('articles', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        } else {
            $imageUrl = $validated['image'] ?? null;
        }

        $user = $request->user();
        $company = $user && $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;
        $school = $user && $user->role === 'school' ? School::where('user_id', $user->id)->first() : null;
        $univ = $user && $user->role === 'university' ? University::where('user_id', $user->id)->first() : null;

        $defaultAuthor = $company?->name ?? $school?->name ?? $univ?->name ?? ($user ? $user->name : 'Admin IndoTech');
        $defaultRole = match ($user?->role) {
            'company' => 'Perusahaan Mitra',
            'school' => 'Sekolah Vokasi',
            'university' => 'Perguruan Tinggi',
            default => 'Admin IndoTech',
        };
        $defaultAvatar = $company?->logo_url ?? $school?->logo_url ?? $univ?->logo_url ?? $user?->avatar_url;

        $article = Article::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'category' => $validated['category'],
            'tags' => $tagsArray,
            'author_name' => ! empty($validated['author_name']) ? $validated['author_name'] : $defaultAuthor,
            'author_role' => ! empty($validated['author_role']) ? $validated['author_role'] : $defaultRole,
            'author_avatar' => ! empty($validated['author_avatar']) ? $validated['author_avatar'] : $defaultAvatar,
            'image' => $imageUrl,
            'image_caption' => $validated['image_caption'] ?? null,
            'status' => $validated['status'],
            'read_time' => $validated['read_time'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'user_id' => $user ? $user->id : null,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$article->title}\" berhasil disimpan ke database!");
    }

    /**
     * Show form for editing an article.
     */
    public function edit(int $id): View
    {
        $article = $this->findVisibleArticle($id);

        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Update an article in the database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $article = $this->findVisibleArticle($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', "unique:articles,slug,{$id}"],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'string'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'author_role' => ['nullable', 'string', 'max:255'],
            'author_avatar' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'url', 'max:500'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'status' => ['required', 'in:published,draft,archived'],
            'read_time' => ['nullable', 'string', 'max:50'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $tagsArray = ! empty($validated['tags'])
            ? array_map('trim', explode(',', $validated['tags']))
            : null;

        // Determine image URL: uploaded file overrides URL if present
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('articles', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        } else {
            $imageUrl = $validated['image'] ?? $article->image;
        }

        $user = $request->user();
        $company = $user && $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;
        $school = $user && $user->role === 'school' ? School::where('user_id', $user->id)->first() : null;
        $univ = $user && $user->role === 'university' ? University::where('user_id', $user->id)->first() : null;
        $defaultAvatar = $company?->logo_url ?? $school?->logo_url ?? $univ?->logo_url ?? $user?->avatar_url;

        $article->update([
            'title' => $validated['title'],
            'slug' => ! empty($validated['slug']) ? $validated['slug'] : $article->slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'category' => $validated['category'],
            'tags' => $tagsArray,
            'author_name' => $validated['author_name'] ?? $article->author_name,
            'author_role' => $validated['author_role'] ?? $article->author_role,
            'author_avatar' => ! empty($validated['author_avatar']) ? $validated['author_avatar'] : ($article->author_avatar ?: $defaultAvatar),
            'image' => $imageUrl,
            'image_caption' => $validated['image_caption'] ?? null,
            'status' => $validated['status'],
            'read_time' => $validated['read_time'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'user_id' => $article->user_id ?: ($user ? $user->id : null),
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$article->title}\" berhasil diperbarui di database.");
    }

    /**
     * Delete an article from the database.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! in_array(auth()->user()->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $article = $this->findVisibleArticle($id);
        $title = $article->title;
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$title}\" berhasil dihapus dari database.");
    }

    /**
     * Query articles yang boleh dilihat user: super_admin semua, role school/university/company hanya miliknya.
     */
    private function visibleArticles()
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['school', 'university', 'company'], true)) {
            return Article::where('user_id', $user->id);
        }

        return Article::query();
    }

    /**
     * Ambil artikel dengan proteksi kepemilikan (404 kalau bukan miliknya).
     */
    private function findVisibleArticle(int $id): Article
    {
        return $this->visibleArticles()->findOrFail($id);
    }

    private function authorizeCreate(): void
    {
        $user = auth()->user();

        if (! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true)) {
            abort(403, 'Akses ditolak. Akun ini tidak dapat menambah artikel.');
        }
    }
}
