<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\OrganizerBankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventPaymentAndConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_event_registration_flow_remains_direct(): void
    {
        $organizer = User::factory()->create([
            'role' => 'university',
            'status' => 'active',
        ]);

        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Webinar Gratis Laravel',
            'organizer' => $organizer->name,
            'organizer_type' => 'University',
            'category' => 'Webinar',
            'mode' => 'Online',
            'price' => 'Free',
            'status' => 'Approved',
            'is_verified' => true,
            'total_quota' => 50,
        ]);

        $participant = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($participant)->post(route('event.register', $event->id), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $participant->id,
            'name' => 'John Doe',
            'amount' => 0,
            'payment_status' => 'free',
        ]);

        $registration = EventRegistration::where('event_id', $event->id)->first();
        $this->assertNotNull($registration->ticket_code);
    }

    public function test_paid_event_requires_payment_proof_and_creates_pending_status(): void
    {
        Storage::fake('public');

        $organizer = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        OrganizerBankAccount::create([
            'user_id' => $organizer->id,
            'bank_name' => 'BCA',
            'account_number' => '7720188921',
            'account_holder' => 'Bali IT Academy',
            'is_active' => true,
        ]);

        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Bootcamp Berbayar Golang',
            'organizer' => $organizer->name,
            'organizer_type' => 'School',
            'category' => 'Bootcamp',
            'mode' => 'In-Person',
            'price' => 'Rp 50.000',
            'status' => 'Approved',
            'is_verified' => true,
            'total_quota' => 20,
        ]);

        $participant = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Detail page should display the bank account info
        $pageResponse = $this->actingAs($participant)->get(route('event.show', $event->id));
        $pageResponse->assertOk();
        $pageResponse->assertSee('7720188921');
        $pageResponse->assertSee('Bali IT Academy');
        $pageResponse->assertSee('Rp 50.000');

        // Submitting registration without payment proof should fail validation
        $failResponse = $this->actingAs($participant)->post(route('event.register', $event->id), [
            'name' => 'Peserta Satu',
            'email' => 'peserta@example.com',
            'phone' => '08123456789',
        ]);
        $failResponse->assertSessionHasErrors(['payment_proof']);

        // Submitting with valid payment proof
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $successResponse = $this->actingAs($participant)->post(route('event.register', $event->id), [
            'name' => 'Peserta Satu',
            'email' => 'peserta@example.com',
            'phone' => '08123456789',
            'sender_bank' => 'Mandiri',
            'sender_name' => 'Peserta Satu',
            'payment_proof' => $file,
        ]);

        $successResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $participant->id,
            'amount' => 50000,
            'payment_status' => 'pending',
            'sender_bank' => 'Mandiri',
            'sender_name' => 'Peserta Satu',
        ]);
    }

    public function test_organizer_can_confirm_and_reject_payment_in_dashboard(): void
    {
        $organizer = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
        ]);

        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Workshop UI/UX Premium',
            'organizer' => $organizer->name,
            'organizer_type' => 'School',
            'category' => 'Workshop',
            'mode' => 'Online',
            'price' => 'Rp 100.000',
            'status' => 'Approved',
            'is_verified' => true,
        ]);

        $participant = User::factory()->create(['role' => 'user']);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $participant->id,
            'name' => 'Peserta Desain',
            'email' => 'desain@example.com',
            'phone' => '0812334455',
            'amount' => 100000,
            'payment_status' => 'pending',
            'payment_proof' => '/storage/events/payments/sample.jpg',
        ]);

        // Organizer sees confirmation page
        $dashboardResponse = $this->actingAs($organizer)->get(route('admin.events.confirmations'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Peserta Desain');

        // Confirm payment
        $confirmResponse = $this->actingAs($organizer)->post(route('admin.events.registrations.confirm', $registration->id));
        $confirmResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'id' => $registration->id,
            'payment_status' => 'confirmed',
            'confirmed_by' => $organizer->id,
        ]);

        // Reject payment test
        $rejectResponse = $this->actingAs($organizer)->post(route('admin.events.registrations.reject', $registration->id), [
            'rejection_reason' => 'Nominal transfer tidak sesuai biaya tiket.',
        ]);
        $rejectResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'id' => $registration->id,
            'payment_status' => 'rejected',
            'rejection_reason' => 'Nominal transfer tidak sesuai biaya tiket.',
        ]);
    }

    public function test_organizer_can_manage_bank_account_and_view_finance(): void
    {
        $organizer = User::factory()->create([
            'role' => 'company',
            'status' => 'active',
        ]);

        // View bank account page
        $bankPage = $this->actingAs($organizer)->get(route('admin.events.bank-account'));
        $bankPage->assertOk();

        // Save bank account
        $updateResponse = $this->actingAs($organizer)->post(route('admin.events.bank-account.update'), [
            'bank_name' => 'Bank Mandiri',
            'account_number' => '142001928374',
            'account_holder' => 'PT Indo Mitra Solusi',
            'instructions' => 'Transfer tepat waktu dan simpan bukti bayar.',
        ]);
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('organizer_bank_accounts', [
            'user_id' => $organizer->id,
            'bank_name' => 'Bank Mandiri',
            'account_number' => '142001928374',
            'account_holder' => 'PT Indo Mitra Solusi',
        ]);

        // View finance page
        $financePage = $this->actingAs($organizer)->get(route('admin.events.finance'));
        $financePage->assertOk();
        $financePage->assertSee('PT Indo Mitra Solusi');
    }
}
