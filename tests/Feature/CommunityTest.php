<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_only_lists_active_communities(): void
    {
        Community::create([
            'name' => 'Active Community',
            'category' => 'Technology',
            'members' => 10,
            'status' => 'active',
        ]);

        Community::create([
            'name' => 'Inactive Community',
            'category' => 'Technology',
            'members' => 5,
            'status' => 'inactive',
        ]);

        $response = $this->get(route('community.index'));

        $response->assertOk();
        $response->assertSee('Active Community');
        $response->assertDontSee('Inactive Community');
    }

    public function test_admin_can_create_community(): void
    {
        $admin = User::factory()->create([
            'email' => User::ADMIN_EMAIL,
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.community.store'), [
            'name' => 'New Community',
            'category' => 'Technology',
            'description' => 'A new community.',
            'website' => 'https://example.com',
            'email' => 'hello@example.com',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.community.index'));
        $this->assertDatabaseHas('communities', [
            'name' => 'New Community',
            'members' => 0,
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_community_management(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('admin.community.index'))->assertForbidden();
    }

    public function test_community_index_has_add_community_button_linking_to_create_page(): void
    {
        $response = $this->get(route('community.index'));

        $response->assertOk();
        $response->assertSee(route('community.create'));
        $response->assertSee('Tambah Komunitas');
    }

    public function test_user_can_view_create_community_page(): void
    {
        $response = $this->get(route('community.create'));

        $response->assertOk();
        $response->assertSee('Buat Komunitas Baru');
        $response->assertSee('Nama Komunitas');
        $response->assertSee('Kategori');
        $response->assertSee('Deskripsi');
        $response->assertSee('Logo Komunitas');
        $response->assertSee('Banner Komunitas');
        $response->assertSee('Jenis Komunitas');
        $response->assertSee('Bahasa Utama');
        $response->assertSee('Aturan Komunitas');
        $response->assertSee('Buat Komunitas');
        $response->assertSee('Batal');
    }

    public function test_user_can_create_community_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.store'), [
            'name' => 'Komunitas Flutter Indonesia',
            'category' => 'Technology',
            'description' => 'Komunitas pengembang aplikasi Flutter dan Dart se-Indonesia.',
            'logo' => UploadedFile::fake()->image('flutter_logo.png', 200, 200),
            'banner' => UploadedFile::fake()->image('flutter_banner.jpg', 1200, 400),
            'type' => 'public',
            'location' => 'Jakarta / Online',
            'language' => 'Bahasa Indonesia',
            'rules' => "1. Saling menghargai sesama anggota.\n2. Dilarang spam.",
        ]);

        $community = Community::where('name', 'Komunitas Flutter Indonesia')->first();
        $this->assertNotNull($community);

        $response->assertRedirect(route('community.my'));
        $this->assertEquals($user->id, $community->user_id);
        $this->assertEquals('pending', $community->status);
        $this->assertEquals('pending', $community->approval_status);
        $this->assertEquals('Technology', $community->category);
        $this->assertEquals('public', $community->type);
        $this->assertEquals('Jakarta / Online', $community->location);
        $this->assertEquals('Bahasa Indonesia', $community->language);
        $this->assertStringContainsString('Saling menghargai', $community->rules);
        $this->assertNotNull($community->logo_url);
        $this->assertNotNull($community->cover_url);
        $this->assertEquals(1, $community->members);
        $this->assertTrue($community->members()->whereKey($user->id)->exists());
    }

    public function test_community_creation_validation_errors(): void
    {
        $response = $this->post(route('community.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'category',
            'description',
            'logo',
            'type',
            'language',
        ]);
    }

    public function test_guest_cannot_access_my_communities_page(): void
    {
        $response = $this->get(route('community.my'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_sees_empty_state_when_no_communities_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('community.my'));
        $response->assertOk();
        $response->assertSee('Kamu belum membuat komunitas');
        $response->assertSee('Buat Komunitas');
        $response->assertSee(route('community.create'));
    }

    public function test_user_sees_only_own_communities_in_my_communities(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $commA = Community::create([
            'user_id' => $userA->id,
            'name' => 'Komunitas User A',
            'category' => 'Technology',
            'status' => 'active',
            'members' => 1,
            'description' => 'Deskripsi komunitas user A',
        ]);

        $commB = Community::create([
            'user_id' => $userB->id,
            'name' => 'Komunitas User B',
            'category' => 'Design',
            'status' => 'active',
            'members' => 1,
            'description' => 'Deskripsi komunitas user B',
        ]);

        $response = $this->actingAs($userA)->get(route('community.my'));
        $response->assertOk();
        $response->assertSee('Komunitas User A');
        $response->assertDontSee('Komunitas User B');
        $response->assertSee('Lihat Komunitas');
        $response->assertSee('Kelola Komunitas');
        $response->assertSee(route('community.show', $commA));
        $response->assertSee(route('community.manage', $commA));
    }

    public function test_creator_can_access_manage_community_page(): void
    {
        $user = User::factory()->create();
        $comm = Community::create([
            'user_id' => $user->id,
            'name' => 'Komunitas Laravel ID',
            'category' => 'Technology',
            'status' => 'active',
            'members' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('community.manage', $comm));
        $response->assertOk();
        $response->assertSee('Kelola Komunitas');
        $response->assertSee('Pengaturan Komunitas');
        $response->assertSee('Kelola Anggota');
        $response->assertSee('Moderasi Postingan');
    }

    public function test_non_creator_cannot_access_manage_community_page(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $comm = Community::create([
            'user_id' => $owner->id,
            'name' => 'Komunitas Rahasia',
            'category' => 'Technology',
            'status' => 'active',
        ]);

        $response = $this->actingAs($otherUser)->get(route('community.manage', $comm));
        $response->assertForbidden();
    }

    public function test_creator_can_update_community_settings(): void
    {
        $owner = User::factory()->create();
        $comm = Community::create([
            'user_id' => $owner->id,
            'name' => 'Old Name',
            'category' => 'Technology',
            'description' => 'Old description',
            'status' => 'active',
            'type' => 'public',
            'location' => 'Jakarta',
            'language' => 'Bahasa Indonesia',
        ]);

        $response = $this->actingAs($owner)->put(route('community.manage.update', $comm), [
            'name' => 'New Name',
            'category' => 'Design',
            'description' => 'Updated description',
            'status' => 'active',
            'type' => 'private',
            'location' => 'Bandung',
            'language' => 'English',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('communities', [
            'id' => $comm->id,
            'name' => 'New Name',
            'category' => 'Design',
            'type' => 'private',
        ]);
    }

    public function test_creator_can_kick_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $comm = Community::create([
            'user_id' => $owner->id,
            'name' => 'Komunitas Kick Test',
            'category' => 'Technology',
            'status' => 'active',
            'members' => 2,
        ]);
        $comm->members()->attach([$owner->id, $member->id]);

        $response = $this->actingAs($owner)->delete(route('community.manage.kick', [$comm, $member]));
        $response->assertSessionHas('success');
        $this->assertFalse($comm->members()->whereKey($member->id)->exists());
        $this->assertEquals(1, $comm->fresh()->members);
    }

    public function test_creator_can_ban_and_unban_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $comm = Community::create([
            'user_id' => $owner->id,
            'name' => 'Komunitas Ban Test',
            'category' => 'Technology',
            'status' => 'active',
            'members' => 2,
        ]);
        $comm->members()->attach([$owner->id, $member->id]);

        // Ban
        $response = $this->actingAs($owner)->post(route('community.manage.ban', [$comm, $member]));
        $response->assertSessionHas('success');
        $this->assertTrue((bool) $comm->members()->where('user_id', $member->id)->first()->pivot->is_banned);

        // Banned member cannot join
        $joinResponse = $this->actingAs($member)->post(route('community.join', $comm));
        $joinResponse->assertSessionHas('error');

        // Unban
        $response = $this->actingAs($owner)->post(route('community.manage.ban', [$comm, $member]));
        $response->assertSessionHas('success');
        $this->assertFalse((bool) $comm->members()->where('user_id', $member->id)->first()->pivot->is_banned);
    }

    public function test_creator_can_moderate_posts(): void
    {
        $owner = User::factory()->create();
        $comm = Community::create([
            'user_id' => $owner->id,
            'name' => 'Komunitas Post Test',
            'category' => 'Technology',
            'status' => 'active',
        ]);
        $post = $comm->posts()->create([
            'user_id' => $owner->id,
            'title' => 'Diskusi Pertama',
            'content' => 'Konten diskusi yang perlu dimoderasi',
            'status' => 'published',
        ]);

        // Hide post
        $this->actingAs($owner)->patch(route('community.manage.post.moderate', [$comm, $post]), [
            'action' => 'hide',
        ])->assertSessionHas('success');
        $this->assertEquals('hidden', $post->fresh()->status);

        // Delete post
        $this->actingAs($owner)->patch(route('community.manage.post.moderate', [$comm, $post]), [
            'action' => 'delete',
        ])->assertSessionHas('success');
        $this->assertDatabaseMissing('community_posts', ['id' => $post->id]);
    }

    public function test_navbar_contains_komunitas_saya_for_authenticated_users(): void
    {
        // Guest
        $response = $this->get('/');
        $response->assertDontSee(route('community.my'));

        // Authenticated user
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertSee(route('community.my'));
        $response->assertSee('Komunitas Saya');
    }

    public function test_community_index_is_paginated_and_renders_pagination_links(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            Community::create([
                'name' => "Komunitas Tech {$i}",
                'category' => 'Technology',
                'members' => $i * 10,
                'status' => 'active',
            ]);
        }

        $response = $this->get(route('community.index'));
        $response->assertOk();
        $response->assertSee('cmty-pagination');
        $response->assertSee('Komunitas Tech 8');

        $pageTwo = $this->get(route('community.index', ['page' => 2]));
        $pageTwo->assertOk();
        $pageTwo->assertSee('Komunitas Tech 2');
        $pageTwo->assertSee('Komunitas Tech 1');
    }

    public function test_user_can_create_community_with_tags(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.store'), [
            'name' => 'Figma Community Bali',
            'category' => 'Design',
            'description' => 'Komunitas belajar desain dan Figma di Bali.',
            'logo' => UploadedFile::fake()->image('logo.png', 100, 100),
            'type' => 'public',
            'mode' => 'online',
            'language' => 'Bahasa Indonesia',
            'tags' => 'UI/UX, #Figma, Design System, Prototyping',
        ]);

        $response->assertRedirect(route('community.my'));

        $community = Community::where('name', 'Figma Community Bali')->firstOrFail();
        $this->assertEquals(['UI/UX', 'Figma', 'Design System', 'Prototyping'], $community->tags);
    }

    public function test_owner_can_update_community_tags(): void
    {
        $owner = User::factory()->create();
        $community = Community::create([
            'user_id' => $owner->id,
            'name' => 'Web Dev Club',
            'category' => 'Technology',
            'description' => 'Komunitas web dev.',
            'status' => 'active',
            'type' => 'public',
            'mode' => 'online',
            'language' => 'Bahasa Indonesia',
        ]);

        $response = $this->actingAs($owner)->put(route('community.manage.update', $community), [
            'name' => 'Web Dev Club',
            'category' => 'Technology',
            'description' => 'Komunitas web dev.',
            'status' => 'active',
            'type' => 'public',
            'mode' => 'online',
            'language' => 'Bahasa Indonesia',
            'tags' => 'Laravel, React, Vue, TailwindCSS',
        ]);

        $response->assertSessionHas('success');

        $community->refresh();
        $this->assertEquals(['Laravel', 'React', 'Vue', 'TailwindCSS'], $community->tags);
    }

    public function test_search_finds_community_by_tag(): void
    {
        Community::create([
            'name' => 'Front-End Masters',
            'category' => 'Technology',
            'description' => 'Belajar coding antarmuka.',
            'status' => 'active',
            'approval_status' => 'approved',
            'tags' => ['ReactJS', 'NextJS', 'TypeScript'],
        ]);

        $response = $this->get(route('community.index', ['q' => 'TypeScript']));
        $response->assertOk();
        $response->assertSee('Front-End Masters');
        $response->assertSee('#TypeScript');
    }
}
