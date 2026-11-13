<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnlineEventController extends Controller
{
    /**
     * Record the participant's online attendance and redirect to the meeting.
     */
    public function join(Request $request, string $token): RedirectResponse
    {
        $registration = EventRegistration::with('event')
            ->where('meeting_token', $token)
            ->firstOrFail();

        $event = $registration->event;

        abort_unless($event !== null && $event->requiresOnlineAttendance(), 404);
        abort_unless($request->user()?->id === $registration->user_id, 403);

        if (! $registration->isConfirmed()) {
            return redirect()->route('event.show', $event->id)
                ->with('error', 'Pembayaran Anda belum dikonfirmasi. Link meeting aktif setelah pembayaran diverifikasi.');
        }

        if (! $registration->hasJoinedOnline()) {
            $registration->update(['joined_at' => now()]);
        }

        return redirect()->away($event->meeting_url);
    }
}
