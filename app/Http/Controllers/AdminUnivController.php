<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\UserReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUnivController extends Controller
{
    /**
     * Display a listing of universities with search/filter support.
     * For university role: redirect directly to their university edit/create page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Akun universitas langsung diarahkan ke data universitasnya
        if ($user->role === 'university') {
            $ownedUniv = University::where('user_id', $user->id)->first();

            if ($ownedUniv) {
                return redirect()->route('admin.univ.edit', $ownedUniv->id)
                    ->with('info', 'Silakan perbarui informasi universitas Anda di bawah ini.');
            }

            return redirect()->route('admin.univ.create')
                ->with('info', 'Lengkapi data universitas Anda untuk mulai menampilkan profil universitas.');
        }

        $query = $this->visibleUnivs();
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(npsn) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(province) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(location) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->input('status'))]);
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->whereRaw('LOWER(type) = ?', [strtolower($request->input('type'))]);
        }

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'name_asc') {
                $query->orderBy('name', 'asc');
            } elseif ($sort === 'name_desc') {
                $query->orderBy('name', 'desc');
            } elseif ($sort === 'region_asc') {
                $query->orderBy('city', 'asc');
            } elseif ($sort === 'region_desc') {
                $query->orderBy('city', 'desc');
            } elseif ($sort === 'oldest') {
                $query->orderBy('id', 'asc');
            } else {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $totalUnivs = (clone $query)->count();
        $activePartners = (clone $query)->whereRaw('LOWER(status) = ?', ['active'])->count();
        $newSubmissions = (clone $query)->where('created_at', '>=', now()->subDays(30))->count();

        $univs = $query->paginate(6)->withQueryString();

        return view('admin.univ.index', [
            'univs' => $univs,
            'totalUnivs' => $totalUnivs,
            'activePartners' => $activePartners,
            'newSubmissions' => $newSubmissions,
            'canManage' => in_array($request->user()->role, ['super_admin', 'admin'], true),
        ]);
    }

    /**
     * Show the form for creating a new university.
     * Role university boleh membuat jika belum punya data universitas.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        // Jika role university sudah punya universitas, arahkan ke edit
        if ($user->role === 'university') {
            $existing = University::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.univ.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data universitas. Silakan edit informasi berikut.');
            }
        }

        $this->authorizeCreate();

        return view('admin.univ.create');
    }

    /**
     * Store a newly created university in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Role university: hanya boleh buat 1 universitas, dan otomatis terikat ke akunnya
        if ($user->role === 'university') {
            $existing = University::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.univ.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data universitas.');
            }
        } else {
            $this->authorizeCreate();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['required', 'string', 'max:20', 'unique:universities,npsn'],
            'type' => ['required', 'string'],
            'location' => ['required', 'string', 'max:150'],
            'accreditation' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_link' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:5120'],
        ]);

        $locations = array_map('trim', explode(',', $validated['location']));
        $city = $locations[0] ?? $validated['location'];
        $province = $locations[1] ?? '';

        $words = explode(' ', $validated['name']);
        $logoText = strtoupper(substr($words[0] ?? 'UNIV', 0, 3));

        $isInstitution = in_array($user->role, ['school', 'university', 'company'], true);

        // Institusi: 1 akun = 1 data, wajib pending sampai di-approve admin
        if ($isInstitution && University::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['name' => 'Akun Anda sudah memiliki data universitas. Hanya diperbolehkan 1 data per akun.']);
        }

        $data = [
            'name' => $validated['name'],
            'npsn' => $validated['npsn'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'city' => $city,
            'province' => $province,
            'accreditation' => $validated['accreditation'] ?? 'A',
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'map_link' => $validated['map_link'] ?? null,
            'status' => $isInstitution ? 'Pending' : 'Active',
            'logo_text' => $logoText,
            'logo_bg' => 'bg-blue-700',
            'gallery' => [],
        ];
        if ($isInstitution) {
            $data['user_id'] = $user->id;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('universities/logos', 'public');
            $data['logo_url'] = Storage::url($path);
            $data['logo_name'] = $file->getClientOriginalName();
        }

        if ($request->hasFile('gallery')) {
            $galleryUrls = [];
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('universities/gallery', 'public');
                $galleryUrls[] = Storage::url($path);
            }
            $data['gallery'] = array_values(array_slice($galleryUrls, 0, 4));
        }

        $univ = University::create($data);

        if ($user->role === 'university') {
            return redirect()->route('admin.univ.edit', $univ->id)
                ->with('success', "Data universitas \"{$validated['name']}\" berhasil dibuat dan sedang menunggu verifikasi admin.");
        }

        return redirect()->route('admin.univ.index')
            ->with('success', "Universitas \"{$validated['name']}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified university.
     */
    public function show(int $id): View
    {
        $univ = $this->findVisibleUniv($id);

        return view('admin.univ.show', compact('univ'));
    }

    private function authorizeCreate(): void
    {
        $user = auth()->user();

        if (! in_array($user->role, ['super_admin', 'admin', 'university'], true)) {
            abort(403, 'Akses ditolak. Akun ini tidak dapat menambah data universitas.');
        }
    }

    private function visibleUnivs()
    {
        $user = auth()->user();
        if ($user && $user->role === 'university') {
            return University::where('user_id', $user->id);
        }

        return University::query();
    }

    /**
     * Ambil universitas dengan proteksi kepemilikan (404 kalau bukan miliknya).
     */
    private function findVisibleUniv(int $id): University
    {
        return $this->visibleUnivs()->findOrFail($id);
    }

    /**
     * Show the form for editing the specified university.
     */
    public function edit(int $id): View
    {
        $univ = $this->findVisibleUniv($id);

        return view('admin.univ.edit', compact('univ'));
    }

    /**
     * Update the specified university in database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $univ = $this->findVisibleUniv($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['required', 'string', 'max:20', Rule::unique('universities', 'npsn')->ignore($id)],
            'type' => ['required', 'string'],
            'location' => ['required', 'string', 'max:150'],
            'accreditation' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'total_students' => ['nullable', 'integer', 'min:0'],
            'total_faculties' => ['nullable', 'integer', 'min:0'],
            'founded' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_link' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:4'],
            'gallery.*' => ['image', 'max:5120'],
            'existing_gallery' => ['nullable', 'array', 'max:4'],
            'existing_gallery.*' => ['string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
        ]);

        $locationParts = array_map('trim', explode(',', $validated['location']));
        $city = $locationParts[0] ?? $validated['location'];
        $province = $locationParts[1] ?? '';

        // Build logo_text from name initials
        $words = explode(' ', $validated['name']);
        $logoText = mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 4))));

        $data = [
            'name' => $validated['name'],
            'npsn' => $validated['npsn'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'city' => $city,
            'province' => $province,
            'accreditation' => $validated['accreditation'] ?? $univ->accreditation,
            'website' => $validated['website'] ?? $univ->website,
            'email' => $validated['email'] ?? $univ->email,
            'phone' => $validated['phone'] ?? $univ->phone,
            'total_students' => $validated['total_students'] ?? $univ->total_students,
            'total_faculties' => $validated['total_faculties'] ?? $univ->total_faculties,
            'founded' => $validated['founded'] ?? $univ->founded,
            'description' => $validated['description'] ?? $univ->description,
            'address' => $validated['address'] ?? $univ->address,
            'latitude' => $validated['latitude'] ?? $univ->latitude,
            'longitude' => $validated['longitude'] ?? $univ->longitude,
            'map_link' => $validated['map_link'] ?? $univ->map_link,
            'logo_text' => $logoText,
            'status' => 'Active',
            'tags' => array_values(array_filter($validated['tags'] ?? [], fn ($t) => ! empty(trim($t)))),
        ];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('universities/logos', 'public');
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
                $path = $image->store('universities/gallery', 'public');
                $gallery[] = Storage::url($path);
            }
        }

        $data['gallery'] = array_values(array_slice($gallery, 0, 4));

        $univ->update($data);

        return redirect()->route('admin.univ.edit', $univ->id)
            ->with('success', "Data universitas \"{$validated['name']}\" berhasil diperbarui.");
    }

    /**
     * Tampilkan halaman formulir pelaporan bug / masalah untuk admin universitas.
     */
    public function reportBug(): View
    {
        return view('admin.univ.report_bug');
    }

    /**
     * Simpan laporan bug dari admin universitas.
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
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.univ.report-bug')
            ->with('success', 'Laporan masalah berhasil dikirim ke tim IndoTech. Tim kami akan segera meninjaunya.');
    }

    /**
     * Remove the specified university from database.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(auth()->user()->role !== 'super_admin' && auth()->user()->role !== 'admin', 403, 'Akses ditolak. Hanya admin.');

        $univ = $this->findVisibleUniv($id);
        $name = $univ->name;
        $univ->delete();

        return redirect()->route('admin.univ.index')
            ->with('success', "Universitas \"{$name}\" berhasil dihapus.");
    }
}
