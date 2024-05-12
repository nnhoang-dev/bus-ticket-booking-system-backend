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
        Schema::create('temporary_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('trip_id');
            $table->foreign('trip_id')
                ->references('id')
                ->on('trips');

            $table->string('customer_id')->nullable();
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
            $table->integer('seat')->between(1, 36);
            $table->timestamps();

            $table->unique(['trip_id', 'seat'], 'unique_trips_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_tickets');
    }
};
