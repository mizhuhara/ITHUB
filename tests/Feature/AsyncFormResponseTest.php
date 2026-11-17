<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AsyncFormResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_is_converted_to_json_with_flash_message(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.univ.store'), [
            'name' => 'Universitas Async',
            'npsn' => '77777777',
            'type' => 'Negeri',
            'location' => 'Bandung, Jawa Barat',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Universitas "Universitas Async" berhasil ditambahkan.');

        $this->assertDatabaseHas('universities', ['name' => 'Universitas Async']);
    }

    public function test_redirect_with_error_flash_is_converted_to_422_json(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.users.toggle-hide', $admin->id));

        $response->assertUnprocessable()
            ->assertJsonPath('message', 'Anda tidak dapat menyembunyikan akun Anda sendiri.');
    }

    public function test_login_redirect_returns_json_with_target_url(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'redirect'])
            ->assertJsonPath('redirect', route('welcome'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_status_flash_is_used_as_json_message(): void
    {
        Mail::fake();

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Jika email terdaftar, tautan reset password sudah dikirim.');
    }
}
