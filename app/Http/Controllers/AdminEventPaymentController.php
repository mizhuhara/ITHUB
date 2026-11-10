<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\OrganizerBankAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminEventPaymentController extends Controller
{
    /**
     * Display a listing of event registrations / payment confirmations.
     */
    public function confirmations(Request $request): View
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $visibleEventIds = $this->visibleEventIds();
        $myEvents = Event::whereIn('id', $visibleEventIds)->orderByDesc('id')->get(['id', 'title', 'price']);

        $query = EventRegistration::with(['event', 'user', 'confirmedBy', 'rejectedBy'])
            ->whereIn('event_id', $visibleEventIds);

        // Filter event
        if ($request->filled('event_id') && $request->event_id !== 'all') {
            $query->where('event_id', (int) $request->event_id);
        }

        // Filter status: pending, confirmed, rejected, all
        $status = strtolower($request->query('status', 'all'));
        if ($status === 'pending') {
            $query->where('payment_status', 'pending');
        } elseif ($status === 'confirmed') {
            $query->whereIn('payment_status', ['confirmed', 'free']);
        } elseif ($status === 'rejected') {
            $query->where('payment_status', 'rejected');
        }

        // Filter keyword
        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest('id')->paginate(12)->withQueryString();

        $allRegistrations = EventRegistration::whereIn('event_id', $visibleEventIds);
        $totalPending = (clone $allRegistrations)->where('payment_status', 'pending')->count();
        $totalConfirmed = (clone $allRegistrations)->whereIn('payment_status', ['confirmed', 'free'])->count();
        $totalRejected = (clone $allRegistrations)->where('payment_status', 'rejected')->count();

        return view('admin.events.confirmations', [
            'registrations' => $registrations,
            'myEvents' => $myEvents,
            'selectedEventId' => $request->query('event_id', 'all'),
            'selectedStatus' => $status,
            'searchQuery' => $request->query('q', ''),
            'totalPending' => $totalPending,
            'totalConfirmed' => $totalConfirmed,
            'totalRejected' => $totalRejected,
        ]);
    }

    /**
     * Confirm a participant's payment.
     */
    public function confirm(EventRegistration $registration): RedirectResponse
    {
        $this->authorizeEventOwnership($registration->event_id);

        $registration->update([
            'payment_status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Pembayaran peserta \"{$registration->name}\" berhasil dikonfirmasi. Tiket telah diaktifkan.");
    }

    /**
     * Reject a participant's payment with a reason.
     */
    public function reject(Request $request, EventRegistration $registration): RedirectResponse
    {
        $this->authorizeEventOwnership($registration->event_id);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $registration->update([
            'payment_status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Pembayaran peserta \"{$registration->name}\" telah ditolak.");
    }

    /**
     * Display event financial and revenue analytics.
     */
    public function finance(Request $request): View
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $visibleEventIds = $this->visibleEventIds();
        $events = Event::withCount([
            'registrations as total_registrations_count',
            'registrations as confirmed_registrations_count' => function ($q) {
                $q->whereIn('payment_status', ['confirmed', 'free']);
            },
            'registrations as pending_registrations_count' => function ($q) {
                $q->where('payment_status', 'pending');
            },
        ])
            ->whereIn('id', $visibleEventIds)
            ->orderByDesc('id')
            ->get();

        $allRegistrations = EventRegistration::whereIn('event_id', $visibleEventIds);

        $totalEarned = (int) (clone $allRegistrations)
            ->where('payment_status', 'confirmed')
            ->sum('amount');

        $totalPendingRevenue = (int) (clone $allRegistrations)
            ->where('payment_status', 'pending')
            ->sum('amount');

        $totalPaidTicketsSold = (clone $allRegistrations)
            ->where('payment_status', 'confirmed')
            ->where('amount', '>', 0)
            ->count();

        $totalFreeParticipants = (clone $allRegistrations)
            ->where(function ($q) {
                $q->where('payment_status', 'free')
                    ->orWhere(function ($q2) {
                        $q2->where('payment_status', 'confirmed')->where('amount', 0);
                    });
            })
            ->count();

        $recentTransactions = (clone $allRegistrations)
            ->with(['event', 'user'])
            ->where('amount', '>', 0)
            ->latest('id')
            ->take(10)
            ->get();

        $bankAccount = $user->bankAccount;

        return view('admin.events.finance', [
            'events' => $events,
            'totalEarned' => $totalEarned,
            'totalPendingRevenue' => $totalPendingRevenue,
            'totalPaidTicketsSold' => $totalPaidTicketsSold,
            'totalFreeParticipants' => $totalFreeParticipants,
            'recentTransactions' => $recentTransactions,
            'bankAccount' => $bankAccount,
        ]);
    }

    /**
     * Display the organizer's bank account settings.
     */
    public function bankAccount(Request $request): View
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $bankAccount = OrganizerBankAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('admin.events.bank-account', [
            'bankAccount' => $bankAccount,
        ]);
    }

    /**
     * Update or create the organizer's bank account settings.
     */
    public function updateBankAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'qr_code_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $bankAccount = OrganizerBankAccount::firstOrNew(['user_id' => $user->id]);

        $bankAccount->bank_name = $validated['bank_name'];
        $bankAccount->account_number = $validated['account_number'];
        $bankAccount->account_holder = $validated['account_holder'];
        $bankAccount->instructions = $validated['instructions'] ?? null;
        $bankAccount->is_active = true;

        if ($request->hasFile('qr_code_image')) {
            $qrPath = Storage::url($request->file('qr_code_image')->store('events/qr', 'public'));
            $bankAccount->qr_code_image = $qrPath;
        }

        $bankAccount->save();

        return back()->with('success', 'Rekening pembayaran berhasil disimpan. Rekening ini akan ditampilkan pada seluruh event berbayar Anda.');
    }

    /**
     * Helper to get accessible event IDs for the current user.
     *
     * @return array<int>
     */
    private function visibleEventIds(): array
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['school', 'university', 'company'], true)) {
            $company = $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;

            return Event::where(function (Builder $q) use ($user, $company) {
                $q->where('user_id', $user->id);
                if ($company && ! empty($company->name)) {
                    $q->orWhere('organizer', $company->name);
                }
            })->pluck('id')->all();
        }

        return Event::pluck('id')->all();
    }

    /**
     * Authorize that the current user owns the event.
     */
    private function authorizeEventOwnership(int $eventId): void
    {
        $visibleIds = $this->visibleEventIds();
        abort_unless(in_array($eventId, $visibleIds, true), 403, 'Anda tidak memiliki akses ke event ini.');
    }
}
