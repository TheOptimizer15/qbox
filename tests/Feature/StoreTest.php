<?php

namespace Tests\Feature;

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
     * A basic feature test example.
     */
    public function test_should_return_current_store_if_user_is_a_cashier()
    {
        $cashier = User::factory()->cashier()->create();
        Sanctum::actingAs($cashier);

        $url = "{$this->baseUrl}stores";
        $response = $this->get($url);
        $response->assertStatus(200);
        // $response->dump();
    }

    public function test_should_return_all_the_stores_if_user_is_an_owner()
    {
        $store = Store::factory()->create();
        $owner = $store->company->owner;

        Sanctum::actingAs($owner);
        $url = "{$this->baseUrl}stores";
        $response = $this->get($url);
        $response->assertStatus(200);
        // $response->dump();
    }

    public function test_should_not_return_store_if_user_not_the_owner(){
        $store =Store::factory()->create();
        $owner = Company::factory()->create()->owner;

        Sanctum::actingAs($owner);
        $url = "{$this->baseUrl}stores/{$store->id}";
        $response = $this->get($url);
        $response->assertStatus(403);
        // $response->dump();
    }

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
            'company_id' => $company->id,
        ];

        $url = "{$this->baseUrl}stores";
        $response = $this->postJson($url, $payload);
        $response->assertStatus(201);
        // $response->dump();
    }

    public function test_should_not_create_store_if_owner_is_not_the_owner_of_a_company()
    {

        // create a simple user without a company
        $user = User::factory()->create([
            'role' => 'owner',
        ]);
        
        Sanctum::actingAs($user);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $url = "{$this->baseUrl}stores";
        $response = $this->postJson($url, $payload);
        $response->assertStatus(403);
        // $response->dump();
    }

    public function test_should_not_create_store_if_user_not_a_owner()
    {
        $user = User::factory()->create([
            'role' => 'cashier',
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'name' => fake()->name(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
        ];

        $url = "{$this->baseUrl}stores";
        $response = $this->postJson($url, $payload);
        $response->assertStatus(403);
        // $response->dump();
    }

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

        // $this->withoutExceptionHandling();

        $url = "{$this->baseUrl}stores/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(200);
        // $response->dump();
    }

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

        // $this->withoutExceptionHandling();

        $url = "{$this->baseUrl}stores/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(403);
        // $response->dump();
    }

    public function test_should_not_update_store_if_user_is_not_the_owner()
    {
        /**
         * @var Store $store
         */
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

        // $this->withoutExceptionHandling();

        $url = "{$this->baseUrl}stores/{$store->id}";
        $response = $this->putJson($url, $payload);
        $response->assertStatus(403);
        // $response->dump();
    }
}
