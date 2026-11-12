<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\MembershipPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminMembershipController extends Controller
{
    /**
     * Tampilkan daftar keanggotaan institusi, permohonan pembayaran, dan pengaturan sistem premium.
     */
    public function index(Request $request): View
    {
        $institutionRoles = ['school', 'school_admin', 'university', 'univ_rep', 'company', 'company_hr'];

        $query = User::whereIn('role', $institutionRoles)
            ->with(['school', 'university', 'company'])
            ->latest();

        // Search filter
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->query('role')) {
            if ($role === 'school') {
                $query->whereIn('role', ['school', 'school_admin']);
            } elseif ($role === 'university') {
                $query->whereIn('role', ['university', 'univ_rep']);
            } elseif ($role === 'company') {
                $query->whereIn('role', ['company', 'company_hr']);
            }
        }

        // Status filter
        if ($status = $request->query('status')) {
            if ($status === 'disabled') {
                $query->where('is_premium_active', false);
            } elseif ($status === 'expired') {
                $query->where('is_premium_active', true)
                    ->where('membership_expires_at', '<', now());
            } elseif ($status === 'trial') {
                $query->where('is_premium_active', true)
                    ->where('membership_status', 'trial')
                    ->where(function ($q) {
                        $q->whereNull('membership_expires_at')
                            ->orWhere('membership_expires_at', '>=', now());
                    });
            } elseif ($status === 'active') {
                $query->where('is_premium_active', true)
                    ->whereIn('membership_status', ['active', 'premium'])
                    ->where(function ($q) {
                        $q->whereNull('membership_expires_at')
                            ->orWhere('membership_expires_at', '>=', now());
                    });
            }
        }

        $users = $query->paginate(15)->withQueryString();

        // Summary counts
        $allInstitutions = User::whereIn('role', $institutionRoles)->get();
        $totalCount = $allInstitutions->count();
        $trialCount = $allInstitutions->filter(fn (User $u) => $u->is_premium_active && $u->membership_status === 'trial' && (! $u->membership_expires_at || $u->membership_expires_at->isFuture()))->count();
        $activeMemberCount = $allInstitutions->filter(fn (User $u) => $u->is_premium_active && in_array($u->membership_status, ['active', 'premium'], true) && (! $u->membership_expires_at || $u->membership_expires_at->isFuture()))->count();
        $expiredCount = $allInstitutions->filter(fn (User $u) => $u->is_premium_active && $u->membership_expires_at && $u->membership_expires_at->isPast())->count();
        $disabledCount = $allInstitutions->filter(fn (User $u) => ! $u->is_premium_active)->count();

        $isGlobalPremiumEnabled = AppSetting::isPremiumFeatureEnabled();
        $bankSettings = AppSetting::getMembershipBankSettings();

        // Payments / Renewal Requests
        $paymentsQuery = MembershipPayment::with(['user.school', 'user.university', 'user.company', 'approver'])->latest();
        if ($paymentStatus = $request->query('payment_status')) {
            $paymentsQuery->where('status', $paymentStatus);
        }
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page')->withQueryString();
        $pendingPaymentsCount = MembershipPayment::where('status', 'pending')->count();

        return view('admin.memberships.index', compact(
            'users',
            'totalCount',
            'trialCount',
            'activeMemberCount',
            'expiredCount',
            'disabledCount',
            'isGlobalPremiumEnabled',
            'bankSettings',
            'payments',
            'pendingPaymentsCount'
        ));
    }

    /**
     * Perbarui konfigurasi rekening bank dan nominal membership.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'membership_bank_name' => 'required|string|max:100',
            'membership_bank_account_number' => 'required|string|max:100',
            'membership_bank_account_name' => 'required|string|max:150',
            'membership_price_monthly' => 'required|numeric|min:0',
            'membership_price_yearly' => 'required|numeric|min:0',
            'membership_payment_instructions' => 'nullable|string|max:1000',
        ]);

        AppSetting::set('membership_bank_name', $validated['membership_bank_name'], 'membership');
        AppSetting::set('membership_bank_account_number', $validated['membership_bank_account_number'], 'membership');
        AppSetting::set('membership_bank_account_name', $validated['membership_bank_account_name'], 'membership');
        AppSetting::set('membership_price_monthly', (int) $validated['membership_price_monthly'], 'membership');
        AppSetting::set('membership_price_yearly', (int) $validated['membership_price_yearly'], 'membership');
        AppSetting::set('membership_payment_instructions', $validated['membership_payment_instructions'] ?? '', 'membership');

        return back()->with('success', 'Pengaturan rekening bank dan nominal harga membership berhasil diperbarui.');
    }

    /**
     * Setujui bukti pembayaran dan perpanjang masa aktif institusi secara otomatis setelah validasi manual.
     */
    public function approvePayment(Request $request, MembershipPayment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $user = $payment->user;
        if (! $user) {
            return back()->with('error', 'Data akun pemohon tidak ditemukan.');
        }

        $baseDate = ($user->membership_expires_at && $user->membership_expires_at->isFuture())
            ? $user->membership_expires_at
            : now();

        if ($payment->plan === '1_year') {
            $user->membership_expires_at = $baseDate->copy()->addYear();
            $user->membership_plan = '1_year';
        } else {
            $user->membership_expires_at = $baseDate->copy()->addMonth();
            $user->membership_plan = '1_month';
        }

        $user->membership_status = 'active';
        $user->is_premium_active = true;
        if (! $user->membership_started_at) {
            $user->membership_started_at = now();
        }
        $user->save();

        $payment->status = 'approved';
        $payment->admin_notes = $request->input('admin_notes');
        $payment->approved_at = now();
        $payment->approved_by = Auth::id();
        $payment->save();

        return back()->with('success', "Validasi pembayaran {$user->name} berhasil disetujui. Fitur premium telah diaktifkan hingga {$user->membership_expires_at->format('d/m/Y')}.");
    }

    /**
     * Tolak bukti pembayaran perpanjangan membership.
     */
    public function rejectPayment(Request $request, MembershipPayment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ], [
            'admin_notes.required' => 'Wajib menyertakan alasan penolakan pembayaran.',
        ]);

        $payment->status = 'rejected';
        $payment->admin_notes = $validated['admin_notes'];
        $payment->approved_at = now();
        $payment->approved_by = Auth::id();
        $payment->save();

        return back()->with('success', 'Pengajuan pembayaran telah ditolak.');
    }

    /**
     * Matikan sementara atau aktifkan fitur pembatasan membership secara global di sistem.
     */
    public function toggleGlobal(Request $request): RedirectResponse
    {
        $current = AppSetting::isPremiumFeatureEnabled();
        $new = ! $current;
        AppSetting::setPremiumFeatureEnabled($new);

        $msg = $new
            ? 'Fitur pembatasan premium berhasil DIAKTIFKAN. Profil institusi kini mengikuti status keanggotaan/trial.'
            : 'Fitur pembatasan premium berhasil DIMATIKAN SEMENTARA. Semua profil institusi kini tayang di website publik.';

        return back()->with('success', $msg);
    }

    /**
     * Aktifkan atau matikan sementara fitur premium untuk satu institusi tertentu.
     */
    public function toggleUser(User $user): RedirectResponse
    {
        if (! $user->isInstitution()) {
            return back()->with('error', 'User bukan akun institusi.');
        }

        $user->is_premium_active = ! $user->is_premium_active;
        $user->save();

        $action = $user->is_premium_active ? 'diaktifkan' : 'dinonaktifkan sementara';

        return back()->with('success', "Fitur premium untuk {$user->name} berhasil {$action}.");
    }

    /**
     * Perbarui paket atau perpanjang masa keanggotaan institusi secara manual.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (! $user->isInstitution()) {
            return back()->with('error', 'User bukan akun institusi.');
        }

        $validated = $request->validate([
            'extension_type' => 'required|string|in:add_month,add_year,custom_date',
            'membership_status' => 'required|string|in:trial,active,premium,expired',
            'custom_expires_at' => 'nullable|date|required_if:extension_type,custom_date',
        ]);

        $baseDate = ($user->membership_expires_at && $user->membership_expires_at->isFuture())
            ? $user->membership_expires_at
            : now();

        if ($validated['extension_type'] === 'add_month') {
            $user->membership_expires_at = $baseDate->copy()->addMonth();
        } elseif ($validated['extension_type'] === 'add_year') {
            $user->membership_expires_at = $baseDate->copy()->addYear();
        } elseif ($validated['extension_type'] === 'custom_date') {
            $user->membership_expires_at = Carbon::parse($validated['custom_expires_at'])->endOfDay();
        }

        $user->membership_status = $validated['membership_status'];
        $user->is_premium_active = true;
        if (! $user->membership_started_at) {
            $user->membership_started_at = now();
        }
        $user->save();

        return back()->with('success', "Masa keanggotaan {$user->name} berhasil diperpanjang hingga {$user->membership_expires_at->format('d/m/Y')}.");
    }
}
