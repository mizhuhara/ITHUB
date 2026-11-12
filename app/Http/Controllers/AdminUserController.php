<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of system users with search & filter support.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Search filter (Name, Email, Role)
        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        // Status filter
        if ($request->filled('status') && strtolower($request->query('status')) !== 'all') {
            $status = strtolower($request->query('status'));
            $query->where('status', $status);
        }

        // Role filter
        if ($request->filled('role') && strtolower($request->query('role')) !== 'all') {
            $role = strtolower($request->query('role'));
            if ($role === 'user') {
                $query->where('role', 'user');
            } elseif ($role === 'school') {
                $query->where(function ($sub) {
                    $sub->where('role', 'school')->orWhere('role', 'school_admin');
                });
            } elseif ($role === 'company') {
                $query->where(function ($sub) {
                    $sub->where('role', 'company')->orWhere('role', 'company_hr');
                });
            } elseif ($role === 'university') {
                $query->where(function ($sub) {
                    $sub->where('role', 'university')->orWhere('role', 'univ_rep');
                });
            } elseif ($role === 'admin' || $role === 'super_admin') {
                $query->where(function ($sub) {
                    $sub->where('role', 'super_admin')
                        ->orWhere('role', 'admin')
                        ->orWhere('role', 'superadmin');
                });
            } else {
                $query->where('role', 'like', "%{$role}%");
            }
        }

        // Sorting
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'oldest' => $query->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'role_asc' => $query->orderBy('role', 'asc')->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };

        $users = $query->paginate(8)->withQueryString();

        $totalUsersCount = User::count();
        $activeUsersCount = User::whereIn('status', ['active', 'Active'])->count();
        $pendingUsersCount = User::whereIn('status', ['pending', 'Pending'])->count();
        $hiddenUsersCount = User::whereIn('status', ['hidden', 'Hidden'])->count();

        return view('admin.users.index', [
            'users' => $users,
            'totalUsersCount' => $totalUsersCount,
            'activeUsersCount' => $activeUsersCount,
            'pendingUsersCount' => $pendingUsersCount,
            'hiddenUsersCount' => $hiddenUsersCount,
            'activeRole' => $request->query('role', 'all'),
            'activeStatus' => $request->query('status', 'all'),
            'activeSort' => $sort,
            'searchQuery' => $request->query('q', ''),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
            'role' => ['required', Rule::in($this->assignableRoles())],
            'status' => ['required', 'in:active,pending,rejected,inactive,hidden'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => strtolower($validated['role']),
            'status' => strtolower($validated['status']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" berhasil ditambahkan.");
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($this->isElevated($user) && ! $this->currentUserIsSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Hanya super admin yang dapat mengubah akun admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
            'role' => ['required', Rule::in($this->assignableRoles())],
            'status' => ['required', 'in:active,pending,rejected,inactive,hidden'],
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh user lain.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => strtolower($validated['role']),
            'status' => strtolower($validated['status']),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        if ($payload['status'] === 'active' && ! $user->hasVerifiedEmail()) {
            $payload['email_verified_at'] = now();
        }

        DB::transaction(function () use ($user, $payload) {
            $user->update($payload);

            $institutionStatus = $payload['status'] === 'active' ? 'Active' : 'Inactive';
            $this->syncInstitutionStatus($user, $institutionStatus);
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Data user \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Sembunyikan atau tampilkan kembali akun pengguna (Membership toggle).
     */
    public function toggleHide(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($this->isElevated($user) && ! $this->currentUserIsSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Hanya super admin yang dapat mengubah status akun admin.');
        }

        // Prevent hiding own account
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menyembunyikan akun Anda sendiri.');
        }

        // Prevent hiding main super admin
        if ($user->email === User::ADMIN_EMAIL || $user->role === 'super_admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun Super Admin tidak dapat disembunyikan.');
        }

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

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($this->isElevated($user) && ! $this->currentUserIsSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Hanya super admin yang dapat menghapus akun admin.');
        }

        // Prevent self deletion
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting main super admin
        if ($user->email === User::ADMIN_EMAIL) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun Super Admin utama tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" berhasil dihapus.");
    }

    /**
     * Role yang boleh ditetapkan oleh pengguna yang sedang login.
     *
     * @return array<int, string>
     */
    private function assignableRoles(): array
    {
        if ($this->currentUserIsSuperAdmin()) {
            return ['super_admin', 'admin', 'school', 'university', 'company', 'user'];
        }

        return ['school', 'university', 'company', 'user'];
    }

    private function isElevated(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin'], true);
    }

    private function currentUserIsSuperAdmin(): bool
    {
        return $this->authUser()?->role === 'super_admin';
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
