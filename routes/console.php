<?php

use App\Models\KhuyenMai;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Ramsey\Uuid\Uuid;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $data = [
        "discount" => 10,
        "status" => 1,
    ];
    $data['id'] = Uuid::uuid4();
    KhuyenMai::create($data);
})->everyFiveSeconds();