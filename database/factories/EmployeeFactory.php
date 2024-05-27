<?php

namespace Database\Factories;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmployeeFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'phone_number' => '0909125679',
            'password' => Hash::make("0909125679"),
            'email' => 'nnhoanghd2004@gmail.com',
            'avatar' => '',
            'first_name' => 'Hoàng',
            'last_name' => 'Nguyễn',
            'date_of_birth' => '2004-11-01',
            'gender' => '1',
            'address' => 'TP HCM',
            'role' => 'manager',
            'status' => '1',

        ];
    }
}
