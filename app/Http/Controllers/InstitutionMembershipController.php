<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\MembershipPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionMembershipController extends Controller
{
    /**
     * Tampilkan informasi status membership untuk institusi (school, university, company).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Cari institusi terkait
        $institution = match ($user->role) {
            'school', 'school_admin' => $user->school,
            'university', 'univ_rep' => $user->university,
            'company', 'company_hr' => $user->company,
            default => null,
        };

        $isGlobalDisabled = ! AppSetting::isPremiumFeatureEnabled();
        $isPubliclyVisible = $user->hasActiveMembership();
        $bankSettings = AppSetting::getMembershipBankSettings();
        $payments = MembershipPayment::where('user_id', $user->id)->latest()->get();

        return view('admin.dashboards.membership', compact(
            'user',
            'institution',
            'isGlobalDisabled',
            'isPubliclyVisible',
            'bankSettings',
            'payments'
        ));
    }

    /**
     * Simpan pengajuan perpanjangan membership dengan bukti pembayaran.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isInstitution()) {
            return back()->with('error', 'Hanya akun institusi yang dapat mengajukan perpanjangan membership.');
        }

        $validated = $request->validate([
            'plan' => 'required|string|in:1_month,1_year',
            'sender_bank' => 'required|string|max:100',
            'sender_name' => 'required|string|max:150',
            'sender_account_number' => 'nullable|string|max:100',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ], [
            'plan.required' => 'Pilih paket perpanjangan membership.',
            'sender_bank.required' => 'Nama bank atau metode pembayaran pengirim wajib diisi.',
            'sender_name.required' => 'Nama pemilik rekening pengirim wajib diisi.',
            'payment_proof.required' => 'Bukti transfer pembayaran wajib dilampirkan.',
            'payment_proof.mimes' => 'Bukti pembayaran harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 5MB.',
        ]);

        $bankSettings = AppSetting::getMembershipBankSettings();
        $amount = $validated['plan'] === '1_year'
            ? $bankSettings['price_yearly']
            : $bankSettings['price_monthly'];

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        MembershipPayment::create([
            'user_id' => $user->id,
            'institution_type' => $user->role,
            'plan' => $validated['plan'],
            'amount' => $amount,
            'sender_bank' => $validated['sender_bank'],
            'sender_name' => $validated['sender_name'],
            'sender_account_number' => $validated['sender_account_number'] ?? null,
            'payment_proof' => $proofPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan perpanjangan membership dan bukti pembayaran berhasil dikirim. Tim administrator akan memverifikasi dalam waktu 1x24 jam.');
    }
}
