<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Community;
use App\Models\Company;
use App\Models\Event;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\JobSeeker;
use App\Models\School;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Handle global multi-entity search.
     */
    public function index(Request $request): JsonResponse|View
    {
        $keyword = trim((string) $request->query('q', ''));
        $activeType = (string) $request->query('type', 'all');

        $counts = [
            'all' => 0,
            'campus' => 0,
            'school' => 0,
            'company' => 0,
            'career' => 0,
            'event' => 0,
            'community' => 0,
            'article' => 0,
        ];

        $results = [
            'campuses' => collect(),
            'schools' => collect(),
            'companies' => collect(),
            'jobs' => collect(),
            'internships' => collect(),
            'seekers' => collect(),
            'events' => collect(),
            'communities' => collect(),
            'articles' => collect(),
        ];

        $paginatedResults = null;

        if ($keyword !== '') {
            // 1. Campus / Universities
            $campusQuery = University::query()
                ->where(function ($query) {
                    $query->whereIn('status', ['Active', 'active', 'Pending', 'pending'])
                        ->orWhereNull('status')
                        ->orWhere('status', '!=', 'Inactive');
                })
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('province', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('type', 'like', "%{$keyword}%");
                });

            // 2. Schools / SMK
            $schoolQuery = School::query()
                ->where(function ($query) {
                    $query->whereIn('status', ['Active', 'active'])
                        ->orWhereNull('status');
                })
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhere('province', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('institution_type', 'like', "%{$keyword}%");
                });

            // 3. Companies / Industri
            $companyQuery = Company::query()
                ->whereIn('status', ['Active', 'active'])
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('industry', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });

            // 4. Careers (Job Listings + Internships + Job Seekers)
            $jobQuery = JobListing::query()
                ->where('is_active', true)
                ->where('status', '!=', 'Draft')
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('company', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });

            $internshipQuery = Internship::query()
                ->whereIn('status', ['open', 'Aktif', 'aktif', 'Active', 'active'])
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('company', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });

            $seekerQuery = JobSeeker::query()
                ->where('status', 'aktif')
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('job_title', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('about', 'like', "%{$keyword}%");
                });

            // 5. Events
            $eventQuery = Event::query()
                ->where('status', 'Approved')
                ->where('is_verified', true)
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('organizer', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });

            // 6. Communities
            $communityQuery = Community::query()
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('tags', 'like', "%{$keyword}%");
                });

            // 7. Articles (Knowledge Hub)
            $articleQuery = Article::query()
                ->where('status', 'published')
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhere('excerpt', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%");
                });

            // Compute counts
            $counts['campus'] = (clone $campusQuery)->count();
            $counts['school'] = (clone $schoolQuery)->count();
            $counts['company'] = (clone $companyQuery)->count();
            $jobCount = (clone $jobQuery)->count();
            $internCount = (clone $internshipQuery)->count();
            $seekerCount = (clone $seekerQuery)->count();
            $counts['career'] = $jobCount + $internCount + $seekerCount;
            $counts['event'] = (clone $eventQuery)->count();
            $counts['community'] = (clone $communityQuery)->count();
            $counts['article'] = (clone $articleQuery)->count();

            $counts['all'] = $counts['campus']
                + $counts['school']
                + $counts['company']
                + $counts['career']
                + $counts['event']
                + $counts['community']
                + $counts['article'];

            // Fast JSON response for live search autocomplete
            if ($request->expectsJson() || $request->query('format') === 'json') {
                $liveResults = collect();

                // Campuses (max 3)
                $campuses = (clone $campusQuery)->limit(3)->get()->map(fn ($c) => [
                    'type' => 'Kampus',
                    'title' => $c->name,
                    'subtitle' => $c->city ? $c->city.($c->province ? ', '.$c->province : '') : ($c->location ?: 'Indonesia'),
                    'url' => route('campus.show', $c->npsn ?: $c->id),
                    'icon' => '🏫',
                ]);
                $liveResults = $liveResults->concat($campuses);

                // Schools (max 3)
                $schools = (clone $schoolQuery)->limit(3)->get()->map(fn ($s) => [
                    'type' => 'SMK / Vokasi',
                    'title' => $s->name,
                    'subtitle' => $s->city ? $s->city.($s->province ? ', '.$s->province : '') : ($s->location ?: 'Indonesia'),
                    'url' => route('education.show', $s->npsn ?: $s->id),
                    'icon' => '🖥️',
                ]);
                $liveResults = $liveResults->concat($schools);

                // Companies (max 2)
                $companies = (clone $companyQuery)->limit(2)->get()->map(fn ($co) => [
                    'type' => 'Industri',
                    'title' => $co->name,
                    'subtitle' => $co->industry ?: 'Perusahaan Teknologi',
                    'url' => route('industry'),
                    'icon' => '🏢',
                ]);
                $liveResults = $liveResults->concat($companies);

                // Jobs (max 2)
                $jobs = (clone $jobQuery)->limit(2)->get()->map(fn ($j) => [
                    'type' => 'Lowongan',
                    'title' => $j->title,
                    'subtitle' => $j->company,
                    'url' => route('career.index', ['type' => 'jobs', 'q' => $j->title]),
                    'icon' => '💼',
                ]);
                $liveResults = $liveResults->concat($jobs);

                // Events (max 2)
                $events = (clone $eventQuery)->limit(2)->get()->map(fn ($e) => [
                    'type' => 'Event IT',
                    'title' => $e->title,
                    'subtitle' => $e->short_date ?: $e->date,
                    'url' => route('event.show', $e->id),
                    'icon' => '📅',
                ]);
                $liveResults = $liveResults->concat($events);

                // Communities (max 2)
                $communities = (clone $communityQuery)->limit(2)->get()->map(fn ($cm) => [
                    'type' => 'Komunitas',
                    'title' => $cm->name,
                    'subtitle' => ($cm->members ?? 0).' anggota',
                    'url' => route('community.show', $cm->id),
                    'icon' => '💬',
                ]);
                $liveResults = $liveResults->concat($communities);

                return response()->json([
                    'results' => $liveResults->values(),
                    'total' => $counts['all'],
                ]);
            }

            // Fetch results based on active type
            if ($activeType === 'campus') {
                $paginatedResults = $campusQuery->latest()->paginate(12)->withQueryString();
            } elseif ($activeType === 'school') {
                $paginatedResults = $schoolQuery->latest()->paginate(12)->withQueryString();
            } elseif ($activeType === 'company') {
                $paginatedResults = $companyQuery->latest()->paginate(12)->withQueryString();
            } elseif ($activeType === 'career') {
                // Combine jobs + internships + seekers into a unified list
                $combined = collect()
                    ->concat($jobQuery->latest()->get()->map(fn ($j) => [
                        'type' => 'job',
                        'badge' => 'Lowongan Kerja',
                        'title' => $j->title,
                        'subtitle' => $j->company,
                        'location' => $j->location ?: 'Indonesia',
                        'tag' => $j->type ?? 'Full Time',
                        'tags' => array_values(array_filter([$j->type ?? 'Full Time', $j->category ?? null])),
                        'url' => route('career.index', ['type' => 'jobs', 'q' => $j->title]),
                        'banner' => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=1000&q=80',
                        'image' => $j->logo_url ?? null,
                        'initials' => strtoupper(substr($j->company ?: 'IT', 0, 4)),
                        'logo_bg' => 'bg-blue-600',
                        'status_badge' => 'Lowongan Kerja',
                        'status_badge_type' => 'blue',
                        'link_text' => 'Lihat Lowongan',
                    ]))
                    ->concat($internshipQuery->latest()->get()->map(fn ($i) => [
                        'type' => 'internship',
                        'badge' => 'Magang',
                        'title' => $i->title,
                        'subtitle' => $i->company,
                        'location' => $i->location ?: 'Indonesia',
                        'tag' => 'Magang',
                        'tags' => ['Magang IT', 'Kerja Praktik'],
                        'url' => route('career.index', ['type' => 'internship', 'q' => $i->title]),
                        'banner' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80',
                        'image' => $i->profile_picture ? asset('storage/'.$i->profile_picture) : null,
                        'initials' => strtoupper(substr($i->company ?: 'MG', 0, 4)),
                        'logo_bg' => 'bg-purple-600',
                        'status_badge' => 'Program Magang',
                        'status_badge_type' => 'purple',
                        'link_text' => 'Lihat Magang',
                    ]))
                    ->concat($seekerQuery->latest()->get()->map(fn ($s) => [
                        'type' => 'seeker',
                        'badge' => 'Siap Kerja',
                        'title' => $s->name,
                        'subtitle' => $s->job_title,
                        'location' => $s->location ?: 'Indonesia',
                        'tag' => $s->availability,
                        'tags' => array_values(array_filter([$s->job_title, $s->availability])),
                        'url' => route('career.seeker.show', $s->id),
                        'banner' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1000&q=80',
                        'image' => $s->photo_url ?? null,
                        'initials' => strtoupper(substr($s->name ?: 'SK', 0, 4)),
                        'logo_bg' => 'bg-amber-600',
                        'status_badge' => 'Siap Kerja',
                        'status_badge_type' => 'green',
                        'link_text' => 'Lihat Profil',
                    ]));

                $page = (int) $request->input('page', 1);
                $perPage = 12;
                $paginatedResults = new LengthAwarePaginator(
                    $combined->forPage($page, $perPage)->values(),
                    $combined->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } elseif ($activeType === 'event') {
                $paginatedResults = $eventQuery->orderBy('date')->paginate(12)->withQueryString();
            } elseif ($activeType === 'community') {
                $paginatedResults = $communityQuery->latest()->paginate(12)->withQueryString();
            } elseif ($activeType === 'article') {
                $paginatedResults = $articleQuery->latest('published_at')->paginate(12)->withQueryString();
            } else {
                // 'all': fetch top 3 items for each category (for balanced 3-column grid)
                $results['campuses'] = $campusQuery->limit(3)->get();
                $results['schools'] = $schoolQuery->limit(3)->get();
                $results['companies'] = $companyQuery->limit(3)->get();
                $results['jobs'] = $jobQuery->limit(3)->get();
                $results['internships'] = $internshipQuery->limit(3)->get();
                $results['seekers'] = $seekerQuery->limit(3)->get();
                $results['events'] = $eventQuery->limit(3)->get();
                $results['communities'] = $communityQuery->limit(3)->get();
                $results['articles'] = $articleQuery->limit(3)->get();
            }
        }

        return view('search.index', [
            'keyword' => $keyword,
            'activeType' => $activeType,
            'counts' => $counts,
            'results' => $results,
            'paginatedResults' => $paginatedResults,
        ]);
    }
}
