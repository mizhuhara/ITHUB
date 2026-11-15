<?php

namespace App\Http\Controllers;

use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        if ($request->user()->role === 'school') {
            return redirect()->route('dashboard.school');
        }

        if ($request->user()->role === 'university') {
            return redirect()->route('dashboard.university');
        }

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'portfolio_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
        ], [
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto profil harus berupa JPEG, PNG, JPG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 2MB.',
            'cv.file' => 'File CV harus berupa berkas dokumen.',
            'cv.mimes' => 'Format CV harus berupa file PDF (.pdf).',
            'cv.max' => 'Ukuran file CV maksimal 2MB.',
        ]);

        $payload = [
            'name' => $validated['name'],
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
        ];

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $payload['avatar'] = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $payload['avatar'] = ImageUploadService::storeAndOptimize($request->file('avatar'), 'avatars');
        }

        if ($request->boolean('remove_cv')) {
            if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
                Storage::disk('public')->delete($user->cv_path);
            }
            $payload['cv_path'] = null;
        } elseif ($request->hasFile('cv')) {
            if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
                Storage::disk('public')->delete($user->cv_path);
            }
            $payload['cv_path'] = $request->file('cv')->store('cvs', 'public');
        }

        $user->update($payload);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
