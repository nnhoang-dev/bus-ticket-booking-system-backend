<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ve_xe', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('ve_id')->unique();


            $table->string('chuyen_xe_id');
            $table->foreign('chuyen_xe_id')
                ->references('id')
                ->on('chuyen_xe');

            $table->string('khach_hang_id')->nullable();
            $table->foreign('khach_hang_id')
                ->references('id')
                ->on('khach_hang');

            $table->string('hoa_don_id')->nullable();
            $table->foreign('hoa_don_id')
                ->references('id')
                ->on('hoa_don');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('route_name');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('start_address');
            $table->string('end_address');
            $table->string('seat');
            $table->integer('price');
            $table->string('license');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ve_xe');
    }
};
