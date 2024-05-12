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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('ticket_id')->unique();


            $table->string('trip_id');
            $table->foreign('trip_id')
                ->references('id')
                ->on('trips');

            $table->string('customer_id')->nullable();
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');

            $table->string('invoice_id')->nullable();
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices');

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
        Schema::dropIfExists('tickets');
    }
};
