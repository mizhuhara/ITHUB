<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\View\View;

class CampusController extends Controller
{
    public function index(): View
    {
        $campuses = University::publiclyVisible()
            ->where(function ($query) {
                $query->whereIn('status', ['Active', 'active', 'Pending', 'pending'])
                    ->orWhereNull('status')
                    ->orWhere('status', '!=', 'Inactive');
            })
            ->inRandomOrder()
            ->get()
            ->map(fn (University $university) => $this->mapCampus($university))
            ->all();

        return view('education.campus.index', compact('campuses'));
    }

    public function show(string $id): View
    {
        $university = University::publiclyVisible()
            ->where(function ($query) use ($id) {
                $query->where('npsn', $id);

                if (ctype_digit($id)) {
                    $query->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();

        return view('education.campus.show', [
            'campus' => $this->mapCampus($university),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapCampus(University $university): array
    {
        $tags = $university->tags ?? [];
        if (! is_array($tags)) {
            $tags = [];
        }
        $isPublic = $university->type === 'Negeri';
        $gallery = is_array($university->gallery) ? $university->gallery : [];

        // Banner image: use first gallery image if available, else standard fallback
        $bannerImage = ! empty($gallery) ? $gallery[0] : 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80';

        return [
            'id' => $university->npsn ?: (string) $university->id,
            'name' => $university->name,
            'verified' => in_array(strtolower($university->status ?? ''), ['active', 'verified'], true) || empty($university->status),
            'category_badge' => $isPublic ? 'Perguruan Tinggi Negeri' : 'Perguruan Tinggi Swasta',
            'location' => $university->location ?: trim("{$university->city}, {$university->province}", ', '),
            'location_full' => $university->address ?: trim("{$university->location}, Indonesia", ', '),
            'province' => $university->province,
            'faculty' => $university->accreditation ? "Akreditasi {$university->accreditation}" : '-',
            'tags' => $tags,
            'status_badge' => 'Terverifikasi',
            'status_badge_type' => 'green',
            'image' => $bannerImage,
            'logo_url' => $university->logo_url,
            'gallery' => $gallery,
            'logo_bg_class' => $university->logo_bg ?: 'bg-blue-700',
            'logo_text' => $university->logo_text ?: mb_substr($university->name, 0, 8),
            'website' => $university->website,
            'email' => $university->email,
            'phone' => $university->phone,
            'latitude' => $university->latitude,
            'longitude' => $university->longitude,
            'map_link' => $university->map_link ?: 'https://maps.google.com/?q='.urlencode($university->name),
            'about' => $university->description ?: 'Belum ada deskripsi profil untuk universitas ini.',
            'stats' => [
                'Mahasiswa Aktif' => $university->total_students ? number_format($university->total_students) : '-',
                'Fakultas / Prodi' => (string) ($university->total_faculties ?? '-'),
                'Akreditasi' => $university->accreditation ?? '-',
                'Tahun Berdiri' => (string) ($university->founded ?? '-'),
            ],
            'competencies' => collect($tags)->map(fn (string $tag) => [
                'name' => $tag,
                'icon' => $this->iconForTag($tag),
                'desc' => "Program studi bidang {$tag}.",
            ])->all(),
        ];
    }

    private function iconForTag(string $tag): string
    {
        $tag = strtolower($tag);

        if (str_contains($tag, 'network') || str_contains($tag, 'jaringan') || str_contains($tag, 'cloud')) {
            return 'server';
        }

        if (str_contains($tag, 'design') || str_contains($tag, 'multimedia') || str_contains($tag, 'animation')) {
            return 'cube';
        }

        return 'code';
    }
}
