<?php

namespace Database\Factories;

use App\Models\AvailabilityCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailabilityCheck>
 */
class AvailabilityCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('4235######'),
            'street' => fake()->streetAddress(),
            'unit' => null,
            'city' => 'Cleveland',
            'state' => 'TN',
            'postal_code' => '37312',
        ];
    }

    /**
     * A check left with no contact details (both are optional).
     */
    public function withoutContact(): static
    {
        return $this->state(fn () => ['email' => null, 'phone' => null]);
    }
}
