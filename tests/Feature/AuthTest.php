<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $loginUrl;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUrl = "{$this->baseUrl}auth/login";
    }

    /**
     * Test if a user can login with valid credentials.
     */
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

        $response = $this->postJson($this->loginUrl, $payload);
        $response->assertStatus(200);
    }

    /**
     * Test that login fails if the phone number does not exist.
     */
    public function test_should_fail_login_if_phone_number_does_not_exists(): void
    {
        $firstPhone = '0556203925';
        $secondPhone = '0103284835';

        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $firstPhone,
        ]);

        $payload = [
            'phone_number' => $secondPhone,
            'password' => 'password',
        ];

        $response = $this->postJson($this->loginUrl, $payload);
        $response->assertStatus(422)->assertJsonValidationErrorFor('phone_number');
    }

    /**
     * Test that login fails with an incorrect password.
     */
    public function test_should_reject_user_if_wrong_password(): void
    {
        $phone = '0556203925';

        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $phone,
        ]);

        $payload = [
            'phone_number' => $phone,
            'password' => 'anotherpassword',
        ];

        $response = $this->postJson($this->loginUrl, $payload);

        $response->assertStatus(401);
    }

    /**
     * Test that login fails if the user account is blocked.
     */
    public function test_should_fail_login_when_user_is_blocked(): void
    {
        $phone = '0556203925';

        User::factory()->create([
            'role' => UserRole::OWNER,
            'phone_number' => $phone,
            'is_active' => false,
            'blocked_reason' => 'too much attempt to log in',
        ]);

        $payload = [
            'phone_number' => $phone,
            'password' => 'anotherpassword',
        ];

        $response = $this->postJson($this->loginUrl, $payload);

        $response->assertStatus(403);
    }
}

