<?php

namespace Database\Seeders;

use App\Models\NhaXe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NhaXeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NhaXe::factory()->count(10)->create();
    }
}
