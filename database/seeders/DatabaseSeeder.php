<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NhanVien;
use App\Models\KhachHang;
use App\Models\NhaXe;
use App\Models\Xe;
use App\Models\KhuyenMai;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NhaXe::factory()->count(10)->create();
        NhanVien::factory()->count(10)->create();
        KhachHang::factory()->count(10)->create();
        Xe::factory()->count(10)->create();
        KhuyenMai::factory()->count(10)->create();
    }
}