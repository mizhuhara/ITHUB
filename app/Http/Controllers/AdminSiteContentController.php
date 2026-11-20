<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSiteContentController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_if($this->authUser()?->role !== 'super_admin', 403, 'Akses ditolak. Hanya Super Admin yang dapat mengelola konten teks situs.');
    }

    public function index(): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.site-content.index', [
            'settings' => AppSetting::getSiteContentSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            // Beranda (Home)
            'home_hero_title' => ['required', 'string', 'max:255'],
            'home_hero_subtitle' => ['required', 'string', 'max:1000'],

            // Pendidikan - Direktori SMK
            'education_smk_title' => ['required', 'string', 'max:255'],
            'education_smk_subtitle' => ['required', 'string', 'max:1000'],

            // Pendidikan - Perguruan Tinggi (Kampus)
            'campus_title' => ['required', 'string', 'max:255'],
            'campus_subtitle' => ['required', 'string', 'max:1000'],

            // Industri
            'industry_title' => ['required', 'string', 'max:255'],
            'industry_subtitle' => ['required', 'string', 'max:1000'],

            // Komunitas
            'community_title' => ['required', 'string', 'max:255'],
            'community_subtitle' => ['required', 'string', 'max:1000'],

            // Pusat Pengetahuan (Knowledge Hub)
            'knowledge_title' => ['required', 'string', 'max:255'],
            'knowledge_subtitle' => ['required', 'string', 'max:1000'],

            // Navbar Links
            'nav_home' => ['required', 'string', 'max:100'],
            'nav_education' => ['required', 'string', 'max:100'],
            'nav_industry' => ['required', 'string', 'max:100'],
            'nav_career' => ['required', 'string', 'max:100'],
            'nav_events' => ['required', 'string', 'max:100'],
            'nav_community' => ['required', 'string', 'max:100'],
            'nav_knowledge' => ['required', 'string', 'max:100'],
        ]);

        AppSetting::setSiteContentSettings($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Konten teks situs berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Konten teks situs berhasil diperbarui.');
    }
}
