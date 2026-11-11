<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoPopupTest extends TestCase
{
    use RefreshDatabase;

    private function enablePopup(array $overrides = []): void
    {
        AppSetting::setPromoPopupSettings(array_merge([
            'enabled' => true,
            'title' => 'Promo Eksklusif Test',
            'subtitle' => 'Penawaran terbatas untuk pengunjung beranda.',
            'image' => 'https://example.com/promo.jpg',
            'button_text' => 'Lihat Promo',
            'button_link' => '/career',
            'theme' => 'light',
        ], $overrides));
    }

    public function test_popup_shows_on_homepage_once_per_browser_session(): void
    {
        $this->enablePopup();

        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Promo Eksklusif Test')
            ->assertSee('Penawaran terbatas untuk pengunjung beranda.');

        $this->assertTrue(session()->has('promo_popup_seen'));

        $this->get(route('welcome'))
            ->assertOk()
            ->assertDontSee('Promo Eksklusif Test');
    }

    public function test_popup_does_not_show_when_disabled(): void
    {
        $this->enablePopup(['enabled' => false]);

        $this->get(route('welcome'))
            ->assertOk()
            ->assertDontSee('Promo Eksklusif Test');

        $this->assertFalse(session()->has('promo_popup_seen'));
    }

    public function test_super_admin_does_not_see_popup_on_homepage(): void
    {
        $this->enablePopup();

        $superAdmin = User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('welcome'))
            ->assertOk()
            ->assertDontSee('Promo Eksklusif Test');

        $this->assertFalse(session()->has('promo_popup_seen'));
    }

    public function test_regular_user_sees_popup_on_homepage(): void
    {
        $this->enablePopup();

        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('welcome'))
            ->assertOk()
            ->assertSee('Promo Eksklusif Test');
    }

    public function test_popup_shows_again_after_login(): void
    {
        $this->enablePopup();

        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->withSession(['promo_popup_seen' => true])
            ->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('welcome'));

        $this->assertFalse(session()->has('promo_popup_seen'));

        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Promo Eksklusif Test');
    }

    public function test_super_admin_can_view_promo_popup_settings(): void
    {
        $superAdmin = User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->enablePopup();

        $this->actingAs($superAdmin)
            ->get(route('admin.promo-popup.edit'))
            ->assertOk()
            ->assertSee('Promo Popup')
            ->assertSee('Promo Eksklusif Test');
    }

    public function test_super_admin_can_update_promo_popup_settings(): void
    {
        $superAdmin = User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.promo-popup.update'), [
                'enabled' => '1',
                'title' => 'Promo Baru',
                'subtitle' => 'Deskripsi promo baru.',
                'image' => 'https://example.com/new-promo.jpg',
                'button_text' => 'Ambil Promo',
                'button_link' => 'https://example.com/promo',
                'theme' => 'brand',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $settings = AppSetting::getPromoPopupSettings();

        $this->assertTrue($settings['enabled']);
        $this->assertSame('Promo Baru', $settings['title']);
        $this->assertSame('brand', $settings['theme']);
    }

    public function test_regular_user_cannot_access_promo_popup_settings(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('admin.promo-popup.edit'))
            ->assertForbidden();
    }
}
