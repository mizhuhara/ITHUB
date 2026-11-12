<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminJobController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        $query = JobListing::query();

        // Scope to company's own jobs if role is company
        if ($isCompany) {
            $query->where(function ($q) use ($user, $company) {
                $q->where('user_id', $user->id);
                if ($company && ! empty($company->name)) {
                    $q->orWhere('company', $company->name);
                }
            });
        }

        // Tab filter (all, active, drafts, closed)
        $tab = $request->query('tab', 'all');
        if ($tab === 'active') {
            $query->where('tab_status', 'active');
        } elseif ($tab === 'drafts') {
            $query->where('tab_status', 'drafts');
        } elseif ($tab === 'closed') {
            $query->where('tab_status', 'closed');
        }

        // Status filter
        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        // Job Type filter
        if ($request->filled('type') && $request->query('type') !== 'all') {
            $query->where('type', 'like', "%{$request->query('type')}%");
        }

        // Department filter
        if ($request->filled('department') && $request->query('department') !== 'all') {
            $query->where('department', 'like', "%{$request->query('department')}%");
        }

        // Search query filter
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $paginatedJobs = $query->withCount('applications')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = JobListing::query();
        if ($isCompany) {
            $statsQuery->where(function ($q) use ($user, $company) {
                $q->where('user_id', $user->id);
                if ($company && ! empty($company->name)) {
                    $q->orWhere('company', $company->name);
                }
            });
        }

        $totalPostings = number_format((clone $statsQuery)->count());
        $activeJobs = number_format((clone $statsQuery)->where('tab_status', 'active')->count());
        $applicationsCount = number_format((clone $statsQuery)->sum('applicants_count'));

        $hasActiveFilter = ($request->filled('status') && $request->query('status') !== 'all') ||
                           ($request->filled('type') && $request->query('type') !== 'all') ||
                           ($request->filled('department') && $request->query('department') !== 'all');

        return view('admin.jobs.index', [
            'jobs' => $paginatedJobs,
            'currentTab' => $tab,
            'totalPostings' => $totalPostings,
            'activeJobs' => $activeJobs,
            'applicationsCount' => $applicationsCount,
            'search' => $request->query('search', ''),
            'status' => $request->query('status', 'all'),
            'type' => $request->query('type', 'all'),
            'department' => $request->query('department', 'all'),
            'hasActiveFilter' => $hasActiveFilter,
        ]);
    }

    /**
     * Export job listings to Excel-compatible CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        $query = JobListing::query();

        if ($isCompany) {
            $query->where(function ($q) use ($user, $company) {
                $q->where('user_id', $user->id);
                if ($company && ! empty($company->name)) {
                    $q->orWhere('company', $company->name);
                }
            });
        }

        $tab = $request->query('tab', 'all');
        if ($tab === 'active') {
            $query->where('tab_status', 'active');
        } elseif ($tab === 'drafts') {
            $query->where('tab_status', 'drafts');
        } elseif ($tab === 'closed') {
            $query->where('tab_status', 'closed');
        }

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('type') && $request->query('type') !== 'all') {
            $query->where('type', 'like', "%{$request->query('type')}%");
        }

        if ($request->filled('department') && $request->query('department') !== 'all') {
            $query->where('department', 'like', "%{$request->query('department')}%");
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderByDesc('id')->get();
        $filename = 'jobs_export_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($jobs) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, [
                'ID / Kode',
                'Judul Pekerjaan',
                'Perusahaan',
                'Departemen',
                'Tipe Pekerjaan',
                'Pengalaman',
                'Rentang Gaji',
                'Lokasi',
                'Status',
                'Jumlah Pelamar',
                'Total Dilihat',
                'Tanggal Diposting',
                'Tanggal Dibuat',
            ]);

            foreach ($jobs as $job) {
                fputcsv($output, array_map(fn ($value) => $this->csvSafe($value), [
                    $job->code ?? ('JOB-'.$job->id),
                    $job->title,
                    $job->company,
                    $job->department,
                    $job->type,
                    $job->experience ?? '-',
                    $job->salary_range ?? '-',
                    $job->location,
                    $job->status,
                    $job->applicants_count ?? 0,
                    $job->total_views ?? 0,
                    $job->date_posted,
                    $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : '-',
                ]));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Show form for creating a job posting.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        return view('admin.jobs.create', compact('isCompany', 'company'));
    }

    /**
     * Store new job posting in MySQL.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['nullable', 'string', 'max:500'],
            'responsibilities' => ['nullable', 'array'],
            'responsibilities.*' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:100'],
            'deadline' => ['nullable', 'string', 'max:50'],
            'company' => [$isCompany ? 'nullable' : 'required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'company_size' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);

        $companyName = $isCompany ? ($company?->name ?? ($validated['company'] ?? 'Perusahaan')) : $validated['company'];
        $companyLogo = $isCompany ? ($company?->logo_url) : null;

        $isActive = $request->boolean('is_active', true);
        $status = $isActive ? 'Active' : 'Draft';
        $tabStatus = $isActive ? 'active' : 'drafts';

        $requirementsArr = array_values(array_filter($request->input('requirements', []), fn ($v) => ! empty(trim($v))));
        $responsibilitiesArr = array_values(array_filter($request->input('responsibilities', []), fn ($v) => ! empty(trim($v))));
        $skillsArr = array_values(array_filter($request->input('skills', []), fn ($v) => ! empty(trim($v))));

        // Process image and logo
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('jobs', 'public');
            $imageUrl = Storage::url($path);
        } else {
            $imageUrl = $validated['image'] ?? null;
        }

        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('jobs/logos', 'public');
            $logoUrl = Storage::url($logoPath);
        } else {
            $logoUrl = $validated['logo_url'] ?? $imageUrl;
        }

        if (empty($imageUrl) && ! empty($logoUrl)) {
            $imageUrl = $logoUrl;
        }

        if (empty($logoUrl) && ! empty($imageUrl)) {
            $logoUrl = $imageUrl;
        }

        if (empty($imageUrl) && ! empty($companyLogo)) {
            $imageUrl = $companyLogo;
        }

        if (empty($logoUrl) && ! empty($companyLogo)) {
            $logoUrl = $companyLogo;
        }

        $job = JobListing::create([
            'user_id' => $user?->id,
            'title' => $validated['title'],
            'department' => $validated['department'],
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'experience' => $validated['experience'] ?? 'Mid',
            'salary_range' => $validated['salary_range'] ?? null,
            'description' => $validated['description'],
            'requirements' => ! empty($requirementsArr) ? $requirementsArr : null,
            'responsibilities' => ! empty($responsibilitiesArr) ? $responsibilitiesArr : null,
            'skills' => ! empty($skillsArr) ? $skillsArr : null,
            'deadline' => $validated['deadline'] ?? null,
            'company' => $companyName,
            'location' => $validated['location'],
            'location_full' => $validated['location'],
            'company_size' => $validated['company_size'],
            'image' => $imageUrl,
            'logo_url' => $logoUrl,
            'status' => $status,
            'tab_status' => $tabStatus,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.jobs.index')
            ->with('success', "Lowongan pekerjaan \"{$job->title}\" berhasil dipublikasikan!");
    }

    /**
     * Show detail of a job posting.
     */
    public function show(Request $request, int $id): View
    {
        $job = JobListing::with([
            'applications' => function ($query) {
                $query->with(['statusHistories.changedBy', 'user'])->latest('id');
            },
        ])->findOrFail($id);
        $this->authorizeJobAccess($job, $request);

        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Download applicant resume for this job.
     */
    public function downloadResume(Request $request, int $job, int $application): StreamedResponse
    {
        $jobListing = JobListing::findOrFail($job);
        $this->authorizeJobAccess($jobListing, $request);

        $jobApp = $jobListing->applications()->findOrFail($application);
        abort_unless(Storage::disk('local')->exists($jobApp->resume_path), 404, 'File resume tidak ditemukan.');

        return Storage::disk('local')->download(
            $jobApp->resume_path,
            basename($jobApp->resume_path)
        );
    }

    /**
     * Update application status for this job.
     */
    public function updateApplicationStatus(Request $request, int $job, int $application): RedirectResponse
    {
        $jobListing = JobListing::findOrFail($job);
        $this->authorizeJobAccess($jobListing, $request);

        $jobApp = $jobListing->applications()->findOrFail($application);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,shortlisted,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $jobApp->status;
        $note = filled($validated['note'] ?? null) ? trim($validated['note']) : null;
        $statusChanged = $previousStatus !== $validated['status'];

        if (! $statusChanged && $note === null) {
            return back()->with('info', "Tidak ada perubahan status pada lamaran {$jobApp->full_name}.");
        }

        DB::transaction(function () use ($jobApp, $validated, $note) {
            $jobApp->update(['status' => $validated['status']]);

            $jobApp->statusHistories()->create([
                'status' => $validated['status'],
                'note' => $note,
                'changed_by' => Auth::id(),
            ]);
        });

        $jobApp->user?->notify(new ApplicationStatusUpdated(
            $jobApp->fresh(),
            $previousStatus,
            $note
        ));

        $statusLabel = JobApplication::STATUS_LABELS[$validated['status']] ?? ucfirst($validated['status']);

        return back()->with('success', "Status lamaran {$jobApp->full_name} berhasil diperbarui menjadi \"{$statusLabel}\".");
    }

    /**
     * Show edit form for job posting.
     */
    public function edit(Request $request, int $id): View
    {
        $job = JobListing::findOrFail($id);
        $this->authorizeJobAccess($job, $request);

        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        return view('admin.jobs.edit', compact('job', 'isCompany', 'company'));
    }

    /**
     * Update job posting in MySQL.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $job = JobListing::findOrFail($id);
        $this->authorizeJobAccess($job, $request);

        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['nullable', 'string', 'max:500'],
            'responsibilities' => ['nullable', 'array'],
            'responsibilities.*' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:100'],
            'deadline' => ['nullable', 'string', 'max:50'],
            'company' => [$isCompany ? 'nullable' : 'required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'company_size' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);

        $companyName = $isCompany ? ($company?->name ?? ($job->company ?: 'Perusahaan')) : $validated['company'];

        $isActive = $request->boolean('is_active', false);
        $status = $isActive ? 'Active' : 'Draft';
        $tabStatus = $isActive ? 'active' : 'drafts';

        $requirementsArr = array_values(array_filter($request->input('requirements', []), fn ($v) => ! empty(trim($v))));
        $responsibilitiesArr = array_values(array_filter($request->input('responsibilities', []), fn ($v) => ! empty(trim($v))));
        $skillsArr = array_values(array_filter($request->input('skills', []), fn ($v) => ! empty(trim($v))));

        // Process image
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('jobs', 'public');
            $imageUrl = Storage::url($path);
            $logoUrl = $imageUrl;
        } elseif ($request->filled('image')) {
            $imageUrl = $validated['image'];
            $logoUrl = $imageUrl;
        } else {
            $imageUrl = $job->image;
            $logoUrl = $job->logo_url ?? $imageUrl;
        }

        // Process logo if explicitly uploaded
        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('jobs/logos', 'public');
            $logoUrl = Storage::url($logoPath);
        } elseif ($request->filled('logo_url')) {
            $logoUrl = $validated['logo_url'];
        }

        if (empty($imageUrl) && ! empty($logoUrl)) {
            $imageUrl = $logoUrl;
        }

        if (empty($logoUrl) && ! empty($imageUrl)) {
            $logoUrl = $imageUrl;
        }

        $jobData = [
            'title' => $validated['title'],
            'department' => $validated['department'],
            'type' => $validated['type'],
            'category' => ! empty($validated['category']) ? $validated['category'] : $job->category,
            'experience' => ! empty($validated['experience']) ? $validated['experience'] : $job->experience,
            'salary_range' => $validated['salary_range'] ?? null,
            'description' => $validated['description'],
            'requirements' => ! empty($requirementsArr) ? $requirementsArr : null,
            'responsibilities' => ! empty($responsibilitiesArr) ? $responsibilitiesArr : null,
            'skills' => ! empty($skillsArr) ? $skillsArr : null,
            'deadline' => $validated['deadline'] ?? null,
            'company' => $companyName,
            'location' => $validated['location'],
            'location_full' => $validated['location'],
            'company_size' => $validated['company_size'],
            'image' => $imageUrl,
            'logo_url' => $logoUrl,
            'status' => $status,
            'tab_status' => $tabStatus,
            'is_active' => $isActive,
        ];

        if (! $job->user_id && $user) {
            $jobData['user_id'] = $user->id;
        }

        $job->update($jobData);

        return redirect()->route('admin.jobs.index')
            ->with('success', "Lowongan pekerjaan \"{$job->title}\" berhasil diperbarui.");
    }

    /**
     * Remove job posting from MySQL.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $job = JobListing::findOrFail($id);
        $this->authorizeJobAccess($job, $request);

        $title = $job->title;
        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', "Lowongan pekerjaan \"{$title}\" berhasil dihapus.");
    }

    /**
     * Authorize access to a job listing.
     */
    protected function authorizeJobAccess(JobListing $job, Request $request): void
    {
        $user = $request->user();
        if ($user && $user->role === 'company') {
            $company = Company::where('user_id', $user->id)->first();
            $isOwner = ($job->user_id && $job->user_id === $user->id) ||
                       ($company && ! empty($company->name) && $job->company === $company->name);

            if (! $isOwner) {
                abort(403, 'Anda tidak memiliki hak akses untuk lowongan pekerjaan ini.');
            }
        }
    }
}
