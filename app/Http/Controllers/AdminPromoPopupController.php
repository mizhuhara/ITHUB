<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPromoPopupController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_if(auth()->user()?->role !== 'super_admin', 403, 'Hanya Super Admin yang dapat mengelola promo popup.');
    }

    public function edit(): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.promo-popup.edit', [
            'settings' => AppSetting::getPromoPopupSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'enabled' => ['nullable'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_image' => ['nullable'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail) {
                if (filled($value) && ! str_starts_with($value, '/') && ! in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)) {
                    $fail('Link tombol harus berupa URL http/https atau path yang diawali /.');
                }
            }],
            'theme' => ['required', 'in:light,dark,brand'],
        ]);

        $current = AppSetting::getPromoPopupSettings();
        $image = $current['image'];

        if ($request->boolean('remove_image')) {
            $image = '';
        } elseif ($request->hasFile('image_file')) {
            $image = Storage::disk('public')->url($request->file('image_file')->store('promo-popup', 'public'));
        } elseif (! empty($validated['image'])) {
            $image = $validated['image'];
        }

        AppSetting::setPromoPopupSettings([
            'enabled' => $request->boolean('enabled'),
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? '',
            'image' => $image,
            'button_text' => $validated['button_text'] ?? '',
            'button_link' => $validated['button_link'] ?? '',
            'theme' => $validated['theme'],
        ]);

        return back()->with('success', 'Pengaturan promo popup berhasil disimpan.');
    }
}
