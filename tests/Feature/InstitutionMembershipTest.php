<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Company;
use App\Models\MembershipPayment;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstitutionMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_institution_registration_receives_one_month_trial(): void
    {
        Storage::fake('local');

        $doc = UploadedFile::fake()->create('sk_pendirian.pdf', 500, 'application/pdf');

        $response = $this->post(route('register.submit'), [
            'name' => 'SMK Maju Bersama',
            'email' => 'smkmaju@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'school',
            'org_contact' => 'Drs. Budi Santoso',
            'org_phone' => '081234567890',
            'org_address' => 'Jl. Pendidikan No. 10',
            'org_doc' => $doc,
        ]);

        $response->assertRedirect();

        $user = User::where('email', 'smkmaju@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('trial', $user->membership_status);
        $this->assertSame('trial_1_month', $user->membership_plan);
        $this->assertTrue($user->is_premium_active);
        $this->assertNotNull($user->membership_expires_at);
        $this->assertTrue($user->membership_expires_at->isFuture());
        $this->assertTrue($user->hasActiveMembership());
        $this->assertGreaterThanOrEqual(28, $user->membership_days_remaining);
    }

    public function test_active_trial_institution_is_visible_publicly(): void
    {
        $user = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
            'is_premium_active' => true,
            'membership_status' => 'trial',
            'membership_expires_at' => now()->addDays(20),
        ]);

        School::create([
            'user_id' => $user->id,
            'npsn' => 'SMK-1111',
            'name' => 'SMK Teknologi Unggulan',
            'institution_type' => 'SMK IT',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'status' => 'Active',
        ]);

        $this->get(route('education.index'))
            ->assertOk()
            ->assertSee('SMK Teknologi Unggulan');
    }

    public function test_expired_institution_is_hidden_from_public_directory(): void
    {
        $user = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
            'is_premium_active' => true,
            'membership_status' => 'trial',
            'membership_expires_at' => now()->subDay(), // Expired!
        ]);

        School::create([
            'user_id' => $user->id,
            'npsn' => 'SMK-2222',
            'name' => 'SMK Kadaluarsa',
            'institution_type' => 'SMK IT',
            'city' => 'Semarang',
            'province' => 'Jawa Tengah',
            'status' => 'Active',
        ]);

        $this->assertFalse($user->hasActiveMembership());

        $this->get(route('education.index'))
            ->assertOk()
            ->assertDontSee('SMK Kadaluarsa');

        $this->get(route('education.show', 'SMK-2222'))
            ->assertNotFound();
    }

    public function test_disabled_individual_institution_is_hidden(): void
    {
        $user = User::factory()->create([
            'role' => 'company',
            'status' => 'active',
            'is_premium_active' => false, // Disabled by super admin!
            'membership_status' => 'active',
            'membership_expires_at' => now()->addMonth(),
        ]);

        Company::create([
            'user_id' => $user->id,
            'name' => 'PT Nonaktif Tech',
            'status' => 'Active',
            'industry' => 'Software',
        ]);

        $this->get(route('industry'))
            ->assertOk()
            ->assertDontSee('PT Nonaktif Tech');
    }

    public function test_global_disable_allows_all_institutions_to_be_visible(): void
    {
        // Fitur dimatikan sementara oleh Super Admin
        AppSetting::setPremiumFeatureEnabled(false);

        $user = User::factory()->create([
            'role' => 'university',
            'status' => 'active',
            'is_premium_active' => true,
            'membership_status' => 'expired',
            'membership_expires_at' => now()->subDays(10), // Expired
        ]);

        University::create([
            'user_id' => $user->id,
            'npsn' => 'UNIV-3333',
            'name' => 'Universitas Bebas Terbuka',
            'type' => 'Swasta',
            'status' => 'Active',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);

        // Karena global disable, universitas kadaluarsa tetap tayang
        $this->get(route('campus.index'))
            ->assertOk()
            ->assertSee('Universitas Bebas Terbuka');
    }

    public function test_super_admin_can_toggle_global_premium_feature(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        AppSetting::setPremiumFeatureEnabled(true);

        $response = $this->actingAs($admin)->post(route('admin.memberships.toggle_global'));
        $response->assertRedirect();
        $this->assertFalse(AppSetting::isPremiumFeatureEnabled());

        $response = $this->actingAs($admin)->post(route('admin.memberships.toggle_global'));
        $response->assertRedirect();
        $this->assertTrue(AppSetting::isPremiumFeatureEnabled());
    }

    public function test_super_admin_can_toggle_and_extend_individual_institution(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $institutionUser = User::factory()->create([
            'role' => 'company',
            'status' => 'active',
            'is_premium_active' => true,
            'membership_status' => 'trial',
            'membership_expires_at' => now()->subDay(),
        ]);

        // 1. Toggle off
        $this->actingAs($admin)
            ->post(route('admin.memberships.toggle', $institutionUser->id))
            ->assertRedirect();

        $institutionUser->refresh();
        $this->assertFalse($institutionUser->is_premium_active);

        // 2. Extend by 1 year
        $this->actingAs($admin)
            ->post(route('admin.memberships.extend', $institutionUser->id), [
                'extension_type' => 'add_year',
                'membership_status' => 'active',
            ])
            ->assertRedirect();

        $institutionUser->refresh();
        $this->assertTrue($institutionUser->is_premium_active);
        $this->assertSame('active', $institutionUser->membership_status);
        $this->assertTrue($institutionUser->membership_expires_at->isFuture());
    }

    public function test_institution_user_can_view_membership_page(): void
    {
        $user = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
            'membership_status' => 'trial',
            'membership_expires_at' => now()->addDays(25),
            'is_premium_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.membership'));

        $response->assertOk()
            ->assertSee('Status Keanggotaan')
            ->assertSee('Trial Gratis 1 Bulan')
            ->assertSee('Formulir Konfirmasi Pembayaran');
    }

    public function test_institution_can_submit_membership_renewal_with_payment_proof(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'company',
            'status' => 'active',
            'membership_status' => 'expired',
            'membership_expires_at' => now()->subDay(),
        ]);

        $proof = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->actingAs($user)->post(route('dashboard.membership.store'), [
            'plan' => '1_month',
            'sender_bank' => 'BCA',
            'sender_name' => 'PT Sukses Makmur',
            'sender_account_number' => '12345678',
            'payment_proof' => $proof,
            'notes' => 'Pembayaran perpanjangan bulan November',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('membership_payments', [
            'user_id' => $user->id,
            'plan' => '1_month',
            'sender_bank' => 'BCA',
            'sender_name' => 'PT Sukses Makmur',
            'status' => 'pending',
        ]);
    }

    public function test_super_admin_can_update_bank_and_price_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.memberships.settings'), [
            'membership_bank_name' => 'Bank Mandiri',
            'membership_bank_account_number' => '987-654-3210',
            'membership_bank_account_name' => 'PT IndoTech Official',
            'membership_price_monthly' => 200000,
            'membership_price_yearly' => 2000000,
            'membership_payment_instructions' => 'Transfer tepat waktu dan simpan bukti transaksi.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $settings = AppSetting::getMembershipBankSettings();
        $this->assertSame('Bank Mandiri', $settings['bank_name']);
        $this->assertSame('987-654-3210', $settings['account_number']);
        $this->assertSame('PT IndoTech Official', $settings['account_name']);
        $this->assertSame(200000, $settings['price_monthly']);
        $this->assertSame(2000000, $settings['price_yearly']);
    }

    public function test_super_admin_can_approve_payment_and_membership_is_extended(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'school',
            'status' => 'active',
            'membership_status' => 'expired',
            'membership_expires_at' => now()->subDay(),
        ]);

        $payment = MembershipPayment::create([
            'user_id' => $user->id,
            'institution_type' => 'school',
            'plan' => '1_year',
            'amount' => 1500000,
            'sender_bank' => 'BRI',
            'sender_name' => 'Bendahara SMK',
            'payment_proof' => 'payment-proofs/sample.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.memberships.payments.approve', $payment->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame('approved', $payment->status);
        $this->assertSame($admin->id, $payment->approved_by);

        $user->refresh();
        $this->assertSame('active', $user->membership_status);
        $this->assertSame('1_year', $user->membership_plan);
        $this->assertTrue($user->is_premium_active);
        $this->assertTrue($user->membership_expires_at->isFuture());
    }

    public function test_super_admin_can_reject_payment_with_reason(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'university',
            'status' => 'active',
        ]);

        $payment = MembershipPayment::create([
            'user_id' => $user->id,
            'institution_type' => 'university',
            'plan' => '1_month',
            'amount' => 150000,
            'sender_bank' => 'BCA',
            'sender_name' => 'Mahasiswa',
            'payment_proof' => 'payment-proofs/sample.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.memberships.payments.reject', $payment->id), [
                'admin_notes' => 'Nominal transfer tidak sesuai dengan tagihan.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame('rejected', $payment->status);
        $this->assertSame('Nominal transfer tidak sesuai dengan tagihan.', $payment->admin_notes);
    }
}
