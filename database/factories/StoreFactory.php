<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    public function __construct(protected UserFactory $userFactory) {
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = $this->userFactory->create(['role' => 'owner']);
        return [
            'name' => fake()->name(),
            'owner_id' => $user->id
        ];
    }
}
