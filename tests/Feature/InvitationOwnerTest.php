<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Store;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvitationOwnerTest extends TestCase
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
     * Test that a company owner can successfully create an invitation for their store.
     */
    public function test_should_create_invitation()
    {
        Queue::fake();
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

    /**
     * Test that an invitation fails if the owner doesn't own the specified store.
     */
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

    /**
     * Test that an invitation fails if the user is not an OWNER.
     */
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

    /**
     * Test that an invitation fails if an invalid role (e.g. SUPER_ADMIN) is requested for the invitee.
     */
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

    /**
     * Test that an owner can successfully cancel an invitation they created.
     */
    public function test_should_cancel_invitation()
    {
        $invitation = Invitation::factory()->create();
        $owner = $invitation->invitedBy;
        Sanctum::actingAs($owner);

        $url = "{$this->invitationsUrl}/{$invitation->id}";
        $response = $this->deleteJson($url);
        $response->assertStatus(200);
    }

    /**
     * Test that cancelling an invitation fails if the user is not the one who created it.
     */
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
