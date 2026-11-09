<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCompany = $user && $user->role === 'company';
        $company = $isCompany ? Company::where('user_id', $user->id)->first() : null;

        $baseScope = function ($q) use ($isCompany, $user, $company) {
            if ($isCompany) {
                $q->whereHas('jobListing', function ($jq) use ($user, $company) {
                    $jq->where(function ($sub) use ($user, $company) {
                        $sub->where('user_id', $user->id);
                        if ($company && ! empty($company->name)) {
                            $sub->orWhere('company', $company->name);
                        }
                    });
                });
            } else {
                $q->where(function ($sub) {
                    $sub->whereNull('job_listing_id')
                        ->orWhereHas('jobListing', function ($jq) {
                            $jq->whereNull('user_id')
                                ->orWhereHas('user', function ($uq) {
                                    $uq->whereIn('role', ['super_admin', 'admin']);
                                });
                        });
                });
            }
        };

        $query = JobApplication::with(['jobListing', 'internship'])
            ->where($baseScope);

        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($sub) use ($search) {
                $sub->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        $applications = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.applications.index', [
            'applications' => $applications,
            'totalApplications' => JobApplication::where($baseScope)->count(),
            'pendingApplications' => JobApplication::where($baseScope)->where('status', 'pending')->count(),
            'shortlistedApplications' => JobApplication::where($baseScope)->where('status', 'shortlisted')->count(),
            'searchQuery' => $request->query('q', ''),
            'activeStatus' => $request->query('status', 'all'),
        ]);
    }

    public function update(Request $request, JobApplication $application): RedirectResponse
    {
        $this->authorizeApplicationAccess($request, $application);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,shortlisted,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $application->status;
        $note = filled($validated['note'] ?? null) ? trim($validated['note']) : null;
        $statusChanged = $previousStatus !== $validated['status'];

        if (! $statusChanged && $note === null) {
            return back()->with('success', "Tidak ada perubahan pada lamaran {$application->full_name}.");
        }

        DB::transaction(function () use ($application, $validated, $note) {
            $application->update(['status' => $validated['status']]);

            $application->statusHistories()->create([
                'status' => $validated['status'],
                'note' => $note,
                'changed_by' => auth()->id(),
            ]);
        });

        $application->user?->notify(new ApplicationStatusUpdated(
            $application->fresh(),
            $previousStatus,
            $note
        ));

        return back()->with('success', "Status lamaran {$application->full_name} diperbarui.");
    }

    public function resume(Request $request, JobApplication $application): StreamedResponse
    {
        $this->authorizeApplicationAccess($request, $application);

        abort_unless(Storage::disk('local')->exists($application->resume_path), 404);

        return Storage::disk('local')->download(
            $application->resume_path,
            basename($application->resume_path)
        );
    }

    public function destroy(Request $request, JobApplication $application): RedirectResponse
    {
        $this->authorizeApplicationAccess($request, $application);

        $name = $application->full_name;

        Storage::disk('local')->delete($application->resume_path);
        $application->delete();

        return back()->with('success', "Lamaran {$name} berhasil dihapus.");
    }

    /**
     * Pastikan role company hanya dapat mengelola lamaran pada lowongan miliknya.
     */
    protected function authorizeApplicationAccess(Request $request, JobApplication $application): void
    {
        $user = $request->user();
        if ($user && $user->role === 'company') {
            $company = Company::where('user_id', $user->id)->first();
            $job = $application->jobListing;

            $isOwner = $job && (
                ($job->user_id && $job->user_id === $user->id) ||
                ($company && ! empty($company->name) && $job->company === $company->name)
            );

            if (! $isOwner) {
                abort(403, 'Anda tidak memiliki hak akses untuk lamaran ini.');
            }
        }
    }
}
