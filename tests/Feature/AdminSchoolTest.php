<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSchoolTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    public function test_store_returns_json_for_async_request(): void
    {
        $payload = [
            'name' => 'SMK Telkom Jakarta',
            'npsn' => '20109988',
            'institution_type' => 'SMK IT',
            'location' => 'Jakarta Selatan',
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.schools.store'), $payload);

        $response->assertCreated()
            ->assertJsonPath('message', 'Sekolah "SMK Telkom Jakarta" berhasil ditambahkan.');

        $this->assertDatabaseHas('schools', [
            'name' => 'SMK Telkom Jakarta',
            'npsn' => '20109988',
        ]);
    }

    public function test_store_still_redirects_for_regular_form_submit(): void
    {
        $payload = [
            'name' => 'SMA Negeri 1 Bandung',
            'npsn' => '10101010',
            'institution_type' => 'SMA IT',
            'location' => 'Bandung',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.schools.store'), $payload);

        $response->assertRedirect(route('admin.schools.index'));

        $this->assertSame(1, School::count());
    }

    public function test_update_returns_json_for_async_request(): void
    {
        $school = School::create([
            'name' => 'SMK Lama',
            'npsn' => '33333333',
            'institution_type' => 'SMK',
            'location' => 'Surabaya',
        ]);

        $payload = [
            'name' => 'SMK Baru',
            'npsn' => '33333333',
            'institution_type' => 'SMK IT',
            'location' => 'Surabaya',
        ];

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.schools.update', $school->id), $payload);

        $response->assertOk()
            ->assertJsonPath('message', 'Data sekolah "SMK Baru" berhasil diperbarui.');

        $this->assertSame('SMK Baru', $school->fresh()->name);
    }

    public function test_super_admin_can_update_school_views_without_npsn(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $school = School::create([
            'name' => 'SMK Awal',
            'npsn' => '44444444',
            'institution_type' => 'SMK IT',
            'location' => 'Jakarta',
            'views' => 10,
        ]);

        $response = $this->actingAs($superAdmin)
            ->put(route('admin.schools.update', $school->id), [
                'name' => 'SMK Update',
                'institution_type' => 'SMK IT',
                'location' => 'Jakarta',
                'views' => 750,
            ]);

        $response->assertRedirect(route('admin.schools.index'));

        $fresh = $school->fresh();
        $this->assertSame('SMK Update', $fresh->name);
        $this->assertSame('44444444', $fresh->npsn);
        $this->assertSame(750, $fresh->views);
    }

    public function test_super_admin_can_create_school_without_npsn_and_with_views(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.schools.store'), [
                'name' => 'SMK Tanpa NPSN',
                'institution_type' => 'SMK IT',
                'location' => 'Yogyakarta',
                'views' => 250,
            ]);

        $response->assertRedirect(route('admin.schools.index'));

        $school = School::where('name', 'SMK Tanpa NPSN')->first();
        $this->assertNotNull($school);
        $this->assertNotEmpty($school->npsn);
        $this->assertSame(250, $school->views);
    }
}
