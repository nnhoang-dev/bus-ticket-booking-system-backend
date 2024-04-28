<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NhaXe>
 */
class NhaXeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "id" => Uuid::uuid4()->toString(),
            "name" => $this->faker->name,
            "address" => $this->faker->address,
            "phone_number" => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['1', '0'])

        ];
    }
}