<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStation;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use App\Models\Voucher;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusStation::factory()->count(10)->create();
        Employee::factory()->count(10)->create();
        Customer::factory()->count(10)->create();
        Bus::factory()->count(10)->create();
        Voucher::factory()->count(10)->create();
    }
}
