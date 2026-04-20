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

    /**
     * @var string
     */
    protected string $companyUrl;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->companyUrl = "{$this->baseUrl}companies";
    }

    /**
     * Test if a user with OWNER role can create a company.
     */
    public function test_should_create_company(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'SamuelBusiness',
        ];

        $response = $this->postJson($this->companyUrl, $payload);
        $response->assertStatus(201);
    }

    /**
     * Test that company creation fails if the user is not an owner (e.g. SUPER_ADMIN).
     */
    public function test_should_fail_company_creation_when_user_is_not_owner()
    {
        $user = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'SamuelBusiness',
        ];

        $response = $this->postJson($this->companyUrl, $payload);
        $response->assertStatus(403);
    }

    /**
     * Test that only a SUPER_ADMIN can delete a company.
     */
    public function test_delete_company_only_for_admin(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->companyUrl}/{$company->id}";
        $response = $this->delete($url);
        $response->assertStatus(200);
    }

    /**
     * Test that company deletion fails if the user is not a SUPER_ADMIN.
     */
    public function test_should_fail_deletion_when_user_not_admin(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->companyUrl}/{$company->id}";
        $response = $this->delete($url);
        $response->assertStatus(403);
    }

    /**
     * Test that a company owner can successfully update the company name.
     */
    public function test_should_sucessfully_update_company_name(): void
    {
        $company = Company::factory()->create();
        $user = $company->owner;

        Sanctum::actingAs($user);

        $url = "{$this->companyUrl}/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(200);
    }

    /**
     * Test that updating a company name fails if the owner doesn't own this specific company.
     */
    public function test_should_fail_update_company_name_when_user_do_not_own_the_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        Sanctum::actingAs($user);

        $url = "{$this->companyUrl}/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(400);
    }

    /**
     * Test that updating a company name fails if the user is not an owner at all (e.g. default user).
     */
    public function test_should_fail_update_company_name_when_user_is_not_an_owner(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $url = "{$this->companyUrl}/{$company->id}";
        $payload = [
            'name' => 'updated business name qbox',
        ];

        $response = $this->patchJson($url, $payload);
        $response->assertStatus(403);
    }
}
