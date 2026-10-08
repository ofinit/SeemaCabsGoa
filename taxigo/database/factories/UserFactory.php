<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = User::class;

    public function definition(): array
    {
       return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number'=>$this->faker->phoneNumber(),
            'gender' => $this->faker->boolean(),
            'country_id' => $this->faker->numberBetween(1, 200),
            'state_id' => $this->faker->numberBetween(1, 500),
            'device' => $this->faker->randomElement(['Android', 'iOS', 'Windows', 'Mac']),
            'type' => 3,
            'password' => bcrypt('password'), 
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
