<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityChatNavbarTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_does_not_show_chat_dropdown_for_guests(): void
    {
        $response = $this->get(route('community.index'));

        $response->assertOk();
        $response->assertDontSee('id="it-chat-btn"', false);
        $response->assertDontSee('id="it-chat-dropdown"', false);
    }

    public function test_navbar_shows_chat_icon_and_dropdown_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('community.index'));

        $response->assertOk();
        $response->assertSee('id="it-chat-btn"', false);
        $response->assertSee('id="it-chat-dropdown"', false);
        $response->assertSee('Chat Komunitas');
        $response->assertSee('Lihat Semua');
        $response->assertSee('Buka Semua Chat');
    }

    public function test_navbar_shows_empty_state_when_user_has_no_joined_communities(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('community.index'));

        $response->assertOk();
        $response->assertSee('Belum ada obrolan komunitas');
        $response->assertSee('Jelajahi Komunitas');
    }

    public function test_navbar_shows_user_communities_with_latest_message_and_sender(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        $sender = User::factory()->create(['name' => 'Siti Rahma']);

        $community = Community::create([
            'name' => 'Frontend Developers ID',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);
        $community->members()->attach($user->id, ['role' => 'member', 'is_banned' => false]);
        $community->members()->attach($sender->id, ['role' => 'member', 'is_banned' => false]);

        CommunityMessage::create([
            'community_id' => $community->id,
            'user_id' => $sender->id,
            'message' => 'Halo kawan-kawan, ada yang pakai Tailwind v4?',
            'type' => 'text',
        ]);

        $response = $this->actingAs($user)->get(route('community.index'));

        $response->assertOk();
        $response->assertSee('Frontend Developers ID');
        $response->assertSee('Siti Rahma:');
        $response->assertSee('Halo kawan-kawan, ada yang pakai Tailwind v4?');
    }

    public function test_unread_count_calculated_accurately_from_database(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create(['name' => 'Ahmad Fauzi']);

        $communityA = Community::create([
            'name' => 'DevOps Indonesia',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);
        $communityA->members()->attach($user->id, ['role' => 'member', 'is_banned' => false]);
        $communityA->members()->attach($sender->id, ['role' => 'member', 'is_banned' => false]);

        // Sender sends 2 messages
        CommunityMessage::create([
            'community_id' => $communityA->id,
            'user_id' => $sender->id,
            'message' => 'Pesan 1',
            'type' => 'text',
        ]);
        CommunityMessage::create([
            'community_id' => $communityA->id,
            'user_id' => $sender->id,
            'message' => 'Pesan 2',
            'type' => 'text',
        ]);

        // User's own message (should NOT count as unread for the user)
        CommunityMessage::create([
            'community_id' => $communityA->id,
            'user_id' => $user->id,
            'message' => 'Pesan dari saya sendiri',
            'type' => 'text',
        ]);

        $response = $this->actingAs($user)->get(route('community.index'));

        $response->assertOk();
        // Unread count should be exactly 2
        $response->assertSee('it-chat-badge', false);
        $response->assertSee('2');
    }

    public function test_visiting_chat_room_marks_community_as_read(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $community = Community::create([
            'name' => 'Cyber Security ID',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);
        $community->members()->attach($user->id, ['role' => 'member', 'is_banned' => false]);
        $community->members()->attach($sender->id, ['role' => 'member', 'is_banned' => false]);

        $msg1 = CommunityMessage::create([
            'community_id' => $community->id,
            'user_id' => $sender->id,
            'message' => 'Waspada phishing!',
            'type' => 'text',
        ]);

        // Before visiting chat: unread is 1
        $resBefore = $this->actingAs($user)->getJson(route('community.chat.navbar_summary'));
        $resBefore->assertOk();
        $resBefore->assertJsonPath('data.total_unread_count', 1);

        // User visits the chat room
        $this->actingAs($user)->get(route('community.chat', $community))->assertOk();

        // Database should record the read message id
        $this->assertDatabaseHas('community_message_reads', [
            'user_id' => $user->id,
            'community_id' => $community->id,
            'last_read_message_id' => $msg1->id,
        ]);

        // After visiting chat: unread becomes 0
        $resAfter = $this->actingAs($user)->getJson(route('community.chat.navbar_summary'));
        $resAfter->assertOk();
        $resAfter->assertJsonPath('data.total_unread_count', 0);
    }

    public function test_navbar_summary_json_endpoint_returns_real_data(): void
    {
        $user = User::factory()->create();
        $comm = Community::create([
            'user_id' => $user->id,
            'name' => 'Mobile Developer Club',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);

        $response = $this->actingAs($user)->getJson(route('community.chat.navbar_summary'));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.total_unread_count', 0);
        $response->assertJsonCount(1, 'data.communities');
        $response->assertJsonPath('data.communities.0.name', 'Mobile Developer Club');
    }

    public function test_mark_read_json_endpoint_updates_database_and_returns_count(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $comm = Community::create([
            'name' => 'AI Engineers',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);
        $comm->members()->attach($user->id, ['role' => 'member', 'is_banned' => false]);
        $comm->members()->attach($sender->id, ['role' => 'member', 'is_banned' => false]);

        $msg = CommunityMessage::create([
            'community_id' => $comm->id,
            'user_id' => $sender->id,
            'message' => 'Gemini 2.5 Flash is great!',
            'type' => 'text',
        ]);

        $response = $this->actingAs($user)->postJson(route('community.chat.mark_read', $comm));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('total_unread_count', 0);

        $this->assertDatabaseHas('community_message_reads', [
            'user_id' => $user->id,
            'community_id' => $comm->id,
            'last_read_message_id' => $msg->id,
        ]);
    }

    public function test_chat_all_route_redirects_to_first_active_community_chat(): void
    {
        $user = User::factory()->create();
        $comm = Community::create([
            'user_id' => $user->id,
            'name' => 'Active Comm',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'public',
        ]);

        $response = $this->actingAs($user)->get(route('chat.index'));

        $response->assertRedirect(route('community.chat', $comm));
    }

    public function test_chat_all_redirects_to_community_index_when_no_communities(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('chat.index'));

        $response->assertRedirect(route('community.index'));
    }

    public function test_user_cannot_see_private_community_chat_in_navbar_if_not_a_member(): void
    {
        $user = User::factory()->create();
        $privateComm = Community::create([
            'name' => 'Top Secret Club',
            'category' => 'Technology',
            'status' => 'active',
            'type' => 'private',
        ]);

        $response = $this->actingAs($user)->getJson(route('community.chat.navbar_summary'));

        $response->assertOk();
        $response->assertJsonCount(0, 'data.communities');
    }
}
