<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display the public verification page for a ticket.
     */
    public function show(Request $request, string $ticketCode): View
    {
        $registration = EventRegistration::with('event')
            ->where('ticket_code', $ticketCode)
            ->firstOrFail();

        $event = $registration->event;
        $qrCode = $event->isOffline() && $registration->isConfirmed()
            ? (new QRCode)->render(route('ticket.show', $registration->ticket_code))
            : null;

        return view('ticket.show', [
            'registration' => $registration,
            'event' => $event,
            'qrCode' => $qrCode,
            'certificateAvailable' => $event->certificateAvailableFor($registration),
            'canCheckIn' => $event->isOffline()
                && $registration->isConfirmed()
                && $this->canManageEvent($request->user(), $event),
        ]);
    }

    /**
     * Toggle attendance for an offline event ticket.
     */
    public function checkIn(Request $request, string $ticketCode): RedirectResponse
    {
        $registration = EventRegistration::with('event')
            ->where('ticket_code', $ticketCode)
            ->firstOrFail();

        $event = $registration->event;

        abort_unless($event->isOffline(), 404);
        abort_unless($this->canManageEvent($request->user(), $event), 403);

        if (! $registration->isConfirmed()) {
            return back()->with('error', 'Peserta belum terdaftar resmi. Pembayaran belum dikonfirmasi.');
        }

        $checkedIn = $registration->checked_in_at === null;

        $registration->update([
            'checked_in_at' => $checkedIn ? now() : null,
        ]);

        return back()->with('success', $checkedIn
            ? "Check-in {$registration->name} berhasil."
            : "Check-in {$registration->name} dibatalkan.");
    }

    /**
     * Check whether the user may manage the given event.
     */
    private function canManageEvent(?User $user, Event $event): bool
    {
        return $event->isManageableBy($user);
    }
}
