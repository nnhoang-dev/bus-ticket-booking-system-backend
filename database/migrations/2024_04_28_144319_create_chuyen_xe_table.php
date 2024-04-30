<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('chuyen_xe', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('tuyen_xe_id');
            $table->foreign('tuyen_xe_id')
                ->references('id')
                ->on('tuyen_xe');

            $table->string('xe_id');
            $table->foreign('xe_id')
                ->references('id')
                ->on('xe');

            $table->string('tai_xe_id');
            $table->foreign('tai_xe_id')
                ->references('id')
                ->on('nhan_vien');

            $table->integer('price');
            $table->string('seat')->default("");
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->index('tuyen_xe_id');
            $table->index('xe_id');
            $table->index('tai_xe_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('chuyen_xe');
    }
};