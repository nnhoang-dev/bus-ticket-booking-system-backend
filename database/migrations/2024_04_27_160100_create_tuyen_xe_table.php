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
        Schema::create('tuyen_xe', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('start_address');
            $table->foreign('start_address')
                ->references('id')
                ->on('nha_xe')
                ->onDelete('cascade');

            $table->string('end_address');
            $table->foreign('end_address')
                ->references('id')
                ->on('nha_xe')
                ->onDelete('cascade');
            $table->time('time');
            $table->unique(['start_address', 'end_address'], 'unique_tuyen_xe_route');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuyen_xe');
    }
};