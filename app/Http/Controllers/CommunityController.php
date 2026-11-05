<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Community::where('status', 'active')
            ->where('approval_status', 'approved');

        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        /** @var array<string,string> Map slug → TitleCase category stored in DB */
        $categoryMap = [
            'technology' => 'Technology',
            'design' => 'Design',
            'business' => 'Business',
            'education' => 'Education',
            'science' => 'Science',
            'other' => 'Other',
        ];

        $rawCategory = $request->input('category', 'all');
        $activeCategory = $rawCategory;

        if ($rawCategory !== 'all') {
            // Resolve slug (e.g. "technology") or exact TitleCase (e.g. "Technology") to DB value
            $dbCategory = $categoryMap[strtolower($rawCategory)] ?? $rawCategory;
            $query->where('category', $dbCategory);
        }

        $sort = $request->input('sort', 'random');
        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'newest' => $query->orderByDesc('created_at'),
            'members_desc' => $query->orderByDesc('members'),
            default => $query->inRandomOrder(),
        };

        return view('community.index', [
            'communities' => $query->paginate(6)->withQueryString(),
            'totalMembers' => Community::where('status', 'active')->where('approval_status', 'approved')->sum('members'),
            'totalGroups' => Community::where('status', 'active')->where('approval_status', 'approved')->count(),
            'activeSort' => $sort,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function create(): View
    {
        $categories = ['Technology', 'Design', 'Business', 'Education', 'Science', 'Other'];

        return view('community.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1500'],
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'type' => ['required', 'in:public,private,publik,privat'],
            'mode' => ['nullable', 'in:online,offline,hybrid,Online,Offline,Hybrid'],
            'location' => ['nullable', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:100'],
            'rules' => ['nullable', 'string', 'max:3000'],
            'tags' => ['nullable'],
        ], [
            'name.required' => 'Nama komunitas wajib diisi.',
            'category.required' => 'Kategori komunitas wajib dipilih.',
            'description.required' => 'Deskripsi komunitas wajib diisi.',
            'logo.required' => 'Logo komunitas wajib diunggah.',
            'logo.image' => 'Logo harus berupa berkas gambar (JPG, PNG, WebP, SVG).',
            'logo.max' => 'Ukuran berkas logo maksimal 2MB.',
            'banner.image' => 'Banner harus berupa berkas gambar (JPG, PNG, WebP, SVG).',
            'banner.max' => 'Ukuran berkas banner maksimal 4MB.',
            'type.required' => 'Jenis komunitas wajib dipilih.',
            'language.required' => 'Bahasa utama wajib diisi.',
        ]);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('communities', 'public');
            $logoUrl = Storage::disk('public')->url($path);
        }

        $coverUrl = null;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('communities', 'public');
            $coverUrl = Storage::disk('public')->url($path);
        }

        $normalizedType = in_array(strtolower($validated['type']), ['privat', 'private'], true) ? 'private' : 'public';
        $normalizedMode = strtolower($validated['mode'] ?? 'online');
        if (! in_array($normalizedMode, ['online', 'offline', 'hybrid'], true)) {
            $normalizedMode = 'online';
        }

        $tags = $this->parseTags($request->input('tags'));

        $community = Community::create([
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'logo_url' => $logoUrl,
            'cover_url' => $coverUrl,
            'type' => $normalizedType,
            'mode' => $normalizedMode,
            'location' => $validated['location'] ?? null,
            'language' => $validated['language'],
            'rules' => $validated['rules'] ?? null,
            'tags' => $tags,
            'status' => 'pending',
            'approval_status' => 'pending',
            'members' => $request->user() ? 1 : 0,
        ]);

        if ($request->user()) {
            $community->members()->attach($request->user()->id, [
                'role' => 'admin',
                'is_banned' => false,
            ]);
        }

        return redirect()->route('community.my')
            ->with('success', "Komunitas \"{$community->name}\" berhasil diajukan dan sedang menunggu persetujuan Super Admin.");
    }

    public function my(Request $request): View
    {
        $myCommunities = Community::where('user_id', $request->user()->id)
            ->withCount('members')
            ->latest()
            ->paginate(12);

        return view('community.my', [
            'communities' => $myCommunities,
        ]);
    }

    public function manage(Request $request, Community $community): View
    {
        abort_unless(
            $community->canModerate($request->user()),
            403,
            'Anda tidak memiliki hak akses untuk mengelola komunitas ini.'
        );

        $isOwner = $community->isOwner($request->user());
        $userRole = $community->getUserRole($request->user());

        $members = $community->members()
            ->orderByPivot('role')
            ->get();

        $categories = ['Technology', 'Design', 'Business', 'Education', 'Science', 'Other'];

        return view('community.manage', [
            'community' => $community,
            'members' => $members,
            'categories' => $categories,
            'isOwner' => $isOwner,
            'userRole' => $userRole,
        ]);
    }

    public function updateSettings(Request $request, Community $community): RedirectResponse
    {
        abort_unless(
            $community->user_id === $request->user()->id,
            403,
            'Anda tidak memiliki hak akses untuk mengelola komunitas ini.'
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1500'],
            'status' => ['required', 'in:active,inactive,pending,rejected'],
            'type' => ['required', 'in:public,private,publik,privat'],
            'mode' => ['nullable', 'in:online,offline,hybrid,Online,Offline,Hybrid'],
            'location' => ['nullable', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:100'],
            'rules' => ['nullable', 'string', 'max:3000'],
            'resubmit_notes' => ['nullable', 'string', 'max:2000'],
            'tags' => ['nullable'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('communities', 'public');
            $validated['logo_url'] = Storage::disk('public')->url($path);
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('communities', 'public');
            $validated['cover_url'] = Storage::disk('public')->url($path);
        }

        $validated['type'] = in_array(strtolower($validated['type']), ['privat', 'private'], true) ? 'private' : 'public';
        if (! empty($validated['mode'])) {
            $validated['mode'] = strtolower($validated['mode']);
        }

        if ($request->has('tags')) {
            $validated['tags'] = $this->parseTags($request->input('tags'));
        }

        if ($request->boolean('resubmit') || $request->input('action') === 'resubmit') {
            $validated['approval_status'] = 'pending';
            $validated['status'] = 'pending';
            $validated['resubmit_notes'] = $request->input('resubmit_notes');
            $validated['rejected_by'] = null;
            $validated['rejected_at'] = null;
        }

        unset($validated['logo'], $validated['banner'], $validated['resubmit'], $validated['action']);

        $community->update($validated);

        $msg = ($community->approval_status === 'pending')
            ? "Komunitas \"{$community->name}\" berhasil diajukan kembali dan sedang menunggu persetujuan Super Admin."
            : "Pengaturan komunitas \"{$community->name}\" berhasil diperbarui.";

        return back()->with('success', $msg);
    }

    public function kickMember(Request $request, Community $community, User $user): RedirectResponse|JsonResponse
    {
        abort_unless(
            $community->canKickMember($request->user(), $user),
            403,
            'Anda tidak memiliki hak akses untuk mengeluarkan anggota ini.'
        );

        $detached = DB::transaction(function () use ($community, $user) {
            $locked = Community::whereKey($community->id)->lockForUpdate()->firstOrFail();
            $count = $locked->members()->detach($user->id);
            if ($count > 0 && $locked->members > 0) {
                $locked->decrement('members');
            }

            return $count > 0;
        });

        if ($detached) {
            Log::info('Anggota komunitas dikeluarkan (kicked)', [
                'actor_id' => $request->user()->id,
                'actor_name' => $request->user()->name,
                'target_id' => $user->id,
                'target_name' => $user->name,
                'community_id' => $community->id,
                'community_name' => $community->name,
                'kicked_at' => now()->toIso8601String(),
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            if ($detached) {
                return response()->json([
                    'success' => true,
                    'message' => "Pengguna {$user->name} berhasil dikeluarkan dari komunitas.",
                    'members_count' => (int) $community->fresh()->members,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Pengguna tersebut bukan anggota komunitas ini.',
            ], 422);
        }

        return back()->with(
            $detached ? 'success' : 'error',
            $detached
                ? "Pengguna {$user->name} berhasil dikeluarkan dari komunitas."
                : 'Pengguna tersebut bukan anggota komunitas ini.'
        );
    }

    public function updateMemberRole(Request $request, Community $community, User $user): RedirectResponse
    {
        abort_unless(
            $community->canAssignRoles($request->user()),
            403,
            'Hanya pemilik komunitas yang dapat mengubah peran (role) anggota.'
        );

        if ($user->id === $community->user_id) {
            return back()->with('error', 'Role pemilik komunitas tidak dapat diubah.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:member,moderator,admin'],
        ]);

        $member = $community->members()->where('user_id', $user->id)->first();
        if (! $member) {
            return back()->with('error', 'Pengguna tersebut bukan anggota komunitas ini.');
        }

        $community->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        $roleLabels = [
            'member' => 'Anggota',
            'moderator' => 'Moderator',
            'admin' => 'Admin',
        ];
        $roleName = $roleLabels[$validated['role']] ?? $validated['role'];

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$roleName}.");
    }

    public function toggleBanMember(Request $request, Community $community, User $user): JsonResponse|RedirectResponse
    {
        abort_unless(
            $community->canBanMember($request->user(), $user),
            403,
            'Anda tidak memiliki hak akses untuk memblokir anggota ini.'
        );

        if ($user->id === $community->user_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pembuat komunitas tidak dapat diblokir.'], 403);
            }

            return back()->with('error', 'Pembuat komunitas tidak dapat diblokir.');
        }

        $member = $community->members()->where('user_id', $user->id)->first();
        if ($member) {
            $newBan = ! (bool) $member->pivot->is_banned;
            $community->members()->updateExistingPivot($user->id, [
                'is_banned' => $newBan,
            ]);
            $msg = $newBan
                ? "Pengguna {$user->name} berhasil diblokir dari komunitas."
                : "Blokir untuk {$user->name} telah dicabut.";
        } else {
            $community->members()->attach($user->id, [
                'role' => 'member',
                'is_banned' => true,
            ]);
            $msg = "Pengguna {$user->name} berhasil diblokir dari komunitas.";
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'members_count' => $community->fresh()->members()->wherePivot('is_banned', false)->count(),
            ]);
        }

        return back()->with('success', $msg);
    }

    public function moderatePost(Request $request, Community $community, CommunityPost $post): RedirectResponse
    {
        abort_unless(
            $community->canModerate($request->user()),
            403,
            'Anda tidak memiliki hak akses untuk memoderasi postingan ini.'
        );
        abort_unless($post->community_id === $community->id, 404);

        $action = $request->input('action');
        if ($action === 'delete') {
            $post->delete();
            $msg = 'Postingan berhasil dihapus.';
        } elseif ($action === 'hide') {
            $post->update(['status' => 'hidden']);
            $msg = 'Postingan berhasil disembunyikan.';
        } elseif ($action === 'publish') {
            $post->update(['status' => 'published']);
            $msg = 'Postingan berhasil dipublikasikan kembali.';
        } else {
            return back()->with('error', 'Aksi moderasi tidak valid.');
        }

        return back()->with('success', $msg);
    }

    public function storePost(Request $request, Community $community): RedirectResponse
    {
        abort_unless($community->status === 'active' && $community->approval_status === 'approved', 404);

        $membership = $community->members()->where('user_id', $request->user()->id)->first();
        if ($membership && $membership->pivot->is_banned) {
            return back()->with('error', 'Anda diblokir dan tidak dapat memposting di komunitas ini.');
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $community->posts()->create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'status' => 'published',
        ]);

        return back()->with('success', 'Postingan Anda berhasil diterbitkan.');
    }

    public function show(Request $request, Community $community): View
    {
        $isApproved = $community->status === 'active' && $community->approval_status === 'approved';
        $canPreview = $request->user() && ($request->user()->role === 'super_admin' || $community->user_id === $request->user()->id);

        abort_unless($isApproved || $canPreview, 404);

        $isMember = $request->user()
            && $community->members()->whereKey($request->user()->id)->exists();

        $isOwnerOrAdmin = $request->user()
            && $community->isOwnerOrAdmin($request->user());

        $canModerate = $request->user()
            && $community->canModerate($request->user());

        return view('community.show', [
            'community' => $community,
            'isMember' => $isMember,
            'isOwnerOrAdmin' => $isOwnerOrAdmin,
            'canModerate' => $canModerate,
            'memberUsers' => $community->members()
                ->orderByDesc('community_user.created_at')
                ->limit(12)
                ->get(),
        ]);
    }

    public function join(Request $request, Community $community): RedirectResponse
    {
        abort_unless($community->status === 'active' && $community->approval_status === 'approved', 404);

        $isBanned = $community->members()
            ->where('user_id', $request->user()->id)
            ->wherePivot('is_banned', true)
            ->exists();

        if ($isBanned) {
            return back()->with('error', 'Anda telah diblokir (banned) dari komunitas ini.');
        }

        $joined = DB::transaction(function () use ($request, $community) {
            $locked = Community::whereKey($community->id)->lockForUpdate()->firstOrFail();

            if ($locked->members()->whereKey($request->user()->id)->exists()) {
                return false;
            }

            $locked->members()->attach($request->user()->id);
            $locked->increment('members');

            return true;
        });

        return back()->with(
            $joined ? 'success' : 'error',
            $joined
                ? "Anda berhasil bergabung dengan \"{$community->name}\"."
                : 'Anda sudah menjadi anggota komunitas ini.'
        );
    }

    public function leave(Request $request, Community $community): RedirectResponse
    {
        // Pemilik/pembuat komunitas tidak boleh keluar dari komunitasnya sendiri
        if ($community->user_id === $request->user()->id) {
            return back()->with('error', 'Sebagai pemilik komunitas, Anda tidak dapat keluar dari komunitas ini.');
        }

        $left = DB::transaction(function () use ($request, $community) {
            $locked = Community::whereKey($community->id)->lockForUpdate()->firstOrFail();
            $detached = $locked->members()->detach($request->user()->id);

            if ($detached > 0 && $locked->members > 0) {
                $locked->decrement('members');
            }

            return $detached > 0;
        });

        return back()->with(
            $left ? 'success' : 'error',
            $left
                ? "Anda keluar dari \"{$community->name}\"."
                : 'Anda belum menjadi anggota komunitas ini.'
        );
    }

    public function resubmit(Request $request, Community $community): RedirectResponse
    {
        abort_unless(
            $community->user_id === $request->user()->id,
            403,
            'Anda tidak memiliki hak akses untuk mengajukan kembali komunitas ini.'
        );

        $validated = $request->validate([
            'resubmit_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $community->update([
            'approval_status' => 'pending',
            'status' => 'pending',
            'resubmit_notes' => $validated['resubmit_notes'] ?? null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return redirect()->route('community.my')
            ->with('success', "Pengajuan komunitas \"{$community->name}\" berhasil dikirim ulang beserta catatan perbaikan dan sedang menunggu persetujuan Super Admin.");
    }

    /**
     * Parse and clean tags input into an array of trimmed tags.
     *
     * @return array<int, string>|null
     */
    protected function parseTags(mixed $tagsInput): ?array
    {
        if (empty($tagsInput)) {
            return null;
        }

        if (is_string($tagsInput)) {
            $tags = array_values(array_filter(array_map(
                fn ($t) => trim(ltrim(trim($t), '#')),
                explode(',', $tagsInput)
            )));
        } elseif (is_array($tagsInput)) {
            $tags = array_values(array_filter(array_map(
                fn ($t) => trim(ltrim(trim((string) $t), '#')),
                $tagsInput
            )));
        } else {
            return null;
        }

        $tags = array_slice($tags, 0, 10);

        return ! empty($tags) ? $tags : null;
    }
}
