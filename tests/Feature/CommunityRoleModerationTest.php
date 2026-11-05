<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityRoleModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_community_owner_can_assign_moderator_role_to_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);
        $community->members()->attach($member->id, ['role' => 'member', 'is_banned' => false]);

        $response = $this->actingAs($owner)->patch(
            route('community.manage.member.role', [$community, $member]),
            ['role' => 'moderator']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $member->id,
            'role' => 'moderator',
        ]);
    }

    public function test_non_owner_cannot_assign_roles(): void
    {
        $owner = User::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($member1->id, ['role' => 'moderator', 'is_banned' => false]);
        $community->members()->attach($member2->id, ['role' => 'member', 'is_banned' => false]);

        // Moderator attempts to assign role to member2
        $response = $this->actingAs($member1)->patch(
            route('community.manage.member.role', [$community, $member2]),
            ['role' => 'admin']
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $member2->id,
            'role' => 'member',
        ]);
    }

    public function test_owner_role_cannot_be_changed(): void
    {
        $owner = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $response = $this->actingAs($owner)->patch(
            route('community.manage.member.role', [$community, $owner]),
            ['role' => 'moderator']
        );

        $response->assertSessionHas('error');
    }

    public function test_moderator_can_access_manage_page(): void
    {
        $owner = User::factory()->create();
        $moderator = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($moderator->id, ['role' => 'moderator', 'is_banned' => false]);

        $response = $this->actingAs($moderator)->get(route('community.manage', $community));

        $response->assertOk();
        $response->assertSee('Kelola Anggota');
        $response->assertSee('Moderasi Postingan');
        $response->assertDontSee('Pengaturan Komunitas'); // Owner only
    }

    public function test_regular_member_cannot_access_manage_page(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($member->id, ['role' => 'member', 'is_banned' => false]);

        $response = $this->actingAs($member)->get(route('community.manage', $community));

        $response->assertForbidden();
    }

    public function test_moderator_can_kick_impolite_regular_member(): void
    {
        $owner = User::factory()->create();
        $moderator = User::factory()->create();
        $badMember = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($moderator->id, ['role' => 'moderator', 'is_banned' => false]);
        $community->members()->attach($badMember->id, ['role' => 'member', 'is_banned' => false]);
        $community->update(['members' => 3]);

        $response = $this->actingAs($moderator)->delete(
            route('community.manage.kick', [$community, $badMember])
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('community_user', [
            'community_id' => $community->id,
            'user_id' => $badMember->id,
        ]);
        $this->assertSame(2, $community->refresh()->members);
    }

    public function test_moderator_cannot_kick_community_owner_or_another_moderator(): void
    {
        $owner = User::factory()->create();
        $moderator1 = User::factory()->create();
        $moderator2 = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($moderator1->id, ['role' => 'moderator', 'is_banned' => false]);
        $community->members()->attach($moderator2->id, ['role' => 'moderator', 'is_banned' => false]);

        // Attempt to kick owner
        $response = $this->actingAs($moderator1)->delete(
            route('community.manage.kick', [$community, $owner])
        );
        $response->assertForbidden();

        // Attempt to kick fellow moderator
        $response = $this->actingAs($moderator1)->delete(
            route('community.manage.kick', [$community, $moderator2])
        );
        $response->assertForbidden();
    }

    public function test_moderator_can_ban_and_unban_regular_member(): void
    {
        $owner = User::factory()->create();
        $moderator = User::factory()->create();
        $badMember = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($moderator->id, ['role' => 'moderator', 'is_banned' => false]);
        $community->members()->attach($badMember->id, ['role' => 'member', 'is_banned' => false]);

        // Ban
        $response = $this->actingAs($moderator)->post(
            route('community.manage.ban', [$community, $badMember])
        );
        $response->assertRedirect();

        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $badMember->id,
            'is_banned' => true,
        ]);

        // Unban
        $response = $this->actingAs($moderator)->post(
            route('community.manage.ban', [$community, $badMember])
        );
        $response->assertRedirect();

        $this->assertDatabaseHas('community_user', [
            'community_id' => $community->id,
            'user_id' => $badMember->id,
            'is_banned' => false,
        ]);
    }

    public function test_moderator_can_moderate_posts(): void
    {
        $owner = User::factory()->create();
        $moderator = User::factory()->create();
        $author = User::factory()->create();
        $community = $this->createApprovedCommunity($owner);

        $community->members()->attach($moderator->id, ['role' => 'moderator', 'is_banned' => false]);

        $post = CommunityPost::create([
            'community_id' => $community->id,
            'user_id' => $author->id,
            'title' => 'Post Tidak Sopan',
            'content' => 'Kata-kata kasar atau spam',
            'status' => 'published',
        ]);

        // Hide post
        $response = $this->actingAs($moderator)->patch(
            route('community.manage.post.moderate', [$community, $post]),
            ['action' => 'hide']
        );
        $response->assertRedirect();
        $this->assertSame('hidden', $post->refresh()->status);

        // Delete post
        $response = $this->actingAs($moderator)->patch(
            route('community.manage.post.moderate', [$community, $post]),
            ['action' => 'delete']
        );
        $response->assertRedirect();
        $this->assertDatabaseMissing('community_posts', [
            'id' => $post->id,
        ]);
    }

    private function createApprovedCommunity(User $owner): Community
    {
        $community = Community::create([
            'user_id' => $owner->id,
            'name' => 'Komunitas Moderasi',
            'category' => 'Technology',
            'members' => 1,
            'status' => 'active',
            'approval_status' => 'approved',
            'type' => 'public',
            'mode' => 'online',
            'language' => 'Indonesia',
            'description' => 'Komunitas untuk pengujian role & moderasi.',
        ]);

        $community->members()->attach($owner->id, [
            'role' => 'admin',
            'is_banned' => false,
        ]);

        return $community;
    }
}
