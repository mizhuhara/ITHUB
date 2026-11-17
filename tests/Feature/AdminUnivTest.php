<?php

namespace Tests\Feature;

use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUnivTest extends TestCase
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

    public function test_can_list_universities(): void
    {
        University::factory()->create([
            'name' => 'Universitas Negeri Jakarta',
            'npsn' => '10101010',
            'type' => 'Negeri',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'location' => 'Jakarta, DKI Jakarta',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.univ.index'));

        $response->assertStatus(200);
        $response->assertSee('Universitas Negeri Jakarta');
    }

    public function test_can_filter_universities_by_search(): void
    {
        University::factory()->create(['name' => 'Universitas Padjadjaran', 'npsn' => '99887766', 'location' => 'Sumedang']);
        University::factory()->create(['name' => 'Universitas Udayana', 'npsn' => '11223344', 'location' => 'Denpasar']);

        $response = $this->actingAs($this->admin)->get(route('admin.univ.index', ['search' => 'Padjadjaran']));

        $response->assertStatus(200);
        $response->assertSee('Universitas Padjadjaran');
        $response->assertDontSee('Universitas Udayana');
    }

    public function test_can_create_university(): void
    {
        $payload = [
            'name' => 'Universitas Brawijaya',
            'npsn' => '20202020',
            'type' => 'Negeri',
            'location' => 'Malang, Jawa Timur',
            'accreditation' => 'A',
            'website' => 'https://ub.ac.id',
            'description' => 'Universitas unggulan di Malang',
            'address' => 'Jl. Veteran, Malang',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.univ.store'), $payload);

        $response->assertRedirect(route('admin.univ.index'));
        $this->assertDatabaseHas('universities', [
            'name' => 'Universitas Brawijaya',
            'city' => 'Malang',
        ]);
    }

    public function test_can_show_university(): void
    {
        $univ = University::factory()->create(['name' => 'Universitas Sebelas Maret']);

        $response = $this->actingAs($this->admin)->get(route('admin.univ.show', $univ->id));

        $response->assertStatus(200);
        $response->assertSee('Universitas Sebelas Maret');
    }

    public function test_can_update_university(): void
    {
        $univ = University::factory()->create([
            'name' => 'Universitas Lama',
            'npsn' => '30303030',
            'type' => 'Swasta',
            'location' => 'Surakarta, Jawa Tengah',
        ]);

        $payload = [
            'name' => 'Universitas Baru',
            'npsn' => '30303030',
            'type' => 'Negeri',
            'location' => 'Solo, Jawa Tengah',
            'accreditation' => 'A',
            'description' => 'Deskripsi baru',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.univ.update', $univ->id), $payload);

        $response->assertRedirect(route('admin.univ.edit', $univ->id));
        $this->assertDatabaseHas('universities', [
            'id' => $univ->id,
            'name' => 'Universitas Baru',
            'type' => 'Negeri',
        ]);
    }

    public function test_can_delete_university(): void
    {
        $univ = University::factory()->create(['name' => 'Universitas Hapus']);

        $response = $this->actingAs($this->admin)->delete(route('admin.univ.destroy', $univ->id));

        $response->assertRedirect(route('admin.univ.index'));
        $this->assertDatabaseMissing('universities', [
            'id' => $univ->id,
        ]);
    }
}
