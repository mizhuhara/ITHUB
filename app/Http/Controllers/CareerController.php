<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CareerController extends Controller
{
    /** @var array<string, string> */
    private array $tabs = [
        'jobs' => 'Pekerjaan',
        'kerja' => 'Job Seeker',
        'internship' => 'Magang',
        'freelance' => 'Freelance',
        'remote' => 'Kerja Remote',
        'graduate' => 'Fresh Graduate',
    ];

    public function index(Request $request): View
    {
        $tab = $request->query('type', 'jobs');

        // "Siap Kerja" tab — show job seekers directory
        if ($tab === 'kerja') {
            $seekerQuery = JobSeeker::with('user')->where('status', 'aktif');

            if ($request->filled('q')) {
                $keyword = trim($request->query('q'));
                $seekerQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('job_title', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%")
                        ->orWhere('about', 'like', "%{$keyword}%");
                });
            }

            if ($request->filled('experience')) {
                $seekerQuery->whereIn('experience_level', (array) $request->query('experience'));
            }

            if ($request->filled('availability')) {
                $seekerQuery->whereIn('availability', (array) $request->query('availability'));
            }

            if ($request->filled('skills')) {
                $skillFilters = (array) $request->query('skills');
                foreach ($skillFilters as $skill) {
                    $seekerQuery->where(function ($q) use ($skill) {
                        $q->whereJsonContains('skills', $skill)
                            ->orWhereJsonContains('skills', strtolower($skill))
                            ->orWhereJsonContains('skills', ucfirst($skill));
                    });
                }
            }

            $seekers = $seekerQuery->latest('id')->paginate(6)->withQueryString();

            return view('career.kerja', [
                'seekers' => $seekers,
                'tab' => $tab,
                'tabs' => $this->tabs,
                'experienceLevels' => ['Entry', 'Mid', 'Senior'],
                'availabilities' => ['Segera', 'Bulan Depan', '> 1 Bulan'],
                'allSkills' => [
                    'Laravel', 'PHP', 'React', 'Vue', 'JavaScript', 'TypeScript',
                    'Python', 'Node.js', 'Go', 'Java', 'UI/UX', 'Figma', 'Tailwind',
                    'MySQL', 'PostgreSQL', 'Docker', 'AWS', 'Git', 'Flutter',
                ],
            ]);
        }

        // Internship tab — fetch from Internship model
        if ($tab === 'internship') {
            $jobs = $this->getInternships($request);
        } else {
            // All other tabs — fetch from JobListing model
            $query = JobListing::with(['user.company'])
                ->where('is_active', true)
                ->where('status', '!=', 'Draft');

            if ($tab !== 'all' && $tab !== 'jobs') {
                $query->where('category', $tab);
            }

            if ($request->filled('job_type')) {
                $query->whereIn('type', (array) $request->query('job_type'));
            }

            if ($request->filled('experience')) {
                $query->whereIn('experience', (array) $request->query('experience'));
            }

            if ($request->filled('salary')) {
                $query->whereIn('salary_range', (array) $request->query('salary'));
            }

            if ($request->filled('q')) {
                $keyword = trim($request->query('q'));
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('company', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%");
                });
            }

            if ($request->filled('skills')) {
                $skillFilters = (array) $request->query('skills');
                foreach ($skillFilters as $skill) {
                    $query->whereJsonContains('skills', $skill);
                }
            }

            $jobs = $query->latest('id')->paginate(6)->withQueryString();
        }

        return view('career.index', [
            'jobs' => $jobs,
            'tab' => $tab,
            'tabs' => $this->tabs,
            'jobTypes' => ['Full-time', 'Part-time', 'Contract', 'Internship'],
            'experienceLevels' => ['Entry', 'Mid', 'Senior'],
            'salaryRanges' => ['< 8jt', '8–15jt', '15–25jt', '25jt+'],
            'allSkills' => [
                'Laravel', 'PHP', 'React', 'Vue', 'JavaScript', 'TypeScript',
                'Python', 'Node.js', 'Go', 'Java', 'Spring Boot', 'UI/UX',
                'Figma', 'Tailwind', 'MySQL', 'PostgreSQL', 'Redis', 'Docker',
                'Kubernetes', 'AWS', 'GraphQL', 'REST API', 'Git', 'Flutter',
            ],
        ]);
    }

    /**
     * Show form for registering as a job seeker.
     */
    public function createJobSeeker(): View
    {
        return view('career.seeker-create', [
            'tab' => 'kerja',
            'tabs' => $this->tabs,
        ]);
    }

    /**
     * Show details of a specific job seeker.
     */
    public function showJobSeeker(int $id): View
    {
        $seeker = JobSeeker::where('status', 'aktif')->findOrFail($id);

        return view('career.seeker-show', [
            'seeker' => $seeker,
            'tab' => 'kerja',
            'tabs' => $this->tabs,
        ]);
    }

    /**
     * Store a new job seeker profile.
     */
    public function storeJobSeeker(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:job_seekers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:100'],
            'job_title' => ['required', 'string', 'max:100'],
            'skills' => ['nullable', 'string'],
            'experience_level' => ['required', 'in:Entry,Mid,Senior'],
            'expected_salary' => ['nullable', 'string', 'max:50'],
            'about' => ['nullable', 'string', 'max:1000'],
            'portfolio_url' => ['nullable', 'url', 'max:300'],
            'linkedin_url' => ['nullable', 'url', 'max:300'],
            'availability' => ['required', 'in:Segera,Bulan Depan,> 1 Bulan'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $skillsArray = ! empty($validated['skills'])
            ? array_map('trim', explode(',', $validated['skills']))
            : [];

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('job-seekers', 'public');
        } elseif (auth()->check() && auth()->user()->avatar) {
            $photoPath = auth()->user()->avatar;
        }

        $userId = auth()->id() ?? User::where('email', $validated['email'])->value('id');

        $seeker = JobSeeker::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'location' => $validated['location'] ?? null,
            'job_title' => $validated['job_title'],
            'skills' => $skillsArray,
            'experience_level' => $validated['experience_level'],
            'expected_salary' => $validated['expected_salary'] ?? null,
            'about' => $validated['about'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'photo_url' => $photoPath,
            'availability' => $validated['availability'],
            'status' => 'aktif',
        ]);

        return redirect()->route('career.seeker.show', $seeker->id)
            ->with('seeker_success', "Profil \"{$validated['name']}\" berhasil didaftarkan! Perusahaan kini bisa menemukan dan melihat profilmu.");
    }

    private function getInternships(Request $request)
    {
        $query = Internship::where('status', 'active');

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('company', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        }

        return $query->latest('id')->paginate(6)->withQueryString();
    }

    public function apply(string $type, int $id): View
    {
        $job = $this->resolveJob($type, $id);

        if ($job instanceof JobListing || $job instanceof Internship) {
            $job->increment('total_views');
        }

        return view('career.apply', [
            'job' => $job,
            'applyType' => $type,
        ]);
    }

    public function storeApplication(Request $request, string $type, int $id): RedirectResponse
    {
        $job = $this->resolveJob($type, $id);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'linkedin' => ['nullable', 'url', 'max:200'],
            'portfolio' => ['nullable', 'url', 'max:200'],
            'cover_letter' => ['required', 'string', 'min:50', 'max:3000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $resumePath = $request->file('resume')->store('resumes', 'local');
        $userId = $request->user()?->id;

        try {
            DB::transaction(function () use ($job, $validated, $resumePath, $userId) {
                $application = JobApplication::create([
                    'job_listing_id' => $job instanceof JobListing ? $job->id : null,
                    'internship_id' => $job instanceof Internship ? $job->id : null,
                    'user_id' => $userId,
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'linkedin' => $validated['linkedin'] ?? null,
                    'portfolio' => $validated['portfolio'] ?? null,
                    'cover_letter' => $validated['cover_letter'],
                    'resume_path' => $resumePath,
                ]);

                $application->statusHistories()->create([
                    'status' => 'pending',
                    'note' => 'Lamaran dikirim.',
                ]);

                if ($job instanceof JobListing) {
                    $job->increment('applicants_count');
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($resumePath);

            throw $exception;
        }

        return redirect()
            ->route('career.index', ['type' => $job->category])
            ->with('apply_success', "Lamaran kamu untuk \"{$job->title}\" di {$job->company} berhasil dikirim!");
    }

    private function resolveJob(string $type, int $id): Internship|JobListing
    {
        if ($type === 'internship') {
            return Internship::where('status', 'active')->findOrFail($id);
        }

        if ($type === 'job') {
            return JobListing::where('is_active', true)
                ->where('status', '!=', 'Draft')
                ->findOrFail($id);
        }

        abort(404);
    }
}
