<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use App\Notifications\CommunityApprovedNotification;
use App\Notifications\CommunityRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommunityApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@indotech.id',
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@indotech.id',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user@indotech.id',
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_user_can_create_community_which_defaults_to_pending_approval(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->regularUser)->post(route('community.store'), [
            'name' => 'Komunitas AI Nusantara',
            'category' => 'Technology',
            'description' => 'Komunitas praktisi kecerdasan buatan Indonesia.',
            'mode' => 'hybrid',
            'type' => 'public',
            'location' => 'Jakarta / Online',
            'language' => 'Bahasa Indonesia',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect(route('community.my'));
        $response->assertSessionHas('success');

        $community = Community::where('name', 'Komunitas AI Nusantara')->first();
        $this->assertNotNull($community);
        $this->assertEquals('pending', $community->status);
        $this->assertEquals('pending', $community->approval_status);
        $this->assertEquals('hybrid', $community->mode);
        $this->assertEquals($this->regularUser->id, $community->user_id);

        // Creator should be admin member
        $this->assertTrue(
            $community->members()
                ->where('users.id', $this->regularUser->id)
                ->wherePivot('role', 'admin')
                ->exists()
        );
    }

    public function test_pending_community_does_not_appear_on_public_directory_or_search(): void
    {
        $pendingCommunity = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Rahasia Belum Disetujui',
            'category' => 'Technology',
            'description' => 'Menunggu persetujuan admin.',
            'status' => 'pending',
            'approval_status' => 'pending',
            'mode' => 'online',
            'members' => 1,
        ]);

        $approvedCommunity = Community::create([
            'name' => 'Komunitas Resmi Aktif',
            'category' => 'Technology',
            'description' => 'Komunitas aktif.',
            'status' => 'active',
            'approval_status' => 'approved',
            'mode' => 'online',
            'members' => 5,
        ]);

        // Public index
        $response = $this->get(route('community.index'));
        $response->assertOk();
        $response->assertSee('Komunitas Resmi Aktif');
        $response->assertDontSee('Komunitas Rahasia Belum Disetujui');

        // Search
        $searchResponse = $this->get(route('community.index', ['search' => 'Rahasia']));
        $searchResponse->assertOk();
        $searchResponse->assertDontSee('Komunitas Rahasia Belum Disetujui');
    }

    public function test_pending_community_cannot_be_accessed_publicly_by_other_users_but_can_be_previewed_by_creator_and_super_admin(): void
    {
        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Preview Test',
            'category' => 'Technology',
            'status' => 'pending',
            'approval_status' => 'pending',
            'mode' => 'online',
        ]);

        $otherUser = User::factory()->create();

        // Guest / unauthenticated gets 404
        $this->get(route('community.show', $community))->assertNotFound();

        // Other regular user gets 404
        $this->actingAs($otherUser)->get(route('community.show', $community))->assertNotFound();

        // Creator can preview
        $creatorResponse = $this->actingAs($this->regularUser)->get(route('community.show', $community));
        $creatorResponse->assertOk();
        $creatorResponse->assertSee('Pratinjau Komunitas');
        $creatorResponse->assertSee('Menunggu Persetujuan');

        // Super Admin can preview
        $adminResponse = $this->actingAs($this->superAdmin)->get(route('community.show', $community));
        $adminResponse->assertOk();
        $adminResponse->assertSee('Pratinjau Komunitas');
    }

    public function test_super_admin_can_view_community_approvals_list_and_details(): void
    {
        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Data Science Club',
            'category' => 'Technology',
            'description' => 'Belajar data science bersama.',
            'mode' => 'hybrid',
            'type' => 'public',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Index
        $response = $this->actingAs($this->superAdmin)->get(route('admin.community-approvals.index'));
        $response->assertOk();
        $response->assertSee('Approval Komunitas');
        $response->assertSee('Data Science Club');
        $response->assertSee($this->regularUser->name);

        // Show detail
        $showResponse = $this->actingAs($this->superAdmin)->get(route('admin.community-approvals.show', $community));
        $showResponse->assertOk();
        $showResponse->assertSee('Data Science Club');
        $showResponse->assertSee('Belajar data science bersama.');
        $showResponse->assertSee('Hybrid');
    }

    public function test_super_admin_can_approve_community(): void
    {
        Notification::fake();

        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Backend Go',
            'category' => 'Technology',
            'description' => 'Go developers.',
            'status' => 'pending',
            'approval_status' => 'pending',
            'mode' => 'online',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.community-approvals.approve', $community));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $community->refresh();
        $this->assertEquals('approved', $community->approval_status);
        $this->assertEquals('active', $community->status);
        $this->assertEquals($this->superAdmin->id, $community->approved_by);
        $this->assertNotNull($community->approved_at);
        $this->assertNull($community->rejected_by);
        $this->assertNull($community->rejection_reason);

        // Creator should be admin member
        $this->assertTrue(
            $community->members()
                ->where('users.id', $this->regularUser->id)
                ->wherePivot('role', 'admin')
                ->exists()
        );

        // Notification sent to creator
        Notification::assertSentTo($this->regularUser, CommunityApprovedNotification::class);

        // Now appears in public directory
        $publicResponse = $this->get(route('community.index'));
        $publicResponse->assertOk();
        $publicResponse->assertSee('Komunitas Backend Go');
    }

    public function test_super_admin_cannot_reject_community_without_reason(): void
    {
        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Tanpa Alasan',
            'category' => 'Technology',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.community-approvals.reject', $community), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);

        $community->refresh();
        $this->assertEquals('pending', $community->approval_status);
    }

    public function test_super_admin_can_reject_community_with_valid_reason(): void
    {
        Notification::fake();

        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Spamming',
            'category' => 'Technology',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $reason = 'Deskripsi melanggar pedoman komunitas dan logo tidak pantas.';

        $response = $this->actingAs($this->superAdmin)->post(route('admin.community-approvals.reject', $community), [
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $community->refresh();
        $this->assertEquals('rejected', $community->approval_status);
        $this->assertEquals('rejected', $community->status);
        $this->assertEquals($this->superAdmin->id, $community->rejected_by);
        $this->assertEquals($reason, $community->rejection_reason);
        $this->assertNotNull($community->rejected_at);
        $this->assertNull($community->approved_by);

        // Notification sent
        Notification::assertSentTo($this->regularUser, CommunityRejectedNotification::class);

        // Public index does NOT show rejected community (flush previous flash message first)
        $this->flushSession();
        $this->get(route('community.index'))->assertDontSee('Komunitas Spamming');

        // Cannot be accessed by other users
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser)->get(route('community.show', $community))->assertNotFound();
    }

    public function test_creator_sees_rejection_reason_on_my_communities_page_and_can_resubmit(): void
    {
        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Komunitas Ditolak',
            'category' => 'Technology',
            'status' => 'rejected',
            'approval_status' => 'rejected',
            'rejection_reason' => 'Perbaiki deskripsi kegiatan dan perjelas tujuan komunitas.',
            'rejected_at' => now(),
            'rejected_by' => $this->superAdmin->id,
        ]);

        // Attach creator
        $community->members()->attach($this->regularUser->id, ['role' => 'admin']);

        // Check My Communities page
        $response = $this->actingAs($this->regularUser)->get(route('community.my'));
        $response->assertOk();
        $response->assertSee('Ditolak');
        $response->assertSee('Perbaiki deskripsi kegiatan dan perjelas tujuan komunitas.');
        $response->assertSee('Ajukan Kembali');

        // Resubmit via endpoint with revision notes
        $resubmitResponse = $this->actingAs($this->regularUser)->post(route('community.resubmit', $community), [
            'resubmit_notes' => 'Sudah kami perbaiki deskripsi dan aturan komunitas.',
        ]);
        $resubmitResponse->assertRedirect(route('community.my'));
        $resubmitResponse->assertSessionHas('success');

        $community->refresh();
        $this->assertEquals('pending', $community->approval_status);
        $this->assertEquals('pending', $community->status);
        $this->assertEquals('Sudah kami perbaiki deskripsi dan aturan komunitas.', $community->resubmit_notes);
        $this->assertNull($community->rejected_by);
        $this->assertNull($community->rejected_at);
    }

    public function test_non_super_admin_cannot_access_approval_routes_or_actions(): void
    {
        $community = Community::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Secure Community Test',
            'category' => 'Technology',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Regular user attempts
        $this->actingAs($this->regularUser)->get(route('admin.community-approvals.index'))->assertForbidden();
        $this->actingAs($this->regularUser)->get(route('admin.community-approvals.show', $community))->assertForbidden();
        $this->actingAs($this->regularUser)->post(route('admin.community-approvals.approve', $community))->assertForbidden();
        $this->actingAs($this->regularUser)->post(route('admin.community-approvals.reject', $community), [
            'rejection_reason' => 'Hacking attempt',
        ])->assertForbidden();

        // Regular admin (not super_admin) attempts - MUST ALSO BE FORBIDDEN as per instructions
        $this->actingAs($this->admin)->get(route('admin.community-approvals.index'))->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.community-approvals.show', $community))->assertForbidden();
        $this->actingAs($this->admin)->post(route('admin.community-approvals.approve', $community))->assertForbidden();
        $this->actingAs($this->admin)->post(route('admin.community-approvals.reject', $community), [
            'rejection_reason' => 'Admin attempt',
        ])->assertForbidden();
    }
}
