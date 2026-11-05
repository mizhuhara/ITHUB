<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\UserReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCompanyController extends Controller
{
    /**
     * Display a listing of companies with search & filter support.
     * Role company langsung diarahkan ke edit perusahaannya atau create jika belum ada.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Akun perusahaan langsung diarahkan ke data perusahaannya
        if ($user->role === 'company') {
            $ownedCompany = Company::where('user_id', $user->id)->first();

            if ($ownedCompany) {
                return redirect()->route('admin.company.edit', $ownedCompany->id)
                    ->with('info', 'Silakan perbarui informasi perusahaan Anda di bawah ini.');
            }

            return redirect()->route('admin.company.create')
                ->with('info', 'Lengkapi data perusahaan Anda untuk mulai menampilkan profil perusahaan.');
        }

        $query = Company::query();

        // Search Filter (Nama, NPSN/Kode, Kota, Provinsi, Industri, Lokasi)
        if ($request->filled('search')) {
            $search = strtolower(trim($request->input('search')));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(npsn) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(province) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(industry) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(location) LIKE ?', ["%{$search}%"]);
            });
        }

        // Status Filter
        if ($request->filled('status') && strtolower($request->input('status')) !== 'all') {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->input('status'))]);
        }

        // Type Filter (Swasta, BUMN, Multinasional, Startup)
        if ($request->filled('type') && strtolower($request->input('type')) !== 'all') {
            $query->whereRaw('LOWER(type) = ?', [strtolower($request->input('type'))]);
        }

        // Sort Filter
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

        $totalCompanies = (clone $query)->count();
        $activePartners = (clone $query)->whereRaw('LOWER(status) = ?', ['active'])->count();
        $newSubmissions = (clone $query)->where('created_at', '>=', now()->subDays(30))->count();

        $companies = $query->paginate(6)->withQueryString();

        return view('admin.company.index', [
            'companies' => $companies,
            'totalCompanies' => $totalCompanies,
            'activePartners' => $activePartners,
            'newSubmissions' => $newSubmissions,
            'canManage' => in_array($request->user()->role, ['super_admin', 'admin'], true),
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        // Jika role company sudah punya perusahaan, arahkan ke edit
        if ($user->role === 'company') {
            $existing = Company::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.company.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data perusahaan. Silakan edit informasi berikut.');
            }
        } else {
            abort_if($user->role !== 'super_admin' && $user->role !== 'admin', 403, 'Akses ditolak. Hanya admin.');
        }

        return view('admin.company.create');
    }

    /**
     * Store a newly created company in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Role company: hanya boleh buat 1 perusahaan, otomatis terikat ke akunnya
        if ($user->role === 'company') {
            $existing = Company::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('admin.company.edit', $existing->id)
                    ->with('info', 'Anda sudah memiliki data perusahaan.');
            }
        } else {
            abort_if(! in_array($user->role, ['super_admin', 'admin'], true), 403, 'Akses ditolak. Hanya admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:50', 'unique:companies,npsn'],
            'type' => ['required', 'string'],
            'industry' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'total_employees' => ['nullable', 'integer', 'min:0'],
            'founded' => ['nullable', 'integer', 'min:1800', 'max:2030'],
            'projects_completed' => ['nullable', 'string', 'max:100'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:255'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:100'],
            'status' => [$user->role === 'company' ? 'nullable' : 'required', 'string'],
            'map_link' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:4'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'existing_gallery' => ['nullable', 'array', 'max:4'],
            'existing_gallery.*' => ['string'],
        ], [
            'name.required' => 'Nama perusahaan wajib diisi.',
            'industry.required' => 'Bidang industri wajib diisi.',
            'website.url' => 'Format URL website tidak valid. Sertakan http:// atau https://',
        ]);

        // Build location if not explicitly provided
        $location = trim($validated['location'] ?? '');
        if (empty($location)) {
            $parts = array_filter([$validated['city'] ?? null, $validated['province'] ?? null]);
            $location = ! empty($parts) ? implode(', ', $parts) : 'Indonesia';
        }

        // Process logo upload
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company-logos', 'public');
            $logoUrl = Storage::url($path);
        }

        // Process gallery images upload
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('company-gallery', 'public');
                $galleryPaths[] = Storage::url($path);
            }
        }
        $galleryPaths = array_values(array_slice($galleryPaths, 0, 4));

        // Normalize Google Maps embed url if link provided or build from coordinates
        $mapLink = $this->formatMapLink($validated['map_link'] ?? null);
        if (empty($mapLink) && ! empty($validated['latitude']) && ! empty($validated['longitude'])) {
            $mapLink = "https://www.google.com/maps?q={$validated['latitude']},{$validated['longitude']}";
        }

        // Standardize status: Active / Inactive
        $user = $request->user();
        $isInstitution = in_array($user->role, ['school', 'university', 'company'], true);

        // Institusi: 1 akun = 1 data, wajib pending sampai di-approve admin
        if ($isInstitution && Company::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['name' => 'Akun Anda sudah memiliki data perusahaan. Hanya diperbolehkan 1 data per akun.']);
        }

        $status = $isInstitution ? 'Pending' : (strtolower($validated['status'] ?? 'active') === 'inactive' ? 'Inactive' : 'Active');

        $company = Company::create([
            'name' => $validated['name'],
            'npsn' => $validated['npsn'] ?? null,
            'type' => $validated['type'],
            'industry' => $validated['industry'],
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'location' => $location,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'total_employees' => $validated['total_employees'] ?? null,
            'founded' => $validated['founded'] ?? null,
            'projects_completed' => $validated['projects_completed'] ?? null,
            'specialties' => ! empty($validated['specialties']) ? array_values(array_filter($validated['specialties'])) : null,
            'tech_stack' => ! empty($validated['tech_stack']) ? array_values(array_filter($validated['tech_stack'])) : null,
            'status' => $status,
            'logo_url' => $logoUrl,
            'gallery' => $galleryPaths,
            'map_link' => $mapLink,
            'user_id' => $isInstitution ? $user->id : null,
        ]);

        if ($user->role === 'company') {
            return redirect()->route('admin.company.edit', $company->id)
                ->with('success', "Data perusahaan \"{$company->name}\" berhasil disimpan. Anda bisa melengkapi informasi lebih lanjut di sini.");
        }

        return redirect()->route('admin.company.index')
            ->with('success', "Perusahaan \"{$company->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified company.
     */
    public function show(int $id): View
    {
        $company = $this->findVisibleCompany($id);

        return view('admin.company.show', compact('company'));
    }

    private function visibleCompanies()
    {
        $user = auth()->user();
        if ($user && $user->role === 'company') {
            return Company::where('user_id', $user->id);
        }

        return Company::query();
    }

    /**
     * Ambil perusahaan dengan proteksi kepemilikan (404 kalau bukan miliknya).
     */
    private function findVisibleCompany(int $id): Company
    {
        return $this->visibleCompanies()->findOrFail($id);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(int $id): View
    {
        $company = $this->findVisibleCompany($id);

        return view('admin.company.edit', compact('company'));
    }

    /**
     * Update the specified company in database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $company = $this->findVisibleCompany($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:50', Rule::unique('companies', 'npsn')->ignore($company->id)],
            'type' => ['required', 'string'],
            'industry' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'total_employees' => ['nullable', 'integer', 'min:0'],
            'founded' => ['nullable', 'integer', 'min:1800', 'max:2030'],
            'projects_completed' => ['nullable', 'string', 'max:100'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:255'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:100'],
            'status' => [$request->user()->role === 'company' ? 'nullable' : 'required', 'string'],
            'map_link' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:4'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'existing_gallery' => ['nullable', 'array', 'max:4'],
            'existing_gallery.*' => ['string'],
            'remove_gallery' => ['nullable', 'array'],
        ], [
            'name.required' => 'Nama perusahaan wajib diisi.',
            'industry.required' => 'Bidang industri wajib diisi.',
            'website.url' => 'Format URL website tidak valid. Sertakan http:// atau https://',
        ]);

        // Build location if not explicitly provided
        $location = trim($validated['location'] ?? '');
        if (empty($location)) {
            $parts = array_filter([$validated['city'] ?? null, $validated['province'] ?? null]);
            $location = ! empty($parts) ? implode(', ', $parts) : ($company->location ?: 'Indonesia');
        }

        $status = strtolower($validated['status'] ?? 'active') === 'inactive' ? 'Inactive' : 'Active';
        $user = $request->user();
        if (in_array($user->role, ['school', 'university', 'company'], true)) {
            $status = $company->status; // institusi tidak boleh mengubah status
        }

        // Normalize map link or generate from coordinates
        $mapLink = $this->formatMapLink($validated['map_link'] ?? null);
        if (empty($mapLink) && ! empty($validated['latitude']) && ! empty($validated['longitude'])) {
            $mapLink = "https://www.google.com/maps?q={$validated['latitude']},{$validated['longitude']}";
        }

        $payload = [
            'name' => $validated['name'],
            'npsn' => $validated['npsn'] ?? null,
            'type' => $validated['type'],
            'industry' => $validated['industry'],
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'location' => $location,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'total_employees' => $validated['total_employees'] ?? null,
            'founded' => $validated['founded'] ?? null,
            'projects_completed' => $validated['projects_completed'] ?? null,
            'specialties' => ! empty($validated['specialties']) ? array_values(array_filter($validated['specialties'])) : null,
            'tech_stack' => ! empty($validated['tech_stack']) ? array_values(array_filter($validated['tech_stack'])) : null,
            'status' => $status,
            'map_link' => $mapLink,
        ];

        // Handle logo update
        if ($request->hasFile('logo')) {
            if ($company->logo_url && str_contains($company->logo_url, 'storage/company-logos/')) {
                $oldPath = str_replace('/storage/', '', parse_url($company->logo_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('company-logos', 'public');
            $payload['logo_url'] = Storage::url($path);
        }

        // Handle gallery updates: keep retained existing ones + add newly uploaded ones
        $gallery = $request->input('existing_gallery', []);
        if (! is_array($gallery)) {
            $gallery = [];
        }

        // Also support remove_gallery for backward compatibility
        if (! empty($validated['remove_gallery'])) {
            $gallery = array_values(array_diff($gallery, $validated['remove_gallery']));
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('company-gallery', 'public');
                $gallery[] = Storage::url($path);
            }
        }

        $payload['gallery'] = array_values(array_slice($gallery, 0, 4));

        $company->update($payload);

        if ($user->role === 'company') {
            return redirect()->route('admin.company.edit', $company->id)
                ->with('success', "Data perusahaan \"{$company->name}\" berhasil diperbarui.");
        }

        return redirect()->route('admin.company.index')
            ->with('success', "Data perusahaan \"{$company->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(auth()->user()->role !== 'super_admin' && auth()->user()->role !== 'admin', 403, 'Akses ditolak. Hanya admin.');

        $company = $this->findVisibleCompany($id);
        $name = $company->name;

        // Delete logo and gallery files if present in public storage
        if ($company->logo_url && str_contains($company->logo_url, 'storage/company-logos/')) {
            $path = str_replace('/storage/', '', parse_url($company->logo_url, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        if (! empty($company->gallery)) {
            foreach ($company->gallery as $imgUrl) {
                if (str_contains($imgUrl, 'storage/company-gallery/')) {
                    $path = str_replace('/storage/', '', parse_url($imgUrl, PHP_URL_PATH));
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $company->delete();

        return redirect()->route('admin.company.index')
            ->with('success', "Perusahaan \"{$name}\" berhasil dihapus.");
    }

    /**
     * Helper to format Google Maps iframe or link URL.
     */
    private function formatMapLink(?string $link): ?string
    {
        if (! $link) {
            return null;
        }

        $link = trim($link);

        // If user pastes an <iframe> tag, extract src attribute
        if (str_contains($link, '<iframe')) {
            preg_match('/src="([^"]+)"/', $link, $matches);
            $link = $matches[1] ?? $link;
        }

        // Hanya izinkan http/https, mencegah javascript: dkk.
        $scheme = strtolower((string) parse_url($link, PHP_URL_SCHEME));
        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        // If Google Maps share link, convert query to embed format
        $host = strtolower((string) parse_url($link, PHP_URL_HOST));
        $isGoogleMapsWeb = in_array($host, ['maps.google.com', 'www.google.com', 'google.com'], true)
            && str_contains($link, '/maps');

        if ($isGoogleMapsWeb && ! str_contains($link, 'output=embed') && ! str_contains($link, '/embed')) {
            $link = str_contains($link, '?')
                ? $link.'&output=embed'
                : $link.'?output=embed';
        }

        return $link;
    }

    /**
     * Tampilkan halaman formulir pelaporan bug / masalah untuk admin perusahaan.
     */
    public function reportBug(): View
    {
        return view('admin.company.report_bug');
    }

    /**
     * Simpan laporan bug dari admin perusahaan.
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

        return redirect()->route('admin.company.report-bug')
            ->with('success', 'Laporan masalah berhasil dikirim ke tim IndoTech. Tim kami akan segera meninjaunya.');
    }
}
