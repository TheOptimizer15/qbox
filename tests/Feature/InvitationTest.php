<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Enums\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
  
    public function test_should_create_invitation(){
        $store = Store::factory()->create();
        $owner = $store->company->owner;
        Sanctum::actingAs($owner);

        $url = "{$this->baseUrl}invite";
        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => TenantRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925'
        ];
        $response = $this->postJson($url, $payload);
        $response->assertStatus(201);
        // $response->dump();
    }

    public function test_should_fail_create_invitation_when_user_not_the_owner_of_the_store(){
        $store = Store::factory()->create();
        $owner = User::factory()->state([
            'role' => UserRole::OWNER
        ])->create();
        Sanctum::actingAs($owner);

        $url = "{$this->baseUrl}invite";
        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => TenantRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925'
        ];
        $response = $this->postJson($url, $payload);
        $response->assertStatus(403);
        // $response->dump();
    }

     public function test_should_fail_create_invitation_when_user_not_an_owner(){
        $store = Store::factory()->create();
        $owner = User::factory()->state([
            'role' => UserRole::CASHIER
        ])->create();
        Sanctum::actingAs($owner);

        $url = "{$this->baseUrl}invite";
        $payload = [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'role' => TenantRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925'
        ];
        $response = $this->postJson($url, $payload);
        $response->assertStatus(403);
        $response->dump();
    }
}
