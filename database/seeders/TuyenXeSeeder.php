<?php

namespace Database\Seeders;

use App\Models\TuyenXe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TuyenXeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TuyenXe::factory()->count(10)->create();
    }
}
