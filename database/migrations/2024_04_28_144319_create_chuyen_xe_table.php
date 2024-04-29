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
        Schema::create('chuyen_xe', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('tuyen_xe_id');
            $table->foreign('tuyen_xe_id')
                ->references('id')
                ->on('tuyen_xe')
                ->onDelete('cascade');

            $table->string('xe_id');
            $table->foreign('xe_id')
                ->references('id')
                ->on('xe')
                ->onDelete('cascade');

            $table->string('seat')->default("");
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuyen_xe');
    }
};