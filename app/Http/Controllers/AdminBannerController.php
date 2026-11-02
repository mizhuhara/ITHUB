<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminBannerController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_if(auth()->user()?->role !== 'super_admin', 403, 'Hanya Super Admin yang dapat mengelola banner.');
    }

    /**
     * Display a listing of banners.
     */
    public function index(): View
    {
        $this->authorizeSuperAdmin();

        $banners = Banner::orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.banners.index', [
            'banners' => $banners,
            'totalBanners' => $banners->count(),
            'activeBanners' => $banners->where('is_active', true)->count(),
        ]);
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create(): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.banners.create');
    }

    /**
     * Store a newly created banner in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'link' => ['nullable', 'url', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('banners', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        } else {
            $imageUrl = $validated['image'] ?? '';
        }

        if (empty($imageUrl)) {
            return back()->withErrors(['image' => 'Gambar banner wajib diisi (URL atau upload file).'])->withInput();
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imageUrl,
            'link' => $validated['link'] ?? null,
            'link_text' => $validated['link_text'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan!');
    }

    /**
     * Show the form for editing a banner.
     */
    public function edit(int $id): View
    {
        $this->authorizeSuperAdmin();

        $banner = Banner::findOrFail($id);

        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified banner in the database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'link' => ['nullable', 'url', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('banners', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        } else {
            $imageUrl = $validated['image'] ?? $banner->image;
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imageUrl,
            'link' => $validated['link'] ?? null,
            'link_text' => $validated['link_text'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner \"{$banner->title}\" berhasil diperbarui!");
    }

    /**
     * Remove the specified banner from the database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $banner = Banner::findOrFail($id);
        $title = $banner->title;
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner \"{$title}\" berhasil dihapus!");
    }
}
