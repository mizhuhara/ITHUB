<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EducationDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_directory_uses_database_records(): void
    {
        $school = School::create([
            'npsn' => '12345678',
            'name' => 'SMK Test Nusantara',
            'institution_type' => 'SMK IT',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'Active',
            'tags' => ['RPL'],
            'total_students' => 100,
            'industry_partners' => 5,
        ]);

        $this->get(route('education.index'))
            ->assertOk()
            ->assertSee('SMK Test Nusantara');

        $this->get(route('education.show', $school->id))
            ->assertOk()
            ->assertSee('SMK Test Nusantara')
            ->assertSee('RPL');
    }

    public function test_campus_directory_uses_database_records(): void
    {
        $university = University::create([
            'npsn' => '87654321',
            'name' => 'Universitas Test',
            'type' => 'Negeri',
            'city' => 'Depok',
            'province' => 'Jawa Barat',
            'status' => 'Active',
            'tags' => ['Computer Science'],
            'total_students' => 1000,
            'total_faculties' => 10,
            'accreditation' => 'A',
            'founded' => 2000,
        ]);

        $this->get(route('campus.index'))
            ->assertOk()
            ->assertSee('Universitas Test')
            ->assertSee('campusSearchInput');

        $this->get(route('campus.show', $university->id))
            ->assertOk()
            ->assertSee('Universitas Test')
            ->assertSee('Computer Science');
    }

    public function test_inactive_institution_is_hidden(): void
    {
        $school = School::create([
            'npsn' => '99999999',
            'name' => 'SMK Inactive',
            'status' => 'Inactive',
        ]);

        $this->get(route('education.index'))
            ->assertOk()
            ->assertDontSee('SMK Inactive');

        $this->get(route('education.show', $school->id))->assertNotFound();
    }

    public function test_school_admin_can_update_school_with_logo_and_gallery(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'school', 'status' => 'active']);
        $school = School::create([
            'user_id' => $user->id,
            'npsn' => '55667788',
            'name' => 'SMK Lama',
            'institution_type' => 'SMK IT',
            'location' => 'Jakarta Barat, DKI Jakarta',
            'status' => 'Active',
            'description' => 'Deskripsi lama sekolah.',
        ]);

        $logoFile = UploadedFile::fake()->image('logo_sekolah.png', 200, 200);
        $galleryPhoto1 = UploadedFile::fake()->image('lab_komputer.jpg', 800, 600);
        $galleryPhoto2 = UploadedFile::fake()->image('suasana_kelas.jpg', 800, 600);

        $response = $this->actingAs($user)->put(route('admin.schools.update', $school->id), [
            'name' => 'SMK TI Bintang Masa Depan',
            'npsn' => '55667788',
            'institution_type' => 'SMK IT',
            'location' => 'Bandung, Jawa Barat',
            'address' => 'Jl. Pendidikan No. 100, Bandung',
            'description' => '<p>SMK TI unggulan dengan fokus AI dan Cyber Security.</p>',
            'competencies' => ['Rekayasa Perangkat Lunak', 'Teknik Komputer Jaringan', 'Cyber Security'],
            'latitude' => -6.1753924,
            'longitude' => 106.8271528,
            'map_link' => 'https://www.google.com/maps?q=-6.1753924,106.8271528',
            'logo' => $logoFile,
            'gallery' => [$galleryPhoto1, $galleryPhoto2],
        ]);

        $response->assertRedirect(route('admin.schools.edit', $school->id));
        $response->assertSessionHas('success');

        $school->refresh();
        $this->assertSame('SMK TI Bintang Masa Depan', $school->name);
        $this->assertNotNull($school->logo_url);
        $this->assertSame('logo_sekolah.png', $school->logo_name);
        $this->assertCount(2, $school->gallery);
        $this->assertSame(['Rekayasa Perangkat Lunak', 'Teknik Komputer Jaringan', 'Cyber Security'], $school->competencies);
        $this->assertEquals(-6.1753924, $school->latitude);

        // Verify public user-facing view reflects the updated data
        $publicDetail = $this->get(route('education.show', $school->id));
        $publicDetail->assertOk()
            ->assertSee('SMK TI Bintang Masa Depan')
            ->assertSee('SMK TI unggulan dengan fokus AI dan Cyber Security.')
            ->assertSee('Rekayasa Perangkat Lunak')
            ->assertSee('Teknik Komputer Jaringan')
            ->assertSee('Cyber Security')
            ->assertSee('https://www.google.com/maps?q=-6.1753924,106.8271528')
            ->assertSee($school->logo_url)
            ->assertSee('Galeri Sekolah')
            ->assertSee($school->gallery[0])
            ->assertSee($school->gallery[1]);

        // Verify public directory listing reflects updated name and logo
        $publicDirectory = $this->get(route('education.index'));
        $publicDirectory->assertOk()
            ->assertSee('SMK TI Bintang Masa Depan')
            ->assertSee($school->logo_url);
    }
}
