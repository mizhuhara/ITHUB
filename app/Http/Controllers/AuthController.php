<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use App\Notifications\InstitutionVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function register(Request $request): RedirectResponse
    {
        $role = $request->input('role');
        $institutionRoles = ['school', 'university', 'company'];

        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,school,university,company'],
        ];

        if (in_array($role, $institutionRoles, true)) {
            $rules['org_contact'] = ['required', 'string', 'max:255'];
            $rules['org_phone'] = ['required', 'string', 'max:30', 'unique:users,org_phone'];
            $rules['org_address'] = ['required', 'string'];
            $rules['org_doc'] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
        }

        $data = $request->validate($rules);

        $orgDocPath = null;
        if (in_array($role, $institutionRoles, true) && $request->hasFile('org_doc')) {
            $orgDocPath = $request->file('org_doc')->store('org-docs', 'local');
        }

        $status = $role === 'user' ? 'active' : 'pending';
        $isInstitution = in_array($role, $institutionRoles, true);

        $user = DB::transaction(function () use ($data, $role, $status, $orgDocPath, $isInstitution) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $role,
                'status' => $status,
                'email_verified_at' => $role === 'user' ? now() : null,
                'org_contact' => $data['org_contact'] ?? null,
                'org_phone' => $data['org_phone'] ?? null,
                'org_address' => $data['org_address'] ?? null,
                'org_doc' => $orgDocPath,
                'membership_status' => $isInstitution ? 'trial' : 'active',
                'membership_plan' => $isInstitution ? 'trial_1_month' : null,
                'membership_started_at' => $isInstitution ? now() : null,
                'membership_expires_at' => $isInstitution ? now()->addMonth() : null,
                'is_premium_active' => true,
            ]);

            if ($role === 'user' && ! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            // Auto-create data institusi dari data registrasi (1 akun = 1 data).
            // User login nanti tinggal melengkapi data yang kurang, bukan menambah baru.
            if ($role === 'school') {
                School::create([
                    'user_id' => $user->id,
                    'npsn' => 'BF-'.$user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['org_phone'] ?? null,
                    'address' => $data['org_address'] ?? null,
                    'status' => 'Pending',
                ]);
            } elseif ($role === 'university') {
                University::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['org_phone'] ?? null,
                    'address' => $data['org_address'] ?? null,
                    'status' => 'Pending',
                ]);
            } elseif ($role === 'company') {
                Company::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['org_phone'] ?? null,
                    'address' => $data['org_address'] ?? null,
                    'status' => 'Pending',
                ]);
            }

            return $user;
        });

        if ($role !== 'user' && $status === 'active') {
            $user->sendEmailVerificationNotification();
        }

        // Notify super admin to verify the institution account
        if (in_array($role, $institutionRoles, true)) {
            $notifyTo = User::where('email', User::ADMIN_EMAIL)->first();
            if ($notifyTo) {
                $notifyTo->notify(new InstitutionVerificationNotification($user));
            }
        }

        if ($role === 'user') {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended($this->redirectFor($user));
        }

        // Jangan login user pending - arahkan ke halaman sukses registrasi
        return redirect()->route('register.success')->with('user_id', $user->id);

    }

    public function registerSuccess(Request $request): View
    {
        $userId = $request->session()->get('user_id');
        $user = null;

        if ($userId) {
            $user = User::find($userId);
        }

        return view('auth.register-success', ['user' => $user]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $user = Auth::user();

        if (strtolower((string) $user->status) !== 'active') {
            Auth::logout();
            $request->session()->regenerateToken();

            $message = match (strtolower((string) $user->status)) {
                'pending' => 'Akun masih menunggu verifikasi super admin.',
                'rejected' => 'Pendaftaran ditolak. Hubungi admin untuk informasi.',
                'hidden' => 'Akun Anda sedang disembunyikan/dinonaktifkan (membership berakhir). Hubungi admin untuk memperpanjang membership.',
                default => 'Akun Anda tidak aktif. Hubungi admin untuk informasi.',
            };

            return back()->withErrors(['email' => $message])->withInput();
        }

        $request->session()->regenerate();

        if ($user->role === 'user' || strtolower((string) $user->status) === 'active') {
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
        } elseif (! $user->hasVerifiedEmail()) {
            return redirect()->intended(route('verification.notice'));
        }

        return redirect()->intended($this->redirectFor($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Jika email terdaftar, tautan reset password sudah dikirim.');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login.');
        }

        $message = match ($status) {
            Password::INVALID_USER => 'Email tidak terdaftar.',
            Password::INVALID_TOKEN => 'Tautan reset tidak valid atau sudah kedaluwarsa.',
            default => 'Gagal mereset password.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }

    private function redirectFor(User $user): string
    {
        return match ($user->role) {
            'super_admin', 'admin' => route('admin.dashboard'),
            'school' => route('dashboard.school'),
            'university' => route('dashboard.university'),
            'company' => route('dashboard.company'),
            'user' => route('welcome'),
            default => route('welcome'),
        };
    }
}
