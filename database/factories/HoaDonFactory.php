<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class HoaDonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->optional()->email,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'discount' => $this->faker->optional()->numberBetween(1, 20),
            'price' => $this->faker->randomNumber(5, true),
            'quantity' => $this->faker->randomNumber(1, true),
            'total_price' => function (array $invoice) {
                return $invoice['price'] * $invoice['quantity'] * (1 - ($invoice['discount'] / 100));
            },
        ];
    }
}