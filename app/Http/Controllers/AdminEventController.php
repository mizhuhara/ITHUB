<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\School;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminEventController extends Controller
{
    /**
     * Display a listing of events with filters, search, and pagination.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isInstitution = $user && in_array($user->role, ['school', 'university', 'company'], true);
        $isSchool = $user && $user->role === 'school';
        $query = $this->visibleEvents();

        // Status filter
        $status = strtolower($request->query('status', 'all'));
        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', ucfirst($status));
        }

        // Type filter (only for admin users, not institution roles)
        $activeType = strtolower($request->query('type', 'all'));
        if (! $isInstitution && $activeType !== 'all') {
            $query->whereRaw('LOWER(organizer_type) = ?', [$activeType]);
        }

        // Search
        $searchQuery = trim($request->query('q', ''));
        if (! empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('organizer', 'like', "%{$searchQuery}%")
                    ->orWhere('category', 'like', "%{$searchQuery}%")
                    ->orWhere('location', 'like', "%{$searchQuery}%");
            });
        }

        $events = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $visible = $this->visibleEvents();

        return view('admin.events.index', [
            'events' => $events,
            'activeType' => $activeType,
            'activeStatus' => $status,
            'searchQuery' => $searchQuery,
            'isSchool' => $isInstitution,
            'userRole' => $user?->role,
            'totalEntries' => (clone $visible)->count(),
            'pendingCount' => (clone $visible)->where('status', 'Pending')->count(),
            'approvedCount' => (clone $visible)->where('status', 'Approved')->count(),
            'rejectedCount' => (clone $visible)->where('status', 'Rejected')->count(),
        ]);
    }

    /**
     * Show form for creating a new event.
     */
    public function create(): View
    {
        $user = auth()->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $school = $user->role === 'school' ? School::where('user_id', $user->id)->first() : null;
        $univ = $user->role === 'university' ? University::where('user_id', $user->id)->first() : null;
        $company = $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;
        $isInstitution = in_array($user->role, ['school', 'university', 'company'], true);

        return view('admin.events.create', [
            'school' => $school,
            'univ' => $univ,
            'company' => $company,
            'userRole' => $user->role,
            'isSchool' => $isInstitution,
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'mode' => ['required', 'string', 'in:Online,In-Person,Hybrid'],
            'price' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'string', 'max:100'],
            'time' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'total_quota' => ['nullable', 'integer', 'min:1'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            ...$this->certificateRules(),
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Storage::url($request->file('image')->store('events', 'public'));
        }

        $certificateTemplate = $request->hasFile('certificate_template')
            ? $request->file('certificate_template')->store('events/certificates', 'public')
            : null;

        $certificateEnabled = $request->boolean('certificate_enabled');

        if ($certificateEnabled && ! $certificateTemplate) {
            return back()->withErrors(['certificate_template' => 'Template sertifikat wajib diunggah.'])->withInput();
        }

        if ($user->role === 'school') {
            $school = School::where('user_id', $user->id)->first();
            $organizer = $school ? $school->name : $user->name;
            $organizerType = 'School';
            $status = 'Pending';
            $isVerified = false;
        } elseif ($user->role === 'university') {
            $univ = University::where('user_id', $user->id)->first();
            $organizer = $univ ? $univ->name : $user->name;
            $organizerType = 'University';
            $status = 'Pending';
            $isVerified = false;
        } elseif ($user->role === 'company') {
            $company = Company::where('user_id', $user->id)->first();
            $organizer = $company ? $company->name : $user->name;
            $organizerType = 'Company';
            $status = 'Pending';
            $isVerified = false;
        } else {
            $organizer = $request->input('organizer', $user->name);
            $organizerType = $request->input('organizer_type', 'Company');
            $status = 'Approved';
            $isVerified = true;
        }

        $event = Event::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'organizer' => $organizer,
            'organizer_type' => $organizerType,
            'category' => $validated['category'],
            'mode' => $validated['mode'],
            'price' => ! empty($validated['price']) ? $validated['price'] : 'Free',
            'date' => $validated['date'],
            'short_date' => $validated['date'],
            'full_date' => $validated['date'],
            'time' => ! empty($validated['time']) ? $validated['time'] : '09:00 - 15:00 (WIB)',
            'location' => ! empty($validated['location']) ? $validated['location'] : ($validated['mode'] === 'Online' ? 'Online Webinar' : null),
            'quota' => ! empty($validated['total_quota']) ? "{$validated['total_quota']} Peserta" : null,
            'total_quota' => ! empty($validated['total_quota']) ? $validated['total_quota'] : null,
            'contact_email' => ! empty($validated['contact_email']) ? $validated['contact_email'] : $user->email,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath ?: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=500&fit=crop',
            'status' => $status,
            'is_verified' => $isVerified,
            'certificate_enabled' => $certificateEnabled,
            'certificate_template' => $certificateTemplate,
            'certificate_name_x' => $validated['certificate_name_x'] ?? 50,
            'certificate_name_y' => $validated['certificate_name_y'] ?? 50,
            'certificate_name_font_size' => $validated['certificate_name_font_size'] ?? 48,
            'certificate_name_color' => $validated['certificate_name_color'] ?? '#111827',
        ]);

        $msg = in_array($user->role, ['school', 'university', 'company'], true)
            ? "Event '{$event->title}' berhasil dibuat dan sedang menunggu verifikasi admin."
            : "Event '{$event->title}' berhasil dipublikasikan.";

        return redirect()->route('admin.events.index')->with('success', $msg);
    }

    /**
     * Display details of a specific event.
     */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        $event = $this->visibleEvents()->find($id);

        if (! $event) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Event tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $query = $event->registrations()->with('user');

        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->get();

        return view('admin.events.show', [
            'event' => $event,
            'registrations' => $registrations,
            'searchQuery' => $request->query('q', ''),
            'isSchool' => in_array(auth()->user()?->role, ['school', 'university', 'company'], true),
            'userRole' => auth()->user()?->role,
        ]);
    }

    /**
     * Show form for editing an existing event.
     */
    public function edit(int $id): View
    {
        $user = auth()->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $event = $this->findVisibleEvent($id);
        $school = $user->role === 'school' ? School::where('user_id', $user->id)->first() : null;
        $univ = $user->role === 'university' ? University::where('user_id', $user->id)->first() : null;
        $company = $user->role === 'company' ? Company::where('user_id', $user->id)->first() : null;
        $isInstitution = in_array($user->role, ['school', 'university', 'company'], true);

        return view('admin.events.edit', [
            'event' => $event,
            'school' => $school,
            'univ' => $univ,
            'company' => $company,
            'userRole' => $user->role,
            'isSchool' => $isInstitution,
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $event = $this->findVisibleEvent($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'mode' => ['required', 'string', 'in:Online,In-Person,Hybrid'],
            'price' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'string', 'max:100'],
            'time' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'total_quota' => ['nullable', 'integer', 'min:1'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            ...$this->certificateRules(),
        ]);

        $payload = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'mode' => $validated['mode'],
            'price' => ! empty($validated['price']) ? $validated['price'] : 'Free',
            'date' => $validated['date'],
            'short_date' => $validated['date'],
            'full_date' => $validated['date'],
            'time' => ! empty($validated['time']) ? $validated['time'] : $event->time,
            'location' => ! empty($validated['location']) ? $validated['location'] : ($validated['mode'] === 'Online' ? 'Online Webinar' : null),
            'quota' => ! empty($validated['total_quota']) ? "{$validated['total_quota']} Peserta" : null,
            'total_quota' => ! empty($validated['total_quota']) ? $validated['total_quota'] : null,
            'contact_email' => ! empty($validated['contact_email']) ? $validated['contact_email'] : $event->contact_email,
            'contact_phone' => $validated['contact_phone'] ?? $event->contact_phone,
            'description' => $validated['description'] ?? $event->description,
            'certificate_enabled' => $request->boolean('certificate_enabled'),
            'certificate_name_x' => $validated['certificate_name_x'] ?? 50,
            'certificate_name_y' => $validated['certificate_name_y'] ?? 50,
            'certificate_name_font_size' => $validated['certificate_name_font_size'] ?? 48,
            'certificate_name_color' => $validated['certificate_name_color'] ?? '#111827',
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = Storage::url($request->file('image')->store('events', 'public'));
        }

        if ($request->hasFile('certificate_template')) {
            $payload['certificate_template'] = $request->file('certificate_template')->store('events/certificates', 'public');
        }

        if ($request->boolean('certificate_enabled') && empty($payload['certificate_template'] ?? $event->certificate_template)) {
            return back()->withErrors(['certificate_template' => 'Template sertifikat wajib diunggah.'])->withInput();
        }

        if (! in_array($user->role, ['school', 'university'], true) && $request->filled('organizer')) {
            $payload['organizer'] = $request->input('organizer');
            $payload['organizer_type'] = $request->input('organizer_type', $event->organizer_type);
        }

        $event->update($payload);

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$event->title}' berhasil diperbarui.");
    }

    /**
     * Toggle the event's completed status, which activates participant certificates.
     */
    public function complete(int $id): RedirectResponse
    {
        $event = $this->findVisibleEvent($id);

        $completed = $event->completed_at === null;

        $event->update([
            'completed_at' => $completed ? now() : null,
        ]);

        return back()->with('success', $completed
            ? "Event '{$event->title}' ditandai selesai. Sertifikat peserta aktif."
            : "Status selesai event '{$event->title}' dibatalkan.");
    }

    /**
     * Toggle participant check-in attendance.
     */
    public function toggleAttendance(Event $event, EventRegistration $registration): RedirectResponse
    {
        $this->findVisibleEvent($event->id);
        abort_unless($registration->event_id === $event->id, 404);

        $checkedIn = $registration->checked_in_at === null;

        $registration->update([
            'checked_in_at' => $checkedIn ? now() : null,
        ]);

        return back()->with('success', $checkedIn
            ? "Check-in {$registration->name} berhasil."
            : "Check-in {$registration->name} dibatalkan.");
    }

    /**
     * Approve an event request (Admin/Super Admin only).
     */
    public function approve(int $id): RedirectResponse
    {
        abort_if(! in_array(auth()->user()->role, ['super_admin', 'admin'], true), 403, 'Akses ditolak. Hanya admin yang dapat memverifikasi event.');

        $event = Event::findOrFail($id);
        $event->update([
            'status' => 'Approved',
            'is_verified' => true,
        ]);

        return redirect()->back()
            ->with('success', "Event '{$event->title}' telah berhasil disetujui (Approved).");
    }

    /**
     * Reject an event request (Admin/Super Admin only).
     */
    public function reject(int $id): RedirectResponse
    {
        abort_if(! in_array(auth()->user()->role, ['super_admin', 'admin'], true), 403, 'Akses ditolak. Hanya admin yang dapat menolak event.');

        $event = Event::findOrFail($id);
        $event->update([
            'status' => 'Rejected',
            'is_verified' => false,
        ]);

        return redirect()->back()
            ->with('success', "Event '{$event->title}' telah ditolak (Rejected).");
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        abort_if(! in_array($user->role, ['super_admin', 'admin', 'school', 'university', 'company'], true), 403, 'Akses ditolak.');

        $event = $this->findVisibleEvent($id);
        $title = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$title}' berhasil dihapus dari database.");
    }

    /**
     * Shared validation rules for certificate settings.
     *
     * @return array<string, array<int, string>>
     */
    private function certificateRules(): array
    {
        return [
            'certificate_enabled' => ['nullable', 'boolean'],
            'certificate_template' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            'certificate_name_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'certificate_name_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'certificate_name_font_size' => ['nullable', 'integer', 'min:8', 'max:200'],
            'certificate_name_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    /**
     * Query events visible to the current user:
     * - role school / university / company: only their own events
     * - super_admin / admin: all events
     */
    private function visibleEvents()
    {
        return Event::query()->visibleTo(auth()->user());
    }

    /**
     * Retrieve visible event with ownership check (throws 404 if not found or not owned).
     */
    private function findVisibleEvent(int $id): Event
    {
        return $this->visibleEvents()->findOrFail($id);
    }
}
