<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('khach_hang_id');
            $table->foreign('khach_hang_id')
                ->references('id')
                ->on('khach_hang');

            $table->string('giao_dich_id');
            $table->foreign('giao_dich_id')
                ->references('id')
                ->on('giao_dich');

            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('discount')->nullable();
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('total_price');

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
