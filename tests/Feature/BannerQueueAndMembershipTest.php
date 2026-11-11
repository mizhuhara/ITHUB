<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerQueueAndMembershipTest extends TestCase
{
    use RefreshDatabase;

    private function createInstitutionUser(string $role = 'school', bool $activeMembership = true): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_premium_active' => $activeMembership,
            'membership_status' => $activeMembership ? 'active' : 'expired',
            'membership_expires_at' => $activeMembership ? now()->addMonths(1) : now()->subDay(),
        ]);

        if ($role === 'school') {
            School::create([
                'user_id' => $user->id,
                'npsn' => 'NPSN'.fake()->unique()->numerify('########'),
                'name' => 'SMK Test School',
                'status' => 'Active',
            ]);
        }

        return $user;
    }

    public function test_guest_and_normal_user_cannot_access_banner_management(): void
    {
        $this->get(route('admin.banners.index'))
            ->assertRedirect(route('login'));

        $regularUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($regularUser)
            ->get(route('admin.banners.index'))
            ->assertStatus(403);
    }

    public function test_institution_without_active_membership_cannot_add_banner(): void
    {
        $inactiveSchool = $this->createInstitutionUser('school', false);

        $this->actingAs($inactiveSchool)
            ->get(route('admin.banners.create'))
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('error');

        $this->actingAs($inactiveSchool)
            ->post(route('admin.banners.store'), [
                'title' => 'Promo Sekolah Tanpa Member',
                'image' => 'https://example.com/banner.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('banners', [
            'title' => 'Promo Sekolah Tanpa Member',
        ]);
    }

    public function test_institution_with_active_membership_can_create_banner(): void
    {
        $schoolUser = $this->createInstitutionUser('school', true);

        $this->actingAs($schoolUser)
            ->get(route('admin.banners.create'))
            ->assertOk()
            ->assertSee('Tambah Banner Baru');

        $this->actingAs($schoolUser)
            ->post(route('admin.banners.store'), [
                'title' => 'PPDB SMK Unggulan 2026',
                'subtitle' => 'Pendaftaran Gelombang 1 Dibuka',
                'image' => 'https://example.com/banner-ppdb.jpg',
                'link' => 'https://example.com/ppdb',
                'link_text' => 'Daftar Sekarang',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('banners', [
            'title' => 'PPDB SMK Unggulan 2026',
            'user_id' => $schoolUser->id,
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    public function test_institution_is_rate_limited_to_one_banner_per_week(): void
    {
        $companyUser = $this->createInstitutionUser('company', true);

        // 1st banner succeeds
        $this->actingAs($companyUser)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Perusahaan Minggu Ini',
                'image' => 'https://example.com/banner1.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success');

        // 2nd banner within 7 days is rejected
        $this->actingAs($companyUser)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Perusahaan Kedua (Harus Ditolak)',
                'image' => 'https://example.com/banner2.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('banners', [
            'title' => 'Banner Perusahaan Kedua (Harus Ditolak)',
        ]);
    }

    public function test_banners_exceeding_max_slots_enter_the_queue(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        // Clear existing banners and create exactly 5 active banners
        Banner::query()->delete();

        for ($i = 1; $i <= 5; $i++) {
            Banner::create([
                'title' => "Banner Aktif {$i}",
                'image' => "https://example.com/b{$i}.jpg",
                'status' => 'active',
                'is_active' => true,
                'started_at' => now(),
            ]);
        }

        $this->assertEquals(5, Banner::where('status', 'active')->where('is_active', true)->count());

        // 6th banner from institution enters queue
        $schoolUser = $this->createInstitutionUser('school', true);
        $this->actingAs($schoolUser)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Antrean Pertama',
                'image' => 'https://example.com/queue1.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success');

        $queuedBanner = Banner::where('title', 'Banner Antrean Pertama')->first();
        $this->assertNotNull($queuedBanner);
        $this->assertEquals('queued', $queuedBanner->status);
        $this->assertFalse($queuedBanner->is_active);
        $this->assertEquals(1, $queuedBanner->queue_position);

        // 7th banner enters queue at position 2
        $univUser = $this->createInstitutionUser('university', true);
        $this->actingAs($univUser)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Antrean Kedua',
                'image' => 'https://example.com/queue2.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success');

        $queuedBanner2 = Banner::where('title', 'Banner Antrean Kedua')->first();
        $this->assertNotNull($queuedBanner2);
        $this->assertEquals('queued', $queuedBanner2->status);
        $this->assertEquals(2, $queuedBanner2->queue_position);
    }

    public function test_queued_banner_is_automatically_promoted_when_active_banner_expires_or_is_deleted(): void
    {
        Banner::query()->delete();

        // 5 active banners, one expires
        for ($i = 1; $i <= 4; $i++) {
            Banner::create([
                'title' => "Banner Aktif {$i}",
                'image' => "https://example.com/b{$i}.jpg",
                'status' => 'active',
                'is_active' => true,
            ]);
        }

        // 5th banner is already expired
        $expiredBanner = Banner::create([
            'title' => 'Banner Mau Kadaluarsa',
            'image' => 'https://example.com/exp.jpg',
            'status' => 'active',
            'is_active' => true,
            'started_at' => now()->subDays(8),
            'expires_at' => now()->subDay(),
        ]);

        // Queued banner waiting
        $queued = Banner::create([
            'title' => 'Banner Siap Naik',
            'image' => 'https://example.com/next.jpg',
            'status' => 'queued',
            'is_active' => false,
            'queue_position' => 1,
        ]);

        Banner::syncActiveQueue();

        $expiredBanner->refresh();
        $this->assertEquals('expired', $expiredBanner->status);
        $this->assertFalse($expiredBanner->is_active);

        $queued->refresh();
        $this->assertEquals('active', $queued->status);
        $this->assertTrue($queued->is_active);
        $this->assertNull($queued->queue_position);
        $this->assertNotNull($queued->started_at);
        $this->assertNotNull($queued->expires_at);
    }

    public function test_homepage_shows_maximum_5_active_slides(): void
    {
        Banner::query()->delete();

        for ($i = 1; $i <= 7; $i++) {
            Banner::create([
                'title' => "Banner Test {$i}",
                'image' => "https://example.com/b{$i}.jpg",
                'status' => 'active',
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        $response = $this->get(route('welcome'));
        $response->assertOk();

        // Only 5 slides are passed to view
        $bannersInView = $response->viewData('banners');
        $this->assertCount(5, $bannersInView);
    }

    public function test_institution_cannot_edit_or_delete_other_users_banner(): void
    {
        $school1 = $this->createInstitutionUser('school', true);
        $school2 = $this->createInstitutionUser('school', true);

        $banner1 = Banner::create([
            'user_id' => $school1->id,
            'title' => 'Banner Sekolah 1',
            'image' => 'https://example.com/b1.jpg',
            'status' => 'active',
            'is_active' => true,
        ]);

        // School 2 attempts to edit School 1's banner
        $this->actingAs($school2)
            ->get(route('admin.banners.edit', $banner1->id))
            ->assertStatus(403);

        $this->actingAs($school2)
            ->put(route('admin.banners.update', $banner1->id), [
                'title' => 'Banner Dibajak',
                'image' => 'https://example.com/hacked.jpg',
            ])
            ->assertStatus(403);

        $this->actingAs($school2)
            ->delete(route('admin.banners.destroy', $banner1->id))
            ->assertStatus(403);

        // School 1 can edit and delete their own banner
        $this->actingAs($school1)
            ->get(route('admin.banners.edit', $banner1->id))
            ->assertOk();

        $this->actingAs($school1)
            ->put(route('admin.banners.update', $banner1->id), [
                'title' => 'Banner Sekolah 1 Diperbarui',
                'image' => 'https://example.com/b1-updated.jpg',
            ])
            ->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseHas('banners', [
            'id' => $banner1->id,
            'title' => 'Banner Sekolah 1 Diperbarui',
        ]);
    }

    public function test_admin_can_promote_queued_banner_directly(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $school = $this->createInstitutionUser('school', true);

        $queuedBanner = Banner::create([
            'user_id' => $school->id,
            'title' => 'Banner Queued to Promote',
            'image' => 'https://example.com/queue.jpg',
            'status' => 'queued',
            'is_active' => false,
            'queue_position' => 1,
        ]);

        // Non-admin cannot promote
        $this->actingAs($school)
            ->post(route('admin.banners.promote', $queuedBanner->id))
            ->assertStatus(403);

        // Admin can promote
        $this->actingAs($admin)
            ->post(route('admin.banners.promote', $queuedBanner->id))
            ->assertRedirect(route('admin.banners.index'))
            ->assertSessionHas('success');

        $queuedBanner->refresh();
        $this->assertEquals('active', $queuedBanner->status);
        $this->assertTrue($queuedBanner->is_active);
        $this->assertNull($queuedBanner->queue_position);
    }

    public function test_admin_can_set_banner_as_permanent_or_one_week(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        // Permanent banner
        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Admin Permanen',
                'image' => 'https://example.com/perm.jpg',
                'duration_mode' => 'permanent',
            ])
            ->assertRedirect(route('admin.banners.index'));

        $permBanner = Banner::where('title', 'Banner Admin Permanen')->first();
        $this->assertNotNull($permBanner);
        $this->assertNull($permBanner->expires_at);

        // 1-Week banner
        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Admin 1 Minggu',
                'image' => 'https://example.com/week.jpg',
                'duration_mode' => '1_week',
            ])
            ->assertRedirect(route('admin.banners.index'));

        $weekBanner = Banner::where('title', 'Banner Admin 1 Minggu')->first();
        $this->assertNotNull($weekBanner);
        $this->assertNotNull($weekBanner->expires_at);
        $this->assertTrue($weekBanner->expires_at->isFuture());
    }

    public function test_sidebar_displays_promosi_for_institution_and_banner_promosi_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $school = $this->createInstitutionUser('school', true);

        // Admin sees "Banner Promosi"
        $this->actingAs($admin)
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Banner Promosi');

        // School sees "Promosi"
        $this->actingAs($school)
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Promosi');
    }
}
