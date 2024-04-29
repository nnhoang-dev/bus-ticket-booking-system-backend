<?php

namespace Database\Seeders;

use App\Models\KhuyenMai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KhuyenMaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KhuyenMai::factory()->count(10)->create();
    }
}