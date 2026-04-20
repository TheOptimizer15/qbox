<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    private string $invitationsUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationsUrl = "{$this->baseUrl}invitations";
    }

  

    public function test_should_create_invitation()
    {
        $store = Store::factory()->create();
        $owner = $store->company->owner;
        Sanctum::actingAs($owner);

        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => UserRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925',
        ];

        $response = $this->postJson($this->invitationsUrl, $payload);
        $response->assertStatus(201);
    }

    public function test_should_fail_create_invitation_when_user_not_the_owner_of_the_store()
    {
        $store = Store::factory()->create();
        $owner = User::factory()->state(['role' => UserRole::OWNER])->create();
        Sanctum::actingAs($owner);

        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => UserRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925',
        ];

        $response = $this->postJson($this->invitationsUrl, $payload);
        $response->assertStatus(403);
    }

    public function test_should_fail_create_invitation_when_user_not_an_owner()
    {
        $store = Store::factory()->create();
        $user = User::factory()->state(['role' => UserRole::CASHIER])->create();
        Sanctum::actingAs($user);

        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => UserRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925',
        ];

        $response = $this->postJson($this->invitationsUrl, $payload);
        $response->assertStatus(403);
    }

    public function test_should_fail_create_invitation_if_role_not_allowed()
    {
        $store = Store::factory()->create();
        $owner = $store->company->owner;
        Sanctum::actingAs($owner);

        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => UserRole::SUPER_ADMIN,
            'email' => fake()->email(),
            'phone_number' => '0556203925',
        ];

        $response = $this->postJson($this->invitationsUrl, $payload);
        $response->assertStatus(422);
    }

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


    public function test_should_deny_invitation()
    {
        $invitation = Invitation::factory()->create();

        $url = "{$this->invitationsUrl}/{$invitation->invitation_id}/deny";
        $response = $this->putJson($url);
        $response->assertStatus(200);
    }

    public function test_should_cancel_invitation()
    {
        $invitation = Invitation::factory()->create();
        $owner = $invitation->invitedBy;
        Sanctum::actingAs($owner);

        $url = "{$this->invitationsUrl}/{$invitation->id}";
        $response = $this->deleteJson($url);
        $response->assertStatus(200);
    }

    public function test_should_fail_cancel_invitation_if_not_the_owner()
    {
        $invitation = Invitation::factory()->create();
        $otherOwner = User::factory()->state(['role' => UserRole::OWNER])->create();
        Sanctum::actingAs($otherOwner);

        $url = "{$this->invitationsUrl}/{$invitation->id}";
        $response = $this->deleteJson($url);
        $response->assertStatus(403);
    }
}
