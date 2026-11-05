<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        // Fetch active companies from the database
        $companies = Company::publiclyVisible()->whereIn('status', ['Active', 'active'])->get()->map(function (Company $c) {
            // Determine badge class based on industry (slug)
            $categorySlug = Str::slug($c->industry ?? '');
            $badgeClass = explode('-', $categorySlug)[0] ?? $categorySlug;

            // Map badge class to an icon type
            $iconMap = [
                'software' => 'code',
                'creative' => 'palette',
                'startup' => 'rocket',
            ];
            $iconType = $iconMap[$badgeClass] ?? 'sparkles';

            // Clean plain text excerpt for card preview (strips HTML and base64 images)
            $plainDescription = trim(preg_replace('/\s+/', ' ', strip_tags($c->description ?? '')));
            $cardDescription = Str::limit($plainDescription, 130) ?: 'Tidak ada ringkasan deskripsi.';

            return [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->industry ?? '',
                'category_slug' => $categorySlug,
                'location' => $c->location ?? '',
                'badge_class' => $badgeClass,
                'description' => $cardDescription,
                'logo_bg' => $c->logo_bg ?? '',
                'logo_color' => $c->logo_color ?? '',
                'icon_type' => $iconType,
                'employees' => $c->total_employees ? $c->total_employees.' Karyawan' : '',
                'website' => $c->website ?? '',
                'email' => $c->email ?? '',
                'founded' => $c->founded ?? '',
                'rating' => null,
                'projects_completed' => ! empty($c->projects_completed) ? $c->projects_completed : '100+ Proyek',
                'verified' => true,
                'logo_image' => $c->logo_url ?? '',
                'cover_image' => '',
                'specialties' => ! empty($c->specialties) ? $c->specialties : ($c->tags ?? []),
                'tech_stack' => ! empty($c->tech_stack) ? $c->tech_stack : [],
                'full_description' => $c->description ?? '',
                'gallery' => $c->gallery ?? [],
            ];
        })->toArray();

        return view('industry.industry', compact('companies'));
    }
}
