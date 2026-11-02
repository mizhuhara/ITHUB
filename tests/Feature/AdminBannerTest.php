<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBannerTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_view_banner_index(): void
    {
        Banner::create([
            'title' => 'Test Banner Promo',
            'subtitle' => 'Subtitle Promo',
            'image' => 'https://example.com/banner.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.banners.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Banner Promo');
        $response->assertSee('Kelola Banner');
    }

    public function test_regular_user_cannot_view_banner_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.banners.index'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_banner(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.banners.store'), [
            'title' => 'Banner Baru Super Admin',
            'subtitle' => 'Deskripsi banner',
            'image' => 'https://example.com/new-banner.jpg',
            'link' => 'https://example.com/promo',
            'link_text' => 'Buka Promo',
            'is_active' => '1',
            'sort_order' => 0,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'title' => 'Banner Baru Super Admin',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_update_banner(): void
    {
        $banner = Banner::create([
            'title' => 'Original Banner',
            'image' => 'https://example.com/orig.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('admin.banners.update', $banner->id), [
            'title' => 'Updated Banner Title',
            'subtitle' => 'Updated Subtitle',
            'image' => 'https://example.com/orig.jpg',
            'is_active' => '1',
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'title' => 'Updated Banner Title',
            'sort_order' => 5,
        ]);
    }

    public function test_super_admin_can_delete_banner(): void
    {
        $banner = Banner::create([
            'title' => 'Banner to Delete',
            'image' => 'https://example.com/del.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.banners.destroy', $banner->id));

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_active_banners_display_on_homepage(): void
    {
        Banner::create([
            'title' => 'Banner Tampil di Beranda',
            'subtitle' => 'Subtitle yang tampil',
            'image' => 'https://example.com/beranda.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Banner Tersembunyi',
            'image' => 'https://example.com/hidden.jpg',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('welcome'));

        $response->assertStatus(200);
        $response->assertSee('Banner Tampil di Beranda');
        $response->assertDontSee('Banner Tersembunyi');
    }
}

