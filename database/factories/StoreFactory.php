<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'location' => fake()->city(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'online' => fake()->boolean(),
            'company_id' => Company::factory()->create(),
        ];
    }
}
