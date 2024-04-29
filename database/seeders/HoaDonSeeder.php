<?php

namespace Database\Seeders;

use App\Models\HoaDon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HoaDonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HoaDon::factory()->count(10)->create();
    }
}