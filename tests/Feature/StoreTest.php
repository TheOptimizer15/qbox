<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $storeUrl;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->storeUrl = "{$this->baseUrl}stores";
    }

    /**
     * Test that a cashier can retrieve their assigned store.
     */
    public function test_should_return_current_store_if_user_is_a_cashier()
    {
        $cashier = User::factory()->cashier()->create();
        Sanctum::actingAs($cashier);

        $response = $this->get($this->storeUrl);
        $response->assertStatus(200);
    }

    /**
     * Test that an owner can retrieve all stores belonging to their company.
     */
    public function test_should_return_all_the_stores_if_user_is_an_owner()
    {
        $store = Store::factory()->create();
        $owner = $store->company->owner;

        Sanctum::actingAs($owner);
        $response = $this->get($this->storeUrl);
        $response->assertStatus(200);
    }

    /**
     * Test that an owner cannot retrieve a store that doesn't belong to their company.
     */
    public function test_should_not_return_store_if_user_not_the_owner()
    {
        $store = Store::factory()->create();
        $owner = Company::factory()->create()->owner;

        Sanctum::actingAs($owner);
        $url = "{$this->storeUrl}/{$store->id}";
        $response = $this->get($url);
        $response->assertStatus(403);
    }

    /**
     * Test that a company owner can successfully create a new store.
     */
    public function test_should_create_store_if_user_owns_company(): void
    {
        $company = Company::factory()->create();
        $user = $company->owner;
        Sanctum::actingAs($user);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $response = $this->postJson($this->storeUrl, $payload);
        $response->assertStatus(201);
    }

    /**
     * Test that an OWNER user without an associated company cannot create a store.
     */
    public function test_should_not_create_store_if_owner_is_not_the_owner_of_a_company()
    {
        // create a simple user without a company
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $response = $this->postJson($this->storeUrl, $payload);
        $response->assertStatus(403);
    }

    /**
     * Test that a user with a non-owner role (e.g. CASHIER) cannot create a store.
     */
    public function test_should_not_create_store_if_user_not_a_owner()
    {
        $user = User::factory()->create([
            'role' => UserRole::CASHIER,
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $response = $this->postJson($this->storeUrl, $payload);
        $response->assertStatus(403);
    }

    /**
     * Test that a store owner can successfully update store information.
     */
    public function test_should_update_store_data()
    {
        $store = Store::factory()->create();
        $owner = $store->company->owner;

        Sanctum::actingAs($owner);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $url = "{$this->storeUrl}/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(200);
    }

    /**
     * Test that updating a store fails if the user is not an owner at all.
     */
    public function test_should_not_update_store_if_user_is_not_an_owner()
    {
        $store = Store::factory()->create();
        $owner = User::factory()->create();

        Sanctum::actingAs($owner);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $url = "{$this->storeUrl}/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(403);
    }

    /**
     * Test that updating a store fails if the owner doesn't own this specific store's company.
     */
    public function test_should_not_update_store_if_user_is_not_the_owner()
    {
        $store = Store::factory()->create();
        $owner = Company::factory()->create()->owner;

        Sanctum::actingAs($owner);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $url = "{$this->storeUrl}/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(403);
    }
}

