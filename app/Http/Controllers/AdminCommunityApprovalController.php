<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Notifications\CommunityApprovedNotification;
use App\Notifications\CommunityRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminCommunityApprovalController extends Controller
{
    /**
     * Display a listing of community approval submissions.
     */
    public function index(Request $request): View
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Akses ditolak. Hanya Super Admin yang dapat mengakses halaman Approval Komunitas.'
        );

        $query = Community::with(['creator', 'approver', 'rejecter']);

        // Search by community name, category, or creator name
        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($creatorQ) use ($search) {
                        $creatorQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by approval status: pending, approved, rejected, all
        $status = (string) $request->query('status', 'all');
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('approval_status', $status);
        }

        // Filter by category if provided
        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        // Order: prioritize pending submissions, then latest created
        $query->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 WHEN approval_status = 'rejected' THEN 1 ELSE 2 END")
            ->latest('id');

        return view('admin.community-approvals.index', [
            'communities' => $query->paginate(10)->withQueryString(),
            'totalCount' => Community::count(),
            'pendingCount' => Community::where('approval_status', 'pending')->count(),
            'approvedCount' => Community::where('approval_status', 'approved')->count(),
            'rejectedCount' => Community::where('approval_status', 'rejected')->count(),
            'activeStatus' => $status,
            'categories' => ['Technology', 'Design', 'Business', 'Education', 'Science', 'Other'],
        ]);
    }

    /**
     * Display details of a specific community submission.
     */
    public function show(Request $request, Community $community): View
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Akses ditolak. Hanya Super Admin yang dapat mengakses detail Approval Komunitas.'
        );

        $community->load(['creator', 'approver', 'rejecter']);

        return view('admin.community-approvals.show', [
            'community' => $community,
        ]);
    }

    /**
     * Approve a community submission.
     */
    public function approve(Request $request, Community $community): RedirectResponse
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Akses ditolak. Hanya Super Admin yang dapat menyetujui komunitas.'
        );

        $community->update([
            'approval_status' => 'approved',
            'status' => 'active',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'resubmit_notes' => null,
        ]);

        // Ensure community creator is attached as admin member
        if ($community->user_id) {
            $community->members()->syncWithoutDetaching([
                $community->user_id => [
                    'role' => 'admin',
                    'is_banned' => false,
                ],
            ]);

            // Ensure member count is at least 1
            if ($community->members < 1) {
                $community->update(['members' => 1]);
            }
        }

        // Send approval notification to creator
        if ($community->creator) {
            try {
                $community->creator->notify(new CommunityApprovedNotification($community));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi persetujuan komunitas: '.$e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Komunitas \"{$community->name}\" berhasil disetujui dan kini telah aktif di halaman Community publik.");
    }

    /**
     * Reject a community submission.
     */
    public function reject(Request $request, Community $community): RedirectResponse
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Akses ditolak. Hanya Super Admin yang dapat menolak komunitas.'
        );

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi ketika menolak pengajuan komunitas.',
            'rejection_reason.min' => 'Alasan penolakan minimal 3 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 2000 karakter.',
        ]);

        $community->update([
            'approval_status' => 'rejected',
            'status' => 'rejected',
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Send rejection notification to creator
        if ($community->creator) {
            try {
                $community->creator->notify(new CommunityRejectedNotification($community, $validated['rejection_reason']));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi penolakan komunitas: '.$e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Pengajuan komunitas \"{$community->name}\" telah ditolak dengan alasan yang tercatat.");
    }
}
