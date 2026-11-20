<?php

namespace App\Http\Controllers;

use App\Models\AiTool;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAiToolController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_if($this->authUser()?->role !== 'super_admin', 403, 'Akses ditolak. Hanya Super Admin yang dapat mengelola AI Info.');
    }

    public function index(Request $request): View
    {
        $this->authorizeSuperAdmin();

        $query = AiTool::query();

        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('tagline', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category') && strtolower($request->query('category')) !== 'all') {
            $query->where('category', $request->query('category'));
        }

        $tools = $query->latest('id')->paginate(15)->withQueryString();
        $categories = AiTool::query()->distinct()->pluck('category')->filter()->values()->all();

        return view('admin.ai-tools.index', compact('tools', 'categories'));
    }

    public function create(): View
    {
        $this->authorizeSuperAdmin();

        $existingCategories = AiTool::query()->distinct()->pluck('category')->filter()->values()->all();

        return view('admin.ai-tools.create', compact('existingCategories'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $this->validateTool($request);

        $tool = AiTool::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Data AI \"{$tool->name}\" berhasil ditambahkan.",
                'redirect' => route('admin.ai-tools.index'),
            ], 201);
        }

        return redirect()->route('admin.ai-tools.index')
            ->with('success', "Data AI \"{$tool->name}\" berhasil ditambahkan.");
    }

    public function edit(int $id): View
    {
        $this->authorizeSuperAdmin();

        $tool = AiTool::findOrFail($id);
        $existingCategories = AiTool::query()->distinct()->pluck('category')->filter()->values()->all();

        return view('admin.ai-tools.edit', compact('tool', 'existingCategories'));
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->authorizeSuperAdmin();

        $tool = AiTool::findOrFail($id);
        $validated = $this->validateTool($request);

        $tool->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Data AI \"{$tool->name}\" berhasil diperbarui.",
                'redirect' => route('admin.ai-tools.index'),
            ]);
        }

        return redirect()->route('admin.ai-tools.index')
            ->with('success', "Data AI \"{$tool->name}\" berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $tool = AiTool::findOrFail($id);
        $name = $tool->name;
        $tool->delete();

        return redirect()->route('admin.ai-tools.index')
            ->with('success', "Data AI \"{$name}\" berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTool(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'pricing' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'url', 'max:500'],
            'logo_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'overview' => ['nullable', 'string'],
            'what_it_does' => ['nullable', 'string'],
            'capabilities' => ['nullable', 'array'],
            'capabilities.*.title' => ['nullable', 'string', 'max:255'],
            'capabilities.*.desc' => ['nullable', 'string', 'max:1000'],
            'use_cases' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('logo_file')) {
            $path = ImageUploadService::storeAndOptimize($request->file('logo_file'), 'ai-tools');
            if ($path) {
                $data['logo_url'] = Storage::disk('public')->url($path);
            }
        }
        unset($data['logo_file']);

        // Clean capabilities: only keep entries with at least title or desc
        $cleanCapabilities = [];
        if (! empty($data['capabilities'])) {
            foreach ($data['capabilities'] as $cap) {
                $t = trim($cap['title'] ?? '');
                $d = trim($cap['desc'] ?? '');
                if ($t !== '' || $d !== '') {
                    $cleanCapabilities[] = [
                        'title' => $t,
                        'desc' => $d,
                    ];
                }
            }
        }
        $data['capabilities'] = $cleanCapabilities;

        // Process use_cases (lines from textarea)
        $cleanUseCases = [];
        if (! empty($data['use_cases'])) {
            $lines = preg_split('/\r\n|\r|\n/', (string) $data['use_cases']);
            foreach ($lines as $line) {
                $line = trim(ltrim(trim($line), '-*•'));
                if ($line !== '') {
                    $cleanUseCases[] = $line;
                }
            }
        }
        $data['use_cases'] = $cleanUseCases;

        $data['icon_type'] = $data['icon_type'] ?? 'general';
        $data['icon_bg'] = 'bg-blue-50 text-blue-600';
        $data['action_type'] = 'detail';

        return $data;
    }
}
