<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\AccountRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminVerificationController extends Controller
{
    /**
     * Tampilkan daftar pengajuan verifikasi pengguna & institusi.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Filter status (default 'pending' jika tidak diset secara spesifik)
        $statusFilter = strtolower($request->query('status', 'pending'));
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Filter Tipe / Role
        if ($request->filled('type') && strtolower($request->query('type')) !== 'all') {
            $type = strtolower($request->query('type'));
            $query->where(function ($q) use ($type) {
                if ($type === 'school') {
                    $q->whereIn('role', ['school', 'school_admin']);
                } elseif ($type === 'university') {
                    $q->whereIn('role', ['university', 'univ_rep']);
                } elseif ($type === 'company') {
                    $q->whereIn('role', ['company', 'company_hr']);
                } elseif ($type === 'user') {
                    $q->where('role', 'user');
                } else {
                    $q->where('role', 'like', "%{$type}%");
                }
            });
        }

        // Search Query (Nama, Email, Role, Kontak, HP)
        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%")
                    ->orWhere('org_contact', 'like', "%{$q}%")
                    ->orWhere('org_phone', 'like', "%{$q}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // Count Summary Stats
        $pendingCount = User::where('status', 'pending')->count();
        $approvedCount = User::whereIn('status', ['active', 'Approved'])->count();
        $rejectedCount = User::whereIn('status', ['rejected', 'Rejected'])->count();
        $hiddenCount = User::whereIn('status', ['hidden', 'Hidden'])->count();
        $totalCount = User::count();

        return view('admin.verification.index', [
            'requests' => $users,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'hiddenCount' => $hiddenCount,
            'totalCount' => $totalCount,
            'activeType' => $request->query('type', 'all'),
            'activeStatus' => $statusFilter,
            'searchQuery' => $request->query('q', ''),
        ]);
    }

    /**
     * Setujui / Approve pengajuan verifikasi user.
     */
    public function approve(User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        DB::transaction(function () use ($user) {
            $user->status = 'active';
            if (! $user->hasVerifiedEmail()) {
                $user->email_verified_at = now();
            }
            $user->save();

            $this->syncInstitutionStatus($user, 'Active');
        });

        $user->notify(new AccountApprovedNotification);

        return back()->with('success', "Akun \"{$user->name}\" berhasil disetujui (Active).");
    }

    /**
     * Tolak / Reject pengajuan verifikasi user.
     */
    public function reject(User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        DB::transaction(function () use ($user) {
            $user->status = 'rejected';
            $user->save();

            $this->syncInstitutionStatus($user, 'Inactive');
        });

        $user->notify(new AccountRejectedNotification);

        return back()->with('success', "Akun \"{$user->name}\" telah ditolak (Rejected).");
    }

    /**
     * Sembunyikan atau tampilkan kembali akun institusi/user (Membership toggle).
     */
    public function toggleHide(User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        $isCurrentlyHidden = strtolower((string) $user->status) === 'hidden';
        $newStatus = $isCurrentlyHidden ? 'active' : 'hidden';

        DB::transaction(function () use ($user, $newStatus) {
            $user->status = $newStatus;
            $user->save();

            $institutionStatus = $newStatus === 'active' ? 'Active' : 'Inactive';
            $this->syncInstitutionStatus($user, $institutionStatus);
        });

        $message = $isCurrentlyHidden
            ? "Akun \"{$user->name}\" berhasil ditampilkan kembali (Active)."
            : "Akun \"{$user->name}\" berhasil disembunyikan (Hidden) karena membership.";

        return back()->with('success', $message);
    }

    /**
     * Hapus data pengajuan verifikasi.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Data verifikasi \"{$name}\" berhasil dihapus.");
    }

    private function ensureVerifiable(User $user): void
    {
        abort_if(in_array($user->role, ['super_admin', 'admin'], true), 403, 'Akun admin tidak dapat diproses dari halaman verifikasi.');
        abort_if(Auth::id() === $user->id, 403, 'Anda tidak dapat memproses akun sendiri.');
    }

    public function document(User $user): StreamedResponse
    {
        abort_if(empty($user->org_doc), 404);

        $disk = Storage::disk('local')->exists($user->org_doc) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($user->org_doc), 404);

        return Storage::disk($disk)->download($user->org_doc, basename($user->org_doc));
    }

    private function syncInstitutionStatus(User $user, string $status): void
    {
        $model = match ($user->role) {
            'school' => School::class,
            'university' => University::class,
            'company' => Company::class,
            default => null,
        };

        if ($model) {
            $model::where('user_id', $user->id)->update(['status' => $status]);
        }
    }
}
