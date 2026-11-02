<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobListingFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    private function createSampleJob(array $attributes = []): JobListing
    {
        return JobListing::create(array_merge([
            'title' => 'Software Engineer',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Mid',
            'salary_range' => '15.000.000 - 20.000.000',
            'description' => 'Test job description.',
            'company' => 'IndoTech Corp',
            'location' => 'Jakarta, Indonesia',
            'company_size' => '51-200 Employees',
            'is_active' => true,
            'status' => 'Active',
            'tab_status' => 'active',
        ], $attributes));
    }

    public function test_admin_jobs_index_page_can_be_rendered(): void
    {
        $admin = $this->createAdminUser();
        $this->createSampleJob();

        $response = $this->actingAs($admin)->get(route('admin.jobs.index'));

        $response->assertStatus(200);
        $response->assertSee('Job Management');
        $response->assertSee('ALL JOBS');
    }

    public function test_admin_can_create_a_job_posting(): void
    {
        $admin = $this->createAdminUser();

        $payload = [
            'title' => 'Software Architect Feature Test',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Senior',
            'salary_range' => '30.000.000 - 45.000.000',
            'description' => 'Architecting high-scale enterprise systems.',
            'requirements' => '10+ years in software engineering.',
            'company' => 'IndoTech Test Labs',
            'location' => 'Jakarta, Indonesia',
            'company_size' => '201-500 Employees',
            'is_active' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.jobs.store'), $payload);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('job_listings', [
            'title' => 'Software Architect Feature Test',
            'company' => 'IndoTech Test Labs',
        ]);
    }

    public function test_admin_can_create_a_job_posting_with_image_file(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $file = UploadedFile::fake()->image('job_banner.jpg', 600, 400);

        $payload = [
            'title' => 'AI Engineer Upload Test',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Senior',
            'salary_range' => '25.000.000 - 35.000.000',
            'description' => 'Working on advanced LLM pipelines.',
            'company' => 'IndoTech AI Lab',
            'location' => 'Bandung, Indonesia',
            'company_size' => '51-200 Employees',
            'is_active' => '1',
            'image_file' => $file,
        ];

        $response = $this->actingAs($admin)->post(route('admin.jobs.store'), $payload);

        $response->assertRedirect(route('admin.jobs.index'));
        $job = JobListing::where('title', 'AI Engineer Upload Test')->first();
        $this->assertNotNull($job);
        $this->assertNotNull($job->image);
        $this->assertStringContainsString('/storage/jobs/', $job->image);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $job->image));
    }

    public function test_admin_can_create_a_job_posting_with_image_url(): void
    {
        $admin = $this->createAdminUser();

        $payload = [
            'title' => 'DevOps Specialist URL Test',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Mid',
            'salary_range' => '20.000.000 - 30.000.000',
            'description' => 'Managing cloud infrastructure and CI/CD.',
            'company' => 'IndoTech Cloud Inc',
            'location' => 'Surabaya, Indonesia',
            'company_size' => '201-500 Employees',
            'is_active' => '1',
            'image' => 'https://example.com/custom-job-banner.png',
        ];

        $response = $this->actingAs($admin)->post(route('admin.jobs.store'), $payload);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('job_listings', [
            'title' => 'DevOps Specialist URL Test',
            'image' => 'https://example.com/custom-job-banner.png',
        ]);
    }

    public function test_admin_can_update_a_job_posting(): void
    {
        $admin = $this->createAdminUser();
        $job = $this->createSampleJob();

        $response = $this->actingAs($admin)->put(route('admin.jobs.update', $job->id), [
            'title' => 'Lead Software Architect Updated',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Senior',
            'salary_range' => '35.000.000 - 50.000.000',
            'description' => 'Updated description.',
            'company' => 'IndoTech Test Labs',
            'location' => 'Jakarta, Indonesia',
            'company_size' => '201-500 Employees',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('job_listings', [
            'id' => $job->id,
            'title' => 'Lead Software Architect Updated',
        ]);
    }

    public function test_admin_can_update_a_job_posting_with_new_image_file(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $job = $this->createSampleJob(['image' => 'https://example.com/old-banner.jpg']);

        $file = UploadedFile::fake()->image('updated_banner.png', 800, 500);

        $response = $this->actingAs($admin)->put(route('admin.jobs.update', $job->id), [
            'title' => 'Updated Job Title with Image',
            'department' => 'Engineering',
            'type' => 'Full-time',
            'category' => 'jobs',
            'experience' => 'Senior',
            'salary_range' => '25.000.000 - 35.000.000',
            'description' => 'Updated description with new banner.',
            'company' => 'IndoTech Labs',
            'location' => 'Jakarta, Indonesia',
            'company_size' => '51-200 Employees',
            'is_active' => '1',
            'image_file' => $file,
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        $freshJob = $job->fresh();
        $this->assertStringContainsString('/storage/jobs/', $freshJob->image);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $freshJob->image));
    }

    public function test_admin_can_view_job_detail(): void
    {
        $admin = $this->createAdminUser();
        $job = $this->createSampleJob(['title' => 'Product Designer Specialist']);

        $response = $this->actingAs($admin)->get(route('admin.jobs.show', $job->id));

        $response->assertStatus(200);
        $response->assertSee('Product Designer Specialist');
    }

    public function test_career_index_page_displays_jobs(): void
    {
        $this->createSampleJob(['title' => 'Career Showcase Engineer']);

        $response = $this->get(route('career.index'));

        $response->assertStatus(200);
        $response->assertSee('Career Showcase Engineer');
    }

    public function test_career_apply_increments_applicants_count(): void
    {
        $job = $this->createSampleJob();
        $initialApplicants = $job->applicants_count;

        $response = $this->post(route('career.apply.store', ['type' => 'job', 'id' => $job->id]), [
            'full_name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'phone' => '081234567890',
            'cover_letter' => 'Saya sangat tertarik untuk melamar posisi ini karena memiliki pengalaman relevan yang kuat selama lebih dari lima tahun.',
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertEquals($initialApplicants + 1, $job->fresh()->applicants_count);
    }

    public function test_admin_can_delete_a_job_posting(): void
    {
        $admin = $this->createAdminUser();
        $job = $this->createSampleJob();
        $id = $job->id;

        $response = $this->actingAs($admin)->delete(route('admin.jobs.destroy', $id));

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseMissing('job_listings', ['id' => $id]);
    }

    public function test_admin_jobs_index_filters_by_status_and_type(): void
    {
        $admin = $this->createAdminUser();
        $this->createSampleJob(['title' => 'Senior Backend Engineer', 'status' => 'Active', 'type' => 'Full-time', 'department' => 'Engineering']);
        $this->createSampleJob(['title' => 'UI UX Designer Intern', 'status' => 'Draft', 'type' => 'Internship', 'department' => 'Design']);

        // Filter by status=Draft
        $response = $this->actingAs($admin)->get(route('admin.jobs.index', ['status' => 'Draft']));
        $response->assertStatus(200);
        $response->assertSee('UI UX Designer Intern');
        $response->assertDontSee('Senior Backend Engineer');

        // Filter by type=Full-time
        $response = $this->actingAs($admin)->get(route('admin.jobs.index', ['type' => 'Full-time']));
        $response->assertStatus(200);
        $response->assertSee('Senior Backend Engineer');
        $response->assertDontSee('UI UX Designer Intern');
    }

    public function test_admin_can_export_jobs_to_excel_csv(): void
    {
        $admin = $this->createAdminUser();
        $this->createSampleJob(['title' => 'Exportable Job Engineer', 'company' => 'Export Tech Ltd']);

        $response = $this->actingAs($admin)->get(route('admin.jobs.export'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'jobs_export_'));
    }

    public function test_career_kerja_tab_displays_job_seekers(): void
    {
        JobSeeker::create([
            'name' => 'Aditya Pratama',
            'email' => 'aditya@example.com',
            'job_title' => 'Senior Fullstack Developer',
            'experience_level' => 'Senior',
            'availability' => 'Segera',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('career.index', ['type' => 'kerja']));

        $response->assertStatus(200);
        $response->assertViewIs('career.kerja');
        $response->assertSee('Aditya Pratama');
        $response->assertSee('Senior Fullstack Developer');
        $response->assertSee('Filter Pencari Kerja');
    }

    public function test_career_kerja_tab_supports_sidebar_filters(): void
    {
        JobSeeker::create([
            'name' => 'Budi Laravel',
            'email' => 'budi@example.com',
            'job_title' => 'Backend Developer',
            'experience_level' => 'Entry',
            'availability' => 'Segera',
            'skills' => ['Laravel', 'PHP'],
            'status' => 'aktif',
        ]);

        JobSeeker::create([
            'name' => 'Siti React',
            'email' => 'siti@example.com',
            'job_title' => 'Frontend Developer',
            'experience_level' => 'Senior',
            'availability' => 'Bulan Depan',
            'skills' => ['React', 'JavaScript'],
            'status' => 'aktif',
        ]);

        // Filter by experience = Entry
        $resExp = $this->get(route('career.index', ['type' => 'kerja', 'experience' => ['Entry']]));
        $resExp->assertOk();
        $resExp->assertSee('Budi Laravel');
        $resExp->assertDontSee('Siti React');

        // Filter by availability = Bulan Depan
        $resAvail = $this->get(route('career.index', ['type' => 'kerja', 'availability' => ['Bulan Depan']]));
        $resAvail->assertOk();
        $resAvail->assertSee('Siti React');
        $resAvail->assertDontSee('Budi Laravel');

        // Filter by skills = Laravel
        $resSkill = $this->get(route('career.index', ['type' => 'kerja', 'skills' => ['Laravel']]));
        $resSkill->assertOk();
        $resSkill->assertSee('Budi Laravel');
        $resSkill->assertDontSee('Siti React');
    }

    public function test_user_can_view_seeker_create_page(): void
    {
        $response = $this->get(route('career.seeker.create'));

        $response->assertStatus(200);
        $response->assertViewIs('career.seeker-create');
        $response->assertSee('Daftarkan Diri Sebagai Pencari Kerja');
    }

    public function test_user_can_view_seeker_detail_page(): void
    {
        $seeker = JobSeeker::create([
            'name' => 'Aditya Pratama',
            'email' => 'aditya.detail@example.com',
            'job_title' => 'Senior Fullstack Developer',
            'experience_level' => 'Senior',
            'availability' => 'Segera',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('career.seeker.show', $seeker->id));

        $response->assertStatus(200);
        $response->assertViewIs('career.seeker-show');
        $response->assertSee('Aditya Pratama');
        $response->assertSee('Senior Fullstack Developer');
    }

    public function test_user_can_register_as_job_seeker(): void
    {
        $payload = [
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'phone' => '081298765432',
            'location' => 'Bandung',
            'job_title' => 'UI/UX Designer',
            'skills' => 'Figma, Adobe XD, Tailwind',
            'experience_level' => 'Mid',
            'expected_salary' => '10-15 jt',
            'about' => 'Desainer berpengalaman lebih dari 3 tahun di industri startup.',
            'portfolio_url' => 'https://portfolio.dewi.dev',
            'linkedin_url' => 'https://linkedin.com/in/dewilestari',
            'availability' => 'Segera',
        ];

        $response = $this->post(route('career.seeker.store'), $payload);

        $seeker = JobSeeker::where('email', 'dewi@example.com')->firstOrFail();
        $response->assertRedirect(route('career.seeker.show', $seeker->id));
        $this->assertDatabaseHas('job_seekers', [
            'email' => 'dewi@example.com',
            'name' => 'Dewi Lestari',
            'job_title' => 'UI/UX Designer',
            'experience_level' => 'Mid',
            'status' => 'aktif',
        ]);
    }

    public function test_career_index_page_is_paginated_and_renders_pagination_links(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $this->createSampleJob([
                'title' => "Software Engineer {$i}",
            ]);
        }

        $response = $this->get(route('career.index'));
        $response->assertOk();
        $response->assertSee('cr-pagination');
        $response->assertSee('Software Engineer 8');
        $response->assertDontSee('Software Engineer 2');

        $pageTwo = $this->get(route('career.index', ['page' => 2]));
        $pageTwo->assertOk();
        $pageTwo->assertSee('Software Engineer 2');
        $pageTwo->assertSee('Software Engineer 1');
    }

    public function test_career_kerja_tab_is_paginated_and_renders_pagination_links(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            JobSeeker::create([
                'name' => "Kandidat {$i}",
                'email' => "kandidat{$i}@example.com",
                'job_title' => "Developer {$i}",
                'experience_level' => 'Mid',
                'availability' => 'Segera',
                'status' => 'aktif',
            ]);
        }

        $response = $this->get(route('career.index', ['type' => 'kerja']));
        $response->assertOk();
        $response->assertSee('sk-pagination');
        $response->assertSee('Kandidat 8');
        $response->assertDontSee('Kandidat 2');

        $pageTwo = $this->get(route('career.index', ['type' => 'kerja', 'page' => 2]));
        $pageTwo->assertOk();
        $pageTwo->assertSee('Kandidat 2');
        $pageTwo->assertSee('Kandidat 1');
    }
}
