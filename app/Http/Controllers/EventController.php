<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a paginated listing of approved events for public users.
     */
    public function index(Request $request): View
    {
        $query = Event::where('status', 'Approved')
            ->where('is_verified', true);

        // Category filter
        $category = $request->query('category', 'all');
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        // Search keyword
        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%{$q}%")
                    ->orWhere('organizer', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        // Location filter
        if ($request->filled('location')) {
            $locations = (array) $request->query('location');
            $query->where(function ($q) use ($locations) {
                foreach ($locations as $loc) {
                    $q->orWhere('location', 'like', "%{$loc}%");
                }
            });
        }

        // Price filter
        if ($request->filled('price')) {
            $prices = (array) $request->query('price');
            $query->where(function ($q) use ($prices) {
                foreach ($prices as $price) {
                    if ($price === 'Free') {
                        $q->orWhereRaw("LOWER(price) = 'free'");
                    } elseif ($price === 'Paid') {
                        $q->orWhereRaw("LOWER(price) != 'free'");
                    }
                }
            });
        }

        // Organizer type filter
        if ($request->filled('organizer_type')) {
            $orgTypes = (array) $request->query('organizer_type');
            $query->whereIn('organizer_type', $orgTypes);
        }

        $events = $query->orderBy('id', 'desc')->paginate(3)->withQueryString();

        $categories = [
            'All', 'Seminar', 'Workshop', 'Webinar', 'Hackathon',
            'Competition', 'Job Fair', 'Tech Meetup', 'Conference', 'Training',
        ];

        return view('event.index', [
            'events' => $events,
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    /**
     * Display a single event's details (only if approved/verified).
     */
    public function show(Request $request, int $id): View
    {
        $event = Event::where('status', 'Approved')
            ->where('is_verified', true)
            ->findOrFail($id);

        $event->loadLearningAndSpeakers();

        $registeredCount = $event->registrations()
            ->whereIn('payment_status', ['confirmed', 'free'])
            ->count();

        $registration = $request->user()
            ? $event->registrations()->where('user_id', $request->user()->id)->first()
            : null;
        $isRegistered = $registration !== null;
        $isFull = $event->total_quota > 0 && $registeredCount >= $event->total_quota;
        $bankAccount = $event->bank_account;

        return view('event.show', [
            'event' => $event,
            'registeredCount' => $registeredCount,
            'registration' => $registration,
            'isRegistered' => $isRegistered,
            'isFull' => $isFull,
            'bankAccount' => $bankAccount,
        ]);
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        if ($event->status !== 'Approved' || ! $event->is_verified) {
            abort(404);
        }

        $isPaid = $event->isPaid();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => [$isPaid ? 'required' : 'nullable', 'string', 'max:30'],
        ];

        if ($isPaid) {
            $rules['sender_bank'] = ['nullable', 'string', 'max:100'];
            $rules['sender_name'] = ['nullable', 'string', 'max:150'];
            $rules['payment_notes'] = ['nullable', 'string', 'max:500'];
            $rules['payment_proof'] = ['required', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:2048'];
        }

        $validated = $request->validate($rules);

        $proofPath = null;
        if ($isPaid && $request->hasFile('payment_proof')) {
            $proofPath = Storage::url($request->file('payment_proof')->store('events/payments', 'public'));
        }

        $result = DB::transaction(function () use ($event, $request, $validated, $isPaid, $proofPath) {
            $lockedEvent = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();

            if ($lockedEvent->registrations()->where('user_id', $request->user()->id)->exists()) {
                return 'duplicate';
            }

            if ($lockedEvent->total_quota > 0
                && $lockedEvent->registrations()->whereIn('payment_status', ['confirmed', 'free'])->count() >= $lockedEvent->total_quota) {
                return 'full';
            }

            $registrationData = [
                'user_id' => $request->user()->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'amount' => $isPaid ? $lockedEvent->price_numeric : 0,
                'payment_status' => $isPaid ? 'pending' : 'free',
                'payment_proof' => $proofPath,
                'sender_bank' => $validated['sender_bank'] ?? null,
                'sender_name' => $validated['sender_name'] ?? null,
                'payment_notes' => $validated['payment_notes'] ?? null,
                'confirmed_at' => $isPaid ? null : now(),
            ];

            $lockedEvent->registrations()->create($registrationData);

            return 'registered';
        });

        if ($result === 'registered') {
            if ($isPaid) {
                return back()->with('success', "Pendaftaran dan bukti pembayaran event \"{$event->title}\" berhasil dikirim. Penyelenggara akan segera memverifikasi pembayaran Anda.");
            }

            return back()->with('success', "Pendaftaran event \"{$event->title}\" berhasil.");
        }

        return back()->with('error', $result === 'duplicate'
            ? 'Anda sudah terdaftar di event ini.'
            : 'Kuota event sudah penuh.');
    }

    public function reuploadProof(Request $request, Event $event): RedirectResponse
    {
        $registration = $event->registrations()
            ->where('user_id', $request->user()->id)
            ->where('payment_status', 'rejected')
            ->firstOrFail();

        $validated = $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:2048'],
            'sender_bank' => ['nullable', 'string', 'max:100'],
            'sender_name' => ['nullable', 'string', 'max:150'],
            'payment_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $proofPath = Storage::url($request->file('payment_proof')->store('events/payments', 'public'));

        $registration->update([
            'payment_proof' => $proofPath,
            'payment_status' => 'pending',
            'sender_bank' => $validated['sender_bank'] ?? $registration->sender_bank,
            'sender_name' => $validated['sender_name'] ?? $registration->sender_name,
            'payment_notes' => $validated['payment_notes'] ?? $registration->payment_notes,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah ulang dan sedang menunggu konfirmasi penyelenggara.');
    }

    public function cancel(Request $request, Event $event): RedirectResponse
    {
        $deleted = $event->registrations()
            ->where('user_id', $request->user()->id)
            ->delete();

        if ($deleted === 0) {
            return back()->with('error', 'Anda belum terdaftar di event ini.');
        }

        return back()->with('success', "Pendaftaran event \"{$event->title}\" dibatalkan.");
    }
}
