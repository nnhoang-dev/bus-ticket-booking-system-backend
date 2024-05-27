<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_number')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->boolean('gender');
            $table->string('address');
            $table->enum('role', ['accountant', 'manager', 'driver', 'customer_service', 'operator']);
            $table->boolean('status')->default(1);
            $table->timestamps();


            $table->index('phone_number');
            $table->index('email');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
