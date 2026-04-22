<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTenantTest extends TestCase
{
    use RefreshDatabase;

    protected string $storeTenantUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storeTenantUrl = "{$this->baseUrl}tenants";
    }

    /**
     * block store tenants
     */
    public function test_should_block_tenant(): void
    {
        $cashier = User::factory()->cashier()->create();
        $owner = $cashier->store->company->owner;

        Sanctum::actingAs($owner);

        $url = "{$this->storeTenantUrl}/{$cashier->id}/block";
        $response = $this->putJson($url);
        $response->assertJson([
            'data' => [
                'is_blocked' => true,
            ],
        ]);
        $response->assertStatus(200);
    }

    public function test_should_unblock_tenant()
    {
        $cashier = User::factory()->cashier()->state([
            'is_blocked' => true,
        ])->create();

        $owner = $cashier->store->company->owner;

        Sanctum::actingAs($owner);

        $url = "{$this->storeTenantUrl}/{$cashier->id}/unblock";
        $response = $this->putJson($url);
        $response->assertJson([
            'data' => [
                'is_blocked' => false,
            ],
        ]);
        $response->assertStatus(200);
    }

    public function test_should_fail_blocking_if_not_company_store_owner()
    {
        $cashier = User::factory()->cashier()->create();
        $owner = Company::factory()->create()->owner;
        Sanctum::actingAs($owner);

        $url = "{$this->storeTenantUrl}/{$cashier->id}/unblock";
        $response = $this->putJson($url);

        $response->assertStatus(403);
    }
}
