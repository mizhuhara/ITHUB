<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\UserReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminSchoolController extends Controller
{
    /**
     * Display a listing of schools with search/filter support.
     * For school role: redirect directly to their school edit/create page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Akun sekolah langsung diarahkan ke data sekolahnya
        if ($user->role === 'school') {
            $ownedSchool = School::where('user_id', $user->id)->first();

            if ($ownedSchool) {
                return redirect()->route('admin.schools.edit', $ownedSchool->id)
                    ->with('info', 'Silakan perbarui informasi sekolah Anda di bawah ini.');
            }

            return redirect()->route('admin.schools.create')
                ->with('info', 'Lengkapi data sekolah Anda untuk mulai menampilkan profil sekolah.');
        }

        $query = $this->visibleSchools();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('npsn', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('province', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = strtolower($request->input('status'));
            if ($status === 'active') {
                $query->whereRaw('LOWER(status) = ?', ['active']);
            } elseif ($status === 'inactive') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(status) = ?', ['inactive'])
                        ->orWhereRaw('LOWER(status) != ?', ['active'])
                        ->orWhereNull('status');
                });
            } else {
                $query->whereRaw('LOWER(status) = ?', [$status]);
            }
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->whereRaw('LOWER(institution_type) = ?', [strtolower($request->input('type'))]);
        }

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            match ($sort) {
                'name_asc' => $query->orderBy('name'),
                'name_desc' => $query->orderByDesc('name'),
                'region_asc' => $query->orderBy('city'),
                'region_desc' => $query->orderByDesc('city'),
                'oldest' => $query->orderBy('id'),
                default => $query->orderByDesc('id'),
            };
        } else {
            $query->orderByDesc('id');
        }

        $paginatedSchools = $query->paginate(4)->withQueryString();

        $visible = $this->visibleSchools();

        return view('admin.schools.index', [
            'schools' => $paginatedSchools,
            'totalSchools' => (clone $visible)->count(),
            'activePartners' => (clone $visible)->where('status', 'Active')->count(),
            'newSubmissions' => (clone $visible)->where('status', 'Inactive')->count(),
            'canManage' => in_array($user->role, ['super_admin', 'admin'], true),
        ]);
    }

    /**
     * Show the form for creating a new school.
     * Role school boleh membuat jika belum punya sekolah.
     */
    public function create(): View|RedirectResponse
    {
        $user = $this->authUser();

        // Jika role school sudah punya sekolah, arahkan ke edit
        if ($user->role === 'school') {
            $existing = School::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.schools.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data sekolah. Silakan edit informasi berikut.');
            }
        }

        $this->authorizeCreate();

        return view('admin.schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        // Role school: hanya boleh buat 1 sekolah, dan otomatis terikat ke akunnya
        if ($user->role === 'school') {
            $existing = School::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.schools.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data sekolah.');
            }
        } else {
            $this->authorizeCreate();
        }

        $data = $this->validateSchool($request);

        // Otomatis kaitkan user_id untuk akun sekolah
        if ($user->role === 'school' && empty($data['user_id'])) {
            $data['user_id'] = $user->id;
        }

        // Status default Pending untuk akun sekolah (menunggu verifikasi admin)
        if ($user->role === 'school') {
            $data['status'] = 'Pending';
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('schools/logos', 'public');
            $data['logo_url'] = Storage::url($path);
            $data['logo_name'] = $file->getClientOriginalName();
        }

        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('schools/gallery', 'public');
                $gallery[] = Storage::url($path);
            }
        }
        $data['gallery'] = array_values(array_slice($gallery, 0, 4));

        if (empty($data['logo_text'])) {
            $data['logo_text'] = mb_substr($data['name'], 0, 8);
        }

        if (! empty($data['latitude']) && ! empty($data['longitude']) && empty($data['map_link'])) {
            $data['map_link'] = "https://www.google.com/maps?q={$data['latitude']},{$data['longitude']}";
        }

        $school = School::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Sekolah \"{$data['name']}\" berhasil ditambahkan.",
            ], 201);
        }

        // Role school diarahkan ke halaman edit sekolah yang baru dibuat
        if ($user->role === 'school') {
            return redirect()->route('admin.schools.edit', $school->id)
                ->with('success', "Data sekolah \"{$data['name']}\" berhasil disimpan. Anda bisa melengkapi informasi lebih lanjut di sini.");
        }

        return redirect()->route('admin.schools.index')
            ->with('success', "Sekolah \"{$data['name']}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified school.
     */
    public function show(int $id): View
    {
        $school = $this->findVisibleSchool($id);

        return view('admin.schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(int $id): View
    {
        $school = $this->findVisibleSchool($id);

        return view('admin.schools.edit', compact('school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $school = $this->findVisibleSchool($id);
        $data = $this->validateSchool($request);
        $user = $request->user();

        // Institusi tidak boleh mengubah status — hanya admin yang approve/reject
        if (in_array($user->role, ['school', 'university', 'company'], true)) {
            unset($data['status']);
            unset($data['views']);
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if exists on public disk
            if (! empty($school->logo_url) && str_starts_with($school->logo_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $school->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('logo');
            $path = $file->store('schools/logos', 'public');
            $data['logo_url'] = Storage::url($path);
            $data['logo_name'] = $file->getClientOriginalName();
        }

        // Handle gallery images: keep retained existing ones + add newly uploaded ones
        $gallery = $request->input('existing_gallery', []);
        if (! is_array($gallery)) {
            $gallery = [];
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('schools/gallery', 'public');
                $gallery[] = Storage::url($path);
            }
        }

        $data['gallery'] = array_values(array_slice($gallery, 0, 4));

        if (empty($data['logo_text'])) {
            $data['logo_text'] = mb_substr($data['name'], 0, 8);
        }

        if (! empty($data['latitude']) && ! empty($data['longitude']) && empty($data['map_link'])) {
            $data['map_link'] = "https://www.google.com/maps?q={$data['latitude']},{$data['longitude']}";
        }

        if ($request->has('competencies')) {
            $competenciesClean = array_values(array_filter($request->input('competencies', []), fn ($c) => ! empty(trim((string) $c))));
            $data['competencies'] = $competenciesClean;
            $data['tags'] = $competenciesClean;
        }

        $school->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Data sekolah \"{$data['name']}\" berhasil diperbarui.",
            ]);
        }

        // Role school dikembalikan ke halaman edit (bukan index) agar tetap di profil sekolahnya
        if (in_array($user->role, ['school', 'university', 'company'], true)) {
            return redirect()->route('admin.schools.edit', $school->id)
                ->with('success', "Data sekolah \"{$data['name']}\" berhasil diperbarui.");
        }

        return redirect()->route('admin.schools.index')
            ->with('success', "Data sekolah \"{$data['name']}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if($this->authUser()?->role !== 'super_admin' && $this->authUser()?->role !== 'admin', 403, 'Akses ditolak. Hanya admin.');

        $school = $this->findVisibleSchool($id);
        $name = $school->name;

        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', "Sekolah \"{$name}\" berhasil dihapus.");
    }

    /**
     * Query schools yang boleh dilihat user: super_admin semua, role school hanya miliknya.
     */
    private function visibleSchools()
    {
        $user = $this->authUser();

        if ($user && $user->role === 'school') {
            return School::where('user_id', $user->id);
        }

        return School::query();
    }

    /**
     * Ambil sekolah dengan proteksi kepemilikan (404 kalau bukan miliknya).
     */
    private function findVisibleSchool(int $id): School
    {
        return $this->visibleSchools()->findOrFail($id);
    }

    private function authorizeCreate(): void
    {
        $user = $this->authUser();

        // Role school boleh membuat sekolah pertama mereka; role lain (university, company) tidak
        if (in_array($user->role, ['university', 'company'], true)) {
            abort(403, 'Akses ditolak. Akun institusi tidak dapat menambah data sekolah.');
        }
    }

    /**
     * Handle Summernote image upload — saves to storage and returns URL (avoids base64 in DB).
     */
    public function editorUpload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:25600'],
        ]);

        $path = $request->file('file')->store('editor', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }

    /**
     * Tampilkan halaman formulir pelaporan bug / masalah untuk admin sekolah.
     */
    public function reportBug(): View
    {
        return view('admin.schools.report_bug');
    }

    /**
     * Simpan laporan bug dari admin sekolah.
     */
    public function storeReportBug(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:Bug,Konten,Teknis,Lainnya'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        UserReport::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.school.report-bug')
            ->with('success', 'Laporan masalah berhasil dikirim ke tim IndoTech. Tim kami akan segera meninjaunya.');
    }

    private function validateSchool(Request $request): array
    {
        $validated = $request->validate([
            'npsn' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'institution_type' => ['required', 'string', 'max:100', 'in:SMK IT,SMK,SMA IT,SMK Non-IT,Sekolah IT,Vokasi IT'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_text' => ['nullable', 'string', 'max:50'],
            'logo_bg' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'competencies' => ['nullable', 'array'],
            'competencies.*' => ['string', 'max:100'],
            'gallery' => ['nullable', 'array', 'max:4'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'existing_gallery' => ['nullable', 'array', 'max:4'],
            'existing_gallery.*' => ['string'],
            'total_students' => ['nullable', 'integer', 'min:0'],
            'views' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'industry_partners' => ['nullable', 'integer', 'min:0'],
            'placement_rate' => ['nullable', 'string', 'max:20'],
            'modern_labs' => ['nullable', 'integer', 'min:0'],
            'founded' => ['nullable', 'string', 'max:10'],
            'accreditation' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_link' => ['nullable', 'string', 'max:500'],
        ]);

        if (empty($validated['city']) || empty($validated['province'])) {
            $locationParts = array_map('trim', explode(',', $validated['location']));
            if (empty($validated['city'])) {
                $validated['city'] = $locationParts[0] ?? $validated['location'];
            }
            if (empty($validated['province']) && isset($locationParts[1])) {
                $validated['province'] = $locationParts[1];
            }
        }

        return $validated;
    }
}
