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
        Schema::create('ve_tam', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('chuyen_xe_id');
            $table->foreign('chuyen_xe_id')
                ->references('id')
                ->on('chuyen_xe');

            $table->string('khach_hang_id')->nullable();
            $table->foreign('khach_hang_id')
                ->references('id')
                ->on('khach_hang');
            $table->integer('seat')->between(1, 36);
            $table->timestamps();

            $table->unique(['chuyen_xe_id', 'seat'], 'unique_chuyen_xe_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ve_tam');
    }
};
