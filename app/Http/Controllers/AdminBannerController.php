<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminBannerController extends Controller
{
    /**
     * Allowed roles to manage or create banners.
     */
    protected const ALLOWED_ROLES = ['super_admin', 'admin', 'school', 'university', 'company'];

    /**
     * Check if the authenticated user has access to the banner module.
     */
    private function authorizeBannerAccess(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! in_array($user->role, self::ALLOWED_ROLES, true), 403, 'Anda tidak memiliki hak akses ke modul banner.');
    }

    /**
     * Check if the user is an admin or super admin.
     */
    private function isAdmin(): bool
    {
        return in_array(Auth::user()?->role, ['super_admin', 'admin'], true);
    }

    /**
     * Display a listing of banners.
     */
    public function index(): View
    {
        $this->authorizeBannerAccess();

        // Always sync queue before rendering
        Banner::syncActiveQueue();

        $user = Auth::user();
        $isAdmin = $this->isAdmin();

        $bannersQuery = Banner::with('user')
            ->orderByRaw("CASE 
                WHEN status = 'active' THEN 1 
                WHEN status = 'queued' THEN 2 
                ELSE 3 
            END")
            ->orderBy('queue_position')
            ->orderBy('sort_order')
            ->orderByDesc('id');

        $banners = $bannersQuery->get();

        $activeBannersCount = Banner::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $queuedBannersCount = Banner::where('status', 'queued')->count();

        // Institution eligibility check
        $canCreateBanner = true;
        $membershipActive = true;
        $rateLimitMessage = null;
        $lastBanner = null;

        if (! $isAdmin && $user->isInstitution()) {
            $membershipActive = $user->hasActiveMembership();
            if (! $membershipActive) {
                $canCreateBanner = false;
            } else {
                $lastBanner = Banner::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->latest('created_at')
                    ->first();

                if ($lastBanner) {
                    $canCreateBanner = false;
                    $canPostDate = $lastBanner->created_at->addDays(7);
                    $rateLimitMessage = 'Batas unggah 1x seminggu tercapai. Anda dapat menambahkan banner kembali pada '.$canPostDate->translatedFormat('d F Y H:i');
                }
            }
        }

        return view('admin.banners.index', [
            'banners' => $banners,
            'totalBanners' => $banners->count(),
            'activeBanners' => $activeBannersCount,
            'queuedBanners' => $queuedBannersCount,
            'maxSlots' => Banner::MAX_ACTIVE_SLIDES,
            'isAdmin' => $isAdmin,
            'canCreateBanner' => $canCreateBanner,
            'membershipActive' => $membershipActive,
            'rateLimitMessage' => $rateLimitMessage,
            'lastBanner' => $lastBanner,
        ]);
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create(): View|RedirectResponse
    {
        $this->authorizeBannerAccess();

        $user = Auth::user();
        $isAdmin = $this->isAdmin();

        if (! $isAdmin && $user->isInstitution()) {
            if (! $user->hasActiveMembership()) {
                return redirect()->route('admin.banners.index')
                    ->with('error', 'Institusi Anda harus memiliki status langganan/membership aktif untuk menambahkan banner promosi.');
            }

            $lastBanner = Banner::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->latest('created_at')
                ->first();

            if ($lastBanner) {
                $canPostDate = $lastBanner->created_at->addDays(7);

                return redirect()->route('admin.banners.index')
                    ->with('error', 'Institusi Anda hanya dapat menambahkan banner maksimal 1x seminggu. Anda dapat mengunggah kembali pada '.$canPostDate->translatedFormat('d F Y H:i').'.');
            }
        }

        Banner::syncActiveQueue();

        $activeBannersCount = Banner::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $queuedBannersCount = Banner::where('status', 'queued')->count();

        return view('admin.banners.create', [
            'isAdmin' => $isAdmin,
            'activeBannersCount' => $activeBannersCount,
            'queuedBannersCount' => $queuedBannersCount,
            'maxSlots' => Banner::MAX_ACTIVE_SLIDES,
        ]);
    }

    /**
     * Store a newly created banner in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeBannerAccess();

        $user = Auth::user();
        $isAdmin = $this->isAdmin();

        if (! $isAdmin && $user->isInstitution()) {
            if (! $user->hasActiveMembership()) {
                return redirect()->route('admin.banners.index')
                    ->with('error', 'Institusi Anda harus memiliki status langganan/membership aktif untuk menambahkan banner promosi.');
            }

            $lastBanner = Banner::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->latest('created_at')
                ->first();

            if ($lastBanner) {
                $canPostDate = $lastBanner->created_at->addDays(7);

                return redirect()->route('admin.banners.index')
                    ->with('error', 'Institusi Anda hanya dapat menambahkan banner maksimal 1x seminggu. Anda dapat mengunggah kembali pada '.$canPostDate->translatedFormat('d F Y H:i').'.');
            }
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'link' => ['nullable', 'string', 'max:500'],
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

        // Sync queue first
        Banner::syncActiveQueue();

        $activeCount = Banner::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $status = 'active';
        $isActive = true;
        $queuePosition = null;
        $startedAt = now();
        $durationMode = $request->input('duration_mode', 'permanent');
        $expiresAt = ($isAdmin && $durationMode === 'permanent') ? null : now()->addDays(7);
        $successMessage = 'Banner berhasil ditambahkan dan langsung aktif di slide beranda!';

        if ($isAdmin) {
            $wantsActive = $request->boolean('is_active', true);
            if (! $wantsActive) {
                $status = 'inactive';
                $isActive = false;
                $startedAt = null;
                $successMessage = 'Banner berhasil disimpan sebagai draft / nonaktif.';
            } elseif ($activeCount >= Banner::MAX_ACTIVE_SLIDES) {
                $status = 'queued';
                $isActive = false;
                $startedAt = null;
                $expiresAt = ($durationMode === '1_week') ? null : null; // Queued banners calculate expiration when promoted
                $queuePosition = Banner::where('status', 'queued')->count() + 1;
                $successMessage = "Slot 5 banner utama saat ini penuh ({$activeCount}/5). Banner dimasukkan ke dalam antrean (Urutan #{$queuePosition}).";
            }
        } else {
            // Institution flow: Check 5-slide capacity
            if ($activeCount >= Banner::MAX_ACTIVE_SLIDES) {
                $status = 'queued';
                $isActive = false;
                $startedAt = null;
                $expiresAt = null;
                $queuePosition = Banner::where('status', 'queued')->count() + 1;
                $successMessage = "Slot 5 banner utama beranda saat ini sedang penuh (5/5). Banner Anda berhasil masuk ke antrean (Urutan #{$queuePosition}) dan akan otomatis tayang saat slot tersedia.";
            } else {
                $successMessage = 'Banner institusi Anda berhasil ditambahkan dan langsung aktif di slide beranda selama 7 hari!';
            }
        }

        Banner::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imageUrl,
            'link' => $validated['link'] ?? null,
            'link_text' => $validated['link_text'] ?? null,
            'is_active' => $isActive,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $status,
            'queue_position' => $queuePosition,
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', $successMessage);
    }

    /**
     * Show the form for editing a banner.
     */
    public function edit(int $id): View
    {
        $this->authorizeBannerAccess();

        $banner = Banner::findOrFail($id);
        $user = Auth::user();

        if (! $this->isAdmin() && $banner->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat mengubah banner milik institusi Anda sendiri.');
        }

        return view('admin.banners.edit', [
            'banner' => $banner,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * Update the specified banner in the database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeBannerAccess();

        $banner = Banner::findOrFail($id);
        $user = Auth::user();
        $isAdmin = $this->isAdmin();

        if (! $isAdmin && $banner->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat mengubah banner milik institusi Anda sendiri.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'link' => ['nullable', 'string', 'max:500'],
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

        $updateData = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imageUrl,
            'link' => $validated['link'] ?? null,
            'link_text' => $validated['link_text'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($isAdmin) {
            $wantsActive = $request->boolean('is_active');
            $updateData['is_active'] = $wantsActive;
            if ($wantsActive && $banner->status !== 'active') {
                $updateData['status'] = 'active';
                $updateData['queue_position'] = null;
                $updateData['started_at'] = now();
            } elseif (! $wantsActive && $banner->status === 'active') {
                $updateData['status'] = 'inactive';
            }

            if ($request->has('duration_mode')) {
                $durationMode = $request->input('duration_mode');
                if ($durationMode === '1_week') {
                    $baseDate = $banner->started_at ?? now();
                    $updateData['expires_at'] = $baseDate->copy()->addDays(7);
                } elseif ($durationMode === 'permanent') {
                    $updateData['expires_at'] = null;
                }
            }
        }

        $banner->update($updateData);

        // Sync queue after updates
        Banner::syncActiveQueue();

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner \"{$banner->title}\" berhasil diperbarui!");
    }

    /**
     * Remove the specified banner from the database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeBannerAccess();

        $banner = Banner::findOrFail($id);
        $user = Auth::user();

        if (! $this->isAdmin() && $banner->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat menghapus banner milik institusi Anda sendiri.');
        }

        $title = $banner->title;
        $banner->delete();

        // Sync queue so next in queue can slide in
        Banner::syncActiveQueue();

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner \"{$title}\" berhasil dihapus!");
    }

    /**
     * Admin action to immediately promote a queued banner to active.
     */
    public function promote(int $id): RedirectResponse
    {
        abort_if(! $this->isAdmin(), 403, 'Hanya admin yang dapat mempromosikan antrean banner.');

        $banner = Banner::findOrFail($id);
        abort_if($banner->status !== 'queued', 400, 'Banner tidak dalam status antrean.');

        $banner->update([
            'status' => 'active',
            'is_active' => true,
            'queue_position' => null,
            'started_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        Banner::syncActiveQueue();

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner \"{$banner->title}\" berhasil dipromosikan ke slide aktif!");
    }
}
