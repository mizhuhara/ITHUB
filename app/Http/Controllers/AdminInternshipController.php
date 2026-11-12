<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminInternshipController extends Controller
{
    /**
     * Display a listing of internships.
     */
    public function index(Request $request): View
    {
        $query = Internship::with('user');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && strtolower($request->query('status')) !== 'all') {
            $status = strtolower($request->query('status'));
            $query->where('status', $status);
        }

        $query->orderByDesc('id');
        $internships = $query->paginate(10)->withQueryString();

        $totalPostings = Internship::count();
        $activeInternships = Internship::where('status', 'active')->count();
        $inactiveInternships = Internship::where('status', 'inactive')->count();

        return view('admin.internship.index', [
            'internships' => $internships,
            'currentTab' => $request->query('tab', 'all'),
            'totalPostings' => $totalPostings,
            'activeInternships' => $activeInternships,
            'inactiveInternships' => $inactiveInternships,
            'search' => $request->query('search', ''),
        ]);
    }

    /**
     * Show the form for creating a new internship.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.internship.create', compact('users'));
    }

    /**
     * Store a newly created internship in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('user_id') && Auth::check()) {
            $request->merge(['user_id' => Auth::id()]);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,completed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('internships', 'public');
        }

        $internship = Internship::create($validated);

        return redirect()->route('admin.internships.index')
            ->with('success', "Internship \"{$internship->title}\" berhasil ditambahkan.");
    }

    /**
     * Show detail of an internship.
     */
    public function show(int $id): View
    {
        $internship = Internship::with('user')->findOrFail($id);

        return view('admin.internship.show', compact('internship'));
    }

    /**
     * Update the specified internship in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $internship = Internship::findOrFail($id);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,completed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($internship->profile_picture && Storage::disk('public')->exists($internship->profile_picture)) {
                Storage::disk('public')->delete($internship->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('internships', 'public');
        }

        $internship->update($validated);

        return redirect()->route('admin.internships.index')
            ->with('success', "Internship \"{$internship->title}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified internship from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $internship = Internship::findOrFail($id);
        $title = $internship->title;

        // Delete profile picture if exists
        if ($internship->profile_picture && Storage::disk('public')->exists($internship->profile_picture)) {
            Storage::disk('public')->delete($internship->profile_picture);
        }

        $internship->delete();

        return redirect()->route('admin.internships.index')
            ->with('success', "Internship \"{$title}\" berhasil dihapus.");
    }
}
