<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_create_company(): void
    {
        $url = "{$this->baseUrl}companies";

        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'SamuelBusiness',
        ];

        $response = $this->postJson($url, $payload);
        $response->assertStatus(201);
    }

    public function test_should_fail_company_creation_when_user_is_not_owner()
    {
        $url = "{$this->baseUrl}companies";

        $user = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'SamuelBusiness',
        ];

        $response = $this->postJson($url, $payload);
        $response->assertStatus(403);
    }

    public function test_delete_company_only_for_admin(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->baseUrl}companies/{$company->id}";
        $response = $this->delete($url);
        $response->assertStatus(200);
    }

    public function test_should_fail_deletion_when_user_not_admin(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->baseUrl}companies/{$company->id}";
        $response = $this->delete($url);
        $response->assertStatus(403);
    }

    public function test_should_sucessfully_update_company_name(): void
    {
        $company = Company::factory()->create();
        $user = $company->owner;

        Sanctum::actingAs($user);

        $url = "{$this->baseUrl}companies/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(200);
    }

    public function test_should_fail_update_company_name_when_user_do_not_own_the_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->baseUrl}companies/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(400);
    }

    public function test_should_fail_update_company_name_when_user_is_not_an_owner(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $url = "{$this->baseUrl}companies/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(403);
    }
}
