<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_ticket_verification_page(): void
    {
        $registration = $this->registration();

        $this->get(route('ticket.show', $registration->ticket_code))
            ->assertOk()
            ->assertSee($registration->name)
            ->assertSee($registration->event->title)
            ->assertSee($registration->ticket_code)
            ->assertDontSee($registration->email);
    }

    public function test_unknown_ticket_code_returns_not_found(): void
    {
        $this->get(route('ticket.show', 'EVT-NOTEXIST'))
            ->assertNotFound();
    }

    public function test_event_owner_can_check_in_participant(): void
    {
        $owner = User::factory()->create(['role' => 'company']);
        $registration = $this->registration(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->post(route('ticket.check-in', $registration->ticket_code))
            ->assertSessionHas('success');

        $this->assertNotNull($registration->refresh()->checked_in_at);
    }

    public function test_non_owner_cannot_check_in_participant(): void
    {
        $otherUser = User::factory()->create(['role' => 'company']);
        $registration = $this->registration();

        $this->actingAs($otherUser)
            ->post(route('ticket.check-in', $registration->ticket_code))
            ->assertForbidden();

        $this->assertNull($registration->refresh()->checked_in_at);
    }

    public function test_guest_cannot_check_in_participant(): void
    {
        $registration = $this->registration();

        $this->post(route('ticket.check-in', $registration->ticket_code))
            ->assertRedirect(route('login'));

        $this->assertNull($registration->refresh()->checked_in_at);
    }

    public function test_online_event_cannot_be_checked_in(): void
    {
        $owner = User::factory()->create(['role' => 'company']);
        $registration = $this->registration(['user_id' => $owner->id, 'mode' => 'Online']);

        $this->actingAs($owner)
            ->post(route('ticket.check-in', $registration->ticket_code))
            ->assertNotFound();

        $this->assertNull($registration->refresh()->checked_in_at);
    }

    public function test_pending_payment_cannot_be_checked_in(): void
    {
        $owner = User::factory()->create(['role' => 'company']);
        $registration = $this->registration([
            'user_id' => $owner->id,
            'payment_status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->post(route('ticket.check-in', $registration->ticket_code))
            ->assertSessionHas('error');

        $this->assertNull($registration->refresh()->checked_in_at);
    }

    public function test_offline_registration_shows_qr_on_event_page(): void
    {
        $user = User::factory()->create();
        $registration = $this->registration(['user_id' => $user->id, 'mode' => 'In-Person']);

        $this->actingAs($user)
            ->get(route('event.show', $registration->event_id))
            ->assertOk()
            ->assertSee('Buka Halaman Tiket')
            ->assertSee('data:image/svg+xml', false);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function registration(array $attributes = []): EventRegistration
    {
        $mode = $attributes['mode'] ?? 'In-Person';
        unset($attributes['mode']);

        $event = Event::create([
            'title' => 'Offline Tech Meetup',
            'organizer' => 'IndoTech',
            'category' => 'Seminar',
            'mode' => $mode,
            'price' => 'Free',
            'date' => '2026-12-01',
            'location' => 'Jakarta',
            'total_quota' => 10,
            'status' => 'Approved',
            'is_verified' => true,
            'user_id' => $attributes['user_id'] ?? null,
        ]);

        return $event->registrations()->create(array_merge([
            'name' => 'Peserta Tiket',
            'email' => 'peserta@example.com',
            'phone' => '081234567890',
            'payment_status' => 'confirmed',
        ], $attributes));
    }
}
