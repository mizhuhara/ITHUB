<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminCommunityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Community::query();

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        $query->orderByDesc('id');

        if ($request->filled('sort')) {
            $query->reorder();

            match ($request->query('sort')) {
                'name_asc' => $query->orderBy('name'),
                'name_desc' => $query->orderByDesc('name'),
                'members_desc' => $query->orderByDesc('members'),
                'oldest' => $query->orderBy('id'),
                default => $query->orderByDesc('id'),
            };
        }

        return view('admin.community.index', [
            'communities' => $query->paginate(10)->withQueryString(),
            'totalCommunities' => Community::count(),
            'activeCommunities' => Community::where('status', 'active')->count(),
            'newMembers' => Community::where('created_at', '>=', now()->subDays(30))->sum('members'),
        ]);
    }

    public function create(): View
    {
        return view('admin.community.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCommunity($request);
        $validated['approval_status'] = 'approved';
        $validated['approved_by'] = $request->user()->id;
        $validated['approved_at'] = now();

        Community::create($validated);

        return redirect()->route('admin.community.index')
            ->with('success', "Komunitas \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function show(int $id): View
    {
        return view('admin.community.show', [
            'community' => Community::findOrFail($id),
        ]);
    }

    public function edit(int $id): View
    {
        return view('admin.community.edit', [
            'community' => Community::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $community = Community::findOrFail($id);
        $validated = $this->validateCommunity($request);

        $community->update($validated);

        return redirect()->route('admin.community.index')
            ->with('success', "Data komunitas \"{$community->name}\" berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $community = Community::findOrFail($id);
        $name = $community->name;

        $logoPath = parse_url((string) $community->logo_url, PHP_URL_PATH);

        if ($logoPath && str_starts_with($logoPath, '/storage/communities/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $logoPath));
        }

        $community->delete();

        return redirect()->route('admin.community.index')
            ->with('success', "Komunitas \"{$name}\" berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCommunity(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ]);

        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            $validated['logo_url'] = Storage::disk('public')->url(
                $request->file('logo')->store('communities', 'public')
            );
        }

        return $validated;
    }
}
