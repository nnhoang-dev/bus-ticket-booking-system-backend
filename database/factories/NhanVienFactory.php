<?php

namespace Database\Factories;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class NhanVienFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'phone_number' => $this->faker->unique()->phoneNumber,
            'password' => bcrypt('password'),
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['0', '1']),
            'address' => $this->faker->address,
            'role' => $this->faker->randomElement(['QL', 'TX', 'VH', 'CS', 'KT']),
            'status' => $this->faker->randomElement(['1', '0']),
        ];
    }
}
