<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\Sms\SendSmsJob;
use App\Models\Store;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmsInvitationTest extends TestCase
{
    use RefreshDatabase;
    protected string $invitationUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUrl = "{$this->baseUrl}invitations";
        Bus::fake();
    }

    /**
     * When owner creates an invitation it should send the invitationn via sms
     * This test asserts the event fires, the listener executes and dispatch properly the job
     */
    public function test_should_dispatch_sms_job_successfully(): void
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

        $response = $this->postJson($this->invitationUrl, $payload);
        $response->assertStatus(201);
        Bus::assertDispatched(SendSmsJob::class);
    }
}
