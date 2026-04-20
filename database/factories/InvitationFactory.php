<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * The invitation needs: store_id, invited_by (the store's company owner).
     * Since invited_by depends on the store's company chain (Store → Company → Owner),
     * we eagerly create the store here to traverse the relationship.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $store = Store::factory()->create();

        return [
            'store_id' => $store->id,
            'name' => fake()->name(),
            'invitation_id' => Str::random(50),
            'expires_at' => now()->addMinutes(5),
            'role' => UserRole::CASHIER,
            'email' => fake()->email(),
            'phone_number' => '0556203925',
            'invited_by' => $store->company->owner_id,
            'status' => InvitationStatus::PENDING
        ];
    }

    /**
     * Create an invitation that is already expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinute(),
        ]);
    }
}
