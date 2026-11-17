<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Community;
use App\Models\Company;
use App\Models\Event;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_user_only_sees_own_school_and_cannot_create(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active']);
        $own = $this->school(['user_id' => $schoolUser->id, 'name' => 'Own School', 'npsn' => '11111111']);
        $other = $this->school(['name' => 'Other School', 'npsn' => '22222222']);

        $this->actingAs($schoolUser)
            ->get(route('admin.schools.index'))
            ->assertRedirect(route('admin.schools.edit', $own->id));

        $this->actingAs($schoolUser)
            ->get(route('admin.schools.show', $other->id))
            ->assertNotFound();

        $this->actingAs($schoolUser)
            ->get(route('admin.schools.create'))
            ->assertRedirect(route('admin.schools.edit', $own->id));
    }

    public function test_company_user_only_sees_own_company(): void
    {
        $companyUser = User::factory()->create(['role' => 'company', 'status' => 'active']);
        $own = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'Own Company',
            'type' => 'Swasta',
            'industry' => 'Technology',
            'status' => 'Active',
        ]);
        $other = Company::create([
            'name' => 'Other Company',
            'type' => 'Swasta',
            'industry' => 'Finance',
            'status' => 'Active',
        ]);

        $this->actingAs($companyUser)
            ->get(route('admin.company.index'))
            ->assertOk()
            ->assertSee($own->name)
            ->assertDontSee($other->name);

        $this->actingAs($companyUser)
            ->get(route('admin.company.edit', $other->id))
            ->assertNotFound();
    }

    public function test_company_user_cannot_create_company_via_direct_post(): void
    {
        $companyUser = User::factory()->create(['role' => 'company', 'status' => 'active']);

        $this->actingAs($companyUser)->post(route('admin.company.store'), [
            'name' => 'PT Baru',
            'type' => 'Swasta',
            'industry' => 'Technology',
            'status' => 'Active',
        ])->assertForbidden();

        $this->assertDatabaseMissing('companies', ['name' => 'PT Baru']);
    }

    public function test_school_article_editor_only_sees_own_articles(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active']);
        $own = Article::create([
            'user_id' => $schoolUser->id,
            'title' => 'Own Article',
            'category' => 'Technology',
            'status' => 'published',
        ]);
        $other = Article::create([
            'title' => 'Other Article',
            'category' => 'Technology',
            'status' => 'published',
        ]);

        $this->actingAs($schoolUser)
            ->get(route('admin.articles.index'))
            ->assertOk()
            ->assertSee($own->title)
            ->assertDontSee($other->title);

        $this->actingAs($schoolUser)
            ->get(route('admin.articles.edit', $other->id))
            ->assertNotFound();
    }

    public function test_admin_can_create_and_delete_article(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Artikel Baru',
            'category' => 'Technology',
            'excerpt' => 'Ringkasan artikel.',
            'content' => 'Isi artikel.',
            'image_caption' => 'Ilustrasi arsitektur cloud modern',
            'tags' => 'Laravel, PHP',
            'status' => 'published',
        ])->assertRedirect(route('admin.articles.index'));

        $article = Article::where('title', 'Artikel Baru')->firstOrFail();
        $this->assertSame(['Laravel', 'PHP'], $article->tags);
        $this->assertSame('artikel-baru', $article->slug);
        $this->assertSame('Ilustrasi arsitektur cloud modern', $article->image_caption);

        $this->actingAs($admin)->put(route('admin.articles.update', $article->id), [
            'title' => 'Artikel Baru Diedit',
            'category' => 'Technology',
            'excerpt' => 'Ringkasan diedit.',
            'content' => 'Isi diedit.',
            'image_caption' => 'Caption gambar yang diperbarui',
            'status' => 'published',
        ])->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertSame('Caption gambar yang diperbarui', $article->image_caption);

        $this->actingAs($admin)
            ->delete(route('admin.articles.destroy', $article->id))
            ->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_can_update_and_delete_community(): void
    {
        $admin = $this->admin();
        $community = Community::create([
            'name' => 'Community Lama',
            'category' => 'Technology',
            'members' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($admin)->put(route('admin.community.update', $community->id), [
            'name' => 'Community Baru',
            'category' => 'Design',
            'description' => 'Komunitas baru.',
            'website' => 'https://example.com',
            'email' => 'hello@example.com',
            'status' => 'inactive',
        ])->assertRedirect(route('admin.community.index'));

        $this->assertDatabaseHas('communities', [
            'id' => $community->id,
            'name' => 'Community Baru',
            'category' => 'Design',
            'status' => 'inactive',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.community.destroy', $community->id))
            ->assertRedirect(route('admin.community.index'));

        $this->assertDatabaseMissing('communities', ['id' => $community->id]);
    }

    public function test_admin_can_create_update_and_delete_school(): void
    {
        $admin = $this->admin();
        $payload = [
            'npsn' => '33333333',
            'name' => 'SMK Baru',
            'institution_type' => 'SMK IT',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'location' => 'Jakarta Selatan',
            'status' => 'Active',
        ];

        $this->actingAs($admin)
            ->post(route('admin.schools.store'), $payload)
            ->assertRedirect(route('admin.schools.index'));

        $school = School::where('name', 'SMK Baru')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.schools.update', $school->id), [
            ...$payload,
            'name' => 'SMK Baru Diperbarui',
        ])->assertRedirect(route('admin.schools.index'));

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'SMK Baru Diperbarui',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.schools.destroy', $school->id))
            ->assertRedirect(route('admin.schools.index'));

        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }

    public function test_admin_can_create_and_delete_company(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.company.store'), [
            'name' => 'PT Baru',
            'type' => 'Swasta',
            'industry' => 'Technology',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'Active',
        ])->assertRedirect(route('admin.company.index'));

        $company = Company::where('name', 'PT Baru')->firstOrFail();
        $this->assertSame('Bandung, Jawa Barat', $company->location);

        $this->actingAs($admin)
            ->delete(route('admin.company.destroy', $company->id))
            ->assertRedirect(route('admin.company.index'));

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_school_user_can_access_events_and_only_sees_own_events(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active', 'email_verified_at' => now()]);
        $ownEvent = Event::create([
            'user_id' => $schoolUser->id,
            'title' => 'SMK Tech Expo',
            'organizer' => 'SMK 1',
            'organizer_type' => 'School',
            'category' => 'Workshop',
            'mode' => 'In-Person',
            'date' => '2026-11-15',
            'status' => 'Pending',
        ]);
        $otherEvent = Event::create([
            'title' => 'Company Cloud Summit',
            'organizer' => 'PT Google',
            'organizer_type' => 'Company',
            'category' => 'Conference',
            'mode' => 'Online',
            'date' => '2026-11-20',
            'status' => 'Approved',
        ]);

        $this->actingAs($schoolUser)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertSee($ownEvent->title)
            ->assertDontSee($otherEvent->title);

        $this->actingAs($schoolUser)
            ->get(route('admin.events.show', $ownEvent->id))
            ->assertOk()
            ->assertSee($ownEvent->title)
            ->assertDontSee('Approve Event'); // School cannot see approve button

        $this->actingAs($schoolUser)
            ->get(route('admin.events.show', $otherEvent->id))
            ->assertRedirect(route('admin.events.index'));
    }

    public function test_school_user_can_create_event_with_pending_status(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active', 'email_verified_at' => now()]);
        $this->school(['user_id' => $schoolUser->id, 'name' => 'SMK Telkom']);

        $this->actingAs($schoolUser)
            ->get(route('admin.events.create'))
            ->assertOk()
            ->assertSee('Buat Event Baru');

        $response = $this->actingAs($schoolUser)->post(route('admin.events.store'), [
            'title' => 'Lomba Coding SMK',
            'category' => 'Competition',
            'mode' => 'Online',
            'date' => '2026-12-01',
            'price' => 'Free',
            'total_quota' => 50,
            'description' => 'Kompetisi coding nasional',
        ]);

        $response->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', [
            'title' => 'Lomba Coding SMK',
            'user_id' => $schoolUser->id,
            'organizer' => 'SMK Telkom',
            'organizer_type' => 'School',
            'status' => 'Pending',
            'is_verified' => false,
        ]);
    }

    public function test_school_user_cannot_approve_or_reject_events(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active', 'email_verified_at' => now()]);
        $event = Event::create([
            'user_id' => $schoolUser->id,
            'title' => 'Own School Event',
            'organizer' => 'SMK',
            'organizer_type' => 'School',
            'category' => 'Workshop',
            'mode' => 'Online',
            'date' => '2026-11-15',
            'status' => 'Pending',
        ]);

        $this->actingAs($schoolUser)
            ->post(route('admin.events.approve', $event->id))
            ->assertForbidden();

        $this->actingAs($schoolUser)
            ->post(route('admin.events.reject', $event->id))
            ->assertForbidden();

        $this->assertEquals('Pending', $event->fresh()->status);
    }

    public function test_school_user_can_update_and_delete_own_event_but_not_others(): void
    {
        $schoolUser = User::factory()->create(['role' => 'school', 'status' => 'active', 'email_verified_at' => now()]);
        $ownEvent = Event::create([
            'user_id' => $schoolUser->id,
            'title' => 'Initial Title',
            'organizer' => 'SMK',
            'organizer_type' => 'School',
            'category' => 'Workshop',
            'mode' => 'Online',
            'date' => '2026-11-15',
            'status' => 'Pending',
        ]);
        $otherEvent = Event::create([
            'title' => 'Other Title',
            'organizer' => 'Company',
            'organizer_type' => 'Company',
            'category' => 'Seminar',
            'mode' => 'Online',
            'date' => '2026-11-15',
            'status' => 'Approved',
        ]);

        // Edit own event
        $this->actingAs($schoolUser)
            ->get(route('admin.events.edit', $ownEvent->id))
            ->assertOk()
            ->assertSee('Initial Title');

        $this->actingAs($schoolUser)
            ->put(route('admin.events.update', $ownEvent->id), [
                'title' => 'Updated Event Title',
                'category' => 'Workshop',
                'mode' => 'Hybrid',
                'date' => '2026-11-20',
            ])
            ->assertRedirect(route('admin.events.index'));

        $this->assertEquals('Updated Event Title', $ownEvent->fresh()->title);

        // Edit other event forbidden / not found
        $this->actingAs($schoolUser)
            ->get(route('admin.events.edit', $otherEvent->id))
            ->assertNotFound();

        // Delete other event not found
        $this->actingAs($schoolUser)
            ->delete(route('admin.events.destroy', $otherEvent->id))
            ->assertNotFound();

        // Delete own event succeeds
        $this->actingAs($schoolUser)
            ->delete(route('admin.events.destroy', $ownEvent->id))
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseMissing('events', ['id' => $ownEvent->id]);
    }

    public function test_university_user_dashboard_renders_cards(): void
    {
        $univUser = User::factory()->create(['role' => 'university', 'status' => 'active', 'email_verified_at' => now()]);
        $univ = University::create([
            'user_id' => $univUser->id,
            'name' => 'Universitas Indonesia Tech',
            'npsn' => '77889900',
            'type' => 'Negeri',
            'location' => 'Depok, Jawa Barat',
            'city' => 'Depok',
            'province' => 'Jawa Barat',
            'status' => 'Active',
        ]);

        Article::create([
            'user_id' => $univUser->id,
            'title' => 'Riset AI Kampus',
            'category' => 'Technology',
            'status' => 'published',
        ]);

        Event::create([
            'user_id' => $univUser->id,
            'title' => 'Seminar Riset Nasional',
            'organizer' => $univ->name,
            'organizer_type' => 'University',
            'category' => 'Seminar',
            'mode' => 'Online',
            'date' => '2026-12-10',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($univUser)->get(route('dashboard.university'));

        $response->assertOk()
            ->assertSee('Dashboard Universitas')
            ->assertSee('Universitas Indonesia Tech')
            ->assertSee('Artikel Kampus')
            ->assertSee('1 Artikel')
            ->assertSee('Event & Workshop')
            ->assertSee('1 Event');
    }

    public function test_university_user_redirects_to_edit_own_university_from_index(): void
    {
        $univUser = User::factory()->create(['role' => 'university', 'status' => 'active', 'email_verified_at' => now()]);
        $ownUniv = University::create([
            'user_id' => $univUser->id,
            'name' => 'Institut Teknologi Nusantara',
            'npsn' => '55667788',
            'type' => 'Swasta',
            'location' => 'Bandung, Jawa Barat',
            'status' => 'Active',
        ]);

        $this->actingAs($univUser)
            ->get(route('admin.univ.index'))
            ->assertRedirect(route('admin.univ.edit', $ownUniv->id));
    }

    public function test_university_user_can_manage_articles_and_only_sees_own(): void
    {
        $univUser = User::factory()->create(['role' => 'university', 'status' => 'active', 'email_verified_at' => now()]);
        $ownArticle = Article::create([
            'user_id' => $univUser->id,
            'title' => 'Artikel Kampus Sendiri',
            'category' => 'Technology',
            'status' => 'published',
        ]);
        $otherArticle = Article::create([
            'title' => 'Artikel Kampus Lain',
            'category' => 'Technology',
            'status' => 'published',
        ]);

        $this->actingAs($univUser)
            ->get(route('admin.articles.index'))
            ->assertOk()
            ->assertSee($ownArticle->title)
            ->assertDontSee($otherArticle->title);

        $this->actingAs($univUser)
            ->get(route('admin.articles.edit', $ownArticle->id))
            ->assertOk();

        $this->actingAs($univUser)
            ->get(route('admin.articles.edit', $otherArticle->id))
            ->assertNotFound();
    }

    public function test_university_user_can_create_and_manage_events(): void
    {
        $univUser = User::factory()->create(['role' => 'university', 'status' => 'active', 'email_verified_at' => now()]);
        University::create([
            'user_id' => $univUser->id,
            'name' => 'Universitas Gadjah Mada Tech',
            'npsn' => '44332211',
            'type' => 'Negeri',
            'location' => 'Yogyakarta',
            'status' => 'Active',
        ]);

        $this->actingAs($univUser)
            ->get(route('admin.events.create'))
            ->assertOk()
            ->assertSee('Universitas Gadjah Mada Tech');

        $response = $this->actingAs($univUser)->post(route('admin.events.store'), [
            'title' => 'Hackathon Mahasiswa Nasional',
            'category' => 'Competition',
            'mode' => 'Hybrid',
            'date' => '2026-12-15',
            'price' => 'Free',
            'total_quota' => 200,
            'description' => 'Hackathon tahunan kampus',
        ]);

        $response->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', [
            'title' => 'Hackathon Mahasiswa Nasional',
            'user_id' => $univUser->id,
            'organizer' => 'Universitas Gadjah Mada Tech',
            'organizer_type' => 'University',
            'status' => 'Pending',
            'is_verified' => false,
        ]);

        $event = Event::where('title', 'Hackathon Mahasiswa Nasional')->firstOrFail();

        // Cannot approve
        $this->actingAs($univUser)
            ->post(route('admin.events.approve', $event->id))
            ->assertForbidden();

        // Can edit own event
        $this->actingAs($univUser)
            ->put(route('admin.events.update', $event->id), [
                'title' => 'Hackathon Mahasiswa Nasional v2',
                'category' => 'Competition',
                'mode' => 'Hybrid',
                'date' => '2026-12-20',
            ])
            ->assertRedirect(route('admin.events.index'));

        $this->assertEquals('Hackathon Mahasiswa Nasional v2', $event->fresh()->title);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    private function school(array $attributes = []): School
    {
        return School::create(array_merge([
            'name' => 'School',
            'npsn' => '99999999',
            'institution_type' => 'SMK IT',
            'location' => 'Jakarta',
            'status' => 'Active',
        ], $attributes));
    }
}
