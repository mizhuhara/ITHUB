<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\View\View;

class EducationController extends Controller
{
    public function index(): View
    {
        $schools = School::where('status', 'Active')
            ->publiclyVisible()
            ->inRandomOrder()
            ->get()
            ->map(fn (School $school) => $this->mapSchool($school))
            ->all();

        return view('education.index', compact('schools'));
    }

    public function show(string $id): View
    {
        $school = School::where('status', 'Active')
            ->publiclyVisible()
            ->where(function ($query) use ($id) {
                $query->where('npsn', $id);

                if (ctype_digit($id)) {
                    $query->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();

        $school->increment('views');

        return view('education.show', [
            'school' => $this->mapSchool($school),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapSchool(School $school): array
    {
        $tags = collect($school->competencies ?? [])
            ->merge(collect($school->tags ?? []))
            ->filter(fn ($t) => ! empty(trim((string) $t)))
            ->unique()
            ->values()
            ->all();

        $gallery = is_array($school->gallery) ? $school->gallery : [];

        // Choose banner: first gallery image if exists, else fallback to standard vocational image
        $bannerImage = ! empty($gallery)
            ? $gallery[0]
            : 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80';

        return [
            'id' => $school->npsn ?: (string) $school->id,
            'name' => $school->name,
            'verified' => $school->status === 'Active',
            'views' => (int) ($school->views ?? 0),
            'category_badge' => $school->institution_type ?: 'Sekolah Menengah Kejuruan',
            'location' => $school->location ?: trim("{$school->city}, {$school->province}", ', '),
            'location_full' => $school->address ?: trim("{$school->location}, Indonesia", ', '),
            'province' => $school->province,
            'tags' => $tags,
            'gallery' => $gallery,
            'status_badge' => $school->status === 'Active' ? 'Terverifikasi' : $school->status,
            'status_badge_type' => $school->status === 'Active' ? 'green' : 'gray',
            'image' => $bannerImage,
            'logo_url' => $school->logo_url,
            'logo_bg_class' => $school->logo_bg ?: 'bg-blue-600',
            'logo_text' => $school->logo_text ?: mb_substr($school->name, 0, 8),
            'website' => $school->website,
            'email' => $school->email,
            'map_link' => $school->map_link ?: (! empty($school->latitude) && ! empty($school->longitude) ? "https://www.google.com/maps?q={$school->latitude},{$school->longitude}" : 'https://maps.google.com/?q='.urlencode($school->name.', '.($school->location ?? ''))),
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'about' => $school->description ?: 'Belum ada deskripsi profil untuk sekolah ini.',
            'stats' => [
                'placement_rate' => ! empty($school->placement_rate) ? (str_contains((string) $school->placement_rate, '%') ? $school->placement_rate : $school->placement_rate.'%') : '-',
                'industry_partners' => $school->industry_partners !== null && $school->industry_partners !== '' ? $school->industry_partners.'+' : '-',
                'active_students' => $school->total_students !== null && $school->total_students !== '' ? number_format((int) $school->total_students) : '-',
                'modern_labs' => $school->modern_labs !== null && $school->modern_labs !== '' ? (string) $school->modern_labs : '-',
            ],
            'competencies' => collect($tags)->map(fn (string $tag) => [
                'name' => $tag,
                'icon' => $this->iconForTag($tag),
                'desc' => "Program keahlian {$tag}.",
            ])->values()->all(),
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
