<?php

namespace Database\Factories;

use App\Models\NhaXe;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TuyenXe>
 */
class TuyenXeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'start' => NhaXe::factory(),
            'end' => Nhaxe::factory(),
            'status' => $this->faker->randomElement(['1', '0'])
        ];
    }
}