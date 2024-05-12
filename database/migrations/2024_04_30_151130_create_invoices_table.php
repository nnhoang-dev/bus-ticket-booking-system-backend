<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('customer_id');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');

            $table->string('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions');

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
        Schema::dropIfExists('invoices');
    }
};