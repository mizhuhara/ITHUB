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
}
