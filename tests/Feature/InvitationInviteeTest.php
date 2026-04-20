<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationInviteeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $invitationsUrl;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationsUrl = "{$this->baseUrl}invitations";
    }

    /**
     * Test that an invitee can successfully accept an invitation and create their account.
     */
    public function test_should_create_tenant_when_user_accepts_invitation()
    {
        $invitation = Invitation::factory()->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(201);
    }

    /**
     * Test that an invitee can successfully deny an invitation.
     */
    public function test_should_deny_invitation()
    {
        $invitation = Invitation::factory()->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";
        $response = $this->putJson($url);
        $response->assertStatus(200);
    }

    /**
     * Test that accepting an invitation fails if it is expired.
     */
    public function test_should_fail_create_tenant_for_expired_invitation()
    {
        $invitation = Invitation::factory()->expired()->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(404);
    }

    /**
     * Test that accepting an invitation fails if the invitation object has expired timestamp logic.
     */
    public function test_should_fail_accept_invitation_if_the_invitation_is_expired()
    {
        $invitation = Invitation::factory()->state([
            'expires_at' => now()->subMinutes(5),
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(404);
    }

    /**
     * Test that denying an invitation fails if it is expired.
     */
    public function test_should_fail_deny_invitation_if_the_invitation_is_expired()
    {
        $invitation = Invitation::factory()->state([
            'expires_at' => now()->subMinutes(5),
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";

        $response = $this->putJson($url);
        $response->assertStatus(404);
    }

    /**
     * Test that denying an invitation fails if it was already cancelled.
     */
    public function test_should_fail_deny_invitation_if_the_invitation_status_is_cancelled()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::CANCELLED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";

        $response = $this->putJson($url);
        $response->assertStatus(404);
    }

    /**
     * Test that denying an invitation fails if it was already denied.
     */
    public function test_should_fail_deny_invitation_if_the_invitation_status_is_denied()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::DENIED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";

        $response = $this->putJson($url);
        $response->assertStatus(404);
    }

    /**
     * Test that denying an invitation fails if it was already accepted.
     */
    public function test_should_fail_deny_invitation_if_the_invitation_status_is_accepted()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::ACCEPTED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";

        $response = $this->putJson($url);
        $response->assertStatus(404);
    }

    /**
     * Test that accepting an invitation fails if it was already cancelled.
     */
    public function test_should_fail_accept_invitation_if_the_invitation_status_is_cancelled()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::CANCELLED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(404);
    }

    /**
     * Test that accepting an invitation fails if it was already denied.
     */
    public function test_should_fail_accept_invitation_if_the_invitation_status_is_denied()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::DENIED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(404);
    }

    /**
     * Test that accepting an invitation fails if it was already accepted.
     */
    public function test_should_fail_accept_invitation_if_the_invitation_status_is_accepted()
    {
        $invitation = Invitation::factory()->state([
            'status' => InvitationStatus::ACCEPTED
        ])->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/accept";
        $payload = [
            'phone_number' => '0103284835',
            'password' => 'Thismystrongpassword',
            'password_confirmation' => 'Thismystrongpassword',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];

        $response = $this->putJson($url, $payload);
        $response->assertStatus(404);
    }
}
