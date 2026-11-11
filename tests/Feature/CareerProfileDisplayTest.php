<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobListing;
use App\Models\JobSeeker;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerProfileDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_career_page_displays_real_company_logo_or_user_avatar(): void
    {
        $companyUser = User::factory()->create([
            'role' => 'company',
            'name' => 'Tech Corp',
            'avatar' => 'avatars/poster.jpg',
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'Tech Corp',
            'logo_url' => 'https://example.com/company-logo.png',
        ]);

        $job = JobListing::create([
            'user_id' => $companyUser->id,
            'title' => 'Software Engineer',
            'company' => 'Tech Corp',
            'description' => 'Great job opportunity',
            'location' => 'Jakarta',
            'category' => 'jobs',
            'is_active' => true,
            'status' => 'Active',
        ]);

        $response = $this->get(route('career.index'));

        $response->assertStatus(200);
        $response->assertSee('Software Engineer');
        $response->assertSee('Tech Corp');
        $response->assertSee('https://example.com/company-logo.png');
        $response->assertSee('Job Provider');
    }

    public function test_education_page_displays_lihat_profile_button(): void
    {
        School::create([
            'npsn' => '12345678',
            'name' => 'SMK TI Bali Global',
            'status' => 'Active',
            'city' => 'Denpasar',
            'province' => 'Bali',
            'location' => 'Denpasar, Bali',
            'address' => 'Jl. Tukad Citarum',
        ]);

        $response = $this->get(route('education.index'));

        $response->assertStatus(200);
        $response->assertSee('Lihat Profile');
    }

    public function test_siap_kerja_page_displays_real_user_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Developer',
            'email' => 'budi@example.com',
            'avatar' => 'avatars/budi.jpg',
        ]);

        $seeker = JobSeeker::create([
            'user_id' => $user->id,
            'name' => 'Budi Developer',
            'email' => 'budi@example.com',
            'job_title' => 'Senior Frontend Developer',
            'experience_level' => 'Senior',
            'availability' => 'Segera',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('career.index', ['type' => 'kerja']));

        $response->assertStatus(200);
        $response->assertSee('Budi Developer');
        $response->assertSee('Senior Frontend Developer');
        $response->assertSee($seeker->photo_url);
    }

    public function test_seeker_show_page_displays_photo_and_verification_badge(): void
    {
        $user = User::factory()->create([
            'name' => 'Citra Designer',
            'email' => 'citra@example.com',
            'avatar' => 'avatars/citra.jpg',
        ]);

        $seeker = JobSeeker::create([
            'user_id' => $user->id,
            'name' => 'Citra Designer',
            'email' => 'citra@example.com',
            'job_title' => 'UI/UX Lead',
            'experience_level' => 'Mid',
            'availability' => 'Segera',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('career.seeker.show', $seeker->id));

        $response->assertStatus(200);
        $response->assertSee('Citra Designer');
        $response->assertSee('UI/UX Lead');
        $response->assertSee('Terverifikasi');
        $response->assertSee($seeker->photo_url);
    }

    public function test_user_can_register_as_job_seeker_with_photo_upload(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Doni Santoso',
            'email' => 'doni@example.com',
        ]);

        $photo = UploadedFile::fake()->image('doni.jpg', 300, 300);

        $response = $this->actingAs($user)->post(route('career.seeker.store'), [
            'name' => 'Doni Santoso',
            'email' => 'doni@example.com',
            'job_title' => 'Backend Specialist',
            'experience_level' => 'Entry',
            'availability' => 'Segera',
            'photo' => $photo,
        ]);

        $seeker = JobSeeker::where('email', 'doni@example.com')->first();
        $this->assertNotNull($seeker);
        $this->assertEquals($user->id, $seeker->user_id);
        $this->assertNotNull($seeker->photo_url);
        Storage::disk('public')->assertExists($seeker->getRawOriginal('photo_url'));

        $response->assertRedirect(route('career.seeker.show', $seeker->id));
    }

    public function test_welcome_page_displays_real_jobs_with_company_logo_and_verified_partner(): void
    {
        $companyUser = User::factory()->create([
            'role' => 'company',
            'name' => 'Mitra Solusi Digital',
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'Mitra Solusi Digital',
            'logo_url' => 'https://example.com/mitra-logo.png',
        ]);

        $job = JobListing::create([
            'user_id' => $companyUser->id,
            'title' => 'Cloud Solutions Architect',
            'company' => 'Mitra Solusi Digital',
            'description' => 'Real architecture job opportunity',
            'location' => 'Jakarta (Hybrid)',
            'category' => 'jobs',
            'is_active' => true,
            'status' => 'Active',
            'skills' => ['AWS', 'Terraform', 'Kubernetes'],
            'total_views' => 250,
            'deadline' => '30 Jan 2027',
        ]);

        $response = $this->get(route('welcome'));

        $response->assertStatus(200);
        $response->assertSee('Lowongan Pekerjaan Terbaru');
        $response->assertSee('Cloud Solutions Architect');
        $response->assertSee('Mitra Solusi Digital');
        $response->assertSee('https://example.com/mitra-logo.png');
        $response->assertSee('Diposting oleh mitra resmi Mitra Solusi Digital');
        $response->assertSee('250');
        $response->assertSee('Kubernetes');
        $response->assertSee(route('career.apply', ['type' => 'job', 'id' => $job->id]));
    }
}
