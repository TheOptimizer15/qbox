<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = '/api/v1/auth/login';

    protected $firstPhone = '0556203925';

    protected $secondPhone = '0103284835';

    public function test_user_can_login_with_valid_phone_number(): void
    {
        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => '0556203925',
        ]);

        $payload = [
            'phone_number' => '0556203925',
            'password' => 'password',
        ];

        $response = $this->postJson($this->endpoint, $payload);
        $response->assertStatus(200);
    }

    public function test_should_fail_login_if_phone_number_does_not_exists(): void
    {
        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $this->firstPhone,
        ]);

        $payload = [
            'phone_number' => $this->secondPhone,
            'password' => 'password',
        ];

        $response = $this->postJson($this->endpoint, $payload);
        $response->assertStatus(422)->assertJsonValidationErrorFor('phone_number');
    }

    public function test_should_reject_user_if_wrong_password(): void
    {
        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $this->firstPhone,
        ]);

        $payload = [
            'phone_number' => $this->firstPhone,
            'password' => 'anotherpassword',
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(401);
    }

    public function test_should_fail_login_when_user_is_blocked(): void
    {
        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $this->firstPhone,
            'is_active' => false,
            'blocked_reason' => 'too much attempt to log in',
        ]);

        $payload = [
            'phone_number' => $this->firstPhone,
            'password' => 'anotherpassword',
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(403);
    }
}
