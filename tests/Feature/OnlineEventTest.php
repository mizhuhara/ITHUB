<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnlineEventTest extends TestCase
{
    use RefreshDatabase;

    private string $meetingUrl = 'https://meet.google.com/abc-defg-hij';

    public function test_registration_receives_unique_meeting_token(): void
    {
        $first = $this->registration(User::factory()->create());
        $second = $this->registration(User::factory()->create());

        $this->assertStringStartsWith('MTG-', $first->meeting_token);
        $this->assertNotSame($first->meeting_token, $second->meeting_token);
    }

    public function test_participant_can_join_with_own_link_and_attendance_is_recorded(): void
    {
        $user = User::factory()->create();
        $registration = $this->registration($user);

        $this->actingAs($user)
            ->get(route('event.join', $registration->meeting_token))
            ->assertRedirect($this->meetingUrl);

        $this->assertNotNull($registration->refresh()->joined_at);
    }

    public function test_participant_cannot_use_another_participants_link(): void
    {
        $registration = $this->registration(User::factory()->create());
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('event.join', $registration->meeting_token))
            ->assertForbidden();

        $this->assertNull($registration->refresh()->joined_at);
    }

    public function test_guest_is_redirected_to_login_before_joining(): void
    {
        $registration = $this->registration(User::factory()->create());

        $this->get(route('event.join', $registration->meeting_token))
            ->assertRedirect(route('login'));

        $this->assertNull($registration->refresh()->joined_at);
    }

    public function test_pending_participant_cannot_join(): void
    {
        $user = User::factory()->create();
        $registration = $this->registration($user, [], ['payment_status' => 'pending']);

        $this->actingAs($user)
            ->get(route('event.join', $registration->meeting_token))
            ->assertRedirect(route('event.show', $registration->event_id))
            ->assertSessionHas('error');

        $this->assertNull($registration->refresh()->joined_at);
    }

    public function test_online_certificate_requires_joining_through_personal_link(): void
    {
        $user = User::factory()->create();
        $registration = $this->registration($user, $this->certificateAttributes());

        $this->get(route('ticket.certificate', $registration->ticket_code))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('event.join', $registration->meeting_token))
            ->assertRedirect($this->meetingUrl);

        $this->get(route('ticket.certificate', $registration->ticket_code))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_event_and_ticket_pages_show_personal_meeting_link(): void
    {
        $user = User::factory()->create();
        $registration = $this->registration($user);
        $joinUrl = route('event.join', $registration->meeting_token);

        $this->actingAs($user)
            ->get(route('event.show', $registration->event_id))
            ->assertOk()
            ->assertSee('Masuk ke Meeting')
            ->assertSee($joinUrl, false)
            ->assertSee('Jangan dibagikan');

        $this->get(route('ticket.show', $registration->ticket_code))
            ->assertOk()
            ->assertSee('Link Meeting')
            ->assertSee($joinUrl, false)
            ->assertSee('Jangan dibagikan');
    }

    public function test_admin_can_create_online_event_with_meeting_link(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->post(route('admin.events.store'), [
                'title' => 'Online Webinar',
                'category' => 'Webinar',
                'mode' => 'Online',
                'date' => '2026-12-01',
                'meeting_url' => $this->meetingUrl,
            ])
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', [
            'title' => 'Online Webinar',
            'mode' => 'Online',
            'meeting_url' => $this->meetingUrl,
        ]);
    }

    public function test_admin_can_mark_online_attendance(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $registration = $this->registration(User::factory()->create());

        $this->actingAs($admin)
            ->post(route('admin.events.registrations.check-in', [$registration->event_id, $registration]))
            ->assertSessionHas('success');

        $this->assertNotNull($registration->refresh()->joined_at);
    }

    /**
     * @param  array<string, mixed>  $eventAttributes
     * @param  array<string, mixed>  $registrationAttributes
     */
    private function registration(User $user, array $eventAttributes = [], array $registrationAttributes = []): EventRegistration
    {
        return $this->event($eventAttributes)->registrations()->create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'payment_status' => 'confirmed',
        ], $registrationAttributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function event(array $attributes = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Online Event',
            'organizer' => 'IndoTech',
            'category' => 'Webinar',
            'mode' => 'Online',
            'price' => 'Free',
            'date' => '2026-12-01',
            'location' => 'Online',
            'meeting_url' => $this->meetingUrl,
            'total_quota' => 10,
            'status' => 'Approved',
            'is_verified' => true,
        ], $attributes));
    }

    /**
     * @return array<string, mixed>
     */
    private function certificateAttributes(): array
    {
        Storage::fake('public');

        $templatePath = UploadedFile::fake()
            ->image('certificate-template.png', 800, 600)
            ->store('events/certificates', 'public');

        return [
            'certificate_enabled' => true,
            'certificate_template' => $templatePath,
            'completed_at' => now(),
        ];
    }
}
