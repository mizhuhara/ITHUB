<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_event_with_certificate_returns_pdf(): void
    {
        $registration = $this->registration();

        $response = $this->get(route('ticket.certificate', $registration->ticket_code));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_certificate_is_unavailable_before_event_is_completed(): void
    {
        $registration = $this->registration(['completed_at' => null]);

        $this->get(route('ticket.certificate', $registration->ticket_code))
            ->assertNotFound();
    }

    public function test_certificate_is_unavailable_for_pending_registration(): void
    {
        $registration = $this->registration(['payment_status' => 'pending']);

        $this->get(route('ticket.certificate', $registration->ticket_code))
            ->assertNotFound();
    }

    public function test_certificate_is_unavailable_when_event_disables_it(): void
    {
        $registration = $this->registration(['certificate_enabled' => false]);

        $this->get(route('ticket.certificate', $registration->ticket_code))
            ->assertNotFound();
    }

    public function test_admin_can_view_certificate_settings_on_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $event = $this->event();

        $this->actingAs($admin)
            ->get(route('admin.events.create'))
            ->assertOk()
            ->assertSee('Sertifikat Peserta');

        $this->actingAs($admin)
            ->get(route('admin.events.edit', $event->id))
            ->assertOk()
            ->assertSee('Sertifikat Peserta');
    }

    public function test_event_owner_can_toggle_completed_status(): void
    {
        $owner = User::factory()->create(['role' => 'company']);
        $event = $this->event(['user_id' => $owner->id, 'completed_at' => null]);

        $this->actingAs($owner)
            ->post(route('admin.events.complete', $event->id))
            ->assertSessionHas('success');

        $this->assertNotNull($event->refresh()->completed_at);

        $this->actingAs($owner)
            ->post(route('admin.events.complete', $event->id))
            ->assertSessionHas('success');

        $this->assertNull($event->refresh()->completed_at);
    }

    public function test_non_owner_cannot_toggle_completed_status(): void
    {
        $otherUser = User::factory()->create(['role' => 'company']);
        $event = $this->event(['completed_at' => null]);

        $this->actingAs($otherUser)
            ->post(route('admin.events.complete', $event->id))
            ->assertNotFound();

        $this->assertNull($event->refresh()->completed_at);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function registration(array $attributes = []): EventRegistration
    {
        $event = $this->event($attributes);

        return $event->registrations()->create([
            'name' => 'Peserta Sertifikat',
            'email' => 'sertifikat@example.com',
            'payment_status' => $attributes['payment_status'] ?? 'confirmed',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function event(array $attributes = []): Event
    {
        Storage::fake('public');

        $template = UploadedFile::fake()->image('certificate-template.png', 800, 600);
        $templatePath = $template->store('events/certificates', 'public');

        return Event::create(array_merge([
            'title' => 'Event Bersertifikat',
            'organizer' => 'IndoTech',
            'category' => 'Seminar',
            'mode' => 'In-Person',
            'price' => 'Free',
            'date' => '2026-12-01',
            'location' => 'Jakarta',
            'total_quota' => 10,
            'status' => 'Approved',
            'is_verified' => true,
            'certificate_enabled' => true,
            'certificate_template' => $templatePath,
            'certificate_name_x' => 50,
            'certificate_name_y' => 50,
            'certificate_name_font_size' => 48,
            'certificate_name_color' => '#111827',
            'completed_at' => now(),
        ], $attributes));
    }
}
